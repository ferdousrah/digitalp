<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Support\PhoneNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class PhoneAuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return redirect()->intended('/account');
        }
        return view('auth.login');
    }

    public function sendOtp(Request $request, OtpService $otp)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        $phone = PhoneNormalizer::normalize($request->input('phone'));
        if (!$phone) {
            return response()->json([
                'ok'      => false,
                'message' => 'Please enter a valid Bangladeshi mobile number (e.g. 01712345678).',
            ], 422);
        }

        // IP-based rate limit (defence in depth on top of OtpService throttle)
        $rlKey = 'otp-send:' . $request->ip();
        if (RateLimiter::tooManyAttempts($rlKey, 10)) {
            $seconds = RateLimiter::availableIn($rlKey);
            return response()->json([
                'ok'      => false,
                'message' => "Too many requests. Try again in {$seconds}s.",
            ], 429);
        }
        RateLimiter::hit($rlKey, 600); // 10 attempts / 10 min per IP

        $result = $otp->issue($phone, $request->ip(), $request->userAgent());

        if (!$result['ok']) {
            return response()->json([
                'ok'      => false,
                'message' => $result['reason'] ?? 'Could not send code.',
            ], 422);
        }

        // Stash phone in session so the verify step is bound to this browser
        $request->session()->put('otp_phone', $phone);

        return response()->json([
            'ok'          => true,
            'message'     => 'Code sent. Check your phone.',
            'phone'       => PhoneNormalizer::display($phone),
            'ttl_seconds' => (int) config('sms.otp.ttl_minutes', 5) * 60,
        ]);
    }

    public function verifyOtp(Request $request, OtpService $otp)
    {
        $request->validate([
            'code' => 'required|string|min:4|max:8',
        ]);

        $phone = $request->session()->get('otp_phone');
        if (!$phone) {
            return response()->json([
                'ok'      => false,
                'message' => 'Session expired. Please request a new code.',
            ], 422);
        }

        $rlKey = 'otp-verify:' . $request->ip();
        if (RateLimiter::tooManyAttempts($rlKey, 20)) {
            $seconds = RateLimiter::availableIn($rlKey);
            return response()->json([
                'ok'      => false,
                'message' => "Too many attempts. Try again in {$seconds}s.",
            ], 429);
        }
        RateLimiter::hit($rlKey, 600);

        $result = $otp->verify($phone, (string) $request->input('code'));
        if (!$result['ok']) {
            return response()->json([
                'ok'      => false,
                'message' => $result['reason'] ?? 'Invalid code.',
            ], 422);
        }

        // Resolve user — find by phone, otherwise create
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            $user = User::create([
                'name'              => 'Customer',
                'phone'             => $phone,
                'phone_verified_at' => now(),
            ]);
        } elseif (!$user->phone_verified_at) {
            $user->forceFill(['phone_verified_at' => now()])->save();
        }

        $request->session()->forget('otp_phone');
        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return response()->json([
            'ok'       => true,
            'message'  => 'Signed in successfully.',
            'redirect' => session()->pull('url.intended', url('/account')),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
