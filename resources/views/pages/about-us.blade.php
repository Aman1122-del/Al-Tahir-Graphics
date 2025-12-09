@extends('layouts.app')

@section('content')
    <!-- About Section -->
    <section class="grid gap-10 md:grid-cols-2" data-aos="fade-up">
        <div class="space-y-4">
            <h1 class="section-title">About Al‑Tahir Graphics</h1>
            <p class="section-subtitle">We bring your ideas to life with premium printing solutions.</p>
            <p class="text-slate-600">With years of experience in the printing industry, our team delivers exceptional quality and timely service. From elegant wedding invitations to eye‑catching marketing materials, we help your brand stand out.</p>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow ring-1 ring-black/5">
            <img src="{{ asset('images/random2.png') }}" alt="About printing" class="w-full rounded-2xl object-cover" />
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="mt-14 grid gap-6 sm:grid-cols-3" data-aos="fade-up">
        @php
            $stats = [
                ['label' => 'Years of Experience', 'value' => 12],
                ['label' => 'Happy Clients', 'value' => 2500],
                ['label' => 'Projects Completed', 'value' => 8000],
            ];
        @endphp
        @foreach($stats as $i => $s)
            <div class="card text-center" data-aos="zoom-in" data-aos-delay="{{ 100 * $i }}">
                <div class="text-4xl font-extrabold text-[--color-brand-deepblue]" data-counter="{{ $s['value'] }}">0</div>
                <div class="mt-2 text-sm font-medium text-slate-600">{{ $s['label'] }}</div>
            </div>
        @endforeach
    </section>

    <!-- Contact Section -->
    <section class="mt-14 grid gap-10 md:grid-cols-2" data-aos="fade-up">
        <div>
            <h2 class="section-title">Contact Us</h2>
            <p class="section-subtitle">We would love to hear from you.</p>
            <form class="mt-6 grid gap-4">
                <input class="rounded-lg border border-slate-300 px-4 py-3 focus:border-[--color-brand-blue] focus:outline-none" placeholder="Your Name" />
                <input class="rounded-lg border border-slate-300 px-4 py-3 focus:border-[--color-brand-blue] focus:outline-none" placeholder="Email" />
                <input class="rounded-lg border border-slate-300 px-4 py-3 focus:border-[--color-brand-blue] focus:outline-none" placeholder="Phone" />
                <textarea class="rounded-lg border border-slate-300 px-4 py-3 focus:border-[--color-brand-blue] focus:outline-none" rows="5" placeholder="Your Message"></textarea>
                <button type="button" class="btn-primary w-fit">Send Message</button>
            </form>
        </div>
        <div class="overflow-hidden rounded-2xl shadow ring-1 ring-black/5">
            <iframe title="Map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d241317.11609986683!2d72.74109951731348!3d33.61637231286748!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x38dfbf991cd614ef%3A0x38b9ecb1f2c40b74!2sIslamabad!5e0!3m2!1sen!2sPK!4v1714920000000" width="100%" height="360" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const counters = document.querySelectorAll('[data-counter]');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const el = entry.target;
                        const target = parseInt(el.getAttribute('data-counter')) || 0;
                        const duration = 1200;
                        const start = performance.now();
                        const step = (now) => {
                            const progress = Math.min((now - start) / duration, 1);
                            el.textContent = Math.floor(progress * target).toLocaleString();
                            if (progress < 1) requestAnimationFrame(step);
                        };
                        requestAnimationFrame(step);
                        observer.unobserve(el);
                    }
                });
            }, { threshold: 0.5 });
            counters.forEach(c => observer.observe(c));
        });
    </script>
@endsection