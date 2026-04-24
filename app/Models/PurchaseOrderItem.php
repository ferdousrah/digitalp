<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id', 'product_id',
        'quantity_ordered', 'quantity_received',
        'unit_cost', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'unit_cost' => 'decimal:2',
            'subtotal'  => 'decimal:2',
        ];
    }

    public function purchaseOrder(): BelongsTo { return $this->belongsTo(PurchaseOrder::class); }
    public function product(): BelongsTo       { return $this->belongsTo(Product::class); }

    public function outstanding(): int
    {
        return max(0, (int) $this->quantity_ordered - (int) $this->quantity_received);
    }

    protected static function booted(): void
    {
        static::saving(function (PurchaseOrderItem $item) {
            $item->subtotal = (float) $item->unit_cost * (int) $item->quantity_ordered;
        });
        static::saved(fn (PurchaseOrderItem $item) => $item->purchaseOrder?->recomputeTotals());
        static::deleted(fn (PurchaseOrderItem $item) => $item->purchaseOrder?->recomputeTotals());
    }
}
