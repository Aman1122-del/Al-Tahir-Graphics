@extends('layouts.app')

@section('content')
    <!-- Hero -->
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[--color-brand-deepblue] to-[--color-brand-blue] px-6 py-16 text-white shadow-xl" data-aos="fade-up">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -bottom-16 -left-12 h-72 w-72 rounded-full bg-[--color-brand-orange]/20 blur-3xl"></div>
        <div class="relative z-10 grid items-center gap-10 md:grid-cols-2">
            <div>
                <h1 class="text-balance text-4xl font-extrabold tracking-tight md:text-5xl">Professional Printing Press Services</h1>
                <p class="mt-4 max-w-xl text-white/80 md:text-lg">High-quality wedding cards, visiting cards, flyers, banners, brochures, and posters with modern finishes and vibrant colors.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="/services" class="btn-primary bg-white text-[--color-brand-deepblue] hover:bg-white/95 hover:bg-white/95 hover:text-[--color-brand-deepblue]">Explore Services</a>
<a href="/services#order" class="btn-primary">Order Now</a>
                </div>
            </div>
            <div class="relative" data-aos="zoom-in" data-aos-delay="150">
                <img src="{{ asset('images/random.jpg') }}" alt="Printing press" class="mx-auto w-full max-w-md rounded-2xl shadow-2xl ring-1 ring-white/20" />
                <div class="absolute -bottom-4 -left-4 rounded-xl bg-white/90 p-4 text-sm text-[--color-brand-deepblue] shadow-lg ring-1 ring-black/5">
                    Premium quality inks & papers
                </div>
            </div>
        </div>
    </section>

    
    <section class="mt-14" id="services">
        <h2 class="section-title" data-aos="fade-up">Featured Services</h2>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Everything you need to showcase your brand beautifully.</p>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @php
                $featuredServices = App\Models\Service::featured()->active()->ordered()->take(3)->get();
            @endphp
            @foreach($featuredServices as $idx => $service)
                <div class="card" data-aos="fade-up" data-aos-delay="{{ 100 * $idx }}">
                    <img src="{{ $service->image_path }}" alt="{{ $service->title }}" class="h-44 w-full rounded-xl object-cover" />
                    <div class="mt-4 flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-[--color-brand-deepblue]">{{ $service->title }}</h3>
                            <p class="text-sm text-slate-600">{{ $service->price_display }}</p>
                        </div>
                        <a href="{{ route('services') }}#order" class="btn-primary">Order</a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Highlights -->
    <section class="mt-16 grid gap-6 md:grid-cols-3" data-aos="fade-up">
        <div class="card">
            <h4 class="font-semibold text-[--color-brand-deepblue]">Vibrant Colors</h4>
            <p class="mt-2 text-sm text-slate-600">True-to-life colors and crisp details using professional-grade printers.</p>
        </div>
        <div class="card">
            <h4 class="font-semibold text-[--color-brand-deepblue]">Premium Materials</h4>
            <p class="mt-2 text-sm text-slate-600">Select from matte, glossy, textured, and specialty premium papers.</p>
        </div>
        <div class="card">
            <h4 class="font-semibold text-[--color-brand-deepblue]">On-Time Delivery</h4>
            <p class="mt-2 text-sm text-slate-600">Fast turnaround with reliable delivery to your doorstep.</p>
        </div>
    </section>
@endsection


