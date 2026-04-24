<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $orders->count() > 1 ? $orders->count() . ' courier labels' : 'Label #' . ($orders->first()?->order_number ?? '') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        /* 2×3 inch thermal label = 192 × 288 px at 96dpi — compact layout */
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family: Arial, Helvetica, sans-serif; color:#000; background:#e5e7eb; padding:16px; }

        .sheet {
            width: 2in; height: 3in;
            background:#fff; color:#000;
            margin: 0 auto;
            border:1px solid #000;
            display:flex; flex-direction:column;
            padding:5px 6px;
            font-size:8px; line-height:1.2;
            font-family: Arial, sans-serif;
        }

        /* Top: merchant logo + code */
        .top {
            text-align:center;
            border-bottom:1px dashed #000;
            padding-bottom:3px;
        }
        .top .logo {
            display:inline-block; width:26px; height:14px;
            background:#9ca3af; color:#fff;
            border-radius:1px 40% 1px 1px;
            position:relative;
            margin-bottom:1px;
        }
        .top .logo::after {
            content:''; position:absolute; right:-4px; top:-4px;
            width:10px; height:10px; border-radius:50%;
            background:#fff; border:1px solid #9ca3af;
        }
        .top .merchant-code { font-size:10px; font-weight:800; letter-spacing:0.02em; line-height:1.1; }
        .top .merchant-id { font-size:7px; line-height:1.1; }

        /* Main barcode */
        .barcode { text-align:center; padding:2px 0 1px; }
        .barcode svg { max-width:100%; height:24px; }

        /* QR + info */
        .info-row {
            display:grid; grid-template-columns: 44px 1fr;
            gap:5px;
            border-bottom:1px dashed #000;
            padding:3px 1px;
        }
        .info-row .qr { display:flex; align-items:center; justify-content:center; }
        .info-row .qr > div { width:40px; height:40px; }
        .info-row .info-list { font-size:7.5px; line-height:1.35; }
        .info-row .info-list .k { font-weight:700; }

        /* Receiver block */
        .party {
            border-bottom:1px dashed #000;
            padding:3px 1px;
            font-size:8px;
            line-height:1.35;
        }
        .party .label {
            font-weight:700;
            display:inline-block; min-width:38px;
        }
        .party .addr { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }

        /* COD / PAID strip */
        .cod {
            display:flex; justify-content:space-between; align-items:center;
            border:1px solid #000;
            padding:2px 8px;
            margin-top:3px;
            font-size:11px; font-weight:800;
        }
        .cod .amount { font-size:12px; }

        .footer {
            display:flex; justify-content:space-between; align-items:flex-end;
            margin-top:auto;
            padding-top:2px;
            font-size:6.5px;
            line-height:1.2;
        }
        .footer .courier {
            text-align:right;
            font-weight:800;
            font-size:7.5px;
            display:flex; align-items:center; gap:2px;
        }
        .footer .courier-logo {
            width:9px; height:9px; background:#10b981; border-radius:1px;
            display:inline-flex; align-items:center; justify-content:center;
            color:#fff; font-size:7px; font-weight:900;
        }
        .footer .url { font-weight:400; font-size:6px; color:#374151; }

        /* Toolbar (screen only) */
        .toolbar {
            max-width:2in; margin:0 auto 14px; display:flex; gap:6px; justify-content:center;
        }
        .toolbar button {
            padding:6px 10px; background:#111827; color:#fff; border:none; border-radius:4px;
            font-size:11px; font-weight:600; cursor:pointer;
        }
        .toolbar button.secondary { background:#6b7280; }

        @media print {
            body { background:#fff; padding:0; }
            .sheet { border:none; margin:0; }
            .toolbar { display:none; }
            @page { size: 2in 3in; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button onclick="window.print()">🖨 Print Label</button>
        <button class="secondary" onclick="window.close()">Close</button>
    </div>

    @foreach($orders as $order)
        @php
            $isCod = $order->payment_method === 'cod' || $order->payment_status !== 'paid';
            $amount = (float) $order->total;
        @endphp
        <div class="sheet" style="{{ !$loop->last ? 'page-break-after:always;' : '' }}">
            {{-- Merchant header --}}
            <div class="top">
                <div class="logo"></div>
                <div class="merchant-code">{{ $merchantCode }}</div>
                <div class="merchant-id">ID: {{ $merchantId }}</div>
            </div>

            {{-- Main barcode --}}
            <div class="barcode">
                <svg class="js-barcode" data-value="{{ $order->order_number }}"></svg>
            </div>

            {{-- QR + info --}}
            <div class="info-row">
                <div class="qr"><div class="js-qrcode" data-value="{{ $order->order_number }}"></div></div>
                <div class="info-list">
                    <div><span class="k">Invoice</span> : {{ $order->order_number }}</div>
                    <div><span class="k">{{ $order->courier_service ? ucwords(str_replace('_',' ',$order->courier_service)) . ' ID' : 'Tracking' }}</span> : {{ $order->tracking_number ?: '—' }}</div>
                    <div><span class="k">D. Type</span> : Home</div>
                    <div><span class="k">WGT</span> : {{ $order->items->count() }} item(s)</div>
                </div>
            </div>

            {{-- Receiver --}}
            <div class="party">
                <div><span class="label">Name:</span> {{ $order->shipping_name ?? '—' }}</div>
                <div><span class="label">Phone:</span> {{ $order->shipping_phone ?? '—' }}</div>
                <div class="addr"><span class="label">Address:</span> {{ trim(($order->shipping_address ?? '') . ', ' . ($order->shipping_thana ?? '') . ', ' . ($order->shipping_district ?? ''), ', ') }}</div>
            </div>

            {{-- COD / total --}}
            <div class="cod">
                <span>{{ $isCod ? 'COD' : 'PAID' }}</span>
                <span class="amount">৳{{ number_format($amount, 0) }}</span>
            </div>

            {{-- Footer --}}
            <div class="footer">
                <div>
                    <div>P: {{ $order->created_at->format('d/m/y') }}</div>
                    <div>{{ $order->created_at->format('h:ia') }}</div>
                </div>
                <div>
                    <div class="courier">
                        <span class="courier-logo">✓</span>
                        <span>{{ $order->courier_service ? ucwords(str_replace('_',' ',$order->courier_service)) : 'Courier' }}</span>
                    </div>
                    <div class="url">Ship from: {{ $siteName }}</div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        // Render every barcode + QR code on the page
        document.querySelectorAll('.js-barcode').forEach(function (el) {
            JsBarcode(el, el.dataset.value, {
                format: 'CODE128',
                displayValue: false,
                height: 24,
                margin: 0,
                width: 1
            });
        });
        document.querySelectorAll('.js-qrcode').forEach(function (el) {
            new QRCode(el, {
                text: el.dataset.value,
                width: 40, height: 40,
                correctLevel: QRCode.CorrectLevel.L,
            });
        });
    </script>
</body>
</html>
