<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * Per-request SEO state. Page controllers (or views) call set*() methods,
 * the layout's <head> reads them.
 *
 * Usage in a controller:
 *   app(SeoService::class)
 *       ->title('iPhone 15 — Digital Support')
 *       ->description('...')
 *       ->image($product->getFirstMediaUrl(...))
 *       ->ogType('product');
 */
class SeoService
{
    protected array $data = [];

    public function title(?string $value): self        { $this->data['title']       = $value; return $this; }
    public function description(?string $value): self  { $this->data['description'] = $value; return $this; }
    public function keywords(?string $value): self     { $this->data['keywords']    = $value; return $this; }
    public function image(?string $value): self        { $this->data['image']       = $value; return $this; }
    public function canonical(?string $value): self    { $this->data['canonical']   = $value; return $this; }
    public function ogType(string $value = 'website'): self { $this->data['og_type'] = $value; return $this; }
    public function noindex(bool $on = true): self     { $this->data['noindex']     = $on;    return $this; }

    public function get(string $key, $fallback = null)
    {
        return $this->data[$key] ?? $fallback;
    }

    /**
     * Resolve the full SEO context, merging page-set values with site-level fallbacks
     * (Site Settings) and current request URL.
     */
    public function resolve(): array
    {
        $request    = request();
        $siteName   = SettingService::get('site_name', config('app.name', 'Digital Support'));
        $siteDesc   = SettingService::get('meta_description', '');
        $defaultLogo = SettingService::get('site_logo');
        $defaultLogoUrl = $defaultLogo ? Storage::disk('public')->url($defaultLogo) : null;

        $rawTitle    = $this->data['title'] ?? SettingService::get('meta_title', $siteName);
        // Auto-append site name to page titles so admins changing site_name flows everywhere.
        // Skip the append when title already contains site_name (or IS site_name).
        $title = ($rawTitle === $siteName || str_contains($rawTitle, $siteName))
            ? $rawTitle
            : trim($rawTitle) . ' — ' . $siteName;
        $description = $this->data['description'] ?? $siteDesc;
        $image       = $this->data['image'] ?? $defaultLogoUrl;
        $canonical   = $this->data['canonical'] ?? $request->url();

        return [
            'site_name'   => $siteName,
            'title'       => $title,
            'description' => $description,
            'keywords'    => $this->data['keywords'] ?? null,
            'image'       => $image,
            'canonical'   => $canonical,
            'og_type'     => $this->data['og_type'] ?? 'website',
            'noindex'     => $this->data['noindex'] ?? false,
            'locale'      => app()->getLocale() === 'bn' ? 'bn_BD' : 'en_US',
            // Tracking IDs (set in admin → Settings)
            'gsc_verification'    => SettingService::get('google_site_verification'),
            'google_analytics_id' => SettingService::get('google_analytics_id'),
            'facebook_pixel_id'   => SettingService::get('facebook_pixel_id'),
            // Social profiles (for Organization schema)
            'social' => [
                'facebook'  => SettingService::get('social_facebook'),
                'instagram' => SettingService::get('social_instagram'),
                'twitter'   => SettingService::get('social_twitter'),
                'youtube'   => SettingService::get('social_youtube'),
                'linkedin'  => SettingService::get('social_linkedin'),
                'tiktok'    => SettingService::get('social_tiktok'),
            ],
            // Contact info (for Organization / LocalBusiness schema)
            'contact' => [
                'phone'   => SettingService::get('contact_phone'),
                'email'   => SettingService::get('contact_email'),
                'address' => SettingService::get('contact_address'),
            ],
        ];
    }
}
