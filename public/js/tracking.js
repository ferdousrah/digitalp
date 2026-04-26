/**
 * Universal tracking helper — fires the right shape for both GA4 and Facebook Pixel.
 * Loaded as a static <script> from layouts/app.blade.php (no Vite-bundled).
 *
 * Usage anywhere:
 *   window.dsTrack('view_item', { id: 1, name: 'Phone', price: 100 })
 *   window.dsTrack('add_to_cart', { id, name, price, qty })
 *   window.dsTrack('purchase', { order_number, total, items: [...] })
 */
(function () {
    function gtag() {
        if (typeof window.gtag === 'function') {
            window.gtag.apply(null, arguments);
        }
    }
    function fbq() {
        if (typeof window.fbq === 'function') {
            window.fbq.apply(null, arguments);
        }
    }

    function toGAItem(p) {
        return {
            item_id:    String(p.id ?? p.item_id ?? ''),
            item_name:  p.name ?? p.item_name ?? '',
            item_brand: p.brand ?? null,
            item_category: p.category ?? null,
            price:      Number(p.price ?? 0),
            quantity:   Number(p.qty ?? p.quantity ?? 1),
        };
    }

    window.dsTrack = function (event, data) {
        data = data || {};

        switch (event) {
            case 'view_item': {
                const item = toGAItem(data);
                gtag('event', 'view_item', {
                    currency: 'BDT',
                    value: Number(data.price ?? 0),
                    items: [item],
                });
                fbq('track', 'ViewContent', {
                    content_type: 'product',
                    content_ids: [String(data.id)],
                    content_name: data.name,
                    value: Number(data.price ?? 0),
                    currency: 'BDT',
                });
                break;
            }

            case 'view_item_list': {
                gtag('event', 'view_item_list', {
                    item_list_name: data.list_name ?? 'Products',
                    items: (data.items ?? []).map(toGAItem),
                });
                break;
            }

            case 'add_to_cart': {
                const item = toGAItem(data);
                const qty = Number(data.qty ?? 1);
                gtag('event', 'add_to_cart', {
                    currency: 'BDT',
                    value: Number(data.price ?? 0) * qty,
                    items: [item],
                });
                fbq('track', 'AddToCart', {
                    content_type: 'product',
                    content_ids: [String(data.id)],
                    content_name: data.name,
                    value: Number(data.price ?? 0) * qty,
                    currency: 'BDT',
                });
                break;
            }

            case 'remove_from_cart': {
                const item = toGAItem(data);
                gtag('event', 'remove_from_cart', {
                    currency: 'BDT',
                    value: Number(data.price ?? 0) * Number(data.qty ?? 1),
                    items: [item],
                });
                break;
            }

            case 'view_cart': {
                gtag('event', 'view_cart', {
                    currency: 'BDT',
                    value: Number(data.value ?? 0),
                    items: (data.items ?? []).map(toGAItem),
                });
                break;
            }

            case 'begin_checkout': {
                gtag('event', 'begin_checkout', {
                    currency: 'BDT',
                    value: Number(data.value ?? 0),
                    items: (data.items ?? []).map(toGAItem),
                });
                fbq('track', 'InitiateCheckout', {
                    content_type: 'product',
                    content_ids: (data.items ?? []).map(i => String(i.id)),
                    value: Number(data.value ?? 0),
                    currency: 'BDT',
                    num_items: (data.items ?? []).reduce((n, i) => n + Number(i.qty ?? 1), 0),
                });
                break;
            }

            case 'purchase': {
                gtag('event', 'purchase', {
                    transaction_id: String(data.order_number ?? ''),
                    value: Number(data.total ?? 0),
                    currency: 'BDT',
                    shipping: Number(data.shipping ?? 0),
                    coupon: data.coupon ?? undefined,
                    items: (data.items ?? []).map(toGAItem),
                });
                fbq('track', 'Purchase', {
                    content_type: 'product',
                    content_ids: (data.items ?? []).map(i => String(i.id)),
                    value: Number(data.total ?? 0),
                    currency: 'BDT',
                    num_items: (data.items ?? []).reduce((n, i) => n + Number(i.qty ?? 1), 0),
                });
                break;
            }

            case 'search': {
                gtag('event', 'search', { search_term: data.query ?? '' });
                fbq('track', 'Search', { search_string: data.query ?? '' });
                break;
            }

            case 'sign_up':
            case 'login':
                gtag('event', event, data);
                break;

            default:
                gtag('event', event, data);
        }
    };
})();
