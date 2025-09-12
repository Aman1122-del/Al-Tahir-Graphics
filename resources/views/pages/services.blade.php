@extends('layouts.app')

@section('content')
    <section class="rounded-3xl bg-white p-6 shadow-md ring-1 ring-black/5" data-aos="fade-up">
        <div class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-end">
            <div>
                <h1 class="section-title">Our Printing Services</h1>
                <p class="section-subtitle">Choose a service and place your order instantly.</p>
            </div>
            <a href="{{ route('cart.view') }}" class="btn-primary">View Cart</a>
        </div>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($services as $idx => $service)
                <div class="group overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-black/5 transition-all hover:-translate-y-1 hover:shadow-xl" data-aos="fade-up" data-aos-delay="{{ 50 * $idx }}">
                    <div class="relative h-44 w-full overflow-hidden">
                        <img src="{{ $service->image_path }}" alt="{{ $service->title }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                        <div class="absolute bottom-3 left-3 rounded bg-white/90 px-2 py-1 text-xs font-semibold text-[--color-brand-deepblue] shadow">
                            {{ $service->formatted_price }}
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-[--color-brand-deepblue]">{{ $service->title }}</h3>
                        @if($service->description)
                            <p class="mt-2 text-sm text-slate-600">{{ Str::limit($service->description, 80) }}</p>
                        @endif
                        <div class="mt-4 flex items-center justify-between">
                            <a href="{{ route('service.show', $service) }}" class="text-sm font-medium text-[--color-brand-blue] hover:text-[--color-brand-orange]">Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection


