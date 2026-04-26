<x-filament-panels::page>
    @php
        $r = $this->getReport();
        $fmt = function ($bytes) {
            if ($bytes < 1024) return $bytes . ' B';
            if ($bytes < 1024 * 1024) return round($bytes / 1024, 1) . ' KB';
            return round($bytes / 1024 / 1024, 2) . ' MB';
        };
        $webpPct = ($r['webp_ready'] + $r['webp_missing']) > 0
            ? round(($r['webp_ready'] / ($r['webp_ready'] + $r['webp_missing'])) * 100)
            : 0;
    @endphp

    {{-- Summary cards --}}
    <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:1rem; margin-bottom:1.5rem;" class="ds-plr-summary">
        <div class="ds-surface" style="padding:1rem 1.25rem; border-radius:0.75rem;">
            <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; text-transform:uppercase;">Product Images</div>
            <div style="font-size:1.5rem; font-weight:700;" class="ds-text-strong">{{ number_format($r['total_count']) }}</div>
            <div class="ds-text-muted" style="font-size:0.78rem;">{{ $fmt($r['total_size']) }} total</div>
        </div>
        <div class="ds-surface" style="padding:1rem 1.25rem; border-radius:0.75rem; border-left:4px solid {{ $r['large_count'] > 0 ? '#f59e0b' : '#10b981' }} !important;">
            <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; text-transform:uppercase;">Oversized (>500 KB)</div>
            <div style="font-size:1.5rem; font-weight:700; color:{{ $r['large_count'] > 0 ? '#f59e0b' : '#10b981' }};">{{ $r['large_count'] }}</div>
            <div class="ds-text-muted" style="font-size:0.78rem;">should be < 500 KB</div>
        </div>
        <div class="ds-surface" style="padding:1rem 1.25rem; border-radius:0.75rem; border-left:4px solid {{ $webpPct >= 90 ? '#10b981' : ($webpPct >= 50 ? '#f59e0b' : '#ef4444') }} !important;">
            <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; text-transform:uppercase;">WebP Conversion</div>
            <div style="font-size:1.5rem; font-weight:700;" class="ds-text-strong">{{ $webpPct }}%</div>
            <div class="ds-text-muted" style="font-size:0.78rem;">{{ $r['webp_ready'] }} ready / {{ $r['webp_missing'] }} pending</div>
        </div>
        <div class="ds-surface" style="padding:1rem 1.25rem; border-radius:0.75rem;">
            <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; text-transform:uppercase;">Average Size</div>
            <div style="font-size:1.5rem; font-weight:700;" class="ds-text-strong">{{ $fmt($r['avg_size']) }}</div>
            <div class="ds-text-muted" style="font-size:0.78rem;">target < 200 KB</div>
        </div>
    </div>

    {{-- WebP regen helper --}}
    @if($r['webp_missing'] > 0)
        <div class="ds-surface" style="padding:1rem 1.25rem; border-radius:0.75rem; margin-bottom:1.25rem; border-left:4px solid #f59e0b !important;">
            <h3 class="ds-text-strong" style="margin:0 0 0.25rem; font-size:0.95rem; font-weight:700;">⚡ {{ $r['webp_missing'] }} images don't have WebP variants yet</h3>
            <p class="ds-text-muted" style="margin:0 0 0.5rem; font-size:0.85rem;">Run the regenerate command in your terminal to create WebP versions for every existing image:</p>
            <pre style="margin:0; padding:0.75rem; background:#0f172a; color:#86efac; border-radius:0.5rem; font-size:0.82rem; overflow-x:auto;"><code>php artisan media-library:regenerate</code></pre>
        </div>
    @endif

    {{-- Oversized images list --}}
    @if(count($r['large_images']) > 0)
        <div class="ds-surface" style="border-radius:1rem; overflow:hidden; margin-bottom:1.25rem;">
            <div class="ds-divider" style="padding:1rem 1.5rem; border-bottom:1px solid rgb(241 245 249);">
                <h3 class="ds-text-strong" style="margin:0; font-size:1rem; font-weight:700;">Oversized Product Images</h3>
                <p class="ds-text-muted" style="margin:0.25rem 0 0; font-size:0.82rem;">Files over 500 KB. Compress before re-uploading — try TinyPNG or Squoosh.</p>
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr class="ds-text-muted" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em;">
                            <th style="text-align:left; padding:0.65rem 1.5rem;">Product</th>
                            <th style="text-align:left; padding:0.65rem 1.5rem;">File</th>
                            <th style="text-align:left; padding:0.65rem 1.5rem;">Type</th>
                            <th style="text-align:right; padding:0.65rem 1.5rem;">Size</th>
                            <th style="text-align:right; padding:0.65rem 1.5rem;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($r['large_images'] as $img)
                            <tr class="ds-divider" style="border-top:1px solid rgb(241 245 249);">
                                <td class="ds-text-strong" style="padding:0.6rem 1.5rem; font-weight:600; font-size:0.88rem;">{{ $img['product'] }}</td>
                                <td class="ds-text-muted" style="padding:0.6rem 1.5rem; font-family:monospace; font-size:0.78rem;">{{ \Illuminate\Support\Str::limit($img['file'], 35) }}</td>
                                <td class="ds-text-muted" style="padding:0.6rem 1.5rem; font-size:0.78rem;">{{ $img['mime'] }}</td>
                                <td style="padding:0.6rem 1.5rem; text-align:right; font-weight:700; color:#f59e0b;">{{ $fmt($img['size']) }}</td>
                                <td style="padding:0.6rem 1.5rem; text-align:right;">
                                    <a href="{{ url('/admin/products/' . $img['product_id'] . '/edit') }}" wire:navigate
                                        style="display:inline-block; padding:4px 12px; background:#16a34a; color:#fff; border-radius:6px; font-size:0.78rem; font-weight:600; text-decoration:none;">Open →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Tips --}}
    <div class="ds-surface" style="padding:1.25rem 1.5rem; border-radius:1rem;">
        <h3 class="ds-text-strong" style="margin:0 0 0.75rem; font-size:1rem; font-weight:700;">Page-speed playbook</h3>
        <ul class="ds-text-strong" style="margin:0; padding-left:1.25rem; font-size:0.88rem; line-height:1.7;">
            <li><strong>Compress before upload</strong> — paste images through <a href="https://squoosh.app" target="_blank" style="color:#3b82f6;">squoosh.app</a> or TinyPNG. Aim for &lt; 200 KB on hero images, &lt; 100 KB on thumbnails.</li>
            <li><strong>Run media regenerate</strong> after large uploads — fills in missing WebP / size variants.</li>
            <li><strong>Use the new <code>&lt;x-product-image&gt;</code> component</strong> in any custom views — it auto-emits a <code>&lt;picture&gt;</code> tag with WebP fallback, lazy loading, async decoding, and explicit dimensions (prevents CLS).</li>
            <li><strong>Test with Lighthouse</strong> — Chrome DevTools → Lighthouse → Generate report. Targets: LCP &lt; 2.5s, CLS &lt; 0.1, INP &lt; 200ms.</li>
        </ul>
    </div>
</x-filament-panels::page>
