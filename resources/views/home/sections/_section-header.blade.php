@props(['section', 'viewAllUrl' => null, 'viewAllText' => 'VIEW ALL ITEMS'])
@php
    $primaryColor  = \App\Services\SettingService::get('color_primary', '#16a34a');
    $accentColor   = \App\Services\SettingService::get('color_accent',  '#f97316');
    $dividerColor  = !empty($section->extra['divider_color']) ? $section->extra['divider_color'] : $primaryColor;
    $hasViewAll    = !empty($viewAllUrl);
    $uid           = 'sh' . $section->id;
    $headingAlign  = $section->extra['heading_align'] ?? 'left';
    $showDivider   = $section->extra['show_divider'] ?? true;
@endphp

@if($section->title || $section->subtitle || $hasViewAll)
<div class="{{ $uid }}-wrap gsap-fade-up" style="margin-bottom:28px; text-align:{{ $headingAlign }};">

    {{-- Row 1: Title + View All --}}
    <div style="display:flex; align-items:flex-start; justify-content:{{ $headingAlign === 'center' ? 'center' : ($headingAlign === 'right' ? 'flex-end' : 'space-between') }}; gap:16px; margin-bottom:{{ $section->subtitle ? '4px' : '12px' }};">
        @if($section->title)
        <h2 class="{{ $uid }}-title"
            style="font-weight:{{ $section->heading_weight }}; color:{{ $section->heading_color }}; margin:0; line-height:1.2;">
            {{ $section->title }}
        </h2>
        @endif

        @if($hasViewAll)
        <a href="{{ $viewAllUrl }}" class="{{ $uid }}-va" style="flex-shrink:0; padding-top:4px; margin-left:{{ $headingAlign === 'left' ? 'auto' : '16px' }};">
            <span>{{ $viewAllText }}</span>
            <span class="{{ $uid }}-arrow">
                <svg style="width:14px;height:14px;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </span>
        </a>
        @endif
    </div>

    {{-- Row 2: Subtitle --}}
    @if($section->subtitle)
    <p class="{{ $uid }}-sub"
        style="font-weight:{{ $section->subheading_weight }}; color:{{ $section->subheading_color }}; margin:0 0 14px; line-height:1.5;">
        {{ $section->subtitle }}
    </p>
    @endif

    {{-- Row 3: Animated divider --}}
    @if($showDivider)
    <div class="{{ $uid }}-divider" style="display:flex; align-items:center; gap:8px; height:4px;">
        {{-- Short solid accent segment --}}
        <div class="{{ $uid }}-solid" style="width:0; height:3px; background:{{ $dividerColor }}; border-radius:2px; flex-shrink:0; transition:none;"></div>
        {{-- Dashed line track --}}
        <div style="flex:1; height:2px; position:relative; overflow:hidden;">
            <div style="position:absolute; inset:0; background:repeating-linear-gradient(to right, #e5e7eb 0px, #e5e7eb 6px, transparent 6px, transparent 12px);"></div>
            <div class="{{ $uid }}-line" style="position:absolute; inset:0; background:repeating-linear-gradient(to right, {{ $dividerColor }}55 0px, {{ $dividerColor }}55 6px, transparent 6px, transparent 12px); transform:scaleX(0); transform-origin:left center;"></div>
        </div>
    </div>
    @endif

</div>

<style>
.{{ $uid }}-title { font-size: {{ $section->heading_size_desktop }}; }
.{{ $uid }}-sub   { font-size: {{ $section->subheading_size_desktop }}; }

/* View All */
.{{ $uid }}-va {
    display: inline-flex;
    align-items: center;
    gap: 0;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    text-decoration: none;
    color: {{ $primaryColor }};
    border-bottom: 1.5px solid {{ $primaryColor }};
    padding-bottom: 2px;
    transition: color 0.2s, border-color 0.2s, gap 0.3s ease;
    white-space: nowrap;
}
.{{ $uid }}-va:hover { color: {{ $accentColor }}; border-color: {{ $accentColor }}; gap: 7px; }
.{{ $uid }}-arrow {
    display: flex; align-items: center;
    max-width: 0; opacity: 0; overflow: hidden;
    transform: translateX(-6px);
    transition: max-width 0.3s ease, opacity 0.25s ease 0.05s, transform 0.3s ease;
}
.{{ $uid }}-va:hover .{{ $uid }}-arrow { max-width: 20px; opacity: 1; transform: translateX(0); }

/* Animated line */
@keyframes {{ $uid }}-draw {
    from { transform: scaleX(0); }
    to   { transform: scaleX(1); }
}
@keyframes {{ $uid }}-solid {
    from { width: 0; }
    to   { width: 28px; }
}
.{{ $uid }}-line.is-visible {
    animation: {{ $uid }}-draw 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}
.{{ $uid }}-solid.is-visible {
    animation: {{ $uid }}-solid 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

@media (max-width: 767px) {
    .{{ $uid }}-title { font-size: {{ $section->heading_size_mobile }}; }
    .{{ $uid }}-sub   { font-size: {{ $section->subheading_size_mobile }}; }
}
</style>

@if($showDivider)
<script>
(function() {
    var divider = document.querySelector('.{{ $uid }}-divider');
    if (!divider) return;
    var line  = divider.querySelector('.{{ $uid }}-line');
    var solid = divider.querySelector('.{{ $uid }}-solid');
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                if (solid) solid.classList.add('is-visible');
                if (line)  line.classList.add('is-visible');
                observer.disconnect();
            }
        });
    }, { threshold: 0.3 });
    observer.observe(divider);
})();
</script>
@endif
@endif
