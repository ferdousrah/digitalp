<?php

namespace App\Services;

use App\Models\Order;
use App\Services\Sms\SmsManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Sends a "please review your purchase" message after an order is delivered:
 * SMS via the configured SMS driver (bulksmsbd) and email via the Resend HTTP API.
 * Idempotent — guarded by orders.review_request_sent_at.
 */
class ReviewRequestService
{
    public function __construct(protected SmsManager $sms) {}

    public function sendForOrder(Order $order): void
    {
        if (! config('services.review_request.enabled', true) || $order->review_request_sent_at) {
            return;
        }

        $order->loadMissing('items.product', 'user');

        // Distinct, still-live products the customer bought.
        $products = $order->items
            ->map(fn ($i) => $i->product)
            ->filter(fn ($p) => $p && $p->is_active)
            ->unique('id')
            ->values();

        // Nothing to review — mark done so we don't retry.
        if ($products->isEmpty()) {
            $order->forceFill(['review_request_sent_at' => now()])->saveQuietly();
            return;
        }

        $site  = SettingService::get('site_name', config('app.name'));
        $name  = $order->shipping_name ?: 'there';
        $phone = $order->shipping_phone ?: $order->billing_phone;
        $email = $order->user?->email;
        $first = $products->first();

        if ($phone) {
            try {
                $this->sms->send($phone, sprintf(
                    "%s: Thanks for your order! How was '%s'? Please rate it: %s",
                    $site, Str::limit($first->name, 40), route('products.show', $first)
                ));
            } catch (\Throwable $e) {
                Log::warning('Review-request SMS failed', ['order' => $order->id, 'error' => $e->getMessage()]);
            }
        }

        if ($email && ($key = config('services.resend.key'))) {
            try {
                $this->sendEmail($key, $email, $name, $site, $products);
            } catch (\Throwable $e) {
                Log::warning('Review-request email failed', ['order' => $order->id, 'error' => $e->getMessage()]);
            }
        }

        $order->forceFill(['review_request_sent_at' => now()])->saveQuietly();
    }

    protected function sendEmail(string $key, string $email, string $name, string $site, $products): void
    {
        $from = config('services.resend.from')
            ?: ('noreply@' . parse_url(config('app.url'), PHP_URL_HOST));

        $items = $products->map(fn ($p) =>
            '<li style="margin:8px 0;"><a href="' . e(route('products.show', $p))
            . '" style="color:#f97316;text-decoration:none;font-weight:600;">' . e($p->name) . '</a></li>'
        )->implode('');

        $html = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:560px;margin:0 auto;color:#111827;">'
            . '<h2 style="color:#111827;margin:0 0 12px;">How was your order, ' . e($name) . '? ⭐</h2>'
            . '<p style="color:#374151;line-height:1.6;">Thanks for shopping with ' . e($site)
            . '! Your feedback helps other shoppers. Please take a moment to rate what you bought:</p>'
            . '<ul style="padding-left:18px;">' . $items . '</ul>'
            . '<p style="color:#6b7280;font-size:13px;margin-top:24px;">Open a product and leave a quick star rating — it only takes a few seconds.</p>'
            . '</div>';

        Http::withToken($key)
            ->acceptJson()
            ->timeout(10)
            ->post('https://api.resend.com/emails', [
                'from'    => $from,
                'to'      => [$email],
                'subject' => 'How was your order? Leave a quick review ⭐',
                'html'    => $html,
            ])
            ->throw();
    }
}
