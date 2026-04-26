<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;

/**
 * Dev driver — writes the SMS to the application log instead of sending it.
 * Use this until a real SMS provider is wired up. The OTP appears in
 * storage/logs/laravel.log so you can copy it during testing.
 */
class LogSmsDriver implements SmsDriverInterface
{
    public function send(string $phone, string $message): bool
    {
        Log::channel(config('logging.default'))->info('[SMS:log] To ' . $phone . ' :: ' . $message);
        return true;
    }
}
