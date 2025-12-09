@extends('layouts.app')

@section('content')
    <!-- Include Product Modal -->
    @include('components.product-modal')

    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm text-slate-600" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('home') }}" class="hover:text-[--color-brand-blue] transition-colors">Home</a></li>
            <li><span class="mx-2 text-slate-400">›</span></li>
            <li><a href="{{ route('services') }}" class="hover:text-[--color-brand-blue] transition-colors">Services</a></li>
            <li><span class="mx-2 text-slate-400">›</span></li>
            <li><a href="{{ route('service.show', $service->slug) }}" class="hover:text-[--color-brand-blue] transition-colors">{{ $service->title }}</a></li>
            <li><span class="mx-2 text-slate-400">›</span></li>
            <li class="text-[--color-brand-deepblue] font-medium">{{ $category }}</li>
        </ol>
    </nav>

    <!-- Minimal filter toolbar + grid -->
    <div x-data="categorySamples()" x-init="initSamples({{ $samples->toJson() }})">
        <!-- Filter Bar -->
        <div class="mb-4 rounded-lg bg-white p-4 shadow-sm ring-1 ring-black/5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <!-- Search -->
                    <div class="relative w-64 max-w-full">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" x-model="query" @input="applyFilters()" placeholder="Search designs..." 
                               class="w-full rounded-lg border border-slate-300 pl-10 pr-3 py-2 text-sm focus:border-[--color-brand-blue] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue]/20">
                    </div>

                    <!-- Sample Type (category-specific filter using available data) -->
                    <div>
                        <label class="sr-only" for="typeFilter">Type</label>
                        <select id="typeFilter" x-model="selectedType" @change="applyFilters()" 
                                class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[--color-brand-blue] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue]/20">
                            <option value="">All Types</option>
                            <template x-for="t in sampleTypes" :key="t">
                                <option :value="t" x-text="t"></option>
                            </template>
                        </select>
    </div>

                    <!-- Price Range -->
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-slate-600">PKR</span>
                        <input type="number" x-model.number="minPrice" @input="applyFilters()" min="0" placeholder="Min" 
                               class="w-20 rounded border border-slate-300 px-2 py-1 focus:border-[--color-brand-blue] focus:outline-none focus:ring-1 focus:ring-[--color-brand-blue]/20">
                        <span class="text-slate-400">–</span>
                        <input type="number" x-model.number="maxPrice" @input="applyFilters()" min="0" placeholder="Max" 
                               class="w-20 rounded border border-slate-300 px-2 py-1 focus:border-[--color-brand-blue] focus:outline-none focus:ring-1 focus:ring-[--color-brand-blue]/20">
                    </div>
                </div>

                <!-- Sort + Count + Clear -->
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-slate-600"><span x-text="filtered.length"></span> of <span x-text="all.length"></span> designs</span>
                    <select x-model="sortBy" @change="applyFilters()" 
                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[--color-brand-blue] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue]/20">
                        <option value="newest">Newest</option>
                        <option value="price-low">Price ↑</option>
                        <option value="price-high">Price ↓</option>
                        <option value="name">A-Z</option>
                    </select>
                    <button class="text-[--color-brand-blue] hover:text-[--color-brand-orange] font-medium" @click="resetFilters()">Clear</button>
                </div>
            </div>
        </div>

        <!-- Samples Grid -->
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" data-aos="fade-up" data-aos-delay="100">
            <template x-for="(sample, idx) in paginated" :key="sample.id">
                <article class="group relative overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-black/5 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl focus-within:ring-2 focus-within:ring-[--color-brand-blue]">
                    <div class="relative aspect-square w-full overflow-hidden cursor-pointer" 
                         @click="openSample(sample)" role="button" tabindex="0"
                         @keydown="if($event.key==='Enter'||$event.key===' '){$event.preventDefault(); openSample(sample);}">
                        <img :src="getImageUrl(sample.image_path)" :alt="sample.title" 
                             class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" 
                             loading="lazy" 
                             :onerror="`this.style.display='none'; this.nextElementSibling.style.display='flex';`">
                        <div class="hidden h-full w-full items-center justify-center bg-slate-100 text-xs text-slate-600">Image missing</div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                            <div class="absolute bottom-4 left-4 right-4">
                                <div class="flex items-center justify-center rounded-lg bg-white/90 py-2 text-sm font-medium text-[--color-brand-deepblue] backdrop-blur-sm">Click to Customize</div>
                            </div>
                        </div>
                        <div class="absolute top-3 left-3">
                            <template x-if="isNew(sample)"><span class="inline-flex rounded-full bg-green-500 px-2 py-1 text-xs font-medium text-white shadow-lg">New</span></template>
                        </div>
                        <div class="absolute bottom-3 right-3 rounded-lg bg-white/95 px-3 py-1.5 text-sm font-bold text-[--color-brand-deepblue] shadow-lg backdrop-blur-sm" x-text="sample.formatted_price"></div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-base font-bold text-[--color-brand-deepblue] group-hover:text-[--color-brand-blue] transition-colors" x-text="sample.title"></h3>
                        <template x-if="sample.description">
                            <p class="mt-2 text-sm text-slate-600 line-clamp-2" x-text="sample.description.length > 80 ? sample.description.substring(0,80)+'…' : sample.description"></p>
                        </template>
                </div>
                </article>
            </template>
    </div>

        <!-- Load More -->
        <div class="mt-6 text-center" x-show="hasMore">
            <button class="btn-primary" @click="loadMore()" :disabled="loading">
                <span x-show="!loading">Load More</span>
                <span x-show="loading" class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
            </svg>
                    Loading...
                </span>
            </button>
        </div>
    </div>

    <!-- End minimal Category Samples page -->
@endsection

@push('scripts')
<script>
// Alpine component for category-specific filtering
function categorySamples(){
    return {
        all: [],
        filtered: [],
        paginated: [],
        page: 1,
        pageSize: 12,
        loading: false,
        query: '',
        sampleTypes: [],
        selectedType: '',
        minPrice: '',
        maxPrice: '',
        sortBy: 'newest',

        get hasMore(){ return this.paginated.length < this.filtered.length; },

        initSamples(items){
            this.all = Array.isArray(items) ? items : [];
            // Collect distinct sample types for this category
            const types = new Set();
            this.all.forEach(s => { if (s.sample_type) types.add(s.sample_type); });
            this.sampleTypes = Array.from(types);
            // Init price bounds
            const prices = this.all.map(s => Number(s.unit_price||0)).filter(n=>!isNaN(n));
            const min = prices.length ? Math.min(...prices) : 0;
            const max = prices.length ? Math.max(...prices) : 0;
            this.minPrice = '';
            this.maxPrice = max || '';
            this.applyFilters();
        },

        resetFilters(){
            this.query=''; this.selectedType=''; this.minPrice=''; this.maxPrice=''; this.sortBy='newest'; this.page=1; this.applyFilters();
        },

        applyFilters(){
            let arr = [...this.all];
            const q = this.query.trim().toLowerCase();
            if (q) arr = arr.filter(s => (s.title||'').toLowerCase().includes(q) || (s.description||'').toLowerCase().includes(q));
            if (this.selectedType) arr = arr.filter(s => (s.sample_type||'') === this.selectedType);
            const min = this.minPrice === '' ? -Infinity : Number(this.minPrice);
            const max = this.maxPrice === '' ? Infinity : Number(this.maxPrice);
            arr = arr.filter(s => {
                const p = Number(s.unit_price||0);
                return p >= min && p <= max;
            });
            arr = this.sort(arr);
            this.filtered = arr;
            this.page = 1;
            this.updatePagination();
        },

        sort(arr){
            switch(this.sortBy){
                case 'newest': return arr.sort((a,b)=> new Date(b.created_at) - new Date(a.created_at));
                case 'price-low': return arr.sort((a,b)=> (a.unit_price||0)-(b.unit_price||0));
                case 'price-high': return arr.sort((a,b)=> (b.unit_price||0)-(a.unit_price||0));
                case 'name': return arr.sort((a,b)=> (a.title||'').localeCompare(b.title||''));
                default: return arr;
            }
        },

        updatePagination(){
            const end = this.page * this.pageSize;
            this.paginated = this.filtered.slice(0, end);
        },

        loadMore(){
            if (!this.hasMore || this.loading) return;
            this.loading = true;
            setTimeout(()=>{ this.page++; this.updatePagination(); this.loading = false; }, 250);
        },

        getImageUrl(path){
            if(!path) return '';
            if(path.startsWith('http')) return path;
            if(path.startsWith('/storage/')) return path;
            if(path.startsWith('storage/')) return '/'+path;
            if(path.startsWith('/')) return path;
            return '/storage/'+path;
        },

        isNew(s){
            if(!s.created_at) return false;
            const d = new Date(s.created_at); const t = new Date(); t.setDate(t.getDate()-30); return d > t;
        },

        openSample(sample){
            const productData = {
                id: sample.id,
                title: sample.title,
                description: sample.description,
                image: this.getImageUrl(sample.image_path),
                formattedPrice: sample.formatted_price,
                unitPrice: sample.unit_price,
                serviceId: sample.service_id || {{ $service->id }},
                sampleId: sample.id,
                serviceName: '{{ $service->title }} - {{ $category }}',
                badges: this.isNew(sample) ? [{ type:'new', text:'New' }] : [],
                options: {
                    personalization: true,
                    fileUpload: true,
                    sizes: [
                        { value:'small', label:'Small (4x6)', price:'' },
                        { value:'medium', label:'Medium (5x7)', price:'+PKR 50' },
                        { value:'large', label:'Large (8x10)', price:'+PKR 150' }
                    ],
                    colors: [ { value:'standard', label:'Standard' }, { value:'premium', label:'Premium Finish' } ],
                    extras: [ { value:'rush', label:'Rush Delivery (24hrs)', price:'200' }, { value:'lamination', label:'Lamination', price:'100' } ]
                }
            };
            if (typeof openProductModal === 'function') openProductModal(productData);
        }
    }
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

document.addEventListener('DOMContentLoaded', function(){
    function getLocalCart(){
        try{ return JSON.parse(localStorage.getItem('cart') || '[]'); }catch(e){ return []; }
    }
    function setLocalCart(items){ localStorage.setItem('cart', JSON.stringify(items)); }
    function updateNavbarCount(){
        const items = getLocalCart();
        const count = items.reduce((a,b)=> a + (parseInt(b.quantity)||0), 0);
        const el = document.querySelector('.cart-count'); if(el) el.textContent = count;
    }
    async function syncToServer(item){
        try{
            const res = await fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify(item)
            });
            return await res.json();
        }catch(e){ return { success:false }; }
    }

    // Legacy cart functionality for backwards compatibility
    document.addEventListener('click', async function(e){
        if(e.target.classList.contains('add-sample-to-cart')){
            e.preventDefault();
            const btn = e.target;
            const sampleId = btn.dataset.sampleId;
            const serviceId = btn.dataset.serviceId;
            const unitPrice = parseFloat(btn.dataset.unitPrice);
            const qtyInput = document.querySelector(`.sample-qty[data-sample-id="${sampleId}"]`);
            const quantity = parseInt(qtyInput?.value || 1);

            // Local storage add
            const cart = getLocalCart();
            const existing = cart.find(i => i.service_id==serviceId && i.service_sample_id==sampleId && i.unit_price==unitPrice);
            if(existing){ existing.quantity += quantity; } else { cart.push({ service_id: parseInt(serviceId), service_sample_id: parseInt(sampleId), quantity, unit_price: unitPrice }); }
            setLocalCart(cart);
            updateNavbarCount();

            // Visual feedback
            btn.textContent = 'Added!';
            btn.disabled = true;
            setTimeout(() => {
                btn.textContent = 'Add to Cart';
                btn.disabled = false;
            }, 1500);

            // Attempt server sync (non-blocking UX)
            syncToServer({ service_id: serviceId, service_sample_id: sampleId, quantity, unit_price: unitPrice });
        }
    });

    updateNavbarCount();
});
</script>
@endpush
