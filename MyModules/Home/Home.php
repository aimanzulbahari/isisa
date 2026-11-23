<?php
declare(strict_types=1);

// The router (index.php) already loaded the system; require_once makes this safe if the file is opened another way.
require_once dirname(__DIR__, 2) . '/core/config/bootstrap.php';

// Library loaders for the third-party fonts, CSS and JS in /public (public/library)
require_once PUBLIC_PATH . '/library/font-library.php';
require_once PUBLIC_PATH . '/library/css-library.php';
require_once PUBLIC_PATH . '/library/js-library.php';

// "Log masuk" buttons: go to the Login page (app/auth/Login/Login.php).
// A visitor who is already logged in is sent to the dashboard instead.
$loggedIn  = isLoggedIn();
$loginHref = routeUrl($loggedIn ? 'dashboard' : 'login');
$loginText = $loggedIn ? 'Ke dashboard' : 'Log masuk';

// Topbar: maklumat hubungan dan pautan media sosial.
// TODO: gantikan '#' dengan URL media sosial rasmi MBSA (pautan '#' hanya tempat letak).
$topbarPhone  = ['label' => '03-55105133', 'href' => 'tel:+60355105133'];
$topbarEmail  = 'mbsa@mbsa.gov.my';
$socialLinks  = [
    ['label' => 'Facebook',  'icon' => 'bi-facebook',  'url' => '#'],
    ['label' => 'X',         'icon' => 'bi-twitter-x', 'url' => '#'],
    ['label' => 'Instagram', 'icon' => 'bi-instagram', 'url' => '#'],
    ['label' => 'YouTube',   'icon' => 'bi-youtube',   'url' => '#'],
];

// Seksyen "Program Akan Datang". Belum ada data: isi $programs (atau baca daripada pangkalan data kemudian).
// Program yang tarikhnya sudah lepas tidak dipaparkan. Format setiap baris:
//   ['date' => '2026-12-01', 'tag' => 'Bengkel', 'tone' => 'amber', 'featured' => false, 'featured_label' => '',
//    'title' => '...', 'time' => '09:00 AM', 'place' => '...', 'desc' => '...']
// 'tone' : warna label (blue | green | amber), 'featured' : kad ditonjolkan (guna 'featured_label').
$programs = [];
$monthShort = ['', 'JAN', 'FEB', 'MAC', 'APR', 'MEI', 'JUN', 'JUL', 'OGO', 'SEP', 'OKT', 'NOV', 'DIS'];
$today      = new DateTimeImmutable('today');
$upcoming   = [];
foreach ($programs as $program) {
    $date = new DateTimeImmutable($program['date']);
    if ($date < $today) {
        continue; // sudah lepas
    }
    $days = (int) $today->diff($date)->days;
    $program['day']    = $date->format('d');
    $program['month']  = $monthShort[(int) $date->format('n')];
    $program['year']   = $date->format('Y');
    $program['today']  = $days === 0;
    $program['status'] = $days === 0 ? 'Hari Ini' : 'Baki ' . $days . ' Hari';
    $upcoming[] = $program;
}

// Kad berita Facebook (Facebook Page Plugin).
// TODO: isi URL halaman Facebook rasmi MBSA, contoh: https://www.facebook.com/<nama-halaman>
// Selagi kosong, kad hanya memaparkan mesej tempat letak.
$facebookPageUrl = '';

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ISISA | UTAMA</title>
    <meta name="description" content="ISISA ialah sistem Majlis Bandaraya Shah Alam untuk memantau dan mengurus data sisa pepejal serta ekonomi kitaran, dengan data yang disahkan, dashboard dan laporan rasmi.">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%23071426'/%3E%3Ccircle cx='16' cy='16' r='8' fill='none' stroke='%237AB82E' stroke-width='3'/%3E%3Ccircle cx='16' cy='16' r='2.5' fill='%237AB82E'/%3E%3C/svg%3E">
    <!-- Font: Plus Jakarta Sans (local file, see public/library/font-library.php) -->
    <?= fontLibrary('auth') ?>
    <!-- Libraries: Bootstrap, Bootstrap Icons (see public/library/css-library.php) -->
    <?= cssLibrary(['bootstrap', 'bootstrap-icons']) ?>
    <!-- Gaya halaman Utama: mesti SELEPAS Bootstrap supaya boleh menimpa (see public/assets/css/home-css/main.css) -->
    <link rel="stylesheet" href="<?= e(asset('assets/css/home-css/main.css', true)) ?>">
</head>

<body data-bs-spy="scroll" data-bs-target="#siteNav" data-bs-root-margin="0px 0px -65%" tabindex="0">

    <div class="splash on-dark" id="splash" aria-hidden="true">
        <div class="splash-inner">
            <span class="splash-mark"><i class="bi bi-recycle"></i></span>
            <span class="splash-name">ISISA</span>
            <span class="splash-sub">Majlis Bandaraya Shah Alam</span>
            <span class="splash-bar"><i></i></span>
        </div>
    </div>

    <a class="skip" href="#kandungan">Langkau ke kandungan</a>

    <!-- Topbar: hubungi & pautan sosial (see public/assets/css/home-css/layout/topbar.css) -->
    <div class="topbar">
        <div class="container">
            <ul class="topbar-contact" aria-label="Maklumat hubungan">
                <li><a href="<?= e($topbarPhone['href']) ?>"><i class="bi bi-telephone" aria-hidden="true"></i><span class="topbar-text"><span class="visually-hidden">Tel: </span><?= e($topbarPhone['label']) ?></span></a></li>
                <li><a href="mailto:<?= e($topbarEmail) ?>"><i class="bi bi-envelope" aria-hidden="true"></i><span class="topbar-text"><span class="visually-hidden">Emel: </span><?= e($topbarEmail) ?></span></a></li>
            </ul>
            <ul class="topbar-social" aria-label="Media sosial">
                <?php foreach ($socialLinks as $social): ?>
                    <li><a href="<?= e($social['url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($social['label']) ?> (tab baharu)" title="<?= e($social['label']) ?>"><i class="bi <?= e($social['icon']) ?>" aria-hidden="true"></i></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg sticky-top site-nav" aria-label="Navigasi utama">
        <div class="container">
            <a class="brand" href="#atas" aria-label="ISISA, kembali ke atas">
                <span class="brand-mark" aria-hidden="true"><i class="bi bi-recycle"></i></span>
                <span>
                    <span class="brand-name d-block">ISISA</span>
                    <span class="brand-sub">Majlis Bandaraya Shah Alam</span>
                </span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto align-items-lg-center" id="siteNav">
                    <li class="nav-item"><a class="nav-link" href="#ciri">Ciri-ciri</a></li>
                    <li class="nav-item"><a class="nav-link" href="#dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="#cara">Cara berfungsi</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pengguna">Pengguna</a></li>
                    <li class="nav-item"><a class="nav-link" href="#faq">Soalan lazim</a></li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0"><a class="btn btn-isisa btn-sm" href="<?= e($loginHref) ?>"><i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i><?= e($loginText) ?></a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main id="kandungan">

        <?php require SRC_PATH . '/home-components/hero.php'; ?>

        <?php require SRC_PATH . '/home-components/stats.php'; ?>

        <?php require SRC_PATH . '/home-components/features.php'; ?>

        <?php require SRC_PATH . '/home-components/dashboard.php'; ?>

        <?php require SRC_PATH . '/home-components/how-it-works.php'; ?>

        <?php require SRC_PATH . '/home-components/users.php'; ?>

        <?php require SRC_PATH . '/home-components/faq.php'; ?>

        <?php require SRC_PATH . '/home-components/programs.php'; ?>

        <?php require SRC_PATH . '/home-components/closing.php'; ?>

    </main>

    <footer class="site-foot on-dark" id="hubungi">
        <div class="container">
            <div class="row gy-5 gx-lg-5">
                <div class="col-lg-5">
                    <a class="brand mb-3" href="#atas">
                        <span class="brand-mark" aria-hidden="true"><i class="bi bi-recycle"></i></span>
                        <span class="brand-name">ISISA</span>
                    </a>
                    <p class="foot-lead mb-0">Platform bersepadu pengurusan data sisa pepejal dan ekonomi kitaran untuk MBSA di Shah Alam.</p>
                </div>
                <div class="col-md-6 col-lg-4">
                    <h2>Hubungi kami</h2>
                    <address>
                        <strong>Jabatan Kebersihan Awam dan Kelestarian Sisa</strong>
                        Majlis Bandaraya Shah Alam<br>
                        Tingkat 4 Wisma MBSA, Persiaran Perbandaran,<br>
                        Seksyen 14, 40000 Shah Alam, Selangor
                    </address>
                    <ul class="contact">
                        <li><i class="bi bi-telephone" aria-hidden="true"></i><a href="tel:+60355196402">+603-5519 6402</a></li>
                        <li><i class="bi bi-printer" aria-hidden="true"></i>Faks: +603-5512 7014</li>
                    </ul>
                </div>
                <div class="col-md-6 col-lg-3">
                    <h2>Dalam halaman ini</h2>
                    <ul class="foot-links">
                        <li><a href="#ciri">Ciri-ciri</a></li>
                        <li><a href="#dashboard">Dashboard</a></li>
                        <li><a href="#cara">Cara berfungsi</a></li>
                        <li><a href="#faq">Soalan lazim</a></li>
                    </ul>
                </div>
            </div>
            <p class="foot-bottom mb-0">&copy; 2026 Majlis Bandaraya Shah Alam. Paparan dashboard pada halaman ini ialah contoh, bukan data sebenar.</p>
        </div>
    </footer>

    <a class="to-top" id="toTop" href="#atas" aria-label="Kembali ke atas"><i class="bi bi-arrow-up" aria-hidden="true"></i></a>

    <!-- Libraries: Bootstrap, Chart.js, GSAP (see public/library/js-library.php) -->
    <?= jsLibrary(['bootstrap', 'chart', 'gsap']) ?>
    <!-- Skrip halaman Utama: type="module" kerana main.js mengimport modulnya. Mesti SELEPAS Bootstrap, Chart.js & GSAP. -->
    <script type="module" src="<?= e(asset('assets/js/home-js/main.js', true)) ?>"></script>

</body>

</html>