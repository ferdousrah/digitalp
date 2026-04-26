@php $seo = app(\App\Services\SeoService::class)->resolve(); @endphp

{{-- Title + meta description --}}
<title>{{ $seo['title'] }}</title>
<meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags((string) $seo['description']), 160) }}">
@if($seo['keywords'])
    <meta name="keywords" content="{{ $seo['keywords'] }}">
@endif

{{-- Robots --}}
<meta name="robots" content="{{ $seo['noindex'] ? 'noindex, nofollow' : 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1' }}">

{{-- Canonical --}}
<link rel="canonical" href="{{ $seo['canonical'] }}">

{{-- Open Graph (Facebook / WhatsApp / LinkedIn) --}}
<meta property="og:locale" content="{{ $seo['locale'] }}">
<meta property="og:site_name" content="{{ $seo['site_name'] }}">
<meta property="og:type" content="{{ $seo['og_type'] }}">
<meta property="og:title" content="{{ $seo['title'] }}">
<meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags((string) $seo['description']), 200) }}">
<meta property="og:url" content="{{ $seo['canonical'] }}">
@if($seo['image'])
    <meta property="og:image" content="{{ $seo['image'] }}">
    <meta property="og:image:alt" content="{{ $seo['title'] }}">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="{{ $seo['image'] ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $seo['title'] }}">
<meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit(strip_tags((string) $seo['description']), 200) }}">
@if($seo['image'])
    <meta name="twitter:image" content="{{ $seo['image'] }}">
@endif

{{-- Google Search Console verification --}}
@if($seo['gsc_verification'])
    <meta name="google-site-verification" content="{{ $seo['gsc_verification'] }}">
@endif

{{-- Organization schema (always emit on every page — anchors brand identity) --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'Organization',
    'name'     => $seo['site_name'],
    'url'      => rtrim(config('app.url'), '/'),
    'logo'     => $seo['image'],
    'sameAs'   => array_values(array_filter([
        $seo['social']['facebook'] ?? null,
        $seo['social']['instagram'] ?? null,
        $seo['social']['twitter'] ?? null,
        $seo['social']['youtube'] ?? null,
        $seo['social']['linkedin'] ?? null,
        $seo['social']['tiktok'] ?? null,
    ])),
    'contactPoint' => array_filter([
        '@type'       => 'ContactPoint',
        'telephone'   => $seo['contact']['phone'] ?? null,
        'email'       => $seo['contact']['email'] ?? null,
        'contactType' => 'customer support',
    ]),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

{{-- Website schema with SearchAction (gets you the sitelinks search box) --}}
<script type="application/ld+json">
{!! json_encode([
    '@context'   => 'https://schema.org',
    '@type'      => 'WebSite',
    'name'       => $seo['site_name'],
    'url'        => rtrim(config('app.url'), '/'),
    'potentialAction' => [
        '@type'       => 'SearchAction',
        'target'      => rtrim(config('app.url'), '/') . '/search?q={search_term_string}',
        'query-input' => 'required name=search_term_string',
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

{{-- Google Analytics 4 --}}
@if($seo['google_analytics_id'])
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $seo['google_analytics_id'] }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $seo['google_analytics_id'] }}');
    </script>
@endif

{{-- Facebook Pixel --}}
@if($seo['facebook_pixel_id'])
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
        n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $seo['facebook_pixel_id'] }}');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id={{ $seo['facebook_pixel_id'] }}&ev=PageView&noscript=1"/></noscript>
@endif
