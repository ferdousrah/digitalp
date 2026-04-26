@props(['section', 'brands'])
@if($brands->count())
<section style="background:{{ $section->bg_color }}; padding:{{ $section->padding_y }}px 0;">
    <div class="container-custom px-4 sm:px-6 lg:px-8">
        @include('home.sections._section-header', ['section' => $section])
        <div class="hs-carousel-wrap" style="position:relative;">
            <div class="hs-carousel" id="brand-carousel-{{ $section->id }}"
                style="display:flex; gap:20px; overflow-x:auto; scroll-snap-type:x mandatory; scrollbar-width:none; align-items:center;"
                onmousedown="hsCarouselDragStart(event,this)" onmousemove="hsCarouselDragMove(event,this)" onmouseup="hsCarouselDragEnd(event,this)" onmouseleave="hsCarouselDragEnd(event,this)">
                @foreach($brands as $brand)
                <div style="flex:0 0 calc((100% - {{ ($section->desktop_visible - 1) * 20 }}px) / {{ $section->desktop_visible }}); scroll-snap-align:start; display:flex; align-items:center; justify-content:center; padding:16px; background:#fff; border-radius:10px; border:1px solid #e5e7eb; transition:all 0.2s;" class="brand-item-{{ $section->id }}"
                    onmouseover="this.style.borderColor='var(--color-primary,#16a34a)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'"
                    onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                    @if($brand->getFirstMediaUrl('brand_logo'))
                        <img src="{{ $brand->getFirstMediaUrl('brand_logo') }}" loading="lazy" decoding="async" alt="{{ $brand->name }}" style="max-height:48px; max-width:100%; object-fit:contain; filter:grayscale(1); transition:filter 0.2s;"
                            onmouseover="this.style.filter='grayscale(0)'" onmouseout="this.style.filter='grayscale(1)'">
                    @else
                        <span style="font-weight:700; font-size:1rem; color:#374151;">{{ $brand->name }}</span>
                    @endif
                </div>
                @endforeach
            </div>
            @include('home.sections._carousel-nav', ['id' => 'brand-carousel-'.$section->id])
        </div>
        <style>
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
