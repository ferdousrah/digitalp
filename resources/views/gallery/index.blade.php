@extends('layouts.app')
@section('title', 'Gallery')

@section('content')
@include('components.breadcrumb', ['items' => [['label' => 'Gallery']]])

<div class="container-custom px-4 sm:px-6 lg:px-8 pb-16">
    <h1 class="text-3xl font-display mb-8">Gallery</h1>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($albums as $album)
        <a href="{{ route('gallery.show', $album) }}" class="group relative aspect-video bg-surface-100 rounded-xl overflow-hidden">
            @if($album->getFirstMediaUrl('album_cover'))<img src="{{ $album->getFirstMediaUrl('album_cover') }}" alt="{{ $album->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async">@endif
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                <h3 class="font-semibold text-lg">{{ $album->title }}</h3>
                <p class="text-sm text-white/80">{{ $album->items_count }} items</p>
            </div>
        </a>
        @empty
        <div class="col-span-full text-center py-16"><p class="text-surface-500">No gallery albums yet.</p></div>
        @endforelse
    </div>
</div>
@endsection
