<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * BulkSMSBD.net driver. Endpoint accepts api_key, senderid, number, message.
 * Set in .env:
 *   SMS_DRIVER=bulksmsbd
 *   BULKSMSBD_API_KEY=...
 *   BULKSMSBD_SENDER_ID=...
 *
 * Phone must be local 11-digit form (01712345678) — the service strips +880.
 */
class BulkSmsBdDriver implements SmsDriverInterface
{
    public function __construct(
        protected string $apiKey,
        protected string $senderId,
        protected string $endpoint = 'https://bulksmsbd.net/api/smsapi'
    ) {}

    public function send(string $phone, string $message): bool
    {
        // BulkSMSBD wants 11-digit local number — strip +880
        $local = preg_replace('/^\+?880/', '', $phone);
        if (!str_starts_with($local, '0')) {
            $local = '0' . $local;
        }

        try {
            $response = Http::timeout(10)->asForm()->post($this->endpoint, [
                'api_key'  => $this->apiKey,
                'type'     => 'text',
                'number'   => $local,
                'senderid' => $this->senderId,
                'message'  => $message,
            ]);

            $body = $response->json();
            $code = (int) ($body['response_code'] ?? 0);

            if ($response->successful() && $code === 202) {
                return true;
            }

            Log::warning('[SMS:bulksmsbd] Failed', [
                'phone'    => $phone,
                'response' => $response->body(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::warning('[SMS:bulksmsbd] Exception: ' . $e->getMessage());
            return false;
        }
    }
}
