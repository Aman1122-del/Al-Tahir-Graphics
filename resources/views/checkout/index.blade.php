@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="mb-8">
            <h1 class="section-title">Checkout</h1>
            <p class="section-subtitle">Complete your order and provide delivery information.</p>
        </div>

        <div class="grid gap-8 lg:grid-cols-3">
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-md ring-1 ring-black/5 p-6">
                    <form action="{{ route('checkout.process') }}" method="POST" enctype="multipart/form-data" id="checkoutForm">
                        @csrf
                        
                        <!-- Customer Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-[--color-brand-deepblue] mb-4">Customer Information</h3>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="customer_name" class="block text-sm font-medium text-slate-700 mb-2">Full Name *</label>
                                    <input type="text" id="customer_name" name="customer_name" required 
                                           class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-[--color-brand-blue] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue]/20"
                                           value="{{ old('customer_name', auth()->user()->name ?? '') }}">
                                    @error('customer_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="customer_email" class="block text-sm font-medium text-slate-700 mb-2">Email *</label>
                                    <input type="email" id="customer_email" name="customer_email" required 
                                           class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-[--color-brand-blue] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue]/20"
                                           value="{{ old('customer_email', auth()->user()->email ?? '') }}">
                                    @error('customer_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="customer_phone" class="block text-sm font-medium text-slate-700 mb-2">Phone *</label>
                                    <input type="tel" id="customer_phone" name="customer_phone" required 
                                           class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-[--color-brand-blue] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue]/20"
                                           value="{{ old('customer_phone') }}">
                                    @error('customer_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-[--color-brand-deepblue] mb-4">Shipping Information</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="shipping_address" class="block text-sm font-medium text-slate-700 mb-2">Shipping Address *</label>
                                    <textarea id="shipping_address" name="shipping_address" rows="3" required 
                                              class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-[--color-brand-blue] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue]/20"
                                              placeholder="Enter your complete shipping address">{{ old('shipping_address') }}</textarea>
                                    @error('shipping_address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="shipping_method" class="block text-sm font-medium text-slate-700 mb-2">Shipping Method *</label>
                                    <select id="shipping_method" name="shipping_method" required 
                                            class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-[--color-brand-blue] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue]/20">
                                        <option value="">Select shipping method</option>
                                        <option value="standard" {{ old('shipping_method') === 'standard' ? 'selected' : '' }}>Standard Delivery (3-5 business days) - PKR 500</option>
                                        <option value="express" {{ old('shipping_method') === 'express' ? 'selected' : '' }}>Express Delivery (1-2 business days) - PKR 1000</option>
                                    </select>
                                    @error('shipping_method')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-[--color-brand-deepblue] mb-4">Payment Information</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-slate-700 mb-2">Payment Method *</label>
                                    <select id="payment_method" name="payment_method" required 
                                            class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-[--color-brand-blue] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue]/20">
                                        <option value="">Select payment method</option>
                                        <option value="pay_on_delivery" {{ old('payment_method') === 'pay_on_delivery' ? 'selected' : '' }}>Pay on Delivery</option>
                                        <option value="manual_transfer" {{ old('payment_method') === 'manual_transfer' ? 'selected' : '' }}>Manual Bank Transfer</option>
                                    </select>
                                    @error('payment_method')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Payment Screenshot Upload (for manual transfer) -->
                                <div id="paymentScreenshotSection" class="hidden">
                                    <label for="payment_screenshot" class="block text-sm font-medium text-slate-700 mb-2">Payment Screenshot</label>
                                    <input type="file" id="payment_screenshot" name="payment_screenshot" 
                                           accept="image/*" 
                                           class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-[--color-brand-blue] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue]/20">
                                    <p class="mt-1 text-sm text-slate-500">Upload a screenshot of your bank transfer confirmation</p>
                                    @error('payment_screenshot')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-8">
                            <div class="flex items-start">
                                <input type="checkbox" id="terms_accepted" name="terms_accepted" required 
                                       class="mt-1 h-4 w-4 rounded border-slate-300 text-[--color-brand-blue] focus:ring-[--color-brand-blue]">
                                <label for="terms_accepted" class="ml-2 text-sm text-slate-600">
                                    I agree to the <a href="#" class="text-[--color-brand-blue] hover:underline">Terms and Conditions</a> and <a href="#" class="text-[--color-brand-blue] hover:underline">Privacy Policy</a>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-between items-center">
                            <a href="{{ route('cart.view') }}" class="btn-secondary">
                                ← Back to Cart
                            </a>
                            <button type="submit" class="btn-primary">
                                Place Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-md ring-1 ring-black/5 p-6 sticky top-24">
                    <h3 class="text-lg font-semibold text-[--color-brand-deepblue] mb-4">Order Summary</h3>
                    
                    <!-- Cart Items -->
                    <div class="space-y-3 mb-6">
                        @foreach($cartItems as $item)
                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-slate-900 truncate">{{ $item->service->title }}</h4>
                                    <p class="text-xs text-slate-500">Qty: {{ $item->quantity }}</p>
                                </div>
                                <span class="text-sm font-medium text-slate-900">{{ $item->formatted_total_price }}</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Totals -->
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span>Subtotal ({{ $cartItems->count() }} items)</span>
                            <span class="font-medium">{{ 'PKR ' . number_format($cartTotal, 0) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping</span>
                            <span class="font-medium" id="shippingCost">PKR {{ $shippingCost }}</span>
                        </div>
                        <div class="border-t border-slate-200 pt-3">
                            <div class="flex justify-between text-lg font-bold text-[--color-brand-deepblue]">
                                <span>Total</span>
                                <span id="totalAmount">{{ 'PKR ' . number_format($totalAmount, 0) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 p-4 bg-slate-50 rounded-lg">
                        <h4 class="text-sm font-medium text-slate-900 mb-2">Bank Transfer Details</h4>
                        <div class="text-xs text-slate-600 space-y-1">
                            <p><strong>Bank:</strong> Example Bank</p>
                            <p><strong>Account:</strong> 1234-5678-9012-3456</p>
                            <p><strong>IBAN:</strong> PK36-XXXX-XXXX-XXXX-XXXX</p>
                            <p><strong>Account Title:</strong> Al-Tahir Graphics</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethod = document.getElementById('payment_method');
            const paymentScreenshotSection = document.getElementById('paymentScreenshotSection');
            const shippingMethod = document.getElementById('shipping_method');
            const shippingCostElement = document.getElementById('shippingCost');
            const totalAmountElement = document.getElementById('totalAmount');
            
            // Show/hide payment screenshot section based on payment method
            paymentMethod.addEventListener('change', function() {
                if (this.value === 'manual_transfer') {
                    paymentScreenshotSection.classList.remove('hidden');
                } else {
                    paymentScreenshotSection.classList.add('hidden');
                }
            });
            
            // Update shipping cost and total based on shipping method
            shippingMethod.addEventListener('change', function() {
                const subtotal = {{ $cartTotal }};
                let shippingCost = 0;
                
                if (this.value === 'standard') {
                    shippingCost = 500;
                } else if (this.value === 'express') {
                    shippingCost = 1000;
                }
                
                const total = subtotal + shippingCost;
                
                shippingCostElement.textContent = `PKR ${shippingCost.toLocaleString()}`;
                totalAmountElement.textContent = `PKR ${total.toLocaleString()}`;
            });
            
            // Form validation
            document.getElementById('checkoutForm').addEventListener('submit', function(e) {
                const termsAccepted = document.getElementById('terms_accepted').checked;
                if (!termsAccepted) {
                    e.preventDefault();
                    alert('Please accept the terms and conditions to continue.');
                    return false;
                }
            });
        });
    </script>
@endsection
