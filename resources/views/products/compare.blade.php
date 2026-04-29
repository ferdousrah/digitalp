@extends('layouts.app')
@section('title', 'Compare Products')

@section('content')
@include('components.breadcrumb', ['items' => [['label' => 'Products', 'url' => route('products.index')], ['label' => 'Compare']]])

<div class="container-custom px-4 sm:px-6 lg:px-8 pb-16">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:32px;">
        <h1 style="font-size:1.875rem; font-weight:700; color:#111827;">Compare Products</h1>
        @if($products->count())
            <button onclick="clearCompare()" style="font-size:0.875rem; color:#ef4444; background:none; border:1px solid #fecaca; padding:8px 16px; border-radius:8px; cursor:pointer; font-weight:500; transition:all 0.2s;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'">
                <svg style="width:16px; height:16px; display:inline; vertical-align:-3px; margin-right:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Clear All
            </button>
        @endif
    </div>

    @if($products->count())
    <div style="overflow-x:auto; border-radius:16px; border:1px solid #e5e7eb; background:#fff;">
        <table style="width:100%; border-collapse:collapse; min-width:600px;">
            <!-- Product images & names -->
            <thead>
                <tr>
                    <th style="padding:20px; text-align:left; background:#f9fafb; border-bottom:1px solid #e5e7eb; font-weight:600; color:#6b7280; width:180px; font-size:0.875rem;">Product</th>
                    @foreach($products as $product)
                    <td style="padding:20px; text-align:center; border-bottom:1px solid #e5e7eb; min-width:220px; vertical-align:top;">
                        <div style="position:relative; display:inline-block;">
                            <button onclick="removeFromCompare({{ $product->id }}, this)" title="Remove" style="position:absolute; top:-8px; right:-8px; width:24px; height:24px; background:#ef4444; color:#fff; border:2px solid #fff; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:0; z-index:2; box-shadow:0 2px 6px rgba(0,0,0,0.15); transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                                <svg style="width:12px; height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            @if($product->getFirstMediaUrl('product_thumbnail', 'medium'))
                                <img src="{{ $product->getFirstMediaUrl('product_thumbnail', 'medium') }}" loading="lazy" decoding="async" alt="{{ $product->name }}" style="width:140px; height:140px; object-fit:contain; border-radius:12px; background:#f9fafb; border:1px solid #f3f4f6;">
                            @else
                                <div style="width:140px; height:140px; background:#f3f4f6; border-radius:12px; display:flex; align-items:center; justify-content:center;">
                                    <svg style="width:48px; height:48px; color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                        </div>
                        <div style="margin-top:12px;">
                            <a href="{{ route('products.show', $product) }}" style="font-weight:600; color:#111827; text-decoration:none; font-size:0.95rem; line-height:1.3; display:block; transition:color 0.2s;" onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='#111827'">{{ $product->name }}</a>
                        </div>
                    </td>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <!-- Price -->
                <tr>
                    <td style="padding:16px 20px; background:#f9fafb; border-bottom:1px solid #f3f4f6; font-weight:600; color:#374151; font-size:0.875rem;">Price</td>
                    @foreach($products as $p)
                    <td style="padding:16px 20px; text-align:center; border-bottom:1px solid #f3f4f6;">
                        <span style="font-size:1.25rem; font-weight:700; color:#16a34a;">@bdt($p->price)</span>
                        @if($p->compare_price)
                            <br><span style="font-size:0.825rem; color:#9ca3af; text-decoration:line-through;">@bdt($p->compare_price)</span>
                            <span style="font-size:0.75rem; color:#ef4444; font-weight:600; margin-left:4px;">-{{ round(($p->compare_price - $p->price) / $p->compare_price * 100) }}%</span>
                        @endif
                    </td>
                    @endforeach
                </tr>

                <!-- Brand -->
                <tr>
                    <td style="padding:16px 20px; background:#f9fafb; border-bottom:1px solid #f3f4f6; font-weight:600; color:#374151; font-size:0.875rem;">Brand</td>
                    @foreach($products as $p)
                    <td style="padding:16px 20px; text-align:center; border-bottom:1px solid #f3f4f6; color:#374151;">{{ $p->brand?->name ?? '-' }}</td>
                    @endforeach
                </tr>

                <!-- Availability -->
                <tr>
                    <td style="padding:16px 20px; background:#f9fafb; border-bottom:1px solid #f3f4f6; font-weight:600; color:#374151; font-size:0.875rem;">Availability</td>
                    @foreach($products as $p)
                    <td style="padding:16px 20px; text-align:center; border-bottom:1px solid #f3f4f6;">
                        @if($p->in_stock)
                            <span style="display:inline-flex; align-items:center; gap:5px; color:#16a34a; font-weight:500; font-size:0.875rem;">
                                <span style="width:8px; height:8px; background:#16a34a; border-radius:50%; display:inline-block;"></span>
                                In Stock
                            </span>
                        @else
                            <span style="display:inline-flex; align-items:center; gap:5px; color:#ef4444; font-weight:500; font-size:0.875rem;">
                                <span style="width:8px; height:8px; background:#ef4444; border-radius:50%; display:inline-block;"></span>
                                Out of Stock
                            </span>
                        @endif
                    </td>
                    @endforeach
                </tr>

                <!-- SKU -->
                <tr>
                    <td style="padding:16px 20px; background:#f9fafb; border-bottom:1px solid #f3f4f6; font-weight:600; color:#374151; font-size:0.875rem;">SKU</td>
                    @foreach($products as $p)
                    <td style="padding:16px 20px; text-align:center; border-bottom:1px solid #f3f4f6; color:#6b7280; font-size:0.875rem;">{{ $p->sku ?? '-' }}</td>
                    @endforeach
                </tr>

                <!-- Warranty -->
                <tr>
                    <td style="padding:16px 20px; background:#f9fafb; border-bottom:1px solid #f3f4f6; font-weight:600; color:#374151; font-size:0.875rem;">Warranty</td>
                    @foreach($products as $p)
                    <td style="padding:16px 20px; text-align:center; border-bottom:1px solid #f3f4f6; color:#374151; font-size:0.875rem;">{{ $p->warranty_info ?? '-' }}</td>
                    @endforeach
                </tr>

                <!-- Specifications (dynamic rows) -->
                @php
                    $allSpecKeys = collect();
                    foreach ($products as $p) {
                        if ($p->specifications) {
                            $allSpecKeys = $allSpecKeys->merge(array_keys($p->specifications));
                        }
                    }
                    $allSpecKeys = $allSpecKeys->unique();
                @endphp
                @foreach($allSpecKeys as $specKey)
                <tr>
                    <td style="padding:16px 20px; background:#f9fafb; border-bottom:1px solid #f3f4f6; font-weight:600; color:#374151; font-size:0.875rem;">{{ $specKey }}</td>
                    @foreach($products as $p)
                    <td style="padding:16px 20px; text-align:center; border-bottom:1px solid #f3f4f6; color:#374151; font-size:0.875rem;">{{ $p->specifications[$specKey] ?? '-' }}</td>
                    @endforeach
                </tr>
                @endforeach

                <!-- Short Description -->
                <tr>
                    <td style="padding:16px 20px; background:#f9fafb; border-bottom:1px solid #f3f4f6; font-weight:600; color:#374151; font-size:0.875rem;">Description</td>
                    @foreach($products as $p)
                    <td style="padding:16px 20px; text-align:left; border-bottom:1px solid #f3f4f6; color:#6b7280; font-size:0.825rem; line-height:1.5;">
                        {{ $p->short_description ? \Illuminate\Support\Str::limit(strip_tags($p->short_description), 120) : '-' }}
                    </td>
                    @endforeach
                </tr>

                <!-- Actions -->
                <tr>
                    <td style="padding:20px; background:#f9fafb; font-weight:600; color:#374151; font-size:0.875rem;">Actions</td>
                    @foreach($products as $p)
                    <td style="padding:20px; text-align:center;">
                        <div style="display:flex; flex-direction:column; gap:8px; align-items:center;">
                            <a href="{{ route('products.show', $p) }}" style="display:inline-flex; align-items:center; gap:6px; background:#16a34a; color:#fff; font-weight:600; font-size:0.825rem; padding:8px 18px; border-radius:8px; text-decoration:none; transition:background 0.2s;" onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                                View Details
                                <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                            <button onclick="toggleWishlist({{ $p->id }}, this)" class="wishlist-btn-{{ $p->id }}" style="font-size:0.8rem; color:#6b7280; background:none; border:1px solid #e5e7eb; padding:6px 14px; border-radius:6px; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.borderColor='#ef4444'; this.style.color='#ef4444'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='#6b7280'">
                                <svg style="width:14px; height:14px; display:inline; vertical-align:-2px; margin-right:3px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                Wishlist
                            </button>
                        </div>
                    </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center; padding:80px 20px;">
        <div style="width:80px; height:80px; background:#f0fdf4; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
            <svg style="width:40px; height:40px; color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <h2 style="font-size:1.25rem; font-weight:600; color:#111827; margin-bottom:8px;">No products to compare</h2>
        <p style="color:#6b7280; margin-bottom:24px;">Browse products and click the compare icon to add them here.</p>
        <a href="{{ route('products.index') }}" style="display:inline-flex; align-items:center; gap:6px; background:#16a34a; color:#fff; font-weight:600; font-size:0.9rem; padding:12px 24px; border-radius:10px; text-decoration:none; transition:background 0.2s;" onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
            Browse Products
            <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>
    @endif
</div>

@push('scripts')
<script>
function removeFromCompare(productId, btn) {
    fetch('{{ url("compare/remove") }}/' + productId, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        showToast(data.message, 'info');
        var badge = document.getElementById('compare-badge');
        if (badge) { badge.textContent = data.count; badge.style.display = data.count > 0 ? 'flex' : 'none'; }
        // Reload page to refresh the table
        setTimeout(function() { window.location.reload(); }, 500);
    })
    .catch(function() { showToast('Something went wrong.', 'error'); });
}

function clearCompare() {
    fetch('{{ route("compare.clear") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        showToast(data.message, 'info');
        var badge = document.getElementById('compare-badge');
        if (badge) { badge.textContent = '0'; badge.style.display = 'none'; }
        setTimeout(function() { window.location.reload(); }, 500);
    })
    .catch(function() { showToast('Something went wrong.', 'error'); });
}
</script>
@endpush
@endsection
