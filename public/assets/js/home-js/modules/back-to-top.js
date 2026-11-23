/**
 * Butang kembali ke atas: dipaparkan selepas skrol 700px.
 */
export function initBackToTop() {
    var toTop = document.getElementById('toTop');
    var ticking = false;
    window.addEventListener('scroll', function () {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(function () {
            toTop.classList.toggle('show', window.scrollY > 700);
            ticking = false;
        });
    }, {
        passive: true
    });
}
