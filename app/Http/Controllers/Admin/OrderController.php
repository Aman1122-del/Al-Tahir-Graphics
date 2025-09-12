<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderNote;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'assignedDesigner']);

        // Apply filters
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('design_status')) {
            $query->byDesignStatus($request->design_status);
        }

        if ($request->has('payment_status')) {
            $query->byPaymentStatus($request->payment_status);
        }

        if ($request->has('designer')) {
            $query->assignedToDesigner($request->designer);
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(20);
        $designers = User::role('designer')->get();

        return view('admin.orders.index', compact('orders', 'designers'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'assignedDesigner', 'orderItems.service', 'orderNotes.user']);
        $designers = User::role('designer')->get();
        
        return view('admin.orders.show', compact('order', 'designers'));
    }

    public function edit(Order $order)
    {
        $order->load(['user', 'assignedDesigner', 'orderItems.service']);
        $designers = User::role('designer')->get();
        
        return view('admin.orders.edit', compact('order', 'designers'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => 'required|in:pending,processing,completed,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'assigned_designer_id' => 'nullable|exists:users,id',
            'design_status' => 'required|in:pending,in_progress,review,approved,completed',
            'design_due_date' => 'nullable|date',
            'design_notes' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ]);

        $oldValues = $order->toArray();

        $order->update($request->only([
            'order_status', 'payment_status', 'assigned_designer_id',
            'design_status', 'design_due_date', 'design_notes', 'admin_notes'
        ]));

        // Add order note if admin notes changed
        if ($request->filled('admin_notes') && $request->admin_notes !== $oldValues['admin_notes']) {
            OrderNote::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'type' => 'admin',
                'note' => 'Admin notes updated: ' . $request->admin_notes,
                'is_internal' => true,
            ]);
        }

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'order_updated',
            'model_type' => Order::class,
            'model_id' => $order->id,
            'old_values' => $oldValues,
            'new_values' => $order->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.orders.show', $order)
                        ->with('success', 'Order updated successfully.');
    }

    public function assignDesigner(Request $request, Order $order)
    {
        $request->validate([
            'assigned_designer_id' => 'required|exists:users,id',
            'design_due_date' => 'nullable|date',
            'design_notes' => 'nullable|string',
        ]);

        $oldValues = $order->toArray();

        $order->update([
            'assigned_designer_id' => $request->assigned_designer_id,
            'design_status' => 'pending',
            'design_due_date' => $request->design_due_date,
            'design_notes' => $request->design_notes,
        ]);

        // Add order note
        $designer = User::find($request->assigned_designer_id);
        OrderNote::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'type' => 'admin',
            'note' => "Order assigned to designer: {$designer->name}",
            'is_internal' => true,
        ]);

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'designer_assigned',
            'model_type' => Order::class,
            'model_id' => $order->id,
            'old_values' => $oldValues,
            'new_values' => $order->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Designer assigned successfully.');
    }

    public function addNote(Request $request, Order $order)
    {
        $request->validate([
            'note' => 'required|string|max:1000',
            'type' => 'required|in:admin,designer,system,customer',
            'is_internal' => 'boolean',
        ]);

        OrderNote::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'note' => $request->note,
            'is_internal' => $request->boolean('is_internal'),
        ]);

        return back()->with('success', 'Note added successfully.');
    }

    public function export(Request $request)
    {
        $query = Order::with(['user', 'assignedDesigner']);

        // Apply filters
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        $orders = $query->get();

        if ($request->format === 'csv') {
            return $this->exportToCsv($orders);
        }

        return $this->exportToExcel($orders);
    }

    private function exportToCsv($orders)
    {
        $filename = 'orders_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Order Number', 'Customer', 'Email', 'Status', 'Total Amount',
                'Payment Status', 'Design Status', 'Designer', 'Created Date'
            ]);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->customer_name,
                    $order->customer_email,
                    $order->order_status,
                    $order->total_amount,
                    $order->payment_status,
                    $order->design_status,
                    $order->assignedDesigner ? $order->assignedDesigner->name : 'Unassigned',
                    $order->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportToExcel($orders)
    {
        // This would use Laravel Excel package
        // For now, return CSV as fallback
        return $this->exportToCsv($orders);
    }
}
