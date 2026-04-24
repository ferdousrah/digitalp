<?php

namespace App\Http\Middleware;

use App\Models\Visitor;
use Closure;
use Illuminate\Http\Request;
use Throwable;

class TrackVisitor
{
    public function handle(Request $request, Closure $next)
    {
        // Process response first so we don't add any latency to the user's request
        $response = $next($request);

        try {
            $this->track($request);
        } catch (Throwable $e) {
            // Swallow — tracking must never break a page
            report($e);
        }

        return $response;
    }

    protected function track(Request $request): void
    {
        // Only track GET requests
        if (!$request->isMethod('GET')) return;

        // Skip admin, Livewire, API, assets
        if (
            $request->is('admin*') ||
            $request->is('livewire/*') ||
            $request->is('api/*') ||
            $request->is('storage/*') ||
            $request->is('build/*') ||
            $request->is('images/*') ||
            $request->is('install.php') ||
            $request->is('*.css') ||
            $request->is('*.js') ||
            $request->is('*.png') ||
            $request->is('*.jpg') ||
            $request->is('*.jpeg') ||
            $request->is('*.webp') ||
            $request->is('*.svg') ||
            $request->is('*.ico')
        ) return;

        // Basic bot filter
        $ua = strtolower((string) $request->userAgent());
        if ($ua === '' || preg_match('/bot|crawler|spider|slurp|facebookexternalhit|preview|monitor|pingdom|uptime|curl|wget|headless/i', $ua)) {
            return;
        }

        // One visit per session per day (dedupe)
        $sessionId = $request->hasSession() ? $request->session()->getId() : md5($request->ip() . $ua);
        $today = now()->startOfDay();

        $exists = Visitor::where('session_id', $sessionId)
            ->where('created_at', '>=', $today)
            ->exists();
        if ($exists) return;

        Visitor::create([
            'session_id' => $sessionId,
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr($request->userAgent() ?? '', 0, 1000),
            'referrer'   => mb_substr($request->header('referer') ?? '', 0, 500),
            'url'        => mb_substr($request->fullUrl(), 0, 500),
            'source'     => $this->detectSource($request),
        ]);
    }

    protected function detectSource(Request $request): string
    {
        $referrer = $request->header('referer');

        // UTM source takes priority if present
        $utm = strtolower((string) $request->query('utm_source'));
        if ($utm !== '') {
            return $this->normalizeSource($utm);
        }

        if (!$referrer) return 'direct';

        $refHost = strtolower((string) parse_url($referrer, PHP_URL_HOST));
        $ownHost = strtolower((string) $request->getHost());

        if ($refHost === '' || $refHost === $ownHost) return 'direct';

        return $this->normalizeSource($refHost);
    }

    protected function normalizeSource(string $host): string
    {
        $map = [
            'facebook'  => ['facebook.', 'fb.com', 'm.facebook', 'lm.facebook', 'l.facebook'],
            'instagram' => ['instagram.', 'l.instagram'],
            'google'    => ['google.', 'google/', 'googlesyndication'],
            'youtube'   => ['youtube.', 'youtu.be'],
            'tiktok'    => ['tiktok.'],
            'twitter'   => ['twitter.', 't.co', 'x.com'],
            'linkedin'  => ['linkedin.'],
            'whatsapp'  => ['whatsapp.', 'wa.me'],
            'messenger' => ['messenger.', 'm.me'],
            'pinterest' => ['pinterest.'],
            'reddit'    => ['reddit.'],
            'bing'      => ['bing.'],
            'duckduckgo'=> ['duckduckgo.'],
            'email'     => ['mail.', 'gmail.', 'outlook.', 'yahoo.'],
        ];

        foreach ($map as $key => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($host, $needle)) return $key;
            }
        }

        return 'other';
    }
}
