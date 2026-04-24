@extends('layouts.app')

@section('title', 'Order Confirmed - ' . $order->order_number)
@section('meta_description', 'Your order has been placed successfully.')

@section('content')
<div style="background:#f3f4f6; min-height:100vh; padding:1px 0;">
<div class="container-custom px-4 sm:px-6 lg:px-8" style="max-width:900px; padding-top:48px; padding-bottom:80px;">

    {{-- Success Banner --}}
    <div style="text-align:center; padding:40px 24px; background:linear-gradient(135deg,#f0fdf4 0%,#dcfce7 100%); border-radius:16px; border:1px solid #bbf7d0; margin-bottom:32px;">
        <div style="width:72px; height:72px; background:#16a34a; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 18px; box-shadow:0 8px 24px rgba(22,163,74,0.3);">
            <svg style="width:38px;height:38px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 style="font-size:1.6rem; font-weight:800; color:#15803d; margin:0 0 8px;">Order Placed Successfully!</h1>
        <p style="font-size:0.92rem; color:#166534; margin:0;">Thank you for your order. We'll process it and get it to you soon.</p>
    </div>

    {{-- Order Number --}}
    <div style="background:#fff7ed; border:2px dashed #f97316; border-radius:12px; padding:18px 24px; margin-bottom:28px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
        <div>
            <div style="font-size:0.78rem; font-weight:600; color:#9a3412; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:4px;">Order Number</div>
            <div style="font-size:1.4rem; font-weight:800; color:#ea580c; letter-spacing:0.06em;">{{ $order->order_number }}</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:0.78rem; font-weight:600; color:#9a3412; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:4px;">Order Date</div>
            <div style="font-size:0.9rem; font-weight:600; color:#111827;">{{ $order->created_at->format('d M Y, h:i A') }}</div>
        </div>
    </div>

    {{-- Status + Payment --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:28px;">
        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:16px 20px;">
            <div style="font-size:0.75rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px;">Order Status</div>
            <span style="display:inline-flex; align-items:center; gap:5px; background:#fef3c7; color:#d97706; font-size:0.82rem; font-weight:700; padding:4px 12px; border-radius:20px; text-transform:capitalize;">
                <svg style="width:12px;height:12px;" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                {{ ucfirst($order->status) }}
            </span>
        </div>
        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:16px 20px;">
            <div style="font-size:0.75rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px;">Payment</div>
            <div style="font-size:0.9rem; font-weight:700; color:#111827;">{{ $order->payment_method_label }}</div>
            <div style="font-size:0.78rem; color:#6b7280; margin-top:2px; text-transform:capitalize;">Status: {{ $order->payment_status }}</div>
        </div>
    </div>

    {{-- Items Ordered --}}
    <div style="background:#fff; border:1px solid #e5e7eb; border-radius:10px; overflow:hidden; margin-bottom:28px;">
        <div style="background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:14px 20px; display:flex; align-items:center; gap:8px;">
            <svg style="width:18px;height:18px;color:#f97316;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <h3 style="font-size:0.88rem; font-weight:700; color:#111827; text-transform:uppercase; letter-spacing:0.06em; margin:0; padding-bottom:3px; border-bottom:2px solid #f97316;">Items Ordered</h3>
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:0.85rem;">
            <thead>
                <tr style="background:#fafafa;">
                    <th style="text-align:left; padding:10px 20px; font-size:0.76rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">Product</th>
                    <th style="text-align:center; padding:10px 12px; font-size:0.76rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">Qty</th>
                    <th style="text-align:right; padding:10px 20px; font-size:0.76rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr style="border-top:1px solid #f3f4f6;">
                    <td style="padding:12px 20px;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            @if($item->product_image)
                            <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" style="width:46px; height:46px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; flex-shrink:0;">
                            @else
                            <div style="width:46px; height:46px; background:#f3f4f6; border-radius:6px; border:1px solid #e5e7eb; flex-shrink:0;"></div>
                            @endif
                            <div style="font-weight:600; color:#111827; line-height:1.4;">{{ $item->product_name }}</div>
                        </div>
                    </td>
                    <td style="padding:12px; text-align:center; color:#374151; font-weight:600;">{{ $item->quantity }}</td>
                    <td style="padding:12px 20px; text-align:right; font-weight:700; color:#111827;">{{ number_format($item->subtotal, 2) }}৳</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#fafafa; border-top:1px solid #e5e7eb;">
                    <td colspan="2" style="padding:10px 20px; font-size:0.82rem; color:#6b7280; font-weight:600;">Items Subtotal</td>
                    <td style="padding:10px 20px; text-align:right; font-weight:700; color:#111827;">{{ number_format($order->subtotal, 2) }}৳</td>
                </tr>
                <tr style="background:#fafafa; border-top:1px solid #f3f4f6;">
                    <td colspan="2" style="padding:10px 20px; font-size:0.82rem; color:#6b7280; font-weight:600;">Delivery Charge</td>
                    <td style="padding:10px 20px; text-align:right; font-weight:700; color:#111827;">{{ number_format($order->delivery_cost, 2) }}৳</td>
                </tr>
                @if($order->coupon_discount > 0)
                <tr style="background:#f0fdf4; border-top:1px solid #f3f4f6;">
                    <td colspan="2" style="padding:10px 20px; font-size:0.82rem; color:#15803d; font-weight:600;">Coupon Discount ({{ $order->coupon_code }})</td>
                    <td style="padding:10px 20px; text-align:right; font-weight:700; color:#15803d;">-{{ number_format($order->coupon_discount, 2) }}৳</td>
                </tr>
                @endif
                <tr style="background:#fff7ed; border-top:2px solid #f97316;">
                    <td colspan="2" style="padding:12px 20px; font-size:0.95rem; font-weight:800; color:#111827;">Total</td>
                    <td style="padding:12px 20px; text-align:right; font-size:1.1rem; font-weight:800; color:#f97316;">{{ number_format($order->total, 2) }}৳</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Shipping + Billing --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:36px;">
        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:18px 20px;">
            <div style="font-size:0.75rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:10px; padding-bottom:8px; border-bottom:2px solid #f97316; display:inline-block;">Shipping Address</div>
            <div style="font-size:0.87rem; color:#111827; line-height:1.8;">
                <div style="font-weight:700;">{{ $order->shipping_name }}</div>
                <div style="color:#374151;">{{ $order->shipping_phone }}</div>
                <div style="color:#374151;">{{ $order->shipping_thana }}, {{ $order->shipping_district }}</div>
                <div style="color:#374151;">{{ $order->shipping_address }}</div>
            </div>
        </div>
        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:18px 20px;">
            <div style="font-size:0.75rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:10px; padding-bottom:8px; border-bottom:2px solid #f97316; display:inline-block;">Billing Address</div>
            <div style="font-size:0.87rem; color:#111827; line-height:1.8;">
                <div style="font-weight:700;">{{ $order->billing_name }}</div>
                <div style="color:#374151;">{{ $order->billing_phone }}</div>
                <div style="color:#374151;">{{ $order->billing_thana }}, {{ $order->billing_district }}</div>
                <div style="color:#374151;">{{ $order->billing_address }}</div>
                <div style="color:#374151;">{{ $order->billing_country }}</div>
            </div>
        </div>
    </div>

    @if($order->notes)
    <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px; padding:16px 20px; margin-bottom:28px;">
        <div style="font-size:0.75rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px;">Special Notes</div>
        <p style="font-size:0.87rem; color:#374151; margin:0; line-height:1.6;">{{ $order->notes }}</p>
    </div>
    @endif

    {{-- Action buttons --}}
    <div style="display:flex; gap:12px; flex-wrap:wrap; justify-content:center;">
        <a href="{{ route('checkout.invoice', $order->order_number) }}" style="display:inline-flex; align-items:center; gap:8px; padding:13px 28px; background:#1d4ed8; color:#fff; font-size:0.88rem; font-weight:700; text-decoration:none; border-radius:8px; transition:background 0.2s;" onmouseover="this.style.background='#1e40af'" onmouseout="this.style.background='#1d4ed8'">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Download Invoice
        </a>
        <a href="{{ route('home') }}" style="display:inline-flex; align-items:center; gap:8px; padding:13px 28px; background:#f97316; color:#fff; font-size:0.88rem; font-weight:700; text-decoration:none; border-radius:8px; transition:background 0.2s;" onmouseover="this.style.background='#ea6c0a'" onmouseout="this.style.background='#f97316'">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Continue Shopping
        </a>
        <a href="{{ route('products.index') }}" style="display:inline-flex; align-items:center; gap:8px; padding:13px 28px; background:#fff; color:#374151; font-size:0.88rem; font-weight:700; text-decoration:none; border-radius:8px; border:1.5px solid #e5e7eb; transition:all 0.2s;" onmouseover="this.style.borderColor='#f97316';this.style.color='#f97316'" onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            Browse Products
        </a>
    </div>

</div>
</div>

<style>
@media (max-width: 600px) {
    .success-grid { grid-template-columns: 1fr !important; }
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<script>
(function launchConfetti() {
    var duration = 3200;
    var end = Date.now() + duration;
    var colors = ['#f97316', '#16a34a', '#3b82f6', '#f59e0b', '#ec4899'];

    confetti({ particleCount: 120, spread: 90, origin: { y: 0.55 }, colors: colors, ticks: 200 });

    var frame = function () {
        confetti({ particleCount: 4, angle: 60,  spread: 55, origin: { x: 0, y: 0.6 }, colors: colors });
        confetti({ particleCount: 4, angle: 120, spread: 55, origin: { x: 1, y: 0.6 }, colors: colors });
        if (Date.now() < end) requestAnimationFrame(frame);
    };
    frame();
}());
</script>
@endpush
@endsection
