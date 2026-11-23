<?php

declare(strict_types=1);

// Start the system (config, session, CSRF, helpers, middleware)
require_once dirname(__DIR__, 3) . '/core/config/bootstrap.php';

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
    <title>Form Template</title>

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
                <!-- BREADCRUMBS -->
                <?php require SRC_PATH . '/components/breadcrumbs.php'; ?>

                <!-- Tab Navigasi: boleh digulung mendatar pada skrin kecil -->
                <!-- Skrin kecil (< md): tab dijadikan dropdown -->
                <?php require SRC_PATH . '/components/nav-tabs.php'; ?>

                <section class="row g-3 g-lg-4" aria-label="Borang">
                    <div class="col-12 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4">
                            <div class="mb-4">
                                <h3 class="h6 fw-semibold mb-1">Borang</h3>
                                <p class="text-muted small mb-0">Contoh semua jenis medan input yang tersedia</p>
                            </div>

                            <!-- PHP: Hantar melalui POST bersama CSRF token & sahkan semua input di bahagian pelayan -->
                            <form class="form-isisa needs-validation" id="demoForm" novalidate>

                                <div class="form-group-title">Teks &amp; Maklumat Asas</div>
                                <div class="row g-3 mb-4">
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inText" class="form-label">Nama Penuh</label>
                                        <input type="text" class="form-control" id="inText" placeholder="cth: Ahmad Faiz bin Ali" required>
                                        <div class="invalid-feedback">Sila masukkan nama penuh.</div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inEmail" class="form-label">Emel</label>
                                        <input type="email" class="form-control" id="inEmail" placeholder="nama@mbsa.gov.my" required>
                                        <div class="invalid-feedback">Sila masukkan emel yang sah.</div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inTel" class="form-label">No. Telefon</label>
                                        <input type="tel" class="form-control" id="inTel" placeholder="012-3456789" inputmode="tel">
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inPassword" class="form-label">Kata Laluan</label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control pe-5" id="inPassword" placeholder="Masukkan kata laluan" autocomplete="new-password">
                                            <button type="button" class="btn-toggle-password" id="demoTogglePwd" aria-label="Tunjukkan kata laluan" style="position:absolute;top:50%;right:0.4rem;transform:translateY(-50%);width:30px;height:30px;border:none;background:transparent;color:var(--isisa-text-muted);">
                                                <i class="bi bi-eye" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inNumber" class="form-label">Nombor</label>
                                        <input type="number" class="form-control" id="inNumber" placeholder="0" min="0" max="1000" step="1">
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inUrl" class="form-label">Laman Web</label>
                                        <input type="url" class="form-control" id="inUrl" placeholder="https://www.mbsa.gov.my">
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inSearch" class="form-label">Carian</label>
                                        <input type="search" class="form-control" id="inSearch" placeholder="Cari...">
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inDatalist" class="form-label">Input dengan Cadangan</label>
                                        <input class="form-control" list="datalistJabatan" id="inDatalist" placeholder="Taip untuk cadangan...">
                                        <datalist id="datalistJabatan">
                                            <option value="Jabatan Kejuruteraan"></option>
                                            <option value="Jabatan Perancangan"></option>
                                            <option value="Jabatan Pelesenan"></option>
                                            <option value="Jabatan Landskap"></option>
                                        </datalist>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inGroupAmount" class="form-label">Input Berkumpulan</label>
                                        <div class="input-group">
                                            <span class="input-group-text">RM</span>
                                            <input type="number" class="form-control" id="inGroupAmount" placeholder="0.00" step="0.01" min="0">
                                            <span class="input-group-text">.00</span>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inWeight" class="form-label">Berat</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="inWeight" placeholder="0.0" step="0.1" min="0" max="500" aria-describedby="unitKg">
                                            <span class="input-group-text" id="unitKg">kg</span>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inHeight" class="form-label">Tinggi</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="inHeight" placeholder="0" step="1" min="0" max="300" aria-describedby="unitCm">
                                            <span class="input-group-text" id="unitCm">cm</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group-title">Tarikh &amp; Masa</div>
                                <div class="row g-3 mb-4">
                                    <div class="col-12 col-md-6 col-xl-3">
                                        <label for="inDate" class="form-label">Tarikh</label>
                                        <input type="date" class="form-control" id="inDate">
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-3">
                                        <label for="inTime" class="form-label">Masa</label>
                                        <input type="time" class="form-control" id="inTime">
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-3">
                                        <label for="inDatetime" class="form-label">Tarikh &amp; Masa</label>
                                        <input type="datetime-local" class="form-control" id="inDatetime">
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-3">
                                        <label for="inMonth" class="form-label">Bulan</label>
                                        <input type="month" class="form-control" id="inMonth">
                                    </div>
                                </div>

                                <div class="form-group-title">Pilihan &amp; Senarai</div>
                                <div class="row g-3 mb-4">
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inSelect" class="form-label">Dropdown (Select)</label>
                                        <select class="form-select" id="inSelect">
                                            <option value="" selected disabled>Pilih jabatan</option>
                                            <option>Jabatan Kejuruteraan</option>
                                            <option>Jabatan Perancangan</option>
                                            <option>Jabatan Pelesenan</option>
                                            <option>Jabatan Landskap</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inMulti" class="form-label">Pilihan Berbilang</label>
                                        <select class="form-select" id="inMulti" multiple data-placeholder="Pilih kategori perkhidmatan">
                                            <option>Kutipan Sisa</option>
                                            <option selected>Kitar Semula</option>
                                            <option>Pembersihan Awam</option>
                                            <option>Landskap</option>
                                            <option>Kawalan Haiwan</option>
                                            <option>Penguatkuasaan</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-xl-4">
                                        <label for="inTextarea" class="form-label">Catatan (Textarea)</label>
                                        <textarea class="form-control" id="inTextarea" rows="4" placeholder="Tulis catatan anda di sini..." maxlength="300"></textarea>
                                    </div>
                                </div>

                                <div class="form-group-title">Checkbox, Radio, Switch &amp; Slider</div>
                                <div class="row g-3 mb-4">
                                    <div class="col-12 col-md-6 col-xl-3">
                                        <div class="form-label">Checkbox</div>
                                        <div class="isisa-choice-group" role="group" aria-label="Jenis sisa">
                                            <label class="isisa-check" for="chk1">
                                                <input type="checkbox" id="chk1" checked>
                                                <span class="isisa-check-box" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5.5 12.5l4.2 4.2L18.5 8"/></svg></span>
                                                <span class="isisa-check-label">Domestik</span>
                                            </label>
                                            <label class="isisa-check" for="chk2">
                                                <input type="checkbox" id="chk2">
                                                <span class="isisa-check-box" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5.5 12.5l4.2 4.2L18.5 8"/></svg></span>
                                                <span class="isisa-check-label">Komersial</span>
                                            </label>
                                            <label class="isisa-check" for="chk3">
                                                <input type="checkbox" id="chk3" disabled>
                                                <span class="isisa-check-box" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5.5 12.5l4.2 4.2L18.5 8"/></svg></span>
                                                <span class="isisa-check-label">Industri (dilumpuhkan)</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-3">
                                        <div class="form-label">Radio</div>
                                        <div class="isisa-choice-group" role="radiogroup" aria-label="Jenis pemohon">
                                            <label class="isisa-radio" for="rad1">
                                                <input type="radio" name="radioJenis" id="rad1" checked>
                                                <span class="isisa-radio-dot" aria-hidden="true"></span>
                                                <span class="isisa-check-label">Individu</span>
                                            </label>
                                            <label class="isisa-radio" for="rad2">
                                                <input type="radio" name="radioJenis" id="rad2">
                                                <span class="isisa-radio-dot" aria-hidden="true"></span>
                                                <span class="isisa-check-label">Syarikat</span>
                                            </label>
                                            <label class="isisa-radio" for="rad3">
                                                <input type="radio" name="radioJenis" id="rad3">
                                                <span class="isisa-radio-dot" aria-hidden="true"></span>
                                                <span class="isisa-check-label">Pertubuhan</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-3">
                                        <div class="form-label">Switch</div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="sw1" checked>
                                            <label class="form-check-label" for="sw1">Notifikasi emel</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="sw2">
                                            <label class="form-check-label" for="sw2">Notifikasi SMS</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="sw3" disabled>
                                            <label class="form-check-label" for="sw3">Dilumpuhkan</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-3">
                                        <label for="inRange" class="form-label d-flex justify-content-between">
                                            <span>Slider (Range)</span><span class="text-muted fw-semibold" id="rangeValue">50</span>
                                        </label>
                                        <input type="range" class="form-range" id="inRange" min="0" max="100" value="50">
                                        <label for="inColor" class="form-label mt-2">Pemilih Warna</label>
                                        <input type="color" class="form-control form-control-color" id="inColor" value="#386641" title="Pilih warna">
                                    </div>
                                </div>

                                <div class="form-group-title">Fail &amp; Keadaan Input</div>
                                <div class="row g-3 mb-4">
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inFile" class="form-label">Muat Naik Fail</label>
                                        <input type="file" class="form-control" id="inFile" accept=".pdf,.jpg,.jpeg,.png">
                                        <div class="form-text">PDF, JPG atau PNG. Maksimum 5 MB.</div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inMultiFile" class="form-label">Muat Naik Berbilang Fail</label>
                                        <input type="file" class="form-control" id="inMultiFile" multiple>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inReadonly" class="form-label">Baca Sahaja (Readonly)</label>
                                        <input type="text" class="form-control" id="inReadonly" value="ISISA-2026-0891" readonly>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inDisabled" class="form-label">Dilumpuhkan (Disabled)</label>
                                        <input type="text" class="form-control" id="inDisabled" value="Tidak boleh diubah" disabled>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inValid" class="form-label">Keadaan Sah</label>
                                        <input type="text" class="form-control is-valid" id="inValid" value="ahmad.faiz@mbsa.gov.my">
                                        <div class="valid-feedback">Kelihatan baik!</div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="inInvalid" class="form-label">Keadaan Ralat</label>
                                        <input type="text" class="form-control is-invalid" id="inInvalid" value="abc">
                                        <div class="invalid-feedback">Format tidak sah. Sila semak semula.</div>
                                    </div>
                                </div>

                                <div class="form-group-title">Pilihan Kotak (Sebaris)</div>
                                <div class="row g-4 mb-4">
                                    <div class="col-12">
                                        <div class="isisa-choice-title" id="rolesLabel">Peranan Yang Boleh Akses Sistem</div>
                                        <div class="isisa-choice-row" style="--cols: 4" role="group" aria-labelledby="rolesLabel">
                                            <label class="isisa-check is-tile" for="roleSuperadmin">
                                                <input type="checkbox" id="roleSuperadmin" name="peranan[]" value="superadmin" checked>
                                                <span class="isisa-check-box" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5.5 12.5l4.2 4.2L18.5 8"/></svg></span>
                                                <span class="isisa-check-label">Superadmin</span>
                                            </label>
                                            <label class="isisa-check is-tile" for="roleKakitangan">
                                                <input type="checkbox" id="roleKakitangan" name="peranan[]" value="kakitangan">
                                                <span class="isisa-check-box" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5.5 12.5l4.2 4.2L18.5 8"/></svg></span>
                                                <span class="isisa-check-label">Kakitangan</span>
                                            </label>
                                            <label class="isisa-check is-tile" for="roleWakil">
                                                <input type="checkbox" id="roleWakil" name="peranan[]" value="wakil-agensi">
                                                <span class="isisa-check-box" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5.5 12.5l4.2 4.2L18.5 8"/></svg></span>
                                                <span class="isisa-check-label">Wakil Agensi</span>
                                            </label>
                                            <label class="isisa-check is-tile" for="rolePemohon">
                                                <input type="checkbox" id="rolePemohon" name="peranan[]" value="pemohon">
                                                <span class="isisa-check-box" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5.5 12.5l4.2 4.2L18.5 8"/></svg></span>
                                                <span class="isisa-check-label">Pemohon / Kontraktor / Pemaju / Pemilik</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="isisa-choice-title" id="maintLabel">Set Status Penyelenggaraan<span class="req" aria-hidden="true">*</span></div>
                                        <div class="isisa-choice-row" style="--cols: 2" role="radiogroup" aria-labelledby="maintLabel" aria-required="true">
                                            <label class="isisa-radio is-tile" for="maintOff">
                                                <input type="radio" id="maintOff" name="status_penyelenggaraan" value="tidak-aktif" checked required>
                                                <span class="isisa-radio-dot" aria-hidden="true"></span>
                                                <span class="isisa-check-label">Penyelenggaraan Tidak Aktif</span>
                                            </label>
                                            <label class="isisa-radio is-tile" for="maintOn">
                                                <input type="radio" id="maintOn" name="status_penyelenggaraan" value="aktif" required>
                                                <span class="isisa-radio-dot" aria-hidden="true"></span>
                                                <span class="isisa-check-label">Penyelenggaraan Aktif</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 pt-3 border-top">
                                    <button type="reset" class="btn toolbar-ctl toolbar-ctl-outline"><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i><span>Set Semula</span></button>
                                    <button type="submit" class="btn btn-primary toolbar-ctl"><i class="bi bi-check2" aria-hidden="true"></i><span>Hantar</span></button>
                                </div>
                            </form>
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

    <!-- Butang Kembali ke Atas -->
    <button type="button" class="back-to-top" id="backToTop" aria-label="Kembali ke atas" title="Kembali ke atas">
        <i class="bi bi-arrow-up" aria-hidden="true"></i>
    </button>

    <!-- Libraries: jQuery, Bootstrap bundle, Axios, Chart.js, Select2, SweetAlert2, Toastr (see public/library/js-library.php) -->
    <?= jsLibrary('dashboard') ?>
    <!-- Page scripts: type="module" because main.js imports its modules. Must come AFTER the libraries. -->
    <script type="module" src="<?= e(asset('assets/js/dashboard-js/main.js', true)) ?>"></script>


</body>

</html>