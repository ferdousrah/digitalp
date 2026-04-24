<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SslcommerzService
{
    protected string $initUrl;
    protected string $validateUrl;
    protected string $storeId;
    protected string $storePasswd;

    public function __construct()
    {
        $sandbox           = config('sslcommerz.sandbox', true);
        $this->initUrl     = $sandbox
            ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php'
            : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';
        $this->validateUrl = $sandbox
            ? 'https://sandbox.sslcommerz.com/validator/api/validationDetails.php'
            : 'https://securepay.sslcommerz.com/validator/api/validationDetails.php';
        $this->storeId     = config('sslcommerz.store_id');
        $this->storePasswd = config('sslcommerz.store_passwd');
    }

    public function initiatePayment(array $order): array
    {
        $response = Http::asForm()->post($this->initUrl, [
            'store_id'        => $this->storeId,
            'store_passwd'    => $this->storePasswd,
            'total_amount'    => number_format($order['amount'], 2, '.', ''),
            'currency'        => 'BDT',
            'tran_id'         => $order['tran_id'],
            'success_url'     => route('sslcommerz.success'),
            'fail_url'        => route('sslcommerz.fail'),
            'cancel_url'      => route('sslcommerz.cancel'),
            'ipn_url'         => route('sslcommerz.ipn'),
            'cus_name'        => $order['cus_name'],
            'cus_phone'       => $order['cus_phone'],
            'cus_add1'        => $order['cus_address'],
            'cus_city'        => $order['cus_city'],
            'cus_country'     => 'Bangladesh',
            'cus_email'       => $order['cus_email'] ?? 'customer@example.com',
            'shipping_method' => 'YES',
            'ship_name'       => $order['cus_name'],
            'ship_add1'       => $order['cus_address'],
            'ship_city'       => $order['cus_city'],
            'ship_country'    => 'Bangladesh',
            'product_name'    => $order['product_name'],
            'product_category'=> 'Electronics',
            'product_profile' => 'physical-goods',
        ]);

        Log::info('SSLCommerz initiate response', ['body' => $response->body()]);

        if ($response->successful()) {
            $data = $response->json();
            if (($data['status'] ?? '') === 'SUCCESS') {
                return $data;
            }
            throw new \Exception('SSLCommerz init failed: ' . ($data['failedreason'] ?? $response->body()));
        }

        throw new \Exception('SSLCommerz HTTP error: ' . $response->body());
    }

    public function validatePayment(string $valId): array
    {
        $response = Http::get($this->validateUrl, [
            'val_id'       => $valId,
            'store_id'     => $this->storeId,
            'store_passwd' => $this->storePasswd,
            'format'       => 'json',
        ]);

        Log::info('SSLCommerz validate response', ['val_id' => $valId, 'body' => $response->body()]);

        return $response->json();
    }

    public function verifyHash(array $postData): bool
    {
        // Verify IPN hash to prevent fraud
        if (empty($postData['verify_sign']) || empty($postData['verify_key'])) {
            return false;
        }

        $preDefinedKey = $this->storePasswd;
        $keys          = explode(',', $postData['verify_key']);
        $hashString    = '';

        foreach ($keys as $key) {
            $hashString .= $key . '=' . ($postData[$key] ?? '') . '&';
        }

        $hashString .= 'store_passwd=' . md5($preDefinedKey);
        $hash        = md5($hashString);

        return $hash === $postData['verify_sign'];
    }
}
