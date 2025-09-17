@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Edit Order - {{ $order->order_number }}</h1>
                    <a href="{{ route('admin.orders.show', $order) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Back to Order
                    </a>
                </div>

                <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Order Status -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Order Status</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Status</label>
                                    <select name="order_status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                                    <select name="payment_status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                                        <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Design Status -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Design Status</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Design Status</label>
                                    <select name="design_status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="pending" {{ $order->design_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ $order->design_status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="review" {{ $order->design_status === 'review' ? 'selected' : '' }}>Review</option>
                                        <option value="approved" {{ $order->design_status === 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="completed" {{ $order->design_status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Designer</label>
                                    <select name="assigned_designer_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select Designer</option>
                                        @foreach($designers as $designer)
                                            <option value="{{ $designer->id }}" {{ $order->assigned_designer_id == $designer->id ? 'selected' : '' }}>
                                                {{ $designer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Design Due Date</label>
                                    <input type="date" name="design_due_date" 
                                           value="{{ $order->design_due_date ? $order->design_due_date->format('Y-m-d') : '' }}"
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="bg-gray-50 p-6 rounded-lg mt-6">
                        <h2 class="text-xl font-semibold mb-4">Notes</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Design Notes</label>
                                <textarea name="design_notes" rows="4" 
                                          class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                          placeholder="Add design notes...">{{ $order->design_notes }}</textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Notes</label>
                                <textarea name="admin_notes" rows="4" 
                                          class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                          placeholder="Add admin notes...">{{ $order->admin_notes }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items (Read-only) -->
                    <div class="bg-gray-50 p-6 rounded-lg mt-6">
                        <h2 class="text-xl font-semibold mb-4">Order Items</h2>
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
                                    @foreach($order->orderItems as $item)
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
                                        <td class="px-4 py-2 font-semibold">PKR {{ number_format($order->subtotal, 0) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right font-semibold">Shipping:</td>
                                        <td class="px-4 py-2 font-semibold">PKR {{ number_format($order->shipping_cost, 0) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right font-semibold text-lg">Total:</td>
                                        <td class="px-4 py-2 font-semibold text-lg">PKR {{ number_format($order->total_amount, 0) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded-lg">
                            Update Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
