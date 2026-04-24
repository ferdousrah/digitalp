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
    2%   { transform: scale3d(0.92,0.92,0.92) rotate(-2deg); }
    5%   { transform: scale3d(1.05,1.05,1.05) rotate(3deg); }
    8%   { transform: scale3d(1.05,1.05,1.05) rotate(-3deg); }
    11%  { transform: scale3d(1.05,1.05,1.05) rotate(3deg); }
    14%  { transform: scale3d(1.05,1.05,1.05) rotate(-3deg); }
    17%  { transform: scale3d(1.05,1.05,1.05) rotate(3deg); }
    19%  { transform: scale3d(1.02,1.02,1.02) rotate(-1deg); }
    21%  { transform: scale3d(1,1,1) rotate(0deg); }
}
</style>

{{-- Cart Sidebar --}}
<div id="cart-overlay"
    onclick="if(event.target===this)cartClose()"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:9998; opacity:0; transition:opacity 0.3s ease;">
</div>

<div id="cart-sidebar"
    style="position:fixed; top:0; right:0; bottom:0; width:380px; max-width:100vw; background:#fff; z-index:9999; display:flex; flex-direction:column; transform:translateX(100%); transition:transform 0.35s cubic-bezier(.4,0,.2,1); box-shadow:-8px 0 40px rgba(0,0,0,0.15);">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 20px; border-bottom:1px solid #e5e7eb; flex-shrink:0;">
        <h2 style="font-size:0.9rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:#111827; margin:0;">Shopping Cart</h2>
        <button onclick="cartClose()" style="display:flex; align-items:center; gap:6px; background:none; border:none; cursor:pointer; font-size:0.8rem; font-weight:600; color:#6b7280; transition:color 0.2s; padding:4px 0;" onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#6b7280'">
            Close
            <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </button>
    </div>

    {{-- Items list --}}
    <div id="cart-items" style="flex:1; overflow-y:auto; padding:0; min-height:0;">
        {{-- Populated by JS --}}
    </div>

    {{-- You May Also Like --}}
    <div id="cart-suggestions" style="display:none; border-top:1px solid #e5e7eb; background:#f9fafb; flex-shrink:0;">

        {{-- Header row --}}
        <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 16px 8px;">
            <div>
                <span style="font-size:0.8rem; font-weight:700; color:#111827; padding-bottom:3px; border-bottom:2px solid #f97316;">You May Also Like</span>
            </div>
            <div style="display:flex; gap:5px;">
                <button id="sugg-prev" onclick="suggPrev()"
                    style="width:26px; height:26px; border-radius:50%; background:#f97316; border:none; color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; opacity:0.4; transition:opacity 0.2s; flex-shrink:0;">
                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button id="sugg-next" onclick="suggNext()"
                    style="width:26px; height:26px; border-radius:50%; background:#f97316; border:none; color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:opacity 0.2s; flex-shrink:0;">
                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        {{-- Peek carousel: overflow hidden wrapper + sliding track --}}
        <div id="sugg-wrapper" style="overflow:hidden; padding:0 16px 10px;">
            <div id="sugg-track" style="display:flex; gap:10px; transition:transform 0.35s cubic-bezier(.4,0,.2,1); will-change:transform;">
                {{-- All cards rendered by JS --}}
            </div>
        </div>

        {{-- Free Delivery Banner --}}
        <div style="margin:0 16px 12px; background:linear-gradient(90deg,#f97316 0%,#ea580c 100%); color:#fff; padding:9px 14px; border-radius:8px; font-size:0.78rem; font-weight:700; display:flex; align-items:center; gap:8px; letter-spacing:0.01em;">
            <svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Free Delivery Unlocked!
        </div>
    </div>

    {{-- Footer (total + checkout) --}}
    <div id="cart-footer" style="display:none; border-top:1px solid #e5e7eb; padding:14px 20px; flex-shrink:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <span style="font-size:0.9rem; font-weight:600; color:#374151;">Subtotal</span>
            <span id="cart-sidebar-total" style="font-size:1.1rem; font-weight:700; color:#111827;">0৳</span>
        </div>
        <a id="cart-checkout-btn" href="{{ route('checkout.index') }}" style="display:block; text-align:center; padding:15px; background:#f97316; color:#fff; font-size:0.88rem; font-weight:800; letter-spacing:0.12em; text-transform:uppercase; text-decoration:none; border-radius:6px; margin-bottom:10px; animation:checkoutAttract 5s ease-in-out infinite 2s;" onmouseover="this.style.background='#ea6c0a'" onmouseout="this.style.background='#f97316'">
            Checkout
        </a>
        <button onclick="cartClear()" style="display:block; width:100%; text-align:center; padding:9px; background:transparent; border:1px solid #e5e7eb; color:#6b7280; font-size:0.8rem; font-weight:500; cursor:pointer; border-radius:4px; transition:all 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
            Clear Cart
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
