@extends('layouts.app')
@section('title', 'My Wishlist - Digital Support')

@section('content')
@include('components.breadcrumb', ['items' => [['label' => 'Wishlist']]])

<div class="container-custom px-4 sm:px-6 lg:px-8 pb-16">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:32px;">
        <h1 style="font-size:1.875rem; font-weight:700; color:#111827;">
            My Wishlist
            @if($wishlistItems->count())
                <span style="font-size:1rem; font-weight:400; color:#6b7280; margin-left:8px;">({{ $wishlistItems->count() }} {{ Str::plural('item', $wishlistItems->count()) }})</span>
            @endif
        </h1>
    </div>

    @if($wishlistItems->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-start">
        @foreach($wishlistItems as $item)
            @if($item->product)
                @include('components.product-card', ['product' => $item->product])
            @endif
        @endforeach
    </div>
    @else
    <div style="text-align:center; padding:80px 20px;">
        <div style="width:80px; height:80px; background:#fef2f2; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
            <svg style="width:40px; height:40px; color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
        </div>
        <h2 style="font-size:1.25rem; font-weight:600; color:#111827; margin-bottom:8px;">Your wishlist is empty</h2>
        <p style="color:#6b7280; margin-bottom:24px;">Browse products and click the heart icon to save your favorites here.</p>
        <a href="{{ route('products.index') }}" style="display:inline-flex; align-items:center; gap:6px; background:#16a34a; color:#fff; font-weight:600; font-size:0.9rem; padding:12px 24px; border-radius:10px; text-decoration:none; transition:background 0.2s;" onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
            Browse Products
            <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>
    @endif
</div>
@endsection
