@extends('layouts.app')

@section('title', 'My Account')

@section('content')
<div style="background:#f8fafc; min-height:60vh;">
    <div class="container-custom" style="padding:48px 16px;">

        @if(session('success'))
            <div role="status" aria-live="polite" style="margin-bottom:20px; padding:12px 16px; background:#ecfdf5; border:1px solid #bbf7d0; color:#166534; border-radius:10px; font-size:0.9rem;">
                {{ session('success') }}
            </div>
        @endif

        <div id="account-grid" style="display:grid; grid-template-columns:280px 1fr; gap:28px; align-items:start;">

            {{-- Sidebar --}}
            <aside id="account-sidebar" style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:24px; position:sticky; top:24px;">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid #f1f5f9;">
                    <div style="width:48px; height:48px; border-radius:50%; background:linear-gradient(135deg,#f97316,#ea580c); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.1rem; flex-shrink:0;">
                        {{ strtoupper(mb_substr($user->name ?? 'C', 0, 1)) }}
                    </div>
                    <div style="min-width:0;">
                        <p style="margin:0; font-weight:700; color:#0f172a; font-size:0.95rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $user->name ?: 'Customer' }}</p>
                        <p style="margin:0; font-size:0.78rem; color:#64748b;">{{ \App\Support\PhoneNormalizer::display($user->phone ?? '') ?: $user->email }}</p>
                    </div>
                </div>

                <nav style="display:flex; flex-direction:column; gap:4px;">
                    <a href="{{ route('account.index') }}" class="acc-nav acc-nav-active" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:8px; text-decoration:none; font-size:0.9rem; font-weight:600; color:#f97316; background:#fff7ed;">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Overview
                    </a>
                    <a href="{{ route('account.orders') }}" class="acc-nav" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:8px; text-decoration:none; font-size:0.9rem; font-weight:600; color:#475569;">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        My Orders
                        @if($orderCount > 0)<span style="margin-left:auto; background:#f1f5f9; color:#64748b; font-size:0.7rem; font-weight:700; padding:2px 8px; border-radius:999px;">{{ $orderCount }}</span>@endif
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="acc-nav" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:8px; text-decoration:none; font-size:0.9rem; font-weight:600; color:#475569;">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        Wishlist
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0; margin-top:6px; padding-top:10px; border-top:1px solid #f1f5f9;">
                        @csrf
                        <button type="submit" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:8px; text-decoration:none; font-size:0.9rem; font-weight:600; color:#ef4444; background:none; border:none; cursor:pointer; width:100%; text-align:left;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
                            <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Log out
                        </button>
                    </form>
                </nav>
            </aside>

            {{-- Main --}}
            <div style="display:flex; flex-direction:column; gap:24px;">

                {{-- Greeting card --}}
                <div style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:28px;">
                    <h1 style="margin:0 0 6px; font-size:1.6rem; font-weight:800; color:#0f172a; letter-spacing:-0.01em;">Welcome back, {{ explode(' ', $user->name ?? 'there')[0] ?: 'there' }} 👋</h1>
                    <p style="margin:0; color:#64748b; font-size:0.95rem;">Manage your orders, track deliveries and update your details.</p>
                </div>

                {{-- Stats --}}
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:16px;">
                    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px;">
                        <p style="margin:0 0 6px; font-size:0.7rem; font-weight:700; color:#64748b; letter-spacing:0.1em; text-transform:uppercase;">Total Orders</p>
                        <p style="margin:0; font-size:1.8rem; font-weight:800; color:#0f172a;">{{ $orderCount }}</p>
                    </div>
                    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px;">
                        <p style="margin:0 0 6px; font-size:0.7rem; font-weight:700; color:#64748b; letter-spacing:0.1em; text-transform:uppercase;">Phone Verified</p>
                        <p style="margin:0; font-size:0.95rem; font-weight:700; color:#16a34a; display:flex; align-items:center; gap:6px;">
                            <svg style="width:18px; height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Verified
                        </p>
                    </div>
                </div>

                {{-- Profile form --}}
                <div style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:28px;">
                    <h2 style="margin:0 0 4px; font-size:1.1rem; font-weight:800; color:#0f172a;">Your details</h2>
                    <p style="margin:0 0 20px; color:#64748b; font-size:0.88rem;">Add your name and email so we can keep you updated about orders.</p>

                    <form method="POST" action="{{ route('account.profile.update') }}" id="account-profile-form" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        @csrf
                        <div>
                            <label style="display:block; font-size:0.78rem; font-weight:700; color:#475569; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Full name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name === 'Customer' ? '' : $user->name) }}" required maxlength="120"
                                style="width:100%; padding:12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:0.95rem; outline:none; transition:border-color 0.2s; background:#fff;"
                                onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#e2e8f0'">
                            @error('name')<p style="color:#ef4444; font-size:0.78rem; margin:6px 0 0;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label style="display:block; font-size:0.78rem; font-weight:700; color:#475569; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Email <span style="color:#94a3b8; font-weight:500; text-transform:none;">(optional)</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" maxlength="255"
                                style="width:100%; padding:12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:0.95rem; outline:none; transition:border-color 0.2s; background:#fff;"
                                onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#e2e8f0'">
                            @error('email')<p style="color:#ef4444; font-size:0.78rem; margin:6px 0 0;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label style="display:block; font-size:0.78rem; font-weight:700; color:#475569; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Mobile</label>
                            <input type="text" value="{{ \App\Support\PhoneNormalizer::display($user->phone ?? '') }}" disabled
                                style="width:100%; padding:12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:0.95rem; background:#f8fafc; color:#64748b;">
                        </div>
                        <div style="grid-column:1/-1; display:flex; justify-content:flex-end;">
                            <button type="submit" style="padding:12px 24px; background:#f97316; color:#fff; border:none; border-radius:8px; font-weight:700; font-size:0.88rem; letter-spacing:0.05em; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#ea6c0a'" onmouseout="this.style.background='#f97316'">
                                Save changes
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Recent orders --}}
                @if($recent->count() > 0)
                <div style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:28px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                        <h2 style="margin:0; font-size:1.1rem; font-weight:800; color:#0f172a;">Recent orders</h2>
                        <a href="{{ route('account.orders') }}" style="font-size:0.85rem; font-weight:700; color:#f97316; text-decoration:none;">View all →</a>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        @foreach($recent as $order)
                            <a href="{{ route('account.orders.show', $order->order_number) }}" style="display:flex; align-items:center; gap:14px; padding:14px; background:#f8fafc; border:1px solid #f1f5f9; border-radius:10px; text-decoration:none; transition:all 0.15s;" onmouseover="this.style.background='#fff'; this.style.borderColor='#e2e8f0'; this.style.transform='translateY(-1px)';" onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#f1f5f9'; this.style.transform='none';">
                                <div style="flex:1; min-width:0;">
                                    <p style="margin:0; font-weight:700; color:#0f172a; font-size:0.9rem;">#{{ $order->order_number ?? $order->id }}</p>
                                    <p style="margin:2px 0 0; font-size:0.78rem; color:#64748b;">{{ $order->created_at?->format('M d, Y h:i A') }}</p>
                                </div>
                                <span style="font-size:0.72rem; font-weight:700; padding:4px 10px; background:#fff; border:1px solid #e2e8f0; color:#475569; border-radius:999px; text-transform:uppercase; letter-spacing:0.05em;">{{ $order->status ?? 'Pending' }}</span>
                                <span style="font-weight:800; color:#0f172a;">@bdt($order->total ?? 0)</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Below 1024px: sidebar stacks above content (avoids cramped 280px sidebar on tablets) */
@media (max-width: 1023px) {
    #account-grid {
        grid-template-columns: 1fr !important;
        gap: 16px !important;
    }
    #account-sidebar {
        position: static !important;
        top: auto !important;
    }
}

/* Below 640px: profile form stacks vertically */
@media (max-width: 639px) {
    #account-profile-form {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endsection
