@extends('layouts.app')
@section('title', $blogPost->meta_title ?? $blogPost->title . ' - Digital Support Blog')
@section('meta_description', $blogPost->meta_description ?? \Illuminate\Support\Str::limit(strip_tags((string) ($blogPost->excerpt ?? $blogPost->content)), 160))

@php
    app(\App\Services\SeoService::class)
        ->ogType('article')
        ->image($blogPost->getFirstMediaUrl('blog_image', 'large') ?: $blogPost->getFirstMediaUrl('featured_image', 'large'))
        ->canonical(route('blog.show', $blogPost));
@endphp

@push('seo')
    @include('partials.schema.article', ['post' => $blogPost])
    @include('partials.schema.breadcrumbs', ['items' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Blog', 'url' => route('blog.index')],
        ['label' => $blogPost->title],
    ]])
@endpush

@section('content')
@include('components.breadcrumb', ['items' => [['label' => 'Blog', 'url' => route('blog.index')], ['label' => $blogPost->title]]])

<article class="container-custom px-4 sm:px-6 lg:px-8 pb-16">
    <div class="max-w-3xl mx-auto">
        <header class="mb-8">
            @if($blogPost->category)<a href="{{ route('blog.category', $blogPost->category) }}" class="text-sm text-primary-600 font-semibold uppercase tracking-wide">{{ $blogPost->category->name }}</a>@endif
            <h1 class="text-3xl md:text-4xl font-display mt-2 mb-4">{{ $blogPost->title }}</h1>
            <div class="flex items-center gap-4 text-sm text-surface-500">
                <span>By {{ $blogPost->author->name }}</span>
                <time>{{ $blogPost->published_at?->format('F d, Y') }}</time>
            </div>
        </header>

        @if($blogPost->getFirstMediaUrl('featured_image'))
        <div class="rounded-2xl overflow-hidden mb-8">
            <img src="{{ $blogPost->getFirstMediaUrl('featured_image') }}" alt="{{ $blogPost->title }}" class="w-full">
        </div>
        @endif

        <div class="prose prose-lg max-w-none">{!! $blogPost->content !!}</div>
    </div>

    @if($relatedPosts->count())
    <div class="max-w-3xl mx-auto mt-16 border-t border-surface-200 pt-8">
        <h2 class="text-2xl font-display mb-6">Related Posts</h2>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach($relatedPosts as $related)
            <a href="{{ route('blog.show', $related) }}" class="group">
                <div class="aspect-video bg-surface-100 rounded-lg overflow-hidden mb-3">
                    @if($related->getFirstMediaUrl('featured_image'))<img src="{{ $related->getFirstMediaUrl('featured_image') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform" loading="lazy">@endif
                </div>
                <h3 class="font-semibold line-clamp-2 group-hover:text-primary-600 transition-colors">{{ $related->title }}</h3>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</article>
@endsection
