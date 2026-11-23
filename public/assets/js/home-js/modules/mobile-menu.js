/**
 * Tutup menu mudah alih selepas pautan diklik.
 */
export function initMobileMenu() {
    var menu = document.getElementById('navMenu');
    document.querySelectorAll('#navMenu a[href^="#"]').forEach(function (a) {
        a.addEventListener('click', function () {
            if (menu.classList.contains('show')) bootstrap.Collapse.getOrCreateInstance(menu).hide();
        });
    });
}
