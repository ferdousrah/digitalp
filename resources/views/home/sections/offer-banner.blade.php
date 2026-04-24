@props(['section', 'banners'])
@if($banners->count())
<section style="background:{{ $section->bg_color }}; padding:{{ $section->padding_y }}px 0;">
    <div class="container-custom px-4 sm:px-6 lg:px-8">
        @include('home.sections._section-header', ['section' => $section])
        @php $cols = $section->extra['columns'] ?? 2; @endphp
        <div class="offer-banner-grid-{{ $section->id }}" style="display:grid; grid-template-columns:repeat({{ $cols }}, 1fr); gap:16px;">
            @foreach($banners as $banner)
            @php $img = $banner->getFirstMediaUrl('slide_image'); @endphp
            @if($img)
            <div style="position:relative; border-radius:12px; overflow:hidden; aspect-ratio:{{ $cols == 1 ? '3/1' : '2/1' }};">
                <img src="{{ $img }}" alt="{{ $banner->title ?? '' }}" style="width:100%; height:100%; object-fit:cover; transition:transform 0.4s ease;"
                    onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                @if($banner->link_url)
                <a href="{{ $banner->link_url }}" style="position:absolute;inset:0;" aria-label="{{ $banner->title ?? 'Offer' }}"></a>
                @endif
                @if($banner->title || $banner->subtitle)
                <div style="position:absolute;bottom:0;left:0;right:0;padding:16px 20px;background:linear-gradient(to top,rgba(0,0,0,0.6),transparent);">
                    @if($banner->title)<p style="color:#fff;font-weight:700;font-size:1rem;margin:0;">{{ $banner->title }}</p>@endif
                    @if($banner->subtitle)<p style="color:rgba(255,255,255,0.85);font-size:0.8rem;margin:4px 0 0;">{{ $banner->subtitle }}</p>@endif
                </div>
                @endif
            </div>
            @endif
            @endforeach
        </div>
        <style>
        @media(max-width:767px) { .offer-banner-grid-{{ $section->id }} { grid-template-columns: 1fr !important; } }
        </style>
    </div>
</section>
@endif
