@php
    /** @var \Illuminate\Support\Collection $faqs collection of Faq models with question + answer */
    $entries = [];
    foreach ($faqs as $faq) {
        if (empty($faq->question)) continue;
        $entries[] = [
            '@type' => 'Question',
            'name'  => $faq->question,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => strip_tags((string) $faq->answer),
            ],
        ];
    }
@endphp

@if(!empty($entries))
<script type="application/ld+json">
{!! json_encode([
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => $entries,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif
