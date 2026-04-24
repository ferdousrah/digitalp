<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_number', 'supplier_id', 'status',
        'order_date', 'expected_date', 'received_date',
        'subtotal', 'tax', 'discount', 'shipping_cost', 'total',
        'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'order_date'     => 'date',
            'expected_date'  => 'date',
            'received_date'  => 'date',
            'subtotal'       => 'decimal:2',
            'tax'            => 'decimal:2',
            'discount'       => 'decimal:2',
            'shipping_cost'  => 'decimal:2',
            'total'          => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function creator(): BelongsTo  { return $this->belongsTo(User::class, 'created_by'); }
    public function items(): HasMany      { return $this->hasMany(PurchaseOrderItem::class); }
    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'reference');
    }

    public static function statuses(): array
    {
        return [
            'draft'     => 'Draft',
            'ordered'   => 'Ordered',
            'partial'   => 'Partially Received',
            'received'  => 'Received',
            'cancelled' => 'Cancelled',
        ];
    }

    /**
     * Recompute status based on items' received quantities.
     */
    public function recomputeStatus(): void
    {
        $items = $this->items()->get();
        if ($items->isEmpty()) return;

        $allReceived = $items->every(fn ($i) => $i->quantity_received >= $i->quantity_ordered);
        $anyReceived = $items->some(fn ($i) => $i->quantity_received > 0);

        if ($allReceived) {
            $this->status = 'received';
            if (!$this->received_date) $this->received_date = now()->toDateString();
        } elseif ($anyReceived) {
            $this->status = 'partial';
        } elseif ($this->status === 'draft') {
            // leave draft
        } else {
            $this->status = 'ordered';
        }
        $this->save();
    }

    public function recomputeTotals(): void
    {
        $subtotal = (float) $this->items()->sum('subtotal');
        $total = $subtotal + (float) $this->tax + (float) $this->shipping_cost - (float) $this->discount;
        $this->forceFill([
            'subtotal' => $subtotal,
            'total'    => max(0, $total),
        ])->save();
    }
}
