@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Invoice Details - {{ $invoice->invoice_number }}</h1>
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.invoices.pdf', $invoice) }}" 
                           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg" target="_blank">
                            View PDF
                        </a>
                        <a href="{{ route('admin.invoices.download', $invoice) }}" 
                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                            Download PDF
                        </a>
                        <a href="{{ route('admin.invoices.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                            Back to Invoices
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Invoice Information -->
                    <div class="lg:col-span-2">
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">Invoice Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Invoice Number</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $invoice->invoice_number }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($invoice->status === 'paid') bg-green-100 text-green-800
                                        @elseif($invoice->status === 'sent') bg-blue-100 text-blue-800
                                        @elseif($invoice->status === 'overdue') bg-red-100 text-red-800
                                        @elseif($invoice->status === 'cancelled') bg-gray-100 text-gray-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Due Date</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $invoice->due_date->format('M d, Y') }}
                                        @if($invoice->isOverdue())
                                            <span class="text-red-500 text-xs ml-1">(Overdue)</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Paid Date</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $invoice->paid_date ? $invoice->paid_date->format('M d, Y') : 'Not paid' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Created Date</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $invoice->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $invoice->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">Customer Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $invoice->user->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $invoice->user->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">User Account</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <a href="{{ route('admin.users.show', $invoice->user) }}" class="text-indigo-600 hover:text-indigo-900">
                                            View Profile
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Information -->
                        @if($invoice->order)
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">Related Order</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Order Number</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <a href="{{ route('admin.orders.show', $invoice->order) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $invoice->order->order_number }}
                                        </a>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Order Status</label>
                                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($invoice->order->order_status === 'completed') bg-green-100 text-green-800
                                        @elseif($invoice->order->order_status === 'processing') bg-blue-100 text-blue-800
                                        @elseif($invoice->order->order_status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($invoice->order->order_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Invoice Items -->
                        @if($invoice->order && $invoice->order->orderItems->count() > 0)
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">Invoice Items</h2>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                            <th class="px-4 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                            <th class="px-4 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                                            <th class="px-4 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->order->orderItems as $item)
                                        <tr>
                                            <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-900">
                                                {{ $item->service->title }}
                                            </td>
                                            <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-900">
                                                {{ $item->quantity }}
                                            </td>
                                            <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-900">
                                                PKR {{ number_format($item->unit_price, 0) }}
                                            </td>
                                            <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-900">
                                                PKR {{ number_format($item->total_price, 0) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="3" class="px-4 py-2 text-right font-semibold">Subtotal:</td>
                                            <td class="px-4 py-2 font-semibold">PKR {{ number_format($invoice->subtotal, 0) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="px-4 py-2 text-right font-semibold">Tax:</td>
                                            <td class="px-4 py-2 font-semibold">PKR {{ number_format($invoice->tax_amount, 0) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="px-4 py-2 text-right font-semibold">Discount:</td>
                                            <td class="px-4 py-2 font-semibold">PKR {{ number_format($invoice->discount_amount, 0) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="px-4 py-2 text-right font-semibold text-lg">Total:</td>
                                            <td class="px-4 py-2 font-semibold text-lg">PKR {{ number_format($invoice->total_amount, 0) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- Notes -->
                        @if($invoice->notes)
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Notes</h2>
                            <div class="prose max-w-none">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $invoice->notes }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <!-- Quick Actions -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
                            <div class="space-y-3">
                                <a href="{{ route('admin.invoices.pdf', $invoice) }}" 
                                   class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md block text-center" target="_blank">
                                    View PDF
                                </a>
                                
                                <a href="{{ route('admin.invoices.download', $invoice) }}" 
                                   class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md block text-center">
                                    Download PDF
                                </a>
                                
                                @if($invoice->status === 'draft')
                                    <form method="POST" action="{{ route('admin.invoices.send-email', $invoice) }}">
                                        @csrf
                                        <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md">
                                            Send Email
                                        </button>
                                    </form>
                                @endif
                                
                                @if($invoice->status === 'sent')
                                    <form method="POST" action="{{ route('admin.invoices.mark-paid', $invoice) }}">
                                        @csrf
                                        <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                                            Mark as Paid
                                        </button>
                                    </form>
                                @endif
                                
                                @if($invoice->status === 'paid')
                                    <div class="text-center text-green-600 font-medium">
                                        ✓ Invoice Paid
                                    </div>
                                @endif
                                
                                @if($invoice->isOverdue())
                                    <div class="text-center text-red-600 font-medium">
                                        ⚠️ Invoice Overdue
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Invoice Summary -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Invoice Summary</h2>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Subtotal:</span>
                                    <span class="text-sm font-medium">PKR {{ number_format($invoice->subtotal, 0) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Tax:</span>
                                    <span class="text-sm font-medium">PKR {{ number_format($invoice->tax_amount, 0) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Discount:</span>
                                    <span class="text-sm font-medium">PKR {{ number_format($invoice->discount_amount, 0) }}</span>
                                </div>
                                <div class="border-t pt-2">
                                    <div class="flex justify-between">
                                        <span class="text-base font-semibold">Total:</span>
                                        <span class="text-base font-semibold">PKR {{ number_format($invoice->total_amount, 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
