@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div style="background:#f8fafc; min-height:60vh;">
    <div class="container-custom" style="padding:48px 16px;">

        <div style="display:flex; align-items:center; gap:14px; margin-bottom:24px;">
            <a href="{{ route('account.index') }}" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; color:#475569; font-weight:600; text-decoration:none; font-size:0.85rem;">
                <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Account
            </a>
            <h1 style="margin:0; font-size:1.5rem; font-weight:800; color:#0f172a;">My Orders</h1>
        </div>

        @if($orders->count() === 0)
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:60px 24px; text-align:center;">
                <div style="width:88px; height:88px; margin:0 auto 18px; background:#f8fafc; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                    <svg style="width:42px; height:42px; color:#cbd5e1;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                </div>
                <p style="margin:0 0 6px; font-size:1.05rem; font-weight:700; color:#0f172a;">No orders yet</p>
                <p style="margin:0 0 18px; color:#64748b; font-size:0.9rem;">When you place an order it will show up here.</p>
                <a href="{{ route('products.index') }}" style="display:inline-block; padding:12px 24px; background:#f97316; color:#fff; border-radius:8px; font-weight:700; text-decoration:none; font-size:0.88rem;">Browse products</a>
            </div>
        @else
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                            <th style="text-align:left; padding:14px 20px; font-size:0.72rem; font-weight:700; color:#64748b; letter-spacing:0.08em; text-transform:uppercase;">Order</th>
                            <th style="text-align:left; padding:14px 20px; font-size:0.72rem; font-weight:700; color:#64748b; letter-spacing:0.08em; text-transform:uppercase;">Date</th>
                            <th style="text-align:left; padding:14px 20px; font-size:0.72rem; font-weight:700; color:#64748b; letter-spacing:0.08em; text-transform:uppercase;">Status</th>
                            <th style="text-align:right; padding:14px 20px; font-size:0.72rem; font-weight:700; color:#64748b; letter-spacing:0.08em; text-transform:uppercase;">Total</th>
                            <th style="padding:14px 20px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:16px 20px; font-weight:700; color:#0f172a;">#{{ $order->order_number ?? $order->id }}</td>
                            <td style="padding:16px 20px; color:#64748b; font-size:0.88rem;">{{ $order->created_at?->format('M d, Y') }}</td>
                            <td style="padding:16px 20px;">
                                <span style="font-size:0.72rem; font-weight:700; padding:4px 10px; background:#f8fafc; border:1px solid #e2e8f0; color:#475569; border-radius:999px; text-transform:uppercase; letter-spacing:0.05em;">{{ $order->status ?? 'Pending' }}</span>
                            </td>
                            <td style="padding:16px 20px; text-align:right; font-weight:800; color:#0f172a;">{{ number_format((float) ($order->total ?? 0), 0) }}৳</td>
                            <td style="padding:16px 20px; text-align:right;">
                                @if(\Illuminate\Support\Facades\Route::has('checkout.success'))
                                    <a href="{{ route('checkout.success', $order->order_number ?? $order->id) }}" style="color:#f97316; font-weight:700; text-decoration:none; font-size:0.85rem;">View →</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top:18px;">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
