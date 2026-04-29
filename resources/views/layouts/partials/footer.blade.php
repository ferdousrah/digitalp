<style>
.ds-footer { background:#0a0a0a; color:#a3a3a3; position:relative; overflow:hidden; font-feature-settings:"ss01","cv11"; }
.ds-footer a { color:inherit; text-decoration:none; }

/* Animated arrow link (Explore / Shop / Connect lists) */
.ds-flink {
    color:#a3a3a3;
    font-size:0.95rem;
    font-weight:500;
    position:relative;
    display:inline-flex;
    align-items:center;
    gap:0;
    transition:color 0.25s, gap 0.25s, letter-spacing 0.25s;
}
.ds-flink::after {
    content:'→';
    display:inline-block;
    opacity:0;
    transform:translateX(-8px);
    transition:opacity 0.25s, transform 0.25s, margin-left 0.25s;
    margin-left:0;
    font-weight:400;
}
.ds-flink:hover { color:#fff; gap:8px; }
.ds-flink:hover::after { opacity:1; transform:translateX(0); margin-left:6px; }

/* Live status pulse */
@keyframes dsPulse {
    0%, 100% { box-shadow:0 0 0 0 rgba(34,197,94,0.45); }
    50%      { box-shadow:0 0 0 6px rgba(34,197,94,0); }
}
.ds-live-dot { animation: dsPulse 2s ease-in-out infinite; }

/* Main grid */
.ds-footer .ds-cols { display:grid; grid-template-columns: 2.2fr 1fr 1fr 1.2fr; gap:48px; }
@media (max-width:1024px) { .ds-footer .ds-cols { grid-template-columns: 1fr 1fr; gap:36px; } }
@media (max-width:640px)  { .ds-footer .ds-cols { grid-template-columns: 1fr; gap:32px; } }

/* Newsletter row */
.ds-news-grid { display:grid; grid-template-columns:1fr 1fr; gap:40px; align-items:center; }
@media (max-width:768px) { .ds-news-grid { grid-template-columns:1fr; gap:24px; } }

/* Section dividers */
.ds-div { border-top:1px solid #1a1a1a; }

/* Newsletter underlined input */
.ds-news-form { display:flex; gap:0; border-bottom:1.5px solid #404040; padding-bottom:8px; transition:border-color 0.3s; }
.ds-news-form:focus-within { border-color:#f97316; }
.ds-news-form input {
    flex:1; background:transparent; border:none; outline:none;
    padding:10px 4px; color:#fff; font-size:1rem; font-weight:500; min-width:0;
}
.ds-news-form input::placeholder { color:#525252; }
.ds-news-form button {
    display:flex; align-items:center; gap:6px;
    background:transparent; border:none; cursor:pointer;
    color:#fff; font-size:0.78rem; font-weight:800; letter-spacing:0.12em; text-transform:uppercase;
    padding:10px 4px; transition:color 0.2s;
}
.ds-news-form button:hover { color:#f97316; }
.ds-news-form button svg { transition:transform 0.25s; }
.ds-news-form button:hover svg { transform:translateX(3px); }

/* Back to top */
.ds-totop {
    display:inline-flex; align-items:center; gap:8px;
    color:#737373; font-size:0.78rem; font-weight:700; letter-spacing:0.12em; text-transform:uppercase;
    transition:color 0.2s, gap 0.25s;
}
.ds-totop:hover { color:#fff; gap:12px; }
.ds-totop svg { transition:transform 0.25s; }
.ds-totop:hover svg { transform:translateY(-3px); }

/* Mailto big link */
.ds-mailto {
    display:inline-block; color:#fff; font-weight:600; font-size:0.95rem;
    border-bottom:1px solid #404040; padding-bottom:2px;
    transition:color 0.2s, border-color 0.2s;
}
.ds-mailto:hover { color:#f97316; border-color:#f97316; }

/* Section labels */
.ds-section-label { color:#525252; font-size:0.7rem; font-weight:800; letter-spacing:0.18em; text-transform:uppercase; margin:0 0 20px; }

/* Connect column — address / email / phone rows */
.ds-info-row { display:flex; align-items:flex-start; gap:12px; margin-bottom:18px; }
.ds-info-icon {
    flex-shrink:0; width:36px; height:36px;
    display:inline-flex; align-items:center; justify-content:center;
    background:#111; border:1px solid #1f1f1f; border-radius:10px;
    color:#f97316;
    transition:border-color 0.25s, background 0.25s, transform 0.25s, box-shadow 0.25s;
}
.ds-info-icon svg { width:16px; height:16px; transition:transform 0.25s; }
.ds-info-row:hover .ds-info-icon {
    border-color:rgba(249,115,22,0.6);
    background:rgba(249,115,22,0.08);
    transform:translateY(-2px);
    box-shadow:0 6px 16px rgba(249,115,22,0.12);
}
.ds-info-row:hover .ds-info-icon svg { transform:scale(1.08); }
.ds-info-label { font-size:0.66rem; color:#525252; letter-spacing:0.18em; text-transform:uppercase; font-weight:700; margin:0 0 4px; }
.ds-info-text { color:#a3a3a3; font-size:0.88rem; line-height:1.5; margin:0; }
.ds-info-link { color:#fff; font-size:0.92rem; font-weight:600; text-decoration:none; line-height:1.4; transition:color 0.2s; word-break:break-word; }
.ds-info-link:hover { color:#f97316; }

/* Social icons */
.ds-social-row { display:flex; gap:10px; flex-wrap:wrap; }
.ds-social {
    width:40px; height:40px;
    display:inline-flex; align-items:center; justify-content:center;
    background:#111; border:1px solid #1f1f1f; border-radius:50%;
    color:#a3a3a3;
    transition:color 0.25s, border-color 0.25s, background 0.25s, transform 0.25s, box-shadow 0.25s;
}
.ds-social svg { width:16px; height:16px; transition:transform 0.25s; }
.ds-social:hover {
    color:#f97316;
    border-color:rgba(249,115,22,0.6);
    background:rgba(249,115,22,0.08);
    transform:translateY(-3px);
    box-shadow:0 6px 16px rgba(249,115,22,0.12);
}
.ds-social:hover svg { transform:scale(1.08); }
</style>

<footer class="ds-footer">

    {{-- ─── NEWSLETTER ─── --}}
    <div class="container-custom" style="padding:96px 16px 48px;">
        <div class="ds-news-grid">
            <div>
                <h3 style="font-size:1.5rem; font-weight:800; color:#fff; margin:0 0 6px; letter-spacing:-0.01em;">{{ sc('footer', 'newsletter_title', 'Subscribe to our newsletter') }}</h3>
                <p style="font-size:0.88rem; color:#737373; margin:0;">{{ sc('footer', 'newsletter_subtitle', 'Stay in the loop. New arrivals and exclusive offers, straight to your inbox. No spam.') }}</p>
            </div>
            <div class="ds-news-wrap">
                <form action="{{ route('newsletter.subscribe') }}" method="post" class="ds-news-form" onsubmit="return dsSubscribe(event, this);">
                    @csrf
                    <input type="email" name="email" placeholder="your@email.com" required autocomplete="email">
                    <button type="submit" aria-label="Subscribe">
                        <span class="ds-sub-label">Subscribe</span>
                        <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </button>
                </form>
                <div class="ds-news-thanks" style="display:none; align-items:center; gap:12px; padding:14px 4px; border-bottom:1.5px solid #22c55e;">
                    <span style="display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:50%; background:rgba(34,197,94,0.15); color:#22c55e; flex-shrink:0;">
                        <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="ds-news-thanks-msg" style="color:#fff; font-weight:600; font-size:0.95rem;">Thanks for subscribing!</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── MAIN COLUMNS ─── --}}
    <div class="container-custom ds-div" style="padding:64px 16px 48px;">
        <div class="ds-cols">

            {{-- Brand block --}}
            @php
                $__siteName = \App\Services\SettingService::get('site_name', config('app.name'));
                $__nameParts = preg_split('/\s+/', trim($__siteName), 2);
                $__defaultTagline = \App\Services\SettingService::get('site_description', 'Your trusted partner.');
            @endphp
            <div>
                <a href="{{ url('/') }}" aria-label="{{ $__siteName }}" style="display:inline-flex; align-items:baseline; gap:6px; margin-bottom:20px;">
                    <span style="font-size:1.5rem; font-weight:900; color:#fff; letter-spacing:-0.02em;">{{ $__nameParts[0] }}</span>
                    @if(!empty($__nameParts[1]))
                        <span style="font-size:1.5rem; font-weight:900; color:#f97316; letter-spacing:-0.02em;">{{ $__nameParts[1] }}<span style="color:#f97316;">.</span></span>
                    @else
                        <span style="font-size:1.5rem; font-weight:900; color:#f97316;">.</span>
                    @endif
                </a>
                <p style="color:#737373; font-size:0.95rem; line-height:1.65; margin:0 0 28px; max-width:380px;">{{ sc('footer', 'tagline', $__defaultTagline) }}</p>

                {{-- Live status / location --}}
                <div style="display:inline-flex; align-items:center; gap:10px; padding:8px 14px; background:#111; border:1px solid #1f1f1f; border-radius:999px; font-size:0.78rem; color:#a3a3a3;">
                    <span class="ds-live-dot" style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#22c55e;"></span>
                    <span id="footer-time" style="font-variant-numeric:tabular-nums; color:#fff; font-weight:600;">—</span>
                    <span style="color:#525252;">·</span>
                    <span>Dhaka, BD</span>
                </div>
            </div>

            {{-- Explore --}}
            <div>
                <h3 class="ds-section-label">Explore</h3>
                <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:12px;">
                    <li><a href="{{ route('products.index') }}" class="ds-flink">Products</a></li>
                    <li><a href="{{ route('services.index') }}" class="ds-flink">Services</a></li>
                    <li><a href="{{ route('pages.about') }}" class="ds-flink">About</a></li>
                    <li><a href="{{ route('blog.index') }}" class="ds-flink">Blog</a></li>
                    <li><a href="{{ route('gallery.index') }}" class="ds-flink">Gallery</a></li>
                    <li><a href="{{ route('faq.index') }}" class="ds-flink">FAQ</a></li>
                </ul>
            </div>

            {{-- Shop --}}
            <div>
                <h3 class="ds-section-label">Shop</h3>
                <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:12px;">
                    @foreach(\App\Models\Category::active()->whereNull('parent_id')->orderBy('sort_order')->limit(5)->get() as $cat)
                        <li><a href="{{ route('categories.show', $cat) }}" class="ds-flink">{{ $cat->name }}</a></li>
                    @endforeach
                    <li><a href="{{ route('products.index') }}" class="ds-flink" style="color:#f97316;">All categories</a></li>
                </ul>
            </div>

            {{-- Connect --}}
            <div>
                <h3 class="ds-section-label">Connect</h3>

                {{-- Address --}}
                <div class="ds-info-row">
                    <span class="ds-info-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </span>
                    <div>
                        <p class="ds-info-label">Visit us</p>
                        <p class="ds-info-text">{{ \App\Services\SettingService::get('contact_address', '123 Tech Street, Digital City, DC 12345') }}</p>
                    </div>
                </div>

                {{-- Email --}}
                <div class="ds-info-row">
                    <span class="ds-info-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <div>
                        <p class="ds-info-label">Drop a line</p>
                        <a href="mailto:{{ \App\Services\SettingService::get('contact_email', 'info@digitalsupport.com') }}" class="ds-info-link">{{ \App\Services\SettingService::get('contact_email', 'info@digitalsupport.com') }}</a>
                    </div>
                </div>

                {{-- Phone --}}
                <div class="ds-info-row" style="margin-bottom:24px;">
                    <span class="ds-info-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </span>
                    <div>
                        <p class="ds-info-label">Call us</p>
                        <a href="tel:{{ preg_replace('/\s+/', '', \App\Services\SettingService::get('contact_phone', '+8801234567890')) }}" class="ds-info-link">{{ \App\Services\SettingService::get('contact_phone', '+880 1234 567 890') }}</a>
                    </div>
                </div>

                {{-- Social --}}
                <p class="ds-info-label" style="margin-bottom:12px;">Follow us</p>
                <div class="ds-social-row">
                    <a href="{{ \App\Services\SettingService::get('social_facebook', '#') }}" target="_blank" rel="noopener" class="ds-social" aria-label="Facebook">
                        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="{{ \App\Services\SettingService::get('social_instagram', '#') }}" target="_blank" rel="noopener" class="ds-social" aria-label="Instagram">
                        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="{{ \App\Services\SettingService::get('social_twitter', '#') }}" target="_blank" rel="noopener" class="ds-social" aria-label="Twitter / X">
                        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="{{ \App\Services\SettingService::get('social_linkedin', '#') }}" target="_blank" rel="noopener" class="ds-social" aria-label="LinkedIn">
                        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.063 2.063 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── BOTTOM BAR ─── --}}
    <div class="ds-div" style="padding:18px 16px;">
        <div class="container-custom" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; font-size:0.78rem; color:#525252;">
            <p style="margin:0; display:inline-flex; align-items:center; gap:14px; flex-wrap:wrap;">
                <span>{{ sc('footer', 'copyright', '© ' . date('Y') . ' ' . \App\Services\SettingService::get('site_name', config('app.name')) . '. All Rights Reserved.') }}</span>
                <span style="opacity:0.4;">·</span>
                <span>Developed by
                    <a href="https://technocratsbd.com" target="_blank" rel="noopener" class="ds-credit" style="color:#a3a3a3; font-weight:600; text-decoration:none; border-bottom:1px solid #404040; padding-bottom:1px; transition:color 0.2s, border-color 0.2s;" onmouseover="this.style.color='#f97316';this.style.borderColor='#f97316'" onmouseout="this.style.color='#a3a3a3';this.style.borderColor='#404040'">Technocrats</a>
                </span>
            </p>
            <div style="display:flex; gap:24px; align-items:center;">
                <a href="{{ url('/page/privacy-policy') }}" style="color:#525252; transition:color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#525252'">Privacy</a>
                <a href="{{ url('/page/terms-of-service') }}" style="color:#525252; transition:color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#525252'">Terms</a>
                <a href="#top" class="ds-totop" onclick="event.preventDefault(); window.scrollTo({top:0, behavior:'smooth'});">
                    Back to top
                    <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

    <script>
    (function(){
        var el = document.getElementById('footer-time');
        if (!el) return;
        function tick() {
            try {
                var s = new Date().toLocaleTimeString('en-US', {
                    hour: '2-digit', minute: '2-digit', second: '2-digit',
                    hour12: false, timeZone: 'Asia/Dhaka'
                });
                el.textContent = s + ' (GMT+6)';
            } catch (e) {
                var d = new Date();
                el.textContent = String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0') + ':' + String(d.getSeconds()).padStart(2,'0');
            }
        }
        tick();
        setInterval(tick, 1000);
    })();

    // Newsletter subscribe — AJAX submit with success/error UI
    window.dsSubscribe = function (e, form) {
        e.preventDefault();
        var btn      = form.querySelector('button[type="submit"]');
        var label    = btn.querySelector('.ds-sub-label');
        var origText = label ? label.textContent : 'Subscribe';
        if (label) label.textContent = 'Sending…';
        btn.disabled = true;

        fetch(form.action, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form),
        })
        .then(function (r) {
            return r.json().then(function (j) { return { ok: r.ok, status: r.status, body: j }; });
        })
        .then(function (res) {
            if (res.ok && res.body.success) {
                var wrap   = form.parentElement;
                var thanks = wrap.querySelector('.ds-news-thanks');
                var msg    = wrap.querySelector('.ds-news-thanks-msg');
                if (msg && res.body.message) msg.textContent = res.body.message;
                if (thanks) {
                    form.style.display = 'none';
                    thanks.style.display = 'flex';
                }
                if (window.showToast) window.showToast(res.body.message || 'Subscribed!', 'success');
            } else {
                var errMsg = 'Could not subscribe. Please try again.';
                if (res.body && res.body.errors && res.body.errors.email) {
                    errMsg = res.body.errors.email[0];
                } else if (res.body && res.body.message) {
                    errMsg = res.body.message;
                }
                if (window.showToast) window.showToast(errMsg, 'error');
                if (label) label.textContent = origText;
                btn.disabled = false;
            }
        })
        .catch(function () {
            if (window.showToast) window.showToast('Network error. Please try again.', 'error');
            if (label) label.textContent = origText;
            btn.disabled = false;
        });

        return false;
    };
    </script>
</footer>
