<?php

namespace App\Support;

/**
 * BD phone number normalization.
 *
 * Accepts: 01712345678, 1712345678, 8801712345678, +8801712345678,
 *          with spaces / dashes / parens stripped.
 * Produces: +8801712345678 (E.164)
 *
 * BD mobile operator prefixes are 013–019, so the canonical form must
 * match /^\+8801[3-9]\d{8}$/.
 */
class PhoneNormalizer
{
    public static function normalize(?string $raw): ?string
    {
        if (!$raw) return null;

        // Keep digits + a leading "+"
        $hasPlus = str_starts_with(trim($raw), '+');
        $digits  = preg_replace('/\D+/', '', $raw);

        if ($digits === '' || $digits === null) return null;

        // Already in +880... form
        if ($hasPlus && str_starts_with($digits, '880')) {
            $candidate = '+' . $digits;
        }
        // 8801... (13 digits, no plus)
        elseif (strlen($digits) === 13 && str_starts_with($digits, '880')) {
            $candidate = '+' . $digits;
        }
        // 01...    (11 digits, local form)
        elseif (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $candidate = '+88' . $digits;
        }
        // 1...     (10 digits, missing leading 0)
        elseif (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            $candidate = '+880' . $digits;
        }
        else {
            return null;
        }

        return self::isValid($candidate) ? $candidate : null;
    }

    public static function isValid(string $phone): bool
    {
        return (bool) preg_match('/^\+8801[3-9]\d{8}$/', $phone);
    }

    /** Format for display: +880 1712-345678 */
    public static function display(string $phone): string
    {
        if (!self::isValid($phone)) return $phone;
        // +8801712345678 → +880 1712-345678
        return preg_replace('/^\+880(\d{4})(\d{6})$/', '+880 $1-$2', $phone);
    }
}
