@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-8">
            <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-green-100 flex items-center justify-center">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="section-title text-green-600">Order Confirmed!</h1>
            <p class="section-subtitle">Thank you for your order. We've received your request and will process it shortly.</p>
        </div>

        <div class="grid gap-8 lg:grid-cols-2">
            <!-- Order Details -->
            <div class="bg-white rounded-2xl shadow-md ring-1 ring-black/5 p-6">
                <h3 class="text-lg font-semibold text-[--color-brand-deepblue] mb-4">Order Details</h3>

                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Order Number:</span>
                        <span class="font-medium">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Order Date:</span>
                        <span class="font-medium">{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Order Status:</span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            @if($order->order_status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->order_status === 'processing') bg-blue-100 text-blue-800
                            @elseif($order->order_status === 'completed') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Payment Status:</span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            @if($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->payment_status === 'pending_verification') bg-orange-100 text-orange-800
                            @elseif($order->payment_status === 'paid') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-white rounded-2xl shadow-md ring-1 ring-black/5 p-6">
                <h3 class="text-lg font-semibold text-[--color-brand-deepblue] mb-4">Customer Information</h3>

                <div class="space-y-3">
                    <div>
                        <span class="text-slate-600 text-sm">Name:</span>
                        <p class="font-medium">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <span class="text-slate-600 text-sm">Email:</span>
                        <p class="font-medium">{{ $order->customer_email }}</p>
                    </div>
                    <div>
                        <span class="text-slate-600 text-sm">Phone:</span>
                        <p class="font-medium">{{ $order->customer_phone }}</p>
                    </div>
                    <div>
                        <span class="text-slate-600 text-sm">Shipping Address:</span>
                        <p class="font-medium">{{ $order->shipping_address }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="mt-8 bg-white rounded-2xl shadow-md ring-1 ring-black/5 p-6">
            <h3 class="text-lg font-semibold text-[--color-brand-deepblue] mb-4">Order Items</h3>

            <div class="space-y-4">
                @foreach($order->orderItems as $item)
                    <div class="flex items-center gap-4 py-3 border-b border-slate-100 last:border-b-0">
                        <div class="w-16 h-16 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-slate-900">{{ $item->service_name }}</h4>
                            <p class="text-sm text-slate-500">Qty: {{ $item->quantity }}</p>
                            @if($item->custom_requirements)
                                <p class="text-xs text-slate-600 mt-1">
                                    <strong>Requirements:</strong> {{ $item->custom_requirements }}
                                </p>
                            @endif
                            @if($item->wedding_details)
                                <div class="mt-2 p-2 bg-blue-50 rounded border border-blue-100">
                                    <p class="text-xs font-bold text-blue-600 uppercase tracking-widest">Wedding Details</p>
                                    <div class="grid grid-cols-1 gap-1 text-xs mt-1">
                                        <p><span class="text-slate-500">Groom:</span> {{ $item->wedding_details['groom'] ?? 'N/A' }}</p>
                                        <p><span class="text-slate-500">Bride:</span> {{ $item->wedding_details['bride'] ?? 'N/A' }}</p>
                                        @if(isset($item->wedding_details['eventType']) && !empty($item->wedding_details['eventType']))
                                            <p><span class="text-slate-500">Event:</span> {{ $item->wedding_details['eventType'] }}</p>
                                        @endif
                                        @if(isset($item->wedding_details['dateTime']) && !empty($item->wedding_details['dateTime']))
                                            <p><span class="text-slate-500">Date/Time:</span> {{ $item->wedding_details['dateTime'] }}</p>
                                        @endif
                                        @if(isset($item->wedding_details['venue']) && !empty($item->wedding_details['venue']))
                                            <p><span class="text-slate-500">Venue:</span> {{ $item->wedding_details['venue'] }}</p>
                                        @endif
                                        @if(!empty($item->wedding_details['additionalMessage'] ?? $item->wedding_details['remarks'] ?? ''))
                                            <p><span class="text-slate-500">Message:</span> {{ $item->wedding_details['additionalMessage'] ?? $item->wedding_details['remarks'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if($item->visiting_card_details)
                                <div class="mt-2 p-2 bg-green-50 rounded border border-green-100">
                                    <p class="text-xs font-bold text-green-600 uppercase tracking-widest">Visiting Card Details</p>
                                    <div class="grid grid-cols-1 gap-1 text-xs mt-1">
                                        <p><span class="text-slate-500">Name:</span> {{ $item->visiting_card_details['businessName'] ?? 'N/A' }}</p>
                                        @if(isset($item->visiting_card_details['designation']) && !empty($item->visiting_card_details['designation']))
                                            <p><span class="text-slate-500">Designation:</span> {{ $item->visiting_card_details['designation'] }}</p>
                                        @endif
                                        @if(isset($item->visiting_card_details['companyName']) && !empty($item->visiting_card_details['companyName']))
                                            <p><span class="text-slate-500">Company:</span> {{ $item->visiting_card_details['companyName'] }}</p>
                                        @endif
                                        <p><span class="text-slate-500">Mobile:</span> {{ $item->visiting_card_details['mobileNumber'] ?? 'N/A' }}</p>
                                        @if(isset($item->visiting_card_details['whatsappNumber']) && !empty($item->visiting_card_details['whatsappNumber']))
                                            <p><span class="text-slate-500">WhatsApp:</span> {{ $item->visiting_card_details['whatsappNumber'] }}</p>
                                        @endif
                                        @if(isset($item->visiting_card_details['emailAddress']) && !empty($item->visiting_card_details['emailAddress']))
                                            <p><span class="text-slate-500">Email:</span> {{ $item->visiting_card_details['emailAddress'] }}</p>
                                        @endif
                                        <p><span class="text-slate-500">Printing:</span> {{ $item->visiting_card_details['printingSide'] ?? 'N/A' }}</p>
                                        @if(isset($item->visiting_card_details['officeAddress']) && !empty($item->visiting_card_details['officeAddress']))
                                            <p><span class="text-slate-500">Address:</span> {{ $item->visiting_card_details['officeAddress'] }}</p>
                                        @endif
                                        @if(!empty($item->visiting_card_details['additionalMessage'] ?? ''))
                                            <p><span class="text-slate-500">Message:</span> {{ $item->visiting_card_details['additionalMessage'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if($item->panaflex_details)
                                <div class="mt-2 p-2 bg-purple-50 rounded border border-purple-100">
                                    <p class="text-xs font-bold text-purple-600 uppercase tracking-widest">Panaflex Details</p>
                                    <div class="grid grid-cols-1 gap-1 text-xs mt-1">
                                        <p><span class="text-slate-500">Business/Event Name:</span> {{ $item->panaflex_details['businessName'] ?? 'N/A' }}</p>
                                        <p><span class="text-slate-500">Panaflex Size:</span> {{ $item->panaflex_details['panaflexSize'] ?? 'N/A' }}</p>
                                        @if(isset($item->panaflex_details['eventType']) && !empty($item->panaflex_details['eventType']))
                                            <p><span class="text-slate-500">Event Type:</span> {{ $item->panaflex_details['eventType'] }}</p>
                                        @endif
                                        <p><span class="text-slate-500">Main Heading:</span> {{ $item->panaflex_details['mainHeading'] ?? 'N/A' }}</p>
                                        @if(isset($item->panaflex_details['subHeading']) && !empty($item->panaflex_details['subHeading']))
                                            <p><span class="text-slate-500">Sub Heading:</span> {{ $item->panaflex_details['subHeading'] }}</p>
                                        @endif
                                        @if(isset($item->panaflex_details['dateTime']) && !empty($item->panaflex_details['dateTime']))
                                            <p><span class="text-slate-500">Date/Time:</span> {{ $item->panaflex_details['dateTime'] }}</p>
                                        @endif
                                        <p><span class="text-slate-500">Venue/Location:</span> {{ $item->panaflex_details['venue'] ?? 'N/A' }}</p>
                                        <p><span class="text-slate-500">Contact Number:</span> {{ $item->panaflex_details['contactNumber'] ?? 'N/A' }}</p>
                                        @if(!empty($item->panaflex_details['additionalInstructions'] ?? ''))
                                            <p><span class="text-slate-500">Instructions:</span> {{ $item->panaflex_details['additionalInstructions'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if($item->flyer_brochure_details)
                                <div class="mt-2 p-2 bg-emerald-50 rounded border border-emerald-100">
                                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Flyer/Brochure Details</p>
                                    <div class="grid grid-cols-1 gap-1 text-xs mt-1">
                                        <p><span class="text-slate-500">Business/Brand Name:</span> {{ $item->flyer_brochure_details['businessName'] ?? 'N/A' }}</p>
                                        <p><span class="text-slate-500">Brochure Type:</span> {{ $item->flyer_brochure_details['brochureType'] ?? 'N/A' }}</p>
                                        <p><span class="text-slate-500">Paper Type:</span> {{ $item->flyer_brochure_details['paperType'] ?? 'N/A' }}</p>
                                        <p><span class="text-slate-500">Fold Type:</span> {{ $item->flyer_brochure_details['foldType'] ?? 'N/A' }}</p>
                                        <p><span class="text-slate-500">Quantity:</span> {{ $item->flyer_brochure_details['quantity'] ?? 'N/A' }}</p>
                                        <p><span class="text-slate-500">Contact Info:</span> {{ $item->flyer_brochure_details['contactInfo'] ?? 'N/A' }}</p>
                                        @if(isset($item->flyer_brochure_details['validityDate']) && !empty($item->flyer_brochure_details['validityDate']))
                                            <p><span class="text-slate-500">Validity Date:</span> {{ $item->flyer_brochure_details['validityDate'] }}</p>
                                        @endif
                                        @if(isset($item->flyer_brochure_details['address']) && !empty($item->flyer_brochure_details['address']))
                                            <p><span class="text-slate-500">Address:</span> {{ $item->flyer_brochure_details['address'] }}</p>
                                        @endif
                                        @if(isset($item->flyer_brochure_details['offerDetails']) && !empty($item->flyer_brochure_details['offerDetails']))
                                            <p><span class="text-slate-500">Offer Details:</span> {{ $item->flyer_brochure_details['offerDetails'] }}</p>
                                        @endif
                                        @if(!empty($item->flyer_brochure_details['additionalMessage'] ?? ''))
                                            <p><span class="text-slate-500">Message:</span> {{ $item->flyer_brochure_details['additionalMessage'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-slate-900">{{ 'PKR ' . number_format($item->total_price, 0) }}</p>
                            <p class="text-sm text-slate-500">{{ 'PKR ' . number_format($item->unit_price, 0) }} each</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Order Summary -->
        <div class="mt-8 bg-white rounded-2xl shadow-md ring-1 ring-black/5 p-6">
            <h3 class="text-lg font-semibold text-[--color-brand-deepblue] mb-4">Order Summary</h3>

            <div class="max-w-md ml-auto">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span>Subtotal ({{ $order->orderItems->count() }} items)</span>
                        <span class="font-medium">{{ 'PKR ' . number_format($order->subtotal, 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Shipping ({{ ucfirst($order->shipping_method) }})</span>
                        <span class="font-medium">{{ 'PKR ' . number_format($order->shipping_cost, 0) }}</span>
                    </div>
                    <div class="border-t border-slate-200 pt-3">
                        <div class="flex justify-between text-lg font-bold text-[--color-brand-deepblue]">
                            <span>Total</span>
                            <span>{{ 'PKR ' . number_format($order->total_amount, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="mt-8 bg-blue-50 rounded-2xl p-6 border border-blue-200">
            <h3 class="text-lg font-semibold text-[--color-brand-deepblue] mb-4">What Happens Next?</h3>

            <div class="space-y-4">
                @if($order->payment_method === 'manual_transfer')
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-blue-600 text-xs font-bold">1</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-slate-900">Complete Payment</h4>
                            <p class="text-sm text-slate-600">Please complete your bank transfer using the details provided during checkout. Once transferred, upload the payment screenshot in your dashboard.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-blue-600 text-xs font-bold">2</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-slate-900">Payment Verification</h4>
                            <p class="text-sm text-slate-600">Our team will verify your payment within 24 hours and update your order status.</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-blue-600 text-xs font-bold">1</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-slate-900">Order Processing</h4>
                            <p class="text-sm text-slate-600">We'll start processing your order immediately and assign it to our design team.</p>
                        </div>
                    </div>
                @endif

                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-blue-600 text-xs font-bold">{{ $order->payment_method === 'manual_transfer' ? '3' : '2' }}</span>
                    </div>
                    <div>
                        <h4 class="font-medium text-slate-900">Design & Production</h4>
                        <p class="text-sm text-slate-600">Our team will work on your design and keep you updated on the progress through your dashboard.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-blue-600 text-xs font-bold">{{ $order->payment_method === 'manual_transfer' ? '4' : '3' }}</span>
                    </div>
                    <div>
                        <h4 class="font-medium text-slate-900">Delivery</h4>
                        <p class="text-sm text-slate-600">Your order will be delivered to your specified address within the selected shipping timeframe.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('dashboard') }}" class="btn-primary">
                Go to Dashboard
            </a>
            <a href="{{ route('services') }}" class="btn-secondary">
                Continue Shopping
            </a>
        </div>

        <!-- Contact Support -->
        <div class="mt-8 text-center">
            <p class="text-slate-600 mb-2">Have questions about your order?</p>
            <a href="{{ route('chat.index') }}" class="text-[--color-brand-blue] hover:text-[--color-brand-orange] font-medium">
                Chat with our support team
            </a>
        </div>
    </div>
@endsection
