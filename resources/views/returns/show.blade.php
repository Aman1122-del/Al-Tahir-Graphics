@extends('layouts.app')

@section('title', 'Return Request Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Return Request #{{ $returnRequest->id }}</h1>
                <a href="{{ route('returns.index') }}"
                   class="text-blue-600 hover:text-blue-900 text-sm">
                    ← Back to My Requests
                </a>
            </div>

            <!-- Status Badge -->
            <div class="mb-6">
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                    @if($returnRequest->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($returnRequest->status === 'approved') bg-blue-100 text-blue-800
                    @elseif($returnRequest->status === 'rejected') bg-red-100 text-red-800
                    @elseif($returnRequest->status === 'completed') bg-green-100 text-green-800
                    @elseif($returnRequest->status === 'refunded') bg-green-100 text-green-800
                    @endif">
                    {{ $returnRequest->status_text }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Request Details -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Request Details</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Type</dt>
                                <dd class="text-sm text-gray-900">{{ $returnRequest->type_text }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Reason</dt>
                                <dd class="text-sm text-gray-900">{{ $returnRequest->reason_text }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Submitted Date</dt>
                                <dd class="text-sm text-gray-900">{{ $returnRequest->created_at ? $returnRequest->created_at->format('M d, Y H:i') : 'N/A' }}</dd>
                            </div>
                            @if($returnRequest->approved_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Approved Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $returnRequest->approved_at->format('M d, Y H:i') }}</dd>
                                </div>
                            @endif
                            @if($returnRequest->completed_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Completed Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $returnRequest->completed_at->format('M d, Y H:i') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Order Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Order Information</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Order Number</dt>
                                <dd class="text-sm text-gray-900">#{{ $returnRequest->order->order_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Order Date</dt>
                                <dd class="text-sm text-gray-900">{{ $returnRequest->order->created_at ? $returnRequest->order->created_at->format('M d, Y') : 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Order Total</dt>
                                <dd class="text-sm text-gray-900">{{ $returnRequest->order->formatted_total_amount }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Order Status</dt>
                                <dd class="text-sm text-gray-900">{{ ucfirst($returnRequest->order->order_status) }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if($returnRequest->orderItem)
                        <!-- Order Item Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Item Information</h3>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Item</dt>
                                    <dd class="text-sm text-gray-900">{{ $returnRequest->orderItem->service_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Quantity</dt>
                                    <dd class="text-sm text-gray-900">{{ $returnRequest->orderItem->quantity }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Unit Price</dt>
                                    <dd class="text-sm text-gray-900">{{ $returnRequest->orderItem->formatted_unit_price }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Price</dt>
                                    <dd class="text-sm text-gray-900">{{ $returnRequest->orderItem->formatted_total_price }}</dd>
                                </div>
                            </dl>
                        </div>
                    @endif
                </div>

                <!-- Description and Additional Info -->
                <div class="space-y-4">
                    <!-- Description -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Description</h3>
                        <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-md">
                            {{ $returnRequest->description }}
                        </p>
                    </div>

                    <!-- Admin Notes -->
                    @if($returnRequest->admin_notes)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Admin Notes</h3>
                            <p class="text-sm text-gray-700 bg-blue-50 p-3 rounded-md">
                                {{ $returnRequest->admin_notes }}
                            </p>
                        </div>
                    @endif

                    <!-- Refund Information -->
                    @if($returnRequest->refund_amount)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Refund Information</h3>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Refund Amount</dt>
                                    <dd class="text-sm text-gray-900">{{ $returnRequest->formatted_refund_amount }}</dd>
                                </div>
                                @if($returnRequest->refund_method)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Refund Method</dt>
                                        <dd class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $returnRequest->refund_method)) }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    @endif

                    <!-- Tracking Information -->
                    @if($returnRequest->tracking_number)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Tracking Information</h3>
                            <p class="text-sm text-gray-700">
                                Tracking Number: <span class="font-mono">{{ $returnRequest->tracking_number }}</span>
                            </p>
                        </div>
                    @endif

                    <!-- Images -->
                    @if($returnRequest->images && count($returnRequest->images) > 0)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Uploaded Images</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($returnRequest->images as $image)
                                    <div class="relative">
                                        <img src="{{ Storage::url($image) }}"
                                             alt="Return request image"
                                             class="w-full h-24 object-cover rounded-md cursor-pointer"
                                             onclick="openImageModal('{{ Storage::url($image) }}')">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
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
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}
</script>
@endsection
