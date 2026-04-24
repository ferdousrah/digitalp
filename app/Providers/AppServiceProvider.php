<?php

namespace App\Providers;

use App\Models\Order;
use App\Observers\OrderObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
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
        // Derive URLs from APP_URL (reliable during bootstrap, before TrustProxies runs).
        // Works on XAMPP (http://localhost/digitalp), VPS root (https://app.com), cPanel.
        // Super Admin bypass — any user with the `super_admin` role passes every Gate check.
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        // Auto-log order activity (status changes, payment updates, shipping, refunds)
        Order::observe(OrderObserver::class);

        $appUrl = rtrim((string) config('app.url'), '/');

        if ($appUrl !== '') {
            // Override the public disk URL so Storage::url() returns the correct absolute URL
            config(['filesystems.disks.public.url' => $appUrl . '/storage']);

            // Force https scheme for all URL generation when app is configured as HTTPS.
            // Fixes mixed-content blocks when behind a proxy (Coolify/Traefik/Cloudflare)
            // that terminates TLS and sends internal HTTP to the container.
            if (str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
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
