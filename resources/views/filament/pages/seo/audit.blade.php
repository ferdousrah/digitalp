<x-filament-panels::page>
    @php
        $issues  = $this->getIssues();
        $summary = $this->getSummary();

        $groups = [
            'missing_title'       => ['label' => 'Missing meta title',       'severity' => 'critical', 'tip' => 'Pages without a title fall back to the site default — terrible for click-through.'],
            'missing_description' => ['label' => 'Missing meta description', 'severity' => 'critical', 'tip' => 'Without one, Google generates a snippet from arbitrary page text.'],
            'short_title'         => ['label' => 'Title too short (<30 chars)', 'severity' => 'warning', 'tip' => 'Aim for 50–60 chars to fit Google’s display width.'],
            'long_title'          => ['label' => 'Title too long (>60 chars)',  'severity' => 'warning', 'tip' => 'Google truncates with “…” — your keywords may get cut.'],
            'short_description'   => ['label' => 'Description too short (<80)', 'severity' => 'warning', 'tip' => 'Use the full 150–160 chars to make a compelling pitch.'],
            'long_description'    => ['label' => 'Description too long (>170)', 'severity' => 'warning', 'tip' => 'Google will truncate. Edit it down.'],
            'weak_slug'           => ['label' => 'Weak / numeric slug',         'severity' => 'info',    'tip' => 'Slugs like /products/12345 or 1-char slugs hurt SEO. Use keyword-rich slugs.'],
        ];

        $colors = [
            'critical' => '#ef4444',
            'warning'  => '#f59e0b',
            'info'     => '#3b82f6',
        ];
    @endphp

    {{-- Summary cards --}}
    <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:1rem; margin-bottom:1.5rem;" class="ds-plr-summary">
        <div class="ds-surface" style="padding:1rem 1.25rem; border-radius:0.75rem; border-left:4px solid #0f172a !important;">
            <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; text-transform:uppercase;">Total Issues</div>
            <div style="font-size:1.5rem; font-weight:700;" class="ds-text-strong">{{ $summary['total'] }}</div>
        </div>
        <div class="ds-surface" style="padding:1rem 1.25rem; border-radius:0.75rem; border-left:4px solid #ef4444 !important;">
            <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; text-transform:uppercase;">Critical</div>
            <div style="font-size:1.5rem; font-weight:700; color:#ef4444;">{{ $summary['critical'] }}</div>
        </div>
        <div class="ds-surface" style="padding:1rem 1.25rem; border-radius:0.75rem; border-left:4px solid #f59e0b !important;">
            <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; text-transform:uppercase;">Warnings</div>
            <div style="font-size:1.5rem; font-weight:700; color:#f59e0b;">{{ $summary['warnings'] }}</div>
        </div>
        <div class="ds-surface" style="padding:1rem 1.25rem; border-radius:0.75rem; border-left:4px solid #3b82f6 !important;">
            <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; text-transform:uppercase;">Slug Issues</div>
            <div style="font-size:1.5rem; font-weight:700; color:#3b82f6;">{{ $summary['slug'] }}</div>
        </div>
    </div>

    @if($summary['total'] === 0)
        <div class="ds-surface" style="padding:3rem; border-radius:1rem; text-align:center;">
            <div style="font-size:3rem; line-height:1;">✨</div>
            <h3 class="ds-text-strong" style="margin-top:0.5rem; font-size:1.1rem; font-weight:700;">No SEO issues detected.</h3>
            <p class="ds-text-muted" style="margin-top:0.5rem;">Every record has solid meta data. Keep an eye on this page as you add new products.</p>
        </div>
    @endif

    @foreach($groups as $key => $g)
        @if(!empty($issues[$key]))
            <div class="ds-surface" style="border-radius:1rem; overflow:hidden; margin-bottom:1.25rem;">
                <div class="ds-divider" style="padding:1rem 1.5rem; border-bottom:1px solid rgb(241 245 249); display:flex; align-items:center; justify-content:space-between; gap:1rem;">
                    <div style="display:flex; align-items:center; gap:0.625rem;">
                        <span style="width:10px; height:10px; border-radius:50%; background:{{ $colors[$g['severity']] }};"></span>
                        <h3 class="ds-text-strong" style="margin:0; font-size:1rem; font-weight:700;">{{ $g['label'] }}</h3>
                        <span class="ds-text-muted" style="font-size:0.78rem;">— {{ count($issues[$key]) }} record(s)</span>
                    </div>
                </div>
                <p class="ds-text-muted" style="padding:0.75rem 1.5rem 0; margin:0; font-size:0.82rem;">{{ $g['tip'] }}</p>

                <table style="width:100%; border-collapse:collapse; margin-top:0.5rem;">
                    <thead>
                        <tr class="ds-text-muted" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em;">
                            <th style="text-align:left; padding:0.6rem 1.5rem;">Type</th>
                            <th style="text-align:left; padding:0.6rem 1.5rem;">Record</th>
                            @if(in_array($key, ['short_title','long_title','short_description','long_description']))
                                <th style="text-align:right; padding:0.6rem 1.5rem;">Length</th>
                            @endif
                            @if($key === 'weak_slug')
                                <th style="text-align:left; padding:0.6rem 1.5rem;">Slug</th>
                            @endif
                            <th style="text-align:right; padding:0.6rem 1.5rem;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($issues[$key] as $row)
                            <tr class="ds-divider" style="border-top:1px solid rgb(241 245 249);">
                                <td class="ds-text-muted" style="padding:0.6rem 1.5rem; font-size:0.82rem;">{{ $row['type'] }}</td>
                                <td class="ds-text-strong" style="padding:0.6rem 1.5rem; font-weight:600; font-size:0.88rem;">{{ $row['name'] }}</td>
                                @if(in_array($key, ['short_title','long_title','short_description','long_description']))
                                    <td class="ds-text-muted" style="padding:0.6rem 1.5rem; text-align:right; font-variant-numeric:tabular-nums;">{{ $row['len'] }}</td>
                                @endif
                                @if($key === 'weak_slug')
                                    <td style="padding:0.6rem 1.5rem; font-family:monospace; font-size:0.82rem;" class="ds-text-muted">/{{ $row['slug'] }}</td>
                                @endif
                                <td style="padding:0.6rem 1.5rem; text-align:right;">
                                    <a href="{{ $row['url'] }}" wire:navigate
                                        style="display:inline-block; padding:4px 12px; background:#16a34a; color:#fff; border-radius:6px; font-size:0.78rem; font-weight:600; text-decoration:none;">Fix →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach
</x-filament-panels::page>
