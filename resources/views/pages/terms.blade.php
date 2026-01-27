@extends('layouts.app')

@section('content')
    <section class="items-center bg-slate-50 py-12 md:py-24" data-aos="fade-up">
        <div class="container mx-auto px-4">
            <div class="mx-auto max-w-4xl rounded-2xl bg-white p-8 shadow-sm md:p-12">
                <h1 class="mb-8 text-3xl font-bold text-slate-900 md:text-4xl text-center">Terms and Conditions</h1>
                <p class="mb-8 text-lg text-slate-600 text-center">Last updated: {{ date('F d, Y') }}</p>

                <div class="prose prose-slate max-w-none text-slate-600">
                    <h3 class="text-xl font-semibold text-slate-800">1. Introduction</h3>
                    <p>Welcome to Al-Tahir Graphics. By accessing our website and using our services, you agree to be bound by these Terms and Conditions. Please read them carefully.</p>

                    <h3 class="text-xl font-semibold text-slate-800 mt-6">2. Services</h3>
                    <p>We provide printing and graphic design services. We reserve the right to refuse service to anyone for any reason at any time.</p>

                    <h3 class="text-xl font-semibold text-slate-800 mt-6">3. Ordering and Payment</h3>
                    <p>All orders are subject to acceptance and availability. Prices for our products are subject to change without notice. Payment must be made in full before production begins, unless otherwise agreed upon.</p>

                    <h3 class="text-xl font-semibold text-slate-800 mt-6">4. Cancellations and Returns</h3>
                    <p>Due to the custom nature of our products, orders cannot be cancelled once production has started. Returns are only accepted for defective items or errors on our part.</p>

                    <h3 class="text-xl font-semibold text-slate-800 mt-6">5. Intellectual Property</h3>
                    <p>All content included on this site, such as text, graphics, logos, and images, is the property of Al-Tahir Graphics or its content suppliers and protected by copyright laws.</p>

                    <h3 class="text-xl font-semibold text-slate-800 mt-6">6. Changes to Terms</h3>
                    <p>We reserve the right to update, change or replace any part of these Terms and Conditions by posting updates and/or changes to our website. It is your responsibility to check this page periodically for changes.</p>

                    <h3 class="text-xl font-semibold text-slate-800 mt-6">7. Contact Information</h3>
                    <p>Questions about the Terms and Conditions should be sent to us via our contact page.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
