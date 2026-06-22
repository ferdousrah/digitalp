import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import { initSearch } from './components/search';

// Alpine powers interactive chrome (mobile drawer, dropdowns, checkout, cart) — it must
// be available immediately, so it stays in the main bundle.
Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initSearch();
});

// GSAP + ScrollTrigger + Barba are the bulk of the JS weight and are purely
// progressive (scroll-reveal animations + page-transition cross-fades). Load them in
// a separate chunk once the browser is idle, so they don't block first paint or inflate
// Total Blocking Time on the initial load. Until then the page is fully usable — links
// just navigate normally and content shows in its natural (un-animated) state.
const startAnimations = () =>
    import('./animations/index.js').then((m) => m.initAnimations()).catch(() => {});

if ('requestIdleCallback' in window) {
    requestIdleCallback(startAnimations, { timeout: 3000 });
} else {
    window.addEventListener('load', () => setTimeout(startAnimations, 300));
}
