<?php
// ---------------------------------------------------------------
// Header data: the logged-in user and the title of this page
// ---------------------------------------------------------------
$headerUser     = currentUser();                                        // null when nobody is logged in
$headerName     = userDisplayName();                                    // "Ahmad Faiz bin Ali"
$headerInitials = userInitials($headerUser['fullname'] ?? null);        // "AF"
$headerEmail    = (string) ($headerUser['email'] ?? '');
$headerRole     = (string) ($headerUser['ref_role_description'] ?? ''); // "Superadmin", "Admin"...
$headerTitle    = $pageTitle ?? pageTitle();                            // a page may set its own $pageTitle
?>
<header class="app-header">
    <!-- Kiri: Hamburger (Mobile) & Tajuk Halaman -->
    <div class="d-flex align-items-center gap-2 gap-md-3">
        <button class="header-btn d-lg-none px-2" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Buka menu navigasi">
            <i class="bi bi-list fs-5" aria-hidden="true"></i>
        </button>
        <h1 class="h6 mb-0 fw-semibold text-dark"><?= e($headerTitle) ?></h1>
    </div>

    <!-- Kanan: Dropdown Bahasa & Profil Pengguna -->
    <div class="d-flex align-items-center gap-2 gap-md-3">

        <!-- Dropdown Tukar Tema (warna sahaja, tiada fungsi): see src/components/header-theme-changer.php -->
        <?php require SRC_PATH . '/components/header-theme-changer.php'; ?>

        <!-- Dropdown Bahasa: see src/components/header-translation.php -->
        <?php require SRC_PATH . '/components/header-translation.php'; ?>

        <!-- Dropdown Profil Pengguna -->
        <div class="dropdown">
            <button class="profile-trigger" type="button" id="profileDropdownBtn" data-bs-toggle="dropdown"
                aria-expanded="false" aria-label="Menu Profil Pengguna">
                <!-- Inisial, nama dan peranan pengguna yang sedang log masuk (semua diescape dengan e()) -->
                <div class="avatar-circle"><?= e($headerInitials) ?></div>
                <div class="d-none d-sm-block text-start">
                    <!-- Nama dipaparkan HURUF BESAR (text-uppercase) dan dipotong dengan "..." jika terlalu panjang -->
                    <div class="fw-semibold text-dark text-uppercase text-truncate" style="font-size: 0.835rem; line-height: 1.2; max-width: 16rem;"><?= e($headerName) ?></div>
                    <div class="text-muted" style="font-size: 0.72rem;"><?= e($headerRole) ?></div>
                </div>
                <i class="bi bi-chevron-down text-muted small d-none d-sm-inline" aria-hidden="true"></i>
            </button>

            <!-- profile-menu: lebar menu sama dengan butang profil di atasnya (lihat header.css) -->
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom profile-menu" aria-labelledby="profileDropdownBtn">
                <li class="px-3 py-2 border-bottom mb-1">
                    <!-- Nama dan e-mel pengguna yang sedang log masuk -->
                    <div class="fw-semibold text-dark text-uppercase text-truncate" style="font-size: 0.85rem;" title="<?= e($headerName) ?>"><?= e($headerName) ?></div>
                    <div class="text-muted text-truncate" style="font-size: 0.75rem;" title="<?= e($headerEmail) ?>"><?= e($headerEmail) ?></div>
                </li>
                <li>
                    <a class="dropdown-item<?= currentRoute() === 'meja-list' ? ' active' : '' ?>" href="<?= e(routeUrl('meja-list')) ?>">
                        <i class="bi bi-question-circle" aria-hidden="true"></i>
                        <span>Bantuan</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item<?= currentRoute() === 'notification' ? ' active' : '' ?>" href="<?= e(routeUrl('notification')) ?>">
                        <i class="bi bi-bell" aria-hidden="true"></i>
                        <span>Notifikasi</span>
                        <span class="notif-count ms-auto" aria-label="10 notifikasi">10</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item<?= currentRoute() === 'accounts' ? ' active' : '' ?>" href="<?= e(routeUrl('accounts')) ?>">
                        <i class="bi bi-person" aria-hidden="true"></i>
                        <span>Akaun Saya</span>
                    </a>
                </li>


                <li>
                    <hr class="dropdown-divider my-1">
                </li>
                <li>
                    <a class="dropdown-item" href="<?= e(routeUrl('lock-screen')) ?>" data-confirm="lock">
                        <i class="bi bi-lock" aria-hidden="true"></i>
                        <span>Kunci Skrin</span>
                    </a>
                </li>
                <li>
                    <!-- KESELAMATAN: Log keluar menggunakan borang POST bersama token CSRF (borang #logoutForm di bawah),
                         bukan pautan GET. Popup "Log Keluar?" (confirm-dialogs.js) hanya meminta pengesahan,
                         dan menghantar borang itu bila pengguna menekan "Ya". -->
                    <a class="dropdown-item dropdown-item-logout" href="#logout" data-confirm="logout">
                        <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                        <span>Log Keluar</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- Borang Log Keluar (disembunyikan). Dihantar oleh popup "Log Keluar?" dalam confirm-dialogs.js.
     Mesti POST + token CSRF; pelayan (app/auth/Logout/LogoutController.php) yang melaksanakan log keluar. -->
<form id="logoutForm" method="post" action="<?= e(routeUrl('logout')) ?>" hidden>
    <?= csrfField() ?>
</form>
