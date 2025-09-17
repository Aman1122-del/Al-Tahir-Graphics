<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-number {
            font-size: 20px;
            font-weight: bold;
            color: #4f46e5;
        }
        .billing-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .billing-section {
            flex: 1;
        }
        .billing-section h3 {
            margin: 0 0 10px 0;
            color: #374151;
            font-size: 16px;
        }
        .billing-section p {
            margin: 5px 0;
            color: #6b7280;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .items-table th {
            background-color: #f9fafb;
            font-weight: bold;
            color: #374151;
        }
        .items-table .text-right {
            text-align: right;
        }
        .totals {
            width: 100%;
            max-width: 300px;
            margin-left: auto;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .totals .total-row {
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #374151;
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status.paid {
            background-color: #dcfce7;
            color: #166534;
        }
        .status.sent {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .status.overdue {
            background-color: #fecaca;
            color: #991b1b;
        }
        .status.draft {
            background-color: #fef3c7;
            color: #92400e;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9fafb;
            border-radius: 6px;
        }
        .notes h4 {
            margin: 0 0 10px 0;
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">{{ config('app.name', 'Al-Tahir Graphics') }}</div>
        <div class="invoice-info">
            <div class="invoice-number">Invoice {{ $invoice->invoice_number }}</div>
            <div>Date: {{ $invoice->created_at->format('M d, Y') }}</div>
            <div>Due: {{ $invoice->due_date->format('M d, Y') }}</div>
            <div>
                <span class="status {{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
            </div>
        </div>
    </div>

    <div class="billing-info">
        <div class="billing-section">
            <h3>Bill To:</h3>
            <p><strong>{{ $invoice->user->name }}</strong></p>
            <p>{{ $invoice->user->email }}</p>
            @if($invoice->order && $invoice->order->customer_phone)
                <p>{{ $invoice->order->customer_phone }}</p>
            @endif
        </div>
        <div class="billing-section">
            <h3>From:</h3>
            <p><strong>{{ config('app.name', 'Al-Tahir Graphics') }}</strong></p>
            <p>Professional Design Services</p>
            <p>Pakistan</p>
        </div>
    </div>

    @if($invoice->order && $invoice->order->orderItems->count() > 0)
    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->order->orderItems as $item)
            <tr>
                <td>{{ $item->service->title }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">PKR {{ number_format($item->unit_price, 0) }}</td>
                <td class="text-right">PKR {{ number_format($item->total_price, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">PKR {{ number_format($invoice->subtotal, 0) }}</td>
            </tr>
            <tr>
                <td>Tax:</td>
                <td class="text-right">PKR {{ number_format($invoice->tax_amount, 0) }}</td>
            </tr>
            <tr>
                <td>Discount:</td>
                <td class="text-right">PKR {{ number_format($invoice->discount_amount, 0) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total:</td>
                <td class="text-right">PKR {{ number_format($invoice->total_amount, 0) }}</td>
            </tr>
        </table>
    </div>

    @if($invoice->notes)
    <div class="notes">
        <h4>Notes:</h4>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>For any questions regarding this invoice, please contact us.</p>
    </div>
</body>
</html>
