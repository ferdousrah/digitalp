<?php

namespace App\Providers\Filament;

use App\Services\SettingService;
use Filament\Enums\ThemeMode;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    /**
     * Safely read a setting. If the DB isn't ready (e.g. during `route:cache`),
     * return the fallback without throwing.
     */
    protected function safeSetting(string $key, ?string $fallback = null): ?string
    {
        try {
            return SettingService::get($key, $fallback);
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    protected function resolveFaviconUrl(): ?string
    {
        $path = $this->safeSetting('site_favicon');
        if (!$path) return null;
        try {
            return Storage::disk('public')->url($path);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(isSimple: false)
            ->databaseNotifications()

            // ── Branding (closures = deferred to render time, DB-safe) ──
            ->brandName(fn () => $this->safeSetting('site_name', 'Digital Support Admin'))
            ->brandLogo(function () {
                $logoPath = $this->safeSetting('site_logo');
                if (!$logoPath) return null;
                try {
                    return Storage::disk('public')->url($logoPath);
                } catch (\Throwable $e) {
                    return null;
                }
            })
            ->brandLogoHeight('2.25rem')
            ->favicon($this->resolveFaviconUrl())

            // ── Color palette ──────────────────────────────────────────
            ->colors([
                'primary'   => Color::Emerald,
                'secondary' => Color::Slate,
                'gray'      => Color::Slate,
                'success'   => Color::Green,
                'info'      => Color::Sky,
                'warning'   => Color::Amber,
                'danger'    => Color::Rose,
            ])

            // ── Typography ──────────────────────────────────────────────
            ->font('Inter', provider: GoogleFontProvider::class)

            // ── Layout ──────────────────────────────────────────────────
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->defaultThemeMode(ThemeMode::Light)
            ->darkMode()

            // Append custom theme overrides after Filament's stylesheet (hence "higher priority")
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn () => Blade::render('<link rel="stylesheet" href="{{ asset(\'css/admin/theme.css\') }}?v={{ filemtime(public_path(\'css/admin/theme.css\')) }}">')
            )

            // "View Site" button in the topbar — opens frontend in a new tab
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                fn () => Blade::render('
                    <a href="{{ url(\'/\') }}" target="_blank" rel="noopener"
                       title="Open website in new tab"
                       style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.45rem 0.85rem; border-radius:0.5rem; font-size:0.8rem; font-weight:600; color:rgb(71 85 105); background:rgb(241 245 249); border:1px solid rgb(226 232 240); text-decoration:none; transition:all 0.15s ease; white-space:nowrap; margin-left:0.5rem;"
                       onmouseover="this.style.background=\'rgb(16 185 129)\'; this.style.color=\'#fff\'; this.style.borderColor=\'rgb(16 185 129)\';"
                       onmouseout="this.style.background=\'rgb(241 245 249)\'; this.style.color=\'rgb(71 85 105)\'; this.style.borderColor=\'rgb(226 232 240)\';">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px; height:14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                        <span>View Site</span>
                    </a>
                ')
            )

            // ── Navigation groups (order + collapsibility) ──────────────
            ->navigationGroups([
                NavigationGroup::make('Shop')->icon('heroicon-o-shopping-bag')->collapsible()->collapsed(),
                NavigationGroup::make('Catalog')->icon('heroicon-o-cube')->collapsible()->collapsed(),
                NavigationGroup::make('Inventory')->icon('heroicon-o-archive-box')->collapsible()->collapsed(),
                NavigationGroup::make('Content')->icon('heroicon-o-document-text')->collapsible()->collapsed(),
                NavigationGroup::make('Blog')->icon('heroicon-o-newspaper')->collapsible()->collapsed(),
                NavigationGroup::make('Company')->icon('heroicon-o-building-office')->collapsible()->collapsed(),
                NavigationGroup::make('Submissions')->icon('heroicon-o-inbox')->collapsible()->collapsed(),
                NavigationGroup::make('Accounts')->icon('heroicon-o-calculator')->collapsible()->collapsed(),
                NavigationGroup::make('Reports')->icon('heroicon-o-chart-pie')->collapsible()->collapsed(),
                NavigationGroup::make('Settings')->icon('heroicon-o-cog-6-tooth')->collapsible()->collapsed(),
            ])

            // ── Global search ───────────────────────────────────────────
            ->globalSearch()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])

            // ── Resources / Pages / Widgets (auto-discover) ─────────────
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])

            // ── Middleware ──────────────────────────────────────────────
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
