@extends('layouts.app')
@section('title', 'Order ' . $order->order_number . ' — Tracking')

@section('content')
@include('components.breadcrumb', ['items' => [
    ['label' => sc('track', 'breadcrumb', 'Track Order'), 'url' => route('track-order.index')],
    ['label' => $order->order_number],
]])

@php
$steps = [
    'pending'    => ['label' => 'Order Placed',   'icon' => 'fi-rr-shopping-cart-check', 'desc' => 'Your order has been received'],
    'processing' => ['label' => 'Processing',      'icon' => 'fi-rr-settings',            'desc' => 'We are preparing your order'],
    'shipped'    => ['label' => 'Shipped',         'icon' => 'fi-rr-truck-side',          'desc' => 'Your order is on the way'],
    'delivered'  => ['label' => 'Delivered',       'icon' => 'fi-rr-box-check',           'desc' => 'Order delivered successfully'],
];
$stepKeys    = array_keys($steps);
$currentStep = array_search($order->status, $stepKeys);
if ($currentStep === false) $currentStep = 0;

$statusColors = [
    'pending'    => ['bg' => '#fff7ed', 'text' => '#c2410c', 'border' => '#fed7aa'],
    'processing' => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#bfdbfe'],
    'shipped'    => ['bg' => '#f5f3ff', 'text' => '#6d28d9', 'border' => '#ddd6fe'],
    'delivered'  => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0'],
    'cancelled'  => ['bg' => '#fef2f2', 'text' => '#dc2626', 'border' => '#fecaca'],
    'completed'  => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0'],
];
$sc = $statusColors[$order->status] ?? $statusColors['pending'];
@endphp

<div style="background:#f3f4f6; min-height:80vh; padding:32px 16px 60px;">
<div style="max-width:860px; margin:0 auto; display:flex; flex-direction:column; gap:24px;">

    {{-- Header card --}}
    <div style="background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,0.07); overflow:hidden;">
        <div style="background:linear-gradient(135deg,#f97316 0%,#ea580c 100%); padding:24px 28px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <div>
                <div style="font-size:0.75rem; color:rgba(255,255,255,0.8); font-weight:600; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:4px;">Order Number</div>
                <div style="font-size:1.4rem; font-weight:900; color:#fff; font-family:monospace; letter-spacing:0.04em;">{{ $order->order_number }}</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:0.75rem; color:rgba(255,255,255,0.8); font-weight:600; margin-bottom:4px;">Order Date</div>
                <div style="font-size:0.95rem; font-weight:700; color:#fff;">{{ $order->created_at->format('d M Y, h:i A') }}</div>
            </div>
        </div>

        <div style="padding:20px 28px; display:flex; align-items:center; gap:16px; flex-wrap:wrap; border-bottom:1px solid #f3f4f6;">
            <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 14px; border-radius:50px; background:{{ $sc['bg'] }}; border:1px solid {{ $sc['border'] }};">
                <span style="width:8px; height:8px; border-radius:50%; background:{{ $sc['text'] }};"></span>
                <span style="font-size:0.82rem; font-weight:700; color:{{ $sc['text'] }}; text-transform:uppercase; letter-spacing:0.06em;">{{ ucfirst($order->status) }}</span>
            </div>
            @if($order->payment_status === 'paid')
            <div style="display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:50px; background:#f0fdf4; border:1px solid #bbf7d0;">
                <i class="fi fi-rr-check-circle" style="color:#16a34a; font-size:14px; line-height:1;"></i>
                <span style="font-size:0.82rem; font-weight:700; color:#15803d;">Payment Confirmed</span>
            </div>
            @elseif($order->payment_status === 'pending')
            <div style="display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:50px; background:#fffbeb; border:1px solid #fde68a;">
                <i class="fi fi-rr-clock" style="color:#d97706; font-size:14px; line-height:1;"></i>
                <span style="font-size:0.82rem; font-weight:700; color:#92400e;">Payment Pending</span>
            </div>
            @endif
            <div style="margin-left:auto; font-size:1.1rem; font-weight:800; color:#111827;">@bdt($order->total)</div>
        </div>
    </div>

    {{-- Status Timeline --}}
    @if(!in_array($order->status, ['cancelled']))
    <div style="background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,0.07); padding:28px;">
        <h3 style="font-size:0.82rem; font-weight:800; color:#6b7280; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 28px;">Order Progress</h3>

        <div style="position:relative;">
            {{-- Progress line --}}
            <div style="position:absolute; top:24px; left:24px; right:24px; height:3px; background:#e5e7eb; z-index:0; border-radius:2px;">
                <div id="progress-bar" data-width="{{ $currentStep === 0 ? '0' : ($currentStep / (count($steps)-1) * 100) }}"
                    style="height:100%; border-radius:2px; background:linear-gradient(90deg,#f97316,#ea580c); width:0%; transition:width 1s cubic-bezier(0.4,0,0.2,1);"></div>
            </div>

            <div style="display:flex; justify-content:space-between; position:relative; z-index:1;">
                @foreach($steps as $key => $step)
                @php
                    $idx      = array_search($key, $stepKeys);
                    $done     = $idx <= $currentStep;
                    $active   = $idx === $currentStep;
                @endphp
                <div style="display:flex; flex-direction:column; align-items:center; gap:10px; flex:1;">
                    <div class="{{ $active ? 'step-active' : '' }}" style="width:48px; height:48px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;
                        background:{{ $done ? '#f97316' : '#fff' }};
                        border:3px solid {{ $done ? '#f97316' : '#e5e7eb' }};
                        box-shadow:{{ $active ? '0 0 0 5px rgba(249,115,22,0.2)' : 'none' }};
                        transition:all 0.3s ease;">
                        <i class="fi {{ $step['icon'] }}" style="font-size:18px; line-height:1; color:{{ $done ? '#fff' : '#9ca3af' }};"></i>
                    </div>
                    <div style="text-align:center;">
                        <div style="font-size:0.78rem; font-weight:{{ $active ? '800' : '600' }}; color:{{ $done ? '#111827' : '#9ca3af' }};">{{ $step['label'] }}</div>
                        <div style="font-size:0.7rem; color:{{ $done ? '#6b7280' : '#d1d5db' }}; margin-top:2px; display:none;" class="md-block">{{ $step['desc'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        @if($order->status === 'shipped')
        <div style="margin-top:20px; background:#f5f3ff; border:1px solid #ddd6fe; border-radius:8px; padding:12px 16px; font-size:0.84rem; color:#5b21b6; display:flex; align-items:center; gap:8px;">
            <i class="fi fi-rr-truck-side" style="font-size:18px; line-height:1;"></i>
            Your order is on its way! Expected delivery within 2-5 business days.
        </div>
        @elseif($order->status === 'delivered' || $order->status === 'completed')
        <div style="margin-top:20px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:12px 16px; font-size:0.84rem; color:#15803d; display:flex; align-items:center; gap:8px;">
            <i class="fi fi-rr-party-horn" style="font-size:18px; line-height:1;"></i>
            Your order has been delivered. Thank you for shopping with us!
        </div>
        @endif
    </div>
    @else
    <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:14px; padding:20px 24px; display:flex; align-items:center; gap:14px;">
        <i class="fi fi-rr-cross-circle" style="font-size:28px; color:#ef4444; flex-shrink:0; line-height:1;"></i>
        <div>
            <div style="font-size:0.95rem; font-weight:700; color:#dc2626;">Order Cancelled</div>
            <div style="font-size:0.84rem; color:#991b1b; margin-top:2px;">This order has been cancelled. Please contact us if you have any questions.</div>
        </div>
    </div>
    @endif

    {{-- Two column: Shipping + Payment --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;" class="track-grid">

        {{-- Shipping Address --}}
        <div style="background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,0.07); padding:24px;">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
                <div style="width:36px; height:36px; background:#fff7ed; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fi fi-rr-marker" style="font-size:16px; color:#f97316; line-height:1;"></i>
                </div>
                <h3 style="font-size:0.82rem; font-weight:800; color:#111827; text-transform:uppercase; letter-spacing:0.06em; margin:0;">Shipping Address</h3>
            </div>
            <div style="font-size:0.875rem; color:#374151; line-height:1.7;">
                <div style="font-weight:700; font-size:0.95rem; color:#111827;">{{ $order->shipping_name }}</div>
                <div>{{ $order->shipping_phone }}</div>
                <div>{{ $order->shipping_address }}</div>
                <div>{{ $order->shipping_thana }}, {{ $order->shipping_district }}</div>
            </div>
        </div>

        {{-- Payment Info --}}
        <div style="background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,0.07); padding:24px;">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
                <div style="width:36px; height:36px; background:#fff7ed; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fi fi-rr-credit-card" style="font-size:16px; color:#f97316; line-height:1;"></i>
                </div>
                <h3 style="font-size:0.82rem; font-weight:800; color:#111827; text-transform:uppercase; letter-spacing:0.06em; margin:0;">Payment Summary</h3>
            </div>
            <div style="display:flex; flex-direction:column; gap:8px; font-size:0.875rem;">
                <div style="display:flex; justify-content:space-between; color:#6b7280;">
                    <span>Method</span>
                    <span style="font-weight:600; color:#111827;">{{ $order->payment_method_label }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; color:#6b7280;">
                    <span>Subtotal</span>
                    <span style="font-weight:600; color:#111827;">@bdt($order->subtotal)</span>
                </div>
                <div style="display:flex; justify-content:space-between; color:#6b7280;">
                    <span>Delivery</span>
                    <span style="font-weight:600; color:#111827;">@bdt($order->delivery_cost)</span>
                </div>
                @if($order->coupon_discount > 0)
                <div style="display:flex; justify-content:space-between; color:#16a34a;">
                    <span>Discount</span>
                    <span style="font-weight:600;">-@bdt($order->coupon_discount)</span>
                </div>
                @endif
                <div style="border-top:1px dashed #e5e7eb; padding-top:8px; display:flex; justify-content:space-between;">
                    <span style="font-weight:700; color:#111827;">Total</span>
                    <span style="font-size:1.05rem; font-weight:800; color:#f97316;">@bdt($order->total)</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Items --}}
    <div style="background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,0.07); overflow:hidden;">
        <div style="padding:20px 24px; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; gap:10px;">
            <div style="width:36px; height:36px; background:#fff7ed; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fi fi-rr-shopping-bag" style="font-size:16px; color:#f97316; line-height:1;"></i>
            </div>
            <h3 style="font-size:0.82rem; font-weight:800; color:#111827; text-transform:uppercase; letter-spacing:0.06em; margin:0;">
                Order Items <span style="font-size:0.75rem; color:#9ca3af; font-weight:600; text-transform:none;">({{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }})</span>
            </h3>
        </div>
        @foreach($order->items as $item)
        <div style="display:flex; align-items:center; gap:16px; padding:16px 24px; border-bottom:1px solid #f9fafb;">
            @if($item->product_image)
                <img src="{{ Storage::disk('public')->url($item->product_image) }}" alt="{{ $item->product_name }}" loading="lazy" decoding="async" width="60" height="60"
                    style="width:60px; height:60px; object-fit:cover; border-radius:8px; border:1px solid #e5e7eb; flex-shrink:0;">
            @else
                <div style="width:60px; height:60px; background:#f3f4f6; border-radius:8px; border:1px solid #e5e7eb; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fi fi-rr-picture" style="font-size:22px; color:#d1d5db; line-height:1;"></i>
                </div>
            @endif
            <div style="flex:1; min-width:0;">
                <div style="font-weight:600; color:#111827; font-size:0.9rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $item->product_name }}</div>
                <div style="font-size:0.8rem; color:#6b7280; margin-top:2px;">@bdt($item->price) × {{ $item->quantity }}</div>
            </div>
            <div style="font-weight:800; color:#111827; font-size:0.95rem; flex-shrink:0;">@bdt($item->subtotal)</div>
        </div>
        @endforeach
    </div>

    {{-- Actions --}}
    <div style="display:flex; gap:12px; flex-wrap:wrap;">
        <a href="{{ route('track-order.index') }}"
            style="display:inline-flex; align-items:center; gap:8px; padding:11px 22px; background:#fff; border:1.5px solid #e5e7eb; border-radius:8px; font-size:0.85rem; font-weight:700; color:#374151; text-decoration:none; transition:all 0.2s;"
            onmouseover="this.style.borderColor='#f97316'; this.style.color='#f97316';"
            onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='#374151';">
            <i class="fi fi-rr-arrow-left" style="line-height:1;"></i>
            Track Another Order
        </a>
        <a href="{{ route('checkout.invoice', $order->order_number) }}"
            style="display:inline-flex; align-items:center; gap:8px; padding:11px 22px; background:#f97316; border:1.5px solid #f97316; border-radius:8px; font-size:0.85rem; font-weight:700; color:#fff; text-decoration:none; transition:all 0.2s;"
            onmouseover="this.style.background='#ea6c0a';"
            onmouseout="this.style.background='#f97316';">
            <i class="fi fi-rr-file-download" style="line-height:1;"></i>
            Download Invoice
        </a>
        <a href="{{ route('products.index') }}"
            style="display:inline-flex; align-items:center; gap:8px; padding:11px 22px; background:#fff; border:1.5px solid #e5e7eb; border-radius:8px; font-size:0.85rem; font-weight:700; color:#374151; text-decoration:none; transition:all 0.2s;"
            onmouseover="this.style.borderColor='#16a34a'; this.style.color='#16a34a';"
            onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='#374151';">
            <i class="fi fi-rr-shopping-cart" style="line-height:1;"></i>
            Continue Shopping
        </a>
    </div>

</div>
</div>

<style>
@media (max-width: 640px) {
    .track-grid { grid-template-columns: 1fr !important; }
}

@keyframes pulse-ring {
    0%   { box-shadow: 0 0 0 0 rgba(249,115,22,0.5); }
    70%  { box-shadow: 0 0 0 10px rgba(249,115,22,0); }
    100% { box-shadow: 0 0 0 0 rgba(249,115,22,0); }
}

@keyframes icon-bounce {
    0%, 100% { transform: scale(1); }
    40%       { transform: scale(1.18); }
    60%       { transform: scale(0.94); }
}

.step-active {
    animation: pulse-ring 1.8s ease-out infinite;
}

.step-active i {
    animation: icon-bounce 1.8s ease-in-out infinite;
    display: inline-block;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const bar = document.getElementById('progress-bar');
    if (bar) {
        const target = bar.dataset.width;
        requestAnimationFrame(function () {
            setTimeout(function () {
                bar.style.width = target + '%';
            }, 150);
        });
    }
});
</script>
@endsection
