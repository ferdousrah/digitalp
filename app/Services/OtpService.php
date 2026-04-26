<?php

namespace App\Services;

use App\Models\PhoneOtpCode;
use App\Services\Sms\SmsManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    public function __construct(protected SmsManager $sms) {}

    /**
     * Issue a fresh OTP for a phone, send via SMS, return ['ok'=>bool, 'reason'=>string|null].
     * Throttles per phone using the config window. The previous unconsumed codes for the
     * same phone are invalidated (consumed_at set) so only one code is active at a time.
     */
    public function issue(string $phone, ?string $ip = null, ?string $ua = null): array
    {
        $cfg = config('sms.otp');

        // Rate limit: count codes issued in the rolling window
        $issuedRecently = PhoneOtpCode::where('phone', $phone)
            ->where('created_at', '>=', Carbon::now()->subMinutes((int) $cfg['window_minutes']))
            ->count();

        if ($issuedRecently >= (int) $cfg['max_per_window']) {
            return ['ok' => false, 'reason' => 'Too many codes requested. Please try again in a few minutes.'];
        }

        // Generate code
        $code = $this->generateNumericCode((int) $cfg['length']);

        // Invalidate any active codes for this phone
        PhoneOtpCode::where('phone', $phone)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        // Persist new
        PhoneOtpCode::create([
            'phone'      => $phone,
            'code_hash'  => Hash::make($code),
            'attempts'   => 0,
            'expires_at' => now()->addMinutes((int) $cfg['ttl_minutes']),
            'ip_address' => $ip,
            'user_agent' => $ua ? mb_substr($ua, 0, 250) : null,
        ]);

        $brand = config('app.name', 'Digital Support');
        $minutes = (int) $cfg['ttl_minutes'];
        $message = "{$brand}: Your verification code is {$code}. Valid for {$minutes} minutes. Don't share it.";

        $sent = $this->sms->send($phone, $message);

        if (!$sent) {
            return ['ok' => false, 'reason' => 'Could not deliver SMS. Please try again.'];
        }

        return ['ok' => true, 'reason' => null];
    }

    /**
     * Verify a submitted code. Returns ['ok'=>bool, 'reason'=>string|null].
     * On success, the code is marked consumed.
     */
    public function verify(string $phone, string $code): array
    {
        $cfg = config('sms.otp');

        $record = PhoneOtpCode::where('phone', $phone)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (!$record) {
            return ['ok' => false, 'reason' => 'No active code. Please request a new one.'];
        }

        if ($record->isExpired()) {
            $record->update(['consumed_at' => now()]);
            return ['ok' => false, 'reason' => 'Code expired. Please request a new one.'];
        }

        if ($record->attempts >= (int) $cfg['max_attempts']) {
            $record->update(['consumed_at' => now()]);
            return ['ok' => false, 'reason' => 'Too many attempts. Please request a new code.'];
        }

        $record->increment('attempts');

        if (!Hash::check($code, $record->code_hash)) {
            $remaining = max(0, (int) $cfg['max_attempts'] - $record->attempts);
            return ['ok' => false, 'reason' => "Wrong code. {$remaining} attempt(s) left."];
        }

        $record->update(['consumed_at' => now()]);
        return ['ok' => true, 'reason' => null];
    }

    protected function generateNumericCode(int $length): string
    {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= random_int(0, 9);
        }
        return $code;
    }
}
