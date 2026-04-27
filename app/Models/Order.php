<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'status',
        'shipping_name', 'shipping_phone', 'shipping_district', 'shipping_thana', 'shipping_address',
        'billing_name', 'billing_phone', 'billing_country', 'billing_district', 'billing_thana', 'billing_address',
        'payment_method', 'payment_status', 'bkash_payment_id', 'bkash_trx_id', 'ssl_val_id',
        'coupon_code', 'coupon_discount',
        'notes', 'subtotal', 'delivery_cost', 'total',
        // Fulfilment & admin
        'tracking_number', 'courier_service',
        'shipped_at', 'delivered_at', 'cancelled_at',
        'admin_notes', 'refund_amount', 'refunded_at', 'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'         => 'decimal:2',
            'delivery_cost'    => 'decimal:2',
            'total'            => 'decimal:2',
            'coupon_discount'  => 'decimal:2',
            'refund_amount'    => 'decimal:2',
            'shipped_at'       => 'datetime',
            'delivered_at'     => 'datetime',
            'cancelled_at'     => 'datetime',
            'refunded_at'      => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(OrderActivity::class)->latest();
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /** Helper to append an activity entry. Called from the Observer + custom actions. */
    public function logActivity(string $event, string $title, ?string $description = null, array $meta = []): OrderActivity
    {
        return $this->activities()->create([
            'user_id'     => auth()->id(),
            'event'       => $event,
            'title'       => $title,
            'description' => $description,
            'meta'        => $meta ?: null,
        ]);
    }

    public static function statuses(): array
    {
        return [
            'pending'    => 'Pending',
            'processing' => 'Processing',
            'shipped'    => 'Shipped',
            'delivered'  => 'Delivered',
            'completed'  => 'Completed',
            'cancelled'  => 'Cancelled',
            'refunded'   => 'Refunded',
        ];
    }

    public static function paymentStatuses(): array
    {
        return [
            'pending'  => 'Pending',
            'paid'     => 'Paid',
            'failed'   => 'Failed',
            'refunded' => 'Refunded',
        ];
    }

    public static function couriers(): array
    {
        return [
            'pathao'        => 'Pathao',
            'redx'          => 'RedX',
            'steadfast'     => 'Steadfast',
            'paperfly'      => 'Paperfly',
            'sundarban'     => 'Sundarban Courier',
            'sa_paribahan'  => 'SA Paribahan',
            'janani'        => 'Janani Courier',
            'own'           => 'Own Delivery',
            'other'         => 'Other',
        ];
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
