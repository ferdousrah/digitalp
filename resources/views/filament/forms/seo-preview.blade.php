@php
    $state = $getContainer()?->getState() ?? [];
    $title = trim((string) ($state['meta_title'] ?? ''));
    if ($title === '') {
        $title = trim((string) ($state['name'] ?? $state['title'] ?? 'Untitled page'));
    }
    $desc = trim((string) ($state['meta_description'] ?? ''));
    if ($desc === '') {
        $desc = \Illuminate\Support\Str::limit(strip_tags((string) (
            $state['short_description'] ?? $state['excerpt'] ?? $state['description'] ?? ''
        )), 160);
        if ($desc === '') $desc = 'No description set. Search engines will fall back to your default site description.';
    }

    $slug = trim((string) ($state['slug'] ?? ''));
    $base = rtrim(config('app.url'), '/');
    $url  = $slug ? $base . ' › ' . str_replace('-', ' ', $slug) : $base;

    // Truncate title/desc to Google's typical pixel widths (~580px title, ~990px desc)
    $titleDisplay = mb_strlen($title) > 60 ? mb_substr($title, 0, 57) . '…' : $title;
    $descDisplay  = mb_strlen($desc)  > 160 ? mb_substr($desc, 0, 157) . '…' : $desc;
@endphp

<div style="border:1px solid rgb(226 232 240); background:#fff; border-radius:0.5rem; padding:1rem 1.25rem; margin-top:0.25rem;" class="ds-surface">
    <div class="ds-text-muted" style="font-size:0.7rem; font-weight:600; letter-spacing:0.06em; text-transform:uppercase; margin-bottom:0.75rem;">
        Google Search Preview
    </div>

    <div style="font-family: arial, sans-serif;">
        <div style="font-size:0.78rem; color:#202124; line-height:1.4; margin-bottom:2px;">
            {{ $url }}
        </div>
        <div style="font-size:1.25rem; color:#1a0dab; line-height:1.3; font-weight:400; margin-bottom:4px; cursor:pointer;">
            {{ $titleDisplay }}
        </div>
        <div style="font-size:0.875rem; color:#4d5156; line-height:1.5;">
            {{ $descDisplay }}
        </div>
    </div>
</div>

<div class="ds-text-muted" style="margin-top:0.5rem; font-size:0.78rem;">
    💡 This preview updates as you type. Strong meta titles include the primary keyword + brand. Descriptions should be a compelling 1–2 sentence summary.
</div>
