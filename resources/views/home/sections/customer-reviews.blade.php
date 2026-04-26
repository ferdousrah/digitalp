@props(['section', 'testimonials'])
@if($testimonials->count())
<section style="background:{{ $section->bg_color }}; padding:{{ $section->padding_y }}px 0;">
    <div class="container-custom px-4 sm:px-6 lg:px-8">
        @include('home.sections._section-header', ['section' => $section])
        <div class="hs-carousel-wrap" style="position:relative;">
            <div class="hs-carousel" id="review-carousel-{{ $section->id }}"
                style="display:flex; gap:20px; overflow-x:auto; scroll-snap-type:x mandatory; scrollbar-width:none; -ms-overflow-style:none;"
                onmousedown="hsCarouselDragStart(event,this)" onmousemove="hsCarouselDragMove(event,this)" onmouseup="hsCarouselDragEnd(event,this)" onmouseleave="hsCarouselDragEnd(event,this)">
                @foreach($testimonials as $t)
                <div style="flex:0 0 calc((100% - {{ ($section->desktop_visible - 1) * 20 }}px) / {{ $section->desktop_visible }}); scroll-snap-align:start; background:#fff; border-radius:16px; padding:24px; border:1px solid #e5e7eb; box-shadow:0 2px 8px rgba(0,0,0,0.06);" class="review-item-{{ $section->id }}">
                    <div style="display:flex; gap:2px; margin-bottom:12px;">
                        @for($i=1;$i<=5;$i++)<svg style="width:16px;height:16px;color:{{ $i <= ($t->rating ?? 5) ? '#f59e0b' : '#d1d5db' }};" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endfor
                    </div>
                    @if($t->content)<p style="font-size:0.9rem; color:#374151; line-height:1.6; margin:0 0 16px; font-style:italic;">"{{ Str::limit($t->content, 160) }}"</p>@endif
                    <div style="display:flex; align-items:center; gap:10px;">
                        @if($t->getFirstMediaUrl('testimonial_image'))
                            <img src="{{ $t->getFirstMediaUrl('testimonial_image') }}" loading="lazy" decoding="async" alt="{{ $t->name }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                        @else
                            <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#22c55e);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1rem;">{{ strtoupper(substr($t->name,0,1)) }}</div>
                        @endif
                        <div>
                            <p style="font-weight:600;font-size:0.875rem;color:#111827;margin:0;">{{ $t->name }}</p>
                            @if($t->position)<p style="font-size:0.75rem;color:#9ca3af;margin:0;">{{ $t->position }}</p>@endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @include('home.sections._carousel-nav', ['id' => 'review-carousel-'.$section->id])
        </div>
        <style>
        #review-carousel-{{ $section->id }}::-webkit-scrollbar { display:none; }
        @media(max-width:767px) {
            #review-carousel-{{ $section->id }} .review-item-{{ $section->id }} {
                flex: 0 0 calc((100% - {{ ($section->mobile_visible - 1) * 20 }}px) / {{ $section->mobile_visible }}) !important;
            }
        }
        </style>
    </div>
</section>
@endif
