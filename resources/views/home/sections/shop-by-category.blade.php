@props(['section', 'categories'])
<section style="background:{{ $section->bg_color }}; padding:{{ $section->padding_y }}px 0;">
    <div class="container-custom px-4 sm:px-6 lg:px-8">
        @include('home.sections._section-header', ['section' => $section])

        @if($section->display_type === 'carousel')
        {{-- Carousel --}}
        <div class="hs-carousel-wrap" style="position:relative;">
            <div class="hs-carousel" id="cat-carousel-{{ $section->id }}"
                data-autoscroll="0.4"
                style="display:flex; gap:16px; overflow-x:auto; scrollbar-width:none; -ms-overflow-style:none; padding-bottom:4px; cursor:grab;"
                onmousedown="hsCarouselDragStart(event,this)" onmousemove="hsCarouselDragMove(event,this)" onmouseup="hsCarouselDragEnd(event,this)" onmouseleave="hsCarouselDragEnd(event,this)">
                @foreach($categories as $category)
                <a href="{{ route('categories.show', $category) }}"
                    style="flex:0 0 calc((100% - {{ ($section->desktop_visible - 1) * 16 }}px) / {{ $section->desktop_visible }}); scroll-snap-align:start; text-decoration:none; background:#fff; border-radius:12px; padding:20px 12px; text-align:center; border:1px solid #e5e7eb; transition:all 0.25s; display:flex; flex-direction:column; align-items:center; gap:10px; min-width:0;"
                    class="cat-card-{{ $section->id }}"
                    onmouseover="this.style.borderColor='var(--color-primary,#16a34a)';this.style.boxShadow='0 4px 16px rgba(0,0,0,0.1)';this.style.transform='translateY(-3px)'"
                    onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow='none';this.style.transform='none'">
                    @if($category->getFirstMediaUrl('category_image'))
                        <img src="{{ $category->getFirstMediaUrl('category_image') }}" loading="lazy" decoding="async" alt="{{ $category->name }}" style="width:56px; height:56px; object-fit:cover; border-radius:8px;">
                    @else
                        <div style="width:56px; height:56px; background:linear-gradient(135deg,#f0fdf4,#dcfce7); border-radius:8px; display:flex; align-items:center; justify-content:center;">
                            <svg style="width:28px;height:28px;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        </div>
                    @endif
                    <span style="font-size:0.85rem; font-weight:600; color:#111827; line-height:1.3; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100%;">{{ $category->name }}</span>
                    <span style="font-size:0.72rem; color:#9ca3af;">{{ $category->products_count ?? 0 }} items</span>
                </a>
                @endforeach
            </div>
            @include('home.sections._carousel-nav', ['id' => 'cat-carousel-'.$section->id])
        </div>
        <style>
        #cat-carousel-{{ $section->id }}::-webkit-scrollbar { display:none; }
        @media(max-width:767px) {
            #cat-carousel-{{ $section->id }} a.cat-card-{{ $section->id }} {
                flex: 0 0 calc((100% - {{ ($section->mobile_visible - 1) * 16 }}px) / {{ $section->mobile_visible }}) !important;
            }
        }
        </style>
        @else
        {{-- Grid --}}
        <div style="display:grid; grid-template-columns:repeat({{ $section->desktop_columns }}, 1fr); gap:16px;">
            @foreach($categories->take($section->items_count) as $category)
            <a href="{{ route('categories.show', $category) }}" style="text-decoration:none; background:#fff; border-radius:12px; padding:20px 12px; text-align:center; border:1px solid #e5e7eb; transition:all 0.25s; display:flex; flex-direction:column; align-items:center; gap:10px;"
                onmouseover="this.style.borderColor='var(--color-primary,#16a34a)';this.style.transform='translateY(-3px)'"
                onmouseout="this.style.borderColor='#e5e7eb';this.style.transform='none'">
                @if($category->getFirstMediaUrl('category_image'))
                    <img src="{{ $category->getFirstMediaUrl('category_image') }}" loading="lazy" decoding="async" alt="{{ $category->name }}" style="width:56px; height:56px; object-fit:cover; border-radius:8px;">
                @else
                    <div style="width:56px; height:56px; background:linear-gradient(135deg,#f0fdf4,#dcfce7); border-radius:8px; display:flex; align-items:center; justify-content:center;">
                        <svg style="width:28px;height:28px;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    </div>
                @endif
                <span style="font-size:0.85rem; font-weight:600; color:#111827;">{{ $category->name }}</span>
                <span style="font-size:0.72rem; color:#9ca3af;">{{ $category->products_count ?? 0 }} items</span>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</section>
