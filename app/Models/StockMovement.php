<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id', 'type', 'quantity', 'balance_after',
        'reference_type', 'reference_id', 'notes', 'user_id',
    ];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }

    public function reference()
    {
        return $this->morphTo();
    }

    public static function types(): array
    {
        return [
            'purchase'   => 'Purchase (received)',
            'sale'       => 'Sale',
            'adjustment' => 'Adjustment',
            'return'     => 'Customer Return',
        ];
    }
}
