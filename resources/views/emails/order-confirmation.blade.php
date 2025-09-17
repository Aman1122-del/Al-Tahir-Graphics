<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmation - {{ $order->order_number }}</title>
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
            background: #3b82f6;
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
            background: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Order Confirmation</h1>
        <p>Thank you for your order!</p>
    </div>
    
    <div class="content">
        <h2>Order Details</h2>
        <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
        <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
        <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
        <p><strong>Email:</strong> {{ $order->customer_email }}</p>
        <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
        
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
        
        @if($order->payment_method === 'manual_transfer')
        <div style="background: #fef3c7; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4>Payment Instructions</h4>
            <p>Please transfer the amount to our bank account and send the payment screenshot to our WhatsApp or email.</p>
            <p>Account details will be sent to you shortly.</p>
        </div>
        @endif
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('checkout.confirmation', $order) }}" class="btn">View Order Details</a>
        </div>
    </div>
    
    <div class="footer">
        <p>Thank you for choosing Al-Tahir Graphics!</p>
        <p>If you have any questions, please contact us.</p>
    </div>
</body>
</html>
