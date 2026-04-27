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
            <x-empty-state
                icon="orders"
                title="No orders yet"
                body="When you place an order it will show up here."
                ctaLabel="Browse Products"
                :ctaHref="route('products.index')" />
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
                            <td style="padding:16px 20px; text-align:right; font-weight:800; color:#0f172a;">@bdt($order->total ?? 0)</td>
                            <td style="padding:16px 20px; text-align:right;">
                                <a href="{{ route('account.orders.show', $order->order_number) }}" style="color:#f97316; font-weight:700; text-decoration:none; font-size:0.85rem;">View →</a>
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
