<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->get('q', ''));
        $products = collect();

        if (strlen($query) >= 2) {
            // Scout search — uses Meilisearch in production, the database engine locally.
            $products = Product::search($query)
                ->query(fn ($builder) => $builder->active()->with(['media', 'brand']))
                ->paginate(12)
                ->withQueryString();
        }

        return view('search.results', compact('products', 'query'));
    }

    public function autocomplete(Request $request)
    {
        $query = trim($request->get('q', ''));
        if (strlen($query) < 2) return response()->json([]);

        $products = Product::search($query)
            ->query(fn ($builder) => $builder->active()->with(['media', 'brand']))
            ->take(8)
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
