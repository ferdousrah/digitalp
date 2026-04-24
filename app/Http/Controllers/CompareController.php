<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index(Request $request)
    {
        $compareIds = $request->session()->get('compare', []);
        $products = Product::whereIn('id', $compareIds)->with(['media', 'brand', 'attributeValues.attribute'])->get();
        return view('products.compare', compact('products'));
    }

    public function add(Product $product, Request $request)
    {
        $compare = $request->session()->get('compare', []);

        if (in_array($product->id, $compare)) {
            // Already in compare — remove it (toggle behavior)
            $compare = array_values(array_diff($compare, [$product->id]));
            $request->session()->put('compare', $compare);
            $message = 'Product removed from comparison.';
            $added = false;
        } elseif (count($compare) >= 4) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You can compare up to 4 products.', 'count' => count($compare), 'added' => false, 'error' => true], 422);
            }
            return back()->with('error', 'You can compare up to 4 products.');
        } else {
            $compare[] = $product->id;
            $request->session()->put('compare', $compare);
            $message = 'Product added to comparison!';
            $added = true;
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'count' => count($compare), 'added' => $added]);
        }
        return back()->with('success', $message);
    }

    public function remove(Product $product, Request $request)
    {
        $compare = $request->session()->get('compare', []);
        $compare = array_values(array_diff($compare, [$product->id]));
        $request->session()->put('compare', $compare);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Product removed from comparison.', 'count' => count($compare)]);
        }
        return back()->with('success', 'Product removed from comparison.');
    }

    public function clear(Request $request)
    {
        $request->session()->forget('compare');

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Comparison cleared.', 'count' => 0]);
        }
        return back()->with('success', 'Comparison cleared.');
    }
}
