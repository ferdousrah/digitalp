<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $products = collect();

        if (strlen($query) >= 2) {
            $products = Product::active()
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('short_description', 'LIKE', "%{$query}%")
                      ->orWhere('sku', 'LIKE', "%{$query}%");
                })
                ->with(['media', 'brand'])
                ->paginate(12)
                ->withQueryString();
        }

        return view('search.results', compact('products', 'query'));
    }

    public function autocomplete(Request $request)
    {
        $query = $request->get('q', '');
        if (strlen($query) < 2) return response()->json([]);

        $products = Product::active()
            ->where('name', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'slug', 'price', 'compare_price', 'brand_id')
            ->with(['media', 'brand'])
            ->limit(8)
            ->get()
            ->map(fn ($p) => [
                'name'          => $p->name,
                'url'           => route('products.show', $p),
                'price'         => number_format($p->price, 2),
                'compare_price' => $p->compare_price ? number_format($p->compare_price, 2) : null,
                'image'         => $p->getFirstMediaUrl('product_thumbnail') ?: $p->getFirstMediaUrl('product_images'),
                'brand'         => $p->brand?->name,
            ]);

        return response()->json($products);
    }
}
