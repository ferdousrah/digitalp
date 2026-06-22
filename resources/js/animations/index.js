// Deferred animation bundle — code-split out of the main app.js so GSAP + ScrollTrigger
// + Barba only download/parse once the browser is idle (see app.js). Loading them here
// keeps them out of the critical path and off the initial Total Blocking Time.
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import barba from '@barba/core';

import { initScrollReveal } from './scroll-reveal';
import { initPageTransitions } from './transitions';

export function initAnimations() {
    gsap.registerPlugin(ScrollTrigger);
    window.gsap = gsap;
    window.ScrollTrigger = ScrollTrigger;

    initScrollReveal();
    initPageTransitions(barba, gsap, ScrollTrigger);
}
