<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'status',
        'shipping_name', 'shipping_phone', 'shipping_district', 'shipping_thana', 'shipping_address',
        'billing_name', 'billing_phone', 'billing_country', 'billing_district', 'billing_thana', 'billing_address',
        'payment_method', 'payment_status', 'bkash_payment_id', 'bkash_trx_id', 'ssl_val_id',
        'coupon_code', 'coupon_discount',
        'notes', 'subtotal', 'delivery_cost', 'total',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'         => 'decimal:2',
            'delivery_cost'    => 'decimal:2',
            'total'            => 'decimal:2',
            'coupon_discount'  => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cod'    => 'Cash On Delivery',
            'bkash'  => 'Bkash',
            'online' => 'Online Payment',
            default  => ucfirst($this->payment_method),
        };
    }
}
