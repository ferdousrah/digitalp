<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\InventoryService;

class OrderObserver
{
    public function created(Order $order): void
    {
        $order->logActivity(
            event: 'created',
            title: 'Order created',
            description: "Order #{$order->order_number} was placed.",
            meta: ['total' => (float) $order->total]
        );
    }

    public function updated(Order $order): void
    {
        $changes = $order->getChanges();

        if (isset($changes['status'])) {
            $before = $order->getOriginal('status');
            $after  = $changes['status'];
            $order->logActivity(
                event: 'status_changed',
                title: 'Status changed',
                description: ucfirst((string) $before) . ' → ' . ucfirst((string) $after),
                meta: ['from' => $before, 'to' => $after],
            );

            // Side effects: stamp shipped_at / delivered_at / cancelled_at
            if ($after === 'shipped' && !$order->shipped_at) {
                $order->forceFill(['shipped_at' => now()])->saveQuietly();
            }
            if ($after === 'delivered' && !$order->delivered_at) {
                $order->forceFill(['delivered_at' => now()])->saveQuietly();
            }
            if ($after === 'cancelled' && !$order->cancelled_at) {
                $order->forceFill(['cancelled_at' => now()])->saveQuietly();
            }

            // ── Inventory side-effects ──
            // Deduct stock when an order ships (or is marked delivered from non-shipped state).
            // Uses InventoryService which checks for dup-deduction automatically.
            if (in_array($after, ['shipped', 'delivered'])) {
                InventoryService::deductForOrder($order->load('items'));
            }

            // Restock on cancel/refund if we had previously deducted
            if (in_array($after, ['cancelled', 'refunded'])) {
                InventoryService::returnForOrder($order->load('items'));
            }
        }

        if (isset($changes['payment_status'])) {
            $before = $order->getOriginal('payment_status');
            $after  = $changes['payment_status'];
            $order->logActivity(
                event: 'payment_updated',
                title: 'Payment status changed',
                description: ucfirst((string) $before) . ' → ' . ucfirst((string) $after),
                meta: ['from' => $before, 'to' => $after],
            );
        }

        if (isset($changes['tracking_number']) && $changes['tracking_number']) {
            $order->logActivity(
                event: 'tracking_added',
                title: 'Tracking number set',
                description: 'Tracking: ' . $changes['tracking_number']
                    . ($order->courier_service ? ' — ' . ($order->courier_service) : ''),
                meta: ['tracking' => $changes['tracking_number'], 'courier' => $order->courier_service],
            );
        }

        if (isset($changes['refund_amount']) && (float) $changes['refund_amount'] > 0) {
            $order->logActivity(
                event: 'refunded',
                title: 'Refund recorded',
                description: 'Refund amount: ৳' . number_format((float) $changes['refund_amount'], 2),
                meta: ['amount' => (float) $changes['refund_amount']],
            );
        }
    }
}
