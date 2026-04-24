<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use App\Services\SslcommerzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SslcommerzController extends Controller
{
    public function success(Request $request, SslcommerzService $ssl)
    {
        $tranId = $request->input('tran_id');
        $valId  = $request->input('val_id');
        $status = $request->input('status');

        Log::info('SSLCommerz success callback', $request->all());

        $order = Order::where('order_number', $tranId)->first();

        if (! $order) {
            return redirect()->route('checkout.index')->with('error', 'Order not found.');
        }

        if ($status !== 'VALID' && $status !== 'VALIDATED') {
            $order->update(['payment_status' => 'failed']);
            return redirect()->route('checkout.index')->with('error', 'Payment was not validated.');
        }

        try {
            $validation = $ssl->validatePayment($valId);

            if (in_array($validation['status'] ?? '', ['VALID', 'VALIDATED'])
                && $validation['tran_id'] === $tranId
                && (float) $validation['amount'] >= (float) $order->total
            ) {
                $order->update([
                    'payment_status' => 'paid',
                    'ssl_val_id'     => $valId,
                    'status'         => 'processing',
                ]);

                CartService::clear();

                return redirect()->route('checkout.success', $order->order_number)
                    ->with('success', 'Payment successful! Transaction ID: ' . $valId);
            }

            Log::warning('SSLCommerz validation mismatch', ['validation' => $validation, 'order' => $order->order_number]);
            $order->update(['payment_status' => 'failed']);
            return redirect()->route('checkout.index')->with('error', 'Payment validation failed.');

        } catch (\Exception $e) {
            Log::error('SSLCommerz validate error: ' . $e->getMessage());
            $order->update(['payment_status' => 'failed']);
            return redirect()->route('checkout.index')->with('error', 'Payment verification failed. Contact support.');
        }
    }

    public function fail(Request $request)
    {
        Log::info('SSLCommerz fail callback', $request->all());

        $order = Order::where('order_number', $request->input('tran_id'))->first();
        if ($order) {
            $order->update(['payment_status' => 'failed']);
        }

        return redirect()->route('checkout.index')->with('error', 'Payment failed. Please try again.');
    }

    public function cancel(Request $request)
    {
        Log::info('SSLCommerz cancel callback', $request->all());

        $order = Order::where('order_number', $request->input('tran_id'))->first();
        if ($order) {
            $order->update(['payment_status' => 'cancelled', 'status' => 'cancelled']);
        }

        return redirect()->route('checkout.index')->with('warning', 'Payment was cancelled.');
    }

    public function ipn(Request $request, SslcommerzService $ssl)
    {
        Log::info('SSLCommerz IPN received', $request->all());

        if (! $ssl->verifyHash($request->all())) {
            Log::warning('SSLCommerz IPN hash verification failed');
            return response('Hash verification failed', 400);
        }

        $tranId = $request->input('tran_id');
        $valId  = $request->input('val_id');
        $status = $request->input('status');

        $order = Order::where('order_number', $tranId)->first();
        if (! $order) {
            return response('Order not found', 404);
        }

        if (in_array($status, ['VALID', 'VALIDATED']) && $order->payment_status !== 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'ssl_val_id'     => $valId,
                'status'         => 'processing',
            ]);
        }

        return response('IPN processed', 200);
    }
}
