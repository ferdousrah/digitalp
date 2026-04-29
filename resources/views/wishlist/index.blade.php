@extends('layouts.app')
@section('title', 'My Wishlist')

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
    <x-empty-state
        icon="wishlist"
        title="Your wishlist is empty"
        body="Browse products and click the heart icon to save your favorites here."
        ctaLabel="Browse Products"
        :ctaHref="route('products.index')"
        variant="plain" />
    @endif
</div>
@endsection
