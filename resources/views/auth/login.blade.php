@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<div class="auth-page">
    {{-- Subtle dot pattern background --}}
    <div class="auth-dots" aria-hidden="true"></div>

    <div x-data="phoneAuth()" class="auth-card">

        {{-- Step 1: Phone --}}
        <div x-show="step === 'phone'" x-transition>
            <div class="auth-icon-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>

            <h1 class="auth-h1">Welcome back</h1>
            <p class="auth-sub">Sign in with your mobile number — we'll text you a 6-digit verification code. No password needed.</p>

            <form @submit.prevent="sendOtp" class="auth-form">
                <label for="phone" class="auth-label">Mobile number</label>
                <div class="auth-phone-input" :class="phoneError ? 'is-error' : ''">
                    <span class="auth-phone-prefix">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>+880</span>
                    </span>
                    <input id="phone" type="tel" x-model="phone" inputmode="numeric" maxlength="10" pattern="[0-9]{10}" placeholder="1712345678" autocomplete="tel" data-no-type
                        @keypress="if (!/[0-9]/.test($event.key)) $event.preventDefault()"
                        @paste="$nextTick(() => { phone = (phone || '').replace(/\D/g,'').slice(0,10); })"
                        @input="phoneError = ''">
                </div>
                <p x-show="phoneError" x-cloak x-text="phoneError" class="auth-err"></p>

                <button type="submit" :disabled="loading" class="auth-btn">
                    <span x-show="!loading">Continue with code</span>
                    <span x-show="loading" x-cloak class="auth-btn-loading">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="9" stroke-opacity="0.25"/>
                            <path stroke-linecap="round" d="M21 12a9 9 0 00-9-9"/>
                        </svg>
                        Sending&hellip;
                    </span>
                    <svg x-show="!loading" class="auth-btn-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </button>

                <p class="auth-fineprint">
                    By continuing, you agree to our
                    <a href="{{ url('/page/terms-of-service') }}">Terms</a> and
                    <a href="{{ url('/page/privacy-policy') }}">Privacy Policy</a>.
                </p>
            </form>
        </div>

        {{-- Step 2: OTP --}}
        <div x-show="step === 'otp'" x-transition x-cloak>
            <div class="auth-icon-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>

            <h1 class="auth-h1">Verify your number</h1>
            <p class="auth-sub">
                We sent a 6-digit code to<br>
                <span class="auth-phone-tag" x-text="phoneDisplay"></span>
            </p>

            <form @submit.prevent="verifyOtp" class="auth-form">
                <label for="otp" class="auth-label">Verification code</label>
                <input id="otp" type="text" x-model="code" x-ref="codeInput" inputmode="numeric" maxlength="6"
                    autocomplete="one-time-code" placeholder="••••••" class="auth-otp" data-no-type
                    :class="otpError ? 'is-error' : (otpExpired ? 'is-error' : '')"
                    :disabled="otpExpired"
                    @keypress="if (!/[0-9]/.test($event.key)) $event.preventDefault()"
                    @input="otpError = ''; if (code.length === 6 && !otpExpired) verifyOtp();">
                <p x-show="otpError" x-cloak x-text="otpError" class="auth-err"></p>

                {{-- Expiry timer --}}
                <div class="auth-timer" :class="otpExpired ? 'is-expired' : (otpExpiresIn <= 30 ? 'is-warning' : '')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/>
                    </svg>
                    <span x-show="!otpExpired">Code expires in <strong x-text="otpExpiryDisplay"></strong></span>
                    <span x-show="otpExpired" x-cloak>Code expired — tap <strong>Resend</strong> for a new one</span>
                </div>

                <button type="submit" :disabled="loading || code.length < 4 || otpExpired" class="auth-btn">
                    <span x-show="!loading">Verify &amp; sign in</span>
                    <span x-show="loading" x-cloak class="auth-btn-loading">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="9" stroke-opacity="0.25"/>
                            <path stroke-linecap="round" d="M21 12a9 9 0 00-9-9"/>
                        </svg>
                        Verifying&hellip;
                    </span>
                    <svg x-show="!loading" class="auth-btn-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </button>

                <div class="auth-otp-actions">
                    <button type="button" @click="reset()" class="auth-link auth-link-grey">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        Change number
                    </button>
                    <button type="button" @click="resend()" :disabled="resendCooldown > 0 || loading" class="auth-link auth-link-orange">
                        <span x-text="resendCooldown > 0 ? `Resend in ${resendCooldown}s` : 'Resend code'"></span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Trust strip --}}
        <div class="auth-trust">
            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg> End-to-end encrypted</span>
            <span class="auth-trust-dot"></span>
            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> No password</span>
        </div>
    </div>

    <p class="auth-help">
        Need help signing in? <a href="{{ url('/contact') }}">Contact support</a>
    </p>
</div>

<style>
[x-cloak] { display:none !important; }

.auth-page {
    position:relative;
    min-height:70vh;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    padding:64px 20px;
    background:#fff;
    overflow:hidden;
}

/* Subtle decorative dot grid */
.auth-dots {
    position:absolute; inset:0;
    background-image:radial-gradient(circle, #e2e8f0 1px, transparent 1px);
    background-size:24px 24px;
    background-position:0 0;
    mask-image:radial-gradient(ellipse 70% 60% at 50% 50%, black 30%, transparent 80%);
    -webkit-mask-image:radial-gradient(ellipse 70% 60% at 50% 50%, black 30%, transparent 80%);
    opacity:0.7;
    pointer-events:none;
}

/* ─── Card ─── */
.auth-card {
    position:relative;
    width:100%;
    max-width:440px;
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:20px;
    padding:44px 40px 36px;
    box-shadow:
        0 1px 3px rgba(15,23,42,0.04),
        0 24px 48px -16px rgba(15,23,42,0.12);
    text-align:center;
}

/* Top icon chip */
.auth-icon-chip {
    width:64px; height:64px;
    margin:0 auto 22px;
    border-radius:18px;
    background:linear-gradient(135deg,#fff7ed 0%,#ffedd5 100%);
    border:1px solid #fed7aa;
    display:flex; align-items:center; justify-content:center;
    color:#f97316;
    box-shadow:0 6px 18px -6px rgba(249,115,22,0.35);
    transition:transform 0.4s cubic-bezier(.4,0,.2,1);
}
.auth-icon-chip:hover { transform:rotate(-6deg) scale(1.05); }
.auth-icon-chip svg { width:30px; height:30px; }

/* Headlines */
.auth-h1 {
    font-size:1.7rem;
    font-weight:800;
    color:#0f172a;
    letter-spacing:-0.02em;
    margin:0 0 8px;
    line-height:1.15;
}
.auth-sub {
    font-size:0.95rem;
    color:#64748b;
    line-height:1.6;
    margin:0 0 28px;
}
.auth-phone-tag {
    display:inline-block;
    margin-top:6px;
    color:#0f172a; font-weight:700;
    background:#f1f5f9; padding:4px 10px; border-radius:8px;
    font-variant-numeric:tabular-nums;
    border:1px solid #e2e8f0;
}

/* Form (left-aligned inside the centered card) */
.auth-form { text-align:left; }

.auth-label {
    display:block;
    font-size:0.74rem;
    font-weight:800;
    letter-spacing:0.08em;
    text-transform:uppercase;
    color:#475569;
    margin-bottom:10px;
}

.auth-phone-input {
    display:flex;
    align-items:stretch;
    border:1.5px solid #e2e8f0;
    border-radius:12px;
    overflow:hidden;
    background:#fff;
    transition:border-color 0.2s, box-shadow 0.2s;
}
.auth-phone-input:focus-within {
    border-color:#f97316;
    box-shadow:0 0 0 4px rgba(249,115,22,0.12);
}
.auth-phone-input.is-error {
    border-color:#ef4444;
    box-shadow:0 0 0 4px rgba(239,68,68,0.12);
}
.auth-phone-prefix {
    display:inline-flex; align-items:center; gap:8px;
    padding:0 16px;
    background:#f8fafc;
    color:#0f172a;
    font-weight:700; font-size:0.95rem;
    border-right:1.5px solid #e2e8f0;
    white-space:nowrap;
}
.auth-phone-prefix svg { width:14px; height:14px; color:#64748b; }
.auth-phone-input input {
    flex:1; min-width:0;
    padding:15px 14px;
    border:none; outline:none;
    font-size:1rem;
    font-weight:600;
    color:#0f172a;
    background:transparent;
    font-variant-numeric:tabular-nums;
    letter-spacing:0.02em;
}

.auth-otp {
    width:100%;
    padding:20px 14px;
    border:1.5px solid #e2e8f0;
    border-radius:12px;
    font-size:1.7rem; font-weight:800;
    letter-spacing:0.6em;
    text-align:center;
    color:#0f172a;
    outline:none;
    transition:border-color 0.2s, box-shadow 0.2s;
    font-variant-numeric:tabular-nums;
    background:#fff;
}
.auth-otp:focus { border-color:#f97316; box-shadow:0 0 0 4px rgba(249,115,22,0.12); }
.auth-otp.is-error { border-color:#ef4444; box-shadow:0 0 0 4px rgba(239,68,68,0.12); }
.auth-otp::placeholder { color:#cbd5e1; letter-spacing:0.4em; }

.auth-err {
    color:#ef4444;
    font-size:0.82rem;
    font-weight:500;
    margin:8px 0 0;
}

/* OTP expiry timer */
.auth-timer {
    display:flex; align-items:center; justify-content:center; gap:8px;
    margin-top:14px;
    padding:10px 14px;
    background:#f8fafc;
    border:1px solid #e2e8f0;
    border-radius:10px;
    font-size:0.82rem; font-weight:500;
    color:#64748b;
    transition:all 0.25s;
}
.auth-timer svg { width:14px; height:14px; flex-shrink:0; }
.auth-timer strong { color:#0f172a; font-weight:700; font-variant-numeric:tabular-nums; letter-spacing:0.02em; }
.auth-timer.is-warning {
    background:#fff7ed; border-color:#fed7aa; color:#9a3412;
}
.auth-timer.is-warning strong { color:#c2410c; }
.auth-timer.is-expired {
    background:#fef2f2; border-color:#fecaca; color:#b91c1c;
}
.auth-timer.is-expired strong { color:#991b1b; }
.auth-otp:disabled { opacity:0.55; cursor:not-allowed; background:#f8fafc; }

.auth-btn {
    width:100%;
    margin-top:20px;
    padding:15px;
    background:#0f172a;
    color:#fff;
    font-size:0.86rem;
    font-weight:800;
    letter-spacing:0.06em;
    text-transform:uppercase;
    border:none;
    border-radius:12px;
    cursor:pointer;
    display:inline-flex; align-items:center; justify-content:center; gap:10px;
    transition:all 0.25s;
    box-shadow:0 6px 16px rgba(15,23,42,0.18);
}
.auth-btn:hover:not(:disabled) {
    background:#f97316;
    box-shadow:0 8px 22px rgba(249,115,22,0.32);
    transform:translateY(-1px);
}
.auth-btn:disabled { opacity:0.55; cursor:not-allowed; }
.auth-btn-arrow { width:16px; height:16px; transition:transform 0.25s; }
.auth-btn:hover:not(:disabled) .auth-btn-arrow { transform:translateX(3px); }
.auth-btn-loading { display:inline-flex; align-items:center; gap:8px; }
.auth-btn-loading svg { width:16px; height:16px; animation:authSpin 0.9s linear infinite; }
@keyframes authSpin { to { transform:rotate(360deg); } }

.auth-fineprint {
    font-size:0.75rem;
    color:#94a3b8;
    text-align:center;
    margin:18px 0 0;
    line-height:1.6;
}
.auth-fineprint a {
    color:#475569;
    text-decoration:underline;
    text-underline-offset:2px;
}
.auth-fineprint a:hover { color:#0f172a; }

.auth-otp-actions {
    display:flex; justify-content:space-between; align-items:center;
    margin-top:18px;
    font-size:0.85rem;
}
.auth-link {
    background:none; border:none; cursor:pointer; padding:0;
    display:inline-flex; align-items:center; gap:6px;
    font-weight:700;
    transition:color 0.2s, gap 0.25s;
}
.auth-link svg { width:13px; height:13px; transition:transform 0.25s; }
.auth-link-grey { color:#64748b; }
.auth-link-grey:hover:not(:disabled) { color:#0f172a; gap:10px; }
.auth-link-grey:hover:not(:disabled) svg { transform:translateX(-2px); }
.auth-link-orange { color:#f97316; }
.auth-link-orange:hover:not(:disabled) { color:#ea580c; }
.auth-link:disabled { opacity:0.45; cursor:not-allowed; }

/* Trust strip inside the card */
.auth-trust {
    display:flex; align-items:center; justify-content:center; gap:14px;
    flex-wrap:wrap;
    margin-top:32px;
    padding-top:22px;
    border-top:1px solid #f1f5f9;
    font-size:0.74rem;
    color:#64748b;
    font-weight:600;
}
.auth-trust span { display:inline-flex; align-items:center; gap:5px; }
.auth-trust svg { width:13px; height:13px; color:#16a34a; }
.auth-trust-dot { width:3px; height:3px; border-radius:50%; background:#cbd5e1; flex-shrink:0; }

/* Help link below the card */
.auth-help {
    position:relative;
    margin:24px 0 0;
    font-size:0.85rem;
    color:#64748b;
}
.auth-help a {
    color:#f97316;
    font-weight:700;
    text-decoration:none;
}
.auth-help a:hover { color:#ea580c; }

@media (max-width: 480px) {
    .auth-page { padding:32px 16px; }
    .auth-card { padding:32px 24px 28px; border-radius:16px; }
    .auth-h1 { font-size:1.4rem; }
}
</style>

<script>
    function phoneAuth() {
        return {
            step: 'phone',
            phone: @json(request()->query('phone', '')),
            phoneDisplay: '',
            phoneError: '',
            code: '',
            otpError: '',
            loading: false,
            resendCooldown: 0,
            otpExpiresIn: 0,           // seconds remaining until current code expires
            _expiryTimer: null,
            csrf: document.querySelector('meta[name="csrf-token"]').content,

            get otpExpiryDisplay() {
                const m = Math.floor(this.otpExpiresIn / 60);
                const s = this.otpExpiresIn % 60;
                return m + ':' + String(s).padStart(2, '0');
            },
            get otpExpired() {
                return this.step === 'otp' && this.otpExpiresIn <= 0;
            },
            startExpiry(seconds) {
                this.otpExpiresIn = Math.max(0, parseInt(seconds || 0, 10));
                if (this._expiryTimer) clearInterval(this._expiryTimer);
                this._expiryTimer = setInterval(() => {
                    if (this.otpExpiresIn > 0) this.otpExpiresIn--;
                    if (this.otpExpiresIn <= 0 && this._expiryTimer) {
                        clearInterval(this._expiryTimer);
                        this._expiryTimer = null;
                    }
                }, 1000);
            },

            init() {
                if (this.phone) {
                    this.phone = this.phone.replace(/[^\d+]/g, '').replace(/^\+?880/, '').replace(/^0/, '');
                }
            },

            async sendOtp() {
                if (this.loading) return;
                const digits = (this.phone || '').replace(/\D/g, '');
                if (digits.length !== 10 || !/^1[3-9]\d{8}$/.test(digits)) {
                    this.phoneError = 'Enter a 10-digit BD mobile (e.g. 1712345678).';
                    return;
                }
                this.loading = true;
                this.phoneError = '';
                try {
                    const res = await fetch('{{ route('auth.otp.send') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                        },
                        body: JSON.stringify({ phone: this.phone }),
                    });
                    const data = await res.json();
                    if (res.ok && data.ok) {
                        this.phoneDisplay = data.phone || this.phone;
                        this.step = 'otp';
                        this.startResendCooldown(45);
                        this.startExpiry(data.ttl_seconds || 300);
                        this.$nextTick(() => this.$refs.codeInput && this.$refs.codeInput.focus());
                        if (window.showToast) window.showToast(data.message || 'Code sent.', 'success');
                    } else {
                        this.phoneError = data.message || 'Could not send code.';
                    }
                } catch (e) {
                    this.phoneError = 'Network error. Please try again.';
                } finally {
                    this.loading = false;
                }
            },

            async verifyOtp() {
                if (this.loading) return;
                if (!this.code || this.code.length < 4) {
                    this.otpError = 'Please enter the code.';
                    return;
                }
                this.loading = true;
                this.otpError = '';
                try {
                    const res = await fetch('{{ route('auth.otp.verify') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                        },
                        body: JSON.stringify({ code: this.code }),
                    });
                    const data = await res.json();
                    if (res.ok && data.ok) {
                        if (window.showToast) window.showToast(data.message || 'Signed in.', 'success');
                        window.location.href = data.redirect || '{{ url('/account') }}';
                    } else {
                        this.otpError = data.message || 'Invalid code.';
                        this.code = '';
                    }
                } catch (e) {
                    this.otpError = 'Network error. Please try again.';
                } finally {
                    this.loading = false;
                }
            },

            async resend() {
                if (this.resendCooldown > 0) return;
                this.code = '';
                this.otpError = '';
                this.loading = true;
                try {
                    const res = await fetch('{{ route('auth.otp.send') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                        },
                        body: JSON.stringify({ phone: this.phone }),
                    });
                    const data = await res.json();
                    if (res.ok && data.ok) {
                        this.startResendCooldown(45);
                        this.startExpiry(data.ttl_seconds || 300);
                        if (window.showToast) window.showToast('New code sent.', 'success');
                    } else {
                        this.otpError = data.message || 'Could not resend.';
                    }
                } catch (e) {
                    this.otpError = 'Network error.';
                } finally {
                    this.loading = false;
                }
            },

            startResendCooldown(seconds) {
                this.resendCooldown = seconds;
                const timer = setInterval(() => {
                    this.resendCooldown--;
                    if (this.resendCooldown <= 0) clearInterval(timer);
                }, 1000);
            },

            reset() {
                this.step = 'phone';
                this.code = '';
                this.otpError = '';
                this.phoneError = '';
                this.resendCooldown = 0;
                this.otpExpiresIn = 0;
                if (this._expiryTimer) { clearInterval(this._expiryTimer); this._expiryTimer = null; }
            },
        };
    }
</script>
@endsection
