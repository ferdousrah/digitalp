<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Invoice {{ $order->order_number }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        color: #1f2937;
        background: #fff;
    }
    .page { padding: 40px 48px; }

    /* Header */
    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 36px; border-bottom: 3px solid #f97316; padding-bottom: 24px; }
    .brand-name { font-size: 22px; font-weight: 700; color: #f97316; letter-spacing: 0.02em; }
    .brand-tagline { font-size: 10px; color: #6b7280; margin-top: 3px; }
    .invoice-title { text-align: right; }
    .invoice-title h2 { font-size: 24px; font-weight: 800; color: #111827; letter-spacing: 0.06em; }
    .invoice-title p { font-size: 10px; color: #6b7280; margin-top: 2px; }

    /* Info grid */
    .info-grid { display: flex; justify-content: space-between; margin-bottom: 28px; gap: 20px; }
    .info-box { flex: 1; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 14px 16px; }
    .info-box .label { font-size: 9px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
    .info-box .value { font-size: 12px; font-weight: 600; color: #111827; line-height: 1.6; }
    .order-number-box { background: #fff7ed; border-color: #fed7aa; }
    .order-number-box .value { font-size: 15px; font-weight: 800; color: #ea580c; letter-spacing: 0.05em; }

    /* Address grid */
    .address-grid { display: flex; gap: 16px; margin-bottom: 28px; }
    .address-card { flex: 1; border: 1px solid #e5e7eb; border-radius: 6px; padding: 14px 16px; }
    .address-card h4 { font-size: 9px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 2px solid #f97316; display: inline-block; }
    .address-card p { font-size: 11px; color: #374151; line-height: 1.7; margin-top: 4px; }
    .address-card .name { font-weight: 700; color: #111827; }

    /* Items table */
    .items-section h3 { font-size: 11px; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; font-size: 11px; }
    thead tr { background: #111827; color: #fff; }
    thead th { padding: 10px 14px; text-align: left; font-weight: 600; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; }
    thead th:last-child { text-align: right; }
    thead th.center { text-align: center; }
    tbody tr { border-bottom: 1px solid #f3f4f6; }
    tbody tr:nth-child(even) { background: #fafafa; }
    tbody td { padding: 10px 14px; color: #374151; }
    tbody td.center { text-align: center; }
    tbody td.right { text-align: right; font-weight: 600; color: #111827; }
    .product-name { font-weight: 600; color: #111827; }

    /* Totals */
    .totals { margin-top: 0; }
    .totals table { width: auto; margin-left: auto; min-width: 240px; }
    .totals td { padding: 7px 14px; }
    .totals .total-row { background: #fff7ed; border-top: 2px solid #f97316; }
    .totals .total-row td { font-size: 14px; font-weight: 800; color: #ea580c; }
    .totals .discount-row td { color: #16a34a; font-weight: 600; }

    /* Footer */
    .footer { margin-top: 40px; border-top: 1px solid #e5e7eb; padding-top: 16px; text-align: center; color: #9ca3af; font-size: 10px; line-height: 1.8; }

    /* Badge */
    .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: capitalize; }
    .badge-pending  { background: #fef3c7; color: #d97706; }
    .badge-cod      { background: #f0fdf4; color: #16a34a; }
    .badge-bkash    { background: #fdf4ff; color: #9333ea; }
    .badge-online   { background: #eff6ff; color: #2563eb; }
</style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div>
            <div class="brand-name">Digital Support</div>
            <div class="brand-tagline">Your trusted digital partner</div>
        </div>
        <div class="invoice-title">
            <h2>INVOICE</h2>
            <p>Date: {{ $order->created_at->format('d M Y') }}</p>
        </div>
    </div>

    {{-- Order info --}}
    <div class="info-grid">
        <div class="info-box order-number-box">
            <div class="label">Order Number</div>
            <div class="value">{{ $order->order_number }}</div>
        </div>
        <div class="info-box">
            <div class="label">Order Status</div>
            <div class="value">
                <span class="badge badge-pending">{{ ucfirst($order->status) }}</span>
            </div>
        </div>
        <div class="info-box">
            <div class="label">Payment Method</div>
            <div class="value">{{ $order->payment_method_label }}</div>
        </div>
        <div class="info-box">
            <div class="label">Payment Status</div>
            <div class="value">{{ ucfirst($order->payment_status) }}</div>
        </div>
    </div>

    {{-- Addresses --}}
    <div class="address-grid">
        <div class="address-card">
            <h4>Shipping Address</h4>
            <p class="name">{{ $order->shipping_name }}</p>
            <p>{{ $order->shipping_phone }}</p>
            <p>{{ $order->shipping_thana }}, {{ $order->shipping_district }}</p>
            <p>{{ $order->shipping_address }}</p>
        </div>
        <div class="address-card">
            <h4>Billing Address</h4>
            <p class="name">{{ $order->billing_name }}</p>
            <p>{{ $order->billing_phone }}</p>
            <p>{{ $order->billing_thana }}, {{ $order->billing_district }}</p>
            <p>{{ $order->billing_address }}</p>
            <p>{{ $order->billing_country }}</p>
        </div>
    </div>

    {{-- Items --}}
    <div class="items-section">
        <h3>Items Ordered</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th class="center">Qty</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $i => $item)
                <tr>
                    <td style="color:#9ca3af;">{{ $i + 1 }}</td>
                    <td><span class="product-name">{{ $item->product_name }}</span></td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td>&#2547;{{ number_format($item->price, 2) }}</td>
                    <td class="right">&#2547;{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Totals --}}
    <div class="totals" style="margin-top:0;">
        <table>
            <tr>
                <td style="color:#6b7280; padding:7px 14px;">Items Subtotal</td>
                <td style="text-align:right; font-weight:600; padding:7px 14px;">&#2547;{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td style="color:#6b7280; padding:7px 14px;">Delivery Charge</td>
                <td style="text-align:right; font-weight:600; padding:7px 14px;">&#2547;{{ number_format($order->delivery_cost, 2) }}</td>
            </tr>
            @if($order->coupon_discount > 0)
            <tr class="discount-row">
                <td style="color:#16a34a; padding:7px 14px;">Coupon Discount @if($order->coupon_code)({{ $order->coupon_code }})@endif</td>
                <td style="text-align:right; font-weight:600; color:#16a34a; padding:7px 14px;">-&#2547;{{ number_format($order->coupon_discount, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td style="padding:10px 14px; font-size:14px; font-weight:800; color:#ea580c;">Total</td>
                <td style="text-align:right; padding:10px 14px; font-size:14px; font-weight:800; color:#ea580c;">&#2547;{{ number_format($order->total, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($order->notes)
    <div style="margin-top:24px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:12px 16px;">
        <div style="font-size:9px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:5px;">Special Notes</div>
        <p style="font-size:11px; color:#374151; line-height:1.6;">{{ $order->notes }}</p>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p>Thank you for shopping with <strong>Digital Support</strong>!</p>
        <p>This is a computer-generated invoice. No signature is required.</p>
        <p>For support, please contact us with your order number: <strong>{{ $order->order_number }}</strong></p>
    </div>

</div>
</body>
</html>
