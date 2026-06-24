@props(['section', 'brands'])
@if($brands->count())
<section style="background:{{ $section->bg_color }}; padding:{{ $section->padding_y }}px 0;">
    <div class="container-custom px-4 sm:px-6 lg:px-8">
        @include('home.sections._section-header', ['section' => $section])
        <div class="hs-carousel-wrap" style="position:relative;">
            <div class="hs-carousel" id="brand-carousel-{{ $section->id }}"
                data-autoscroll="0.5"
                style="display:flex; gap:20px; overflow-x:auto; scrollbar-width:none; align-items:center;"
                onmousedown="hsCarouselDragStart(event,this)" onmousemove="hsCarouselDragMove(event,this)" onmouseup="hsCarouselDragEnd(event,this)" onmouseleave="hsCarouselDragEnd(event,this)">
                @foreach($brands as $brand)
                <div style="flex:0 0 calc((100% - {{ ($section->desktop_visible - 1) * 20 }}px) / {{ $section->desktop_visible }}); scroll-snap-align:start; display:flex; align-items:center; justify-content:center; padding:12px 18px; min-height:96px; background:#fff; border-radius:10px; border:1px solid #e5e7eb; transition:all 0.2s;" class="brand-item-{{ $section->id }}"
                    onmouseover="this.style.borderColor='var(--color-primary,#16a34a)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'"
                    onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                    <x-media-image :model="$brand" collection="brand_logo" size="medium" :alt="$brand->name" class="brand-logo-img">
                        <span style="font-weight:700; font-size:1rem; color:#374151;">{{ $brand->name }}</span>
                    </x-media-image>
                </div>
                @endforeach
            </div>
            @include('home.sections._carousel-nav', ['id' => 'brand-carousel-'.$section->id])
        </div>
        <style>
        /* Fill most of the card so logos read clearly (works for both wide wordmarks and square marks) */
        .brand-logo-img { max-height:72px; max-width:100%; width:auto; object-fit:contain; filter:grayscale(1); transition:filter 0.2s; }
        .brand-logo-img:hover { filter:grayscale(0); }
        @media(max-width:767px) { .brand-logo-img { max-height:60px; } }
        #brand-carousel-{{ $section->id }}::-webkit-scrollbar { display:none; }
        @media(max-width:767px) {
            #brand-carousel-{{ $section->id }} .brand-item-{{ $section->id }} {
                flex: 0 0 calc((100% - {{ ($section->mobile_visible - 1) * 20 }}px) / {{ $section->mobile_visible }}) !important;
            }
        }
        </style>
    </div>
</section>
@endif
