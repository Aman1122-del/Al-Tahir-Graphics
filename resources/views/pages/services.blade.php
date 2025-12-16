@extends('layouts.app')

@section('content')
    <div class="min-h-screen py-12 overflow-hidden" x-data="servicesPage()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header with fade-in animation -->
            <div class="text-center mb-16 animate-fade-in-up">
                <h1
                    class="text-4xl md:text-5xl mb-6 bg-gradient-to-r from-gray-900 to-blue-600 bg-clip-text text-transparent font-bold">
                    Our Printing Services
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Discover our comprehensive range of professional printing services.
                    From personal celebrations to business needs, we've got you covered.
                </p>
            </div>

            @php
                $categoryGroups = $services->groupBy('category');
            @endphp

            <!-- Product Grid - Show All Products First -->
            <div class="mb-16">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">All Products</h2>
                        <div class="w-20 h-1 bg-gradient-to-r from-blue-600 to-orange-500 rounded-full"></div>
                    </div>

                    <!-- Category Filter -->
                    <div class="relative" x-data="{ isOpen: false }">
                        <button @click="isOpen = !isOpen"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                            </svg>
                            Browse by Category
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="isOpen" @click.outside="isOpen = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                            <div class="py-1">
                                <button @click="filterByCategory('all'); isOpen = false"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    All Categories ({{ $services->count() }})
                                </button>
                                @foreach ($categoryGroups as $category => $categoryServices)
                                    <button @click="filterByCategory('{{ Str::slug($category) }}'); isOpen = false"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ ucwords($category) }} ({{ $categoryServices->count() }})
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                    @foreach ($services as $idx => $service)
                        <a href="{{ route('service.show', $service->slug) }}"
                            class="group relative overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200/80 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl hover:ring-blue-500/30 animate-scale-in product-card block"
                            style="animation-delay: {{ $idx * 50 }}ms"
                            data-category="{{ Str::slug($service->category) }}"
                            aria-label="View {{ $service->title }} details">

                            <div class="relative aspect-square overflow-hidden">
                                <img src="{{ $service->image_path }}" alt="{{ $service->title }}"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-125"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                <!-- Enhanced Fallback -->
                                <div
                                    class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100">
                                    <div class="text-center p-4">
                                        <svg class="mx-auto h-8 w-8 text-blue-400 mb-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-xs text-blue-600 font-medium">{{ $service->title }}</span>
                                    </div>
                                </div>

                                <!-- Badges -->
                                <div class="absolute top-2 left-2 flex flex-col gap-1">
                                    @if ($service->is_featured)
                                        <span
                                            class="rounded-full bg-orange-500 px-2 py-1 text-xs font-bold text-white shadow-lg animate-pulse">Best
                                            Seller</span>
                                    @endif
                                    @if (isset($service->created_at) && $service->created_at && $service->created_at->diffInDays() <= 30)
                                        <span
                                            class="rounded-full bg-green-500 px-2 py-1 text-xs font-bold text-white shadow-lg">New</span>
                                    @endif
                                </div>

                                <!-- Price Badge -->
                                <div
                                    class="absolute bottom-2 right-2 rounded-lg bg-white/95 backdrop-blur-sm px-2 py-1 text-xs font-bold text-gray-900 shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                    {{ $service->price_display ?: $service->formatted_price }}
                                </div>

                                <!-- Hover Overlay -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-blue-600/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end justify-center pb-4">
                                    <span
                                        class="text-white font-medium text-sm transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                        View Details
                                    </span>
                                </div>
                            </div>

                            <div class="p-3">
                                <h3
                                    class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2 mb-1">
                                    {{ $service->title }}
                                </h3>
                                <p class="text-xs text-gray-500">{{ ucwords($service->category) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>


            <!-- Call to Action Section -->
            <div class="bg-gradient-to-br from-blue-50/50 to-orange-50/50 rounded-2xl p-8 md:p-12 text-center relative overflow-hidden border hover:border-blue-200 transition-all duration-500 hover:shadow-xl animate-fade-in-up"
                style="animation-delay: 800ms">

                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-50 blur-[80px] bg-[rgba(173,109,244,0.5)] ">
                    <div class="absolute top-0 left-1/4 w-32 h-32 bg-blue-500 rounded-full blur-3xl animate-pulse"></div>
                    <div class="absolute bottom-0 right-1/4 w-48 h-48 bg-orange-500 rounded-full blur-3xl animate-pulse"
                        style="animation-delay: 2s"></div>
                </div>

                <h2
                    class="text-2xl md:text-3xl font-bold mb-4 relative z-10 bg-gradient-to-r from-gray-900 to-blue-600 bg-clip-text text-transparent">
                    Can't Find What You Need?
                </h2>
                <p class="text-lg text-gray-600 mb-6 max-w-2xl mx-auto relative z-10 leading-relaxed">
                    We offer custom printing solutions for unique projects.
                    Get in touch with our team to discuss your specific requirements.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center relative z-10">
                    <a href="{{ route('contact') }}"
                        class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 hover:scale-105 transition-all duration-300 hover:shadow-xl focus:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        Contact Us
                    </a>
                    <a href="#"
                        class="inline-flex items-center justify-center px-6 py-3 border border-blue-200 text-blue-600 font-medium rounded-lg hover:bg-blue-50 hover:scale-105 transition-all duration-300 hover:shadow-lg hover:border-blue-300 focus:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        Request Quote
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Product Modal for cart functionality -->
    @include('components.product-modal')
@endsection

@push('scripts')
    <script>
        // Services page Alpine.js component
        function servicesPage() {
            return {
                hoveredCard: null,
                currentFilter: 'all',

                init() {
                    this.updateCartCount();
                    // Trigger animations after component is initialized
                    this.$nextTick(() => {
                        this.animateElements();
                    });
                },

                setHoveredCard(cardId) {
                    this.hoveredCard = cardId;
                },

                scrollToCategory(categorySlug) {
                    const element = document.getElementById('category-' + categorySlug);
                    if (element) {
                        element.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start',
                            inline: 'nearest'
                        });
                    }
                },

                filterByCategory(categorySlug) {
                    this.currentFilter = categorySlug;
                    const allCards = document.querySelectorAll('.product-card');

                    allCards.forEach((card, index) => {
                        const cardCategory = card.getAttribute('data-category');
                        const shouldShow = categorySlug === 'all' || cardCategory === categorySlug;

                        if (shouldShow) {
                            card.classList.remove('hidden');
                            card.style.animationDelay = (index * 50) + 'ms';
                            setTimeout(() => {
                                card.classList.add('animate-visible');
                            }, index * 50);
                        } else {
                            card.classList.add('hidden');
                            card.classList.remove('animate-visible');
                        }
                    });

                    // Scroll to top of products section
                    const firstCard = document.querySelector('.product-card');
                    if (firstCard) {
                        firstCard.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                },

                animateElements() {
                    // Add staggered animation to category cards
                    const categoryCards = document.querySelectorAll('.animate-slide-in-up');
                    categoryCards.forEach((card, index) => {
                        setTimeout(() => {
                            card.classList.add('animate-visible');
                        }, index * 200);
                    });

                    // Add staggered animation to service items
                    const serviceItems = document.querySelectorAll('.animate-scale-in');
                    serviceItems.forEach((item, index) => {
                        setTimeout(() => {
                            item.classList.add('animate-visible');
                        }, index * 100);
                    });
                },

                updateCartCount() {
                    try {
                        const items = JSON.parse(localStorage.getItem('cart') || '[]');
                        const count = items.reduce((a, b) => a + (parseInt(b.quantity) || 0), 0);
                        const el = document.querySelector('.cart-count');
                        if (el) el.textContent = count;
                    } catch (e) {}
                }
            }
        }

        // Per-service grouping & filtering (preserved for compatibility)
        function serviceSamples({
            samples,
            categories
        }) {
            return {
                raw: samples || [],
                categories: categories || [],
                activeCategory: 'all',
                searchQuery: '',
                sortBy: 'newest',
                pageSize: 12,
                pageByCategory: {},

                get countsTotal() {
                    return this.filterAll().length;
                },

                init() {
                    this.categories = Array.from(new Set(this.categories));
                    this.categories.forEach(c => {
                        if (!(c in this.pageByCategory)) this.pageByCategory[c] = 1;
                    });
                },

                setCategory(cat) {
                    this.activeCategory = cat;
                    if (!(cat in this.pageByCategory)) this.pageByCategory[cat] = 1;
                },
                refresh() {
                    /* reactive via getters */
                },

                getCount(cat) {
                    return this.filterByCategory(cat).length;
                },

                filterAll() {
                    let arr = [...this.raw];
                    if (this.searchQuery.trim()) {
                        const q = this.searchQuery.toLowerCase();
                        arr = arr.filter(s => (s.title || '').toLowerCase().includes(q) || (s.description || '')
                            .toLowerCase().includes(q) || (s.sub_category || '').toLowerCase().includes(q));
                    }
                    return this.sort(arr);
                },

                filterByCategory(cat) {
                    return this.filterAll().filter(s => (s.sub_category || '') === cat);
                },

                getVisible(cat) {
                    const page = this.pageByCategory[cat] || 1;
                    return this.filterByCategory(cat).slice(0, page * this.pageSize);
                },

                hasMore(cat) {
                    return this.getVisible(cat).length < this.filterByCategory(cat).length;
                },
                loadMore(cat) {
                    this.pageByCategory[cat] = (this.pageByCategory[cat] || 1) + 1;
                },

                isNew(s) {
                    if (!s.created_at) return false;
                    const d = new Date(s.created_at);
                    const t = new Date();
                    t.setDate(t.getDate() - 30);
                    return d > t;
                },

                sort(arr) {
                    switch (this.sortBy) {
                        case 'newest':
                            return arr.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                        case 'popular':
                            return arr.sort((a, b) => (b.sort_order || 0) - (a.sort_order || 0));
                        case 'price-low':
                            return arr.sort((a, b) => (a.unit_price || 0) - (b.unit_price || 0));
                        case 'price-high':
                            return arr.sort((a, b) => (b.unit_price || 0) - (a.unit_price || 0));
                        case 'name':
                            return arr.sort((a, b) => (a.title || '').localeCompare(b.title || ''));
                        default:
                            return arr;
                    }
                },

                getImageUrl(path) {
                    if (!path) return '';
                    if (path.startsWith('http')) return path;
                    if (path.startsWith('/storage/')) return path;
                    if (path.startsWith('storage/')) return '/' + path;
                    if (path.startsWith('/')) return path;
                    return '/storage/' + path;
                },

                openSampleModal(sample) {
                    const productData = {
                        id: sample.id,
                        title: sample.title,
                        description: sample.description,
                        image: this.getImageUrl(sample.image_path),
                        formattedPrice: sample.formatted_price,
                        unitPrice: sample.unit_price,
                        serviceId: sample.service_id,
                        sampleId: sample.id,
                        serviceName: sample.service ? sample.service.title : 'Service',
                        badges: this.isNew(sample) ? [{
                            type: 'new',
                            text: 'New'
                        }] : [],
                        options: {
                            personalization: true,
                            fileUpload: true,
                            sizes: [{
                                    value: 'small',
                                    label: 'Small (4x6)',
                                    price: ''
                                },
                                {
                                    value: 'medium',
                                    label: 'Medium (5x7)',
                                    price: '+PKR 50'
                                },
                                {
                                    value: 'large',
                                    label: 'Large (8x10)',
                                    price: '+PKR 150'
                                }
                            ],
                            colors: [{
                                    value: 'standard',
                                    label: 'Standard'
                                },
                                {
                                    value: 'premium',
                                    label: 'Premium Finish'
                                }
                            ],
                            extras: [{
                                    value: 'rush',
                                    label: 'Rush Delivery (24hrs)',
                                    price: '200'
                                },
                                {
                                    value: 'lamination',
                                    label: 'Lamination',
                                    price: '100'
                                }
                            ]
                        }
                    };
                    if (typeof openProductModal === 'function') openProductModal(productData);
                }
            }
        }

        // Global function to open product modal (preserved for compatibility)
        function openProductModal(productData) {
            if (typeof Alpine !== 'undefined' && Alpine.store('productModal')) {
                Alpine.store('productModal').openModal(productData);
            } else {
                const modal = document.querySelector('[x-data*="productModal"]').__x.$data;
                if (modal && typeof modal.openModal === 'function') {
                    modal.openModal(productData);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            function updateNavbarCount() {
                try {
                    const items = JSON.parse(localStorage.getItem('cart') || '[]');
                    const count = items.reduce((a, b) => a + (parseInt(b.quantity) || 0), 0);
                    const el = document.querySelector('.cart-count');
                    if (el) el.textContent = count;
                } catch (e) {}
            }
            updateNavbarCount();
        });
    </script>
@endpush
