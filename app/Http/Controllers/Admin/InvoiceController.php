<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\AuditLog;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['order', 'user']);

        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($orderQuery) use ($search) {
                      $orderQuery->where('order_number', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->latest()->paginate(20);
        return view('admin.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['order.orderItems.service', 'user']);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function create()
    {
        $orders = Order::whereDoesntHave('invoice')
                      ->where('payment_status', 'paid')
                      ->get();
        return view('admin.invoices.create', compact('orders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'due_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $order = Order::findOrFail($request->order_id);

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'subtotal' => $order->subtotal,
            'tax_amount' => 0, // Can be calculated based on business logic
            'discount_amount' => 0, // Can be calculated based on business logic
            'total_amount' => $order->total_amount,
            'due_date' => $request->due_date,
            'notes' => $request->notes,
            'status' => 'draft',
        ]);

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'invoice_created',
            'model_type' => Invoice::class,
            'model_id' => $invoice->id,
            'new_values' => $invoice->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.invoices.show', $invoice)
                        ->with('success', 'Invoice created successfully.');
    }

    public function generatePdf(Invoice $invoice)
    {
        $invoice->load(['order.orderItems.service', 'user']);
        
        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'));
        
        return $pdf->stream($invoice->invoice_number . '.pdf');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['order.orderItems.service', 'user']);
        
        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'));
        
        return $pdf->download($invoice->invoice_number . '.pdf');
    }

    public function sendEmail(Invoice $invoice)
    {
        // Generate PDF and store it
        $invoice->load(['order.orderItems.service', 'user']);
        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'));
        
        $pdfPath = 'invoices/' . $invoice->invoice_number . '.pdf';
        Storage::disk('public')->put($pdfPath, $pdf->output());
        
        // Update invoice with PDF path
        $invoice->update(['pdf_path' => $pdfPath]);
        
        // Send email with PDF attachment
        // This would integrate with your email service
        // Mail::to($invoice->user->email)->send(new InvoiceMail($invoice));
        
        // Mark as sent
        $invoice->markAsSent();
        
        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'invoice_sent',
            'model_type' => Invoice::class,
            'model_id' => $invoice->id,
            'new_values' => ['status' => 'sent'],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Invoice sent successfully.');
    }

    public function markAsPaid(Invoice $invoice)
    {
        $oldValues = $invoice->toArray();
        
        $invoice->markAsPaid();
        
        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'invoice_marked_paid',
            'model_type' => Invoice::class,
            'model_id' => $invoice->id,
            'old_values' => $oldValues,
            'new_values' => $invoice->fresh()->toArray(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Invoice marked as paid.');
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
        ]);

        $oldValues = $invoice->toArray();

        $invoice->update($request->only(['due_date', 'notes', 'status']));

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'invoice_updated',
            'model_type' => Invoice::class,
            'model_id' => $invoice->id,
            'old_values' => $oldValues,
            'new_values' => $invoice->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.invoices.show', $invoice)
                        ->with('success', 'Invoice updated successfully.');
    }
}
