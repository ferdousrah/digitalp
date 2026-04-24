<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * All stock changes must go through this service so movements are logged
 * atomically and the product's stock_quantity stays in sync with the ledger.
 */
class InventoryService
{
    /**
     * Apply a stock change and log it. Returns the created StockMovement.
     *
     * @param int    $delta     Positive to add, negative to deduct.
     * @param string $type      purchase | sale | adjustment | return
     * @param Model  $reference The record that caused this (PO, Order, null for manual).
     */
    public static function move(
        Product $product,
        int $delta,
        string $type,
        ?Model $reference = null,
        ?string $notes = null
    ): StockMovement {
        return DB::transaction(function () use ($product, $delta, $type, $reference, $notes) {
            // Lock the product row for the duration of the transaction
            $product = Product::where('id', $product->id)->lockForUpdate()->first();

            $newQty = max(0, (int) $product->stock_quantity + $delta);
            $product->stock_quantity = $newQty;
            // Auto-flip in_stock based on quantity
            $product->in_stock = $newQty > 0;
            $product->save();

            return StockMovement::create([
                'product_id'     => $product->id,
                'type'           => $type,
                'quantity'       => $delta,
                'balance_after'  => $newQty,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id'   => $reference?->getKey(),
                'notes'          => $notes,
                'user_id'        => auth()->id(),
            ]);
        });
    }

    /**
     * Receive a purchase order item — adds its outstanding qty to stock.
     * If $quantity is null, receives all outstanding.
     */
    public static function receivePurchaseOrderItem(
        \App\Models\PurchaseOrderItem $item,
        ?int $quantity = null
    ): ?StockMovement {
        return DB::transaction(function () use ($item, $quantity) {
            $outstanding = $item->outstanding();
            if ($outstanding <= 0) return null;

            $toReceive = $quantity === null ? $outstanding : min($quantity, $outstanding);
            if ($toReceive <= 0) return null;

            $movement = static::move(
                product: $item->product,
                delta: $toReceive,
                type: 'purchase',
                reference: $item->purchaseOrder,
                notes: "Received from PO #{$item->purchaseOrder->po_number}",
            );

            $item->increment('quantity_received', $toReceive);

            // Weighted Average Cost:
            //   WAC = ((existing_qty × existing_cost) + (received_qty × unit_cost))
            //         / (existing_qty + received_qty)
            // Keeps cost_price as the blended true cost of on-hand stock instead of
            // clobbering with whatever the latest PO paid.
            if ($item->unit_cost > 0 && $toReceive > 0) {
                $product      = $item->product->fresh();
                $existingQty  = max(0, (int) $product->stock_quantity - $toReceive); // stock before this receipt
                $existingCost = (float) $product->cost_price;

                $totalQty = $existingQty + $toReceive;
                $newCost  = $totalQty > 0
                    ? (($existingQty * $existingCost) + ($toReceive * (float) $item->unit_cost)) / $totalQty
                    : (float) $item->unit_cost;

                $product->forceFill(['cost_price' => round($newCost, 2)])->save();
            }

            $item->purchaseOrder->recomputeStatus();

            return $movement;
        });
    }

    /**
     * Deduct stock for a sold order. Called from OrderObserver when
     * status transitions into "shipped" (if not already deducted).
     */
    public static function deductForOrder(\App\Models\Order $order): void
    {
        DB::transaction(function () use ($order) {
            // Skip if we've already created sale movements for this order
            $alreadyDeducted = StockMovement::where('reference_type', \App\Models\Order::class)
                ->where('reference_id', $order->id)
                ->where('type', 'sale')
                ->exists();
            if ($alreadyDeducted) return;

            foreach ($order->items as $item) {
                if (!$item->product_id) continue;
                $product = Product::find($item->product_id);
                if (!$product) continue;

                static::move(
                    product: $product,
                    delta: -(int) $item->quantity,
                    type: 'sale',
                    reference: $order,
                    notes: "Order #{$order->order_number}",
                );
            }
        });
    }

    /**
     * Return stock back when a shipped order is cancelled / refunded.
     */
    public static function returnForOrder(\App\Models\Order $order): void
    {
        DB::transaction(function () use ($order) {
            // Only return if we previously deducted (sale entry exists)
            $wasDeducted = StockMovement::where('reference_type', \App\Models\Order::class)
                ->where('reference_id', $order->id)
                ->where('type', 'sale')
                ->exists();
            if (!$wasDeducted) return;

            $alreadyReturned = StockMovement::where('reference_type', \App\Models\Order::class)
                ->where('reference_id', $order->id)
                ->where('type', 'return')
                ->exists();
            if ($alreadyReturned) return;

            foreach ($order->items as $item) {
                if (!$item->product_id) continue;
                $product = Product::find($item->product_id);
                if (!$product) continue;

                static::move(
                    product: $product,
                    delta: (int) $item->quantity,
                    type: 'return',
                    reference: $order,
                    notes: "Restocked from cancelled order #{$order->order_number}",
                );
            }
        });
    }
}
