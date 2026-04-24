export function initHeroAnimations() {
    const hero = document.querySelector('.hero-section');
    if (!hero) return;

    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });

    tl.from('.hero-title', { opacity: 0, y: 60, duration: 1 })
      .from('.hero-subtitle', { opacity: 0, y: 40, duration: 0.8 }, '-=0.5')
      .from('.hero-cta', { opacity: 0, y: 30, duration: 0.6 }, '-=0.4')
      .from('.hero-image', { opacity: 0, scale: 0.9, duration: 1 }, '-=0.6');
}
