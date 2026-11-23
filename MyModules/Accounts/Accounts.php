<?php

declare(strict_types=1);

// Start the system (config, session, CSRF, helpers, middleware)
require_once dirname(__DIR__, 2) . '/core/config/bootstrap.php';

// Library loaders for the third-party fonts, CSS and JS in /public
require_once PUBLIC_PATH . '/library/font-library.php';
require_once PUBLIC_PATH . '/library/css-library.php';
require_once PUBLIC_PATH . '/library/js-library.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Akaun Saya</title>

    <!-- Favicon (see src/components/favicon.php) -->
    <?php require SRC_PATH . '/components/favicon.php'; ?>
    <!-- Font: Inter (local file, see public/library/font-library.php) -->
    <?= fontLibrary('dashboard') ?>
    <!-- Libraries: Bootstrap, Bootstrap Icons, Select2, SweetAlert2, Toastr (see public/library/css-library.php) -->
    <?= cssLibrary('dashboard') ?>
    <!-- Page styles: must come AFTER the libraries so they can override them -->
    <link rel="stylesheet" href="<?= e(asset('assets/css/dashboard-css/main.css', true)) ?>">
</head>

<body>

    <div class="app-wrapper">
        <!-- ======================================================================== SIDEBAR ======================================================================== -->
        <!-- Sidebar Desktop + Mobile (see src/layout/sidebar.php) -->
        <?php require SRC_PATH . '/layout/sidebar.php'; ?>

        <!-- ======================================================================== MAIN ======================================================================== -->
        <div class="main-container">
            <!-- ====================================================================== HEADER ====================================================================== -->
            <?php require SRC_PATH . '/layout/header.php'; ?>

            <!-- ====================================================================== MAIN CONTENT ====================================================================== -->
            <main class="content-area">
                <section class="welcome-banner mb-4 animate-in delay-1" aria-label="Alu-aluan Pengguna">
                    <div class="position-relative" style="z-index: 1;">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div>
                                <!-- PHP: Nama pengguna di bawah ialah placeholder. Gantikan dengan sesi PHP & lindungi dengan htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8') -->
                                <h2 class="welcome-title">
                                    Akaun Saya
                                </h2>
                                <p class="mb-0 text-white text-opacity-75" style="font-size: 0.925rem;">
                                    Berikut maklumat akaun anda.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="row g-3 g-lg-4" aria-label="Tetapan Akaun">
                    <!-- Kad pautan: Profil Saya / Keselamatan -->
                    <div class="col-12 col-lg-4 col-xl-3 animate-in delay-2">
                        <div class="isisa-card p-3 p-md-4 h-100">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Akaun</h3>
                                <p class="text-muted small mb-0">Urus maklumat dan keselamatan akaun anda</p>
                            </div>
                            <div class="d-flex flex-column gap-2" id="accountTabs" role="tablist" aria-orientation="vertical" aria-label="Menu Akaun">
                                <button type="button" class="quick-link account-link active" id="tabProfileBtn" data-bs-toggle="pill" data-bs-target="#tabProfile" role="tab" aria-controls="tabProfile" aria-selected="true">
                                    <span class="quick-link-icon"><i class="bi bi-person" aria-hidden="true"></i></span>
                                    <span class="quick-link-text">Profil Saya</span>
                                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="quick-link account-link" id="tabDeptBtn" data-bs-toggle="pill" data-bs-target="#tabDept" role="tab" aria-controls="tabDept" aria-selected="false">
                                    <span class="quick-link-icon"><i class="bi bi-building" aria-hidden="true"></i></span>
                                    <span class="quick-link-text">Info Jabatan</span>
                                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="quick-link account-link" id="tabRoleBtn" data-bs-toggle="pill" data-bs-target="#tabRole" role="tab" aria-controls="tabRole" aria-selected="false">
                                    <span class="quick-link-icon"><i class="bi bi-person-badge" aria-hidden="true"></i></span>
                                    <span class="quick-link-text">Info Peranan</span>
                                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="quick-link account-link" id="tabContactBtn" data-bs-toggle="pill" data-bs-target="#tabContact" role="tab" aria-controls="tabContact" aria-selected="false">
                                    <span class="quick-link-icon"><i class="bi bi-telephone" aria-hidden="true"></i></span>
                                    <span class="quick-link-text">Info Perhubungan</span>
                                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="quick-link account-link" id="tabSecurityBtn" data-bs-toggle="pill" data-bs-target="#tabSecurity" role="tab" aria-controls="tabSecurity" aria-selected="false">
                                    <span class="quick-link-icon"><i class="bi bi-shield-lock" aria-hidden="true"></i></span>
                                    <span class="quick-link-text">Keselamatan</span>
                                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Kandungan: dipaparkan mengikut pautan yang diklik -->
                    <div class="col-12 col-lg-8 col-xl-9 animate-in delay-3">
                        <div class="isisa-card p-3 p-md-4 h-100">
                            <div class="tab-content">

                                <!-- Profil Saya -->
                                <div class="tab-pane fade show active" id="tabProfile" role="tabpanel" aria-labelledby="tabProfileBtn" tabindex="0">
                                    <div class="mb-4">
                                        <h3 class="h6 fw-semibold mb-1">Profil Saya</h3>
                                        <p class="text-muted small mb-0">Kemas kini maklumat peribadi anda</p>
                                    </div>
                                    <!-- PHP: Hantar melalui POST bersama CSRF token & sahkan input di bahagian pelayan -->
                                    <form class="form-isisa needs-validation" id="profileForm" novalidate>
                                        <div class="row g-3 mb-4">
                                            <div class="col-12 col-md-6">
                                                <label for="profileName" class="form-label">Nama Penuh</label>
                                                <input type="text" class="form-control" id="profileName" name="nama" value="Ahmad Faiz" maxlength="150" required>
                                                <div class="invalid-feedback">Sila masukkan nama penuh.</div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="profileIc" class="form-label">No. Kad Pengenalan</label>
                                                <input type="text" class="form-control" id="profileIc" name="no_kp" value="900101145674" readonly>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="profileBirthDate" class="form-label">Tarikh Lahir</label>
                                                <input type="date" class="form-control" id="profileBirthDate" name="tarikh_lahir" value="1990-01-01" readonly>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="profileAge" class="form-label">Umur</label>
                                                <input type="text" class="form-control" id="profileAge" name="umur" placeholder="Dikira secara automatik" readonly>
                                            </div>
                                            <div class="col-12 col-md-12">
                                                <label for="profileStatus" class="form-label">Status</label>
                                                <input type="text" class="form-control" id="profileStatus" name="status" value="Aktif" readonly>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end pt-3 border-top">
                                            <button type="submit" class="btn btn-primary toolbar-ctl"><i class="bi bi-send" aria-hidden="true"></i><span>Kemaskini</span></button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Info Jabatan -->
                                <div class="tab-pane fade" id="tabDept" role="tabpanel" aria-labelledby="tabDeptBtn" tabindex="0">
                                    <div class="mb-4">
                                        <h3 class="h6 fw-semibold mb-1">Info Jabatan</h3>
                                        <p class="text-muted small mb-0">Maklumat penempatan jabatan anda</p>
                                    </div>
                                    <!-- PHP: Gantikan nilai contoh dengan data pangkalan data menggunakan htmlspecialchars() -->
                                    <div class="form-isisa">
                                        <div class="row g-3">
                                            <div class="col-12 col-md-6">
                                                <label for="deptName" class="form-label">Jabatan</label>
                                                <input type="text" class="form-control" id="deptName" value="Jabatan Kesihatan dan Kelestarian Sisa" readonly>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="deptSection" class="form-label">Seksyen</label>
                                                <input type="text" class="form-control" id="deptSection" value="Seksyen Pengurusan Sisa Pepejal" readonly>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="deptDivision" class="form-label">Bahagian</label>
                                                <input type="text" class="form-control" id="deptDivision" value="Bahagian Operasi" readonly>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="deptBranch" class="form-label">Cawangan</label>
                                                <input type="text" class="form-control" id="deptBranch" value="Ibu Pejabat MBSA" readonly>
                                            </div>
                                            <div class="col-12 col-md-12">
                                                <label for="deptPosition" class="form-label">Jawatan</label>
                                                <input type="text" class="form-control" id="deptPosition" value="Pegawai Kesihatan Persekitaran" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Info Peranan -->
                                <div class="tab-pane fade" id="tabRole" role="tabpanel" aria-labelledby="tabRoleBtn" tabindex="0">
                                    <div class="mb-4">
                                        <h3 class="h6 fw-semibold mb-1">Info Peranan</h3>
                                        <p class="text-muted small mb-0">Peranan dan capaian anda dalam sistem</p>
                                    </div>
                                    <!-- PHP: Gantikan nilai contoh dengan data pangkalan data menggunakan htmlspecialchars() -->
                                    <div class="form-isisa">
                                        <div class="row g-3">
                                            <div class="col-12 col-md-6">
                                                <label for="roleName" class="form-label">Peranan</label>
                                                <input type="text" class="form-control" id="roleName" value="Pentadbir" readonly>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="roleType" class="form-label">Jenis Peranan</label>
                                                <input type="text" class="form-control" id="roleType" value="Kakitangan" readonly>
                                            </div>
                                            <div class="col-12 col-md-12">
                                                <label for="roleDate" class="form-label">Tarikh Akaun Didaftarkan</label>
                                                <input type="text" class="form-control" id="roleDate" value="01 Januari 2024" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Info Perhubungan -->
                                <div class="tab-pane fade" id="tabContact" role="tabpanel" aria-labelledby="tabContactBtn" tabindex="0">
                                    <div class="mb-4">
                                        <h3 class="h6 fw-semibold mb-1">Info Perhubungan</h3>
                                        <p class="text-muted small mb-0">Maklumat untuk dihubungi</p>
                                    </div>
                                    <!-- PHP: Gantikan nilai contoh dengan data pangkalan data menggunakan htmlspecialchars() -->
                                    <div class="form-isisa">
                                        <div class="row g-3">
                                            <div class="col-12 col-md-6">
                                                <label for="contactEmail" class="form-label">Emel Rasmi</label>
                                                <input type="text" class="form-control" id="contactEmail" value="ahmad.faiz@mbsa.gov.my" readonly>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="contactPhone" class="form-label">No. Telefon</label>
                                                <input type="text" class="form-control" id="contactPhone" value="012-3456789" readonly>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="contactOffice" class="form-label">No. Telefon Pejabat</label>
                                                <input type="text" class="form-control" id="contactOffice" value="03-5522 9999" readonly>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="contactExt" class="form-label">Sambungan (ext.)</label>
                                                <input type="text" class="form-control" id="contactExt" value="1234" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Keselamatan -->
                                <div class="tab-pane fade" id="tabSecurity" role="tabpanel" aria-labelledby="tabSecurityBtn" tabindex="0">
                                    <div class="mb-4">
                                        <h3 class="h6 fw-semibold mb-1">Keselamatan</h3>
                                        <p class="text-muted small mb-0">Tukar kata laluan akaun anda</p>
                                    </div>
                                    <!-- PHP: Hantar melalui POST bersama CSRF token; sahkan kata laluan lama & simpan kata laluan baharu dengan password_hash() -->
                                    <form class="form-isisa needs-validation" id="securityForm" novalidate>
                                        <div class="row g-3 mb-4">
                                            <div class="col-12 col-md-6">
                                                <label for="securityEmail" class="form-label">Emel</label>
                                                <input type="email" class="form-control" id="securityEmail" name="emel" value="ahmad.faiz@mbsa.gov.my" autocomplete="username" required>
                                                <div class="invalid-feedback">Sila masukkan emel yang sah.</div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="securityPhone" class="form-label">No. Telefon</label>
                                                <input type="tel" class="form-control" id="securityPhone" name="telefon" value="012-3456789" inputmode="tel">
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="securityOldPwd" class="form-label">Kata Laluan Lama</label>
                                                <div class="password-field">
                                                    <input type="password" class="form-control" id="securityOldPwd" name="kata_laluan_lama" placeholder="Masukkan kata laluan lama" autocomplete="current-password" required>
                                                    <button type="button" class="password-toggle-btn" data-toggle-password="securityOldPwd" aria-label="Tunjukkan kata laluan" aria-pressed="false">
                                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                                <div class="invalid-feedback">Sila masukkan kata laluan lama.</div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="securityNewPwd" class="form-label">Kata Laluan Baharu</label>
                                                <div class="password-field">
                                                    <input type="password" class="form-control" id="securityNewPwd" name="kata_laluan_baharu" placeholder="Masukkan kata laluan baharu" autocomplete="new-password" minlength="8" required>
                                                    <button type="button" class="password-toggle-btn" data-toggle-password="securityNewPwd" aria-label="Tunjukkan kata laluan" aria-pressed="false">
                                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                                <div class="invalid-feedback">Kata laluan baharu mestilah sekurang-kurangnya 8 aksara.</div>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 pt-3 border-top">
                                            <button type="button" class="btn toolbar-ctl btn-danger-soft" id="btnDeactivateAccount" data-confirm="deactivate"><i class="bi bi-person-x" aria-hidden="true"></i><span>Nyahaktifkan Akaun</span></button>
                                            <button type="submit" class="btn btn-primary toolbar-ctl"><i class="bi bi-send" aria-hidden="true"></i><span>Kemaskini</span></button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </section>

                <!-- Jadual Log Aktiviti Akaun -->
                <section class="row g-3 g-lg-4 mt-0" aria-label="Log Aktiviti Akaun">
                    <div class="col-12 animate-in delay-4">
                        <div class="isisa-card p-3 p-md-4 d-flex flex-column">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Log Aktiviti Akaun</h3>
                                <p class="text-muted small mb-0">Aktiviti terkini pada akaun anda</p>
                            </div>

                            <!-- PHP: Gelung foreach untuk memaparkan baris jadual dari pangkalan data (gunakan htmlspecialchars()) -->
                            <div class="table-responsive">
                                <table class="table table-isisa align-middle" id="activityTable">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 60px;">No.</th>
                                            <th scope="col">Tarikh &amp; Masa</th>
                                            <th scope="col">Aktiviti</th>
                                            <th scope="col">Alamat IP</th>
                                            <th scope="col">Peranti</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tabular-nums">
                                        <tr>
                                            <td class="text-muted">1</td>
                                            <td>13 Nov 2026, 9:15 pagi</td>
                                            <td class="fw-semibold">Log masuk</td>
                                            <td>192.168.1.24</td>
                                            <td>Chrome · Windows</td>
                                            <td>
                                                <span class="badge-status badge-status-success">
                                                    <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Berjaya
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">2</td>
                                            <td>12 Nov 2026, 4:42 petang</td>
                                            <td class="fw-semibold">Tukar kata laluan</td>
                                            <td>192.168.1.24</td>
                                            <td>Chrome · Windows</td>
                                            <td>
                                                <span class="badge-status badge-status-success">
                                                    <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Berjaya
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">3</td>
                                            <td>12 Nov 2026, 8:58 pagi</td>
                                            <td class="fw-semibold">Log masuk</td>
                                            <td>103.52.118.7</td>
                                            <td>Safari · iPhone</td>
                                            <td>
                                                <span class="badge-status badge-status-danger">
                                                    <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Gagal
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">4</td>
                                            <td>11 Nov 2026, 10:03 pagi</td>
                                            <td class="fw-semibold">Kemas kini profil</td>
                                            <td>192.168.1.24</td>
                                            <td>Chrome · Windows</td>
                                            <td>
                                                <span class="badge-status badge-status-success">
                                                    <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Berjaya
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">5</td>
                                            <td>10 Nov 2026, 2:27 petang</td>
                                            <td class="fw-semibold">Log keluar</td>
                                            <td>192.168.1.31</td>
                                            <td>Edge · Windows</td>
                                            <td>
                                                <span class="badge-status badge-status-neutral">
                                                    <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Selesai
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination (UI sahaja) -->
                            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 mt-3 pt-3 border-top">
                                <div class="small text-muted">Menunjukkan 1–5 daripada 5 rekod</div>
                                <nav aria-label="Navigasi halaman jadual">
                                    <ul class="pagination pagination-isisa mb-0">
                                        <li class="page-item disabled"><a class="page-link" href="#" aria-label="Halaman sebelumnya"><i class="bi bi-chevron-left" aria-hidden="true"></i></a></li>
                                        <li class="page-item active"><a class="page-link" href="#" aria-label="Halaman 1" aria-current="page">1</a></li>
                                        <li class="page-item disabled"><a class="page-link" href="#" aria-label="Halaman seterusnya"><i class="bi bi-chevron-right" aria-hidden="true"></i></a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <!-- ======================================================================
           BAHAGIAN 4: FOOTER (Boleh diasingkan kepada includes/footer.php)
           ====================================================================== -->
            <?php require SRC_PATH . '/layout/footer.php'; ?>

        </div>
    </div>


    <!-- Libraries: jQuery, Bootstrap bundle, Axios, Chart.js, Select2, SweetAlert2, Toastr (see public/library/js-library.php) -->
    <?= jsLibrary('dashboard') ?>
    <!-- Page scripts: type="module" because main.js imports its modules. Must come AFTER the libraries. -->
    <script type="module" src="<?= e(asset('assets/js/dashboard-js/main.js', true)) ?>"></script>


</body>

</html>