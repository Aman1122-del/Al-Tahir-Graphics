@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="section-title">Shopping Cart</h1>
            <p class="section-subtitle">Review your selected services and proceed to checkout.</p>
        </div>

        @if($cartItems->count() > 0)
            <div class="grid gap-8 lg:grid-cols-3">
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-4">
                    @foreach($cartItems as $item)
                        <div class="bg-white rounded-2xl shadow-md ring-1 ring-black/5 p-6" data-cart-item-id="{{ $item->id }}">
                            <div class="flex gap-4">
                                <img src="{{ $item->design_preview_path ? asset('storage/' . $item->design_preview_path) : $item->service->image_url }}" alt="{{ $item->service->title }}" class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-[--color-brand-deepblue]">{{ $item->service->title }}</h3>
                                    <p class="text-sm text-slate-600 mt-1">{{ $item->service->formatted_price }} each</p>
                                    @if($item->design_id)
                                        <div class="mt-2 text-xs text-slate-600">
                                            Design attached — <a class="text-[--color-brand-blue] underline" href="{{ url('/design?design_id=' . $item->design_id) }}">Edit design</a>
                                        </div>
                                    @endif
                                    
                                    @if($item->custom_requirements)
                                        <div class="mt-2">
                                            <p class="text-xs text-slate-500">Requirements:</p>
                                            <p class="text-sm text-slate-700">{{ $item->custom_requirements }}</p>
                                        </div>
                                    @endif
                                    
                                    <div class="flex items-center gap-4 mt-4">
                                        <div class="flex items-center gap-2">
                                            <label class="text-sm font-medium text-slate-700">Qty:</label>
                                            <input type="number" min="1" value="{{ $item->quantity }}" class="w-16 rounded border border-slate-300 px-2 py-1 text-center text-sm focus:border-[--color-brand-blue] focus:outline-none quantity-input" data-cart-item-id="{{ $item->id }}" data-custom-requirements="{{ $item->custom_requirements ?? '' }}">
                                        </div>
                                        <button class="text-sm text-red-600 hover:text-red-800 font-medium remove-item-btn" data-cart-item-id="{{ $item->id }}">Remove</button>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-[--color-brand-deepblue]">{{ $item->formatted_total_price }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="flex justify-between items-center pt-4">
                        <button class="text-sm text-slate-600 hover:text-slate-800 font-medium clear-cart-btn">Clear Cart</button>
                        <a href="{{ route('services') }}" class="text-sm text-[--color-brand-blue] hover:text-[--color-brand-orange] font-medium">Continue Shopping</a>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-md ring-1 ring-black/5 p-6 sticky top-24">
                        <h3 class="text-lg font-semibold text-[--color-brand-deepblue] mb-4">Order Summary</h3>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span>Subtotal ({{ $itemCount }} items)</span>
                                <span class="font-medium">{{ 'PKR ' . number_format($cartTotal, 0) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Shipping</span>
                                <span class="font-medium">PKR 500</span>
                            </div>
                            <div class="border-t border-slate-200 pt-3">
                                <div class="flex justify-between text-lg font-bold text-[--color-brand-deepblue]">
                                    <span>Total</span>
                                    <span>{{ 'PKR ' . number_format($cartTotal + 500, 0) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <a href="{{ route('checkout.index') }}" class="btn-primary w-full justify-center">
                                Proceed to Checkout
                            </a>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <p class="text-xs text-slate-500">Free shipping on orders over PKR 10,000</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Cart -->
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-slate-100 flex items-center justify-center">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-slate-700 mb-2">Your cart is empty</h3>
                <p class="text-slate-600 mb-6">Looks like you haven't added any services to your cart yet.</p>
                <a href="{{ route('services') }}" class="btn-primary">Browse Services</a>
            </div>
        @endif
    </div>

    <script>
        // Add event listeners when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Quantity change events
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('quantity-input')) {
                    const cartItemId = e.target.dataset.cartItemId;
                    const quantity = e.target.value;
                    const customRequirements = e.target.dataset.customRequirements;
                    updateCartItem(cartItemId, quantity, customRequirements);
                }
            });

            // Remove item events
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-item-btn')) {
                    const cartItemId = e.target.dataset.cartItemId;
                    removeCartItem(cartItemId);
                }
            });

            // Clear cart event
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('clear-cart-btn')) {
                    clearCart();
                }
            });
        });

        function updateCartItem(cartItemId, quantity, customRequirements) {
            fetch(`{{ url('/cart/update') }}/${cartItemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    quantity: quantity,
                    custom_requirements: customRequirements
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart summary in navbar
                    document.getElementById('cartCount').textContent = data.item_count;
                    document.getElementById('cartTotal').textContent = data.cart_total;
                    
                    // Update item total
                    const cartItem = document.querySelector(`[data-cart-item-id="${cartItemId}"]`);
                    if (cartItem) {
                        const totalElement = cartItem.querySelector('.text-lg.font-bold');
                        if (totalElement) {
                            totalElement.textContent = data.item_total;
                        }
                    }
                    
                    // Reload page to update totals
                    location.reload();
                } else {
                    showNotification(data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating cart', 'error');
            });
        }

        function removeCartItem(cartItemId) {
            if (!confirm('Are you sure you want to remove this item from your cart?')) {
                return;
            }

            fetch(`{{ url('/cart/remove') }}/${cartItemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart summary in navbar
                    document.getElementById('cartCount').textContent = data.item_count;
                    document.getElementById('cartTotal').textContent = data.cart_total;
                    
                    // Remove cart item from DOM
                    const cartItem = document.querySelector(`[data-cart-item-id="${cartItemId}"]`);
                    if (cartItem) {
                        cartItem.remove();
                        
                        // If no more items, reload page to show empty cart
                        if (document.querySelectorAll('[data-cart-item-id]').length === 0) {
                            location.reload();
                        }
                    }
                    
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while removing item', 'error');
            });
        }

        function clearCart() {
            if (!confirm('Are you sure you want to clear your entire cart?')) {
                return;
            }

            fetch('{{ route("cart.clear") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart summary in navbar
                    document.getElementById('cartCount').textContent = data.item_count;
                    document.getElementById('cartTotal').textContent = data.cart_total;
                    
                    showNotification(data.message, 'success');
                    location.reload();
                } else {
                    showNotification(data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while clearing cart', 'error');
            });
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 rounded-lg px-6 py-3 text-white font-medium shadow-lg transition-all ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
@endsection
