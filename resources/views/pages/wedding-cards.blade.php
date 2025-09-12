@extends('layouts.app')

@section('content')
    <h1 class="section-title">Wedding Cards</h1>
    <p class="section-subtitle">Choose from elegant designs with different finishes and rates.</p>

    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @php
            $designs = [
                ['name' => 'Classic Foil', 'price' => 4999, 'img' => asset('images/Invitation card (wedding).jpg')],
                ['name' => 'Embossed Floral', 'price' => 5999, 'img' => asset('images/random.jpg')],
                ['name' => 'Minimal Luxe', 'price' => 4499, 'img' => asset('images/logo.jpg')],
            ];
        @endphp

        @foreach($designs as $d)
            <div class="card">
                <img src="{{ $d['img'] }}" alt="{{ $d['name'] }}" class="h-48 w-full rounded-xl object-cover ring-1 ring-black/5" />
                <div class="mt-3">
                    <div class="font-semibold">{{ $d['name'] }}</div>
                    <div class="text-sm text-slate-600">PKR {{ number_format($d['price'], 0) }}</div>
                    <div class="mt-3">
                        <button
                            class="btn-primary"
                            onclick="addWeddingToCart({{ \App\Models\Service::where('title','Wedding Cards')->value('id') }}, {{ $d['price'] }}, '{{ $d['name'] }}')"
                        >
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        async function addWeddingToCart(serviceId, price, variantName) {
            try {
                const res = await fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ service_id: serviceId, quantity: 1, unit_price: price, custom_requirements: variantName })
                });
                const data = await res.json();
                if (data.success) {
                    alert('Added to cart');
                    // update cart count if present
                    if (document.querySelector('.cart-count')) {
                        document.querySelector('.cart-count').textContent = data.item_count;
                    }
                }
            } catch (e) { console.error(e); }
        }
    </script>
@endsection


