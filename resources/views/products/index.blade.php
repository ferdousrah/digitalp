@extends('layouts.app')
@section('title', 'Products - Digital Support')

@section('content')
@include('components.breadcrumb', ['items' => [['label' => 'Products']]])

<h1 class="sr-only">Products</h1>

<div class="container-custom px-4 sm:px-6 lg:px-8 pb-16"
     x-data="{ filterOpen: false }"
     @keydown.escape.window="if (filterOpen) { filterOpen = false; document.body.style.overflow=''; }">

    {{-- Mobile-only toolbar: filter trigger + count --}}
    <div class="filter-mobile-toolbar lg:hidden">
        <button type="button"
            @click="filterOpen = true; document.body.style.overflow='hidden';"
            class="filter-trigger-btn">
            <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            Filter &amp; Sort
        </button>
        <span class="filter-count-pill">{{ $products->total() }} products</span>
    </div>

    {{-- Overlay (mobile only) --}}
    <div x-show="filterOpen" x-cloak x-transition.opacity
        @click="filterOpen = false; document.body.style.overflow='';"
        class="filter-overlay lg:hidden"></div>

    <div id="product-listing" class="flex flex-col lg:flex-row gap-8">
        <div class="filter-pane" :class="filterOpen ? 'is-open' : ''">
            {{-- Mobile drawer header --}}
            <div class="filter-drawer-header lg:hidden">
                <h3 class="font-semibold text-lg text-surface-900">Filter &amp; Sort</h3>
                <button type="button"
                    @click="filterOpen = false; document.body.style.overflow='';"
                    aria-label="Close filters"
                    class="filter-close-btn">
                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @include('components.product-filter-sidebar', [
                'action' => route('products.index'),
                'categories' => $categories,
                'brands' => $brands,
                'filterAttributes' => $filterAttributes,
                'priceRange' => $priceRange,
            ])

            {{-- Mobile drawer footer: apply CTA --}}
            <div class="filter-drawer-footer lg:hidden">
                <button type="button"
                    @click="filterOpen = false; document.body.style.overflow='';"
                    class="filter-apply-btn">
                    Show <span id="filter-apply-count">{{ $products->total() }}</span> products
                </button>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="flex-1" id="products-area">
            <div class="flex justify-between items-center mb-6">
                <p class="text-surface-600 hidden sm:block" id="products-count">{{ $products->total() }} products found</p>
                <select id="sort-select" class="border-surface-300 rounded-lg text-sm w-full sm:w-auto">
                    <option value="" {{ !request('sort') ? 'selected' : '' }}>Default Sort</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
                </select>
            </div>

            <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 items-start">
                @forelse($products as $product)
                    @include('components.product-card', ['product' => $product])
                @empty
                    <div class="col-span-full">
                        <x-empty-state
                            icon="search"
                            title="No products match your filters"
                            body="Try removing a filter or two — or browse our full catalogue."
                            ctaLabel="View All Products"
                            :ctaHref="route('products.index')"
                            variant="plain" />
                    </div>
                @endforelse
            </div>

            <!-- Infinite scroll sentinel -->
            @if($products->hasMorePages())
            <div id="scroll-sentinel" style="display:flex; justify-content:center; padding:32px 0;"
                 data-next-url="{{ $products->nextPageUrl() }}">
                <svg id="scroll-spinner" style="width:28px; height:28px; animation:spin 1s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.2"/>
                    <path d="M12 2a10 10 0 019.8 8" stroke-linecap="round"/>
                </svg>
            </div>
            @else
            <div id="scroll-sentinel"></div>
            @if($products->total() > 0)
            <p style="text-align:center; padding:24px 0; color:#9ca3af; font-size:0.875rem;">You've reached the end of the list</p>
            @endif
            @endif
        </div>
    </div>
</div>

<!-- Loading overlay -->
<div id="filter-loading" style="display:none; position:fixed; inset:0; background:rgba(255,255,255,0.5); z-index:30; align-items:center; justify-content:center;">
    <div style="background:#fff; padding:16px 32px; border-radius:12px; box-shadow:0 4px 24px rgba(0,0,0,0.12); display:flex; align-items:center; gap:12px;">
        <svg style="width:24px;height:24px;animation:spin 1s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-opacity="0.2"/><path d="M12 2a10 10 0 019.8 8" stroke-linecap="round"/></svg>
        <span style="color:#374151; font-size:0.95rem;">Filtering...</span>
    </div>
</div>
<style>
@keyframes spin{to{transform:rotate(360deg)}}
[x-cloak] { display: none !important; }

/* Skeleton placeholder cards — shown while AJAX filter is in flight */
@keyframes skel-shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
.skel-shimmer {
    background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
    background-size: 200% 100%;
    animation: skel-shimmer 1.4s ease-in-out infinite;
}
.product-card-skel {
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}

/* ─── Mobile filter toolbar (visible <1024px) ─── */
.filter-mobile-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 16px;
    padding: 12px 0;
}
.filter-trigger-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: #fff;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    color: #111827;
    cursor: pointer;
    transition: all 0.2s;
}
.filter-trigger-btn:hover { border-color: #16a34a; color: #16a34a; }
.filter-count-pill {
    font-size: 0.85rem;
    color: #6b7280;
    font-weight: 500;
}

/* ─── Mobile drawer ─── */
@media (max-width: 1023px) {
    .filter-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        z-index: 60;
        backdrop-filter: blur(2px);
    }
    .filter-pane {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: 320px;
        max-width: 90vw;
        background: #fff;
        z-index: 70;
        overflow-y: auto;
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(.4,0,.2,1);
        box-shadow: 8px 0 32px rgba(0, 0, 0, 0.15);
        display: flex;
        flex-direction: column;
    }
    .filter-pane.is-open { transform: translateX(0); }

    /* Override the partial's inner sticky positioning inside the drawer */
    .filter-pane aside { width: 100% !important; flex: 1; }
    .filter-pane aside > form > div {
        position: static !important;
        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }
    .filter-pane aside .sticky { position: static !important; }
    /* Drop the partial's header (we use our own) */
    .filter-pane aside form > div > div:first-child {
        display: none !important;
    }
    .filter-pane aside [style*="max-height: calc"] {
        max-height: none !important;
        overflow-y: visible !important;
    }

    .filter-drawer-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 1;
    }
    .filter-close-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
    }
    .filter-close-btn:hover { background: #f1f5f9; color: #0f172a; }

    .filter-drawer-footer {
        position: sticky;
        bottom: 0;
        padding: 14px 20px;
        background: #fff;
        border-top: 1px solid #f1f5f9;
        box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.04);
    }
    .filter-apply-btn {
        width: 100%;
        padding: 14px;
        background: #f97316;
        color: #fff;
        font-size: 0.88rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: background 0.2s;
    }
    .filter-apply-btn:hover { background: #ea6c0a; }
}

/* Desktop: hide drawer-only chrome */
@media (min-width: 1024px) {
    .filter-mobile-toolbar { display: none; }
    .filter-drawer-header,
    .filter-drawer-footer,
    .filter-overlay { display: none !important; }
}
</style>
@endsection

@push('scripts')
<script>
(function () {
    var form = document.getElementById('filter-form');
    var listing = document.getElementById('product-listing');
    var loading = document.getElementById('filter-loading');
    var sortSelect = document.getElementById('sort-select');
    if (!form || !listing) return;

    var debounceTimer = null;
    var fetchController = null;
    var scrollLoading = false;
    var scrollObserver = null;

    function buildUrl() {
        var formData = new FormData(form);
        var params = new URLSearchParams();
        for (var pair of formData.entries()) {
            if (pair[1] !== '' && pair[1] !== null) {
                params.append(pair[0], pair[1]);
            }
        }
        if (sortSelect && sortSelect.value) {
            params.set('sort', sortSelect.value);
        }
        var action = form.getAttribute('action');
        var qs = params.toString();
        return action + (qs ? '?' + qs : '');
    }

    // Infinite scroll: load next page and append cards
    function loadNextPage(sentinel) {
        if (scrollLoading || !sentinel) return;
        var nextUrl = sentinel.getAttribute('data-next-url');
        if (!nextUrl) return;

        scrollLoading = true;

        fetch(nextUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function (res) { return res.text(); })
        .then(function (html) {
            var doc = new DOMParser().parseFromString(html, 'text/html');
            var newGrid = doc.getElementById('products-grid');
            var newSentinel = doc.getElementById('scroll-sentinel');
            var grid = document.getElementById('products-grid');

            if (newGrid && grid) {
                // Append new product cards
                var cards = newGrid.children;
                var fragment = document.createDocumentFragment();
                while (cards.length > 0) {
                    fragment.appendChild(cards[0]);
                }
                grid.appendChild(fragment);
                if (window.Alpine) Alpine.initTree(grid);
            }

            // Update sentinel with next page URL or remove it
            if (newSentinel && newSentinel.getAttribute('data-next-url')) {
                sentinel.setAttribute('data-next-url', newSentinel.getAttribute('data-next-url'));
            } else {
                // No more pages
                if (scrollObserver) scrollObserver.unobserve(sentinel);
                sentinel.innerHTML = '<p style="text-align:center; padding:8px 0; color:#9ca3af; font-size:0.875rem;">You\'ve reached the end of the list</p>';
                sentinel.removeAttribute('data-next-url');
            }

            scrollLoading = false;
        })
        .catch(function () {
            scrollLoading = false;
        });
    }

    // Set up IntersectionObserver for infinite scroll
    function setupScrollObserver() {
        if (scrollObserver) scrollObserver.disconnect();
        var sentinel = document.getElementById('scroll-sentinel');
        if (!sentinel || !sentinel.getAttribute('data-next-url')) return;

        scrollObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    loadNextPage(entry.target);
                }
            });
        }, { rootMargin: '200px' });

        scrollObserver.observe(sentinel);
    }

    // Render N skeleton placeholder cards into the grid while waiting for results
    function showSkeleton(count) {
        var grid = document.getElementById('products-grid');
        if (!grid) return;
        var skel = '<div class="product-card-skel" style="background:#fff; border:1px solid #e5e7eb; border-radius:8px; overflow:hidden;">'
            +   '<div class="skel-shimmer" style="aspect-ratio:1/1;"></div>'
            +   '<div style="padding:16px;">'
            +     '<div class="skel-shimmer" style="height:12px; width:50%; border-radius:4px; margin-bottom:10px;"></div>'
            +     '<div class="skel-shimmer" style="height:14px; width:85%; border-radius:4px; margin-bottom:10px;"></div>'
            +     '<div class="skel-shimmer" style="height:14px; width:60%; border-radius:4px; margin-bottom:14px;"></div>'
            +     '<div class="skel-shimmer" style="height:22px; width:45%; border-radius:4px;"></div>'
            +   '</div>'
            + '</div>';
        var html = '';
        for (var i = 0; i < count; i++) html += skel;
        grid.innerHTML = html;
    }

    // Filter: replaces the entire grid (resets to page 1)
    function doFilter() {
        var url = buildUrl();
        if (fetchController) fetchController.abort();
        fetchController = new AbortController();
        // Skeleton cards in the grid feel snappier than a centered spinner overlay
        showSkeleton(8);
        scrollLoading = false;

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal: fetchController.signal
        })
        .then(function (res) { return res.text(); })
        .then(function (html) {
            var doc = new DOMParser().parseFromString(html, 'text/html');
            var newListing = doc.getElementById('product-listing');
            if (newListing) {
                listing.innerHTML = newListing.innerHTML;
                if (window.Alpine) Alpine.initTree(listing);
                rebind();
            }
            // Sync mobile toolbar pill + drawer "Show X products" CTA
            var newCountEl = doc.getElementById('products-count');
            var newCount   = newCountEl ? (newCountEl.textContent.match(/\d+/) || ['0'])[0] : '0';
            var pill = document.querySelector('.filter-count-pill');
            var apply = document.getElementById('filter-apply-count');
            if (pill)  pill.textContent  = newCount + ' products';
            if (apply) apply.textContent = newCount;
            history.pushState(null, '', url);
        })
        .catch(function (err) { /* AbortError is expected when filter changes mid-flight */ });
    }

    function onChange(e) {
        if (e.target.hasAttribute('data-debounce')) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(doFilter, 600);
        } else {
            doFilter();
        }
    }

    function rebind() {
        form = document.getElementById('filter-form');
        sortSelect = document.getElementById('sort-select');
        listing = document.getElementById('product-listing');
        if (!form) return;

        form.querySelectorAll('[data-filter-input]').forEach(function (input) {
            input.addEventListener('change', onChange);
            if (input.hasAttribute('data-debounce') && input.type !== 'range') {
                input.addEventListener('input', onChange);
            }
        });

        if (sortSelect) sortSelect.addEventListener('change', doFilter);

        var resetLink = form.querySelector('[data-filter-reset]');
        if (resetLink) {
            resetLink.addEventListener('click', function (e) {
                e.preventDefault();
                form.reset();
                var action = form.getAttribute('action');
                if (fetchController) fetchController.abort();
                fetchController = new AbortController();
                loading.style.display = 'flex';
                fetch(action, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: fetchController.signal
                })
                .then(function (res) { return res.text(); })
                .then(function (html) {
                    var doc = new DOMParser().parseFromString(html, 'text/html');
                    var nl = doc.getElementById('product-listing');
                    if (nl) { listing.innerHTML = nl.innerHTML; if (window.Alpine) Alpine.initTree(listing); rebind(); }
                    history.pushState(null, '', action);
                    loading.style.display = 'none';
                })
                .catch(function () { loading.style.display = 'none'; });
            });
        }

        // Set up infinite scroll after each rebind
        setupScrollObserver();
    }

    form.addEventListener('submit', function (e) { e.preventDefault(); doFilter(); });
    rebind();
    window.addEventListener('popstate', function () { location.reload(); });
})();
</script>
@endpush
