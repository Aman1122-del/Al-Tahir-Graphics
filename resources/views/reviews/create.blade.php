@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Write a Review</h1>
            <p class="text-gray-600">Order #{{ $order->order_number }}</p>
        </div>

        <form action="{{ route('reviews.store', $order) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Rating -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-4">Rate your experience</label>
                <div class="flex items-center justify-center space-x-2" x-data="{ rating: 0, hover: 0 }">
                    <input type="hidden" name="rating" :value="rating" required>
                    
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" 
                                @click="rating = {{ $i }}" 
                                @mouseenter="hover = {{ $i }}" 
                                @mouseleave="hover = 0"
                                class="focus:outline-none transition-colors duration-200">
                            <svg class="w-10 h-10" 
                                 :class="(hover || rating) >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'"
                                 fill="currentColor" 
                                 viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </button>
                    @endfor
                </div>
                @error('rating')
                    <p class="mt-2 text-sm text-red-600 text-center">{{ $message }}</p>
                @enderror
            </div>

            <!-- Review Items -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Items to Review:</h3>
                <ul class="list-disc list-inside text-sm text-gray-600">
                    @foreach($order->orderItems as $item)
                        <li>{{ $item->service_name }} (x{{ $item->quantity }})</li>
                    @endforeach
                </ul>
            </div>

            <!-- Comment -->
            <div>
                <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                <textarea id="comment" 
                          name="comment" 
                          rows="4" 
                          class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Tell us what you liked about your order..."></textarea>
                @error('comment')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 font-medium">Cancel</a>
                <button type="submit" class="btn-primary">
                    Submit Review
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
