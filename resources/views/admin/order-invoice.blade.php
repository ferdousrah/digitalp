<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Inter, Helvetica, Arial, sans-serif; color:#111827; background:#f9fafb; padding:24px; }
        .sheet { max-width:820px; margin:0 auto; background:#fff; padding:36px 40px; box-shadow:0 1px 3px rgba(0,0,0,0.08); border-radius:8px; }
        .header { display:flex; justify-content:space-between; align-items:flex-start; border-bottom:2px solid #111827; padding-bottom:18px; margin-bottom:24px; }
        .brand h1 { font-size:1.35rem; letter-spacing:-0.01em; color:#111827; }
        .brand p { font-size:0.82rem; color:#6b7280; margin-top:2px; }
        .invoice-meta { text-align:right; }
        .invoice-meta .label { font-size:0.68rem; letter-spacing:0.06em; text-transform:uppercase; color:#6b7280; font-weight:600; }
        .invoice-meta .value { font-size:1.15rem; font-weight:700; color:#111827; margin-top:2px; }
        .invoice-meta .date { font-size:0.82rem; color:#6b7280; margin-top:6px; }

        .section-title { font-size:0.7rem; letter-spacing:0.08em; text-transform:uppercase; color:#6b7280; font-weight:700; margin-bottom:8px; }
        .two-col { display:grid; grid-template-columns:1fr 1fr; gap:24px; margin-bottom:28px; }
        .addr { font-size:0.88rem; line-height:1.5; color:#374151; }
        .addr strong { color:#111827; display:block; margin-bottom:2px; }

        table.items { width:100%; border-collapse:collapse; margin-bottom:16px; }
        table.items thead th { text-align:left; font-size:0.7rem; letter-spacing:0.06em; text-transform:uppercase; color:#6b7280; font-weight:700; padding:10px 8px; border-bottom:2px solid #111827; }
        table.items tbody td { font-size:0.9rem; padding:12px 8px; border-bottom:1px solid #f3f4f6; }
        table.items .num { text-align:right; font-variant-numeric:tabular-nums; }
        table.items .product-name { font-weight:600; color:#111827; }

        .totals { display:grid; grid-template-columns:1fr auto; gap:6px 60px; max-width:360px; margin-left:auto; font-size:0.9rem; }
        .totals .label { color:#6b7280; }
        .totals .value { text-align:right; font-variant-numeric:tabular-nums; font-weight:600; }
        .totals .grand { border-top:2px solid #111827; padding-top:10px; margin-top:8px; font-size:1.1rem; font-weight:700; color:#111827; }

        .notes { margin-top:28px; padding:14px 16px; background:#f9fafb; border-radius:6px; border-left:4px solid #6b7280; font-size:0.85rem; color:#4b5563; line-height:1.5; }

        .status-pill { display:inline-block; padding:3px 10px; border-radius:99px; font-size:0.72rem; font-weight:700; letter-spacing:0.03em; text-transform:uppercase; background:#dcfce7; color:#15803d; }
        .status-pill.pending { background:#fef3c7; color:#92400e; }
        .status-pill.failed  { background:#fee2e2; color:#991b1b; }

        .footer { margin-top:40px; padding-top:20px; border-top:1px solid #e5e7eb; text-align:center; color:#6b7280; font-size:0.78rem; }

        .toolbar { max-width:820px; margin:0 auto 18px; display:flex; justify-content:flex-end; gap:8px; }
        .toolbar button {
            padding:8px 16px; background:#111827; color:#fff; border:none; border-radius:6px;
            font-size:0.85rem; font-weight:600; cursor:pointer;
        }
        .toolbar button:hover { background:#1f2937; }

        @media print {
            body { background:#fff; padding:0; }
            .sheet { box-shadow:none; border-radius:0; padding:28px 32px; }
            .toolbar { display:none; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button onclick="window.print()">🖨 Print</button>
        <button onclick="window.close()" style="background:#6b7280;">Close</button>
    </div>

    <div class="sheet">
        <div class="header">
            <div class="brand">
                @if(!empty($logo))
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($logo) }}" alt="{{ $siteName }}" style="max-height:54px; margin-bottom:6px;">
                @endif
                <h1>{{ $siteName }}</h1>
                @if($address)<p>{{ $address }}</p>@endif
                @if($phone)<p>{{ $phone }}</p>@endif
                @if($email)<p>{{ $email }}</p>@endif
            </div>
            <div class="invoice-meta">
                <div class="label">Invoice</div>
                <div class="value">#{{ $order->order_number }}</div>
                <div class="date">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                <div style="margin-top:10px;">
                    <span class="status-pill {{ $order->payment_status }}">{{ strtoupper($order->payment_status) }}</span>
                </div>
            </div>
        </div>

        <div class="two-col">
            <div>
                <div class="section-title">Bill To</div>
                <div class="addr">
                    <strong>{{ $order->billing_name ?? $order->shipping_name }}</strong>
                    {{ $order->billing_phone ?? $order->shipping_phone }}<br>
                    @if($order->billing_address)
                        {{ $order->billing_address }}<br>
                        {{ $order->billing_thana }}{{ $order->billing_thana && $order->billing_district ? ', ' : '' }}{{ $order->billing_district }}
                    @endif
                </div>
            </div>
            <div>
                <div class="section-title">Ship To</div>
                <div class="addr">
                    <strong>{{ $order->shipping_name }}</strong>
                    {{ $order->shipping_phone }}<br>
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_thana }}{{ $order->shipping_thana && $order->shipping_district ? ', ' : '' }}{{ $order->shipping_district }}
                </div>
            </div>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th style="width:45%;">Item</th>
                    <th class="num">Price</th>
                    <th class="num">Qty</th>
                    <th class="num">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div class="product-name">{{ $item->product_name }}</div>
                        </td>
                        <td class="num">৳{{ number_format((float) $item->price, 2) }}</td>
                        <td class="num">{{ $item->quantity }}</td>
                        <td class="num">৳{{ number_format((float) $item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="label">Items Subtotal</div>
            <div class="value">৳{{ number_format((float) $order->subtotal, 2) }}</div>

            @if($order->coupon_discount && (float) $order->coupon_discount > 0)
                <div class="label">Discount ({{ $order->coupon_code }})</div>
                <div class="value" style="color:#dc2626;">− ৳{{ number_format((float) $order->coupon_discount, 2) }}</div>
            @endif

            <div class="label">Delivery</div>
            <div class="value">৳{{ number_format((float) $order->delivery_cost, 2) }}</div>

            <div class="label grand">Grand Total</div>
            <div class="value grand">৳{{ number_format((float) $order->total, 2) }}</div>

            @if($order->refund_amount && (float) $order->refund_amount > 0)
                <div class="label" style="color:#dc2626; font-weight:600;">Refunded</div>
                <div class="value" style="color:#dc2626;">− ৳{{ number_format((float) $order->refund_amount, 2) }}</div>
            @endif
        </div>

        @if($order->notes || $order->tracking_number)
            <div class="notes">
                @if($order->tracking_number)
                    <strong>Tracking:</strong> {{ $order->tracking_number }}
                    @if($order->courier_service) ({{ ucwords(str_replace('_', ' ', $order->courier_service)) }}) @endif
                    <br>
                @endif
                @if($order->notes)
                    <strong>Notes:</strong> {{ $order->notes }}
                @endif
            </div>
        @endif

        <div class="footer">
            Thank you for your business. This is a computer-generated invoice.
        </div>
    </div>
</body>
</html>
