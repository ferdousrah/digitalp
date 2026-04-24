<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\SettingService;
use Illuminate\Http\Request;

class OrderInvoiceController extends Controller
{
    public function show(Request $request, Order $order)
    {
        abort_unless(auth()->check(), 403);

        $user = auth()->user();
        abort_unless($user->hasRole('super_admin') || $user->can('orders.view'), 403);

        $order->load('items');

        return view('admin.order-invoice', [
            'order'     => $order,
            'siteName'  => SettingService::get('site_name', 'Digital Support'),
            'address'   => SettingService::get('contact_address', ''),
            'phone'     => SettingService::get('contact_phone', ''),
            'email'     => SettingService::get('contact_email', ''),
            'logo'      => SettingService::get('site_logo'),
        ]);
    }

    public function label(Request $request, Order $order)
    {
        abort_unless(auth()->check(), 403);
        $user = auth()->user();
        abort_unless($user->hasRole('super_admin') || $user->can('orders.view'), 403);

        $order->load('items');

        return view('admin.order-courier-label', [
            'orders'       => collect([$order]),
            'siteName'     => SettingService::get('site_name', 'Digital Support'),
            'phone'        => SettingService::get('contact_phone', ''),
            'merchantId'   => SettingService::get('courier_merchant_id', '—'),
            'merchantCode' => SettingService::get('courier_merchant_code', strtoupper(substr(preg_replace('/[^A-Za-z]/', '', SettingService::get('site_name', 'Shop')), 0, 8))),
        ]);
    }

    public function labels(Request $request)
    {
        abort_unless(auth()->check(), 403);
        $user = auth()->user();
        abort_unless($user->hasRole('super_admin') || $user->can('orders.view'), 403);

        $ids = collect(explode(',', (string) $request->query('ids', '')))
            ->map(fn ($v) => (int) trim($v))
            ->filter()
            ->values()
            ->all();

        abort_if(empty($ids), 400, 'No order IDs provided.');

        $orders = Order::with('items')->whereIn('id', $ids)->get();

        return view('admin.order-courier-label', [
            'orders'       => $orders,
            'siteName'     => SettingService::get('site_name', 'Digital Support'),
            'phone'        => SettingService::get('contact_phone', ''),
            'merchantId'   => SettingService::get('courier_merchant_id', '—'),
            'merchantCode' => SettingService::get('courier_merchant_code', strtoupper(substr(preg_replace('/[^A-Za-z]/', '', SettingService::get('site_name', 'Shop')), 0, 8))),
        ]);
    }
}
