<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Auto-detect storage URL from the actual HTTP request.
        // Works on XAMPP subdirectory (http://localhost/digitalp),
        // VPS root (https://myapp.com), cPanel subdomain — no env changes needed.
        if (!app()->runningInConsole()) {
            $request = request();
            $base    = rtrim($request->getSchemeAndHttpHost() . $request->getBasePath(), '/');
            config(['filesystems.disks.public.url' => $base . '/storage']);
        }

        view()->composer('layouts.partials.header', function ($view) {
            $view->with('megaCategories', \App\Models\Category::active()
                ->whereNull('parent_id')
                ->with(['children' => fn ($q) => $q->active()->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->get()
            );
            $view->with('menuItems', \App\Models\MenuItem::where('is_active', true)
                ->with('category.children')
                ->orderBy('sort_order')
                ->get()
            );
        });

        // Share wishlist & compare data globally (needed by header badges + product cards)
        view()->composer('*', function ($view) {
            static $loaded = false;
            static $data = [];
            if (!$loaded) {
                $sessionId = session()->getId();
                $data['wishlistCount'] = \App\Models\Wishlist::where('session_id', $sessionId)->count();
                $data['compareCount'] = count(session()->get('compare', []));
                $data['wishlistProductIds'] = \App\Models\Wishlist::where('session_id', $sessionId)->pluck('product_id')->toArray();
                $data['compareProductIds'] = session()->get('compare', []);
                $loaded = true;
            }
            $view->with($data);
        });
    }
}
