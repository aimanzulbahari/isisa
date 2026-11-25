<?php
declare(strict_types=1);

/**
 * Breadcrumb paths, one for each route in routes/web.php.
 *
 *   'route-name' => [
 *       ['label' => 'Text shown', 'route' => 'route-to-link-to'],   // a link
 *       ['label' => 'Group name', 'route' => null],                 // plain text (no page)
 *       ['label' => 'This page'],                                   // the last step = current page
 *   ]
 *
 * The first step gets a house icon. The last step is never a link.
 * src/components/breadcrumbs.php reads this list and prints it.
 * A route that is not listed here shows no breadcrumb.
 *
 */

// The page that the "Dashboard" step links to
$dashboard = ['label' => 'Dashboard', 'route' => 'dashboard'];

// The "Components" group has no page of its own, so it is plain text
$components = ['label' => 'Components', 'route' => null];

// The "Dashboard" menu group has no page of its own, so it is plain text
$dashboardGroup = ['label' => 'Dashboard', 'route' => null];

return [

    'guide' => [$dashboard, ['label' => 'Guide', 'route' => null], ['label' => 'README.md']],

    'components-main'   => [$dashboard, $components, ['label' => 'Main']],
    'components-basic'  => [$dashboard, $components, ['label' => 'Basic']],
    'components-form'   => [$dashboard, $components, ['label' => 'Form']],
    'components-tables' => [$dashboard, $components, ['label' => 'Tables']],
    'components-cards'  => [$dashboard, $components, ['label' => 'Cards']],
    'components-charts' => [$dashboard, $components, ['label' => 'Charts']],
    'components-alerts' => [$dashboard, $components, ['label' => 'Alerts']],
    'components-toastr' => [$dashboard, $components, ['label' => 'Toastr']],

    'dashboard-executive' => [$dashboardGroup, ['label' => 'Eksekutif']],
    'dashboard-kpi' => [$dashboardGroup, ['label' => 'KPI & Analitik']],
    'dashboard-operation' => [$dashboardGroup, ['label' => 'Operasi Dan Data']],

    'dashboard'        => [['label' => 'Dashboard']],

    'wisma-department' => [$dashboard, ['label' => 'Wisma', 'route' => null], ['label' => 'Jabatan']],
    'wisma-section' => [$dashboard, ['label' => 'Wisma', 'route' => null], ['label' => 'Seksyen']],
    'wisma-division' => [$dashboard, ['label' => 'Wisma', 'route' => null], ['label' => 'Bahagian']],
    'wisma-branch' => [$dashboard, ['label' => 'Wisma', 'route' => null], ['label' => 'Cawangan']],
    'role-role' => [$dashboard, ['label' => 'Peranan', 'route' => null], ['label' => 'Peranan']],
    'role-type' => [$dashboard, ['label' => 'Peranan', 'route' => null], ['label' => 'Jenis Peranan']],
    'users-users' => [$dashboard, ['label' => 'Pengguna', 'route' => null], ['label' => 'Pengguna']],
    'users-employee' => [$dashboard, ['label' => 'Pengguna', 'route' => null], ['label' => 'Kakitangan']],
    'users-contractor' => [$dashboard, ['label' => 'Pengguna', 'route' => null], ['label' => 'Kontraktor']],
    'system-audit' => [$dashboard, ['label' => 'Tetapan Sistem', 'route' => null], ['label' => 'Jejak Audit']],
    'system-backup' => [$dashboard, ['label' => 'Tetapan Sistem', 'route' => null], ['label' => 'Backup Data']],
    'broadcast-program' => [$dashboard, ['label' => 'Hebahan', 'route' => null], ['label' => 'Program & Aktiviti']],
    'announcement' => [$dashboard, ['label' => 'Hebahan', 'route' => null], ['label' => 'Pengumuman']],

    // Pages from the profile menu in the header (src/layout/header.php)
    'notification' => [$dashboard, ['label' => 'Notifikasi']],
    'accounts' => [$dashboard, ['label' => 'Akaun Saya']],

    'system-info' => [$dashboard, ['label' => 'Tetapan Sistem', 'route' => null], ['label' => 'Info Sistem']],
    'system-maintenance' => [$dashboard, ['label' => 'Tetapan Sistem', 'route' => null], ['label' => 'Penyelenggaraan']],
    'meja-dashboard' => [$dashboard, ['label' => 'Meja Bantuan', 'route' => null], ['label' => 'Dashboard']],
    'meja-list' => [$dashboard, ['label' => 'Meja Bantuan', 'route' => null], ['label' => 'Senarai Aduan']],

];
