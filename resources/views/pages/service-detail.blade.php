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

        <div style="z-index: 100;" x-show="showWeddingModal"
            class="fixed inset-0  flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto"
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
                        <label class="block text-sm font-semibold text-slate-700">Groom Name</label>
                        <input type="text" x-model="weddingData.groom" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Bride Name</label>
                        <input type="text" x-model="weddingData.bride" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Event Type</label>
                        <select x-model="weddingData.eventType" class="mt-1 w-full rounded-lg border-slate-300">
                            <option value="">Select Event Type</option>
                            <option value="Nikah">Nikah</option>
                            <option value="Barat">Barat</option>
                            <option value="Walima">Walima</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Date & Time</label>
                        <input type="datetime-local" x-model="weddingData.dateTime" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Venue / Address</label>
                        <input type="text" x-model="weddingData.venue" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Additional Message (Optional)</label>
                        <textarea x-model="weddingData.additionalMessage" rows="2" class="mt-1 w-full rounded-lg border-slate-300"></textarea>
                    </div>
                </div>
                <div class="p-6 bg-slate-50 flex gap-3">
                    <button @click="showWeddingModal = false" class="flex-1 px-4 py-2 border rounded-lg">Cancel</button>
                    <button @click="confirmWeddingAddToCart()" class="flex-1 btn-primary">Confirm & Add</button>
                </div>
            </div>
        </div>

        <div x-show="showVisitingCardModal"
            class="fixed inset-0 z-[6000] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto"
                @click.away="showVisitingCardModal = false">
                <div
                    style="padding: 1.5rem !important; background-color: #001e3c !important; color: #ffffff !important; display: block !important; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                    <h3
                        style="font-size: 1.25rem !important; font-weight: 700 !important; color: #ffffff !important; margin: 0 !important; line-height: 1.75rem !important;">
                        Visiting Card Details
                    </h3>
                    <p
                        style="font-size: 0.875rem !important; color: rgba(255, 255, 255, 0.8) !important; margin-top: 0.25rem !important; margin-bottom: 0 !important;">
                        Provide details for the Visiting Card
                    </p>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Business / Person Name</label>
                        <input type="text" x-model="visitingCardData.businessName" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Designation (Optional)</label>
                        <input type="text" x-model="visitingCardData.designation" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Company Name (Optional)</label>
                        <input type="text" x-model="visitingCardData.companyName" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Mobile Number</label>
                        <input type="tel" x-model="visitingCardData.mobileNumber" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">WhatsApp Number (Optional)</label>
                        <input type="tel" x-model="visitingCardData.whatsappNumber" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Email Address (Optional)</label>
                        <input type="email" x-model="visitingCardData.emailAddress" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Office Address</label>
                        <textarea x-model="visitingCardData.officeAddress" rows="2" class="mt-1 w-full rounded-lg border-slate-300"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Printing Side</label>
                        <select x-model="visitingCardData.printingSide" class="mt-1 w-full rounded-lg border-slate-300">
                            <option value="">Select Printing Side</option>
                            <option value="Single">Single</option>
                            <option value="Double">Double</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Additional Message (Optional)</label>
                        <textarea x-model="visitingCardData.additionalMessage" rows="2" class="mt-1 w-full rounded-lg border-slate-300"></textarea>
                    </div>
                </div>
                <div class="p-6 bg-slate-50 flex gap-3">
                    <button @click="showVisitingCardModal = false" class="flex-1 px-4 py-2 border rounded-lg">Cancel</button>
                    <button @click="confirmVisitingCardAddToCart()" class="flex-1 btn-primary">Confirm & Add</button>
                </div>
            </div>
        </div>

        <!-- Panaflex Modal -->
        <div x-show="showPanaflexModal"
            class="fixed inset-0 z-[6000] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto"
                @click.away="showPanaflexModal = false">
                <div
                    style="padding: 1.5rem !important; background-color: #7c3aed !important; color: #ffffff !important; display: block !important; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                    <h3 class="text-lg font-bold">Panaflex Details</h3>
                    <p class="text-sm opacity-90 mt-1">Provide details for the Panaflex Printing</p>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Business/Event Name</label>
                        <input type="text" x-model="panaflexData.businessName" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Panaflex Size</label>
                        <select x-model="panaflexData.panaflexSize" class="mt-1 w-full rounded-lg border-slate-300">
                            <option value="">Select Size</option>
                            <option value="2x4 ft">2x4 ft</option>
                            <option value="3x6 ft">3x6 ft</option>
                            <option value="4x8 ft">4x8 ft</option>
                            <option value="5x10 ft">5x10 ft</option>
                            <option value="6x12 ft">6x12 ft</option>
                            <option value="Custom">Custom Size</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Event Type/Purpose (Optional)</label>
                        <select x-model="panaflexData.eventType" class="mt-1 w-full rounded-lg border-slate-300">
                            <option value="">Select Event Type</option>
                            <option value="Wedding">Wedding</option>
                            <option value="Birthday">Birthday</option>
                            <option value="Corporate Event">Corporate Event</option>
                            <option value="Political Campaign">Political Campaign</option>
                            <option value="Product Launch">Product Launch</option>
                            <option value="Store Opening">Store Opening</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Main Heading Text</label>
                        <input type="text" x-model="panaflexData.mainHeading" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Sub Heading Text (Optional)</label>
                        <input type="text" x-model="panaflexData.subHeading" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Date & Time (Optional)</label>
                        <input type="datetime-local" x-model="panaflexData.dateTime" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Venue/Location</label>
                        <input type="text" x-model="panaflexData.venue" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Contact Number</label>
                        <input type="tel" x-model="panaflexData.contactNumber" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Design Upload (Optional)</label>
                        <input type="file" x-ref="panaflexDesignFile" accept="image/*,.pdf,.doc,.docx" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Additional Instructions (Optional)</label>
                        <textarea x-model="panaflexData.additionalInstructions" rows="3" class="mt-1 w-full rounded-lg border-slate-300"></textarea>
                    </div>
                </div>
                <div class="p-6 bg-slate-50 flex gap-3">
                    <button @click="showPanaflexModal = false" class="flex-1 px-4 py-2 border rounded-lg">Cancel</button>
                    <button @click="confirmPanaflexAddToCart()" class="flex-1 btn-primary">Confirm & Add</button>
                </div>
            </div>
        </div>

        <!-- Flyer/Brochure Modal -->
        <div x-show="showFlyerBrochureModal"
            class="fixed inset-0 z-[6000] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto"
                @click.away="showFlyerBrochureModal = false">
                <div
                    style="padding: 1.5rem !important; background-color: #059669 !important; color: #ffffff !important; display: block !important; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                    <h3 class="text-lg font-bold">Flyer/Brochure Details</h3>
                    <p class="text-sm opacity-90 mt-1">Provide details for the Flyer/Brochure Printing</p>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Business / Brand Name</label>
                        <input type="text" x-model="flyerBrochureData.businessName" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Brochure Type</label>
                        <select x-model="flyerBrochureData.brochureType" class="mt-1 w-full rounded-lg border-slate-300">
                            <option value="">Select Type</option>
                            <option value="Brochure">Brochure</option>
                            <option value="Voucher">Voucher</option>
                            <option value="Flyer">Flyer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Offer Details / Content</label>
                        <textarea x-model="flyerBrochureData.offerDetails" rows="3" class="mt-1 w-full rounded-lg border-slate-300"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Validity Date (Optional)</label>
                        <input type="date" x-model="flyerBrochureData.validityDate" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Contact Information</label>
                        <input type="tel" x-model="flyerBrochureData.contactInfo" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Address</label>
                        <textarea x-model="flyerBrochureData.address" rows="2" class="mt-1 w-full rounded-lg border-slate-300"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Paper Type</label>
                        <select x-model="flyerBrochureData.paperType" class="mt-1 w-full rounded-lg border-slate-300">
                            <option value="">Select Paper Type</option>
                            <option value="Matt">Matt</option>
                            <option value="Glossy">Glossy</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Fold Type</label>
                        <select x-model="flyerBrochureData.foldType" class="mt-1 w-full rounded-lg border-slate-300">
                            <option value="">Select Fold Type</option>
                            <option value="Single">Single</option>
                            <option value="Bi-fold">Bi-fold</option>
                            <option value="Tri-fold">Tri-fold</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Quantity</label>
                        <input type="number" x-model="flyerBrochureData.quantity" min="1" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Additional Message (Optional)</label>
                        <textarea x-model="flyerBrochureData.additionalMessage" rows="2" class="mt-1 w-full rounded-lg border-slate-300"></textarea>
                    </div>
                </div>
                <div class="p-6 bg-slate-50 flex gap-3">
                    <button @click="showFlyerBrochureModal = false" class="flex-1 px-4 py-2 border rounded-lg">Cancel</button>
                    <button @click="confirmFlyerBrochureAddToCart()" class="flex-1 btn-primary">Confirm & Add</button>
                </div>
            </div>
        </div>

        <div style="z-index:100" x-show="zoomedImage"
            class="fixed inset-0  flex items-center justify-center bg-black/90 p-4 backdrop-blur-sm" x-cloak
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
                showVisitingCardModal: false,
                pendingSample: null,
                weddingData: {
                    groom: '',
                    bride: '',
                    eventType: '',
                    dateTime: '',
                    venue: '',
                    additionalMessage: ''
                },
                visitingCardData: {
                    businessName: '',
                    designation: '',
                    companyName: '',
                    mobileNumber: '',
                    whatsappNumber: '',
                    emailAddress: '',
                    officeAddress: '',
                    printingSide: '',
                    additionalMessage: ''
                },
                showPanaflexModal: false,
                panaflexData: {
                    businessName: '',
                    panaflexSize: '',
                    eventType: '',
                    mainHeading: '',
                    subHeading: '',
                    dateTime: '',
                    venue: '',
                    contactNumber: '',
                    additionalInstructions: ''
                },
                showFlyerBrochureModal: false,
                flyerBrochureData: {
                    businessName: '',
                    brochureType: '',
                    offerDetails: '',
                    validityDate: '',
                    contactInfo: '',
                    address: '',
                    paperType: '',
                    foldType: '',
                    quantity: 1,
                    additionalMessage: ''
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
                    console.log('Service title:', serviceTitle);
                    console.log('Adding to cart for service:', serviceTitle);
                    if (serviceTitle.toLowerCase().includes('wedding')) {
                        this.pendingSample = sample;
                        this.showWeddingModal = true;
                        return;
                    }
                    if (serviceTitle.toLowerCase().includes('visiting') ||
                        (serviceTitle.toLowerCase().includes('card') && !serviceTitle.toLowerCase().includes('wedding'))) {
                        this.pendingSample = sample;
                        this.showVisitingCardModal = true;
                        return;
                    }
                    if (serviceTitle.toLowerCase().includes('panaflex')) {
                        this.pendingSample = sample;
                        this.showPanaflexModal = true;
                        return;
                    }
                    if (serviceTitle.toLowerCase().includes('flyer') || serviceTitle.toLowerCase().includes('brochure')) {
                        console.log('Triggering flyer/brochure modal for:', serviceTitle);
                        this.pendingSample = sample;
                        this.showFlyerBrochureModal = true;
                        return;
                    }
                    await this.executeAddToCart(sample);
                },

                async confirmWeddingAddToCart() {
                    if (!this.weddingData.groom || !this.weddingData.bride || !this.weddingData.eventType || !this.weddingData.dateTime || !this.weddingData.venue) {
                        alert('Please fill in all required fields: Groom Name, Bride Name, Event Type, Date & Time, and Venue / Address');
                        return;
                    }
                    this.showWeddingModal = false;
                    await this.executeAddToCart(this.pendingSample, this.weddingData);
                    this.weddingData = {
                        groom: '',
                        bride: '',
                        eventType: '',
                        dateTime: '',
                        venue: '',
                        additionalMessage: ''
                    };
                },

                async confirmVisitingCardAddToCart() {
                    if (!this.visitingCardData.businessName || !this.visitingCardData.mobileNumber || !this.visitingCardData.officeAddress || !this.visitingCardData.printingSide) {
                        alert('Please fill in all required fields: Business/Person Name, Mobile Number, Office Address, and Printing Side');
                        return;
                    }

                    this.showVisitingCardModal = false;
                    await this.executeAddToCart(this.pendingSample, null, this.visitingCardData);

                    // Reset form
                    this.visitingCardData = {
                        businessName: '',
                        designation: '',
                        companyName: '',
                        mobileNumber: '',
                        whatsappNumber: '',
                        emailAddress: '',
                        officeAddress: '',
                        printingSide: '',
                        additionalMessage: ''
                    };
                },

                async confirmPanaflexAddToCart() {
                    if (!this.panaflexData.businessName || !this.panaflexData.panaflexSize || !this.panaflexData.mainHeading || !this.panaflexData.venue || !this.panaflexData.contactNumber) {
                        alert('Please fill in all required fields: Business/Event Name, Panaflex Size, Main Heading, Venue/Location, and Contact Number');
                        return;
                    }
                    this.showPanaflexModal = false;
                    await this.executeAddToCart(this.pendingSample, null, null, this.panaflexData);
                    this.panaflexData = {
                        businessName: '',
                        panaflexSize: '',
                        eventType: '',
                        mainHeading: '',
                        subHeading: '',
                        dateTime: '',
                        venue: '',
                        contactNumber: '',
                        additionalInstructions: ''
                    };
                },

                async confirmFlyerBrochureAddToCart() {
                    console.log('Confirm flyer brochure add to cart');
                    console.log('Form data:', this.flyerBrochureData);
                    if (!this.flyerBrochureData.businessName || !this.flyerBrochureData.brochureType || !this.flyerBrochureData.offerDetails || !this.flyerBrochureData.contactInfo || !this.flyerBrochureData.address || !this.flyerBrochureData.paperType || !this.flyerBrochureData.foldType || !this.flyerBrochureData.quantity) {
                        alert('Please fill in all required fields: Business Name, Brochure Type, Offer Details, Contact Information, Address, Paper Type, Fold Type, and Quantity');
                        return;
                    }
                    console.log('Validation passed, adding to cart');
                    this.showFlyerBrochureModal = false;
                    await this.executeAddToCart(this.pendingSample, null, null, null, this.flyerBrochureData);
                    this.flyerBrochureData = {
                        businessName: '',
                        brochureType: '',
                        offerDetails: '',
                        validityDate: '',
                        contactInfo: '',
                        address: '',
                        paperType: '',
                        foldType: '',
                        quantity: 1,
                        additionalMessage: ''
                    };
                },

                async executeAddToCart(sample, weddingDetails = null, visitingCardDetails = null, panaflexDetails = null, flyerBrochureDetails = null) {
                    console.log('Execute add to cart called with:', {
                        sample: sample,
                        weddingDetails: weddingDetails,
                        visitingCardDetails: visitingCardDetails,
                        panaflexDetails: panaflexDetails,
                        flyerBrochureDetails: flyerBrochureDetails
                    });
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
                            wedding_details: weddingDetails,
                            visiting_card_details: visitingCardDetails,
                            panaflex_details: panaflexDetails,
                            flyer_brochure_details: flyerBrochureDetails
                        };

                        console.log('Sending item to cart:', item);

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
                        console.log('Cart response:', data);
                        if (response.ok && data.success) {
                            this.updateNavbarCount(data.item_count);
                            alert(data.message);
                        } else {
                            // Handle errors
                            if (data.errors) {
                                const errorMessages = Object.values(data.errors).flat().join('\n');
                                alert('Validation errors:\n' + errorMessages);
                            } else if (data.message) {
                                alert('Error: ' + data.message);
                            } else {
                                alert('An error occurred while adding to cart. Please try again.');
                            }
                        }
                    } catch (e) {
                        console.error('Error adding to cart:', e);
                        alert('An error occurred while adding to cart. Please check the console for details.');
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
