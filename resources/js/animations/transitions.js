export function initPageTransitions(barba, gsap, ScrollTrigger) {
    barba.init({
        transitions: [{
            name: 'default-transition',
            leave(data) {
                return gsap.to(data.current.container, { opacity: 0, duration: 0.4 });
            },
            afterLeave() {
                ScrollTrigger.getAll().forEach(trigger => trigger.kill());
            },
            enter(data) {
                return gsap.from(data.next.container, { opacity: 0, duration: 0.4 });
            },
            afterEnter() {
                window.scrollTo(0, 0);
            },
        }],
    });
}
