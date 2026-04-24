@extends('layouts.app')
@section('title', 'Products - Digital Support')

@section('content')
@include('components.breadcrumb', ['items' => [['label' => 'Products']]])

<div class="container-custom px-4 sm:px-6 lg:px-8 pb-16">
    <div id="product-listing" class="flex flex-col lg:flex-row gap-8">
        @include('components.product-filter-sidebar', [
            'action' => route('products.index'),
            'categories' => $categories,
            'brands' => $brands,
            'filterAttributes' => $filterAttributes,
            'priceRange' => $priceRange,
        ])

        <!-- Products Grid -->
        <div class="flex-1" id="products-area">
            <div class="flex justify-between items-center mb-6">
                <p class="text-surface-600" id="products-count">{{ $products->total() }} products found</p>
                <select id="sort-select" class="border-surface-300 rounded-lg text-sm">
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
                    <div class="col-span-full text-center py-16">
                        <p class="text-surface-500 text-lg">No products found matching your criteria.</p>
                        <a href="{{ route('products.index') }}" class="btn-primary mt-4 inline-block">View All Products</a>
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
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
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

    // Filter: replaces the entire grid (resets to page 1)
    function doFilter() {
        var url = buildUrl();
        if (fetchController) fetchController.abort();
        fetchController = new AbortController();
        loading.style.display = 'flex';
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
            history.pushState(null, '', url);
            loading.style.display = 'none';
        })
        .catch(function (err) {
            if (err.name !== 'AbortError') loading.style.display = 'none';
        });
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
