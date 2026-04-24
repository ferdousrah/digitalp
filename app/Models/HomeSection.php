<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    protected $fillable = [
        'key',
        'type',
        'title',
        'subtitle',
        'is_enabled',
        'sort_order',
        'bg_color',
        'padding_y',
        'heading_color',
        'heading_size_desktop',
        'heading_size_mobile',
        'heading_weight',
        'subheading_color',
        'subheading_size_desktop',
        'subheading_size_mobile',
        'subheading_weight',
        'text_align',
        'display_type',
        'desktop_columns',
        'mobile_columns',
        'desktop_visible',
        'mobile_visible',
        'items_count',
        'rows',
        'extra',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'extra'      => 'array',
        ];
    }

    public static function enabledOrdered(): Builder
    {
        return static::where('is_enabled', true)->orderBy('sort_order');
    }
}
