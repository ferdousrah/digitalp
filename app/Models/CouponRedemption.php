<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponRedemption extends Model
{
    protected $fillable = [
        'coupon_id', 'order_id', 'customer_phone',
        'subtotal_before', 'discount_applied', 'used_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal_before'  => 'decimal:2',
            'discount_applied' => 'decimal:2',
            'used_at'          => 'datetime',
        ];
    }

    public function coupon(): BelongsTo { return $this->belongsTo(Coupon::class); }
    public function order(): BelongsTo  { return $this->belongsTo(Order::class); }
}
