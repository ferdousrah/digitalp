<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ExpenseCategory extends Model
{
    use HasSlug;

    protected $fillable = [
        'name', 'slug', 'icon', 'color', 'description', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function totalExpenses(): float
    {
        return (float) $this->expenses()->sum('amount');
    }
}
