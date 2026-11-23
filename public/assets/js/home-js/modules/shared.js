/**
 * Nilai & fungsi yang dikongsi oleh beberapa modul (kurang gerakan, format nombor).
 */
export const noMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function fmt(v) {
    return Math.round(v).toLocaleString('en-US');
}
