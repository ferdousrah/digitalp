@extends('layouts.app')
@section('title', $service->title . ' - Digital Support')

@section('content')
@include('components.breadcrumb', ['items' => [['label' => 'Services', 'url' => route('services.index')], ['label' => $service->title]]])

<div class="container-custom px-4 sm:px-6 lg:px-8 pb-16">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl md:text-4xl font-display mb-6">{{ $service->title }}</h1>
        @if($service->short_description)<p class="text-xl text-surface-600 mb-8">{{ $service->short_description }}</p>@endif
        @if($service->description)<div class="prose prose-lg max-w-none">{!! $service->description !!}</div>@endif
    </div>
</div>
@endsection
