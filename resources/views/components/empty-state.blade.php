@props([
    /** Preset icon: cart | wishlist | search | orders | inbox | image | error */
    'icon'      => 'inbox',
    /** Headline string */
    'title'     => '',
    /** Subtext / body string */
    'body'      => '',
    /** Primary CTA label (omit to hide) */
    'ctaLabel'  => null,
    /** Primary CTA href */
    'ctaHref'   => null,
    /** Style preset: 'card' (white card, used inside containers) | 'plain' (no card chrome) */
    'variant'   => 'card',
    /** Tighter for inline contexts (cart drawer, modals) */
    'compact'   => false,
])

@php
    $iconPaths = [
        'cart'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>',
        'wishlist' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
        'search'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
        'orders'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
        'inbox'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-3.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-1.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 007.586 13H4"/>',
        'image'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
        'error'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    ];
    $iconSvg = $iconPaths[$icon] ?? $iconPaths['inbox'];
    $iconSize = $compact ? 32 : 42;
    $chipSize = $compact ? 64 : 88;
    $padY    = $compact ? 32 : 60;
@endphp

<div {{ $attributes->merge([
    'style' => ($variant === 'card' ? 'background:#fff; border:1px solid #e2e8f0; border-radius:14px;' : '')
        . ' padding:' . $padY . 'px 24px; text-align:center;'
]) }}>
    <div style="display:flex; flex-direction:column; align-items:center; gap:14px;">
        <div style="width:{{ $chipSize }}px; height:{{ $chipSize }}px; border-radius:50%; background:#f8fafc; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg style="width:{{ $iconSize }}px; height:{{ $iconSize }}px; color:#cbd5e1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $iconSvg !!}</svg>
        </div>

        <div style="max-width:340px;">
            @if($title)
                <p style="margin:0 0 6px; font-size:{{ $compact ? '0.95rem' : '1.05rem' }}; font-weight:700; color:#0f172a; line-height:1.3;">{{ $title }}</p>
            @endif
            @if($body)
                <p style="margin:0; font-size:{{ $compact ? '0.82rem' : '0.9rem' }}; color:#94a3b8; line-height:1.55;">{{ $body }}</p>
            @endif
            {{-- Optional content slot for richer empty states (e.g. multiple CTAs) --}}
            {{ $slot ?? '' }}
        </div>

        @if($ctaLabel && $ctaHref)
            <a href="{{ $ctaHref }}" style="display:inline-flex; align-items:center; gap:6px; padding:11px 22px; background:#f97316; color:#fff; font-size:0.86rem; font-weight:700; letter-spacing:0.04em; text-transform:uppercase; text-decoration:none; border-radius:10px; transition:background 0.2s; margin-top:4px;" onmouseover="this.style.background='#ea6c0a'" onmouseout="this.style.background='#f97316'">
                {{ $ctaLabel }}
                <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        @endif
    </div>
</div>
