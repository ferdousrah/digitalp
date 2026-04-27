/**
 * Digital Support — Service Worker
 *
 * Cache strategies:
 *   - HTML / navigation requests → network-first with cache fallback (last-known-good page).
 *     If network fails AND the page isn't cached, serve offline.html.
 *   - Static assets (CSS/JS/fonts/images) → stale-while-revalidate (return cached copy
 *     immediately for speed, fetch in background to refresh for next visit).
 *   - Cross-origin and dynamic / mutating endpoints → bypass entirely.
 *
 * Bumping VERSION invalidates all caches and re-installs.
 */
const VERSION = 'ds-v1';
const STATIC_CACHE = 'ds-static-' + VERSION;
const PAGES_CACHE  = 'ds-pages-'  + VERSION;

// Resolve relative to the SW's location so subdirectory deployments work.
// e.g. SW at /digitalp/sw.js → OFFLINE_URL becomes /digitalp/offline.html.
const OFFLINE_URL = new URL('offline.html', self.location).href;

const PRECACHE = [OFFLINE_URL];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => cache.addAll(PRECACHE))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(
            keys.filter((k) => !k.endsWith(VERSION)).map((k) => caches.delete(k))
        )).then(() => self.clients.claim())
    );
});

// Patterns we don't want to cache or proxy — anything authenticated, mutating, or admin-only
const BYPASS_PATTERNS = /\/(admin|livewire|api\b|filament|logout|auth\/(otp|logout)|cart\/(add|update|remove|clear|data|suggestions)|newsletter\/subscribe|sslcommerz|bkash|account|track-order|checkout)/;

self.addEventListener('fetch', (event) => {
    const req = event.request;

    // Only handle GET — POST/PUT/DELETE always go to network
    if (req.method !== 'GET') return;

    const url = new URL(req.url);

    // Same-origin only
    if (url.origin !== self.location.origin) return;

    // Skip auth / dynamic / admin endpoints
    if (BYPASS_PATTERNS.test(url.pathname)) return;

    // ── HTML navigations → network-first ─────────────────────────────
    const accept = req.headers.get('accept') || '';
    const isNavigation = req.mode === 'navigate' || accept.includes('text/html');

    if (isNavigation) {
        event.respondWith(
            fetch(req)
                .then((response) => {
                    // Cache successful HTML for offline fallback
                    if (response.ok && response.status < 400) {
                        const copy = response.clone();
                        caches.open(PAGES_CACHE).then((cache) => cache.put(req, copy));
                    }
                    return response;
                })
                .catch(() =>
                    caches.match(req).then((cached) =>
                        cached || caches.match(OFFLINE_URL)
                    )
                )
        );
        return;
    }

    // ── Static assets → stale-while-revalidate ──────────────────────
    if (/\.(css|js|mjs|png|jpe?g|webp|avif|svg|gif|ico|woff2?|ttf|otf|eot)$/i.test(url.pathname)) {
        event.respondWith(
            caches.match(req).then((cached) => {
                const networkFetch = fetch(req)
                    .then((response) => {
                        if (response.ok && response.status < 400) {
                            const copy = response.clone();
                            caches.open(STATIC_CACHE).then((cache) => cache.put(req, copy));
                        }
                        return response;
                    })
                    .catch(() => cached); // fall back to cache on network failure

                // Return cached immediately if available, otherwise wait for network
                return cached || networkFetch;
            })
        );
        return;
    }

    // Everything else → network with no caching (don't get in the way)
});

// Allow the page to trigger an immediate update via postMessage
self.addEventListener('message', (event) => {
    if (event.data === 'SKIP_WAITING') self.skipWaiting();
});
