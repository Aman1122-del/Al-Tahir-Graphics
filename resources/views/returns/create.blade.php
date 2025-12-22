@extends('layouts.app')

@section('title', 'Return/Cancellation Request')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Return/Cancellation Request</h1>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('returns.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Order Selection -->
                <div>
                    <label for="order_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Order *
                    </label>
                    <select name="order_id" id="order_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Choose an order...</option>
                        @foreach($eligibleOrders as $ord)
                            <option value="{{ $ord->id }}"
                                    {{ ($order && $order->id == $ord->id) ? 'selected' : '' }}>
                                Order #{{ $ord->order_number }} - {{ $ord->formatted_total_amount }} ({{ $ord->created_at->format('M d, Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Order Item Selection (optional) -->
                <div id="order-item-section" style="{{ $orderItem ? '' : 'display: none;' }}">
                    <label for="order_item_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Specific Item (Optional)
                    </label>
                    <select name="order_item_id" id="order_item_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All items in the order</option>
                        @if($order)
                            @foreach($order->orderItems as $item)
                                <option value="{{ $item->id }}"
                                        {{ ($orderItem && $orderItem->id == $item->id) ? 'selected' : '' }}>
                                    {{ $item->service_name }} - Quantity: {{ $item->quantity }} - {{ $item->formatted_total_price }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Request Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Request Type *</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="type" value="return" required
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Return Request</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="type" value="cancellation"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Cancellation Request</span>
                        </label>
                    </div>
                </div>

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason *
                    </label>
                    <select name="reason" id="reason" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select a reason...</option>
                        <option value="defective_product">Defective Product</option>
                        <option value="wrong_item">Wrong Item Received</option>
                        <option value="not_as_described">Not as Described</option>
                        <option value="changed_mind">Changed Mind</option>
                        <option value="late_delivery">Late Delivery</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description *
                    </label>
                    <textarea name="description" id="description" rows="4" required
                              placeholder="Please provide detailed information about your return/cancellation request..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <!-- Image Upload -->
                <div>
                    <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Images (Optional)
                    </label>
                    <input type="file" name="images[]" id="images" multiple accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">You can upload up to 5 images (max 2MB each)</p>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('order_id').addEventListener('change', function() {
    const orderId = this.value;
    const orderItemSection = document.getElementById('order-item-section');
    const orderItemSelect = document.getElementById('order_item_id');

    if (orderId) {
        // Fetch order items via AJAX
        fetch(`/api/order/${orderId}/items`)
            .then(response => response.json())
            .then(data => {
                orderItemSelect.innerHTML = '<option value="">All items in the order</option>';
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = `${item.service_name} - Quantity: ${item.quantity} - ${item.formatted_total_price}`;
                    orderItemSelect.appendChild(option);
                });
                orderItemSection.style.display = 'block';
            })
            .catch(error => {
                console.error('Error fetching order items:', error);
                orderItemSection.style.display = 'none';
            });
    } else {
        orderItemSection.style.display = 'none';
        orderItemSelect.innerHTML = '<option value="">All items in the order</option>';
    }
});
</script>
@endsection
