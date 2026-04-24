@props(['section', 'posts'])
@if($posts->count())
<section style="background:{{ $section->bg_color }}; padding:{{ $section->padding_y }}px 0;">
    <div class="container-custom px-4 sm:px-6 lg:px-8">
        @include('home.sections._section-header', ['section' => $section])
        <div class="blog-grid-{{ $section->id }}" style="display:grid; grid-template-columns:repeat({{ $section->desktop_columns }}, 1fr); gap:24px;">
            @foreach($posts->take($section->items_count) as $post)
            <article style="background:#fff; border-radius:12px; overflow:hidden; border:1px solid #e5e7eb; transition:all 0.25s;"
                onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)';this.style.transform='translateY(-3px)'"
                onmouseout="this.style.boxShadow='none';this.style.transform='none'">
                @if($post->getFirstMediaUrl('featured_image'))
                <div style="aspect-ratio:16/9; overflow:hidden;">
                    <img src="{{ $post->getFirstMediaUrl('featured_image') }}" alt="{{ $post->title }}" style="width:100%;height:100%;object-fit:cover;transition:transform 0.4s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                </div>
                @endif
                <div style="padding:20px;">
                    <p style="font-size:0.72rem; color:var(--color-primary,#16a34a); font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 8px;">{{ $post->published_at?->format('M d, Y') ?? $post->created_at->format('M d, Y') }}</p>
                    <h3 style="font-size:1rem; font-weight:700; color:#111827; margin:0 0 8px; line-height:1.4;">
                        <a href="{{ route('blog.show', $post->slug) }}" style="text-decoration:none; color:inherit; transition:color 0.2s;"
                            onmouseover="this.style.color='var(--color-primary,#16a34a)'" onmouseout="this.style.color='#111827'">{{ Str::limit($post->title, 60) }}</a>
                    </h3>
                    @if($post->excerpt)
                    <p style="font-size:0.85rem; color:#6b7280; line-height:1.6; margin:0;">{{ Str::limit($post->excerpt, 100) }}</p>
                    @endif
                </div>
            </article>
            @endforeach
        </div>
        <style>
        @media(max-width:767px) { .blog-grid-{{ $section->id }} { grid-template-columns: repeat({{ $section->mobile_columns }}, 1fr) !important; } }
        </style>
    </div>
</section>
@endif
