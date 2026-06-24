<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServerPerformanceWidget extends Widget
{
    protected static string $view = 'filament.widgets.server-performance-widget';

    // Sits right after the Total Visitors widget (sort 5).
    protected static ?int $sort = 6;

    // Fills the empty space beside Total Visitors (2 of 4 columns on xl).
    protected int|string|array $columnSpan = ['default' => 'full', 'md' => 1, 'xl' => 2];

    protected function getViewData(): array
    {
        $memory = $this->memoryMetric();
        $disk   = $this->diskMetric();
        $cpu    = $this->cpuMetric();
        $db     = $this->dbMetric();
        $errors = $this->recentErrors();

        $statuses = [$memory['status'], $disk['status'], $cpu['status'], $db['status']];
        if ($errors['count'] > 0) {
            $statuses[] = 'warn';
        }

        $overall = in_array('critical', $statuses, true)
            ? 'critical'
            : (in_array('warn', $statuses, true) ? 'warn' : 'ok');

        return [
            'memory'  => $memory,
            'disk'    => $disk,
            'cpu'     => $cpu,
            'db'      => $db,
            'errors'  => $errors,
            'overall' => $overall,
            'php'     => PHP_VERSION,
            'laravel' => app()->version(),
        ];
    }

    /**
     * Server RAM usage. Prefers real system memory from /proc/meminfo (Linux),
     * and falls back to the PHP process memory vs memory_limit (e.g. on Windows).
     */
    protected function memoryMetric(): array
    {
        if (is_readable('/proc/meminfo')) {
            $info = @file_get_contents('/proc/meminfo');
            if ($info) {
                $read = function (string $key) use ($info): ?int {
                    if (preg_match('/^' . preg_quote($key, '/') . ':\s+(\d+)\s*kB/mi', $info, $m)) {
                        return (int) $m[1] * 1024; // kB -> bytes
                    }
                    return null;
                };

                $total = $read('MemTotal');
                $avail = $read('MemAvailable') ?? $read('MemFree');

                if ($total && $avail !== null) {
                    $used    = max($total - $avail, 0);
                    $percent = (int) round($used / $total * 100);

                    return [
                        'scope'   => 'system',
                        'used'    => $used,
                        'free'    => $avail,
                        'limit'   => $total,
                        'peak'    => null,
                        'percent' => $percent,
                        'status'  => $percent >= 90 ? 'critical' : ($percent >= 80 ? 'warn' : 'ok'),
                    ];
                }
            }
        }

        // Fallback: PHP process memory vs the configured memory_limit.
        $used  = memory_get_usage(true);
        $peak  = memory_get_peak_usage(true);
        $limit = $this->parseBytes(ini_get('memory_limit'));

        if ($limit <= 0) {
            // -1 == unlimited
            return [
                'scope' => 'php', 'used' => $used, 'peak' => $peak, 'limit' => null,
                'free' => null, 'percent' => null, 'status' => 'ok',
            ];
        }

        $percent = (int) min(100, round($used / $limit * 100));

        return [
            'scope' => 'php', 'used' => $used, 'peak' => $peak, 'limit' => $limit, 'free' => null,
            'percent' => $percent,
            'status'  => $percent >= 90 ? 'critical' : ($percent >= 75 ? 'warn' : 'ok'),
        ];
    }

    /** Disk usage of the partition the app lives on. */
    protected function diskMetric(): array
    {
        $path  = base_path();
        $free  = @disk_free_space($path) ?: 0;
        $total = @disk_total_space($path) ?: 0;
        $used  = max($total - $free, 0);
        $percent = $total > 0 ? (int) round($used / $total * 100) : 0;

        return [
            'used' => $used, 'free' => $free, 'total' => $total,
            'percent' => $percent,
            'status'  => $percent >= 90 ? 'critical' : ($percent >= 80 ? 'warn' : 'ok'),
        ];
    }

    /** CPU load average (Linux only; not available on Windows). */
    protected function cpuMetric(): array
    {
        $isWindows = stripos(PHP_OS, 'WIN') === 0;

        if (! $isWindows && function_exists('sys_getloadavg')) {
            $load = @sys_getloadavg();
            if (is_array($load)) {
                $cores   = $this->cpuCores();
                $one     = (float) ($load[0] ?? 0);
                $percent = $cores > 0 ? (int) min(100, round($one / $cores * 100)) : null;

                return [
                    'available' => true,
                    'load'      => array_map(fn ($v) => round((float) $v, 2), $load),
                    'cores'     => $cores,
                    'percent'   => $percent,
                    'status'    => $percent === null
                        ? 'ok'
                        : ($percent >= 90 ? 'critical' : ($percent >= 70 ? 'warn' : 'ok')),
                ];
            }
        }

        return ['available' => false, 'percent' => null, 'status' => 'ok'];
    }

    protected function cpuCores(): int
    {
        $env = (int) ($_SERVER['NUMBER_OF_PROCESSORS'] ?? 0);
        if ($env > 0) {
            return $env;
        }
        if (is_readable('/proc/cpuinfo')) {
            $info = @file_get_contents('/proc/cpuinfo');
            $count = $info ? substr_count($info, 'processor') : 0;
            if ($count > 0) {
                return $count;
            }
        }
        return 1;
    }

    /** Database connectivity + round-trip latency. */
    protected function dbMetric(): array
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $ms = round((microtime(true) - $start) * 1000, 1);

            return [
                'connected' => true,
                'latency'   => $ms,
                'driver'    => DB::connection()->getDriverName(),
                'status'    => $ms >= 500 ? 'warn' : 'ok',
            ];
        } catch (\Throwable $e) {
            return [
                'connected' => false,
                'latency'   => null,
                'driver'    => null,
                'error'     => Str::limit($e->getMessage(), 120),
                'status'    => 'critical',
            ];
        }
    }

    /** Scan the tail of the latest log file for recent ERROR-level entries. */
    protected function recentErrors(): array
    {
        $file = $this->latestLogFile();
        if (! $file) {
            return ['count' => 0, 'items' => [], 'file' => null];
        }

        $tail = $this->tailFile($file, 200 * 1024); // last ~200 KB

        preg_match_all(
            '/\[(\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2})\]\s+\S+\.(ERROR|CRITICAL|ALERT|EMERGENCY):\s*(.*)/',
            $tail,
            $matches,
            PREG_SET_ORDER
        );

        $items = [];
        foreach (array_reverse($matches) as $m) {
            $items[] = [
                'time'    => $m[1],
                'level'   => $m[2],
                'message' => Str::limit(trim($m[3]), 150),
            ];
            if (count($items) >= 5) {
                break;
            }
        }

        return [
            'count' => count($matches),
            'items' => $items,
            'file'  => basename($file),
        ];
    }

    protected function latestLogFile(): ?string
    {
        $dir = storage_path('logs');
        if (! is_dir($dir)) {
            return null;
        }
        $files = glob($dir . DIRECTORY_SEPARATOR . '*.log') ?: [];
        if ($files === []) {
            return null;
        }
        usort($files, fn ($a, $b) => filemtime($b) <=> filemtime($a));

        return $files[0];
    }

    /** Read at most $maxBytes from the end of a file. */
    protected function tailFile(string $path, int $maxBytes): string
    {
        $size = @filesize($path);
        if ($size === false) {
            return '';
        }
        $fh = @fopen($path, 'rb');
        if (! $fh) {
            return '';
        }
        if ($size > $maxBytes) {
            fseek($fh, -$maxBytes, SEEK_END);
        }
        $data = stream_get_contents($fh);
        fclose($fh);

        return $data ?: '';
    }

    /** Convert a php.ini size string (e.g. "256M") to bytes. -1 stays -1. */
    protected function parseBytes($value): int
    {
        $value = trim((string) $value);
        if ($value === '' || $value === '-1') {
            return -1;
        }
        $unit = strtolower($value[strlen($value) - 1]);
        $num  = (int) $value;

        return match ($unit) {
            'g'     => $num * 1024 ** 3,
            'm'     => $num * 1024 ** 2,
            'k'     => $num * 1024,
            default => (int) $value,
        };
    }
}
