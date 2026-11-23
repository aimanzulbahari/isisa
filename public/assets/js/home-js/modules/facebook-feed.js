/**
 * Kad berita Facebook (Facebook Page Plugin).
 *
 * Elemen: <div class="fb-card" data-fb-page="URL halaman Facebook"> dengan <div data-fb-frame> di dalamnya.
 * iframe hanya dibina apabila kad hampir kelihatan (supaya Facebook tidak dimuatkan jika pelawat
 * tidak skrol ke seksyen ini), dan lebarnya dilaraskan mengikut lebar kad (180px - 500px, had Facebook).
 * Jika data-fb-page tiada (URL belum ditetapkan), tiada apa-apa yang dimuatkan.
 */
const MIN_WIDTH = 180;
const MAX_WIDTH = 500;
const HEIGHT = 600;

function buildSrc(pageUrl, width) {
    const params = new URLSearchParams({
        href: pageUrl,
        tabs: 'timeline',
        width: String(width),
        height: String(HEIGHT),
        small_header: 'false',
        adapt_container_width: 'true',
        hide_cover: 'false',
        show_facepile: 'false',
    });
    return 'https://www.facebook.com/plugins/page.php?' + params.toString();
}

export function initFacebookFeed() {
    const card = document.querySelector('.fb-card[data-fb-page]');
    if (!card) return;

    const frame = card.querySelector('[data-fb-frame]');
    const pageUrl = card.getAttribute('data-fb-page');
    if (!frame || !pageUrl) return;

    let iframe = null;
    let lastWidth = 0;

    function cardWidth() {
        const w = Math.floor(frame.clientWidth || card.clientWidth);
        return Math.max(MIN_WIDTH, Math.min(MAX_WIDTH, w));
    }

    function render() {
        const width = cardWidth();
        if (iframe && Math.abs(width - lastWidth) < 40) return; // elak muat semula untuk perubahan kecil
        lastWidth = width;

        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.title = 'Berita dan aktiviti daripada halaman Facebook rasmi';
            iframe.setAttribute('loading', 'lazy');
            iframe.setAttribute('allow', 'encrypted-media');
            iframe.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
            iframe.height = String(HEIGHT);
            frame.textContent = '';
            frame.appendChild(iframe);
        }
        iframe.width = String(width);
        iframe.src = buildSrc(pageUrl, width);
    }

    function start() {
        render();
        let timer = null;
        window.addEventListener('resize', function () {
            clearTimeout(timer);
            timer = setTimeout(render, 250);
        });
    }

    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver(function (entries) {
            if (entries[0].isIntersecting) {
                io.disconnect();
                start();
            }
        }, { rootMargin: '300px 0px' });
        io.observe(card);
    } else {
        start();
    }
}
