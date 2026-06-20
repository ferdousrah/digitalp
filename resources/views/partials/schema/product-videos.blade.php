@php
    /** @var \App\Models\Product $product — emits a VideoObject for each product video (YouTube embed or uploaded file). */
    $videoEntries = [];
    $fallbackThumb = $product->getFirstMediaUrl('product_thumbnail', 'large')
        ?: $product->getFirstMediaUrl('product_images', 'large');
    $videoDesc = strip_tags((string) ($product->short_description ?? $product->description ?? $product->name));
    $videoDesc = $videoDesc !== '' ? \Illuminate\Support\Str::limit($videoDesc, 300) : $product->name;

    foreach (($product->videos ?? []) as $v) {
        $type     = $v['type'] ?? null;
        $title    = !empty($v['title']) ? $v['title'] : $product->name;
        $embedUrl = $contentUrl = $thumb = null;

        if (in_array($type, ['youtube', 'youtube_reel']) && !empty($v['url'])) {
            if (preg_match('~(?:youtube\.com/(?:watch\?v=|shorts/|embed/|v/)|youtu\.be/)([\w-]{11})~', $v['url'], $m)) {
                $embedUrl = 'https://www.youtube.com/embed/' . $m[1];
                $thumb    = 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg';
            }
        } elseif ($type === 'upload' && !empty($v['file'])) {
            $contentUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($v['file']);
            $thumb      = $fallbackThumb;
        }

        // VideoObject requires a thumbnailUrl + a content/embed URL
        if ((! $embedUrl && ! $contentUrl) || ! $thumb) {
            continue;
        }

        $videoEntries[] = array_filter([
            '@context'     => 'https://schema.org',
            '@type'        => 'VideoObject',
            'name'         => $title,
            'description'  => $videoDesc,
            'thumbnailUrl' => $thumb,
            'uploadDate'   => optional($product->created_at)->toIso8601String() ?? now()->toIso8601String(),
            'embedUrl'     => $embedUrl,
            'contentUrl'   => $contentUrl,
        ]);
    }
@endphp
@foreach($videoEntries as $entry)
<script type="application/ld+json">
{!! json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endforeach
