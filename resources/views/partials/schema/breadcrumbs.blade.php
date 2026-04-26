@php
    /** @var array $items  array of ['label' => '...', 'url' => '...'] */
    $list = [];
    $position = 1;
    foreach ($items as $item) {
        if (empty($item['label'])) continue;
        $entry = [
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => $item['label'],
        ];
        if (!empty($item['url'])) {
            $entry['item'] = $item['url'];
        }
        $list[] = $entry;
    }
@endphp

@if(!empty($list))
<script type="application/ld+json">
{!! json_encode([
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => $list,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif
