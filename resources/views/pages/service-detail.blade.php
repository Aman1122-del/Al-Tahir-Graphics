<style>
    @media (max-width: 627px) {
        .cross-btn {
            top: 4%;
            right: 8% !important;
        }
    }
</style>

@extends('layouts.app')

@section('head')
    @if ($service->meta_title)
        <title>{{ $service->meta_title }}</title>
        <meta property="og:title" content="{{ $service->meta_title }}" />
    @else
        <title>{{ $service->title }} - Al-Tahir Graphics</title>
        <meta property="og:title" content="{{ $service->title }} - Al-Tahir Graphics" />
    @endif

    @if ($service->meta_description)
        <meta name="description" content="{{ $service->meta_description }}" />
        <meta property="og:description" content="{{ $service->meta_description }}" />
    @endif

    @if ($service->meta_keywords)
        <meta name="keywords" content="{{ $service->meta_keywords }}" />
    @endif

    <meta property="og:image" content="{{ $service->image_path }}" />
    <meta property="og:url" content="{{ route('service.show', $service->slug) }}" />
    <meta property="og:type" content="product" />
@endsection

@section('content')
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm text-slate-600 p-4" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('home') }}" class="hover:text-[--color-brand-blue] transition-colors">Home</a></li>
            <li><span class="mx-2 text-slate-400">›</span></li>
            <li><a href="{{ route('services') }}" class="hover:text-[--color-brand-blue] transition-colors">Services</a>
            </li>
            <li><span class="mx-2 text-slate-400">›</span></li>
            <li class="text-[--color-brand-deepblue] font-medium">{{ $service->title }}</li>
        </ol>
    </nav>
    <!-- Samples Only -->
    @php
        $allSamples = $service->samples()->where('is_active', true)->orderBy('sort_order')->get();
        $samplesWithCategories = $allSamples->where('sub_category', '!=', null);
        $samplesWithoutCategories = $allSamples->where('sub_category', null);
    @endphp

    @if ($allSamples->count() > 0)
        <!-- Include Product Modal -->
        @include('components.product-modal')

        <div class="space-y-4" x-data="sampleFilter()" x-init="initSamples({{ $allSamples->toJson() }}, {{ $subCategories->toJson() }})">

            <!-- Category Filter Tabs -->
            @if ($subCategories->count() > 0)
                <div class="border-b border-slate-200">
                    <nav class="-mb-px flex flex-wrap gap-2" aria-label="Filter categories">
                        <button
                            class="category-tab whitespace-nowrap border-b-2 border-[--color-brand-blue] px-4 py-2 text-sm font-medium text-[--color-brand-blue]"
                            :class="activeCategory === 'all' ? 'border-[--color-brand-blue] text-[--color-brand-blue]' :
                                'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                            @click="filterByCategory('all')" role="tab" aria-selected="true">
                            Show All
                            <span class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs"
                                :class="activeCategory === 'all' ? 'bg-[--color-brand-blue] text-white' :
                                    'bg-slate-100 text-slate-600'"
                                x-text="allSamples.length"></span>
                        </button>
                        @foreach ($subCategories as $category)
                            <button
                                class="category-tab whitespace-nowrap border-b-2 border-transparent px-4 py-2 text-sm font-medium text-slate-500 hover:text-slate-700 hover:border-slate-300"
                                :class="activeCategory === '{{ $category }}' ?
                                    'border-[--color-brand-blue] text-[--color-brand-blue]' :
                                    'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                                @click="filterByCategory('{{ $category }}')" role="tab">
                                {{ $category }}
                                @php
                                    $categoryCount = $service
                                        ->samples()
                                        ->where('is_active', true)
                                        ->where('sub_category', $category)
                                        ->count();
                                @endphp
                                <span class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs"
                                    :class="activeCategory === '{{ $category }}' ? 'bg-[--color-brand-blue] text-white' :
                                        'bg-slate-100 text-slate-600'">{{ $categoryCount }}</span>
                            </button>
                        @endforeach
                    </nav>
                </div>
            @endif

            <!-- Products Grid -->
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3" id="samplesGrid">
                <template x-for="(sample, index) in paginatedSamples" :key="sample.id">
                    <article
                        class="group relative overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-black/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer"
                        @click="openSampleModal(sample)" :data-category="sample.sub_category || 'uncategorized'"
                        role="button" tabindex="0"
                        @keydown="if($event.key === 'Enter' || $event.key === ' ') { $event.preventDefault(); openSampleModal(sample); }">

                        <!-- Product Image -->
                        <div class="relative h-48 overflow-hidden">
                            <img :src="sample.full_image_url" :alt="sample.title"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105 cursor-zoom-in"
                                loading="lazy" @click.stop="openZoom(sample.full_image_url)" />

                            <!-- Trust Badges -->
                            <div class="absolute top-3 left-3 flex flex-col gap-1">
                                <template x-if="isNewSample(sample)">
                                    <span
                                        class="inline-flex items-center rounded-full bg-green-500 px-2 py-1 text-xs font-medium text-white shadow-lg">
                                        New
                                    </span>
                                </template>
                                <template x-if="index < 3">
                                    <span
                                        class="inline-flex items-center rounded-full bg-[--color-brand-orange] px-2 py-1 text-xs font-medium text-white shadow-lg">
                                        <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        Popular
                                    </span>
                                </template>
                            </div>

                            <!-- Price Badge -->
                            <div class="absolute bottom-3 right-3 rounded-lg bg-white/95 px-2 py-1 text-sm font-bold text-[--color-brand-deepblue] shadow-lg backdrop-blur-sm"
                                x-text="sample.formatted_price"></div>
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <h4 class="font-bold text-[--color-brand-deepblue] group-hover:text-[--color-brand-blue] transition-colors"
                                x-text="sample.title"></h4>
                            <template x-if="sample.description">
                                <p class="mt-1 text-sm text-slate-600 line-clamp-2"
                                    x-text="sample.description.length > 60 ? sample.description.substring(0, 60) + '...' : sample.description">
                                </p>
                            </template>

                            <!-- Category Badge -->
                            <template x-if="sample.sub_category">
                                <div class="mt-2">
                                    <span
                                        class="inline-flex items-center rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700"
                                        x-text="sample.sub_category"></span>
                                </div>
                            </template>
                            <!-- Quick Add to Cart -->
                            <div class="mt-3 flex items-center justify-between gap-2">
                                <input type="number" min="1" value="1"
                                    class="w-16 rounded border border-slate-300 px-2 py-1 text-center text-sm sample-qty"
                                    :data-sample-id="sample.id">
                                <button class="btn-primary text-xs px-3 py-2 add-sample-to-cart"
                                    :data-service-id="sample.service_id || {{ $service->id }}"
                                    :data-sample-id="sample.id" :data-unit-price="sample.unit_price"
                                    :disabled="isAddingToCart[sample.id]" @click.stop="addToCartDirectly(sample)"
                                    aria-label="Add to cart">
                                    <svg x-show="!isAddingToCart[sample.id]" class="w-3 h-3 mr-1" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 12H6L5 9z" />
                                    </svg>
                                    <svg x-show="isAddingToCart[sample.id]" class="w-3 h-3 mr-1 animate-spin" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                    </svg>
                                    <span x-text="isAddingToCart[sample.id] ? 'Adding...' : 'Add to Cart'"></span>
                                </button>
                            </div>
                        </div>
                    </article>
                </template>
            </div>

            <!-- Load More / Pagination -->
            <div class="text-center" x-show="hasMoreSamples">
                <button class="btn-primary" @click="loadMore()" :disabled="isLoading">
                    <span x-show="!isLoading">Load More Designs</span>
                    <span x-show="isLoading" class="flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        Loading...
                    </span>
                </button>
            </div>

            <!-- No Results -->
            <div x-show="filteredSamples.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-slate-900">No designs found</h3>
                <p class="mt-2 text-slate-500">Try selecting a different category or view all designs.</p>
            </div>
            <div x-show="zoomedImage" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[5000] flex items-center justify-center bg-black/90 p-4 backdrop-blur-sm"
                style="display: none;" @click="closeZoom()">

                <img :src="zoomedImage"
                    class="max-h-[90vh] max-w-[90vw] object-contain rounded-lg shadow-2xl relative z-[5001]" @click.stop>

                <button style="top: 4%; right:23%" @click="closeZoom()"
                    class="cross-btn absolute   text-white bg-black/50 hover:bg-black/80 rounded-full p-2 transition-colors"
                    style="z-index: 9999;">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>
    @else
        <!-- Direct Order Section -->
        <div class="rounded-xl bg-gradient-to-br from-slate-50 to-white p-6 ring-1 ring-black/5">
            <h3 class="mb-4 text-lg font-semibold text-[--color-brand-deepblue]">Ready to Order</h3>
            <p class="mb-4 text-sm text-slate-600">This service is ready for immediate ordering. Select your quantity and
                add to cart.</p>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <label for="serviceQty" class="text-sm font-medium text-slate-700">Quantity:</label>
                    <div class="flex items-center">
                        <button type="button"
                            class="flex h-8 w-8 items-center justify-center rounded-l-lg border border-r-0 border-slate-300 text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue]"
                            onclick="decreaseServiceQty()" aria-label="Decrease quantity">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </button>
                        <input type="number" min="1" value="1"
                            class="w-16 border border-slate-300 px-2 py-1 text-center text-sm focus:border-[--color-brand-blue] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue]"
                            id="serviceQty">
                        <button type="button"
                            class="flex h-8 w-8 items-center justify-center rounded-r-lg border border-l-0 border-slate-300 text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue]"
                            onclick="increaseServiceQty()" aria-label="Increase quantity">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </button>
                    </div>
                </div>
                <button class="btn-primary flex-1" id="addServiceToCart" data-service-id="{{ $service->id }}"
                    data-unit-price="{{ $service->price }}" aria-label="Add {{ $service->title }} to cart">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 12H6L5 9z" />
                    </svg>
                    Add to Cart - {{ $service->price_display ?: $service->formatted_price }}
                </button>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        function changeMainImage(imageSrc) {
            document.getElementById('mainImage').src = imageSrc;

            // Update gallery thumb states
            document.querySelectorAll('.gallery-thumb').forEach(thumb => {
                thumb.classList.remove('ring-2', 'ring-[--color-brand-blue]');
                thumb.classList.add('ring-1', 'ring-gray-200');
            });

            // Highlight active thumb
            event.target.closest('.gallery-thumb').classList.remove('ring-1', 'ring-gray-200');
            event.target.closest('.gallery-thumb').classList.add('ring-2', 'ring-[--color-brand-blue]');
        }

        function increaseServiceQty() {
            const input = document.getElementById('serviceQty');
            const currentValue = parseInt(input.value) || 1;
            if (currentValue < 999) {
                input.value = currentValue + 1;
            }
        }

        function decreaseServiceQty() {
            const input = document.getElementById('serviceQty');
            const currentValue = parseInt(input.value) || 1;
            if (currentValue > 1) {
                input.value = currentValue - 1;
            }
        }

        // Alpine.js Sample Filter Component
        function sampleFilter() {
            return {
                allSamples: [],
                filteredSamples: [],
                paginatedSamples: [],
                categories: [],
                activeCategory: 'all',
                itemsPerPage: 9,
                currentPage: 1,
                isLoading: false,
                isAddingToCart: {},
                zoomedImage: null,

                // 2. ADD THESE TWO NEW FUNCTIONS
                openZoom(imageUrl) {
                    this.zoomedImage = imageUrl;
                    document.body.style.overflow = 'hidden'; // Disable scrolling
                },

                closeZoom() {
                    this.zoomedImage = null;
                    document.body.style.overflow = ''; // Enable scrolling
                },

                get hasMoreSamples() {
                    return this.paginatedSamples.length < this.filteredSamples.length;
                },

                initSamples(samples, categories) {
                    this.allSamples = samples;
                    this.categories = categories;
                    this.filteredSamples = [...samples];
                    // Initialize isAddingToCart for all samples
                    this.isAddingToCart = {};
                    samples.forEach(sample => {
                        this.isAddingToCart[sample.id] = false;
                    });
                    this.updatePagination();
                },

                filterByCategory(category) {
                    this.activeCategory = category;
                    this.currentPage = 1;

                    if (category === 'all') {
                        this.filteredSamples = [...this.allSamples];
                    } else {
                        this.filteredSamples = this.allSamples.filter(sample =>
                            sample.sub_category === category
                        );
                    }

                    this.updatePagination();
                },

                updatePagination() {
                    const endIndex = this.currentPage * this.itemsPerPage;
                    this.paginatedSamples = this.filteredSamples.slice(0, endIndex);
                },

                loadMore() {
                    if (this.hasMoreSamples && !this.isLoading) {
                        this.isLoading = true;
                        // Simulate loading delay for better UX
                        setTimeout(() => {
                            this.currentPage++;
                            this.updatePagination();
                            this.isLoading = false;
                        }, 300);
                    }
                },

                openSampleModal(sample) {
                    const productData = {
                        id: sample.id,
                        title: sample.title,
                        description: sample.description,
                        image: sample.full_image_url, // <--- CHANGE HERE
                        formattedPrice: sample.formatted_price,
                        unitPrice: sample.unit_price,
                        serviceId: sample.service_id,
                        sampleId: sample.id,
                        serviceName: '{{ $service->title }}' + (sample.sub_category ? ' - ' + sample.sub_category :
                            ''),
                        badges: this.getSampleBadges(sample),
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

                    // Call global modal function
                    if (typeof openProductModal === 'function') {
                        openProductModal(productData);
                    }
                },

                getSampleBadges(sample) {
                    const badges = [];

                    // Check if sample is new (created within last 30 days)
                    if (this.isNewSample(sample)) {
                        badges.push({
                            type: 'new',
                            text: 'New'
                        });
                    }

                    return badges;
                },

                isNewSample(sample) {
                    if (!sample.created_at) return false;
                    const createdDate = new Date(sample.created_at);
                    const thirtyDaysAgo = new Date();
                    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
                    return createdDate > thirtyDaysAgo;
                },

                async addToCartDirectly(sample) {
                    console.log('Add to Cart Directly clicked for sample:', sample);
                    console.log('isAddingToCart object:', this.isAddingToCart);

                    // Set loading state
                    this.isAddingToCart = this.isAddingToCart || {};
                    this.isAddingToCart[sample.id] = true;

                    try {
                        const qtyInput = document.querySelector(`.sample-qty[data-sample-id="${sample.id}"]`);
                        const quantity = parseInt(qtyInput?.value || 1);

                        const item = {
                            service_id: parseInt(sample.service_id || {{ $service->id }}),
                            service_sample_id: parseInt(sample.id),
                            quantity: quantity,
                            unit_price: parseFloat(sample.unit_price) || 0,
                            custom_requirements: ''
                        };

                        // Optimistic update - add to local storage first
                        const cart = this.getLocalCart();
                        const existing = cart.find(i =>
                            i.service_id === item.service_id &&
                            i.service_sample_id === item.service_sample_id &&
                            i.unit_price === item.unit_price
                        );

                        if (existing) {
                            existing.quantity += item.quantity;
                        } else {
                            cart.push(item);
                        }
                        this.setLocalCart(cart);
                        this.updateNavbarCount();

                        // Sync to server
                        const response = await fetch('{{ route('cart.add') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(item)
                        });

                        if (!response.ok) {
                            // Revert optimistic update if server failed
                            if (existing) {
                                existing.quantity -= item.quantity;
                                if (existing.quantity <= 0) {
                                    const index = cart.indexOf(existing);
                                    cart.splice(index, 1);
                                }
                            } else {
                                cart.pop();
                            }
                            this.setLocalCart(cart);
                            this.updateNavbarCount();

                            throw new Error('Server error: Unable to add to cart');
                        }

                        const data = await response.json();

                        // Show success notification
                        this.showToast(data.message || 'Item added to cart successfully!', 'success');

                        // Update cart count with server response
                        if (data.item_count !== undefined) {
                            if (window.CartManager) {
                                window.CartManager.updateCartCount(data.item_count);
                            } else {
                                const cartCountEl = document.querySelector('.cart-count');
                                if (cartCountEl) cartCountEl.textContent = data.item_count;
                            }
                        }

                        // Show cart animation/indicator
                        if (window.CartManager) {
                            window.CartManager.showCartAddedIndicator();
                        } else {
                            this.showCartAddedIndicator();
                        }

                    } catch (error) {
                        console.error('Error adding to cart:', error);
                        this.showToast('Error adding item to cart. Please try again.', 'error');
                    } finally {
                        // Clear loading state
                        this.isAddingToCart[sample.id] = false;
                    }
                },

                showSuccessMessage(message) {
                    const notification = document.createElement('div');
                    notification.className =
                        'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                    notification.textContent = message;
                    document.body.appendChild(notification);

                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                },

                showToast(message, type = 'success') {
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

                    // Auto-hide after 5 seconds (longer for better visibility)
                    setTimeout(() => {
                        toast.classList.add('translate-x-full', 'opacity-0');
                        setTimeout(() => {
                            if (toast.parentNode) {
                                document.body.removeChild(toast);
                            }
                        }, 300);
                    }, 5000);
                },

                showCartAddedIndicator() {
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
                },

                // Helper methods for cart management
                getLocalCart() {
                    try {
                        return JSON.parse(localStorage.getItem('cart') || '[]');
                    } catch (e) {
                        return [];
                    }
                },

                setLocalCart(items) {
                    localStorage.setItem('cart', JSON.stringify(items));
                },

                updateNavbarCount() {
                    const items = this.getLocalCart();
                    const count = items.reduce((a, b) => a + (parseInt(b.quantity) || 0), 0);
                    const el = document.querySelector('.cart-count');
                    if (el) el.textContent = count;
                }
            };
        }

        // Global function to open product modal
        function openProductModal(productData) {
            if (typeof Alpine !== 'undefined' && Alpine.store('productModal')) {
                Alpine.store('productModal').openModal(productData);
            } else {
                // Fallback for manual Alpine component access
                const modal = document.querySelector('[x-data*="productModal"]').__x.$data;
                if (modal && typeof modal.openModal === 'function') {
                    modal.openModal(productData);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            function getLocalCart() {
                try {
                    return JSON.parse(localStorage.getItem('cart') || '[]');
                } catch (e) {
                    return [];
                }
            }

            function setLocalCart(items) {
                localStorage.setItem('cart', JSON.stringify(items));
            }

            function updateNavbarCount() {
                const items = getLocalCart();
                const count = items.reduce((a, b) => a + (parseInt(b.quantity) || 0), 0);
                const el = document.querySelector('.cart-count');
                if (el) el.textContent = count;
            }
            async function syncToServer(item) {
                try {
                    const res = await fetch('{{ route('cart.add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(item)
                    });
                    return await res.json();
                } catch (e) {
                    return {
                        success: false
                    };
                }
            }

            // Toast notification function
            function showToast(message, type = 'success') {
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
            }

            // Cart added indicator function
            function showCartAddedIndicator() {
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

            // Handle direct service add to cart (for services without samples)
            document.addEventListener('click', async function(e) {
                if (e.target.id === 'addServiceToCart') {
                    e.preventDefault();
                    const btn = e.target;
                    const serviceId = btn.dataset.serviceId;
                    const unitPrice = parseFloat(btn.dataset.unitPrice);
                    const quantity = parseInt(document.getElementById('serviceQty').value || 1);

                    // Set loading state
                    btn.disabled = true;
                    const originalText = btn.innerHTML;
                    btn.innerHTML = `
                <svg class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                </svg>
                Adding...
            `;

                    try {
                        const item = {
                            service_id: parseInt(serviceId),
                            quantity: quantity,
                            unit_price: unitPrice,
                            custom_requirements: ''
                        };

                        // Optimistic update
                        const cart = getLocalCart();
                        const existing = cart.find(i => i.service_id == serviceId && !i
                            .service_sample_id && i.unit_price == unitPrice);
                        if (existing) {
                            existing.quantity += quantity;
                        } else {
                            cart.push(item);
                        }
                        setLocalCart(cart);
                        updateNavbarCount();

                        // Sync to server
                        const response = await fetch('{{ route('cart.add') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(item)
                        });

                        if (!response.ok) {
                            // Revert optimistic update if server failed
                            if (existing) {
                                existing.quantity -= quantity;
                                if (existing.quantity <= 0) {
                                    const index = cart.indexOf(existing);
                                    cart.splice(index, 1);
                                }
                            } else {
                                cart.pop();
                            }
                            setLocalCart(cart);
                            updateNavbarCount();
                            throw new Error('Server error: Unable to add to cart');
                        }

                        const data = await response.json();

                        // Show success notification
                        showToast(data.message || 'Item added to cart successfully!', 'success');

                        // Update cart count with server response
                        if (data.item_count !== undefined) {
                            if (window.CartManager) {
                                window.CartManager.updateCartCount(data.item_count);
                            } else {
                                const cartCountEl = document.querySelector('.cart-count');
                                if (cartCountEl) cartCountEl.textContent = data.item_count;
                            }
                        }

                        // Show cart animation/indicator
                        if (window.CartManager) {
                            window.CartManager.showCartAddedIndicator();
                        } else {
                            showCartAddedIndicator();
                        }

                    } catch (error) {
                        console.error('Error adding to cart:', error);
                        showToast('Error adding item to cart. Please try again.', 'error');
                    } finally {
                        // Restore button state
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                }
            });

            updateNavbarCount();
        });
    </script>
@endpush
