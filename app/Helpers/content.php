<?php

if (! function_exists('sc')) {
    /**
     * Get a site content string by page and key.
     * Falls back to the Bengali value when locale is 'bn', else English.
     */
    function sc(string $page, string $key, string $fallback = ''): string
    {
        return \App\Models\SiteContent::get($page, $key, $fallback);
    }
}

if (! function_exists('current_locale')) {
    function current_locale(): string
    {
        return session('locale', 'en');
    }
}
