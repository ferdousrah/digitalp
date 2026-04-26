@php
    /** @var \App\Models\BlogPost $post */
    $image = $post->getFirstMediaUrl('blog_image', 'large')
        ?: $post->getFirstMediaUrl('featured_image', 'large')
        ?: \App\Services\SettingService::get('site_logo')
            ? \Illuminate\Support\Facades\Storage::disk('public')->url(\App\Services\SettingService::get('site_logo'))
            : null;

    $schema = [
        '@context'      => 'https://schema.org',
        '@type'         => 'Article',
        'headline'      => $post->title,
        'description'   => strip_tags((string) ($post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags((string) $post->content), 160))),
        'datePublished' => optional($post->published_at ?? $post->created_at)->toIso8601String(),
        'dateModified'  => optional($post->updated_at)->toIso8601String(),
        'mainEntityOfPage' => route('blog.show', $post),
        'image' => $image,
        'author' => [
            '@type' => 'Person',
            'name'  => $post->author?->name ?? 'Admin',
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name'  => \App\Services\SettingService::get('site_name', config('app.name')),
            'logo'  => [
                '@type' => 'ImageObject',
                'url'   => \App\Services\SettingService::get('site_logo')
                    ? \Illuminate\Support\Facades\Storage::disk('public')->url(\App\Services\SettingService::get('site_logo'))
                    : null,
            ],
        ],
    ];
@endphp

<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
