@extends('layouts.app')

@section('content')
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm text-slate-600" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('services') }}" class="hover:text-[--color-brand-blue]">Services</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('service.show', $service->slug) }}" class="hover:text-[--color-brand-blue]">{{ $service->title }}</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('service.category', [$service->slug, Str::slug($sample->sub_category)]) }}" class="hover:text-[--color-brand-blue]">{{ $sample->sub_category }}</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-[--color-brand-deepblue] font-medium">{{ $sample->title }}</li>
        </ol>
    </nav>

    <div class="grid gap-8 lg:grid-cols-2">
        <!-- Sample Image -->
        <div class="space-y-4" data-aos="fade-right">
            <div class="overflow-hidden rounded-2xl shadow-lg ring-1 ring-black/5">
                <img src="{{ $sample->image_path }}" alt="{{ $sample->title }}" class="h-96 w-full object-cover" />
            </div>
            <div class="flex items-center justify-between">
                <div class="text-2xl font-bold text-[--color-brand-deepblue]">{{ $sample->formatted_price }}</div>
                <div class="text-sm text-slate-500">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ ucfirst($sample->sample_type ?: 'standard') }}
                    </span>
                    @if($sample->sub_category)
                        <span class="ml-2">{{ $sample->sub_category }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sample Details -->
        <div class="space-y-6" data-aos="fade-left">
            <div>
                <h1 class="text-3xl font-extrabold text-[--color-brand-deepblue]">{{ $sample->title }}</h1>
                <div class="mt-2 text-lg text-[--color-brand-blue]">{{ $service->title }} - {{ $sample->sub_category }}</div>
                @if($sample->description)
                    <p class="mt-4 text-lg text-slate-600">{{ $sample->description }}</p>
                @endif
            </div>

            <!-- Features -->
            <div class="space-y-3">
                <h3 class="text-lg font-semibold text-[--color-brand-deepblue]">Features</h3>
                <ul class="space-y-2 text-slate-600">
                    <li class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-[--color-brand-orange]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        High-quality printing with vibrant colors
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-[--color-brand-orange]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Premium paper and material options
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-[--color-brand-orange]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Fast turnaround time
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-[--color-brand-orange]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Professional design consultation included
                    </li>
                </ul>
            </div>

            <!-- Specifications -->
            @if($sample->sub_category)
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-[--color-brand-deepblue]">Specifications</h3>
                    <div class="grid gap-3 text-sm">
                        <div class="flex justify-between py-2 border-b border-slate-200">
                            <span class="text-slate-600">Category:</span>
                            <span class="font-medium">{{ $sample->sub_category }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-200">
                            <span class="text-slate-600">Type:</span>
                            <span class="font-medium">{{ $service->title }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-200">
                            <span class="text-slate-600">Sample Type:</span>
                            <span class="font-medium">{{ ucfirst($sample->sample_type ?: 'standard') }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-200">
                            <span class="text-slate-600">Price:</span>
                            <span class="font-medium">{{ $sample->formatted_price }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Add to Cart -->
            <div class="rounded-xl bg-slate-50 p-6 ring-1 ring-black/5">
                <h3 class="mb-4 text-lg font-semibold text-[--color-brand-deepblue]">Order This Design</h3>
                <div class="flex items-center gap-4">
                    <label for="sampleQty" class="text-sm font-medium text-slate-700">Quantity:</label>
                    <input type="number" min="1" value="1" class="w-20 rounded border border-slate-300 px-3 py-2 text-center" id="sampleQty">
                    <button class="btn-primary flex-1" id="addSampleToCart" data-service-id="{{ $service->id }}" data-sample-id="{{ $sample->id }}" data-unit-price="{{ $sample->unit_price }}">
                        Add to Cart - {{ $sample->formatted_price }}
                    </button>
                </div>
                <p class="mt-3 text-sm text-slate-600">
                    Need customization? <a href="{{ route('contact') }}" class="text-[--color-brand-blue] hover:text-[--color-brand-orange]">Contact us</a> for a custom quote.
                </p>
            </div>
        </div>
    </div>

    <!-- Back Navigation -->
    <div class="mt-12 flex items-center justify-between" data-aos="fade-up">
        <a href="{{ route('service.category', [$service->slug, Str::slug($sample->sub_category)]) }}" class="inline-flex items-center gap-2 text-[--color-brand-blue] hover:text-[--color-brand-orange] transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to {{ $sample->sub_category }} {{ $service->title }}
        </a>
        <a href="{{ route('cart.view') }}" class="btn-primary">
            View Cart
        </a>
    </div>

    <!-- Related Samples -->
    @php
        $relatedSamples = $service->samples()
            ->where('is_active', true)
            ->where('sub_category', $sample->sub_category)
            ->where('id', '!=', $sample->id)
            ->orderBy('sort_order')
            ->take(3)
            ->get();
    @endphp
    @if($relatedSamples->count())
        <section class="mt-16" data-aos="fade-up">
            <h2 class="section-title">More {{ $sample->sub_category }} Designs</h2>
            <p class="section-subtitle">Explore other designs in this category.</p>
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($relatedSamples as $relatedSample)
                    <div class="card group">
                        <img src="{{ $relatedSample->image_path }}" alt="{{ $relatedSample->title }}" class="h-32 w-full rounded-lg object-cover" />
                        <div class="mt-3">
                            <h4 class="font-semibold text-[--color-brand-deepblue]">{{ $relatedSample->title }}</h4>
                            <p class="text-sm text-slate-600">{{ $relatedSample->formatted_price }}</p>
                            <a href="{{ route('service.sample', [$service->slug, $relatedSample->slug]) }}" class="mt-2 inline-block text-sm font-medium text-[--color-brand-blue] hover:text-[--color-brand-orange]">View Details →</a>
                        </div>
                    </div>
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

    document.getElementById('addSampleToCart').addEventListener('click', async function(e){
        e.preventDefault();
        const btn = e.target;
        const sampleId = btn.dataset.sampleId;
        const serviceId = btn.dataset.serviceId;
        const unitPrice = parseFloat(btn.dataset.unitPrice);
        const quantity = parseInt(document.getElementById('sampleQty').value || 1);

        // Local storage add
        const cart = getLocalCart();
        const existing = cart.find(i => i.service_id==serviceId && i.service_sample_id==sampleId && i.unit_price==unitPrice);
        if(existing){ existing.quantity += quantity; } else { cart.push({ service_id: parseInt(serviceId), service_sample_id: parseInt(sampleId), quantity, unit_price: unitPrice }); }
        setLocalCart(cart);
        updateNavbarCount();

        // Visual feedback
        const originalText = btn.textContent;
        btn.textContent = 'Added to Cart!';
        btn.disabled = true;
        btn.classList.add('bg-green-600');
        setTimeout(() => {
            btn.textContent = originalText;
            btn.disabled = false;
            btn.classList.remove('bg-green-600');
        }, 2000);

        // Attempt server sync (non-blocking UX)
        syncToServer({ service_id: serviceId, service_sample_id: sampleId, quantity, unit_price: unitPrice });
    });

    updateNavbarCount();
});
</script>
@endpush
