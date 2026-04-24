export function initScrollReveal() {
    const reveals = document.querySelectorAll('.gsap-fade-up, .gsap-fade-left, .gsap-fade-right, .gsap-scale-in');

    reveals.forEach((el) => {
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
