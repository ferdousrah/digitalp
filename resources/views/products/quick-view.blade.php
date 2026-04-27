<div style="display:flex; gap:24px; flex-wrap:wrap;">
    <!-- Image -->
    <div style="flex:0 0 280px; max-width:280px;">
        @if($product->getFirstMediaUrl('product_thumbnail', 'large') || $product->getFirstMediaUrl('product_images', 'large'))
            <img src="{{ $product->getFirstMediaUrl('product_images', 'large') ?: $product->getFirstMediaUrl('product_thumbnail', 'large') }}" alt="{{ $product->name }}" decoding="async" style="width:100%; border-radius:12px; object-fit:cover; aspect-ratio:1/1; background:#f9fafb;">
        @else
            <div style="width:100%; aspect-ratio:1/1; background:#f3f4f6; border-radius:12px; display:flex; align-items:center; justify-content:center;">
                <svg style="width:64px; height:64px; color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        @endif
    </div>

    <!-- Details -->
    <div style="flex:1; min-width:240px;">
        @if($product->brand)
            <p style="font-size:0.75rem; color:#16a34a; text-transform:uppercase; letter-spacing:0.08em; font-weight:600; margin-bottom:6px;">{{ $product->brand->name }}</p>
        @endif

        <h2 style="font-size:1.25rem; font-weight:700; color:#111827; margin-bottom:8px; line-height:1.3;">{{ $product->name }}</h2>

        @if($product->sku)
            <p style="font-size:0.8rem; color:#9ca3af; margin-bottom:12px;">SKU: {{ $product->sku }}</p>
        @endif

        <!-- Price -->
        <div style="display:flex; align-items:baseline; gap:10px; margin-bottom:12px;">
            <span style="font-size:1.5rem; font-weight:700; color:#16a34a;">@bdt($product->price)</span>
            @if($product->compare_price)
                <span style="font-size:1rem; color:#9ca3af; text-decoration:line-through;">@bdt($product->compare_price)</span>
                <span style="background:#fef2f2; color:#dc2626; font-size:0.75rem; font-weight:600; padding:3px 8px; border-radius:12px;">
                    -{{ round(($product->compare_price - $product->price) / $product->compare_price * 100) }}%
                </span>
            @endif
        </div>

        <!-- Stock -->
        <div style="margin-bottom:16px;">
            @if($product->in_stock)
                <span style="display:inline-flex; align-items:center; gap:5px; font-size:0.85rem; color:#16a34a; font-weight:500;">
                    <span style="width:7px; height:7px; background:#16a34a; border-radius:50%; display:inline-block;"></span>
                    In Stock{{ $product->stock_quantity ? ' ('.$product->stock_quantity.' available)' : '' }}
                </span>
            @else
                <span style="display:inline-flex; align-items:center; gap:5px; font-size:0.85rem; color:#ef4444; font-weight:500;">
                    <span style="width:7px; height:7px; background:#ef4444; border-radius:50%; display:inline-block;"></span>
                    Out of Stock
                </span>
            @endif
        </div>

        <!-- Short Description -->
        @if($product->short_description)
            <div style="font-size:0.875rem; color:#4b5563; line-height:1.6; margin-bottom:16px; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
                {!! strip_tags($product->short_description) !!}
            </div>
        @endif

        <!-- Key Specs (first 4) -->
        @if($product->specifications && count($product->specifications))
            <div style="margin-bottom:16px; border-top:1px solid #f3f4f6; padding-top:12px;">
                @foreach(array_slice($product->specifications, 0, 4, true) as $key => $value)
                    <div style="display:flex; font-size:0.825rem; padding:3px 0;">
                        <span style="width:40%; color:#6b7280;">{{ $key }}</span>
                        <span style="width:60%; color:#111827; font-weight:500;">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Actions -->
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('products.show', $product) }}" style="display:inline-flex; align-items:center; gap:6px; background:#16a34a; color:#fff; font-weight:600; font-size:0.875rem; padding:10px 20px; border-radius:8px; text-decoration:none; transition:background 0.2s;" onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                View Full Details
                <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
            <button type="button" onclick="toggleWishlist({{ $product->id }}, this)" class="wishlist-btn-{{ $product->id }}" style="display:inline-flex; align-items:center; gap:6px; background:#fff; color:#6b7280; font-weight:500; font-size:0.875rem; padding:10px 16px; border-radius:8px; border:1px solid #e5e7eb; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.borderColor='#ef4444'; this.style.color='#ef4444'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='#6b7280'">
                <svg style="width:18px; height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                Wishlist
            </button>
            <button type="button" onclick="toggleCompare({{ $product->id }}, this)" class="compare-btn-{{ $product->id }}" style="display:inline-flex; align-items:center; gap:6px; background:#fff; color:#6b7280; font-weight:500; font-size:0.875rem; padding:10px 16px; border-radius:8px; border:1px solid #e5e7eb; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.borderColor='#16a34a'; this.style.color='#16a34a'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='#6b7280'">
                <svg style="width:18px; height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Compare
            </button>
        </div>
    </div>
</div>
