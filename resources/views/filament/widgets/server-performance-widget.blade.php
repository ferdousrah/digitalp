@php
    $fmt = function ($bytes) {
        if ($bytes === null) return '∞';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $b = max((float) $bytes, 0);
        $i = $b > 0 ? (int) floor(log($b, 1024)) : 0;
        $i = min($i, count($units) - 1);
        return round($b / (1024 ** $i), $i >= 2 ? 1 : 0) . ' ' . $units[$i];
    };

    $statusColor = ['ok' => '#16a34a', 'warn' => '#d97706', 'critical' => '#dc2626'];
    $col = fn ($s) => $statusColor[$s] ?? '#16a34a';

    $overallMeta = [
        'ok'       => ['label' => 'Healthy',  'bg' => '#ecfdf5', 'fg' => '#059669'],
        'warn'     => ['label' => 'Degraded', 'bg' => '#fffbeb', 'fg' => '#b45309'],
        'critical' => ['label' => 'Critical', 'bg' => '#fef2f2', 'fg' => '#dc2626'],
    ][$overall];
@endphp

<x-filament-widgets::widget>
    <div wire:poll.60s class="ds-surface" style="border-radius:1rem; padding:1.5rem; box-shadow:0 1px 3px rgba(0,0,0,0.04); height:100%; display:flex; flex-direction:column; gap:1rem;">
        <style>
            .sp-err-scroll { scrollbar-width: thin; scrollbar-color: rgba(148,163,184,0.45) transparent; }
            .sp-err-scroll::-webkit-scrollbar { width: 6px; }
            .sp-err-scroll::-webkit-scrollbar-track { background: transparent; }
            .sp-err-scroll::-webkit-scrollbar-thumb { background: rgba(148,163,184,0.45); border-radius: 999px; }
            .sp-err-scroll::-webkit-scrollbar-thumb:hover { background: rgba(148,163,184,0.7); }
        </style>

        {{-- Header --}}
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3 style="margin:0; font-size:1.05rem; font-weight:700; letter-spacing:-0.01em;">Server Performance</h3>
                <p class="ds-text-muted" style="margin:0.15rem 0 0; font-size:0.72rem;">PHP {{ $php }} · Laravel {{ $laravel }}</p>
            </div>
            <span style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.35rem 0.7rem; border-radius:999px; font-size:0.75rem; font-weight:700; background:{{ $overallMeta['bg'] }}; color:{{ $overallMeta['fg'] }};">
                <span style="width:8px; height:8px; border-radius:50%; background:{{ $overallMeta['fg'] }}; box-shadow:0 0 0 3px {{ $overallMeta['fg'] }}22;"></span>
                {{ $overallMeta['label'] }}
            </span>
        </div>

        {{-- Metric tiles --}}
        <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:0.75rem;">

            {{-- Memory --}}
            <div class="ds-surface" style="border-radius:0.75rem; padding:0.85rem; box-shadow:inset 0 0 0 1px rgba(148,163,184,0.18);">
                <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:0.5rem;">
                    <span class="ds-text-muted" style="font-size:0.72rem; font-weight:600; text-transform:uppercase; letter-spacing:0.04em;">Memory</span>
                    <span class="ds-text-strong" style="font-size:0.85rem; font-weight:800;">{{ $memory['percent'] === null ? '—' : $memory['percent'] . '%' }}</span>
                </div>
                <div style="height:6px; border-radius:999px; background:rgba(148,163,184,0.2); overflow:hidden;">
                    <div style="height:100%; width:{{ $memory['percent'] ?? 4 }}%; background:{{ $col($memory['status']) }}; border-radius:999px; transition:width .6s;"></div>
                </div>
                <div class="ds-text-muted" style="font-size:0.72rem; margin-top:0.45rem;">
                    {{ $fmt($memory['used']) }} / {{ $fmt($memory['limit']) }}
                </div>
            </div>

            {{-- Disk --}}
            <div class="ds-surface" style="border-radius:0.75rem; padding:0.85rem; box-shadow:inset 0 0 0 1px rgba(148,163,184,0.18);">
                <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:0.5rem;">
                    <span class="ds-text-muted" style="font-size:0.72rem; font-weight:600; text-transform:uppercase; letter-spacing:0.04em;">Disk</span>
                    <span class="ds-text-strong" style="font-size:0.85rem; font-weight:800;">{{ $disk['percent'] }}%</span>
                </div>
                <div style="height:6px; border-radius:999px; background:rgba(148,163,184,0.2); overflow:hidden;">
                    <div style="height:100%; width:{{ $disk['percent'] }}%; background:{{ $col($disk['status']) }}; border-radius:999px; transition:width .6s;"></div>
                </div>
                <div class="ds-text-muted" style="font-size:0.72rem; margin-top:0.45rem;">
                    {{ $fmt($disk['used']) }} / {{ $fmt($disk['total']) }} · {{ $fmt($disk['free']) }} free
                </div>
            </div>

            {{-- CPU --}}
            <div class="ds-surface" style="border-radius:0.75rem; padding:0.85rem; box-shadow:inset 0 0 0 1px rgba(148,163,184,0.18);">
                <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:0.5rem;">
                    <span class="ds-text-muted" style="font-size:0.72rem; font-weight:600; text-transform:uppercase; letter-spacing:0.04em;">CPU Load</span>
                    <span class="ds-text-strong" style="font-size:0.85rem; font-weight:800;">
                        {{ $cpu['available'] && $cpu['percent'] !== null ? $cpu['percent'] . '%' : 'N/A' }}
                    </span>
                </div>
                @if($cpu['available'])
                    <div style="height:6px; border-radius:999px; background:rgba(148,163,184,0.2); overflow:hidden;">
                        <div style="height:100%; width:{{ $cpu['percent'] ?? 0 }}%; background:{{ $col($cpu['status']) }}; border-radius:999px; transition:width .6s;"></div>
                    </div>
                    <div class="ds-text-muted" style="font-size:0.72rem; margin-top:0.45rem;">
                        load {{ implode(' · ', $cpu['load']) }} · {{ $cpu['cores'] }} {{ \Illuminate\Support\Str::plural('core', $cpu['cores']) }}
                    </div>
                @else
                    <div style="height:6px; border-radius:999px; background:rgba(148,163,184,0.15);"></div>
                    <div class="ds-text-muted" style="font-size:0.72rem; margin-top:0.45rem;">Not available on this OS</div>
                @endif
            </div>

            {{-- Database --}}
            <div class="ds-surface" style="border-radius:0.75rem; padding:0.85rem; box-shadow:inset 0 0 0 1px rgba(148,163,184,0.18);">
                <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:0.5rem;">
                    <span class="ds-text-muted" style="font-size:0.72rem; font-weight:600; text-transform:uppercase; letter-spacing:0.04em;">Database</span>
                    <span class="ds-text-strong" style="font-size:0.85rem; font-weight:800; color:{{ $col($db['status']) }};">
                        {{ $db['connected'] ? $db['latency'] . ' ms' : 'Down' }}
                    </span>
                </div>
                <div style="display:flex; align-items:center; gap:0.4rem; margin-top:0.1rem;">
                    <span style="width:8px; height:8px; border-radius:50%; background:{{ $col($db['status']) }}; box-shadow:0 0 0 3px {{ $col($db['status']) }}22;"></span>
                    <span class="ds-text-muted" style="font-size:0.72rem;">
                        {{ $db['connected'] ? 'Connected (' . $db['driver'] . ')' : ($db['error'] ?? 'Connection failed') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Error notifications --}}
        <div style="margin-top:auto;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.5rem;">
                <span class="ds-text-strong" style="font-size:0.82rem; font-weight:700;">Recent Errors</span>
                @if($errors['count'] > 0)
                    <span style="display:inline-flex; align-items:center; gap:0.3rem; padding:0.2rem 0.55rem; border-radius:999px; font-size:0.72rem; font-weight:700; background:#fef2f2; color:#dc2626;">
                        {{ $errors['count'] }} logged
                    </span>
                @else
                    <span style="display:inline-flex; align-items:center; gap:0.3rem; padding:0.2rem 0.55rem; border-radius:999px; font-size:0.72rem; font-weight:700; background:#ecfdf5; color:#059669;">
                        All clear
                    </span>
                @endif
            </div>

            @if($errors['count'] === 0)
                <div class="ds-text-muted" style="display:flex; align-items:center; gap:0.5rem; padding:0.75rem; border-radius:0.6rem; background:rgba(16,185,129,0.06); font-size:0.8rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#059669" style="width:16px; height:16px; flex-shrink:0;"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                    No errors in the latest log{{ $errors['file'] ? ' (' . $errors['file'] . ')' : '' }}.
                </div>
            @else
                <div class="sp-err-scroll" style="display:flex; flex-direction:column; gap:0.4rem; max-height:200px; overflow-y:auto; padding-right:0.35rem;">
                    @foreach($errors['items'] as $err)
                        <div style="display:flex; gap:0.6rem; padding:0.6rem 0.7rem; border-radius:0.6rem; background:rgba(220,38,38,0.05); box-shadow:inset 0 0 0 1px rgba(220,38,38,0.12);">
                            <span style="width:7px; height:7px; border-radius:50%; background:#dc2626; margin-top:0.35rem; flex-shrink:0;"></span>
                            <div style="min-width:0; flex:1;">
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <span style="font-size:0.66rem; font-weight:800; color:#dc2626; text-transform:uppercase; letter-spacing:0.04em;">{{ $err['level'] }}</span>
                                    <span class="ds-text-muted" style="font-size:0.68rem;">{{ $err['time'] }}</span>
                                </div>
                                <div class="ds-text-strong" style="font-size:0.78rem; margin-top:0.15rem; word-break:break-word; line-height:1.35;">{{ $err['message'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($errors['count'] > count($errors['items']))
                    <div class="ds-text-muted" style="font-size:0.72rem; text-align:center; padding-top:0.5rem;">
                        +{{ $errors['count'] - count($errors['items']) }} more in {{ $errors['file'] }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-filament-widgets::widget>
