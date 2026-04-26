<?php

namespace App\Services\Sms;

class SmsManager implements SmsDriverInterface
{
    public function __construct(protected SmsDriverInterface $driver) {}

    public function send(string $phone, string $message): bool
    {
        return $this->driver->send($phone, $message);
    }

    public function driverName(): string
    {
        return class_basename($this->driver);
    }
}
