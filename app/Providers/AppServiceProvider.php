<?php

namespace App\Providers;

use App\Models\Order;
use App\Observers\OrderObserver;
use App\Observers\SlugChangeRedirectObserver;
use App\Support\Money;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\SeoService::class);

        // SMS driver — resolved from config/sms.php
        $this->app->singleton(\App\Services\Sms\SmsManager::class, function ($app) {
            $cfg = $app['config']['sms'];
            $driver = match ($cfg['driver']) {
                'bulksmsbd' => new \App\Services\Sms\BulkSmsBdDriver(
                    apiKey:   (string) ($cfg['bulksmsbd']['api_key'] ?? ''),
                    senderId: (string) ($cfg['bulksmsbd']['sender_id'] ?? ''),
                    endpoint: (string) ($cfg['bulksmsbd']['endpoint'] ?? 'https://bulksmsbd.net/api/smsapi'),
                ),
                default => new \App\Services\Sms\LogSmsDriver(),
            };
            return new \App\Services\Sms\SmsManager($driver);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Some shared-hosting MySQL servers (older versions, MyISAM defaults) cap
        // index keys at 1000 bytes. Default utf8mb4 = 4 bytes/char, so VARCHAR(255)
        // indexes blow past that. Limiting default string length to 191 keeps every
        // VARCHAR index ≤ 764 bytes — works on every cPanel host.
        Schema::defaultStringLength(191);

        // Derive URLs from APP_URL (reliable during bootstrap, before TrustProxies runs).
        // Works on XAMPP (http://localhost/digitalp), VPS root (https://app.com), cPanel.
        // Super Admin bypass — any user with the `super_admin` role passes every Gate check.
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        // @bdt($amount)     → "59,990৳"     (display, no decimals)
        // @bdtFull($amount) → "59,990.00৳" (invoices, accounting, refunds)
        Blade::directive('bdt',     fn ($expr) => "<?php echo \\App\\Support\\Money::format($expr); ?>");
        Blade::directive('bdtFull', fn ($expr) => "<?php echo \\App\\Support\\Money::full($expr); ?>");

        // @siteName → resolves the current site name from settings (admin-editable).
        // Use anywhere the brand name appears: page titles, headings, invoice headers, etc.
        Blade::directive('siteName', fn () => "<?php echo e(\\App\\Services\\SettingService::get('site_name', config('app.name'))); ?>");

        // Auto-log order activity (status changes, payment updates, shipping, refunds)
        Order::observe(OrderObserver::class);

        // Auto-create 301 redirects when a content model's slug changes.
        // Path prefix is resolved by class inside the observer (see its $prefixMap).
        \App\Models\Product::observe(SlugChangeRedirectObserver::class);
        \App\Models\Category::observe(SlugChangeRedirectObserver::class);
        \App\Models\BlogPost::observe(SlugChangeRedirectObserver::class);
        \App\Models\Page::observe(SlugChangeRedirectObserver::class);
        \App\Models\Brand::observe(SlugChangeRedirectObserver::class);

        // Bust the redirect lookup cache when admins edit redirects directly
        \App\Models\Redirect::saved(fn () => \Illuminate\Support\Facades\Cache::forget('redirects.lookup'));
        \App\Models\Redirect::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('redirects.lookup'));

        // Bust the sitemap cache whenever any indexed model changes
        $bustSitemap = fn () => \Illuminate\Support\Facades\Cache::forget('sitemap.xml');
        foreach ([
            \App\Models\Product::class,
            \App\Models\Category::class,
            \App\Models\Brand::class,
            \App\Models\BlogPost::class,
            \App\Models\Page::class,
            \App\Models\Service::class,
        ] as $cls) {
            $cls::saved($bustSitemap);
            $cls::deleted($bustSitemap);
        }

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
