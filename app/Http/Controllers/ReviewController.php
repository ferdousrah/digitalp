<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);

        $user = $request->user();

        // Only a logged-in customer who actually purchased the product may review it.
        if (! $product->purchasedBy($user)) {
            return back(fallback: route('products.show', $product))
                ->with('review_error', 'Only customers who purchased this product can write a review.');
        }

        // One review per buyer per product.
        if ($product->reviewedBy($user)) {
            return back(fallback: route('products.show', $product))
                ->with('review_error', 'You have already reviewed this product.');
        }

        $data = $request->validate([
            'rating'  => ['required', 'integer', 'between:1,5'],
            'title'   => ['nullable', 'string', 'max:150'],
            'comment' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        Review::create([
            'product_id'  => $product->id,
            'user_id'     => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'rating'      => $data['rating'],
            'title'       => $data['title'] ?? null,
            'comment'     => $data['comment'],
            'status'      => 'pending',
            'is_verified' => true, // every reviewer is a verified buyer
        ]);

        return back(fallback: route('products.show', $product))
            ->with('review_submitted', 'Thank you! Your review has been submitted and will appear once approved.');
    }
}
