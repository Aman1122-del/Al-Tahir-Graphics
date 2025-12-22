<style>
    *:focus {
        outline: none !important;
    }

    a:focus {
        outline: none !important;
    }

    /* Hide scrollbar for seamless look if needed */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<nav x-data="{ open: false }"
    class="sticky top-0 z-50 bg-[radial-gradient(60%_120%_at_50%_50%,hsla(0,0%,100%,0)_0,rgba(252,205,238,.5)_100%)] backdrop-blur-sm bg-white/80 border-b border-gray-200 transition-all duration-300">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="block w-auto">
                        <x-application-logo class="block h-10 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')"
                        class="hover:text-blue-600 transition-colors duration-200">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link :href="route('services')" :active="request()->routeIs('services')"
                        class="hover:text-blue-600 transition-colors duration-200">
                        {{ __('Services') }}
                    </x-nav-link>
                    <x-nav-link :href="route('design')" :active="request()->routeIs('design')"
                        class="hover:text-blue-600 transition-colors duration-200">
                        {{ __('Design') }}
                    </x-nav-link>
                    <x-nav-link :href="route('about')" :active="request()->routeIs('about')"
                        class="hover:text-blue-600 transition-colors duration-200">
                        {{ __('About Us') }}
                    </x-nav-link>

                    <x-nav-link :href="route('contact')" :active="request()->routeIs('contact')"
                        class="hover:text-blue-600 transition-colors duration-200">
                        {{ __('Contact Us') }}
                    </x-nav-link>

                    @auth
                        <x-nav-link :href="route('chat.index')" :active="request()->routeIs('chat.*')"
                            class="hover:text-blue-600 transition-colors duration-200">
                            {{ __('Chat') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:space-x-4">

                <a href="{{ route('cart.view') }}"
                    class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors duration-200 rounded-full hover:bg-gray-100"
                    aria-label="Shopping cart">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01">
                        </path>
                    </svg>
                    <span
                        class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center cart-count shadow-sm ring-2 ring-white">
                        0
                    </span>
                </a>

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('dashboard')">{{ __('Dashboard') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('returns.index')">{{ __('My Returns') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('login') }}"
                            class="text-sm font-medium text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md hover:bg-gray-50 transition">Login</a>
                        <a href="{{ route('register') }}"
                            class="text-sm font-medium bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 shadow-sm transition">Register</a>
                    </div>
                @endauth
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-900 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="sm:hidden bg-white border-t border-gray-100 shadow-lg absolute w-full left-0 z-40">

        <div class="pt-2 pb-3 space-y-1 px-2">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Home') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('services')" :active="request()->routeIs('services')">
                {{ __('Services') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('design')" :active="request()->routeIs('design')">
                {{ __('Design') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('about')" :active="request()->routeIs('about')">
                {{ __('About Us') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('contact')" :active="request()->routeIs('contact')">
                {{ __('Contact Us') }}
            </x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('chat.index')" :active="request()->routeIs('chat.*')">
                    {{ __('Chat') }}
                </x-responsive-nav-link>
            @endauth

            <x-responsive-nav-link :href="route('cart.view')" class="flex items-center justify-between group">
                <span class="flex items-center">
                    <svg class="h-5 w-5 mr-2 text-gray-500 group-hover:text-blue-600" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01">
                        </path>
                    </svg>
                    {{ __('My Cart') }}
                </span>
                <span
                    class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full cart-count">0</span>
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-4 border-t border-gray-200 bg-gray-50">
            @auth
                <div class="px-4 flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1 px-2">
                    <x-responsive-nav-link :href="route('dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('returns.index')">
                        {{ __('My Returns') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="text-red-600 hover:text-red-700 hover:bg-red-50">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="mt-1 space-y-1 px-2">
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Login') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')" class="text-blue-600 font-semibold">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>
