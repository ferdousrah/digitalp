@extends('layouts.app')
@section('title', 'All Categories')

@section('content')
{{-- Dedicated category browser — responsive: single tight column on phones,
     wider with more grid columns + taller banners on tablet/desktop. --}}
<style>
    .mcat-wrap         { max-width:560px; margin:0 auto; padding:12px 12px 28px; background:#fff; }
    .mcat-title        { font-size:1.15rem; font-weight:800; color:#0f172a; margin:4px 4px 14px; }
    .mcat-banner       { min-height:122px; }
    .mcat-banner-title { font-size:1.05rem; }
    .mcat-grid         { display:grid; grid-template-columns:repeat(4, 1fr); gap:10px 8px; }
    .mcat-tile-label   { font-size:0.62rem; }

    @media (min-width:640px) {
        .mcat-wrap         { max-width:760px; padding:18px 20px 36px; }
        .mcat-title        { font-size:1.4rem; margin-bottom:18px; }
        .mcat-banner       { min-height:170px; }
        .mcat-banner-title { font-size:1.4rem; }
        .mcat-grid         { grid-template-columns:repeat(6, 1fr); gap:18px 14px; }
        .mcat-tile-label   { font-size:0.72rem; }
    }
    @media (min-width:1024px) {
        .mcat-wrap         { max-width:1200px; padding:24px 32px 56px; }
        .mcat-title        { font-size:1.6rem; }
        .mcat-banner       { min-height:210px; }
        .mcat-banner-title { font-size:1.7rem; }
        .mcat-grid         { grid-template-columns:repeat(8, 1fr); gap:22px 16px; }
        .mcat-tile-label   { font-size:0.78rem; }
    }
</style>
<div class="mcat-wrap">
    <h1 class="mcat-title">{{ sc('navbar', 'categories', 'Categories') }}</h1>

    @php
        // Soft colour palette cycled per section for the Daraz/Shajgoj-style banners
        $catPalettes = [
            ['bg' => '#fdf2f8', 'accent' => '#db2777'],
            ['bg' => '#f0fdf4', 'accent' => '#16a34a'],
            ['bg' => '#eff6ff', 'accent' => '#2563eb'],
            ['bg' => '#fff7ed', 'accent' => '#ea580c'],
            ['bg' => '#faf5ff', 'accent' => '#9333ea'],
            ['bg' => '#fefce8', 'accent' => '#ca8a04'],
        ];
    @endphp

    @forelse($categories as $category)
        @php $p = $catPalettes[$loop->index % count($catPalettes)]; @endphp
        <section style="margin-bottom:22px;">

            {{-- Banner header — parent category's banner as the FULL section background (text overlaid) --}}
            @php
                $bWebp = $category->getFirstMediaUrl('category_banner', 'large_webp') ?: $category->getFirstMediaUrl('category_image', 'medium_webp');
                $bJpg  = $category->getFirstMediaUrl('category_banner', 'large') ?: $category->getFirstMediaUrl('category_banner')
                         ?: $category->getFirstMediaUrl('category_image', 'medium') ?: $category->getFirstMediaUrl('category_image');
            @endphp
            <a href="{{ route('categories.show', $category) }}" class="mcat-banner"
               style="position:relative; display:flex; align-items:center; background:{{ $p['bg'] }}; border-radius:14px; margin-bottom:12px; text-decoration:none; overflow:hidden;">
                @if($bJpg)
                    {{-- full-bleed banner image --}}
                    <picture>
                        @if($bWebp)<source type="image/webp" srcset="{{ $bWebp }}">@endif
                        <img src="{{ $bJpg }}" alt="{{ $category->name }}" loading="lazy" decoding="async"
                             style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;">
                    </picture>
                    {{-- left-to-right scrim so the overlaid text stays readable --}}
                    <span style="position:absolute; inset:0; background:linear-gradient(to right, {{ $p['bg'] }} 0%, {{ $p['bg'] }}f2 32%, {{ $p['bg'] }}80 58%, transparent 90%);"></span>
                @endif
                <span style="position:relative; z-index:1; display:flex; flex-direction:column; justify-content:center; padding:14px 18px; max-width:72%;">
                    <span class="mcat-banner-title" style="display:block; font-weight:800; color:{{ $p['accent'] }}; line-height:1.2;">{{ $category->name }}</span>
                    @if($category->description)
                        <span style="display:block; font-size:0.7rem; color:#475569; margin:4px 0 9px; line-height:1.35; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">{{ $category->description }}</span>
                    @else
                        <span style="display:block; height:9px;"></span>
                    @endif
                    <span style="display:inline-flex; align-self:flex-start; align-items:center; gap:4px; background:{{ $p['accent'] }}; color:#fff; font-size:0.66rem; font-weight:700; padding:5px 12px; border-radius:999px;">
                        View All
                        <svg style="width:11px; height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </span>
            </a>

            {{-- Sub-category grid --}}
            @if($category->children->count())
            <div class="mcat-grid">

                {{-- View All tile --}}
                <a href="{{ route('categories.show', $category) }}" style="display:flex; flex-direction:column; align-items:center; gap:6px; text-decoration:none;">
                    <span style="width:100%; aspect-ratio:1; border-radius:12px; background:{{ $p['bg'] }}; display:flex; align-items:center; justify-content:center; border:1px solid {{ $p['accent'] }}22;">
                        <svg style="width:26px; height:26px; color:{{ $p['accent'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </span>
                    <span class="mcat-tile-label" style="font-weight:700; color:{{ $p['accent'] }}; text-align:center; line-height:1.2;">View All</span>
                </a>

                @foreach($category->children as $child)
                <a href="{{ route('categories.show', $child) }}" style="display:flex; flex-direction:column; align-items:center; gap:6px; text-decoration:none;">
                    <span style="width:100%; aspect-ratio:1; border-radius:12px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                        <x-media-image :model="$child" collection="category_image" size="medium" :alt="$child->name" style="width:100%; height:100%; object-fit:cover;">
                            <svg style="width:24px; height:24px; color:#94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </x-media-image>
                    </span>
                    <span class="mcat-tile-label" style="font-weight:600; color:#334155; text-align:center; line-height:1.2; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">{{ $child->name }}</span>
                </a>
                @endforeach
            </div>
            @endif
        </section>
    @empty
        <div style="text-align:center; padding:48px 16px;">
            <p style="color:#64748b;">No categories available yet.</p>
        </div>
    @endforelse
</div>
@endsection
