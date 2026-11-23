/**
 * HOME JS - Halaman Utama / Landing (ISISA)
 * Fail masuk (entry point): mengimport dan memulakan semua modul.
 *
 * PENTING: muatkan dengan <script type="module" src=".../home-js/main.js"></script>
 * (selepas Bootstrap, Chart.js & GSAP).
 */
import { initSplashFlag } from './modules/splash-flag.js';
import { initCharts } from './modules/charts.js';
import { initAnimations } from './modules/animations.js';
import { initMobileMenu } from './modules/mobile-menu.js';
import { initBackToTop } from './modules/back-to-top.js';
import { initFacebookFeed } from './modules/facebook-feed.js';

initSplashFlag(); // mesti sebelum initAnimations(): animations.js membaca kelas splash-on
initCharts();
initAnimations();
initMobileMenu();
initBackToTop();
initFacebookFeed();
