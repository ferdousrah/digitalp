@extends('layouts.app')
@section('title', sc('track', 'page_title', 'Track Your Order'))

@section('content')
@include('components.breadcrumb', ['items' => [['label' => sc('track', 'breadcrumb', 'Track Order')]]])

<div style="min-height:60vh; background:#f3f4f6; display:flex; align-items:center; justify-content:center; padding:48px 16px;">
    <div style="width:100%; max-width:520px;">

        {{-- Heading --}}
        <div style="text-align:center; margin-bottom:32px;">
            <h1 style="font-size:1.6rem; font-weight:800; color:#111827; margin:0 0 8px;">{{ sc('track', 'heading', 'Track Your Order') }}</h1>
            <p style="font-size:0.9rem; color:#6b7280; margin:0;">{{ sc('track', 'subheading', 'Enter your order number and phone number to check your delivery status.') }}</p>
        </div>

        {{-- Error --}}
        @if(session('error'))
        <div style="background:#fef2f2; border:1px solid #fecaca; border-left:4px solid #ef4444; border-radius:8px; padding:14px 18px; margin-bottom:20px; font-size:0.85rem; color:#dc2626; display:flex; align-items:center; gap:10px;">
            <i class="fi fi-rr-cross-circle" style="font-size:18px; flex-shrink:0;"></i>
            {{ session('error') }}
        </div>
        @endif

        {{-- Form card --}}
        <div style="background:#fff; border-radius:14px; box-shadow:0 4px 24px rgba(0,0,0,0.08); padding:32px;">
            <form method="POST" action="{{ route('track-order.track') }}">
                @csrf

                <div style="margin-bottom:20px;">
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:#374151; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">
                        {{ sc('track', 'order_number_label', 'Order Number') }} <span style="color:#ef4444;">*</span>
                    </label>
                    <div style="position:relative;">
                        <i class="fi fi-rr-receipt" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); font-size:16px; color:#9ca3af; line-height:1;"></i>
                        <input type="text" name="order_number" value="{{ old('order_number') }}" required
                            placeholder="{{ sc('track', 'order_number_placeholder', 'e.g. ORD-ABC123XYZ') }}"
                            style="width:100%; padding:11px 14px 11px 44px; border:1.5px solid {{ $errors->has('order_number') ? '#ef4444' : '#e5e7eb' }}; border-radius:8px; font-size:0.9rem; color:#111827; outline:none; box-sizing:border-box; transition:border-color 0.2s, box-shadow 0.2s;"
                            onfocus="this.style.borderColor='#f97316'; this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.12)';"
                            onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                    </div>
                    @error('order_number')<p style="font-size:0.76rem; color:#ef4444; margin:5px 0 0;">{{ $message }}</p>@enderror
                </div>

                <div style="margin-bottom:28px;">
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:#374151; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">
                        {{ sc('track', 'phone_label', 'Phone Number') }} <span style="color:#ef4444;">*</span>
                    </label>
                    <div style="position:relative;">
                        <i class="fi fi-rr-phone-call" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); font-size:16px; color:#9ca3af; line-height:1;"></i>
                        <input type="text" name="phone" value="{{ old('phone') }}" required
                            placeholder="{{ sc('track', 'phone_placeholder', '01XXXXXXXXX') }}"
                            style="width:100%; padding:11px 14px 11px 44px; border:1.5px solid {{ $errors->has('phone') ? '#ef4444' : '#e5e7eb' }}; border-radius:8px; font-size:0.9rem; color:#111827; outline:none; box-sizing:border-box; transition:border-color 0.2s, box-shadow 0.2s;"
                            onfocus="this.style.borderColor='#f97316'; this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.12)';"
                            onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                    </div>
                    @error('phone')<p style="font-size:0.76rem; color:#ef4444; margin:5px 0 0;">{{ $message }}</p>@enderror
                </div>

                <button type="submit"
                    style="width:100%; padding:13px; background:#111827; color:#fff; font-size:0.9rem; font-weight:800; letter-spacing:0.08em; text-transform:uppercase; border:none; border-radius:8px; cursor:pointer; transition:background 0.2s, transform 0.15s;"
                    onmouseover="this.style.background='#000'; this.style.transform='translateY(-1px)';"
                    onmouseout="this.style.background='#111827'; this.style.transform='translateY(0)';">
                    {{ sc('track', 'track_btn', 'Track Order') }}
                </button>
            </form>
        </div>

        <p style="text-align:center; font-size:0.8rem; color:#9ca3af; margin-top:20px;">
            {{ sc('track', 'help_text', 'Need help?') }}
            <a href="{{ route('contact.index') }}" style="color:#f97316; text-decoration:none; font-weight:600;">{{ sc('track', 'contact_link', 'Contact Us') }}</a>
        </p>

    </div>
</div>
@endsection
