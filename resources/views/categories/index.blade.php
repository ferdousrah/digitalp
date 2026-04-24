@extends('layouts.app')
@section('title', 'Product Categories - Digital Support')

@section('content')
@include('components.breadcrumb', ['items' => [['label' => 'Categories']]])

<div class="container-custom px-4 sm:px-6 lg:px-8 pb-16">
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-display mb-4">Product Categories</h1>
        <p class="text-surface-600 max-w-2xl mx-auto">Browse our wide selection of digital products organized by category.</p>
    </div>

    @if($categories->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($categories as $category)
        <div class="bg-white rounded-xl shadow-sm border border-surface-200 overflow-hidden group hover:shadow-md transition-shadow duration-300">
            <a href="{{ route('categories.show', $category) }}" class="block relative overflow-hidden aspect-[16/10]">
                @if($category->getFirstMediaUrl('category_image'))
                    <img src="{{ $category->getFirstMediaUrl('category_image') }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-primary-50 to-primary-100 flex items-center justify-center">
                        <svg class="w-16 h-16 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </a>

            <div class="p-6">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-xl font-semibold text-surface-900">
                        <a href="{{ route('categories.show', $category) }}" class="hover:text-primary-600 transition-colors">{{ $category->name }}</a>
                    </h2>
                    <span class="text-sm text-surface-500 bg-surface-100 px-3 py-1 rounded-full">{{ $category->products_count }} {{ Str::plural('product', $category->products_count) }}</span>
                </div>

                @if($category->description)
                    <p class="text-surface-600 text-sm mb-4 line-clamp-2">{{ $category->description }}</p>
                @endif

                @if($category->children->count())
                <div class="flex flex-wrap gap-2">
                    @foreach($category->children->take(5) as $child)
                    <a href="{{ route('categories.show', $child) }}" class="text-xs px-3 py-1.5 bg-surface-100 hover:bg-primary-50 text-surface-600 hover:text-primary-600 rounded-full transition-colors">
                        {{ $child->name }} ({{ $child->products_count }})
                    </a>
                    @endforeach
                    @if($category->children->count() > 5)
                    <a href="{{ route('categories.show', $category) }}" class="text-xs px-3 py-1.5 bg-primary-50 text-primary-600 rounded-full hover:bg-primary-100 transition-colors">
                        +{{ $category->children->count() - 5 }} more
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-16">
        <svg class="w-16 h-16 text-surface-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
        <p class="text-surface-500 text-lg">No categories available yet.</p>
    </div>
    @endif
</div>
@endsection
