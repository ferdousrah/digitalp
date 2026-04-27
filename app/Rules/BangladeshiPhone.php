<?php

namespace App\Rules;

use App\Support\PhoneNormalizer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates a BD mobile number using PhoneNormalizer.
 *
 * Accepts any common form (01712345678, +8801712345678, 8801712345678,
 * with spaces / dashes / parens) — anything that normalizes to a valid
 * +8801[3-9]XXXXXXXX passes.
 *
 * Usage:
 *   $request->validate([
 *       'phone' => ['required', 'string', new BangladeshiPhone],
 *   ]);
 *
 * Pair with `dehydrateStateUsing(fn ($s) => PhoneNormalizer::normalize($s))`
 * (Filament) or normalize manually before persisting so the saved value
 * is always E.164.
 */
class BangladeshiPhone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') return;

        if (PhoneNormalizer::normalize((string) $value) === null) {
            $fail('The :attribute must be a valid Bangladeshi mobile number (e.g. 01712345678).');
        }
    }
}
