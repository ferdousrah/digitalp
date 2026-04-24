<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'description', 'type', 'value',
        'min_order_amount', 'max_discount',
        'starts_at', 'expires_at',
        'usage_limit', 'usage_limit_per_customer',
        'used_count', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value'            => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'max_discount'     => 'decimal:2',
            'starts_at'        => 'datetime',
            'expires_at'       => 'datetime',
            'is_active'        => 'boolean',
        ];
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class);
    }

    /**
     * Validate a coupon for a given subtotal + optional customer phone.
     * Returns [bool isValid, ?string error message].
     */
    public function validateFor(float $subtotal, ?string $customerPhone = null): array
    {
        if (!$this->is_active)                  return [false, 'This coupon is not active.'];
        if ($this->starts_at && $this->starts_at->isFuture()) return [false, 'This coupon has not started yet.'];
        if ($this->expires_at && $this->expires_at->isPast()) return [false, 'This coupon has expired.'];

        if ($this->min_order_amount && $subtotal < (float) $this->min_order_amount) {
            return [false, 'Minimum order of ৳' . number_format((float) $this->min_order_amount, 0) . ' required.'];
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return [false, 'This coupon has reached its usage limit.'];
        }

        if ($this->usage_limit_per_customer !== null && $customerPhone) {
            $used = $this->redemptions()->where('customer_phone', $customerPhone)->count();
            if ($used >= $this->usage_limit_per_customer) {
                return [false, 'You have already used this coupon the maximum number of times.'];
            }
        }

        return [true, null];
    }

    /**
     * Calculate the actual discount amount for a given subtotal (does not validate).
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'fixed') {
            return min((float) $this->value, $subtotal);
        }

        // percentage
        $discount = $subtotal * ((float) $this->value / 100);
        if ($this->max_discount !== null) {
            $discount = min($discount, (float) $this->max_discount);
        }
        return round($discount, 2);
    }
}
