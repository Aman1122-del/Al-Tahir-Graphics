<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Quote::with(['user', 'assignedDesigner']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('designer')) {
            $query->where('assigned_designer_id', $request->designer);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $quotes = $query->latest()->paginate(20);
        $designers = User::role('designer')->get();

        return view('admin.quotes.index', compact('quotes', 'designers'));
    }

    public function show(Quote $quote)
    {
        $quote->load(['user', 'assignedDesigner']);
        $designers = User::role('designer')->get();
        
        return view('admin.quotes.show', compact('quote', 'designers'));
    }

    public function create()
    {
        $designers = User::role('designer')->get();
        return view('admin.quotes.create', compact('designers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'project_description' => 'required|string',
            'requirements' => 'nullable|string',
            'estimated_price' => 'nullable|numeric|min:0',
            'assigned_designer_id' => 'nullable|exists:users,id',
            'valid_until' => 'required|date|after:today',
            'admin_notes' => 'nullable|string',
        ]);

        $quote = Quote::create([
            'quote_number' => Quote::generateQuoteNumber(),
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'project_description' => $request->project_description,
            'requirements' => $request->requirements,
            'estimated_price' => $request->estimated_price,
            'assigned_designer_id' => $request->assigned_designer_id,
            'valid_until' => $request->valid_until,
            'admin_notes' => $request->admin_notes,
            'status' => 'pending',
        ]);

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'quote_created',
            'model_type' => Quote::class,
            'model_id' => $quote->id,
            'new_values' => $quote->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.quotes.show', $quote)
                        ->with('success', 'Quote created successfully.');
    }

    public function edit(Quote $quote)
    {
        $designers = User::role('designer')->get();
        return view('admin.quotes.edit', compact('quote', 'designers'));
    }

    public function update(Request $request, Quote $quote)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'project_description' => 'required|string',
            'requirements' => 'nullable|string',
            'estimated_price' => 'nullable|numeric|min:0',
            'assigned_designer_id' => 'nullable|exists:users,id',
            'valid_until' => 'required|date',
            'admin_notes' => 'nullable|string',
            'status' => 'required|in:draft,pending,approved,rejected,expired',
        ]);

        $oldValues = $quote->toArray();

        $quote->update($request->all());

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'quote_updated',
            'model_type' => Quote::class,
            'model_id' => $quote->id,
            'old_values' => $oldValues,
            'new_values' => $quote->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.quotes.show', $quote)
                        ->with('success', 'Quote updated successfully.');
    }

    public function assignDesigner(Request $request, Quote $quote)
    {
        $request->validate([
            'assigned_designer_id' => 'required|exists:users,id',
        ]);

        $oldValues = $quote->toArray();

        $quote->update([
            'assigned_designer_id' => $request->assigned_designer_id,
        ]);

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'quote_designer_assigned',
            'model_type' => Quote::class,
            'model_id' => $quote->id,
            'old_values' => $oldValues,
            'new_values' => $quote->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Designer assigned successfully.');
    }

    public function approve(Quote $quote)
    {
        $oldValues = $quote->toArray();

        $quote->update(['status' => 'approved']);

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'quote_approved',
            'model_type' => Quote::class,
            'model_id' => $quote->id,
            'old_values' => $oldValues,
            'new_values' => $quote->fresh()->toArray(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Quote approved successfully.');
    }

    public function reject(Quote $quote)
    {
        $oldValues = $quote->toArray();

        $quote->update(['status' => 'rejected']);

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'quote_rejected',
            'model_type' => Quote::class,
            'model_id' => $quote->id,
            'old_values' => $oldValues,
            'new_values' => $quote->fresh()->toArray(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Quote rejected successfully.');
    }
}
