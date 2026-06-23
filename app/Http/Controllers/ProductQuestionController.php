<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductQuestion;
use Illuminate\Http\Request;

class ProductQuestionController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'question' => ['required', 'string', 'max:1000'],
        ]);

        ProductQuestion::create([
            'product_id'   => $product->id,
            'user_id'      => $request->user()?->id,
            'name'         => $data['name'],
            'question'     => $data['question'],
            'is_published' => false,
        ]);

        return back()
            ->with('question_success', 'Thanks! Your question has been submitted — we\'ll answer it soon.')
            ->withFragment('product-tabs');
    }
}
