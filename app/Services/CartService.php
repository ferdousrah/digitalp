<?php

namespace App\Services;

use App\Models\Product;

class CartService
{
    const SESSION_KEY = 'cart';

    public static function get(): array
    {
        return session(self::SESSION_KEY, []);
    }

    public static function add(Product $product, int $qty = 1): void
    {
        $cart = self::get();
        $key  = (string) $product->id;

        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
        } else {
            $cart[$key] = [
                'id'    => $product->id,
                'name'  => $product->name,
                'slug'  => $product->slug,
                'price' => (float) $product->price,
                'image' => $product->getFirstMediaUrl('product_thumbnail') ?: $product->getFirstMediaUrl('product_images'),
                'qty'   => $qty,
            ];
        }

        session([self::SESSION_KEY => $cart]);
    }

    public static function update(string $key, int $qty): void
    {
        $cart = self::get();

        if ($qty < 1) {
            unset($cart[$key]);
        } elseif (isset($cart[$key])) {
            $cart[$key]['qty'] = $qty;
        }

        session([self::SESSION_KEY => $cart]);
    }

    public static function remove(string $key): void
    {
        $cart = self::get();
        unset($cart[$key]);
        session([self::SESSION_KEY => $cart]);
    }

    public static function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public static function total(): float
    {
        return array_reduce(self::get(), fn ($sum, $item) => $sum + ($item['price'] * $item['qty']), 0.0);
    }

    public static function itemCount(): int
    {
        return array_reduce(self::get(), fn ($sum, $item) => $sum + $item['qty'], 0);
    }

    public static function count(): int
    {
        return count(self::get());
    }
}
