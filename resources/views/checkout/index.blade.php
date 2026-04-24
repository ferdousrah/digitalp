@extends('layouts.app')

@section('title', 'Checkout')
@section('meta_description', 'Complete your order securely')

@push('styles')
<style>
.co-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 1px 6px rgba(0,0,0,0.07);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}
.co-card-header {
    padding: 14px 20px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.co-card-header h3 {
    font-size: 0.88rem;
    font-weight: 700;
    color: #111827;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin: 0;
    padding-bottom: 3px;
    border-bottom: 2px solid #f97316;
}
.co-card-body {
    padding: 20px;
}
.co-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 5px;
}
.co-input {
    width: 100%;
    padding: 9px 13px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.85rem;
    color: #111827;
    background: #fff;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
    box-sizing: border-box;
}
.co-input:focus {
    border-color: #f97316;
    box-shadow: 0 0 0 3px rgba(249,115,22,0.12);
}
.co-input.error {
    border-color: #ef4444;
}
.co-error {
    font-size: 0.76rem;
    color: #ef4444;
    margin-top: 4px;
}
.co-select {
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 16px;
    padding-right: 34px;
}
.co-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
}
.pay-card {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 13px 16px;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
    display: flex;
    align-items: center;
    gap: 12px;
    user-select: none;
}
.pay-card:hover {
    border-color: #f97316;
    background: #fff7ed;
}
.pay-card.active {
    border-color: #f97316;
    background: #fff7ed;
}
.pay-radio {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 2px solid #d1d5db;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: border-color 0.2s;
}
.pay-radio.active {
    border-color: #f97316;
}
.pay-radio.active::after {
    content: '';
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: #f97316;
}
.place-btn {
    display: block;
    width: 100%;
    padding: 15px;
    background: #f97316;
    color: #fff;
    font-size: 0.9rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s, transform 0.15s;
}
.place-btn:hover:not(:disabled) {
    background: #ea6c0a;
    transform: translateY(-1px);
}
.place-btn:disabled {
    opacity: 0.75;
    cursor: not-allowed;
}
@media (max-width: 768px) {
    .co-grid-2 { grid-template-columns: 1fr; }
}
/* Coupon accordion slide transition */
.co-acc-enter { transition: opacity 0.25s ease, transform 0.25s ease; }
.co-acc-enter-from { opacity: 0; transform: translateY(-8px); }
.co-acc-enter-to   { opacity: 1; transform: translateY(0); }
.co-acc-leave      { transition: opacity 0.18s ease, transform 0.18s ease; }
.co-acc-leave-from { opacity: 1; transform: translateY(0); }
.co-acc-leave-to   { opacity: 0; transform: translateY(-6px); }
</style>
@endpush

@section('content')
<div x-data="checkoutApp()" style="background:#f3f4f6; min-height:100vh;">

    {{-- Page header --}}
    <div style="background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:14px 0;">
        <div class="container-custom px-4 sm:px-6 lg:px-8" style="display:flex; align-items:center; gap:8px; font-size:0.82rem; color:#6b7280;">
            <a href="{{ route('home') }}" style="color:#6b7280; text-decoration:none;" onmouseover="this.style.color='#f97316'" onmouseout="this.style.color='#6b7280'">Home</a>
            <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('cart.index') }}" style="color:#6b7280; text-decoration:none;" onmouseover="this.style.color='#f97316'" onmouseout="this.style.color='#6b7280'">Cart</a>
            <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span style="color:#f97316; font-weight:600;">Checkout</span>
        </div>
    </div>

    {{-- Main content --}}
    <div class="container-custom px-4 sm:px-6 lg:px-8" style="padding-top:28px; padding-bottom:60px;">

        <h1 style="font-size:1.5rem; font-weight:800; color:#111827; margin:0 0 24px;">{{ sc('checkout', 'title', 'Checkout') }}</h1>

        @if($errors->any())
        <div style="background:#fef2f2; border:1px solid #fecaca; border-left:4px solid #ef4444; border-radius:8px; padding:14px 18px; margin-bottom:20px; font-size:0.85rem; color:#dc2626;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin:8px 0 0 16px; padding:0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('checkout.store') }}" @submit.prevent="handleSubmit($event)">
            @csrf

            <div style="display:grid; grid-template-columns:1fr; gap:24px;">
                {{-- On large screens: 2/3 + 1/3 --}}
                <div style="display:grid; grid-template-columns:minmax(0,2fr) minmax(0,1fr); gap:24px;" class="checkout-grid">

                    {{-- ====== LEFT COLUMN ====== --}}
                    <div style="display:flex; flex-direction:column; gap:20px;">

                        {{-- Order Review --}}
                        <div class="co-card">
                            <div class="co-card-header">
                                <svg style="width:18px;height:18px;color:#f97316;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <h3>Order Review</h3>
                                <span style="margin-left:auto; font-size:0.8rem; color:#6b7280;" x-text="cartItems.length + (cartItems.length !== 1 ? ' items' : ' item')"></span>
                            </div>
                            <div class="co-card-body" style="padding:0;">
                                <table style="width:100%; border-collapse:collapse; font-size:0.85rem;">
                                    <thead>
                                        <tr style="background:#f9fafb; border-bottom:1px solid #f0f0f0;">
                                            <th style="text-align:left; padding:10px 20px; font-size:0.76rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">Product</th>
                                            <th style="text-align:center; padding:10px 12px; font-size:0.76rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">Qty</th>
                                            <th style="text-align:right; padding:10px 20px; font-size:0.76rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="item in cartItems" :key="item.key">
                                        <tr style="border-bottom:1px solid #f3f4f6;">
                                            <td style="padding:12px 20px;">
                                                <div style="display:flex; align-items:center; gap:12px;">
                                                    <template x-if="item.image">
                                                        <img :src="item.image" :alt="item.name" style="width:52px; height:52px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; flex-shrink:0;">
                                                    </template>
                                                    <template x-if="!item.image">
                                                        <div style="width:52px; height:52px; background:#f3f4f6; border-radius:6px; border:1px solid #e5e7eb; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                                            <svg style="width:20px;height:20px;color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                                                        </div>
                                                    </template>
                                                    <div>
                                                        <div x-text="item.name" style="font-weight:600; color:#111827; line-height:1.3;"></div>
                                                        <div style="font-size:0.78rem; color:#6b7280; margin-top:2px;">Unit: <span x-text="parseFloat(item.price).toFixed(2) + '৳'"></span></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="padding:10px 12px; text-align:center;">
                                                <div style="display:inline-flex; align-items:center; gap:4px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:3px;">
                                                    <button type="button" @click="updateQty(item.key, item.qty - 1)"
                                                        style="width:26px;height:26px;border:none;border-radius:4px;background:transparent;cursor:pointer;font-size:1.1rem;font-weight:700;display:flex;align-items:center;justify-content:center;color:#374151;line-height:1;transition:background 0.15s;"
                                                        onmouseover="this.style.background='#f97316';this.style.color='#fff'" onmouseout="this.style.background='transparent';this.style.color='#374151'">−</button>
                                                    <span x-text="item.qty" style="min-width:26px;text-align:center;font-weight:700;font-size:0.88rem;color:#111827;"></span>
                                                    <button type="button" @click="updateQty(item.key, item.qty + 1)"
                                                        style="width:26px;height:26px;border:none;border-radius:4px;background:transparent;cursor:pointer;font-size:1.1rem;font-weight:700;display:flex;align-items:center;justify-content:center;color:#374151;line-height:1;transition:background 0.15s;"
                                                        onmouseover="this.style.background='#f97316';this.style.color='#fff'" onmouseout="this.style.background='transparent';this.style.color='#374151'">+</button>
                                                </div>
                                            </td>
                                            <td style="padding:12px 20px; text-align:right;">
                                                <div style="display:flex; align-items:center; justify-content:flex-end; gap:10px;">
                                                    <span x-text="(parseFloat(item.price) * item.qty).toFixed(2) + '৳'" style="font-weight:700; color:#111827;"></span>
                                                    <button type="button" @click="removeItem(item.key)" title="Remove item"
                                                        style="width:28px;height:28px;border:none;border-radius:5px;cursor:pointer;display:flex;align-items:center;justify-content:center;background:#fef2f2;color:#ef4444;transition:background 0.15s;flex-shrink:0;"
                                                        onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        </template>
                                    </tbody>
                                    <tfoot>
                                        <tr style="background:#fafafa;">
                                            <td colspan="2" style="padding:12px 20px; font-size:0.82rem; color:#6b7280; font-weight:600;">Items Subtotal</td>
                                            <td style="padding:12px 20px; text-align:right; font-weight:700; color:#111827;" x-text="subtotal.toFixed(2) + '৳'"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        {{-- Shipping Address --}}
                        <div class="co-card">
                            <div class="co-card-header">
                                <svg style="width:18px;height:18px;color:#f97316;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <h3>{{ sc('checkout', 'shipping_title', 'Shipping Address') }}</h3>
                            </div>
                            <div class="co-card-body">
                                <div style="display:flex; flex-direction:column; gap:20px;">
                                    <div class="co-grid-2">
                                        <div>
                                            <label class="co-label" for="shipping_name">Full Name <span style="color:#ef4444;">*</span></label>
                                            <input class="co-input @error('shipping_name') error @enderror" type="text" id="shipping_name" name="shipping_name" value="{{ old('shipping_name') }}" placeholder="{{ sc('checkout', 'name_placeholder', 'Your full name') }}" required>
                                            @error('shipping_name')<p class="co-error">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label class="co-label" for="shipping_phone">Phone Number <span style="color:#ef4444;">*</span></label>
                                            <input class="co-input @error('shipping_phone') error @enderror" type="text" id="shipping_phone" name="shipping_phone" value="{{ old('shipping_phone') }}" placeholder="{{ sc('checkout', 'phone_placeholder', '01XXXXXXXXX') }}" required>
                                            @error('shipping_phone')<p class="co-error">{{ $message }}</p>@enderror
                                        </div>
                                    </div>
                                    <div class="co-grid-2">
                                        <div>
                                            <label class="co-label" for="shipping_district">District <span style="color:#ef4444;">*</span></label>
                                            <select class="co-input co-select @error('shipping_district') error @enderror"
                                                id="shipping_district" name="shipping_district" required
                                                x-model="shippingDistrict"
                                                @change="fetchShippingThanas()">
                                                <option value="">Select District</option>
                                                @foreach($districts as $d)
                                                <option value="{{ $d }}" {{ old('shipping_district') === $d ? 'selected' : '' }}>{{ $d }}</option>
                                                @endforeach
                                            </select>
                                            @error('shipping_district')<p class="co-error">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label class="co-label" for="shipping_thana">Thana / Upazila <span style="color:#ef4444;">*</span></label>
                                            <select class="co-input co-select @error('shipping_thana') error @enderror"
                                                id="shipping_thana" name="shipping_thana" required>
                                                <option value="">Select Thana</option>
                                                <template x-for="t in shippingThanas" :key="t">
                                                    <option :value="t" x-text="t" :selected="t === '{{ old('shipping_thana') }}'"></option>
                                                </template>
                                            </select>
                                            @error('shipping_thana')<p class="co-error">{{ $message }}</p>@enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="co-label" for="shipping_address">Full Address <span style="color:#ef4444;">*</span></label>
                                        <textarea class="co-input @error('shipping_address') error @enderror" id="shipping_address" name="shipping_address" rows="3" placeholder="{{ sc('checkout', 'address_placeholder', 'House no, Road, Area...') }}" required style="resize:vertical;">{{ old('shipping_address') }}</textarea>
                                        @error('shipping_address')<p class="co-error">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Billing Address --}}
                        <div class="co-card">
                            <div class="co-card-header">
                                <svg style="width:18px;height:18px;color:#f97316;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <h3>{{ sc('checkout', 'billing_title', 'Billing Address') }}</h3>
                            </div>
                            <div class="co-card-body">
                                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-size:0.85rem; font-weight:500; color:#374151; margin-bottom:14px; user-select:none;">
                                    <input type="checkbox" x-model="sameAsBilling" style="width:17px; height:17px; accent-color:#f97316; cursor:pointer;">
                                    {{ sc('checkout', 'same_as_shipping', 'Same as shipping address') }}
                                </label>

                                <div x-show="!sameAsBilling"
                                    x-transition:enter="co-acc-enter"
                                    x-transition:enter-start="co-acc-enter-from"
                                    x-transition:enter-end="co-acc-enter-to"
                                    x-transition:leave="co-acc-leave"
                                    x-transition:leave-start="co-acc-leave-from"
                                    x-transition:leave-end="co-acc-leave-to"
                                    style="display:flex; flex-direction:column; gap:20px;">
                                    <div class="co-grid-2">
                                        <div>
                                            <label class="co-label" for="billing_name">Full Name</label>
                                            <input class="co-input" type="text" id="billing_name" name="billing_name" value="{{ old('billing_name') }}" placeholder="{{ sc('checkout', 'name_placeholder', 'Your full name') }}">
                                        </div>
                                        <div>
                                            <label class="co-label" for="billing_phone">Phone Number</label>
                                            <input class="co-input" type="text" id="billing_phone" name="billing_phone" value="{{ old('billing_phone') }}" placeholder="{{ sc('checkout', 'phone_placeholder', '01XXXXXXXXX') }}">
                                        </div>
                                    </div>
                                    <div class="co-grid-2">
                                        <div>
                                            <label class="co-label" for="billing_district">District</label>
                                            <select class="co-input co-select" id="billing_district" name="billing_district"
                                                x-model="billingDistrict"
                                                @change="fetchBillingThanas()">
                                                <option value="">Select District</option>
                                                @foreach($districts as $d)
                                                <option value="{{ $d }}" {{ old('billing_district') === $d ? 'selected' : '' }}>{{ $d }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="co-label" for="billing_thana">Thana / Upazila</label>
                                            <select class="co-input co-select" id="billing_thana" name="billing_thana">
                                                <option value="">Select Thana</option>
                                                <template x-for="t in billingThanas" :key="t">
                                                    <option :value="t" x-text="t" :selected="t === '{{ old('billing_thana') }}'"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="co-label" for="billing_address">Full Address</label>
                                        <textarea class="co-input" id="billing_address" name="billing_address" rows="3" placeholder="{{ sc('checkout', 'address_placeholder', 'House no, Road, Area...') }}" style="resize:vertical;">{{ old('billing_address') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>{{-- END LEFT --}}

                    {{-- ====== RIGHT COLUMN ====== --}}
                    <div style="display:flex; flex-direction:column; gap:20px;">

                        {{-- Order Summary --}}
                        <div class="co-card">
                            <div class="co-card-header">
                                <svg style="width:18px;height:18px;color:#f97316;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <h3>{{ sc('checkout', 'summary_title', 'Order Summary') }}</h3>
                            </div>
                            <div class="co-card-body" style="display:flex; flex-direction:column; gap:10px;">
                                <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:#374151;">
                                    <span>Items Subtotal</span>
                                    <span style="font-weight:600;" x-text="subtotal.toFixed(2) + '৳'"></span>
                                </div>
                                <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:#374151;">
                                    <span>
                                        Delivery Charge
                                        <span style="font-size:0.75rem; color:#6b7280;" x-text="shippingDistrict === 'Dhaka' ? '(Inside Dhaka)' : '(Outside Dhaka)'"></span>
                                    </span>
                                    <span style="font-weight:600;" x-text="deliveryCost.toFixed(2) + '৳'"></span>
                                </div>
                                <div style="border-top:1px dashed #e5e7eb; padding-top:10px; display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-size:0.9rem; font-weight:700; color:#111827;">Total</span>
                                    <span style="font-size:1.15rem; font-weight:800; color:#f97316;" x-text="orderTotal.toFixed(2) + '৳'"></span>
                                </div>
                                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; padding:9px 12px; font-size:0.78rem; color:#15803d; display:flex; align-items:center; gap:7px;">
                                    <svg style="width:15px;height:15px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Inside Dhaka {{ $deliveryInside }}৳ &nbsp;|&nbsp; Outside Dhaka {{ $deliveryOutside }}৳
                                </div>
                            </div>
                        </div>

                        {{-- Coupon Code --}}
                        <div class="co-card">
                            <div class="co-card-header" style="cursor:pointer;" @click="couponOpen = !couponOpen">
                                <svg style="width:18px;height:18px;color:#f97316;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                <h3>{{ sc('checkout', 'coupon_title', 'Coupon Code') }}</h3>
                                <svg width="16" height="16" :style="{ marginLeft:'auto', flexShrink:'0', transition:'transform 0.2s', transform: couponOpen ? 'rotate(180deg)' : 'rotate(0deg)', color:'#6b7280' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            <div x-show="couponOpen"
                                x-transition:enter="co-acc-enter"
                                x-transition:enter-start="co-acc-enter-from"
                                x-transition:enter-end="co-acc-enter-to"
                                x-transition:leave="co-acc-leave"
                                x-transition:leave-start="co-acc-leave-from"
                                x-transition:leave-end="co-acc-leave-to"
                                class="co-card-body">
                                <div style="display:flex; gap:8px;">
                                    <input class="co-input" type="text" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="{{ sc('checkout', 'coupon_placeholder', 'Enter coupon code') }}" style="flex:1;">
                                    <button type="button" style="padding:9px 16px; background:#f97316; color:#fff; border:none; border-radius:6px; font-size:0.82rem; font-weight:700; cursor:pointer; white-space:nowrap; transition:background 0.2s;" onmouseover="this.style.background='#ea6c0a'" onmouseout="this.style.background='#f97316'">{{ sc('checkout', 'coupon_btn', 'Apply') }}</button>
                                </div>
                                <p style="font-size:0.76rem; color:#6b7280; margin-top:8px; margin-bottom:0;">If you have a discount coupon, enter it above.</p>
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div class="co-card">
                            <div class="co-card-header">
                                <svg style="width:18px;height:18px;color:#f97316;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                <h3>{{ sc('checkout', 'payment_title', 'Payment Method') }}</h3>
                            </div>
                            <div class="co-card-body" style="display:flex; flex-direction:column; gap:10px;">
                                <input type="hidden" name="payment_method" :value="paymentMethod">

                                {{-- COD --}}
                                <div class="pay-card" :class="{ active: paymentMethod === 'cod' }" @click="paymentMethod = 'cod'">
                                    <div class="pay-radio" :class="{ active: paymentMethod === 'cod' }"></div>
                                    <div style="font-size:1.6rem; line-height:1;">💵</div>
                                    <div>
                                        <div style="font-size:0.85rem; font-weight:700; color:#111827;">{{ sc('checkout', 'cod_label', 'Cash on Delivery') }}</div>
                                        <div style="font-size:0.76rem; color:#6b7280; margin-top:1px;">{{ sc('checkout', 'cod_desc', 'Pay when your order is delivered') }}</div>
                                    </div>
                                </div>

                                {{-- Bkash --}}
                                <div class="pay-card" :class="{ active: paymentMethod === 'bkash' }" @click="paymentMethod = 'bkash'">
                                    <div class="pay-radio" :class="{ active: paymentMethod === 'bkash' }"></div>
                                    <div style="width:36px; height:36px; background:#e20074; border-radius:6px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <span style="color:#fff; font-size:0.65rem; font-weight:900; letter-spacing:-0.5px;">bKash</span>
                                    </div>
                                    <div>
                                        <div style="font-size:0.85rem; font-weight:700; color:#111827;">{{ sc('checkout', 'bkash_label', 'bKash') }}</div>
                                        <div style="font-size:0.76rem; color:#6b7280; margin-top:1px;">{{ sc('checkout', 'bkash_desc', 'Pay securely with bKash') }}</div>
                                    </div>
                                </div>

                                {{-- Online --}}
                                <div class="pay-card" :class="{ active: paymentMethod === 'online' }" @click="paymentMethod = 'online'">
                                    <div class="pay-radio" :class="{ active: paymentMethod === 'online' }"></div>
                                    <div style="background:#0c713a; border-radius:6px; padding:4px 8px; flex-shrink:0; display:flex; align-items:center; justify-content:center;">
                                        <span style="color:#fff; font-size:0.6rem; font-weight:900; letter-spacing:-0.3px; line-height:1.2; text-align:center;">SSL<br>Commerz</span>
                                    </div>
                                    <div>
                                        <div style="font-size:0.85rem; font-weight:700; color:#111827;">{{ sc('checkout', 'online_label', 'Online Payment') }}</div>
                                        <div style="font-size:0.76rem; color:#6b7280; margin-top:1px;">{{ sc('checkout', 'online_desc', 'Card, Net Banking, MFS & more') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Special Notes --}}
                        <div class="co-card">
                            <div class="co-card-header">
                                <svg style="width:18px;height:18px;color:#f97316;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <h3>{{ sc('checkout', 'notes_title', 'Special Notes') }}</h3>
                            </div>
                            <div class="co-card-body">
                                <textarea class="co-input" name="notes" rows="3" placeholder="{{ sc('checkout', 'notes_placeholder', 'Any special instructions for your order...') }}" style="resize:vertical;">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        {{-- Terms + Place Order --}}
                        <div>
                            <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer; font-size:0.82rem; color:#374151; margin-bottom:14px; line-height:1.5;">
                                <input type="checkbox" name="terms" value="1" required style="width:17px; height:17px; accent-color:#f97316; cursor:pointer; margin-top:1px; flex-shrink:0;">
                                <span>I agree to the <a href="#" style="color:#f97316; text-decoration:underline;" onclick="return false;">Terms & Conditions</a> and <a href="#" style="color:#f97316; text-decoration:underline;" onclick="return false;">Privacy Policy</a></span>
                            </label>
                            @error('terms')<p class="co-error" style="margin-bottom:10px;">{{ $message }}</p>@enderror

                            <button type="submit" class="place-btn" :disabled="submitting" x-text="submitting ? '{{ sc('checkout', 'place_order_btn', 'Place Order') }}...' : '{{ sc('checkout', 'place_order_btn', 'Place Order') }} →'"></button>

                            <div style="text-align:center; margin-top:12px;">
                                <a href="{{ route('cart.index') }}" style="font-size:0.8rem; color:#6b7280; text-decoration:none; display:inline-flex; align-items:center; gap:5px;" onmouseover="this.style.color='#f97316'" onmouseout="this.style.color='#6b7280'">
                                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    Back to Cart
                                </a>
                            </div>
                        </div>

                    </div>{{-- END RIGHT --}}

                </div>{{-- END CHECKOUT GRID --}}
            </div>

        </form>
    </div>
</div>

<style>
@media (max-width: 900px) {
    .checkout-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
function checkoutApp() {
    return {
        paymentMethod: '{{ old('payment_method', 'cod') }}',
        sameAsBilling: true,
        couponOpen: false,
        submitting: false,
        shippingDistrict: '{{ old('shipping_district', '') }}',
        billingDistrict: '{{ old('billing_district', '') }}',
        shippingThanas: @json(old('shipping_district') ? \App\Services\BangladeshGeoService::thanasForDistrict(old('shipping_district')) : []),
        billingThanas: @json(old('billing_district') ? \App\Services\BangladeshGeoService::thanasForDistrict(old('billing_district')) : []),
        cartItems: @json(collect($items)->map(fn($item, $key) => array_merge($item, ['key' => (string)$key]))->values()),
        deliveryInside: {{ $deliveryInside }},
        deliveryOutside: {{ $deliveryOutside }},

        get subtotal() {
            return this.cartItems.reduce((s, i) => s + parseFloat(i.price) * i.qty, 0);
        },

        get deliveryCost() {
            return this.shippingDistrict === 'Dhaka' ? this.deliveryInside : this.deliveryOutside;
        },

        get orderTotal() {
            return this.subtotal + this.deliveryCost;
        },

        async updateQty(key, newQty) {
            if (newQty < 1) { return this.removeItem(key); }
            const item = this.cartItems.find(i => i.key === key);
            if (item) item.qty = newQty;
            try {
                await fetch('{{ url('/cart/update') }}/' + key, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ qty: newQty })
                });
            } catch(e) {}
        },

        async removeItem(key) {
            this.cartItems = this.cartItems.filter(i => i.key !== key);
            try {
                await fetch('{{ url('/cart/remove') }}/' + key, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
            } catch(e) {}
            if (this.cartItems.length === 0) {
                window.location.href = '{{ route('cart.index') }}';
            }
        },

        async fetchShippingThanas() {
            if (!this.shippingDistrict) { this.shippingThanas = []; return; }
            try {
                const r = await fetch('{{ route('checkout.thanas') }}?district=' + encodeURIComponent(this.shippingDistrict));
                const d = await r.json();
                this.shippingThanas = d.thanas || [];
            } catch(e) {}
        },

        async fetchBillingThanas() {
            if (!this.billingDistrict) { this.billingThanas = []; return; }
            try {
                const r = await fetch('{{ route('checkout.thanas') }}?district=' + encodeURIComponent(this.billingDistrict));
                const d = await r.json();
                this.billingThanas = d.thanas || [];
            } catch(e) {}
        },

        handleSubmit(e) {
            this.submitting = true;
            e.target.submit();
        },
    }
}
</script>
@endsection
