<style>
    @media (max-width: 627px) {
        .cross-btn {
            top: 4%;
            right: 8% !important;
        }
    }

    [x-cloak] {
        display: none !important;
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

    @php
        $allSamples = $service->samples()->where('is_active', true)->orderBy('sort_order')->get();
        $subCategories = $subCategories ?? collect();
    @endphp

    {{-- Main Container with x-data --}}
    <div x-data="sampleFilter()" x-init="initSamples({{ $allSamples->toJson() }}, {{ $subCategories->toJson() }})">

        @if ($allSamples->count() > 0)
            @include('components.product-modal')

            <div class="space-y-4">
                @if ($subCategories->count() > 0)
                    <div class="border-b border-slate-200">
                        <nav class="-mb-px flex flex-wrap gap-2" aria-label="Filter categories">
                            <button class="category-tab whitespace-nowrap border-b-2 px-4 py-2 text-sm font-medium"
                                :class="activeCategory === 'all' ?
                                    'border-[--color-brand-blue] text-[--color-brand-blue]' :
                                    'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                                @click="filterByCategory('all')">
                                Show All
                                <span class="ml-2 rounded-full px-2 py-0.5 text-xs"
                                    :class="activeCategory === 'all' ? 'bg-[--color-brand-blue] text-white' :
                                        'bg-slate-100 text-slate-600'"
                                    x-text="allSamples.length"></span>
                            </button>
                            @foreach ($subCategories as $category)
                                <button class="category-tab whitespace-nowrap border-b-2 px-4 py-2 text-sm font-medium"
                                    :class="activeCategory === '{{ $category }}' ?
                                        'border-[--color-brand-blue] text-[--color-brand-blue]' :
                                        'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                                    @click="filterByCategory('{{ $category }}')">
                                    {{ $category }}
                                    <span class="ml-2 rounded-full px-2 py-0.5 text-xs"
                                        :class="activeCategory === '{{ $category }}' ?
                                            'bg-[--color-brand-blue] text-white' : 'bg-slate-100 text-slate-600'">
                                        {{ $allSamples->where('sub_category', $category)->count() }}
                                    </span>
                                </button>
                            @endforeach
                        </nav>
                    </div>
                @endif

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3" id="samplesGrid">
                    <template x-for="(sample, index) in paginatedSamples" :key="sample.id">
                        <article
                            class="group relative overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-black/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer"
                            @click="openSampleModal(sample)">

                            <div class="relative h-48 overflow-hidden">
                                <img :src="sample.full_image_url" :alt="sample.title"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105 cursor-zoom-in"
                                    loading="lazy" @click.stop="openZoom(sample.full_image_url)" />
                                <div class="absolute bottom-3 right-3 rounded-lg bg-white/95 px-2 py-1 text-sm font-bold text-[--color-brand-deepblue] shadow-lg backdrop-blur-sm"
                                    x-text="sample.formatted_price"></div>
                            </div>

                            <div class="p-4">
                                <h4 class="font-bold text-[--color-brand-deepblue] group-hover:text-[--color-brand-blue] transition-colors"
                                    x-text="sample.title"></h4>
                                <div class="mt-3 flex items-center justify-between gap-2">
                                    <input type="number" min="1" value="1"
                                        class="w-16 rounded border border-slate-300 px-2 py-1 text-center text-sm sample-qty"
                                        :data-sample-id="sample.id" @click.stop>
                                    <button class="btn-primary text-xs px-3 py-2" :disabled="isAddingToCart[sample.id]"
                                        @click.stop="addToCartDirectly(sample)">
                                        <span x-show="!isAddingToCart[sample.id]">Add to Cart</span>
                                        <span x-show="isAddingToCart[sample.id]">Adding...</span>
                                    </button>
                                </div>
                            </div>
                        </article>
                    </template>
                </div>
            </div>
        @else
            <div class="rounded-xl bg-gradient-to-br from-slate-50 to-white p-6 ring-1 ring-black/5">
                <h3 class="mb-4 text-lg font-semibold text-[--color-brand-deepblue]">Ready to Order</h3>
                <div class="flex items-center gap-4">
                    <input type="number" min="1" value="1"
                        class="w-16 border border-slate-300 px-2 py-1 text-center text-sm" id="serviceQty">
                    <button class="btn-primary flex-1"
                        @click="addToCartDirectly({id: null, unit_price: {{ $service->price ?: 0 }}, service_id: {{ $service->id }} })">
                        Add to Cart - {{ $service->price_display ?: $service->formatted_price }}
                    </button>
                </div>
            </div>
        @endif

        <div x-show="showWeddingModal"
            class="fixed inset-0 z-[6000] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
                @click.away="showWeddingModal = false">
                <div
                    style="padding: 1.5rem !important; background-color: #001e3c !important; color: #ffffff !important; display: block !important; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                    <h3
                        style="font-size: 1.25rem !important; font-weight: 700 !important; color: #ffffff !important; margin: 0 !important; line-height: 1.75rem !important;">
                        Card Details
                    </h3>
                    <p
                        style="font-size: 0.875rem !important; color: rgba(255, 255, 255, 0.8) !important; margin-top: 0.25rem !important; margin-bottom: 0 !important;">
                        Provide details for the Wedding Card
                    </p>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Groom's Name</label>
                        <input type="text" x-model="weddingData.groom" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Bride's Name</label>
                        <input type="text" x-model="weddingData.bride" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Other Details(Address,Design,Timing etc)</label>
                        <textarea x-model="weddingData.remarks" rows="2" class="mt-1 w-full rounded-lg border-slate-300"></textarea>
                    </div>
                </div>
                <div class="p-6 bg-slate-50 flex gap-3">
                    <button @click="showWeddingModal = false" class="flex-1 px-4 py-2 border rounded-lg">Cancel</button>
                    <button @click="confirmWeddingAddToCart()" class="flex-1 btn-primary">Confirm & Add</button>
                </div>
            </div>
        </div>

        <div x-show="zoomedImage"
            class="fixed inset-0 z-[5000] flex items-center justify-center bg-black/90 p-4 backdrop-blur-sm" x-cloak
            @click="closeZoom()">
            <img :src="zoomedImage" class="max-h-[90vh] max-w-[90vw] object-contain rounded-lg shadow-2xl" @click.stop>
        </div>

    </div> {{-- End of x-data div --}}

@endsection

@push('scripts')
    <script>
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
                showWeddingModal: false,
                pendingSample: null,
                weddingData: {
                    groom: '',
                    bride: '',
                    remarks: ''
                },

                initSamples(samples, categories) {
                    this.allSamples = samples;
                    this.categories = categories;
                    this.filteredSamples = [...samples];
                    samples.forEach(s => this.isAddingToCart[s.id] = false);
                    this.updatePagination();
                },

                openZoom(imageUrl) {
                    this.zoomedImage = imageUrl;
                    document.body.style.overflow = 'hidden';
                },

                closeZoom() {
                    this.zoomedImage = null;
                    document.body.style.overflow = '';
                },

                filterByCategory(category) {
                    this.activeCategory = category;
                    this.currentPage = 1;
                    this.filteredSamples = category === 'all' ? [...this.allSamples] : this.allSamples.filter(s => s
                        .sub_category === category);
                    this.updatePagination();
                },

                updatePagination() {
                    this.paginatedSamples = this.filteredSamples.slice(0, this.currentPage * this.itemsPerPage);
                },

                // --- ADD TO CART LOGIC ---
                async addToCartDirectly(sample) {
                    const serviceTitle = '{{ $service->title }}';
                    if (serviceTitle.toLowerCase().includes('wedding')) {
                        this.pendingSample = sample;
                        this.showWeddingModal = true;
                        return;
                    }
                    await this.executeAddToCart(sample);
                },

                async confirmWeddingAddToCart() {
                    if (!this.weddingData.groom || !this.weddingData.bride) {
                        alert('Please enter Groom and Bride names');
                        return;
                    }
                    this.showWeddingModal = false;
                    await this.executeAddToCart(this.pendingSample, this.weddingData);
                    this.weddingData = {
                        groom: '',
                        bride: '',
                        remarks: ''
                    };
                },

                async executeAddToCart(sample, weddingDetails = null) {
                    // If it's a sample, use its ID. If direct order, use null.
                    const sampleId = sample.id || null;
                    if (sampleId) this.isAddingToCart[sampleId] = true;

                    try {
                        const qtyInput = document.querySelector(sampleId ? `.sample-qty[data-sample-id="${sampleId}"]` :
                            '#serviceQty');
                        const quantity = parseInt(qtyInput?.value || 1);

                        const item = {
                            service_id: sample.service_id || {{ $service->id }},
                            service_sample_id: sampleId,
                            quantity: quantity,
                            unit_price: sample.unit_price,
                            wedding_details: weddingDetails
                        };

                        const response = await fetch('{{ route('cart.add') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(item)
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.updateNavbarCount(data.item_count);
                            alert(data.message);
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        if (sampleId) this.isAddingToCart[sampleId] = false;
                    }
                },

                updateNavbarCount(count) {
                    const el = document.querySelector('.cart-count');
                    if (el) el.textContent = count;
                },

                openSampleModal(sample) {
                    if (typeof openProductModal === 'function') {
                        openProductModal({
                            id: sample.id,
                            title: sample.title,
                            image: sample.full_image_url,
                            unitPrice: sample.unit_price,
                            serviceId: sample.service_id
                        });
                    }
                }
            };
        }
    </script>
@endpush
