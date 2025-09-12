@extends('layouts.app')

@section('content')
    <div class="grid gap-8 lg:grid-cols-2">
        <!-- Service Image -->
        <div class="space-y-4" data-aos="fade-right">
            <div class="overflow-hidden rounded-2xl shadow-lg ring-1 ring-black/5">
                <img src="{{ $service->image_path }}" alt="{{ $service->title }}" class="h-96 w-full object-cover" />
            </div>
            <div class="flex items-center justify-between">
                <div class="text-2xl font-bold text-[--color-brand-deepblue]">{{ $service->price }}</div>
                <div class="text-sm text-slate-500">{{ $service->category }}</div>
            </div>
        </div>

        <!-- Service Details -->
        <div class="space-y-6" data-aos="fade-left">
            <div>
                <h1 class="text-3xl font-extrabold text-[--color-brand-deepblue]">{{ $service->title }}</h1>
                @if($service->description)
                    <p class="mt-4 text-lg text-slate-600">{{ $service->description }}</p>
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
                        Premium paper options available
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
                        Professional design consultation
                    </li>
                </ul>
            </div>

            <!-- Samples & Add to Cart -->
            <div class="rounded-xl bg-slate-50 p-6 ring-1 ring-black/5">
                <h3 class="mb-4 text-lg font-semibold text-[--color-brand-deepblue]">Choose a Sample</h3>
                @if($service->samples->count())
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach($service->samples as $sample)
                            <div class="flex gap-4 rounded-lg bg-white p-4 ring-1 ring-black/5">
                                <img src="{{ $sample->image_path ? asset('storage/' . $sample->image_path) : $service->image_path }}" class="w-20 h-20 rounded object-cover" alt="{{ $sample->title }}">
                                <div class="flex-1">
                                    <div class="font-semibold text-[--color-brand-deepblue]">{{ $sample->title }}</div>
                                    <div class="text-sm text-slate-600">{{ $sample->price ? 'PKR ' . number_format($sample->price, 0) : $service->formatted_price }}</div>
                                    <div class="mt-2 flex items-center gap-2">
                                        <input type="number" min="1" value="1" class="w-16 rounded border border-slate-300 px-2 py-1 text-center text-sm sample-qty" data-sample-id="{{ $sample->id }}">
                                        <button class="btn-primary add-sample-to-cart" data-service-id="{{ $service->id }}" data-sample-id="{{ $sample->id }}" data-unit-price="{{ $sample->price ?? $service->price }}">Add to Cart</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <input type="number" min="1" value="1" class="w-16 rounded border border-slate-300 px-2 py-1 text-center text-sm" id="serviceQty">
                        <button class="btn-primary" id="addServiceToCart" data-service-id="{{ $service->id }}" data-unit-price="{{ $service->price }}">Add to Cart</button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Related Services -->
    <section class="mt-16" data-aos="fade-up">
        <h2 class="section-title">Other Services</h2>
        <p class="section-subtitle">Explore our complete range of printing solutions.</p>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @php
                $relatedServices = App\Models\Service::active()->where('id', '!=', $service->id)->ordered()->take(3)->get();
            @endphp
            @foreach($relatedServices as $relatedService)
                <div class="card group">
                    <img src="{{ $relatedService->image_path }}" alt="{{ $relatedService->title }}" class="h-32 w-full rounded-lg object-cover" />
                    <div class="mt-3">
                        <h4 class="font-semibold text-[--color-brand-deepblue]">{{ $relatedService->title }}</h4>
                        <p class="text-sm text-slate-600">{{ $relatedService->price }}</p>
                        <a href="{{ route('service.show', $relatedService) }}" class="mt-2 inline-block text-sm font-medium text-[--color-brand-blue] hover:text-[--color-brand-orange]">View Details →</a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
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

            // Attempt server sync (non-blocking UX)
            syncToServer({ service_id: serviceId, service_sample_id: sampleId, quantity, unit_price: unitPrice });
        }

        if(e.target.id === 'addServiceToCart'){
            e.preventDefault();
            const btn = e.target;
            const serviceId = btn.dataset.serviceId;
            const unitPrice = parseFloat(btn.dataset.unitPrice);
            const quantity = parseInt(document.getElementById('serviceQty').value || 1);

            const cart = getLocalCart();
            const existing = cart.find(i => i.service_id==serviceId && !i.service_sample_id && i.unit_price==unitPrice);
            if(existing){ existing.quantity += quantity; } else { cart.push({ service_id: parseInt(serviceId), quantity, unit_price: unitPrice }); }
            setLocalCart(cart);
            updateNavbarCount();

            syncToServer({ service_id: serviceId, quantity, unit_price: unitPrice });
        }
    });

    updateNavbarCount();
});
</script>
@endpush
