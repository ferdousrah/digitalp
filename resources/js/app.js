import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
gsap.registerPlugin(ScrollTrigger);
window.gsap = gsap;
window.ScrollTrigger = ScrollTrigger;

import barba from '@barba/core';

import { initScrollReveal } from './animations/scroll-reveal';
import { initHeroAnimations } from './animations/hero';
import { initPageTransitions } from './animations/transitions';
import { initSearch } from './components/search';

document.addEventListener('DOMContentLoaded', () => {
    initScrollReveal();
    initHeroAnimations();
    initSearch();
    initPageTransitions(barba, gsap, ScrollTrigger);
});
