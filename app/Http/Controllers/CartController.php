<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $items = CartService::get();
        $total = CartService::total();
        return view('cart.index', compact('items', 'total'));
    }

    public function data()
    {
        return response()->json([
            'items'      => array_values(CartService::get()),
            'itemCount'  => CartService::itemCount(),
            'total'      => (float) CartService::total(),
        ]);
    }

    public function add(Request $request, Product $product)
    {
        $qty = max(1, (int) $request->input('qty', 1));
        CartService::add($product, $qty);

        return response()->json([
            'message'   => $product->name . ' added to cart.',
            'itemCount' => CartService::itemCount(),
            'total'     => (float) CartService::total(),
            'items'     => array_values(CartService::get()),
        ]);
    }

    public function update(Request $request, string $key)
    {
        $qty = max(0, (int) $request->input('qty', 1));
        CartService::update($key, $qty);

        return response()->json([
            'itemCount' => CartService::itemCount(),
            'total'     => (float) CartService::total(),
            'items'     => array_values(CartService::get()),
        ]);
    }

    public function remove(string $key)
    {
        CartService::remove($key);

        return response()->json([
            'itemCount' => CartService::itemCount(),
            'total'     => (float) CartService::total(),
            'items'     => array_values(CartService::get()),
        ]);
    }

    public function clear()
    {
        CartService::clear();

        return response()->json([
            'itemCount' => 0,
            'total'     => '0.00',
            'items'     => [],
        ]);
    }

    public function suggestions()
    {
        $products = Product::active()
            ->with('media')
            ->inRandomOrder()
            ->limit(8)
            ->get()
            ->map(function ($p) {
                return [
                    'id'            => $p->id,
                    'name'          => $p->name,
                    'price'         => (float) $p->price,
                    'compare_price' => $p->compare_price ? (float) $p->compare_price : null,
                    'image'         => $p->getFirstMediaUrl('product_images', 'thumb') ?: $p->getFirstMediaUrl('product_images') ?: null,
                    'url'           => route('products.show', $p->slug),
                ];
            });

        return response()->json(['products' => $products->values()]);
    }
}
