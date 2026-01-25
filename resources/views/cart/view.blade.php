<style>
    .shopping-btn {
        background-color: burlywood;
        padding: 12px 11px 12px 10px;
        border-radius: 23px;
        color: white;
    }

    .clear-cart-btn {
        background-color: crimson;
        padding: 9px 15px 10px 16px;
        border-radius: 8px;
        color: white;

    }
</style>

@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="section-title">Shopping Cart</h1>
            <p class="section-subtitle">Review your selected services and proceed to checkout.</p>
        </div>

        @if ($cartItems->count() > 0)
            <div class="grid gap-8 lg:grid-cols-3">
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-4">
                    @foreach ($cartItems as $item)
                        <div class="bg-white rounded-2xl shadow-md ring-1 ring-black/5 p-6"
                            data-cart-item-id="{{ $item->id }}">
                            <div class="flex gap-4">
                                <img src="{{ $item->design_preview_path ? asset('storage/' . $item->design_preview_path) : ($item->sample ? $item->sample->image_path : $item->service->image_path) }}"
                                    alt="{{ $item->sample ? $item->sample->title : $item->service->title }}"
                                    class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-[--color-brand-deepblue]">
                                        @if ($item->sample)
                                            {{ $item->sample->title }}
                                            <span class="text-sm font-normal text-slate-600">-
                                                {{ $item->service->title }}</span>
                                        @else
                                            {{ $item->service->title }}
                                        @endif
                                    </h3>
                                    @if ($item->sample && $item->sample->sub_category)
                                        <p class="text-sm text-slate-500">{{ $item->sample->sub_category }} Category</p>
                                    @endif
                                    <p class="text-sm text-slate-600 mt-1">
                                        {{ $item->sample ? $item->sample->formatted_price : $item->service->formatted_price }}
                                        each</p>
                                    {{-- --- NEW: Wedding Details Display --- --}}
                                    @if ($item->wedding_details)
                                        <div class="mt-3 p-3 bg-blue-50/50 rounded-lg border border-blue-100">
                                            <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-1">
                                                Wedding Card Details</p>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 text-sm">
                                                <p><span class="text-slate-500">Groom:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->wedding_details['groom'] ?? 'N/A' }}</span>
                                                </p>
                                                <p><span class="text-slate-500">Bride:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->wedding_details['bride'] ?? 'N/A' }}</span>
                                                </p>
                                                @if(isset($item->wedding_details['eventType']) && !empty($item->wedding_details['eventType']))
                                                <p><span class="text-slate-500">Event Type:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->wedding_details['eventType'] }}</span>
                                                </p>
                                                @endif
                                                @if(isset($item->wedding_details['dateTime']) && !empty($item->wedding_details['dateTime']))
                                                <p><span class="text-slate-500">Date & Time:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->wedding_details['dateTime'] }}</span>
                                                </p>
                                                @endif
                                                @if(isset($item->wedding_details['venue']) && !empty($item->wedding_details['venue']))
                                                <p><span class="text-slate-500">Venue:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->wedding_details['venue'] }}</span>
                                                </p>
                                                @endif
                                            </div>
                                            @if (!empty($item->wedding_details['additionalMessage'] ?? $item->wedding_details['remarks'] ?? ''))
                                                <p
                                                    class="mt-1 text-sm italic text-slate-600 border-t border-blue-100 pt-1 mt-1">
                                                    "{{ $item->wedding_details['additionalMessage'] ?? $item->wedding_details['remarks'] }}"
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                    {{-- --- Visiting Card Details Display --- --}}
                                    @if ($item->visiting_card_details)
                                        <div class="mt-3 p-3 bg-green-50/50 rounded-lg border border-green-100">
                                            <p class="text-[10px] font-bold text-green-600 uppercase tracking-widest mb-1">
                                                Visiting Card Details</p>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 text-sm">
                                                <p><span class="text-slate-500">Name:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->visiting_card_details['businessName'] ?? 'N/A' }}</span>
                                                </p>
                                                @if(isset($item->visiting_card_details['designation']) && !empty($item->visiting_card_details['designation']))
                                                <p><span class="text-slate-500">Designation:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->visiting_card_details['designation'] }}</span>
                                                </p>
                                                @endif
                                                @if(isset($item->visiting_card_details['companyName']) && !empty($item->visiting_card_details['companyName']))
                                                <p><span class="text-slate-500">Company:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->visiting_card_details['companyName'] }}</span>
                                                </p>
                                                @endif
                                                <p><span class="text-slate-500">Mobile:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->visiting_card_details['mobileNumber'] ?? 'N/A' }}</span>
                                                </p>
                                                @if(isset($item->visiting_card_details['whatsappNumber']) && !empty($item->visiting_card_details['whatsappNumber']))
                                                <p><span class="text-slate-500">WhatsApp:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->visiting_card_details['whatsappNumber'] }}</span>
                                                </p>
                                                @endif
                                                @if(isset($item->visiting_card_details['emailAddress']) && !empty($item->visiting_card_details['emailAddress']))
                                                <p><span class="text-slate-500">Email:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->visiting_card_details['emailAddress'] }}</span>
                                                </p>
                                                @endif
                                                <p><span class="text-slate-500">Printing:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->visiting_card_details['printingSide'] ?? 'N/A' }}</span>
                                                </p>
                                            </div>
                                            @if(isset($item->visiting_card_details['officeAddress']) && !empty($item->visiting_card_details['officeAddress']))
                                                <p class="mt-1 text-sm text-slate-600">
                                                    <span class="text-slate-500">Address:</span> {{ $item->visiting_card_details['officeAddress'] }}
                                                </p>
                                            @endif
                                            @if (!empty($item->visiting_card_details['additionalMessage'] ?? ''))
                                                <p
                                                    class="mt-1 text-sm italic text-slate-600 border-t border-green-100 pt-1 mt-1">
                                                    "{{ $item->visiting_card_details['additionalMessage'] }}"
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                    {{-- --- Panaflex Details Display --- --}}
                                    @if ($item->panaflex_details)
                                        <div class="mt-3 p-3 bg-purple-50/50 rounded-lg border border-purple-100">
                                            <p class="text-[10px] font-bold text-purple-600 uppercase tracking-widest mb-1">
                                                Panaflex Printing Details</p>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 text-sm">
                                                <p><span class="text-slate-500">Business/Event Name:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->panaflex_details['businessName'] ?? 'N/A' }}</span>
                                                </p>
                                                <p><span class="text-slate-500">Panaflex Size:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->panaflex_details['panaflexSize'] ?? 'N/A' }}</span>
                                                </p>
                                                @if(isset($item->panaflex_details['eventType']) && !empty($item->panaflex_details['eventType']))
                                                <p><span class="text-slate-500">Event Type:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->panaflex_details['eventType'] }}</span>
                                                </p>
                                                @endif
                                                <p><span class="text-slate-500">Main Heading:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->panaflex_details['mainHeading'] ?? 'N/A' }}</span>
                                                </p>
                                                @if(isset($item->panaflex_details['subHeading']) && !empty($item->panaflex_details['subHeading']))
                                                <p><span class="text-slate-500">Sub Heading:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->panaflex_details['subHeading'] }}</span>
                                                </p>
                                                @endif
                                                @if(isset($item->panaflex_details['dateTime']) && !empty($item->panaflex_details['dateTime']))
                                                <p><span class="text-slate-500">Date & Time:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->panaflex_details['dateTime'] }}</span>
                                                </p>
                                                @endif
                                                <p><span class="text-slate-500">Venue/Location:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->panaflex_details['venue'] ?? 'N/A' }}</span>
                                                </p>
                                                <p><span class="text-slate-500">Contact Number:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->panaflex_details['contactNumber'] ?? 'N/A' }}</span>
                                                </p>
                                            </div>
                                            @if (!empty($item->panaflex_details['additionalInstructions'] ?? ''))
                                                <p
                                                    class="mt-1 text-sm italic text-slate-600 border-t border-purple-100 pt-1 mt-1">
                                                    "{{ $item->panaflex_details['additionalInstructions'] }}"
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                    {{-- --- Flyer/Brochure Details Display --- --}}
                                    @if ($item->flyer_brochure_details)
                                        <div class="mt-3 p-3 bg-emerald-50/50 rounded-lg border border-emerald-100">
                                            <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-1">
                                                Flyer/Brochure Details</p>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 text-sm">
                                                <p><span class="text-slate-500">Business/Brand Name:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->flyer_brochure_details['businessName'] ?? 'N/A' }}</span>
                                                </p>
                                                <p><span class="text-slate-500">Brochure Type:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->flyer_brochure_details['brochureType'] ?? 'N/A' }}</span>
                                                </p>
                                                <p><span class="text-slate-500">Paper Type:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->flyer_brochure_details['paperType'] ?? 'N/A' }}</span>
                                                </p>
                                                <p><span class="text-slate-500">Fold Type:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->flyer_brochure_details['foldType'] ?? 'N/A' }}</span>
                                                </p>
                                                <p><span class="text-slate-500">Quantity:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->flyer_brochure_details['quantity'] ?? 'N/A' }}</span>
                                                </p>
                                                <p><span class="text-slate-500">Contact Info:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->flyer_brochure_details['contactInfo'] ?? 'N/A' }}</span>
                                                </p>
                                                @if(isset($item->flyer_brochure_details['validityDate']) && !empty($item->flyer_brochure_details['validityDate']))
                                                <p><span class="text-slate-500">Validity Date:</span> <span
                                                        class="font-bold text-slate-800">{{ $item->flyer_brochure_details['validityDate'] }}</span>
                                                </p>
                                                @endif
                                            </div>
                                            @if(isset($item->flyer_brochure_details['address']) && !empty($item->flyer_brochure_details['address']))
                                                <p class="mt-1 text-sm text-slate-600">
                                                    <span class="text-slate-500">Address:</span> {{ $item->flyer_brochure_details['address'] }}
                                                </p>
                                            @endif
                                            @if(isset($item->flyer_brochure_details['offerDetails']) && !empty($item->flyer_brochure_details['offerDetails']))
                                                <p class="mt-1 text-sm text-slate-600">
                                                    <span class="text-slate-500">Offer Details:</span> {{ $item->flyer_brochure_details['offerDetails'] }}
                                                </p>
                                            @endif
                                            @if (!empty($item->flyer_brochure_details['additionalMessage'] ?? ''))
                                                <p
                                                    class="mt-1 text-sm italic text-slate-600 border-t border-emerald-100 pt-1 mt-1">
                                                    "{{ $item->flyer_brochure_details['additionalMessage'] }}"
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                    @if ($item->design_id)
                                        <div class="mt-2 text-xs text-slate-600">
                                            Design attached — <a class="text-[--color-brand-blue] underline"
                                                href="{{ url('/design?design_id=' . $item->design_id) }}">Edit design</a>
                                        </div>
                                    @endif

                                    @if ($item->custom_requirements)
                                        <div class="mt-2">
                                            <p class="text-xs text-slate-500">Requirements:</p>
                                            <p class="text-sm text-slate-700">{{ $item->custom_requirements }}</p>
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-4 mt-4">
                                        <div class="flex items-center gap-2">
                                            <label class="text-sm font-medium text-slate-700">Qty:</label>
                                            <input type="number" min="1" value="{{ $item->quantity }}"
                                                class="w-16 rounded border border-slate-300 px-2 py-1 text-center text-sm focus:border-[--color-brand-blue] focus:outline-none quantity-input"
                                                data-cart-item-id="{{ $item->id }}"
                                                data-custom-requirements="{{ $item->custom_requirements ?? '' }}">
                                        </div>
                                        <button class="text-sm text-red-600 hover:text-red-800 font-medium remove-item-btn"
                                            data-cart-item-id="{{ $item->id }}">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-[--color-brand-deepblue]">
                                        {{ $item->formatted_total_price }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="flex justify-between items-center pt-4">
                        <button class="text-sm  hover:text-slate-800 font-medium clear-cart-btn">Clear
                            Cart</button>
                        <a href="{{ route('services') }}"
                            class="text-sm  hover:text-black font-medium shopping-btn">Continue
                            Shopping</a>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
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
            const updateUrl = `{{ route('cart.update', ['cartItem' => '__ID__']) }}`.replace('__ID__', cartItemId);
            fetch(updateUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
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
                        // Update cart summary in navbar (if present)
                        if (window.CartManager) {
                            window.CartManager.updateCartCount(data.item_count);
                        } else {
                            const badge = document.querySelector('.cart-count');
                            if (badge) badge.textContent = data.item_count;
                        }

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

            const removeUrl = `{{ route('cart.remove', ['cartItem' => '__ID__']) }}`.replace('__ID__', cartItemId);
            fetch(removeUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update cart summary in navbar (if present)
                        if (window.CartManager) {
                            window.CartManager.updateCartCount(data.item_count);
                        } else {
                            const badge = document.querySelector('.cart-count');
                            if (badge) badge.textContent = data.item_count;
                        }

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

            fetch('{{ route('cart.clear') }}', {
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
