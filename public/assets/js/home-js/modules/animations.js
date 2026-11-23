/**
 * Animasi (GSAP): urutan hero, kaunter angka, splash screen dan kad dashboard.
 * Peningkatan progresif: tanpa GSAP / dengan kurang gerakan, kandungan terus dipaparkan.
 */
import { noMotion, fmt } from './shared.js';

    /* ---------- Animasi (GSAP) ----------
       Peningkatan progresif: jika GSAP gagal dimuat atau pengguna memilih kurang gerakan,
       angka terus dipaparkan pada nilai akhir dan semua kandungan kekal kelihatan. */
export function initAnimations() {
    var counters = document.querySelectorAll('[data-count]');


    function showFinal() {
        counters.forEach(function (c) {
            c.textContent = fmt(Number(c.getAttribute('data-count')));
        });
    }

    var splash = document.getElementById('splash');

    function endSplash() {
        document.documentElement.classList.remove('splash-on');
        if (splash && splash.parentNode) splash.parentNode.removeChild(splash);
        try {
            sessionStorage.setItem('isisa-splash', '1');
        } catch (e) { }
    }

    if (!window.gsap || noMotion) {
        endSplash();
        showFinal();
    } else {
        document.documentElement.classList.add('js-gsap');
        gsap.defaults({
            ease: 'power3.out'
        });

        /* Satu urutan masuk di hero */
        var showSplash = document.documentElement.classList.contains('splash-on');
        var tl = gsap.timeline({
            paused: showSplash
        });
        tl.from('.hero .pill', {
            y: 14,
            opacity: 0,
            duration: .5,
            clearProps: 'opacity,transform'
        })
            .from('.hero h1', {
                y: 26,
                opacity: 0,
                duration: .75,
                clearProps: 'opacity,transform'
            }, '-=.3')
            .from('.hero .lead, .hero .btn', {
                y: 16,
                opacity: 0,
                duration: .55,
                stagger: .08,
                clearProps: 'opacity,transform'
            }, '-=.45')
            .from('.deck-main', {
                y: 44,
                opacity: 0,
                duration: .85,
                clearProps: 'opacity,transform'
            }, .25)
            .from('.deck-layer', {
                x: 0,
                y: 0,
                opacity: 0,
                duration: .8,
                stagger: .12
            }, '-=.45')
            .from('.bars i', {
                scaleY: 0,
                transformOrigin: 'bottom',
                duration: .7,
                stagger: .045,
                clearProps: 'transform'
            }, '-=.5')
            .from('.donut', {
                rotation: -140,
                scale: .7,
                opacity: 0,
                duration: .9,
                clearProps: 'opacity,transform'
            }, '<');

        counters.forEach(function (c) {
            var to = Number(c.getAttribute('data-count')),
                o = {
                    v: 0
                };
            c.textContent = fmt(0);
            tl.to(o, {
                v: to,
                duration: 1.3,
                ease: 'power2.out',
                onUpdate: function () {
                    c.textContent = fmt(o.v);
                },
                onComplete: function () {
                    c.textContent = fmt(to);
                }
            }, .7);
        });

        /* Splash: logo masuk, bar mengisi, kemudian panel terangkat dan urutan hero bermula */
        if (showSplash && splash) {
            var sp = gsap.timeline({
                onComplete: endSplash
            });
            sp.from('.splash-mark', {
                scale: .6,
                opacity: 0,
                duration: .6,
                ease: 'back.out(1.6)'
            })
                .from('.splash-name, .splash-sub', {
                    y: 12,
                    opacity: 0,
                    duration: .5,
                    stagger: .1
                }, '-=.3')
                .to('.splash-bar i', {
                    scaleX: 1,
                    duration: 1,
                    ease: 'power1.inOut'
                }, '-=.4')
                .to('.splash-inner', {
                    y: -14,
                    opacity: 0,
                    duration: .35,
                    ease: 'power2.in'
                }, '+=.15')
                .to(splash, {
                    yPercent: -100,
                    duration: .75,
                    ease: 'power3.inOut'
                }, '-=.1')
                .add(function () {
                    tl.play();
                }, '-=.4');
            setTimeout(function () {
                if (document.getElementById('splash')) {
                    sp.progress(1);
                    tl.play();
                }
            }, 6000);
        }

        /* Dashboard: kad naik perlahan apabila dipaparkan, dan setiap kali tab ditukar */
        var reveal = function (pane) {
            if (!pane) return;
            gsap.from(pane.querySelectorAll('.dash-intro, .kpi-tile, .cc'), {
                y: 18,
                opacity: 0,
                duration: .5,
                stagger: .06,
                ease: 'power2.out',
                clearProps: 'opacity,transform',
                overwrite: 'auto'
            });
        };
        var first = document.getElementById('p-eks');
        if ('IntersectionObserver' in window && first) {
            var io = new IntersectionObserver(function (es) {
                if (es[0].isIntersecting) {
                    io.disconnect();
                    reveal(first);
                }
            }, {
                threshold: .15
            });
            io.observe(first);
        }
        document.querySelectorAll('#dashTabs [data-bs-toggle="tab"]').forEach(function (t) {
            t.addEventListener('shown.bs.tab', function () {
                reveal(document.querySelector(t.getAttribute('data-bs-target')));
            });
        });
    }
}
