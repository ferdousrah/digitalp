@extends('layouts.app')
@section('title', \App\Services\SettingService::get('meta_title', \App\Services\SettingService::get('site_name', config('app.name'))))

{{-- Preload the first hero slide image — usually the page's LCP element, worth prioritising --}}
@php $__heroImg = optional($sliders->first())->getFirstMediaUrl('slide_image'); @endphp
@if($__heroImg)
    @push('seo')
        <link rel="preload" as="image" href="{{ $__heroImg }}" fetchpriority="high">
    @endpush
@endif

@section('content')
{{-- Visually-hidden page heading — gives the page a single h1 for screen readers / SEO without
     duplicating the hero slide titles in the visible UI. --}}
<h1 class="sr-only">{{ \App\Services\SettingService::get('site_name', config('app.name')) }} — Online store</h1>

<!-- Hero Section -->
<section class="hero-section bg-surface-100" style="padding: 12px 0;">
    <div class="container-custom">
        <style>
            #hero-grid {
                display: grid;
                grid-template-columns: {{ ($heroShowBanners && ($banner1 || $banner2)) ? "{$heroSliderColWidth}fr {$heroBannerColWidth}fr" : '1fr' }};
                grid-template-rows: {{ $heroSliderHeight }}px;
                gap: 12px;
            }
            #hero-slider,
            .hero-banner-row {
                height: 100%;
            }
            /* Banner zoom-in on hover */
            .hero-banner-row > div img {
                transition: transform 0.45s ease;
            }
            .hero-banner-row > div:hover img {
                transform: scale(1.05);
            }
            /* Arrow hover behaviour */
            #slide-prev, #slide-next {
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.25s ease, visibility 0.25s ease, background 0.2s;
            }
            #hero-slider:hover #slide-prev,
            #hero-slider:hover #slide-next {
                opacity: 1;
                visibility: visible;
            }
            @media(max-width:767px) {
                #hero-grid { grid-template-columns: 1fr; grid-template-rows: auto; }
                #hero-slider { height: 240px !important; }
                .hero-banner-row { flex-direction: row !important; }
                .hero-banner-row > div { min-height: 130px !important; }
                #slide-prev, #slide-next { opacity:1; visibility:visible; }
            }
        </style>
        <div id="hero-grid">

            {{-- LEFT: Image Slider --}}
            <div id="hero-slider" style="position:relative; overflow:hidden; border-radius:10px; background:#1a1a2e;">

                {{-- Slides --}}
                @forelse($sliders as $i => $slide)
                @php $img = $slide->getFirstMediaUrl('slide_image'); @endphp
                <div class="hero-slide" data-index="{{ $i }}"
                     style="position:absolute; inset:0; opacity:{{ $i === 0 ? 1 : 0 }}; transition:opacity 0.6s ease; z-index:{{ $i === 0 ? 1 : 0 }};">
                    @if($img)
                        <img src="{{ $img }}" alt="{{ $slide->title }}"
                             @if($i === 0) decoding="async" fetchpriority="high" @else loading="lazy" decoding="async" @endif
                             style="width:100%; height:100%; object-fit:cover; display:block;">
                    @else
                        <div style="width:100%; height:100%; background:linear-gradient(135deg,#1a1a2e,#16213e,#0f3460); display:flex; align-items:center; justify-content:center;">
                            <svg style="width:80px;height:80px;color:rgba(255,255,255,0.2)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                    {{-- Full-slide link overlay (behind text) --}}
                    @if($slide->link_url)
                    <a href="{{ $slide->link_url }}" style="position:absolute; inset:0; z-index:1;" aria-label="{{ $slide->title ?? 'Slide link' }}"></a>
                    @endif
                    {{-- Overlay text --}}
                    @if($slide->title || $slide->subtitle || $slide->button_text)
                    <div style="position:absolute; inset:0; background:linear-gradient(to right, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.1) 70%, transparent 100%); display:flex; align-items:center; padding:40px; z-index:2;">
                        <div style="max-width:480px;">
                            @if($slide->title)
                            <h2 style="font-size:clamp(1.4rem,3vw,2.2rem); font-weight:700; color:#fff; line-height:1.2; margin:0 0 12px;">{{ $slide->title }}</h2>
                            @endif
                            @if($slide->subtitle)
                            <p style="font-size:clamp(0.9rem,1.5vw,1.1rem); color:rgba(255,255,255,0.85); margin:0 0 20px; line-height:1.5;">{{ $slide->subtitle }}</p>
                            @endif
                            @if($slide->button_text && $slide->button_url)
                            <a href="{{ $slide->button_url }}" style="display:inline-block; background:#16a34a; color:#fff; font-weight:600; padding:10px 24px; border-radius:6px; text-decoration:none; font-size:0.95rem; transition:background 0.2s; position:relative; z-index:3;">{{ $slide->button_text }}</a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                @empty
                {{-- Fallback when no sliders configured --}}
                <div style="width:100%; height:100%; background:linear-gradient(135deg,#1a1a2e,#16213e,#0f3460); display:flex; align-items:center; padding:40px;">
                    <div style="max-width:480px;">
                        <h2 style="font-size:2rem; font-weight:700; color:#fff; margin:0 0 12px;">Welcome to {{ \App\Services\SettingService::get('site_name', config('app.name')) }}</h2>
                        <p style="color:rgba(255,255,255,0.8); margin:0 0 20px;">Your one-stop shop for laptops, printers &amp; accessories.</p>
                        <a href="{{ route('products.index') }}" style="display:inline-block; background:#16a34a; color:#fff; font-weight:600; padding:10px 24px; border-radius:6px; text-decoration:none;">Shop Now</a>
                    </div>
                </div>
                @endforelse

                {{-- Prev / Next arrows (only when more than 1 slide) --}}
                @if($sliders->count() > 1)
                <button id="slide-prev" aria-label="Previous"
                        style="position:absolute; left:12px; top:50%; transform:translateY(-50%); z-index:10; width:42px; height:42px; border-radius:50%; background:rgba(255,255,255,0.25); border:none; cursor:pointer; color:#fff; font-size:20px; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(6px);">&#10094;</button>
                <button id="slide-next" aria-label="Next"
                        style="position:absolute; right:12px; top:50%; transform:translateY(-50%); z-index:10; width:42px; height:42px; border-radius:50%; background:rgba(255,255,255,0.25); border:none; cursor:pointer; color:#fff; font-size:20px; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(6px);">&#10095;</button>

                {{-- Dot indicators --}}
                <div style="position:absolute; bottom:14px; left:50%; transform:translateX(-50%); display:flex; gap:6px; z-index:10;">
                    @foreach($sliders as $i => $slide)
                    <button class="slide-dot" data-index="{{ $i }}"
                            style="width:{{ $i === 0 ? '22px' : '8px' }}; height:8px; border-radius:4px; border:none; cursor:pointer; background:{{ $i === 0 ? '#fff' : 'rgba(255,255,255,0.4)' }}; transition:all 0.3s; padding:0;"></button>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- RIGHT: Banner column — only renders if at least one banner is active --}}
            @if($heroShowBanners && ($banner1 || $banner2))
            <div class="hero-banner-row" style="display:flex; flex-direction:column; gap:12px;">

                {{-- Banner 1 — flex:1 so it stretches to fill remaining height --}}
                @if($banner1)
                @php $b1img = $banner1->getFirstMediaUrl('slide_image'); @endphp
                <div style="flex:1; border-radius:10px; overflow:hidden; position:relative; min-height:120px;">
                    <img src="{{ $b1img }}" alt="{{ $banner1->title ?? '' }}" decoding="async"
                         style="width:100%; height:100%; object-fit:cover; display:block;">
                    @if($banner1->link_url)
                    <a href="{{ $banner1->link_url }}" style="position:absolute; inset:0; cursor:pointer;"
                       aria-label="{{ $banner1->title ?? 'Banner' }}"></a>
                    @endif
                </div>
                @endif

                {{-- Banner 2 — flex:1 so it stretches; if Banner 1 is missing, this fills full height --}}
                @if($banner2)
                @php $b2img = $banner2->getFirstMediaUrl('slide_image'); @endphp
                <div style="flex:1; border-radius:10px; overflow:hidden; position:relative; min-height:120px;">
                    <img src="{{ $b2img }}" alt="{{ $banner2->title ?? '' }}" decoding="async"
                         style="width:100%; height:100%; object-fit:cover; display:block;">
                    @if($banner2->link_url)
                    <a href="{{ $banner2->link_url }}" style="position:absolute; inset:0; cursor:pointer;"
                       aria-label="{{ $banner2->title ?? 'Banner' }}"></a>
                    @endif
                </div>
                @endif

            </div>{{-- end right banners --}}
            @endif
        </div>{{-- end grid --}}
    </div>{{-- end container --}}

    {{-- Slider JavaScript --}}
    @if($sliders->count() > 1)
    <script>
    (function() {
        const slider = document.getElementById('hero-slider');
        const slides = document.querySelectorAll('.hero-slide');
        const dots   = document.querySelectorAll('.slide-dot');
        const total  = slides.length;
        let current  = 0;
        let timer;

        function goTo(n) {
            slides[current].style.opacity  = '0';
            slides[current].style.zIndex   = '0';
            dots[current].style.width      = '8px';
            dots[current].style.background = 'rgba(255,255,255,0.4)';
            current = (n + total) % total;
            slides[current].style.opacity  = '1';
            slides[current].style.zIndex   = '1';
            dots[current].style.width      = '22px';
            dots[current].style.background = '#fff';
        }

        // Skip auto-advance for users who prefer reduced motion (manual nav still works)
        var reducedMotion = window.dsReducedMotion && window.dsReducedMotion();
        function autoPlay() { if (reducedMotion) return; timer = setInterval(() => goTo(current + 1), 5000); }
        function resetTimer() { clearInterval(timer); autoPlay(); }

        document.getElementById('slide-prev').addEventListener('click', () => { goTo(current - 1); resetTimer(); });
        document.getElementById('slide-next').addEventListener('click', () => { goTo(current + 1); resetTimer(); });
        dots.forEach(dot => dot.addEventListener('click', () => { goTo(+dot.dataset.index); resetTimer(); }));

        // Pause autoplay while hovering
        slider.addEventListener('mouseenter', () => clearInterval(timer));
        slider.addEventListener('mouseleave', () => autoPlay());

        // ── Touch swipe ──────────────────────────────────────────
        let touchStartX = 0, touchStartY = 0, isSwiping = false;

        slider.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            isSwiping   = true;
        }, { passive: true });

        slider.addEventListener('touchmove', (e) => {
            if (!isSwiping) return;
            const dx = e.touches[0].clientX - touchStartX;
            const dy = e.touches[0].clientY - touchStartY;
            if (Math.abs(dx) > Math.abs(dy)) e.preventDefault(); // block scroll while swiping
        }, { passive: false });

        slider.addEventListener('touchend', (e) => {
            if (!isSwiping) return;
            isSwiping   = false;
            const dx = e.changedTouches[0].clientX - touchStartX;
            const dy = e.changedTouches[0].clientY - touchStartY;
            if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) {
                dx < 0 ? goTo(current + 1) : goTo(current - 1);
                resetTimer();
            }
        }, { passive: true });

        // ── Mouse drag (desktop) ──────────────────────────────────
        let mouseStartX = 0, isDragging = false;

        slider.addEventListener('mousedown',  (e) => { mouseStartX = e.clientX; isDragging = true; slider.style.cursor = 'grabbing'; });
        slider.addEventListener('mouseleave', ()  => { isDragging  = false; slider.style.cursor = ''; });
        slider.addEventListener('mouseup',    (e) => {
            if (!isDragging) return;
            isDragging = false;
            slider.style.cursor = '';
            const dx = e.clientX - mouseStartX;
            if (Math.abs(dx) > 50) { dx < 0 ? goTo(current + 1) : goTo(current - 1); resetTimer(); }
        });

        autoPlay();
    })();
    </script>
    @endif
</section>

{{-- Dynamic Homepage Sections --}}
@foreach($sections as $section)
    @php $data = $sectionData[$section->key] ?? null; @endphp
    @switch($section->type)
        @case('category')
            @include('home.sections.shop-by-category', ['section' => $section, 'categories' => $data ?? collect()])
            @break
        @case('product')
            @include('home.sections.product-section', ['section' => $section, 'products' => $data ?? collect()])
            @break
        @case('offer_banner')
            @include('home.sections.offer-banner', ['section' => $section, 'banners' => $data ?? collect()])
            @break
        @case('reviews')
            @include('home.sections.customer-reviews', ['section' => $section, 'testimonials' => $data ?? collect()])
            @break
        @case('blog')
            @include('home.sections.blog', ['section' => $section, 'posts' => $data ?? collect()])
            @break
        @case('brands')
            @include('home.sections.brands-carousel', ['section' => $section, 'brands' => $data ?? collect()])
            @break
        @case('faq')
            @include('home.sections.faq', ['section' => $section, 'faqs' => $data ?? collect()])
            @break
        @case('seo')
            @include('home.sections.seo-block', ['section' => $section])
            @break
    @endswitch
@endforeach
{{-- END dynamic sections --}}

@endsection
