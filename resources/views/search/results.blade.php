@extends('layouts.app')
@section('title', 'Search Results - Digital Support')

@section('content')
@include('components.breadcrumb', ['items' => [['label' => 'Search Results']]])

<div class="container-custom px-4 sm:px-6 lg:px-8 pb-16">
    <h1 class="text-3xl font-display mb-2">Search Results</h1>
    <p class="text-surface-600 mb-8">{{ $products instanceof \Illuminate\Pagination\LengthAwarePaginator ? $products->total() : $products->count() }} results for "{{ $query }}"</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-start">
        @forelse($products as $product)
            @include('components.product-card', ['product' => $product])
        @empty
            <div class="col-span-full text-center py-16"><p class="text-surface-500 text-lg mb-4">No products found for "{{ $query }}".</p><a href="{{ route('products.index') }}" class="btn-primary">Browse All Products</a></div>
        @endforelse
    </div>
    @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator)<div class="mt-8">{{ $products->links() }}</div>@endif
</div>
@endsection
