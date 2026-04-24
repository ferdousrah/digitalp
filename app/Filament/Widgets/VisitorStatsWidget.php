<?php

namespace App\Filament\Widgets;

use App\Models\Visitor;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class VisitorStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.visitor-stats-widget';
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = ['default' => 'full', 'md' => 1, 'xl' => 2];

    public ?string $period = 'month';

    public function setVisitorPeriod(string $p): void
    {
        $this->period = $p;
    }

    protected function getViewData(): array
    {
        $since = match ($this->period) {
            'today' => Carbon::now()->startOfDay(),
            'week'  => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'year'  => Carbon::now()->subYear(),
            default => Carbon::now()->subMonth(),
        };

        $total    = Visitor::where('created_at', '>=', $since)->count();
        $prevEnd  = $since->copy();
        $prevStart = $since->copy()->sub($since->diffAsCarbonInterval(now()));
        $prev     = Visitor::whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $pct      = $prev > 0 ? round((($total - $prev) / $prev) * 100, 1) : ($total > 0 ? 100 : 0);

        // Source breakdown
        $rows = Visitor::where('created_at', '>=', $since)
            ->selectRaw('source, COUNT(*) as visits')
            ->groupBy('source')
            ->orderByDesc('visits')
            ->get();

        $sourcePalette = [
            'direct'    => ['label' => 'Website',    'color' => '#86c267'],  // green
            'facebook'  => ['label' => 'Facebook',   'color' => '#1877f2'],
            'instagram' => ['label' => 'Instagram',  'color' => '#e4405f'],
            'google'    => ['label' => 'Google',     'color' => '#4285f4'],
            'youtube'   => ['label' => 'YouTube',    'color' => '#ff0000'],
            'tiktok'    => ['label' => 'TikTok',     'color' => '#000000'],
            'twitter'   => ['label' => 'Twitter / X','color' => '#1da1f2'],
            'linkedin'  => ['label' => 'LinkedIn',   'color' => '#0a66c2'],
            'whatsapp'  => ['label' => 'WhatsApp',   'color' => '#25d366'],
            'messenger' => ['label' => 'Messenger',  'color' => '#006aff'],
            'pinterest' => ['label' => 'Pinterest',  'color' => '#e60023'],
            'reddit'    => ['label' => 'Reddit',     'color' => '#ff4500'],
            'bing'      => ['label' => 'Bing',       'color' => '#008373'],
            'duckduckgo'=> ['label' => 'DuckDuckGo', 'color' => '#de5833'],
            'email'     => ['label' => 'Email',      'color' => '#64748b'],
            'other'     => ['label' => 'Other',      'color' => '#94a3b8'],
        ];

        $sources = $rows->map(function ($r) use ($sourcePalette, $total) {
            $info = $sourcePalette[$r->source] ?? ['label' => ucfirst($r->source), 'color' => '#94a3b8'];
            $percent = $total > 0 ? round(($r->visits / $total) * 100) : 0;
            return [
                'source'  => $r->source,
                'label'   => $info['label'],
                'color'   => $info['color'],
                'visits'  => $r->visits,
                'percent' => $percent,
            ];
        })->toArray();

        // Top-4 visual segments for the gauge (like a segmented arc with donut-style)
        $topForArc = array_slice($sources, 0, 4);

        // Biggest-source percent for the "70%" style label (from screenshot inspiration)
        $bigPercent = $topForArc[0]['percent'] ?? 0;

        return [
            'total'       => $total,
            'pct'         => $pct,
            'sources'     => $sources,
            'topForArc'   => $topForArc,
            'bigPercent'  => $bigPercent,
            'period'      => $this->period,
            'periodLabel' => [
                'today' => 'Today',
                'week'  => 'This Week',
                'month' => 'This Month',
                'year'  => 'This Year',
            ][$this->period] ?? 'This Month',
        ];
    }
}
