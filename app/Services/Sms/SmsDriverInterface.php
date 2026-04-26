<?php

namespace App\Services\Sms;

interface SmsDriverInterface
{
    /**
     * Send a text message. Return true on success, false on failure.
     * Implementations should NOT throw — log and return false instead.
     */
    public function send(string $phone, string $message): bool;
}
