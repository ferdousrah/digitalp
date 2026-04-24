<?php
namespace App\Services;

use App\Models\ProductAttribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductFilterService
{
    public function apply(Builder $query, Request $request): Builder
    {
        $query
            ->when($request->filled('category'), fn ($q) =>
                $q->whereHas('categories', fn ($sub) =>
                    $sub->where('slug', $request->category)
                )
            )
            ->when($request->filled('brand'), function ($q) use ($request) {
                $brands = (array) $request->input('brand');
                $q->whereHas('brand', fn ($sub) =>
                    $sub->whereIn('slug', $brands)
                );
            })
            ->when($request->filled('min_price'), fn ($q) =>
                $q->where('price', '>=', $request->min_price)
            )
            ->when($request->filled('max_price'), fn ($q) =>
                $q->where('price', '<=', $request->max_price)
            )
            ->when($request->filled('in_stock'), fn ($q) =>
                $q->where('in_stock', true)
            );

        // Attribute filtering: ?attr[slug]=value1,value2&attr[slug2]=value3
        $attrs = $request->input('attr', []);
        if (is_array($attrs)) {
            foreach ($attrs as $slug => $valuesStr) {
                if (empty($valuesStr)) continue;
                $values = is_array($valuesStr) ? $valuesStr : explode(',', $valuesStr);
                $values = array_filter(array_map('trim', $values));
                if (empty($values)) continue;

                $query->whereHas('attributeValues', function ($q) use ($slug, $values) {
                    $q->whereHas('attribute', fn ($a) => $a->where('slug', $slug))
                      ->whereIn('value', $values);
                });
            }
        }

        // Sorting
        $query->when($request->filled('sort'), fn ($q) => match($request->sort) {
            'price_asc'  => $q->orderBy('price', 'asc'),
            'price_desc' => $q->orderBy('price', 'desc'),
            'newest'     => $q->orderBy('created_at', 'desc'),
            'popular'    => $q->orderBy('view_count', 'desc'),
            'name_asc'   => $q->orderBy('name', 'asc'),
            default      => $q->orderBy('sort_order', 'asc'),
        }, fn ($q) => $q->orderBy('sort_order', 'asc'));

        return $query;
    }

    /**
     * Get filter attribute data for a category (distinct values + counts).
     * Returns a collection of attributes, each with a 'filterOptions' array.
     */
    public function getFilterData(array $categoryIds, Builder $baseQuery): array
    {
        if (empty($categoryIds)) return [];

        // Get attributes linked to these categories
        $attributes = ProductAttribute::where('is_filterable', true)
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->orderBy('sort_order')
            ->get();

        if ($attributes->isEmpty()) return [];

        // Clone the base query to get product IDs matching current filters (excluding attr filters)
        $productIds = (clone $baseQuery)->pluck('products.id');

        $filterData = [];
        foreach ($attributes as $attr) {
            $options = DB::table('product_attribute_values')
                ->select('value', DB::raw('COUNT(DISTINCT product_id) as count'))
                ->where('product_attribute_id', $attr->id)
                ->whereIn('product_id', $productIds)
                ->groupBy('value')
                ->orderBy('value')
                ->get();

            if ($options->isEmpty()) continue;

            $filterData[] = [
                'id' => $attr->id,
                'name' => $attr->name,
                'slug' => $attr->slug,
                'options' => $options->map(fn ($opt) => [
                    'value' => $opt->value,
                    'count' => $opt->count,
                ])->toArray(),
            ];
        }

        return $filterData;
    }

    /**
     * Get price range for a set of products.
     */
    public function getPriceRange(Builder $baseQuery): array
    {
        $result = (clone $baseQuery)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return [
            'min' => (float) ($result->min_price ?? 0),
            'max' => (float) ($result->max_price ?? 0),
        ];
    }
}
