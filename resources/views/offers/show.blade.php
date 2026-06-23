@extends('layouts.app')
@section('title', $offer->meta_title ?? $offer->title)
@section('meta_description', $offer->meta_description ?? ($offer->subtitle ?? \Illuminate\Support\Str::limit(strip_tags((string) $offer->body), 160)))

@section('content')
@include('components.breadcrumb', ['items' => [
    ['label' => 'Offers', 'url' => route('offers.index')],
    ['label' => $offer->title],
]])

<div class="container-custom px-4 sm:px-6 lg:px-8" style="padding-top:16px; padding-bottom:56px;">
    <div style="max-width:860px; margin:0 auto;">

        {{-- Header --}}
        <div style="text-align:center; margin-bottom:20px;">
            <h1 style="font-size:1.7rem; font-weight:800; color:#111827; margin:0; line-height:1.25;">{{ $offer->title }}</h1>
            @if($offer->subtitle)
                <p style="font-size:0.95rem; color:#6b7280; margin:8px 0 0;">{{ $offer->subtitle }}</p>
            @endif
            @php
                $dateBadge = null;
                if ($offer->starts_at && $offer->ends_at) {
                    $dateBadge = $offer->starts_at->format('d M Y') . ' – ' . $offer->ends_at->format('d M Y');
                } elseif ($offer->ends_at) {
                    $dateBadge = 'Valid until ' . $offer->ends_at->format('d M Y');
                }
            @endphp
            @if($dateBadge)
                <span style="display:inline-flex; align-items:center; gap:6px; margin-top:12px; background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; font-size:0.78rem; font-weight:600; padding:5px 14px; border-radius:999px;">
                    <x-app-icon name="clock" :size="13" /> {{ $dateBadge }}
                </span>
            @endif
        </div>

        {{-- Banner --}}
        @if($offer->getFirstMediaUrl('offer_banner'))
        <div style="border-radius:14px; overflow:hidden; margin-bottom:24px; box-shadow:0 4px 24px rgba(0,0,0,0.08);">
            <x-media-image :model="$offer" collection="offer_banner" size="large" :eager="true" :alt="$offer->title"
                style="width:100%; height:auto; display:block;" />
        </div>
        @endif

        {{-- Body --}}
        @if($offer->body)
        <div class="offer-body prose max-w-none" style="color:#374151; line-height:1.7;">
            {!! $offer->body !!}
        </div>
        @endif

        {{-- CTA --}}
        @if($offer->cta_url)
        <div style="text-align:center; margin-top:28px;">
            <a href="{{ $offer->cta_url }}" style="display:inline-flex; align-items:center; gap:8px; background:#f97316; color:#fff; text-decoration:none; font-size:0.95rem; font-weight:700; padding:13px 32px; border-radius:10px; transition:background 0.2s;"
               onmouseover="this.style.background='#ea6c0a'" onmouseout="this.style.background='#f97316'">
                {{ $offer->cta_label ?: 'Shop Now' }}
                <x-app-icon name="angle-small-right" :size="16" />
            </a>
        </div>
        @endif

        {{-- Countdown --}}
        @if($offer->is_live)
        <div id="offer-countdown" data-ends="{{ $offer->ends_at->toIso8601String() }}"
             style="margin-top:32px; display:flex; flex-direction:column; align-items:center;">
            <span style="position:relative; top:14px; background:#fff; padding:0 12px; font-size:0.7rem; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:#f97316;">Ending In</span>
            <div style="display:flex; gap:10px; border:1px solid #e5e7eb; border-radius:14px; padding:18px 22px;">
                @foreach(['days' => 'Days', 'hours' => 'Hours', 'minutes' => 'Minutes', 'seconds' => 'Seconds'] as $k => $label)
                <div style="text-align:center; min-width:52px;">
                    <div data-cd="{{ $k }}" style="background:#111827; color:#fff; font-size:1.25rem; font-weight:800; border-radius:8px; padding:8px 0; font-variant-numeric:tabular-nums;">00</div>
                    <div style="font-size:0.62rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:0.08em; margin-top:6px;">{{ $label }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- More offers --}}
    @if($more->isNotEmpty())
    <div style="max-width:1100px; margin:48px auto 0;">
        <h2 style="font-size:1.1rem; font-weight:800; color:#111827; margin:0 0 18px;">More offers</h2>
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:18px;" class="more-offers-grid">
            @foreach($more as $m)
            <a href="{{ route('offers.show', $m) }}" style="background:#fff; border:1px solid #eef0f2; border-radius:12px; overflow:hidden; text-decoration:none; transition:box-shadow 0.2s, transform 0.2s;"
               onmouseover="this.style.boxShadow='0 10px 24px rgba(0,0,0,0.10)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='none';this.style.transform='none'">
                <div style="aspect-ratio:2/1; background:#f8fafc;">
                    <x-media-image :model="$m" collection="offer_banner" size="medium" :alt="$m->title" style="width:100%;height:100%;object-fit:cover;display:block;">
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;"><x-app-icon name="gift" :size="28" style="color:#d1d5db;" /></div>
                    </x-media-image>
                </div>
                <div style="padding:12px 14px;">
                    <div style="font-size:0.9rem; font-weight:700; color:#111827; line-height:1.3;">{{ $m->title }}</div>
                    @if($m->subtitle)<div style="font-size:0.76rem; color:#6b7280; margin-top:3px;">{{ \Illuminate\Support\Str::limit($m->subtitle, 60) }}</div>@endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>

<style>
    .offer-body h1,.offer-body h2,.offer-body h3 { color:#111827; font-weight:700; margin:1.4em 0 0.5em; line-height:1.3; }
    .offer-body h2 { font-size:1.2rem; } .offer-body h3 { font-size:1.05rem; }
    .offer-body p { margin:0 0 1em; }
    .offer-body ul,.offer-body ol { margin:0 0 1em 1.4em; } .offer-body li { margin:0.3em 0; }
    .offer-body a { color:#f97316; text-decoration:underline; }
    .offer-body img { max-width:100%; height:auto; border-radius:10px; margin:1em 0; }
    @media (max-width:767px) { .more-offers-grid { grid-template-columns:1fr !important; } }
</style>

@if($offer->is_live)
<script>
(function () {
    var el = document.getElementById('offer-countdown');
    if (!el) return;
    var end = new Date(el.getAttribute('data-ends')).getTime();
    var fields = {};
    ['days','hours','minutes','seconds'].forEach(function (k) { fields[k] = el.querySelector('[data-cd="'+k+'"]'); });
    var pad = function (n) { return (n < 10 ? '0' : '') + n; };

    function tick() {
        var diff = end - Date.now();
        if (diff <= 0) {
            Object.values(fields).forEach(function (f) { if (f) f.textContent = '00'; });
            el.style.display = 'none';
            return false;
        }
        var s = Math.floor(diff / 1000);
        if (fields.days)    fields.days.textContent    = pad(Math.floor(s / 86400));
        if (fields.hours)   fields.hours.textContent   = pad(Math.floor((s % 86400) / 3600));
        if (fields.minutes) fields.minutes.textContent = pad(Math.floor((s % 3600) / 60));
        if (fields.seconds) fields.seconds.textContent = pad(s % 60);
        return true;
    }

    if (tick() !== false) {
        var timer = setInterval(function () { if (tick() === false) clearInterval(timer); }, 1000);
    }
})();
</script>
@endif
@endsection
