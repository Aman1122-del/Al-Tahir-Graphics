@extends('layouts.app')

@section('content')
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm text-slate-600" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('services') }}" class="hover:text-[--color-brand-blue]">Services</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('service.show', $service->slug) }}" class="hover:text-[--color-brand-blue]">{{ $service->title }}</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-[--color-brand-deepblue] font-medium">{{ $category }}</li>
        </ol>
    </nav>

    <!-- Category Header -->
    <div class="mb-8" data-aos="fade-up">
        <h1 class="text-3xl font-extrabold text-[--color-brand-deepblue]">{{ $service->title }} - {{ $category }}</h1>
        <p class="mt-2 text-lg text-slate-600">Choose from our {{ $category }} {{ strtolower($service->title) }} collection</p>
    </div>

    <!-- Samples Grid -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" data-aos="fade-up" data-aos-delay="100">
        @foreach($samples as $idx => $sample)
            <div class="group overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-black/5 transition-all hover:-translate-y-1 hover:shadow-xl" data-aos="fade-up" data-aos-delay="{{ 50 * $idx }}">
                <div class="relative h-48 w-full overflow-hidden">
                    <img src="{{ $sample->image_path }}" alt="{{ $sample->title }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                    <div class="absolute bottom-3 left-3 rounded bg-white/90 px-2 py-1 text-xs font-semibold text-[--color-brand-deepblue] shadow">
                        {{ $sample->formatted_price }}
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-[--color-brand-deepblue] group-hover:text-[--color-brand-blue]">{{ $sample->title }}</h3>
                    @if($sample->description)
                        <p class="mt-2 text-sm text-slate-600 line-clamp-2">{{ $sample->description }}</p>
                    @endif
                    <div class="mt-4 flex items-center justify-between">
                        <a href="{{ route('service.sample', [$service->slug, $sample->slug]) }}" class="text-sm font-medium text-[--color-brand-blue] hover:text-[--color-brand-orange] transition-colors">
                            View Details →
                        </a>
                        <div class="flex items-center gap-2">
                            <input type="number" min="1" value="1" class="w-12 rounded border border-slate-300 px-1 py-1 text-center text-xs sample-qty" data-sample-id="{{ $sample->id }}">
                            <button class="btn-primary text-xs px-2 py-1 add-sample-to-cart" data-service-id="{{ $service->id }}" data-sample-id="{{ $sample->id }}" data-unit-price="{{ $sample->unit_price }}">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Back to Service -->
    <div class="mt-12 text-center" data-aos="fade-up">
        <a href="{{ route('service.show', $service->slug) }}" class="inline-flex items-center gap-2 text-[--color-brand-blue] hover:text-[--color-brand-orange] transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to {{ $service->title }}
        </a>
    </div>

    <!-- Related Categories -->
    @if($service->sub_categories->count() > 1)
        <section class="mt-16" data-aos="fade-up">
            <h2 class="section-title">Other {{ $service->title }} Categories</h2>
            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($service->sub_categories as $relatedCategory)
                    @if($relatedCategory !== $category)
                        <a href="{{ route('service.category', [$service->slug, Str::slug($relatedCategory)]) }}" class="group block rounded-lg bg-white p-4 ring-1 ring-black/5 transition-all hover:ring-2 hover:ring-[--color-brand-blue] shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-[--color-brand-deepblue] group-hover:text-[--color-brand-blue]">{{ $relatedCategory }}</div>
                                    <div class="text-sm text-slate-600">
                                        @php
                                            $categoryCount = $service->samples()->where('is_active', true)->where('sub_category', $relatedCategory)->count();
                                        @endphp
                                        {{ $categoryCount }} {{ Str::plural('design', $categoryCount) }}
                                    </div>
                                </div>
                                <svg class="h-4 w-4 text-[--color-brand-blue] transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        </section>
    @endif
@endsection

@push('scripts')
<script>
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
