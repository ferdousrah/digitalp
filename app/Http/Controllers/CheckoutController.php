<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Models\Order;
use App\Models\OrderItem;
use App\Rules\BangladeshiPhone;
use App\Services\BangladeshGeoService;
use App\Services\BkashService;
use App\Services\CartService;
use App\Services\SslcommerzService;
use App\Support\PhoneNormalizer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    const DELIVERY_INSIDE_DHAKA  = 80;
    const DELIVERY_OUTSIDE_DHAKA = 130;

    public function index()
    {
        $items = CartService::get();

        if (empty($items)) {
            return redirect()->route('cart.index')->with('info', 'Your cart is empty.');
        }

        $subtotal        = CartService::total();
        $districts       = BangladeshGeoService::districts();
        $deliveryInside  = self::DELIVERY_INSIDE_DHAKA;
        $deliveryOutside = self::DELIVERY_OUTSIDE_DHAKA;

        return view('checkout.index', compact('items', 'subtotal', 'districts', 'deliveryInside', 'deliveryOutside'));
    }

    public function thanas(Request $request)
    {
        $district = $request->input('district', '');
        $thanas   = BangladeshGeoService::thanasForDistrict($district);

        return response()->json(['thanas' => $thanas]);
    }

    /**
     * Validate a coupon code against the current cart + customer phone (optional).
     * Used by the checkout page's "Apply" button via AJAX. Doesn't mutate state.
     */
    public function applyCoupon(Request $request)
    {
        $data = $request->validate([
            'code'  => 'required|string|max:50',
            'phone' => ['nullable', 'string', 'max:20', new BangladeshiPhone],
        ]);
        if (!empty($data['phone'])) {
            $data['phone'] = PhoneNormalizer::normalize($data['phone']);
        }

        $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper(trim($data['code']))])->first();
        if (!$coupon) {
            return response()->json(['ok' => false, 'message' => 'Invalid coupon code.'], 422);
        }

        $subtotal = (float) CartService::total();
        if ($subtotal <= 0) {
            return response()->json(['ok' => false, 'message' => 'Your cart is empty.'], 422);
        }

        [$valid, $reason] = $coupon->validateFor($subtotal, $data['phone'] ?? null);
        if (!$valid) {
            return response()->json(['ok' => false, 'message' => $reason], 422);
        }

        $discount = $coupon->calculateDiscount($subtotal);

        return response()->json([
            'ok'       => true,
            'code'     => $coupon->code,
            'type'     => $coupon->type,
            'discount' => $discount,
            'message'  => 'Coupon applied: ৳' . number_format($discount, 2) . ' off',
        ]);
    }

    public function store(Request $request)
    {
        $items = CartService::get();

        if (empty($items)) {
            return redirect()->route('cart.index');
        }

        $validated = $request->validate([
            'shipping_name'     => 'required|string|max:255',
            'shipping_phone'    => ['required', 'string', 'max:20', new BangladeshiPhone],
            'shipping_district' => 'required|string|max:100',
            'shipping_thana'    => 'required|string|max:100',
            'shipping_address'  => 'required|string|max:500',
            'billing_name'      => 'nullable|string|max:255',
            'billing_phone'     => ['nullable', 'string', 'max:20', new BangladeshiPhone],
            'billing_district'  => 'nullable|string|max:100',
            'billing_thana'     => 'nullable|string|max:100',
            'billing_address'   => 'nullable|string|max:500',
            'payment_method'    => 'required|in:cod,bkash,online',
            'coupon_code'       => 'nullable|string|max:50',
            'notes'             => 'nullable|string|max:1000',
            'terms'             => 'accepted',
        ]);

        // Normalise to E.164 (+8801XXXXXXXXX) so every order stores phones in one canonical form.
        // Existing /admin filtering, /track-order lookups, and /account order matching all benefit.
        $validated['shipping_phone'] = PhoneNormalizer::normalize($validated['shipping_phone']);
        if (!empty($validated['billing_phone'])) {
            $validated['billing_phone'] = PhoneNormalizer::normalize($validated['billing_phone']);
        }

        $subtotal     = CartService::total();
        $deliveryCost = ($validated['shipping_district'] === 'Dhaka')
            ? self::DELIVERY_INSIDE_DHAKA
            : self::DELIVERY_OUTSIDE_DHAKA;

        // Re-validate coupon server-side (never trust anything from the form).
        // If the code is invalid here we ignore it silently and apply zero discount,
        // rather than fail the whole checkout.
        $couponDiscount = 0;
        $resolvedCoupon = null;
        if (!empty($validated['coupon_code'])) {
            $resolvedCoupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper(trim($validated['coupon_code']))])->first();
            if ($resolvedCoupon) {
                [$ok] = $resolvedCoupon->validateFor($subtotal, $validated['shipping_phone'] ?? null);
                if ($ok) {
                    $couponDiscount = $resolvedCoupon->calculateDiscount($subtotal);
                } else {
                    // Coupon failed server-side validation → don't pretend to apply it
                    $validated['coupon_code'] = null;
                    $resolvedCoupon = null;
                }
            } else {
                $validated['coupon_code'] = null;
            }
        }

        $total = max(0, $subtotal + $deliveryCost - $couponDiscount);

        $order = Order::create([
            'order_number'      => 'ORD-' . strtoupper(uniqid()),
            'user_id'           => auth()->id(),
            'status'            => 'pending',
            'shipping_name'     => $validated['shipping_name'],
            'shipping_phone'    => $validated['shipping_phone'],
            'shipping_district' => $validated['shipping_district'],
            'shipping_thana'    => $validated['shipping_thana'],
            'shipping_address'  => $validated['shipping_address'],
            'billing_name'      => $validated['billing_name']     ?? $validated['shipping_name'],
            'billing_phone'     => $validated['billing_phone']    ?? $validated['shipping_phone'],
            'billing_country'   => 'Bangladesh',
            'billing_district'  => $validated['billing_district'] ?? $validated['shipping_district'],
            'billing_thana'     => $validated['billing_thana']    ?? $validated['shipping_thana'],
            'billing_address'   => $validated['billing_address']  ?? $validated['shipping_address'],
            'payment_method'    => $validated['payment_method'],
            'payment_status'    => 'pending',
            'coupon_code'       => $validated['coupon_code'] ?? null,
            'coupon_discount'   => $couponDiscount,
            'notes'             => $validated['notes'] ?? null,
            'subtotal'          => $subtotal,
            'delivery_cost'     => $deliveryCost,
            'total'             => $total,
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id'      => $order->id,
                'product_id'    => $item['id'],
                'product_name'  => $item['name'],
                'product_image' => $item['image'] ?? null,
                'price'         => $item['price'],
                'quantity'      => $item['qty'],
                'subtotal'      => $item['price'] * $item['qty'],
            ]);
        }

        // Record coupon redemption + increment counter atomically
        if ($resolvedCoupon && $couponDiscount > 0) {
            DB::transaction(function () use ($resolvedCoupon, $order, $validated, $subtotal, $couponDiscount) {
                CouponRedemption::create([
                    'coupon_id'        => $resolvedCoupon->id,
                    'order_id'         => $order->id,
                    'customer_phone'   => $validated['shipping_phone'],
                    'subtotal_before'  => $subtotal,
                    'discount_applied' => $couponDiscount,
                    'used_at'          => now(),
                ]);
                $resolvedCoupon->increment('used_count');
            });
        }

        // bKash: create payment and redirect
        if ($validated['payment_method'] === 'bkash') {
            try {
                $bkash    = app(BkashService::class);
                $callback = route('bkash.callback');
                $result   = $bkash->createPayment($total, $order->order_number, $callback);

                $order->update(['bkash_payment_id' => $result['paymentID']]);

                return redirect()->away($result['bkashURL']);
            } catch (\Exception $e) {
                Log::error('bKash create payment error: ' . $e->getMessage());
                $order->delete();
                return back()->with('error', 'Could not initiate bKash payment. Please try again or choose another method.');
            }
        }

        // SSLCommerz online payment
        if ($validated['payment_method'] === 'online') {
            try {
                $ssl    = app(SslcommerzService::class);
                $result = $ssl->initiatePayment([
                    'amount'       => $total,
                    'tran_id'      => $order->order_number,
                    'cus_name'     => $validated['shipping_name'],
                    'cus_phone'    => $validated['shipping_phone'],
                    'cus_address'  => $validated['shipping_address'],
                    'cus_city'     => $validated['shipping_district'],
                    'product_name' => 'Order ' . $order->order_number,
                ]);

                return redirect()->away($result['GatewayPageURL']);

            } catch (\Exception $e) {
                Log::error('SSLCommerz initiate error: ' . $e->getMessage());
                $order->delete();
                return back()->with('error', 'Could not initiate online payment. Please try again or choose another method.');
            }
        }

        CartService::clear();

        return redirect()->route('checkout.success', $order->order_number);
    }

    public function success(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with('items')
            ->firstOrFail();

        return view('checkout.success', compact('order'));
    }

    public function invoice(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with('items')
            ->firstOrFail();

        $pdf = Pdf::loadView('checkout.invoice', compact('order'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }
}
