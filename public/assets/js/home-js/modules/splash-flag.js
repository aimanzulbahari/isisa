/**
 * Splash screen: tentukan sama ada splash perlu dipaparkan.
 *
 * Menambah kelas "splash-on" pada <html> (CSS dalam components/splash.css memaparkan splash hanya
 * bila kelas ini ada). Splash TIDAK dipaparkan jika:
 *   - pengguna memilih kurang gerakan (prefers-reduced-motion), atau
 *   - splash sudah dipaparkan dalam sesi ini (kunci "isisa-splash" dalam sessionStorage,
 *     ditetapkan oleh endSplash() dalam modules/animations.js).
 *
 * PENTING: mesti dipanggil SEBELUM initAnimations(), kerana animations.js membaca kelas ini.
 */
import { noMotion } from './shared.js';

export function initSplashFlag() {
    try {
        if (noMotion) return;
        if (sessionStorage.getItem('isisa-splash')) return;
    } catch (e) { }
    document.documentElement.classList.add('splash-on');
}
