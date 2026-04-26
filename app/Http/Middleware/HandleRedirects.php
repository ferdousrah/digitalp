<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HandleRedirects
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Skip admin / livewire / api / static files — fast bail-out
        if (
            $request->is('admin*') ||
            $request->is('livewire/*') ||
            $request->is('api/*') ||
            $request->is('storage/*') ||
            $request->is('build/*') ||
            $request->is('js/*') ||
            $request->is('css/*') ||
            $request->is('images/*') ||
            $request->is('sitemap.xml') ||
            $request->is('robots.txt')
        ) {
            return $next($request);
        }

        $path  = Redirect::normalize($request->path() === '/' ? '/' : '/' . ltrim($request->path(), '/'));
        $table = $this->lookupTable();

        if (isset($table[$path])) {
            $r = $table[$path];

            // Increment hits asynchronously (don't block the redirect)
            DB::table('redirects')->where('id', $r['id'])->update([
                'hits'        => DB::raw('hits + 1'),
                'last_hit_at' => now(),
            ]);

            $target = $r['target_path'];
            // Preserve query string from the original request
            if ($request->getQueryString()) {
                $target .= (str_contains($target, '?') ? '&' : '?') . $request->getQueryString();
            }

            return redirect($target, $r['status_code']);
        }

        return $next($request);
    }

    /**
     * Cached map of source_path => row. Cache busted by the model's saved/deleted events.
     */
    protected function lookupTable(): array
    {
        return Cache::rememberForever('redirects.lookup', function () {
            return Redirect::where('is_active', true)
                ->get(['id', 'source_path', 'target_path', 'status_code'])
                ->keyBy('source_path')
                ->map(fn ($r) => $r->only(['id', 'target_path', 'status_code']))
                ->all();
        });
    }
}
