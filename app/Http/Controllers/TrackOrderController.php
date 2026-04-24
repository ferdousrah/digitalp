<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class TrackOrderController extends Controller
{
    public function index()
    {
        return view('track-order.index');
    }

    public function track(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string|max:50',
            'phone'        => 'required|string|max:20',
        ]);

        $order = Order::where('order_number', $request->order_number)
            ->where('shipping_phone', $request->phone)
            ->with('items')
            ->first();

        if (! $order) {
            return back()
                ->withInput()
                ->with('error', 'No order found with that order number and phone number. Please check and try again.');
        }

        return view('track-order.result', compact('order'));
    }
}
