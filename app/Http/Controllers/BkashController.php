<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\BkashService;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BkashController extends Controller
{
    public function callback(Request $request, BkashService $bkash)
    {
        $paymentId = $request->input('paymentID');
        $status    = $request->input('status');

        Log::info('bKash callback received', ['paymentID' => $paymentId, 'status' => $status]);

        $order = Order::where('bkash_payment_id', $paymentId)->first();

        if (! $order) {
            Log::error('bKash callback: order not found for paymentID ' . $paymentId);
            return redirect()->route('checkout.index')->with('error', 'Order not found.');
        }

        if ($status === 'success') {
            try {
                $result = $bkash->executePayment($paymentId);

                if (($result['transactionStatus'] ?? '') === 'Completed') {
                    $order->update([
                        'payment_status' => 'paid',
                        'bkash_trx_id'   => $result['trxID'] ?? null,
                        'status'         => 'processing',
                    ]);

                    CartService::clear();

                    return redirect()->route('checkout.success', $order->order_number)
                        ->with('success', 'Payment successful! TrxID: ' . ($result['trxID'] ?? ''));
                }

                // Payment not completed
                Log::warning('bKash payment not completed', ['result' => $result]);
                $order->update(['payment_status' => 'failed']);
                return redirect()->route('checkout.index')
                    ->with('error', 'Payment was not completed. Status: ' . ($result['transactionStatus'] ?? 'unknown'));

            } catch (\Exception $e) {
                Log::error('bKash execute failed: ' . $e->getMessage());
                $order->update(['payment_status' => 'failed']);
                return redirect()->route('checkout.index')
                    ->with('error', 'Payment verification failed. Please contact support.');
            }
        }

        if ($status === 'cancel') {
            $order->update(['payment_status' => 'cancelled', 'status' => 'cancelled']);
            return redirect()->route('checkout.index')
                ->with('warning', 'bKash payment was cancelled.');
        }

        // failure
        $order->update(['payment_status' => 'failed']);
        return redirect()->route('checkout.index')
            ->with('error', 'bKash payment failed. Please try again.');
    }
}
