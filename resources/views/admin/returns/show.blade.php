@extends('layouts.admin')

@section('title', 'Return Request Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Return Request #{{ $returnRequest->id }}</h1>
            <a href="{{ route('admin.returns.index') }}"
               class="text-blue-600 hover:text-blue-900 text-sm">
                ← Back to All Requests
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Request Details -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Request Details</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($returnRequest->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($returnRequest->status === 'approved') bg-blue-100 text-blue-800
                                    @elseif($returnRequest->status === 'rejected') bg-red-100 text-red-800
                                    @elseif($returnRequest->status === 'completed') bg-green-100 text-green-800
                                    @elseif($returnRequest->status === 'refunded') bg-green-100 text-green-800
                                    @endif">
                                    {{ $returnRequest->status_text }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $returnRequest->type_text }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Reason</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $returnRequest->reason_text }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Submitted Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $returnRequest->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                        @if($returnRequest->approved_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Approved Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $returnRequest->approved_at->format('M d, Y H:i') }}</dd>
                            </div>
                        @endif
                        @if($returnRequest->completed_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Completed Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $returnRequest->completed_at->format('M d, Y H:i') }}</dd>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md">
                            {{ $returnRequest->description }}
                        </dd>
                    </div>
                </div>

                <!-- Order Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Order Number</dt>
                            <dd class="mt-1 text-sm text-gray-900">#{{ $returnRequest->order->order_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Order Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $returnRequest->order->created_at->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Order Total</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $returnRequest->order->formatted_total_amount }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Order Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($returnRequest->order->order_status) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Payment Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $returnRequest->order->payment_status)) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $returnRequest->order->payment_method ?? 'N/A')) }}</dd>
                        </div>
                    </div>

                    @if($returnRequest->orderItem)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Specific Item</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Item</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $returnRequest->orderItem->service_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Quantity</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $returnRequest->orderItem->quantity }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Unit Price</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $returnRequest->orderItem->formatted_unit_price }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Price</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $returnRequest->orderItem->formatted_total_price }}</dd>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Customer Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Customer Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $returnRequest->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $returnRequest->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $returnRequest->order->customer_phone ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Orders</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $returnRequest->user->orders()->count() }}</dd>
                        </div>
                    </div>

                    @if($returnRequest->order->shipping_address)
                        <div class="mt-4">
                            <dt class="text-sm font-medium text-gray-500">Shipping Address</dt>
                            <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md">
                                {{ $returnRequest->order->shipping_address }}
                            </dd>
                        </div>
                    @endif
                </div>

                <!-- Images -->
                @if($returnRequest->images && count($returnRequest->images) > 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Uploaded Images</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($returnRequest->images as $image)
                                <div class="relative">
                                    <img src="{{ Storage::url($image) }}"
                                         alt="Return request image"
                                         class="w-full h-32 object-cover rounded-md cursor-pointer"
                                         onclick="openImageModal('{{ Storage::url($image) }}')">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar - Update Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Update Request</h2>

                    <form action="{{ route('admin.returns.update', $returnRequest) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="space-y-4">
                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                    Status *
                                </label>
                                <select name="status" id="status" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="pending" {{ $returnRequest->status === 'pending' ? 'selected' : '' }}>Pending Review</option>
                                    <option value="approved" {{ $returnRequest->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $returnRequest->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="completed" {{ $returnRequest->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="refunded" {{ $returnRequest->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>

                            <!-- Admin Notes -->
                            <div>
                                <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-1">
                                    Admin Notes
                                </label>
                                <textarea name="admin_notes" id="admin_notes" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="Internal notes about this request...">{{ $returnRequest->admin_notes }}</textarea>
                            </div>

                            <!-- Refund Information -->
                            <div id="refund-section" style="{{ in_array($returnRequest->status, ['refunded']) ? '' : 'display: none;' }}">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Refund Details</h3>

                                <div class="space-y-3">
                                    <div>
                                        <label for="refund_amount" class="block text-sm font-medium text-gray-700 mb-1">
                                            Refund Amount
                                        </label>
                                        <input type="number" name="refund_amount" id="refund_amount" step="0.01" min="0"
                                               value="{{ $returnRequest->refund_amount }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label for="refund_method" class="block text-sm font-medium text-gray-700 mb-1">
                                            Refund Method
                                        </label>
                                        <select name="refund_method" id="refund_method"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">Select method...</option>
                                            <option value="bank_transfer" {{ $returnRequest->refund_method === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                            <option value="credit_card" {{ $returnRequest->refund_method === 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                            <option value="cash" {{ $returnRequest->refund_method === 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="other" {{ $returnRequest->refund_method === 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Tracking Number -->
                            <div>
                                <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tracking Number
                                </label>
                                <input type="text" name="tracking_number" id="tracking_number"
                                       value="{{ $returnRequest->tracking_number }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Return shipping tracking number">
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4">
                                <button type="submit"
                                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Update Request
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-end">
            <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mt-3">
            <img id="modalImage" src="" alt="Return request image" class="w-full h-auto">
        </div>
    </div>
</div>

<script>
document.getElementById('status').addEventListener('change', function() {
    const refundSection = document.getElementById('refund-section');
    if (this.value === 'refunded') {
        refundSection.style.display = 'block';
    } else {
        refundSection.style.display = 'none';
    }
});

function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}
</script>
@endsection
