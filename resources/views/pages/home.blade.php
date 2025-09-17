@extends('layouts.app')

@section('content')
<div class="min-h-screen">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-white to-orange-50 py-20 lg:py-32">
        <!-- Background decorative elements -->
        <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-blue-100 opacity-30 blur-3xl"></div>
        <div class="absolute -bottom-16 -left-16 h-80 w-80 rounded-full bg-orange-100 opacity-40 blur-3xl"></div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Hero Content -->
                <div class="text-center lg:text-left" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6 transition-all duration-700 ease-out" 
                        x-bind:class="loaded ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'">
                        Professional 
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-blue-800">Printing</span> 
                        Services
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 max-w-xl transition-all duration-700 delay-200 ease-out"
                       x-bind:class="loaded ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'">
                        High-quality wedding cards, visiting cards, flyers, banners, brochures, and posters with modern finishes and vibrant colors.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start transition-all duration-700 delay-400 ease-out"
                         x-bind:class="loaded ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'">
                        <a href="{{ route('services') }}" 
                           class="btn-primary transform hover:scale-105 hover:shadow-lg transition-all duration-200 ease-in-out focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                           aria-label="Explore our printing services">
                            <span>Explore Services</span>
                            <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                        <a href="{{ route('services') }}#order" 
                           class="btn-secondary transform hover:scale-105 hover:shadow-lg transition-all duration-200 ease-in-out focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                           aria-label="Start your order now">
                            Order Now
                        </a>
                    </div>
                </div>
                
                <!-- Hero Image -->
                <div class="relative" x-data="{ imageLoaded: false }" x-init="setTimeout(() => imageLoaded = true, 300)">
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl transform transition-all duration-1000 ease-out hover:scale-105"
                         x-bind:class="imageLoaded ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                        <img src="{{ asset('images/random.jpg') }}" 
                             alt="Professional printing services showcase" 
                             class="w-full h-96 object-cover" />
                        <!-- Overlay with floating badge -->
                        <div class="absolute bottom-4 left-4 bg-white/95 backdrop-blur-sm p-4 rounded-xl shadow-lg border border-white/20 transform transition-all duration-300 hover:scale-105">
                            <div class="flex items-center space-x-2">
                                <div class="h-2 w-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-sm font-medium text-gray-800">Premium Quality Guaranteed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-6" data-aos="fade-up">About Al-Tahir Graphics</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto leading-relaxed" data-aos="fade-up" data-aos-delay="100">
                With years of experience in the printing industry, we deliver exceptional quality printing services 
                that bring your vision to life. From wedding invitations to business materials, we use cutting-edge 
                technology and premium materials to ensure every project exceeds expectations.
            </p>
        </div>
    </section>

    <!-- Featured Services Section -->
    <section class="py-16 bg-gray-50" id="services">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4" data-aos="fade-up">Featured Services</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                    Everything you need to showcase your brand beautifully
                </p>
            </div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $featuredServices = App\Models\Service::featured()->active()->ordered()->take(3)->get();
                @endphp
                @foreach($featuredServices as $index => $service)
                    <div class="group relative bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" 
                         data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <div class="relative overflow-hidden">
                            <img src="{{ $service->image_path }}" 
                                 alt="{{ $service->title }}" 
                                 class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-110" />
                            <!-- Hover overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="absolute bottom-4 left-4 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                                <a href="{{ route('services') }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-white/90 backdrop-blur-sm text-gray-900 text-sm font-medium rounded-lg hover:bg-white transition-colors duration-200">
                                    View Details
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $service->title }}</h3>
                            <p class="text-gray-600 mb-4">{{ $service->price_display }}</p>
                            <a href="{{ route('services') }}#order" 
                               class="btn-primary w-full justify-center transform hover:scale-105 transition-transform duration-200">
                                Order Now
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Popular Designs Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4" data-aos="fade-up">Popular Designs</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                    Discover our most loved design samples
                </p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @php
                    $popularImages = [
                        'Wedding Card sample 01.jpg' => 'Wedding Cards',
                        'Visiting Card sample 01.jpg' => 'Business Cards',
                        'Banners.jpg' => 'Banners',
                        'Posters.jpg' => 'Posters'
                    ];
                @endphp
                @foreach($popularImages as $image => $title)
                    <div class="group relative overflow-hidden rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105" 
                         data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <img src="{{ asset('images/' . $image) }}" 
                             alt="{{ $title }}" 
                             class="w-full h-32 md:h-40 object-cover transition-transform duration-300 group-hover:scale-110" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute bottom-2 left-2 right-2">
                            <h4 class="text-white text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity duration-300 transform translate-y-2 group-hover:translate-y-0">
                                {{ $title }}
                            </h4>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-16 bg-gradient-to-r from-blue-50 to-orange-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4" data-aos="fade-up">How It Works</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                    Simple steps to get your perfect prints
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                @php
                    $steps = [
                        [
                            'icon' => '<svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>',
                            'title' => 'Choose Your Service',
                            'description' => 'Browse our wide range of printing services and select what you need.'
                        ],
                        [
                            'icon' => '<svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v1a1 1 0 01-1 1h-1v13a2 2 0 01-2 2H6a2 2 0 01-2-2V7H3a1 1 0 01-1-1V5a1 1 0 011-1h4z"></path></svg>',
                            'title' => 'Upload & Customize',
                            'description' => 'Upload your design or work with our team to create something amazing.'
                        ],
                        [
                            'icon' => '<svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>',
                            'title' => 'Fast Delivery',
                            'description' => 'Receive your high-quality prints delivered right to your door.'
                        ]
                    ];
                @endphp
                @foreach($steps as $index => $step)
                    <div class="text-center group" data-aos="fade-up" data-aos-delay="{{ $index * 150 }}">
                        <div class="relative mb-6">
                            <div class="w-16 h-16 mx-auto bg-blue-600 text-white rounded-full flex items-center justify-center group-hover:bg-blue-700 transition-colors duration-300 transform group-hover:scale-110">
                                {!! $step['icon'] !!}
                            </div>
                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-orange-500 text-white text-xs rounded-full flex items-center justify-center font-bold">
                                {{ $index + 1 }}
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ $step['title'] }}</h3>
                        <p class="text-gray-600">{{ $step['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4" data-aos="fade-up">What Our Customers Say</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                    Trusted by hundreds of satisfied customers
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                @php
                    $testimonials = [
                        [
                            'name' => 'Sarah Ahmed',
                            'role' => 'Bride',
                            'rating' => 5,
                            'text' => 'The wedding cards were absolutely beautiful! The quality exceeded our expectations and the delivery was right on time.'
                        ],
                        [
                            'name' => 'Muhammad Ali',
                            'role' => 'Business Owner',
                            'rating' => 5,
                            'text' => 'Professional business cards that really impressed our clients. Great quality and excellent customer service.'
                        ],
                        [
                            'name' => 'Fatima Khan',
                            'role' => 'Event Planner',
                            'rating' => 5,
                            'text' => 'Al-Tahir Graphics is our go-to for all printing needs. Consistent quality and reliable service every time.'
                        ]
                    ];
                @endphp
                @foreach($testimonials as $index => $testimonial)
                    <div class="bg-gray-50 rounded-2xl p-6 hover:bg-gray-100 transition-colors duration-300" 
                         data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <div class="flex items-center mb-4">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="h-5 w-5 {{ $i <= $testimonial['rating'] ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-gray-700 mb-4 italic">"{{ $testimonial['text'] }}"</p>
                        <div>
                            <div class="font-semibold text-gray-900">{{ $testimonial['name'] }}</div>
                            <div class="text-sm text-gray-600">{{ $testimonial['role'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                @php
                    $features = [
                        [
                            'icon' => '<svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17v4a2 2 0 002 2h4M13 13h4a2 2 0 012 2v4a2 2 0 01-2 2h-4"></path></svg>',
                            'title' => 'Vibrant Colors',
                            'description' => 'True-to-life colors and crisp details using professional-grade printers and premium inks.'
                        ],
                        [
                            'icon' => '<svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>',
                            'title' => 'Premium Materials',
                            'description' => 'Select from matte, glossy, textured, and specialty premium papers for the perfect finish.'
                        ],
                        [
                            'icon' => '<svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                            'title' => 'On-Time Delivery',
                            'description' => 'Fast turnaround with reliable delivery to your doorstep. We value your time as much as you do.'
                        ]
                    ];
                @endphp
                @foreach($features as $index => $feature)
                    <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1" 
                         data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                            {!! $feature['icon'] !!}
                        </div>
                        <h4 class="text-xl font-semibold text-gray-900 mb-3">{{ $feature['title'] }}</h4>
                        <p class="text-gray-600">{{ $feature['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="py-20 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6" data-aos="fade-up">
                Ready to Bring Your Vision to Life?
            </h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                Join hundreds of satisfied customers who trust Al-Tahir Graphics for their printing needs
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up" data-aos-delay="200">
                <a href="{{ route('services') }}" 
                   class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-100 transition-colors duration-200 transform hover:scale-105 focus:ring-2 focus:ring-offset-2 focus:ring-white">
                    <span>Get Started</span>
                    <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
                <a href="{{ route('about') }}" 
                   class="inline-flex items-center px-8 py-4 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-blue-600 transition-colors duration-200 transform hover:scale-105 focus:ring-2 focus:ring-offset-2 focus:ring-white">
                    Learn More About Us
                </a>
            </div>
        </div>
    </section>
</div>

<!-- Add Alpine.js animations script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple AOS-like animation observer
    const animateOnScroll = () => {
        const elements = document.querySelectorAll('[data-aos]');
        elements.forEach(el => {
            const rect = el.getBoundingClientRect();
            const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
            
            if (isVisible) {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }
        });
    };

    // Initialize elements with opacity 0 and transform
    document.querySelectorAll('[data-aos]').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });

    // Run on scroll and load
    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll(); // Run once on load
});
</script>
@endsection