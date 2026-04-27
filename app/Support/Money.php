<?php

namespace App\Support;

/**
 * Centralised money formatting for the storefront. Single source of truth so
 * "59,990৳" looks the same in every place — product card, cart, checkout,
 * invoice, order email.
 *
 * Use the @bdt Blade directive in views (registered in AppServiceProvider).
 * Use Money::format() directly when you need a string in PHP.
 * Use window.dsBdt() in JavaScript (defined in layouts/app.blade.php).
 */
class Money
{
    public const SUFFIX = '৳';

    /**
     * Display format — Western thousand separator, no decimals by default.
     * Pass $decimals=2 for invoices / receipts where precision matters.
     */
    public static function format($amount, int $decimals = 0): string
    {
        $n = (float) $amount;
        return number_format($n, $decimals) . self::SUFFIX;
    }

    /** Full precision (2 decimals) — for invoices / accounting / refunds. */
    public static function full($amount): string
    {
        return self::format($amount, 2);
    }
}
