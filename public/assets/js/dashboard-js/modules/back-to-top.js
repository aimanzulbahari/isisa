/**
 * Butang kembali ke atas: muncul selepas skrol 300px
 */
export function initBackToTop() {
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
        const toggleBackToTop = () => backToTop.classList.toggle('show', window.scrollY > 300);
        window.addEventListener('scroll', toggleBackToTop, {
            passive: true
        });
        toggleBackToTop();
        backToTop.addEventListener('click', () => {
            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            window.scrollTo({
                top: 0,
                behavior: reduce ? 'auto' : 'smooth'
            });
        });
    }
}
