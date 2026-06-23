@extends('layouts.app')
@section('title', 'Offers & Promotions')
@section('meta_description', 'Latest deals, festivals and special offers — grab them before they end.')

@section('content')
@include('components.breadcrumb', ['items' => [['label' => 'Offers']]])

<div class="container-custom px-4 sm:px-6 lg:px-8" style="padding-top:20px; padding-bottom:56px;">

    <div style="margin-bottom:24px;">
        <h1 style="font-size:1.6rem; font-weight:800; color:#111827; margin:0;">Offers &amp; Promotions</h1>
        <p style="font-size:0.9rem; color:#6b7280; margin:6px 0 0;">Limited-time deals — don't miss out.</p>
    </div>

    @if($offers->isEmpty())
        <div style="text-align:center; padding:64px 16px; color:#9ca3af;">
            <x-app-icon name="tags" :size="44" style="color:#d1d5db;" />
            <p style="margin:14px 0 0; font-size:0.95rem;">No active offers right now. Check back soon!</p>
        </div>
    @else
        <div class="offers-grid">
            @foreach($offers as $offer)
            @php
                $dateBadge = null;
                if ($offer->starts_at && $offer->ends_at) {
                    $dateBadge = strtoupper($offer->starts_at->format('d M Y') . ' - ' . $offer->ends_at->format('d M Y'));
                } elseif ($offer->ends_at) {
                    $dateBadge = 'UNTIL ' . strtoupper($offer->ends_at->format('d M Y'));
                }
                // Prefer a dedicated card thumbnail; fall back to the wide banner.
                $hasThumb = (bool) $offer->getFirstMediaUrl('offer_thumbnail');
                $cardColl = $hasThumb ? 'offer_thumbnail' : 'offer_banner';
            @endphp
            <div class="offer-card">
                <a href="{{ route('offers.show', $offer) }}" class="offer-card-media" aria-label="{{ $offer->title }}">
                    <x-media-image :model="$offer" :collection="$cardColl" size="medium" :alt="$offer->title"
                        class="offer-card-img {{ $hasThumb ? 'is-thumb' : 'is-banner' }}">
                        <div class="offer-card-ph"><x-app-icon name="gift" :size="34" style="color:#d1d5db;" /></div>
                    </x-media-image>
                    @if($dateBadge)
                        <span class="offer-card-date"><x-app-icon name="clock" :size="12" /> {{ $dateBadge }}</span>
                    @endif
                </a>
                <div class="offer-card-body">
                    <h3 class="offer-card-title"><a href="{{ route('offers.show', $offer) }}">{{ $offer->title }}</a></h3>
                    @if($offer->subtitle)
                        <p class="offer-card-sub">{{ $offer->subtitle }}</p>
                    @endif
                    <a href="{{ route('offers.show', $offer) }}" class="offer-card-btn">View Details</a>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    .offers-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 22px;
        align-items: stretch;
    }
    .offer-card {
        background: #fff;
        border: 1px solid #eef0f2;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.25s, transform 0.25s;
    }
    .offer-card:hover { box-shadow: 0 12px 30px rgba(0,0,0,0.10); transform: translateY(-3px); }
    /* Square media area. A dedicated thumbnail (admin-uploaded) fills it via cover; if only
       the wide banner exists, it's shown contained (letterboxed) so nothing gets cropped. */
    .offer-card-media { position: relative; display: block; aspect-ratio: 1/1; background: #f8fafc; overflow: hidden; }
    .offer-card-img { width: 100%; height: 100%; display: block; }
    .offer-card-img.is-thumb  { object-fit: cover; }
    .offer-card-img.is-banner { object-fit: contain; }
    .offer-card-ph { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg,#f9fafb,#f3f4f6); }
    .offer-card-date {
        position: absolute; left: 12px; right: 12px; bottom: 12px;
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        background: rgba(255,255,255,0.96); color: #0f172a;
        font-size: 0.72rem; font-weight: 600; padding: 6px 10px; border-radius: 999px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.12); white-space: nowrap;
    }
    .offer-card-body { padding: 16px 16px 18px; display: flex; flex-direction: column; gap: 6px; flex: 1; text-align: center; }
    .offer-card-title { font-size: 1rem; font-weight: 700; line-height: 1.3; margin: 0; }
    .offer-card-title a { color: #111827; text-decoration: none; }
    .offer-card-title a:hover { color: #f97316; }
    .offer-card-sub { font-size: 0.82rem; color: #6b7280; margin: 0; line-height: 1.4; }
    .offer-card-btn {
        margin-top: auto; display: block;
        background: #f97316; color: #fff; text-decoration: none;
        font-size: 0.85rem; font-weight: 700; padding: 9px 14px; border-radius: 8px;
        transition: background 0.2s;
    }
    .offer-card-btn:hover { background: #ea6c0a; }

    @media (max-width: 1023px) { .offers-grid { grid-template-columns: repeat(3, 1fr); gap: 18px; } }
    @media (max-width: 767px)  { .offers-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; } }
    @media (max-width: 479px)  { .offers-grid { grid-template-columns: 1fr; } }
</style>
@endsection
