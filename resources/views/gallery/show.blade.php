@extends('layouts.app')
@section('title', $album->title . ' - Gallery')

@section('content')
@include('components.breadcrumb', ['items' => [['label' => 'Gallery', 'url' => route('gallery.index')], ['label' => $album->title]]])

<div class="container-custom px-4 sm:px-6 lg:px-8 pb-16">
    <h1 class="text-3xl font-display mb-2">{{ $album->title }}</h1>
    @if($album->description)<p class="text-surface-600 mb-8">{{ $album->description }}</p>@endif

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($album->items as $item)
        <div class="aspect-square bg-surface-100 rounded-lg overflow-hidden">
            @if($item->getFirstMediaUrl('item_image'))<img src="{{ $item->getFirstMediaUrl('item_image') }}" alt="{{ $item->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async">@endif
        </div>
        @endforeach
    </div>
</div>
@endsection
