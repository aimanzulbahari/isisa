<?php
// ---------------------------------------------------------------
// Menu state: which page is open now (set by the router)
// ---------------------------------------------------------------
$activeRoute = currentRoute();

// Open the "Components" group when one of its pages is open (route names start with "components-").
$componentsOpen = str_starts_with((string) $activeRoute, 'components-');

// Open a Management group when one of its pages is open (route names start with the group: wisma- / role- / users-).
$wismaOpen = str_starts_with((string) $activeRoute, 'wisma-');
$mejabantuanOpen = str_starts_with((string) $activeRoute, 'meja-');
$roleOpen = str_starts_with((string) $activeRoute, 'role-');
$usersOpen = str_starts_with((string) $activeRoute, 'users-');

// Open the "Dashboard" group when one of its pages is open (route names start with "dashboard-").
$dashboardOpen = str_starts_with((string) $activeRoute, 'dashboard-');
?>
<aside class="sidebar-desktop d-none d-lg-flex" aria-label="Navigasi Utama">
    <a href="<?= e(routeUrl('dashboard')) ?>" class="sidebar-brand">
        <!-- Gantikan src di bawah dengan laluan aset lokal apabila dipindahkan ke PHP (cth: assets/img/logo-mbsa.svg) -->
        <img
            src="https://upload.wikimedia.org/wikipedia/commons/e/e9/Shah_Alam_Emblem.svg"
            alt="Logo Majlis Bandaraya Shah Alam">
        <div>
            <div class="sidebar-brand-title">ISISA</div>
            <div class="sidebar-brand-subtitle">Majlis Bandaraya Shah Alam</div>
        </div>
    </a>

    <nav class="sidebar-scroll" aria-label="Menu Sisi">
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
                href="#componentsSubmenuDesktop"
                role="button"
                aria-expanded="<?= $componentsOpen ? 'true' : 'false' ?>"
                aria-controls="componentsSubmenuDesktop">
                <i class="bi bi-layers nav-icon" aria-hidden="true"></i>
                <span>Components</span>
                <i class="bi bi-chevron-down nav-chevron" aria-hidden="true"></i>
            </a>

            <div class="collapse<?= $componentsOpen ? ' show' : '' ?>" id="componentsSubmenuDesktop">
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
                href="#dashboardSubmenuDesktop"
                role="button"
                aria-expanded="<?= $dashboardOpen ? 'true' : 'false' ?>"
                aria-controls="dashboardSubmenuDesktop">
                <i class="bi bi-grid nav-icon" aria-hidden="true"></i>
                <span>Dashboard</span>
                <i class="bi bi-chevron-down nav-chevron" aria-hidden="true"></i>
            </a>
            <div class="collapse<?= $dashboardOpen ? ' show' : '' ?>" id="dashboardSubmenuDesktop">
                <ul class="submenu-list">
                    <li><a href="<?= e(routeUrl('dashboard-executive')) ?>" class="submenu-link<?= $activeRoute === 'dashboard-executive' ? ' active' : '' ?>" <?= $activeRoute === 'dashboard-executive' ? ' aria-current="page"' : '' ?>>Eksekutif</a></li>
                    <li><a href="<?= e(routeUrl('dashboard-kpi')) ?>" class="submenu-link<?= $activeRoute === 'dashboard-kpi' ? ' active' : '' ?>" <?= $activeRoute === 'dashboard-kpi' ? ' aria-current="page"' : '' ?>>KPI Dan Analitik</a></li>
                    <li><a href="<?= e(routeUrl('dashboard-operation')) ?>" class="submenu-link<?= $activeRoute === 'dashboard-operation' ? ' active' : '' ?>" <?= $activeRoute === 'dashboard-operation' ? ' aria-current="page"' : '' ?>>Operasi Dan Data</a></li>
                    <li><a href="<?= e(routeUrl('dashboard-program')) ?>" class="submenu-link<?= $activeRoute === 'dashboard-program' ? ' active' : '' ?>" <?= $activeRoute === 'dashboard-program' ? ' aria-current="page"' : '' ?>>Program Dan Aktiviti</a></li>
                </ul>
            </div>
        <?php endif; ?>


        <?php if (canSeeAnyMenu('waste-food', 'waste-industrial', 'waste-recycling', 'waste-contractor')): ?>
            <div class="sidebar-section-title">PENGURUSAN SISA</div>
        <?php endif; ?>
        <?php if (canSeeMenu('waste-industrial')): ?>
            <!-- PHP: Gantikan href dengan laluan modul sebenar apabila halaman dibina (MyModules/Waste-Management) -->
            <a href="#" class="nav-item-link">
                <i class="bi bi-buildings nav-icon" aria-hidden="true"></i>
                <span>Sisa Industri</span>
            </a>
        <?php endif; ?>
        <?php if (canSeeMenu('waste-recycling')): ?>
            <!-- PHP: Gantikan href dengan laluan modul sebenar apabila halaman dibina (MyModules/Waste-Management) -->
            <a href="#" class="nav-item-link">
                <i class="bi bi-recycle nav-icon" aria-hidden="true"></i>
                <span>Kitar Semula</span>
            </a>
        <?php endif; ?>
        <?php if (canSeeMenu('waste-food')): ?>
            <!-- PHP: Gantikan href dengan laluan modul sebenar apabila halaman dibina (MyModules/Waste-Management) -->
            <a href="#" class="nav-item-link">
                <i class="bi bi-basket nav-icon" aria-hidden="true"></i>
                <span>Sisa Makanan</span>
            </a>
        <?php endif; ?>
        <?php if (canSeeMenu('waste-contractor')): ?>
            <!-- PHP: Gantikan href dengan laluan modul sebenar apabila halaman dibina (MyModules/Waste-Management) -->
            <a href="#" class="nav-item-link">
                <i class="bi bi-truck nav-icon" aria-hidden="true"></i>
                <span>Operasi-Kontraktor</span>
            </a>
        <?php endif; ?>

        <?php if (canSeeAnyMenu('wisma', 'role', 'users')): ?>
            <div class="sidebar-section-title">PENGURUSAN UTAMA</div>
        <?php endif; ?>
        <?php if (canSeeMenu('wisma')): ?>
            <a
                class="nav-item-link"
                data-bs-toggle="collapse"
                href="#wismaSubmenuDesktop"
                role="button"
                aria-expanded="<?= $wismaOpen ? 'true' : 'false' ?>"
                aria-controls="wismaSubmenuDesktop">
                <i class="bi bi-building nav-icon" aria-hidden="true"></i>
                <span>Wisma</span>
                <i class="bi bi-chevron-down nav-chevron" aria-hidden="true"></i>
            </a>

            <div class="collapse<?= $wismaOpen ? ' show' : '' ?>" id="wismaSubmenuDesktop">
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
                href="#roleSubmenuDesktop"
                role="button"
                aria-expanded="<?= $roleOpen ? 'true' : 'false' ?>"
                aria-controls="roleSubmenuDesktop">
                <i class="bi bi-person nav-icon" aria-hidden="true"></i>
                <span>Peranan</span>
                <i class="bi bi-chevron-down nav-chevron" aria-hidden="true"></i>
            </a>

            <div class="collapse<?= $roleOpen ? ' show' : '' ?>" id="roleSubmenuDesktop">
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
                href="#usersSubmenuDesktop"
                role="button"
                aria-expanded="<?= $usersOpen ? 'true' : 'false' ?>"
                aria-controls="usersSubmenuDesktop">
                <i class="bi bi-people nav-icon" aria-hidden="true"></i>
                <span>Pengguna</span>
                <i class="bi bi-chevron-down nav-chevron" aria-hidden="true"></i>
            </a>

            <div class="collapse<?= $usersOpen ? ' show' : '' ?>" id="usersSubmenuDesktop">
                <ul class="submenu-list">
                    <li><a href="<?= e(routeUrl('users-users')) ?>" class="submenu-link<?= $activeRoute === 'users-users' ? ' active' : '' ?>" <?= $activeRoute === 'users-users' ? ' aria-current="page"' : '' ?>>Pengguna</a></li>
                    <li><a href="<?= e(routeUrl('users-contractor')) ?>" class="submenu-link<?= $activeRoute === 'users-contractor' ? ' active' : '' ?>" <?= $activeRoute === 'users-contractor' ? ' aria-current="page"' : '' ?>>Kontraktor</a></li>
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
                href="#mejabantuanSubmenuDesktop"
                role="button"
                aria-expanded="<?= $mejabantuanOpen ? 'true' : 'false' ?>"
                aria-controls="mejabantuanSubmenuDesktop">
                <i class="bi bi-headset nav-icon" aria-hidden="true"></i>
                <span>Meja Bantuan</span>
                <i class="bi bi-chevron-down nav-chevron" aria-hidden="true"></i>
            </a>

            <div class="collapse<?= $mejabantuanOpen ? ' show' : '' ?>" id="mejabantuanSubmenuDesktop">
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
</aside>

<!-- Sidebar Mobile / Tablet (Bootstrap Offcanvas < lg): see src/layout/sidebar-mobile.php -->
<?php require SRC_PATH . '/layout/sidebar-mobile.php'; ?>