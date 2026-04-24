<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $sessionId = $request->session()->getId();
        $wishlistItems = Wishlist::where('session_id', $sessionId)
            ->with(['product.media', 'product.brand'])
            ->get();

        return view('wishlist.index', compact('wishlistItems'));
    }

    public function toggle(Product $product, Request $request)
    {
        $sessionId = $request->session()->getId();
        $existing = Wishlist::where('session_id', $sessionId)->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();
            $message = 'Product removed from wishlist.';
            $added = false;
        } else {
            Wishlist::create(['session_id' => $sessionId, 'product_id' => $product->id]);
            $message = 'Product added to wishlist!';
            $added = true;
        }

        $count = Wishlist::where('session_id', $sessionId)->count();

        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'count' => $count, 'added' => $added]);
        }
        return back()->with('success', $message);
    }
}
