@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)
@php app(\App\Services\SeoService::class)->noindex(); @endphp

@php
    // Status flow for the visual progress bar
    $flow = ['pending', 'processing', 'shipped', 'delivered'];
    $isCancelled = $order->status === 'cancelled';
    $isRefunded  = $order->status === 'refunded';
    $currentIdx  = array_search($order->status, $flow);
    $currentIdx  = $currentIdx === false ? 0 : $currentIdx;

    $statusColors = [
        'pending'    => ['bg' => '#fef3c7', 'fg' => '#92400e', 'dot' => '#f59e0b'],
        'processing' => ['bg' => '#dbeafe', 'fg' => '#1e40af', 'dot' => '#3b82f6'],
        'shipped'    => ['bg' => '#ede9fe', 'fg' => '#6d28d9', 'dot' => '#8b5cf6'],
        'delivered'  => ['bg' => '#dcfce7', 'fg' => '#166534', 'dot' => '#16a34a'],
        'cancelled'  => ['bg' => '#fee2e2', 'fg' => '#991b1b', 'dot' => '#ef4444'],
        'refunded'   => ['bg' => '#f3f4f6', 'fg' => '#374151', 'dot' => '#6b7280'],
    ];
    $sc = $statusColors[$order->status] ?? $statusColors['pending'];
    $canCancel = in_array($order->status, ['pending', 'processing']) && !$order->shipped_at;
@endphp

@section('content')
<div style="background:#f8fafc; min-height:60vh;">
<div class="container-custom" style="padding:36px 16px 64px; max-width:980px;">

    {{-- Breadcrumbs / back --}}
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:18px; font-size:0.85rem;">
        <a href="{{ route('account.index') }}" style="color:#64748b; text-decoration:none;" onmouseover="this.style.color='#0f172a'" onmouseout="this.style.color='#64748b'">Account</a>
        <span style="color:#cbd5e1;">/</span>
        <a href="{{ route('account.orders') }}" style="color:#64748b; text-decoration:none;" onmouseover="this.style.color='#0f172a'" onmouseout="this.style.color='#64748b'">Orders</a>
        <span style="color:#cbd5e1;">/</span>
        <span style="color:#0f172a; font-weight:600;">{{ $order->order_number }}</span>
    </div>

    @if(session('success'))
        <div role="status" aria-live="polite" style="margin-bottom:18px; padding:12px 16px; background:#ecfdf5; border:1px solid #bbf7d0; color:#166534; border-radius:10px; font-size:0.9rem;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div role="alert" aria-live="assertive" style="margin-bottom:18px; padding:12px 16px; background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:10px; font-size:0.9rem;">{{ $errors->first() }}</div>
    @endif

    {{-- Header card --}}
    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:24px 28px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap;">
        <div>
            <p style="margin:0 0 6px; font-size:0.7rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.1em;">Order</p>
            <h1 style="margin:0; font-size:1.6rem; font-weight:800; color:#0f172a; letter-spacing:-0.02em;">{{ $order->order_number }}</h1>
            <p style="margin:6px 0 0; font-size:0.85rem; color:#64748b;">Placed {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div style="text-align:right;">
            <span style="display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:999px; font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }};">
                <span style="display:inline-block; width:7px; height:7px; border-radius:50%; background:{{ $sc['dot'] }};"></span>
                {{ ucfirst($order->status) }}
            </span>
            <p style="margin:6px 0 0; font-size:1.6rem; font-weight:800; color:#0f172a; letter-spacing:-0.02em;">@bdt($order->total)</p>
        </div>
    </div>

    {{-- Progress timeline (hidden when cancelled/refunded — show banner instead) --}}
    @if($isCancelled || $isRefunded)
        <div style="background:#fff; border:1px solid #fecaca; border-radius:16px; padding:20px 24px; margin-bottom:20px; display:flex; align-items:center; gap:14px;">
            <div style="flex-shrink:0; width:42px; height:42px; border-radius:50%; background:#fef2f2; display:flex; align-items:center; justify-content:center;">
                <svg style="width:20px; height:20px; color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p style="margin:0; font-weight:700; color:#0f172a;">Order {{ $isCancelled ? 'cancelled' : 'refunded' }}</p>
                <p style="margin:2px 0 0; font-size:0.85rem; color:#64748b;">
                    {{ $isCancelled ? ($order->cancelled_at?->format('M d, Y h:i A') ?? '—') : ($order->refunded_at?->format('M d, Y h:i A') ?? '—') }}
                </p>
            </div>
        </div>
    @else
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:24px 28px; margin-bottom:20px;">
            <p style="margin:0 0 18px; font-size:0.7rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.1em;">Progress</p>
            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:8px; position:relative;">
                @foreach($flow as $idx => $step)
                    @php
                        $done    = $idx < $currentIdx;
                        $current = $idx === $currentIdx;
                        $bgColor = $done || $current ? '#16a34a' : '#e2e8f0';
                        $txtColor = $done || $current ? '#0f172a' : '#94a3b8';
                    @endphp
                    <div style="flex:1; display:flex; flex-direction:column; align-items:center; position:relative; z-index:2;">
                        <div style="width:36px; height:36px; border-radius:50%; background:{{ $bgColor }}; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:0.85rem; box-shadow:{{ $current ? '0 0 0 5px rgba(22,163,74,0.18)' : 'none' }};">
                            @if($done)
                                <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @else
                                {{ $idx + 1 }}
                            @endif
                        </div>
                        <p style="margin:8px 0 0; font-size:0.78rem; font-weight:600; color:{{ $txtColor }}; text-transform:capitalize;">{{ $step }}</p>
                        @if($idx === 2 && $order->shipped_at)
                            <p style="margin:2px 0 0; font-size:0.7rem; color:#64748b;">{{ $order->shipped_at->format('M d') }}</p>
                        @elseif($idx === 3 && $order->delivered_at)
                            <p style="margin:2px 0 0; font-size:0.7rem; color:#64748b;">{{ $order->delivered_at->format('M d') }}</p>
                        @elseif($idx === 0)
                            <p style="margin:2px 0 0; font-size:0.7rem; color:#64748b;">{{ $order->created_at->format('M d') }}</p>
                        @endif
                    </div>
                @endforeach
                {{-- connector line --}}
                <div style="position:absolute; top:18px; left:8%; right:8%; height:2px; background:linear-gradient(to right, #16a34a 0%, #16a34a {{ ($currentIdx / max(count($flow) - 1, 1)) * 100 }}%, #e2e8f0 {{ ($currentIdx / max(count($flow) - 1, 1)) * 100 }}%, #e2e8f0 100%); z-index:1;"></div>
            </div>
        </div>
    @endif

    {{-- Tracking card --}}
    @if($order->tracking_number)
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:20px 24px; margin-bottom:20px; display:flex; align-items:center; gap:16px;">
            <div style="flex-shrink:0; width:42px; height:42px; border-radius:10px; background:#fff7ed; border:1px solid #fed7aa; display:flex; align-items:center; justify-content:center;">
                <svg style="width:20px; height:20px; color:#f97316;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 011 1h2.05a2.5 2.5 0 014.9 0H20a1 1 0 001-1v-5a1 1 0 00-1-1h-3.5l-2-3H13v9z"/></svg>
            </div>
            <div style="flex:1; min-width:0;">
                <p style="margin:0; font-size:0.7rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.1em;">Tracking</p>
                <p style="margin:4px 0 0; font-weight:700; color:#0f172a; font-family:monospace;">{{ $order->tracking_number }}</p>
                @if($order->courier_service)
                    <p style="margin:2px 0 0; font-size:0.85rem; color:#64748b;">via {{ $order->courier_service }}</p>
                @endif
            </div>
            <button onclick="navigator.clipboard.writeText('{{ $order->tracking_number }}'); this.textContent='Copied ✓'; setTimeout(() => this.textContent='Copy', 1800);" style="padding:8px 14px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; font-size:0.82rem; font-weight:600; color:#475569; cursor:pointer; transition:all 0.15s;" onmouseover="this.style.background='#0f172a'; this.style.color='#fff';" onmouseout="this.style.background='#f8fafc'; this.style.color='#475569';">Copy</button>
        </div>
    @endif

    {{-- Two-column: items + summary --}}
    <div style="display:grid; grid-template-columns:1.4fr 1fr; gap:20px; margin-bottom:20px;" class="acc-order-grid">

        {{-- Items --}}
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden;">
            <div style="padding:18px 24px; border-bottom:1px solid #f1f5f9;">
                <p style="margin:0; font-size:0.7rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.1em;">Items ({{ $order->items->count() }})</p>
            </div>
            @foreach($order->items as $item)
                <div style="display:flex; gap:14px; padding:16px 24px; border-bottom:1px solid #f1f5f9; align-items:center;">
                    @if($item->product_image)
                        <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" loading="lazy" decoding="async" width="64" height="64" style="width:64px; height:64px; object-fit:cover; border-radius:10px; background:#f8fafc; flex-shrink:0;">
                    @else
                        <div style="width:64px; height:64px; border-radius:10px; background:#f1f5f9; flex-shrink:0;"></div>
                    @endif
                    <div style="flex:1; min-width:0;">
                        <p style="margin:0; font-weight:600; color:#0f172a; line-height:1.4; font-size:0.92rem; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">{{ $item->product_name }}</p>
                        <p style="margin:4px 0 0; font-size:0.78rem; color:#64748b;">@bdt($item->price) × {{ $item->quantity }}</p>
                    </div>
                    <p style="margin:0; font-weight:700; color:#0f172a; flex-shrink:0; letter-spacing:-0.01em;">@bdt($item->subtotal)</p>
                </div>
            @endforeach
        </div>

        {{-- Summary + actions --}}
        <div style="display:flex; flex-direction:column; gap:20px;">
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:20px 24px;">
                <p style="margin:0 0 14px; font-size:0.7rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.1em;">Summary</p>
                <div style="display:flex; flex-direction:column; gap:10px; font-size:0.9rem;">
                    <div style="display:flex; justify-content:space-between;"><span style="color:#64748b;">Subtotal</span><span style="font-weight:600; color:#0f172a;">@bdt($order->subtotal)</span></div>
                    <div style="display:flex; justify-content:space-between;"><span style="color:#64748b;">Delivery</span><span style="font-weight:600; color:#0f172a;">@bdt($order->delivery_cost)</span></div>
                    @if($order->coupon_discount > 0)
                        <div style="display:flex; justify-content:space-between; color:#16a34a;"><span>Coupon ({{ $order->coupon_code }})</span><span style="font-weight:600;">−@bdt($order->coupon_discount)</span></div>
                    @endif
                    @if($order->refund_amount > 0)
                        <div style="display:flex; justify-content:space-between; color:#9333ea;"><span>Refunded</span><span style="font-weight:600;">−@bdt($order->refund_amount)</span></div>
                    @endif
                    <div style="display:flex; justify-content:space-between; padding-top:10px; border-top:1px solid #f1f5f9; align-items:baseline;">
                        <span style="font-weight:700; color:#0f172a;">Total</span>
                        <span style="font-size:1.2rem; font-weight:800; color:#0f172a; letter-spacing:-0.02em;">@bdt($order->total)</span>
                    </div>
                </div>
                <div style="margin-top:14px; padding-top:14px; border-top:1px solid #f1f5f9; font-size:0.82rem;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="color:#64748b;">Payment</span>
                        <span style="font-weight:600; color:#0f172a;">{{ $order->payment_method_label ?? ucfirst($order->payment_method) }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:6px;">
                        <span style="color:#64748b;">Status</span>
                        <span style="font-weight:600; color:{{ $order->payment_status === 'paid' ? '#16a34a' : '#475569' }}; text-transform:capitalize;">{{ $order->payment_status }}</span>
                    </div>
                </div>
            </div>

            {{-- Shipping address --}}
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:20px 24px;">
                <p style="margin:0 0 12px; font-size:0.7rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.1em;">Shipping to</p>
                <p style="margin:0; font-weight:700; color:#0f172a;">{{ $order->shipping_name }}</p>
                <p style="margin:4px 0 0; font-size:0.88rem; color:#475569; line-height:1.55;">
                    {{ $order->shipping_phone }}<br>
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_thana }}, {{ $order->shipping_district }}
                </p>
            </div>

            {{-- Actions --}}
            <div style="display:flex; flex-direction:column; gap:8px;">
                @if(\Illuminate\Support\Facades\Route::has('checkout.invoice'))
                    <a href="{{ route('checkout.invoice', $order->order_number) }}" target="_blank" rel="noopener" style="display:flex; align-items:center; justify-content:center; gap:8px; padding:12px; background:#0f172a; color:#fff; font-size:0.85rem; font-weight:700; border-radius:10px; text-decoration:none; transition:background 0.2s;" onmouseover="this.style.background='#1e293b'" onmouseout="this.style.background='#0f172a'">
                        <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Download invoice
                    </a>
                @endif

                @if($canCancel)
                    <form method="POST" action="{{ route('account.orders.cancel', $order->order_number) }}" onsubmit="return confirm('Cancel this order? This cannot be undone.');" style="margin:0;">
                        @csrf
                        <button type="submit" style="width:100%; padding:12px; background:#fff; color:#dc2626; font-size:0.85rem; font-weight:700; border:1px solid #fecaca; border-radius:10px; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.background='#fef2f2'; this.style.borderColor='#fca5a5';" onmouseout="this.style.background='#fff'; this.style.borderColor='#fecaca';">
                            Cancel order
                        </button>
                    </form>
                @endif

                <a href="{{ url('/contact') }}" style="display:flex; align-items:center; justify-content:center; gap:8px; padding:12px; background:#fff; border:1px solid #e2e8f0; color:#475569; font-size:0.85rem; font-weight:600; border-radius:10px; text-decoration:none; transition:all 0.2s;" onmouseover="this.style.borderColor='#f97316'; this.style.color='#f97316';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#475569';">
                    Need help?
                </a>
            </div>
        </div>
    </div>

    {{-- Notes --}}
    @if($order->notes)
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:20px 24px; margin-bottom:20px;">
            <p style="margin:0 0 8px; font-size:0.7rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.1em;">Order note</p>
            <p style="margin:0; font-size:0.92rem; color:#374151; line-height:1.6;">{{ $order->notes }}</p>
        </div>
    @endif

    {{-- Activity log --}}
    @if($order->activities->count() > 0)
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:20px 24px;">
            <p style="margin:0 0 16px; font-size:0.7rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.1em;">Activity</p>
            <div style="display:flex; flex-direction:column; gap:14px;">
                @foreach($order->activities as $a)
                    <div style="display:flex; gap:12px; align-items:flex-start;">
                        <div style="flex-shrink:0; width:30px; height:30px; border-radius:50%; background:#f1f5f9; border:1px solid #e2e8f0; display:flex; align-items:center; justify-content:center;">
                            <svg style="width:13px; height:13px; color:#64748b;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div style="flex:1;">
                            <p style="margin:0; font-weight:600; color:#0f172a; font-size:0.9rem;">{{ $a->title }}</p>
                            @if($a->description)
                                <p style="margin:2px 0 0; font-size:0.82rem; color:#64748b; line-height:1.5;">{{ $a->description }}</p>
                            @endif
                            <p style="margin:4px 0 0; font-size:0.75rem; color:#94a3b8;">{{ $a->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
</div>

<style>
@media (max-width:780px) {
    .acc-order-grid { grid-template-columns:1fr !important; }
}
</style>
@endsection
