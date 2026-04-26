@extends('layouts.app')
@section('title', 'Shopping Cart - Digital Support')
@php app(\App\Services\SeoService::class)->noindex(); @endphp

@push('scripts')
<script>
    // Analytics: fire view_cart on page load
    window.dsTrack && window.dsTrack('view_cart', {
        value: {{ (float) (\App\Services\CartService::total() ?? 0) }},
        items: @json(collect(\App\Services\CartService::get())->map(fn ($i) => [
            'id'    => $i['id'] ?? null,
            'name'  => $i['name'] ?? null,
            'price' => (float) ($i['price'] ?? 0),
            'qty'   => (int) ($i['qty'] ?? 1),
        ])->values()),
    });
</script>
@endpush

@section('content')
@include('components.breadcrumb', ['items' => [['label' => 'Cart']]])

<div class="container-custom px-4 sm:px-6 lg:px-8 py-10">

    @if(empty($items))
    {{-- Empty cart --}}
    <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:80px 20px; text-align:center;">
        <svg style="width:100px; height:100px; color:#e5e7eb; margin-bottom:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
        <h2 style="font-size:1.5rem; font-weight:700; color:#111827; margin-bottom:10px;">Your cart is empty</h2>
        <p style="color:#9ca3af; font-size:0.925rem; margin-bottom:28px;">Looks like you haven't added anything to your cart yet.</p>
        <a href="{{ route('products.index') }}" style="display:inline-flex; align-items:center; gap:8px; padding:14px 32px; background:#f97316; color:#fff; border-radius:8px; font-weight:700; font-size:0.9rem; text-decoration:none; letter-spacing:0.04em; transition:background 0.2s;" onmouseover="this.style.background='#ea6c0a'" onmouseout="this.style.background='#f97316'">
            <svg style="width:18px; height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Continue Shopping
        </a>
    </div>

    @else
    <div style="display:grid; grid-template-columns:1fr 340px; gap:28px; align-items:start;" id="cart-page-grid">

        {{-- ── Left: Items Table ── --}}
        <div>
            {{-- Header row --}}
            <div style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr 40px; gap:12px; padding:12px 20px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px 10px 0 0; font-size:0.75rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.06em;">
                <span>Product</span>
                <span style="text-align:center;">Price</span>
                <span style="text-align:center;">Quantity</span>
                <span style="text-align:right;">Subtotal</span>
                <span></span>
            </div>

            {{-- Items --}}
            <div id="cart-page-items" style="border:1px solid #e5e7eb; border-top:none; border-radius:0 0 10px 10px; overflow:hidden;">
                @foreach($items as $key => $item)
                <div class="cart-page-row" data-key="{{ $key }}" style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr 40px; gap:12px; padding:16px 20px; border-bottom:1px solid #f3f4f6; align-items:center; transition:background 0.15s;">
                    {{-- Product --}}
                    <div style="display:flex; align-items:center; gap:14px;">
                        @if($item['image'])
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="width:72px; height:72px; object-fit:cover; border-radius:8px; border:1px solid #f0f0f0; flex-shrink:0;">
                        @else
                            <span style="width:72px; height:72px; background:#f3f4f6; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <svg style="width:28px; height:28px; color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </span>
                        @endif
                        <div style="min-width:0;">
                            <a href="{{ route('products.show', ['product' => $item['slug']]) }}" style="font-size:0.9rem; font-weight:600; color:#111827; text-decoration:none; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; line-height:1.4; transition:color 0.2s;" onmouseover="this.style.color='#f97316'" onmouseout="this.style.color='#111827'">{{ $item['name'] }}</a>
                        </div>
                    </div>

                    {{-- Price --}}
                    <div style="text-align:center;">
                        <span style="font-size:0.9rem; font-weight:600; color:#374151;">{{ number_format($item['price'], 2) }}৳</span>
                    </div>

                    {{-- Quantity --}}
                    <div style="display:flex; justify-content:center;">
                        <div style="display:inline-flex; align-items:center; border:1.5px solid #e5e7eb; border-radius:8px; overflow:hidden;">
                            <button onclick="pageCartUpdate('{{ $key }}', {{ $item['qty'] - 1 }})" style="width:32px; height:36px; background:none; border:none; cursor:pointer; font-size:1.1rem; color:#374151; display:flex; align-items:center; justify-content:center; transition:background 0.15s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='none'">−</button>
                            <input type="number" value="{{ $item['qty'] }}" min="1" data-key="{{ $key }}"
                                style="width:44px; height:36px; border:none; border-left:1px solid #e5e7eb; border-right:1px solid #e5e7eb; text-align:center; font-size:0.875rem; font-weight:600; color:#111827; outline:none;"
                                onchange="pageCartUpdate('{{ $key }}', parseInt(this.value) || 1)">
                            <button onclick="pageCartUpdate('{{ $key }}', {{ $item['qty'] + 1 }})" style="width:32px; height:36px; background:none; border:none; cursor:pointer; font-size:1.1rem; color:#374151; display:flex; align-items:center; justify-content:center; transition:background 0.15s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='none'">+</button>
                        </div>
                    </div>

                    {{-- Subtotal --}}
                    <div style="text-align:right;">
                        <span class="row-subtotal" style="font-size:0.925rem; font-weight:700; color:#f97316;">{{ number_format($item['price'] * $item['qty'], 2) }}৳</span>
                    </div>

                    {{-- Remove --}}
                    <div style="display:flex; justify-content:center;">
                        <button onclick="pageCartRemove('{{ $key }}')" style="width:32px; height:32px; background:none; border:1px solid #e5e7eb; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#9ca3af; transition:all 0.2s;" onmouseover="this.style.borderColor='#ef4444'; this.style.color='#ef4444'; this.style.background='#fef2f2'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='#9ca3af'; this.style.background='none'">
                            <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Footer actions --}}
            <div style="display:flex; align-items:center; justify-content:space-between; margin-top:16px; flex-wrap:wrap; gap:12px;">
                <a href="{{ route('products.index') }}" style="display:inline-flex; align-items:center; gap:6px; padding:10px 20px; border:1.5px solid #e5e7eb; border-radius:8px; font-size:0.85rem; font-weight:600; color:#374151; text-decoration:none; transition:all 0.2s;" onmouseover="this.style.borderColor='#374151'" onmouseout="this.style.borderColor='#e5e7eb'">
                    <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/></svg>
                    Continue Shopping
                </a>
                <button onclick="pageCartClear()" style="display:inline-flex; align-items:center; gap:6px; padding:10px 20px; border:1.5px solid #e5e7eb; border-radius:8px; font-size:0.85rem; font-weight:600; color:#ef4444; background:none; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.borderColor='#ef4444'; this.style.background='#fef2f2'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='none'">
                    <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Clear Cart
                </button>
            </div>
        </div>

        {{-- ── Right: Order Summary ── --}}
        <div id="cart-summary" style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:24px; position:sticky; top:130px;">
            <h3 style="font-size:1rem; font-weight:700; color:#111827; margin-bottom:20px; padding-bottom:14px; border-bottom:1px solid #f3f4f6;">Order Summary</h3>

            <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:20px;">
                <div style="display:flex; justify-content:space-between; font-size:0.875rem; color:#6b7280;">
                    <span>Subtotal (<span id="page-item-count">{{ array_sum(array_column($items, 'qty')) }}</span> items)</span>
                    <span id="page-subtotal" style="font-weight:600; color:#111827;">{{ number_format($total, 2) }}৳</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.875rem; color:#6b7280;">
                    <span>Shipping</span>
                    <span style="color:#16a34a; font-weight:500;">Calculated at checkout</span>
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 0; border-top:1px solid #e5e7eb; border-bottom:1px solid #e5e7eb; margin-bottom:20px;">
                <span style="font-size:1rem; font-weight:700; color:#111827;">Total</span>
                <span id="page-total" style="font-size:1.25rem; font-weight:800; color:#f97316;">{{ number_format($total, 2) }}৳</span>
            </div>

            <a href="{{ route('contact.index') }}" style="display:block; text-align:center; padding:15px; background:#f97316; color:#fff; font-size:0.9rem; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; text-decoration:none; border-radius:8px; transition:background 0.2s; margin-bottom:12px;" onmouseover="this.style.background='#ea6c0a'" onmouseout="this.style.background='#f97316'">
                Proceed to Checkout
            </a>
            <a href="{{ route('products.index') }}" style="display:block; text-align:center; padding:12px; background:#0d1f2d; color:#fff; font-size:0.85rem; font-weight:600; text-decoration:none; border-radius:8px; transition:background 0.2s;" onmouseover="this.style.background='#1a3548'" onmouseout="this.style.background='#0d1f2d'">
                Continue Shopping
            </a>

            {{-- Trust badges --}}
            <div style="margin-top:20px; padding-top:16px; border-top:1px solid #f3f4f6; display:flex; flex-direction:column; gap:8px;">
                <div style="display:flex; align-items:center; gap:8px; font-size:0.78rem; color:#6b7280;">
                    <svg style="width:16px; height:16px; color:#16a34a; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Secure checkout
                </div>
                <div style="display:flex; align-items:center; gap:8px; font-size:0.78rem; color:#6b7280;">
                    <svg style="width:16px; height:16px; color:#16a34a; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Multiple payment options
                </div>
                <div style="display:flex; align-items:center; gap:8px; font-size:0.78rem; color:#6b7280;">
                    <svg style="width:16px; height:16px; color:#16a34a; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                    Easy returns
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 900px) {
            #cart-page-grid { grid-template-columns: 1fr !important; }
            #cart-summary { position: static !important; }
            .cart-page-row { grid-template-columns: 1fr 1fr !important; row-gap: 10px; }
            .cart-page-row > div:nth-child(1) { grid-column: 1 / -1; }
        }
        @media (max-width: 640px) {
            .cart-page-row { grid-template-columns: 1fr !important; }
            .cart-page-row > div { text-align: left !important; justify-content: flex-start !important; }
        }
    </style>

    <script>
    var CART_UPDATE_URL = '{{ url("cart/update") }}';
    var CART_REMOVE_URL = '{{ url("cart/remove") }}';
    var CART_CLEAR_URL  = '{{ url("cart/clear") }}';
    var CSRF            = document.querySelector('meta[name="csrf-token"]').content;

    function pageApi(url, method, body) {
        return fetch(url, {
            method: method,
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: body ? JSON.stringify(body) : undefined,
        }).then(function(r){ return r.json(); });
    }

    function pageCartUpdate(key, qty) {
        if (qty < 1) { pageCartRemove(key); return; }
        pageApi(CART_UPDATE_URL + '/' + key, 'PATCH', { qty: qty })
            .then(function(data) {
                // Update the input value and subtotals without full reload
                var row = document.querySelector('.cart-page-row[data-key="' + key + '"]');
                if (!row) { location.reload(); return; }
                // Update qty input
                var inp = row.querySelector('input[type=number]');
                if (inp) inp.value = qty;
                // Update −/+ onclick values
                var btns = row.querySelectorAll('button');
                if (btns[0]) btns[0].setAttribute('onclick', "pageCartUpdate('" + key + "'," + (qty - 1) + ")");
                if (btns[1]) btns[1].setAttribute('onclick', "pageCartUpdate('" + key + "'," + (qty + 1) + ")");
                // Update row subtotal (find price from data)
                var priceEl = row.querySelector('[data-price]');
                // Recalculate from items
                refreshPageSummary(data);
            });
    }

    function pageCartRemove(key) {
        pageApi(CART_REMOVE_URL + '/' + key, 'DELETE')
            .then(function(data) {
                var row = document.querySelector('.cart-page-row[data-key="' + key + '"]');
                if (row) {
                    row.style.opacity = '0';
                    row.style.transition = 'opacity 0.3s';
                    setTimeout(function() {
                        row.remove();
                        refreshPageSummary(data);
                        if (data.items.length === 0) location.reload();
                    }, 300);
                }
            });
    }

    function pageCartClear() {
        pageApi(CART_CLEAR_URL, 'POST')
            .then(function() { location.reload(); });
    }

    function refreshPageSummary(data) {
        var count  = data.itemCount || 0;
        var total  = data.total || '0.00';
        // Update summary panel
        var ic = document.getElementById('page-item-count');
        var st = document.getElementById('page-subtotal');
        var pt = document.getElementById('page-total');
        if (ic) ic.textContent = count;
        if (st) st.textContent = total + '৳';
        if (pt) pt.textContent = total + '৳';
        // Also update floating cart button & header badge via global renderCart
        if (typeof renderCart === 'function') renderCart(data);
        else {
            // Fallback: update badge manually
            var hdrBadge = document.getElementById('cart-header-badge');
            if (hdrBadge) { hdrBadge.textContent = count; hdrBadge.style.display = count > 0 ? 'flex' : 'none'; }
            var fl = document.getElementById('cart-float-label');
            var ft = document.getElementById('cart-float-total');
            if (fl) fl.textContent = count + (count === 1 ? ' Item' : ' Items');
            if (ft) ft.textContent = total + '৳';
        }
        // Reload row subtotals
        if (data.items && data.items.length) {
            data.items.forEach(function(item) {
                var row = document.querySelector('.cart-page-row[data-key="' + item.id + '"]');
                if (!row) return;
                var sub = row.querySelector('.row-subtotal');
                if (sub) sub.textContent = (parseFloat(item.price) * item.qty).toFixed(2) + '৳';
                var inp = row.querySelector('input[type=number]');
                if (inp) inp.value = item.qty;
                var btns = row.querySelectorAll('button');
                if (btns[0]) btns[0].setAttribute('onclick', "pageCartUpdate('" + item.id + "'," + (item.qty - 1) + ")");
                if (btns[1]) btns[1].setAttribute('onclick', "pageCartUpdate('" + item.id + "'," + (item.qty + 1) + ")");
            });
        }
    }
    </script>
    @endif

</div>
@endsection
