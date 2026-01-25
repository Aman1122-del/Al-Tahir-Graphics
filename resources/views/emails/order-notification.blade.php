<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Order - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #dc2626;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f8fafc;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .order-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .order-item {
            border-bottom: 1px solid #e5e7eb;
            padding: 15px 0;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .total {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background: #dc2626;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
        .urgent {
            background: #fef2f2;
            border: 1px solid #fecaca;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Order Received</h1>
        <p>Order #{{ $order->order_number }}</p>
    </div>

    <div class="content">
        <div class="urgent">
            <h3>🚨 New Order Alert</h3>
            <p>A new order has been placed and requires your attention.</p>
        </div>

        <h2>Order Details</h2>
        <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
        <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
        <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
        <p><strong>Email:</strong> {{ $order->customer_email }}</p>
        <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
        <p><strong>User ID:</strong> {{ $order->user_id ?: 'Guest' }}</p>

        <h3>Shipping Address</h3>
        <p>{{ $order->shipping_address }}</p>

        <h3>Order Items</h3>
        <div class="order-details">
            @foreach($orderItems as $item)
            <div class="order-item">
                <h4>{{ $item->service_name }}</h4>
                <p><strong>Quantity:</strong> {{ $item->quantity }}</p>
                <p><strong>Unit Price:</strong> PKR {{ number_format($item->unit_price, 0) }}</p>
                @if($item->size || $item->paper_type || $item->finish)
                <p><strong>Variants:</strong>
                    @if($item->size) Size: {{ ucfirst($item->size) }} @endif
                    @if($item->paper_type) | Paper: {{ ucfirst($item->paper_type) }} @endif
                    @if($item->finish) | Finish: {{ ucfirst($item->finish) }} @endif
                </p>
                @endif
                @if($item->custom_requirements)
                <p><strong>Custom Requirements:</strong> {{ $item->custom_requirements }}</p>
                @endif
                @if($item->wedding_details)
                <div style="background: #eff6ff; padding: 10px; border-radius: 4px; margin: 10px 0;">
                    <p><strong>Wedding Details:</strong></p>
                    <p>Groom: {{ $item->wedding_details['groom'] ?? 'N/A' }} | Bride: {{ $item->wedding_details['bride'] ?? 'N/A' }}</p>
                    @if(isset($item->wedding_details['eventType']) && !empty($item->wedding_details['eventType']))
                        <p>Event: {{ $item->wedding_details['eventType'] }}</p>
                    @endif
                    @if(isset($item->wedding_details['dateTime']) && !empty($item->wedding_details['dateTime']))
                        <p>Date/Time: {{ $item->wedding_details['dateTime'] }}</p>
                    @endif
                    @if(isset($item->wedding_details['venue']) && !empty($item->wedding_details['venue']))
                        <p>Venue: {{ $item->wedding_details['venue'] }}</p>
                    @endif
                    @if(!empty($item->wedding_details['additionalMessage'] ?? $item->wedding_details['remarks'] ?? ''))
                        <p>Message: {{ $item->wedding_details['additionalMessage'] ?? $item->wedding_details['remarks'] }}</p>
                    @endif
                </div>
                @endif
                @if($item->visiting_card_details)
                <div style="background: #f0fdf4; padding: 10px; border-radius: 4px; margin: 10px 0;">
                    <p><strong>Visiting Card Details:</strong></p>
                    <p>Name: {{ $item->visiting_card_details['businessName'] ?? 'N/A' }}</p>
                    @if(isset($item->visiting_card_details['designation']) && !empty($item->visiting_card_details['designation']))
                        <p>Designation: {{ $item->visiting_card_details['designation'] }}</p>
                    @endif
                    @if(isset($item->visiting_card_details['companyName']) && !empty($item->visiting_card_details['companyName']))
                        <p>Company: {{ $item->visiting_card_details['companyName'] }}</p>
                    @endif
                    <p>Mobile: {{ $item->visiting_card_details['mobileNumber'] ?? 'N/A' }}</p>
                    @if(isset($item->visiting_card_details['whatsappNumber']) && !empty($item->visiting_card_details['whatsappNumber']))
                        <p>WhatsApp: {{ $item->visiting_card_details['whatsappNumber'] }}</p>
                    @endif
                    @if(isset($item->visiting_card_details['emailAddress']) && !empty($item->visiting_card_details['emailAddress']))
                        <p>Email: {{ $item->visiting_card_details['emailAddress'] }}</p>
                    @endif
                    <p>Printing: {{ $item->visiting_card_details['printingSide'] ?? 'N/A' }}</p>
                    @if(isset($item->visiting_card_details['officeAddress']) && !empty($item->visiting_card_details['officeAddress']))
                        <p>Address: {{ $item->visiting_card_details['officeAddress'] }}</p>
                    @endif
                    @if(!empty($item->visiting_card_details['additionalMessage'] ?? ''))
                        <p>Message: {{ $item->visiting_card_details['additionalMessage'] }}</p>
                    @endif
                </div>
                @endif
                @if($item->panaflex_details)
                <div style="background: #faf5ff; padding: 10px; border-radius: 4px; margin: 10px 0;">
                    <p><strong>Panaflex Printing Details:</strong></p>
                    <p>Business/Event Name: {{ $item->panaflex_details['businessName'] ?? 'N/A' }}</p>
                    <p>Panaflex Size: {{ $item->panaflex_details['panaflexSize'] ?? 'N/A' }}</p>
                    @if(isset($item->panaflex_details['eventType']) && !empty($item->panaflex_details['eventType']))
                        <p>Event Type: {{ $item->panaflex_details['eventType'] }}</p>
                    @endif
                    <p>Main Heading: {{ $item->panaflex_details['mainHeading'] ?? 'N/A' }}</p>
                    @if(isset($item->panaflex_details['subHeading']) && !empty($item->panaflex_details['subHeading']))
                        <p>Sub Heading: {{ $item->panaflex_details['subHeading'] }}</p>
                    @endif
                    @if(isset($item->panaflex_details['dateTime']) && !empty($item->panaflex_details['dateTime']))
                        <p>Date/Time: {{ $item->panaflex_details['dateTime'] }}</p>
                    @endif
                    <p>Venue/Location: {{ $item->panaflex_details['venue'] ?? 'N/A' }}</p>
                    <p>Contact Number: {{ $item->panaflex_details['contactNumber'] ?? 'N/A' }}</p>
                    @if(!empty($item->panaflex_details['additionalInstructions'] ?? ''))
                        <p>Instructions: {{ $item->panaflex_details['additionalInstructions'] }}</p>
                    @endif
                </div>
                @endif
                @if($item->flyer_brochure_details)
                <div style="background: #ecfdf5; padding: 10px; border-radius: 4px; margin: 10px 0;">
                    <p><strong>Flyer/Brochure Details:</strong></p>
                    <p>Business/Brand Name: {{ $item->flyer_brochure_details['businessName'] ?? 'N/A' }}</p>
                    <p>Brochure Type: {{ $item->flyer_brochure_details['brochureType'] ?? 'N/A' }}</p>
                    <p>Paper Type: {{ $item->flyer_brochure_details['paperType'] ?? 'N/A' }}</p>
                    <p>Fold Type: {{ $item->flyer_brochure_details['foldType'] ?? 'N/A' }}</p>
                    <p>Quantity: {{ $item->flyer_brochure_details['quantity'] ?? 'N/A' }}</p>
                    <p>Contact Info: {{ $item->flyer_brochure_details['contactInfo'] ?? 'N/A' }}</p>
                    @if(isset($item->flyer_brochure_details['validityDate']) && !empty($item->flyer_brochure_details['validityDate']))
                        <p>Validity Date: {{ $item->flyer_brochure_details['validityDate'] }}</p>
                    @endif
                    @if(isset($item->flyer_brochure_details['address']) && !empty($item->flyer_brochure_details['address']))
                        <p>Address: {{ $item->flyer_brochure_details['address'] }}</p>
                    @endif
                    @if(isset($item->flyer_brochure_details['offerDetails']) && !empty($item->flyer_brochure_details['offerDetails']))
                        <p>Offer Details: {{ $item->flyer_brochure_details['offerDetails'] }}</p>
                    @endif
                    @if(!empty($item->flyer_brochure_details['additionalMessage'] ?? ''))
                        <p>Message: {{ $item->flyer_brochure_details['additionalMessage'] }}</p>
                    @endif
                </div>
                @endif
                <p><strong>Total:</strong> PKR {{ number_format($item->total_price, 0) }}</p>
            </div>
            @endforeach

            <div class="total">
                <p>Subtotal: PKR {{ number_format($order->subtotal, 0) }}</p>
                <p>Shipping: PKR {{ number_format($order->shipping_cost, 0) }}</p>
                <p><strong>Total Amount: PKR {{ number_format($order->total_amount, 0) }}</strong></p>
            </div>
        </div>

        <h3>Payment Information</h3>
        <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
        <p><strong>Payment Status:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</p>

        @if($order->payment_method === 'manual_transfer' && $order->payment_screenshot_path)
        <p><strong>Payment Screenshot:</strong> <a href="{{ Storage::url($order->payment_screenshot_path) }}" target="_blank">View Screenshot</a></p>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('admin.orders.show', $order) }}" class="btn">View Order in Admin Panel</a>
        </div>

        <div style="background: #f0f9ff; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4>Next Steps</h4>
            <ul>
                <li>Review the order details</li>
                <li>Verify payment if required</li>
                <li>Assign to a designer if needed</li>
                <li>Update order status</li>
                <li>Contact customer if necessary</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p>This is an automated notification from Al-Tahir Graphics Admin System</p>
    </div>
</body>
</html>
