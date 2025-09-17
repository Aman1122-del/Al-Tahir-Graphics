@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Create New Invoice</h1>
                    <a href="{{ route('admin.invoices.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Back to Invoices
                    </a>
                </div>

                <form method="POST" action="{{ route('admin.invoices.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Order Selection -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Select Order</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Order *</label>
                                    <select name="order_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select an order</option>
                                        @foreach($orders as $order)
                                            <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                                {{ $order->order_number }} - {{ $order->customer_name }} (PKR {{ number_format($order->total_amount, 0) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('order_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                @if($orders->count() === 0)
                                    <div class="text-center text-gray-500 py-4">
                                        <p>No orders available for invoicing.</p>
                                        <p class="text-sm">All orders either already have invoices or are not paid.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Invoice Details -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Invoice Details</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date *</label>
                                    <input type="date" name="due_date" value="{{ old('due_date') }}" required
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('due_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" rows="4"
                                              class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                              placeholder="Add any additional notes for this invoice...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Preview -->
                    <div class="bg-gray-50 p-6 rounded-lg mt-6">
                        <h2 class="text-xl font-semibold mb-4">Order Preview</h2>
                        <div id="order-preview" class="text-gray-500 text-center py-8">
                            Select an order to see details here.
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded-lg" 
                                {{ $orders->count() === 0 ? 'disabled' : '' }}>
                            Create Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderSelect = document.querySelector('select[name="order_id"]');
    const orderPreview = document.getElementById('order-preview');
    
    // Order data for preview
    const orders = @json($orders->keyBy('id'));
    
    orderSelect.addEventListener('change', function() {
        const orderId = this.value;
        
        if (orderId && orders[orderId]) {
            const order = orders[orderId];
            orderPreview.innerHTML = `
                <div class="text-left">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-semibold text-gray-900">Order Information</h3>
                            <p><strong>Order #:</strong> ${order.order_number}</p>
                            <p><strong>Customer:</strong> ${order.customer_name}</p>
                            <p><strong>Email:</strong> ${order.customer_email}</p>
                            <p><strong>Status:</strong> ${order.order_status}</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Amount Details</h3>
                            <p><strong>Subtotal:</strong> PKR ${parseFloat(order.subtotal).toLocaleString()}</p>
                            <p><strong>Shipping:</strong> PKR ${parseFloat(order.shipping_cost).toLocaleString()}</p>
                            <p><strong>Total:</strong> PKR ${parseFloat(order.total_amount).toLocaleString()}</p>
                        </div>
                    </div>
                </div>
            `;
        } else {
            orderPreview.innerHTML = '<p class="text-gray-500 text-center py-8">Select an order to see details here.</p>';
        }
    });
});
</script>
@endsection
