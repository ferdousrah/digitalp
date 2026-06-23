<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductQuestion extends Model
{
    protected $fillable = [
        'product_id', 'user_id', 'name', 'question', 'answer', 'answered_at', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'answered_at'  => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Answered + published questions, shown publicly on the product page. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->whereNotNull('answer');
    }
}
