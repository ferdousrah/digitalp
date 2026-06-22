export function initScrollReveal() {
    // Bail out entirely for users who prefer reduced motion — elements remain in
    // their natural CSS state (visible, no transform). No JS animation runs at all.
    const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion) return;

    // This bundle is loaded lazily (on idle), so by the time it runs some elements may
    // already be on screen. Animating those would hide-then-reveal already-visible
    // content — a flicker. Skip anything currently in the viewport; it stays as-is.
    const alreadyVisible = (el) => {
        const r = el.getBoundingClientRect();
        return r.top < window.innerHeight && r.bottom > 0;
    };

    const reveals = document.querySelectorAll('.gsap-fade-up, .gsap-fade-left, .gsap-fade-right, .gsap-scale-in');

    reveals.forEach((el) => {
        if (alreadyVisible(el)) return;

        let fromVars = { opacity: 0, duration: 0.8, ease: 'power2.out' };
        let toVars = { opacity: 1 };

        if (el.classList.contains('gsap-fade-up')) {
            fromVars.y = 40;
            toVars.y = 0;
        } else if (el.classList.contains('gsap-fade-left')) {
            fromVars.x = -40;
            toVars.x = 0;
        } else if (el.classList.contains('gsap-fade-right')) {
            fromVars.x = 40;
            toVars.x = 0;
        } else if (el.classList.contains('gsap-scale-in')) {
            fromVars.scale = 0.8;
            toVars.scale = 1;
        }

        gsap.fromTo(el, fromVars, {
            ...toVars,
            duration: 0.8,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: el,
                start: 'top 85%',
                toggleActions: 'play none none none',
            },
        });
    });

    // Stagger grid animations — cards appear one by one
    const staggerGrids = document.querySelectorAll('.gsap-stagger-grid');

    staggerGrids.forEach((grid) => {
        const items = grid.querySelectorAll('.gsap-stagger-item');
        if (items.length === 0) return;
        if (alreadyVisible(grid)) return; // leave on-screen grids untouched (no flicker)

        gsap.fromTo(items,
            { opacity: 0, y: 50, scale: 0.95 },
            {
                opacity: 1,
                y: 0,
                scale: 1,
                duration: 0.6,
                ease: 'power3.out',
                stagger: 0.12,
                scrollTrigger: {
                    trigger: grid,
                    start: 'top 85%',
                    toggleActions: 'play none none none',
                },
            }
        );
    });
}
