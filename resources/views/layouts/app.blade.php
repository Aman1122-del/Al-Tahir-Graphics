<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Al-Tahir Graphics') }}</title>

    <!-- Local fonts fallback removed to ensure local-only assets; using system fonts via Tailwind -->

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js is bundled via Vite -->

    <!-- No external dependencies - using database polling -->
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        @include('layouts.navigation')

        <!-- Page Content -->
        <main class="container mx-auto">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-8 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Al-Tahir Graphics</h3>
                        <p class="text-gray-300">Professional graphic design services for your business needs.</p>
                    </div>
                    <div>
                        <h4 class="text-md font-semibold mb-4">Services</h4>
                        <ul class="space-y-2 text-gray-300">
                            <li><a href="/services/wedding-cards" class="hover:text-white">Wedding Cards</a></li>
                            <li><a href="/services/visiting-cards" class="hover:text-white">Visiting Cards</a></li>
                            <li><a href="/services/flyers" class="hover:text-white">Flyers</a></li>
                            <li><a href="/services/banners" class="hover:text-white">Banners</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-md font-semibold mb-4">Quick Links</h4>
                        <ul class="space-y-2 text-gray-300">
                            <li><a href="{{ route('about') }}" class="hover:text-white">About Us</a></li>
                            <li><a href="/contact" class="hover:text-white">Contact Us</a></li>
                            <li><a href="/chat" class="hover:text-white">Chats</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-md font-semibold mb-4">Contact</h4>
                        <div class="text-gray-300 space-y-2">
                            <p>Email: info@altahirgraphics.com</p>
                            <p>Phone: +92 334 573 5533</p>
                            <p>Address: Kaller saydan road. Oppo. Attock Oil pump, Station,, Mankiala, Pakistan</p>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                    <p>&copy; {{ date('Y') }} Al-Tahir Graphics. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Floating Chat Widget -->
    @auth
        @include('components.chat-widget')
    @endauth
    @include('components.chatbot')

    <!-- Cart Count Update Script -->
    <script>
        // Global cart management
        window.CartManager = {
            updateCartCount: function(count) {
                const cartCountElements = document.querySelectorAll('.cart-count');
                cartCountElements.forEach(el => {
                    el.textContent = count || 0;
                });
            },

            fetchCartCount: function() {
                fetch('{{ route('cart.summary') }}')
                    .then(response => response.json())
                    .then(data => {
                        this.updateCartCount(data.item_count);
                    })
                    .catch(error => {
                        console.error('Error fetching cart count:', error);
                        this.updateCartCount(0);
                    });
            },

            showToast: function(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className =
                    `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transform transition-all duration-300 translate-x-full opacity-0`;

                switch (type) {
                    case 'success':
                        toast.classList.add('bg-green-600');
                        break;
                    case 'error':
                        toast.classList.add('bg-red-600');
                        break;
                    case 'warning':
                        toast.classList.add('bg-yellow-600');
                        break;
                    default:
                        toast.classList.add('bg-blue-600');
                }

                toast.innerHTML = `
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                ${type === 'success' ?
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                                }
                            </svg>
                            <span>${message}</span>
                        </div>
                    `;

                document.body.appendChild(toast);

                // Animate in
                setTimeout(() => {
                    toast.classList.remove('translate-x-full', 'opacity-0');
                }, 100);

                // Auto-hide after 5 seconds
                setTimeout(() => {
                    toast.classList.add('translate-x-full', 'opacity-0');
                    setTimeout(() => {
                        if (toast.parentNode) {
                            document.body.removeChild(toast);
                        }
                    }, 300);
                }, 5000);
            },

            showCartAddedIndicator: function() {
                // Animate the cart icon in the navigation
                const cartIcon = document.querySelector('a[href*="cart"]');
                if (cartIcon) {
                    cartIcon.classList.add('animate-pulse');
                    setTimeout(() => {
                        cartIcon.classList.remove('animate-pulse');
                    }, 2000);
                }

                // Show a temporary "View Cart" button
                const viewCartBtn = document.createElement('div');
                viewCartBtn.className = 'fixed bottom-4 right-4 z-50';
                viewCartBtn.innerHTML = `
                        <a href="{{ route('cart.view') }}"
                           class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-700 transition-colors animate-bounce">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            View Cart
                        </a>
                    `;

                document.body.appendChild(viewCartBtn);

                // Auto-hide after 5 seconds
                setTimeout(() => {
                    viewCartBtn.style.opacity = '0';
                    viewCartBtn.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        if (viewCartBtn.parentNode) {
                            document.body.removeChild(viewCartBtn);
                        }
                    }, 300);
                }, 5000);
            }
        };

        // Update cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            window.CartManager.fetchCartCount();
        });
    </script>

    @stack('scripts')
</body>

</html>
