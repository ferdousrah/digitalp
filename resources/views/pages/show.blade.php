@extends('layouts.app')
@section('title', $page->meta_title ?? $page->title . '')

@section('content')
@include('components.breadcrumb', ['items' => [['label' => $page->title]]])

<div class="container-custom px-4 sm:px-6 lg:px-8 pb-16">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl md:text-4xl font-display mb-8">{{ $page->title }}</h1>
        <div class="prose prose-lg max-w-none">{!! $page->content !!}</div>
    </div>
</div>
@endsection
