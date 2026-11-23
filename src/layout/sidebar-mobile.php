<?php
// Sidebar for phones and tablets (a Bootstrap offcanvas, shown below the "lg" width).
// Included by src/layout/sidebar.php, which also sets the variables used below
// ($activeRoute, $componentsOpen, $dashboardOpen ...). Do not require this file on its own.
//
// KEEP THE MENU THE SAME AS THE DESKTOP MENU in sidebar.php. Only the collapse ids differ
// (...SubmenuMobile instead of ...SubmenuDesktop), because ids must be unique on a page.
?>
<div
    class="offcanvas offcanvas-start sidebar-offcanvas d-lg-none"
    tabindex="-1"
    id="mobileSidebar"
    aria-labelledby="mobileSidebarLabel">
    <div class="sidebar-brand justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <!-- Gantikan src di bawah dengan laluan aset lokal apabila dipindahkan ke PHP -->
            <img
                src="https://upload.wikimedia.org/wikipedia/commons/e/e9/Shah_Alam_Emblem.svg"
                alt="Logo Majlis Bandaraya Shah Alam">
            <div>
                <div class="sidebar-brand-title" id="mobileSidebarLabel">ISISA</div>
                <div class="sidebar-brand-subtitle">Majlis Bandaraya Shah Alam</div>
            </div>
        </div>
        <button
            type="button"
            class="btn-close shadow-none"
            data-bs-dismiss="offcanvas"
            aria-label="Tutup menu"></button>
    </div>

    <nav class="sidebar-scroll" aria-label="Menu Sisi Mudah Alih">
        <?php if (canSeeAnyMenu('guide')): ?>
        <div class="sidebar-section-title">GUIDE</div>
        <?php endif; ?>
        <?php if (canSeeMenu('guide')): ?>
        <a href="<?= e(routeUrl('guide')) ?>" class="nav-item-link<?= $activeRoute === 'guide' ? ' active' : '' ?>" <?= $activeRoute === 'guide' ? ' aria-current="page"' : '' ?>>
            <i class="bi bi-journal-text nav-icon" aria-hidden="true"></i>
            <span>README.md</span>
        </a>
        <?php endif; ?>

        <?php if (canSeeAnyMenu('components', 'dashboard')): ?>
        <div class="sidebar-section-title">UTAMA</div>
        <?php endif; ?>
        <?php if (canSeeMenu('components')): ?>
        <a
            class="nav-item-link"
            data-bs-toggle="collapse"
            href="#componentsSubmenuMobile"
            role="button"
            aria-expanded="<?= $componentsOpen ? 'true' : 'false' ?>"
            aria-controls="componentsSubmenuMobile">
            <i class="bi bi-layers nav-icon" aria-hidden="true"></i>
            <span>Components</span>
            <i class="bi bi-chevron-down nav-chevron" aria-hidden="true"></i>
        </a>

        <div class="collapse<?= $componentsOpen ? ' show' : '' ?>" id="componentsSubmenuMobile">
            <ul class="submenu-list">
                <li>
                    <a href="<?= e(routeUrl('components-main')) ?>" class="submenu-link<?= $activeRoute === 'components-main' ? ' active' : '' ?>" <?= $activeRoute === 'components-main' ? ' aria-current="page"' : '' ?>>Main</a>
                </li>
                <li>
                    <a href="<?= e(routeUrl('components-basic')) ?>" class="submenu-link<?= $activeRoute === 'components-basic' ? ' active' : '' ?>" <?= $activeRoute === 'components-basic' ? ' aria-current="page"' : '' ?>>Basic</a>
                </li>
                <li>
                    <a href="<?= e(routeUrl('components-form')) ?>" class="submenu-link<?= $activeRoute === 'components-form' ? ' active' : '' ?>" <?= $activeRoute === 'components-form' ? ' aria-current="page"' : '' ?>>Form</a>
                </li>
                <li>
                    <a href="<?= e(routeUrl('components-tables')) ?>" class="submenu-link<?= $activeRoute === 'components-tables' ? ' active' : '' ?>" <?= $activeRoute === 'components-tables' ? ' aria-current="page"' : '' ?>>Tables</a>
                </li>
                <li>
                    <a href="<?= e(routeUrl('components-cards')) ?>" class="submenu-link<?= $activeRoute === 'components-cards' ? ' active' : '' ?>" <?= $activeRoute === 'components-cards' ? ' aria-current="page"' : '' ?>>Cards</a>
                </li>
                <li>
                    <a href="<?= e(routeUrl('components-charts')) ?>" class="submenu-link<?= $activeRoute === 'components-charts' ? ' active' : '' ?>" <?= $activeRoute === 'components-charts' ? ' aria-current="page"' : '' ?>>Charts</a>
                </li>
                <li>
                    <a href="<?= e(routeUrl('components-alerts')) ?>" class="submenu-link<?= $activeRoute === 'components-alerts' ? ' active' : '' ?>" <?= $activeRoute === 'components-alerts' ? ' aria-current="page"' : '' ?>>Alerts</a>
                </li>
                <li>
                    <a href="<?= e(routeUrl('components-toastr')) ?>" class="submenu-link<?= $activeRoute === 'components-toastr' ? ' active' : '' ?>" <?= $activeRoute === 'components-toastr' ? ' aria-current="page"' : '' ?>>Toastr</a>
                </li>
            </ul>
        </div>
        <?php endif; ?>
        <?php if (canSeeMenu('dashboard')): ?>
        <a
            class="nav-item-link"
            data-bs-toggle="collapse"
            href="#dashboardSubmenuMobile"
            role="button"
            aria-expanded="<?= $dashboardOpen ? 'true' : 'false' ?>"
            aria-controls="dashboardSubmenuMobile">
            <i class="bi bi-grid nav-icon" aria-hidden="true"></i>
            <span>Dashboard</span>
            <i class="bi bi-chevron-down nav-chevron" aria-hidden="true"></i>
        </a>
        <div class="collapse<?= $dashboardOpen ? ' show' : '' ?>" id="dashboardSubmenuMobile">
            <ul class="submenu-list">
                <li><a href="<?= e(routeUrl('dashboard-executive')) ?>" class="submenu-link<?= $activeRoute === 'dashboard-executive' ? ' active' : '' ?>" <?= $activeRoute === 'dashboard-executive' ? ' aria-current="page"' : '' ?>>Eksekutif</a></li>
                <li><a href="<?= e(routeUrl('dashboard-kpi')) ?>" class="submenu-link<?= $activeRoute === 'dashboard-kpi' ? ' active' : '' ?>" <?= $activeRoute === 'dashboard-kpi' ? ' aria-current="page"' : '' ?>>KPI Dan Analitik</a></li>
                <li><a href="<?= e(routeUrl('dashboard-operation')) ?>" class="submenu-link<?= $activeRoute === 'dashboard-operation' ? ' active' : '' ?>" <?= $activeRoute === 'dashboard-operation' ? ' aria-current="page"' : '' ?>>Operasi Dan Data</a></li>
                <li><a href="<?= e(routeUrl('broadcast-program')) ?>" class="submenu-link<?= $activeRoute === 'broadcast-program' ? ' active' : '' ?>" <?= $activeRoute === 'broadcast-program' ? ' aria-current="page"' : '' ?>>Program Dan Aktiviti</a></li>
            </ul>
        </div>
        <?php endif; ?>


        <?php if (canSeeAnyMenu('wisma', 'role', 'users')): ?>
        <div class="sidebar-section-title">PENGURUSAN UTAMA</div>
        <?php endif; ?>
        <?php if (canSeeMenu('wisma')): ?>
        <a
            class="nav-item-link"
            data-bs-toggle="collapse"
            href="#wismaSubmenuMobile"
            role="button"
            aria-expanded="<?= $wismaOpen ? 'true' : 'false' ?>"
            aria-controls="wismaSubmenuMobile">
            <i class="bi bi-building nav-icon" aria-hidden="true"></i>
            <span>Wisma</span>
            <i class="bi bi-chevron-down nav-chevron" aria-hidden="true"></i>
        </a>

        <div class="collapse<?= $wismaOpen ? ' show' : '' ?>" id="wismaSubmenuMobile">
            <ul class="submenu-list">
                <li><a href="<?= e(routeUrl('wisma-department')) ?>" class="submenu-link<?= $activeRoute === 'wisma-department' ? ' active' : '' ?>" <?= $activeRoute === 'wisma-department' ? ' aria-current="page"' : '' ?>>Jabatan</a></li>
                <li><a href="<?= e(routeUrl('wisma-section')) ?>" class="submenu-link<?= $activeRoute === 'wisma-section' ? ' active' : '' ?>" <?= $activeRoute === 'wisma-section' ? ' aria-current="page"' : '' ?>>Seksyen</a></li>
                <li><a href="<?= e(routeUrl('wisma-division')) ?>" class="submenu-link<?= $activeRoute === 'wisma-division' ? ' active' : '' ?>" <?= $activeRoute === 'wisma-division' ? ' aria-current="page"' : '' ?>>Bahagian</a></li>
                <li><a href="<?= e(routeUrl('wisma-branch')) ?>" class="submenu-link<?= $activeRoute === 'wisma-branch' ? ' active' : '' ?>" <?= $activeRoute === 'wisma-branch' ? ' aria-current="page"' : '' ?>>Cawangan</a></li>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (canSeeMenu('role')): ?>
        <a
            class="nav-item-link"
            data-bs-toggle="collapse"
            href="#roleSubmenuMobile"
            role="button"
            aria-expanded="<?= $roleOpen ? 'true' : 'false' ?>"
            aria-controls="roleSubmenuMobile">
            <i class="bi bi-person nav-icon" aria-hidden="true"></i>
            <span>Peranan</span>
            <i class="bi bi-chevron-down nav-chevron" aria-hidden="true"></i>
        </a>

        <div class="collapse<?= $roleOpen ? ' show' : '' ?>" id="roleSubmenuMobile">
            <ul class="submenu-list">
                <li><a href="<?= e(routeUrl('role-role')) ?>" class="submenu-link<?= $activeRoute === 'role-role' ? ' active' : '' ?>" <?= $activeRoute === 'role-role' ? ' aria-current="page"' : '' ?>>Peranan</a></li>
                <li><a href="<?= e(routeUrl('role-type')) ?>" class="submenu-link<?= $activeRoute === 'role-type' ? ' active' : '' ?>" <?= $activeRoute === 'role-type' ? ' aria-current="page"' : '' ?>>Jenis Peranan</a></li>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (canSeeMenu('users')): ?>
        <a
            class="nav-item-link"
            data-bs-toggle="collapse"
            href="#usersSubmenuMobile"
            role="button"
            aria-expanded="<?= $usersOpen ? 'true' : 'false' ?>"
            aria-controls="usersSubmenuMobile">
            <i class="bi bi-people nav-icon" aria-hidden="true"></i>
            <span>Pengguna</span>
            <i class="bi bi-chevron-down nav-chevron" aria-hidden="true"></i>
        </a>

        <div class="collapse<?= $usersOpen ? ' show' : '' ?>" id="usersSubmenuMobile">
            <ul class="submenu-list">
                <li><a href="<?= e(routeUrl('users-users')) ?>" class="submenu-link<?= $activeRoute === 'users-users' ? ' active' : '' ?>" <?= $activeRoute === 'users-users' ? ' aria-current="page"' : '' ?>>Pengguna</a></li>
                <li><a href="<?= e(routeUrl('users-employee')) ?>" class="submenu-link<?= $activeRoute === 'users-employee' ? ' active' : '' ?>" <?= $activeRoute === 'users-employee' ? ' aria-current="page"' : '' ?>>Kakitangan</a></li>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (canSeeAnyMenu('helpdesk')): ?>
        <div class="sidebar-section-title">KHIDMAT BANTUAN</div>
        <?php endif; ?>
        <?php if (canSeeMenu('helpdesk')): ?>
        <a
            class="nav-item-link"
            data-bs-toggle="collapse"
            href="#mejabantuanSubmenuMobile"
            role="button"
            aria-expanded="<?= $mejabantuanOpen ? 'true' : 'false' ?>"
            aria-controls="mejabantuanSubmenuMobile">
            <i class="bi bi-headset nav-icon" aria-hidden="true"></i>
            <span>Meja Bantuan</span>
            <i class="bi bi-chevron-down nav-chevron" aria-hidden="true"></i>
        </a>

        <div class="collapse<?= $mejabantuanOpen ? ' show' : '' ?>" id="mejabantuanSubmenuMobile">
            <ul class="submenu-list">
                <li><a href="<?= e(routeUrl('meja-dashboard')) ?>" class="submenu-link<?= $activeRoute === 'meja-dashboard' ? ' active' : '' ?>" <?= $activeRoute === 'meja-dashboard' ? ' aria-current="page"' : '' ?>>Dashboard</a></li>
                <li><a href="<?= e(routeUrl('meja-list')) ?>" class="submenu-link<?= $activeRoute === 'meja-list' ? ' active' : '' ?>" <?= $activeRoute === 'meja-list' ? ' aria-current="page"' : '' ?>>Senarai Aduan</a></li>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (canSeeAnyMenu('announcement', 'broadcast-program')): ?>
        <div class="sidebar-section-title">HEBAHAN</div>
        <?php endif; ?>
        <?php if (canSeeMenu('announcement')): ?>
        <a href="<?= e(routeUrl('announcement')) ?>" class="nav-item-link<?= $activeRoute === 'announcement' ? ' active' : '' ?>" <?= $activeRoute === 'announcement' ? ' aria-current="page"' : '' ?>>
            <i class="bi bi-megaphone nav-icon" aria-hidden="true"></i>
            <span>Pengumuman</span>
        </a>
        <?php endif; ?>
        <?php if (canSeeMenu('broadcast-program')): ?>
        <a href="<?= e(routeUrl('broadcast-program')) ?>" class="nav-item-link<?= $activeRoute === 'broadcast-program' ? ' active' : '' ?>" <?= $activeRoute === 'broadcast-program' ? ' aria-current="page"' : '' ?>>
            <i class="bi bi-calendar-event nav-icon" aria-hidden="true"></i>
            <span>Program Dan Aktiviti</span>
        </a>
        <?php endif; ?>

        <?php if (canSeeAnyMenu('system-audit', 'system-info', 'system-backup', 'system-maintenance')): ?>
        <div class="sidebar-section-title">TETAPAN SISTEM</div>
        <?php endif; ?>
        <?php if (canSeeMenu('system-audit')): ?>
        <a href="<?= e(routeUrl('system-audit')) ?>" class="nav-item-link<?= $activeRoute === 'system-audit' ? ' active' : '' ?>" <?= $activeRoute === 'system-audit' ? ' aria-current="page"' : '' ?>>
            <i class="bi bi-bezier2 nav-icon" aria-hidden="true"></i>
            <span>Jejak Audit</span>
        </a>
        <?php endif; ?>
        <?php if (canSeeMenu('system-info')): ?>
        <a href="<?= e(routeUrl('system-info')) ?>" class="nav-item-link<?= $activeRoute === 'system-info' ? ' active' : '' ?>" <?= $activeRoute === 'system-info' ? ' aria-current="page"' : '' ?>>
            <i class="bi bi-shield nav-icon" aria-hidden="true"></i>
            <span>Info Sistem</span>
        </a>
        <?php endif; ?>
        <?php if (canSeeMenu('system-backup')): ?>
        <a href="<?= e(routeUrl('system-backup')) ?>" class="nav-item-link<?= $activeRoute === 'system-backup' ? ' active' : '' ?>" <?= $activeRoute === 'system-backup' ? ' aria-current="page"' : '' ?>>
            <i class="bi bi-database nav-icon" aria-hidden="true"></i>
            <span>Backup Data</span>
        </a>
        <?php endif; ?>
        <?php if (canSeeMenu('system-maintenance')): ?>
        <a href="<?= e(routeUrl('system-maintenance')) ?>" class="nav-item-link<?= $activeRoute === 'system-maintenance' ? ' active' : '' ?>" <?= $activeRoute === 'system-maintenance' ? ' aria-current="page"' : '' ?>>
            <i class="bi bi-gear nav-icon" aria-hidden="true"></i>
            <span>Penyelengaraan</span>
        </a>
        <?php endif; ?>

        <?php if (canSeeAnyMenu('reporting')): ?>
        <div class="sidebar-section-title">PELAPORAN</div>
        <?php endif; ?>
        <?php if (canSeeMenu('reporting')): ?>
        <a href="#" class="nav-item-link" aria-current="page">
            <i class="bi bi-graph-up nav-icon" aria-hidden="true"></i>
            <span>Pelaporan</span>
        </a>
        <?php endif; ?>
    </nav>
</div>
