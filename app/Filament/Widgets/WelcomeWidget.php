<?php

namespace App\Filament\Widgets;

use App\Models\ContactSubmission;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class WelcomeWidget extends Widget
{
    protected static string $view = 'filament.widgets.welcome-widget';
    protected static ?int $sort = -2; // show above Stats
    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $user = auth()->user();
        $hour = (int) now()->format('G');
        $greeting = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default    => 'Good evening',
        };

        // Today's snapshot
        $ordersToday = Order::whereDate('created_at', today())->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $newContacts = ContactSubmission::where('is_read', false)->count();
        $lowStock = Product::whereColumn('stock_quantity', '<=', 'min_stock_quantity')
            ->where('in_stock', true)
            ->count();

        return [
            'greeting'      => $greeting,
            'userName'      => $user?->name ?? 'there',
            'today'         => Carbon::now()->format('l, F j'),
            'ordersToday'   => $ordersToday,
            'pendingOrders' => $pendingOrders,
            'newContacts'   => $newContacts,
            'lowStock'      => $lowStock,
        ];
    }
}
