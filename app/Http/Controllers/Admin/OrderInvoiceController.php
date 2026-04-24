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
}
