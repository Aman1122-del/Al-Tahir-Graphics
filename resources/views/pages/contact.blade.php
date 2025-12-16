@extends('layouts.app')

@section('content')
    <section class="grid gap-10 md:grid-cols-2 mt-8" data-aos="fade-up">
        <div class="pt-8">
            <h1 class="section-title">Contact Us</h1>
            <p class="section-subtitle">We would love to hear from you.</p>
            <form class="mt-6 grid gap-4">
                <input
                    class="rounded-lg border border-slate-300 px-4 py-3 focus:border-[--color-brand-blue] focus:outline-none"
                    placeholder="Your Name" />
                <input
                    class="rounded-lg border border-slate-300 px-4 py-3 focus:border-[--color-brand-blue] focus:outline-none"
                    placeholder="Email" />
                <input
                    class="rounded-lg border border-slate-300 px-4 py-3 focus:border-[--color-brand-blue] focus:outline-none"
                    placeholder="Phone" />
                <textarea class="rounded-lg border border-slate-300 px-4 py-3 focus:border-[--color-brand-blue] focus:outline-none"
                    rows="5" placeholder="Your Message"></textarea>
                <button type="button" class="btn-primary w-fit">Send Message</button>
            </form>
        </div>
        <div class="overflow-hidden rounded-2xl shadow ring-1 ring-black/5">
            <iframe title="Map"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d241317.11609986683!2d72.74109951731348!3d33.61637231286748!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x38dfbf991cd614ef%3A0x38b9ecb1f2c40b74!2sIslamabad!5e0!3m2!1sen!2sPK!4v1714920000000"
                width="100%" height="360" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>
@endsection
