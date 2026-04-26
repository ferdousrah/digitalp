<?php

namespace App\Observers;

use App\Models\Redirect;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Watches a model's `slug` for changes. When it changes, automatically creates
 * a 301 from the old URL path to the new one — so SEO and existing inbound
 * links keep working after a rename.
 *
 * Eloquent observers are re-resolved from the container at event time, so the
 * class must be constructible with no args. Path prefixes are looked up by
 * model class via the static map below.
 *
 * Register in AppServiceProvider:
 *   Product::observe(SlugChangeRedirectObserver::class);
 */
class SlugChangeRedirectObserver
{
    protected static array $prefixMap = [
        \App\Models\Product::class  => '/products/',
        \App\Models\Category::class => '/categories/',
        \App\Models\BlogPost::class => '/blog/',
        \App\Models\Page::class     => '/pages/',
        \App\Models\Brand::class    => '/brands/',
    ];

    public function updating(Model $model): void
    {
        $prefix = static::$prefixMap[get_class($model)] ?? null;
        if (!$prefix) return;

        if (!$model->isDirty('slug')) return;

        $oldSlug = $model->getOriginal('slug');
        $newSlug = $model->slug;
        if (!$oldSlug || !$newSlug || $oldSlug === $newSlug) return;

        $oldPath = Redirect::normalize($prefix . $oldSlug);
        $newPath = Redirect::normalize($prefix . $newSlug);

        // Don't create a self-loop
        if ($oldPath === $newPath) return;

        // If a manual redirect already exists for the old path, leave it alone
        $existing = Redirect::where('source_path', $oldPath)->first();
        if ($existing) {
            // But update the target if it's an auto one (so chains stay short)
            if ($existing->is_auto) {
                $existing->update(['target_path' => $newPath, 'is_active' => true]);
            }
            Cache::forget('redirects.lookup');
            return;
        }

        // Also: if the NEW path used to redirect somewhere (rare edge case),
        // remove that redirect — the new path is now a real page.
        Redirect::where('source_path', $newPath)->delete();

        Redirect::create([
            'source_path' => $oldPath,
            'target_path' => $newPath,
            'status_code' => 301,
            'is_active'   => true,
            'is_auto'     => true,
            'notes'       => 'Auto-created when ' . class_basename($model) . ' #' . $model->id . ' slug changed.',
        ]);

        Cache::forget('redirects.lookup');
    }
}
