@extends('layouts.app')
@section('title', 'Blog')

@section('content')
@include('components.breadcrumb', ['items' => array_filter([['label' => 'Blog', 'url' => isset($blogCategory) ? route('blog.index') : null], isset($blogCategory) ? ['label' => $blogCategory->name] : null])])

<div class="container-custom px-4 sm:px-6 lg:px-8 pb-16">
    <h1 class="text-3xl font-display mb-8">{{ isset($blogCategory) ? $blogCategory->name : 'Blog' }}</h1>

    @if($categories->count())
    <div class="flex flex-wrap gap-3 mb-8">
        <a href="{{ route('blog.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !isset($blogCategory) ? 'bg-primary-500 text-white' : 'bg-surface-100 text-surface-700 hover:bg-primary-50' }}">All</a>
        @foreach($categories as $cat)
        <a href="{{ route('blog.category', $cat) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ isset($blogCategory) && $blogCategory->id == $cat->id ? 'bg-primary-500 text-white' : 'bg-surface-100 text-surface-700 hover:bg-primary-50' }}">{{ $cat->name }} ({{ $cat->posts_count }})</a>
        @endforeach
    </div>
    @endif

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 gsap-stagger-grid">
        @forelse($posts as $post)
        <article class="bg-white rounded-xl shadow-sm border border-surface-200 overflow-hidden group gsap-stagger-item">
            <a href="{{ route('blog.show', $post) }}" class="block aspect-video overflow-hidden bg-surface-100">
                @if($post->getFirstMediaUrl('featured_image'))
                    <img src="{{ $post->getFirstMediaUrl('featured_image') }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async">
                @endif
            </a>
            <div class="p-6">
                @if($post->category)<span class="text-xs text-primary-600 font-semibold uppercase tracking-wide">{{ $post->category->name }}</span>@endif
                <h2 class="font-semibold text-xl mt-2 mb-3 line-clamp-2"><a href="{{ route('blog.show', $post) }}" class="hover:text-primary-600 transition-colors">{{ $post->title }}</a></h2>
                @if($post->excerpt)<p class="text-surface-600 text-sm line-clamp-3 mb-4">{{ $post->excerpt }}</p>@endif
                <div class="flex items-center justify-between text-sm text-surface-500">
                    <span>{{ $post->author?->name ?? 'Admin' }}</span>
                    <time>{{ $post->published_at?->format('M d, Y') }}</time>
                </div>
            </div>
        </article>
        @empty
        <div class="col-span-full">
            <x-empty-state
                icon="inbox"
                title="No blog posts yet"
                body="Check back soon — we're working on great content."
                variant="plain" />
        </div>
        @endforelse
    </div>
    <div class="mt-8">{{ $posts->links() }}</div>
</div>
@endsection
