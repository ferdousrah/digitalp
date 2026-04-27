<style>
@keyframes cartShake {
    0%   { transform: translateY(-50%) translateX(0); }
    15%  { transform: translateY(-50%) translateX(-6px) rotate(-4deg); }
    30%  { transform: translateY(-50%) translateX(6px) rotate(4deg); }
    45%  { transform: translateY(-50%) translateX(-5px) rotate(-3deg); }
    60%  { transform: translateY(-50%) translateX(5px) rotate(3deg); }
    75%  { transform: translateY(-50%) translateX(-3px) rotate(-2deg); }
    90%  { transform: translateY(-50%) translateX(3px) rotate(2deg); }
    100% { transform: translateY(-50%) translateX(0) rotate(0); }
}
@keyframes checkoutAttract {
    0%, 22%, 100% { transform: scale3d(1,1,1) rotate(0deg); }
    2%   { transform: scale3d(0.94,0.94,0.94) rotate(-1.5deg); }
    5%   { transform: scale3d(1.04,1.04,1.04) rotate(2deg); }
    8%   { transform: scale3d(1.04,1.04,1.04) rotate(-2deg); }
    11%  { transform: scale3d(1.04,1.04,1.04) rotate(2deg); }
    14%  { transform: scale3d(1.04,1.04,1.04) rotate(-2deg); }
    17%  { transform: scale3d(1.04,1.04,1.04) rotate(2deg); }
    19%  { transform: scale3d(1.02,1.02,1.02) rotate(-1deg); }
    21%  { transform: scale3d(1,1,1) rotate(0deg); }
}
#cart-items::-webkit-scrollbar { width:6px; }
#cart-items::-webkit-scrollbar-track { background:transparent; }
#cart-items::-webkit-scrollbar-thumb { background:#e2e8f0; border-radius:3px; }
#cart-items::-webkit-scrollbar-thumb:hover { background:#cbd5e1; }
.cart-item-row { transition:background 0.15s; }
.cart-item-row:hover { background:#fafbfc; }
</style>

{{-- Cart Sidebar --}}
<div id="cart-overlay"
    onclick="if(event.target===this)cartClose()"
    style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.55); z-index:9998; opacity:0; transition:opacity 0.3s ease; backdrop-filter:blur(2px); -webkit-backdrop-filter:blur(2px);">
</div>

<div id="cart-sidebar"
    role="dialog"
    aria-modal="true"
    aria-labelledby="cart-sidebar-title"
    aria-hidden="true"
    style="position:fixed; top:0; right:0; bottom:0; width:400px; max-width:100vw; background:#fff; z-index:9999; display:flex; flex-direction:column; transform:translateX(100%); transition:transform 0.35s cubic-bezier(.4,0,.2,1); box-shadow:-12px 0 48px rgba(15,23,42,0.18);">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 20px; border-bottom:1px solid #f1f5f9; flex-shrink:0;">
        <div style="display:flex; align-items:center; gap:10px;">
            <h2 id="cart-sidebar-title" style="font-size:0.92rem; font-weight:800; letter-spacing:0.08em; text-transform:uppercase; color:#0f172a; margin:0;">Your Cart</h2>
            <span id="cart-drawer-badge" style="display:none; font-size:0.7rem; font-weight:700; color:#f97316; background:#fff7ed; padding:3px 9px; border-radius:999px; line-height:1;">0</span>
        </div>
        <button onclick="cartClose()" aria-label="Close cart" style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; cursor:pointer; color:#64748b; transition:all 0.2s;" onmouseover="this.style.background='#f1f5f9';this.style.color='#0f172a'" onmouseout="this.style.background='#f8fafc';this.style.color='#64748b'">
            <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Items list --}}
    <div id="cart-items" style="flex:1; overflow-y:auto; padding:0; min-height:0;">
        {{-- Populated by JS --}}
    </div>

    {{-- You May Also Like --}}
    <div id="cart-suggestions" style="display:none; border-top:1px solid #f1f5f9; background:#fafbfc; flex-shrink:0;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px 8px;">
            <span style="font-size:0.78rem; font-weight:700; color:#0f172a; letter-spacing:0.02em;">You may also like</span>
            <div style="display:flex; gap:6px;">
                <button id="sugg-prev" onclick="suggPrev()" aria-label="Previous suggestions"
                    style="width:28px; height:28px; border-radius:50%; background:#fff; border:1px solid #e2e8f0; color:#475569; cursor:pointer; display:flex; align-items:center; justify-content:center; opacity:0.5; transition:all 0.2s; flex-shrink:0;">
                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button id="sugg-next" onclick="suggNext()" aria-label="Next suggestions"
                    style="width:28px; height:28px; border-radius:50%; background:#fff; border:1px solid #e2e8f0; color:#475569; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.2s; flex-shrink:0;">
                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
        <div id="sugg-wrapper" style="overflow:hidden; padding:0 16px 14px;">
            <div id="sugg-track" style="display:flex; gap:10px; transition:transform 0.35s cubic-bezier(.4,0,.2,1); will-change:transform;">
                {{-- Cards rendered by JS --}}
            </div>
        </div>
    </div>

    {{-- Footer (total + checkout) --}}
    <div id="cart-footer" style="display:none; border-top:1px solid #f1f5f9; padding:14px 20px 16px; flex-shrink:0; background:#fff;">
        {{-- Free delivery strip --}}
        <div style="display:flex; align-items:center; gap:8px; padding:8px 12px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; font-size:0.74rem; font-weight:600; color:#166534; margin-bottom:14px;">
            <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            Free delivery unlocked
        </div>

        {{-- Subtotal --}}
        <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:2px;">
            <span style="font-size:0.78rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.06em;">Subtotal</span>
            <span id="cart-sidebar-total" style="font-size:1.25rem; font-weight:800; color:#0f172a; letter-spacing:-0.01em;">0৳</span>
        </div>
        <div style="font-size:0.7rem; color:#94a3b8; margin-bottom:14px;">Shipping &amp; taxes calculated at checkout</div>

        {{-- Checkout --}}
        <a id="cart-checkout-btn" href="{{ route('checkout.index') }}" style="display:flex; align-items:center; justify-content:center; gap:8px; padding:14px; background:#f97316; color:#fff; font-size:0.86rem; font-weight:800; letter-spacing:0.1em; text-transform:uppercase; text-decoration:none; border-radius:10px; box-shadow:0 4px 14px rgba(249,115,22,0.25); animation:checkoutAttract 5s ease-in-out infinite 2s; transition:all 0.2s;" onmouseover="this.style.background='#ea6c0a';this.style.boxShadow='0 6px 20px rgba(249,115,22,0.35)'" onmouseout="this.style.background='#f97316';this.style.boxShadow='0 4px 14px rgba(249,115,22,0.25)'">
            Checkout
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>

        {{-- Clear cart text link --}}
        <button onclick="cartClear()" style="display:block; width:100%; text-align:center; padding:10px 0 0; background:none; border:none; color:#94a3b8; font-size:0.74rem; font-weight:500; cursor:pointer; transition:color 0.15s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">
            Clear cart
        </button>
    </div>
</div>

{{-- Floating cart button --}}
<div id="cart-float" onclick="cartOpen()"
    style="position:fixed; top:50%; right:0; transform:translateY(-50%); z-index:9990; cursor:pointer; border-radius:10px 0 0 10px; overflow:hidden; box-shadow:-4px 0 16px rgba(0,0,0,0.18); transition:transform 0.2s ease;" onmouseover="this.style.transform='translateY(-50%) translateX(-4px)'" onmouseout="this.style.transform='translateY(-50%)'">
    <div style="background:#f97316; color:#fff; padding:10px 16px; display:flex; flex-direction:column; align-items:center; gap:2px;">
        <svg style="width:24px; height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        <span id="cart-float-label" style="font-size:0.7rem; font-weight:700; white-space:nowrap;">0 Items</span>
    </div>
    <div style="background:#e86810; color:#fff; padding:4px 16px; text-align:center;">
        <span id="cart-float-total" style="font-size:0.75rem; font-weight:700;">0৳</span>
    </div>
</div>
