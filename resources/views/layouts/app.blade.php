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
        
        <!-- Pusher loaded only if broadcasting key present -->
        @if(config('broadcasting.connections.pusher.key'))
            <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        @endif
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-50">
            @include('layouts.navigation')

            <!-- Page Content -->
            <main>
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
                                <li><a href="#" class="hover:text-white">Logo Design</a></li>
                                <li><a href="#" class="hover:text-white">Brand Identity</a></li>
                                <li><a href="#" class="hover:text-white">Print Design</a></li>
                                <li><a href="#" class="hover:text-white">Digital Design</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-md font-semibold mb-4">Company</h4>
                            <ul class="space-y-2 text-gray-300">
                                <li><a href="{{ route('about') }}" class="hover:text-white">About Us</a></li>
                                <li><a href="{{ route('contact') }}" class="hover:text-white">Contact</a></li>
                                <li><a href="#" class="hover:text-white">Portfolio</a></li>
                                <li><a href="#" class="hover:text-white">Blog</a></li>
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
        @include('components.floating-chat')
        @include('components.chatbot')
        
        <!-- Cart Count Update Script -->
        <script>
            // Update cart count on page load
            document.addEventListener('DOMContentLoaded', function() {
                fetchCartCount();
            });
            
            function fetchCartCount() {
                fetch('{{ route("cart.summary") }}')
                    .then(response => response.json())
                    .then(data => {
                        const cartCount = document.querySelector('.cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.item_count || 0;
                        }
                    })
                    .catch(error => console.error('Error fetching cart count:', error));
            }
        </script>

        @stack('scripts')
    </body>
</html>