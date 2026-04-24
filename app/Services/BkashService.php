<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashService
{
    protected string $baseUrl;
    protected string $appKey;
    protected string $appSecret;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $sandbox       = config('bkash.sandbox', true);
        $this->baseUrl = $sandbox
            ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout'
            : 'https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/checkout';

        $this->appKey    = config('bkash.app_key');
        $this->appSecret = config('bkash.app_secret');
        $this->username  = config('bkash.username');
        $this->password  = config('bkash.password');
    }

    public function getToken(): string
    {
        return Cache::remember('bkash_access_token', 3500, function () {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'username'     => $this->username,
                'password'     => $this->password,
            ])->post($this->baseUrl . '/token/grant', [
                'app_key'    => $this->appKey,
                'app_secret' => $this->appSecret,
            ]);

            Log::info('bKash grant token response', ['body' => $response->body()]);

            if ($response->successful() && $response->json('statusCode') === '0000') {
                return $response->json('id_token');
            }

            throw new \Exception('bKash token grant failed: ' . $response->body());
        });
    }

    public function createPayment(float $amount, string $invoiceNumber, string $callbackUrl): array
    {
        $token    = $this->getToken();
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $token,
            'X-APP-Key'     => $this->appKey,
        ])->post($this->baseUrl . '/create', [
            'mode'                  => '0011',
            'payerReference'        => ' ',
            'callbackURL'           => $callbackUrl,
            'amount'                => number_format($amount, 2, '.', ''),
            'currency'              => 'BDT',
            'intent'                => 'sale',
            'merchantInvoiceNumber' => $invoiceNumber,
        ]);

        Log::info('bKash create payment response', ['body' => $response->body()]);

        if ($response->successful() && $response->json('statusCode') === '0000') {
            return $response->json();
        }

        throw new \Exception('bKash create payment failed: ' . $response->body());
    }

    public function executePayment(string $paymentId): array
    {
        $token    = $this->getToken();
        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => $token,
            'X-APP-Key'     => $this->appKey,
        ])->post($this->baseUrl . '/execute', [
            'paymentID' => $paymentId,
        ]);

        Log::info('bKash execute payment response', ['paymentID' => $paymentId, 'body' => $response->body()]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('bKash execute payment failed: ' . $response->body());
    }

    public function queryPayment(string $paymentId): array
    {
        $token    = $this->getToken();
        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => $token,
            'X-APP-Key'     => $this->appKey,
        ])->post($this->baseUrl . '/payment/status', [
            'paymentID' => $paymentId,
        ]);

        return $response->json();
    }
}
