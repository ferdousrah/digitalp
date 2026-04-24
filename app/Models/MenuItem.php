<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['label', 'url', 'type', 'category_id', 'sort_order', 'is_active', 'open_in_new_tab'];

    protected $casts = ['is_active' => 'boolean', 'open_in_new_tab' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getResolvedUrlAttribute(): string
    {
        if ($this->type === 'category' && $this->category) {
            return route('categories.show', $this->category);
        }
        return $this->url ?? '#';
    }
}
