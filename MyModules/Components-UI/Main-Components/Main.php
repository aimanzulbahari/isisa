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
    <title>Main Components</title>

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

          <!-- 1b. Tab Navigasi (1–8): boleh digulung mendatar pada skrin kecil -->
          <!-- Skrin kecil (< md): tab dijadikan dropdown -->
          <?php require SRC_PATH . '/components/nav-tabs.php'; ?>

          <!-- 2. Welcome Header / Banner Alu-aluan -->
          <?php require SRC_PATH . '/components/welcome-header.php'; ?>

          <!-- 3. 4 Kad Statistik (Grid: 4 lajur desktop, 2 lajur tablet, 1 lajur mobile) -->
          <!-- PHP: Gantikan nilai statistik contoh di bawah dengan data pangkalan data menggunakan htmlspecialchars() -->
          <section class="row g-3 g-lg-4 mb-4" aria-label="Ringkasan Statistik">
            <!-- Kad 1 -->
            <div class="col-12 col-sm-6 col-xl-3 animate-in delay-1">
              <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div class="stat-icon-circle">
                    <i class="bi bi-folder2-open" aria-hidden="true"></i>
                  </div>
                  <span class="stat-trend trend-up">
                    <i class="bi bi-arrow-up-short" aria-hidden="true"></i>+12.4%
                  </span>
                </div>
                <div>
                  <div class="stat-value tabular-nums mb-1">1,284</div>
                  <div class="stat-label">Jumlah Rekod Aktif</div>
                </div>
              </div>
            </div>

            <!-- Kad 2 -->
            <div class="col-12 col-sm-6 col-xl-3 animate-in delay-2">
              <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div class="stat-icon-circle">
                    <i class="bi bi-hourglass-split" aria-hidden="true"></i>
                  </div>
                  <span class="stat-trend trend-warn">
                    <i class="bi bi-clock" aria-hidden="true"></i>48 baru
                  </span>
                </div>
                <div>
                  <div class="stat-value tabular-nums mb-1">142</div>
                  <div class="stat-label">Permohonan Dalam Proses</div>
                </div>
              </div>
            </div>

            <!-- Kad 3 -->
            <div class="col-12 col-sm-6 col-xl-3 animate-in delay-3">
              <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div class="stat-icon-circle">
                    <i class="bi bi-check2-circle" aria-hidden="true"></i>
                  </div>
                  <span class="stat-trend trend-up">
                    <i class="bi bi-arrow-up-short" aria-hidden="true"></i>+8.2%
                  </span>
                </div>
                <div>
                  <div class="stat-value tabular-nums mb-1">964</div>
                  <div class="stat-label">Selesai Bulan Ini</div>
                </div>
              </div>
            </div>

            <!-- Kad 4 -->
            <div class="col-12 col-sm-6 col-xl-3 animate-in delay-4">
              <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div class="stat-icon-circle">
                    <i class="bi bi-building" aria-hidden="true"></i>
                  </div>
                  <span class="stat-trend trend-up">
                    <i class="bi bi-check" aria-hidden="true"></i>Aktif
                  </span>
                </div>
                <div>
                  <div class="stat-value tabular-nums mb-1">24</div>
                  <div class="stat-label">Jabatan Berdaftar</div>
                </div>
              </div>
            </div>
          </section>

          <!-- 4 & 5. Carta & Jadual Aktiviti Terkini (2 Lajur Desktop, 1 Lajur Mobile) -->
          <section class="row g-3 g-lg-4">

            <!-- 4. Kad Carta (Chart.js) -->
            <div class="col-12 col-lg-8 order-2 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div>
                    <h3 class="h6 fw-semibold mb-1">Statistik Permohonan Bulanan</h3>
                    <p class="text-muted small mb-0">Perbandingan permohonan diterima dan selesai (2026)</p>
                  </div>
                  <span class="badge rounded-pill" style="background-color: var(--isisa-primary-soft); color: var(--isisa-primary); font-weight: 600;">
                    6 Bulan Terkini
                  </span>
                </div>
                <div class="position-relative flex-grow-1" style="min-height: 270px;">
                  <canvas id="isisaMonthlyChart" aria-label="Carta Statistik Permohonan Bulanan" role="img"></canvas>
                </div>
              </div>
            </div>

            <!-- 4b. Kad Carta Pai (Status Permohonan) -->
            <div class="col-12 col-lg-4 order-3 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                <div class="mb-3">
                  <h3 class="h6 fw-semibold mb-1">Status Permohonan</h3>
                  <p class="text-muted small mb-0">Pecahan mengikut status semasa</p>
                </div>
                <div class="position-relative flex-grow-1" style="min-height: 270px;">
                  <canvas id="isisaStatusPie" aria-label="Carta Pai Status Permohonan" role="img"></canvas>
                </div>
              </div>
            </div>

            <!-- 4c. Kad Carta Donut (Permohonan Mengikut Jabatan) -->
            <div class="col-12 col-lg-4 order-4 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                <div class="mb-3">
                  <h3 class="h6 fw-semibold mb-1">Permohonan Mengikut Jabatan</h3>
                  <p class="text-muted small mb-0">Agihan jumlah permohonan bulan ini</p>
                </div>
                <div class="position-relative flex-grow-1" style="min-height: 270px;">
                  <canvas id="isisaDeptDonut" aria-label="Carta Donut Permohonan Mengikut Jabatan" role="img"></canvas>
                </div>
              </div>
            </div>

            <!-- 4d. Kad Carta Garisan (Trend Mingguan) -->
            <div class="col-12 col-lg-8 order-5 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div>
                    <h3 class="h6 fw-semibold mb-1">Trend Permohonan Mingguan</h3>
                    <p class="text-muted small mb-0">Jumlah permohonan diterima setiap minggu</p>
                  </div>
                  <span class="badge rounded-pill" style="background-color: var(--isisa-primary-soft); color: var(--isisa-primary); font-weight: 600;">
                    8 Minggu Terkini
                  </span>
                </div>
                <div class="position-relative flex-grow-1" style="min-height: 270px;">
                  <canvas id="isisaWeeklyLine" aria-label="Carta Garisan Trend Permohonan Mingguan" role="img"></canvas>
                </div>
              </div>
            </div>

            <!-- 4e. Kad Carta Bar Mendatar -->
            <div class="col-12 col-lg-4 order-6 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                <div class="mb-3">
                  <h3 class="h6 fw-semibold mb-1">Jenis Permohonan Teratas</h3>
                  <p class="text-muted small mb-0">Lima kategori dengan permohonan terbanyak</p>
                </div>
                <div class="position-relative flex-grow-1" style="min-height: 270px;">
                  <canvas id="isisaHorizontalBar" aria-label="Carta Bar Mendatar Jenis Permohonan" role="img"></canvas>
                </div>
              </div>
            </div>

            <!-- 4f. Kad Carta Bar Bertindan -->
            <div class="col-12 col-lg-8 order-7 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div>
                    <h3 class="h6 fw-semibold mb-1">Status Permohonan Mengikut Bulan</h3>
                    <p class="text-muted small mb-0">Pecahan status bagi setiap bulan (bar bertindan)</p>
                  </div>
                  <span class="badge rounded-pill" style="background-color: var(--isisa-primary-soft); color: var(--isisa-primary); font-weight: 600;">
                    6 Bulan Terkini
                  </span>
                </div>
                <div class="position-relative flex-grow-1" style="min-height: 270px;">
                  <canvas id="isisaStackedBar" aria-label="Carta Bar Bertindan Status Permohonan Mengikut Bulan" role="img"></canvas>
                </div>
              </div>
            </div>

            <!-- 4g. Kad Pautan Pantas -->
            <!-- 4g-1. Kad Carta Polar Area (di bawah Jenis Permohonan Teratas) -->
            <div class="col-12 col-lg-4 order-8 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                <div class="mb-3">
                  <h3 class="h6 fw-semibold mb-1">Aduan Mengikut Zon</h3>
                  <p class="text-muted small mb-0">Bilangan aduan awam bagi setiap zon (polar area)</p>
                </div>
                <div class="position-relative flex-grow-1" style="min-height: 300px;">
                  <canvas id="isisaPolarArea" aria-label="Carta Polar Area Aduan Mengikut Zon" role="img"></canvas>
                </div>
              </div>
            </div>

            <!-- 4g-2. Kad Carta Radar -->
            <div class="col-12 col-lg-4 order-9 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                <div class="mb-3">
                  <h3 class="h6 fw-semibold mb-1">Prestasi Perkhidmatan</h3>
                  <p class="text-muted small mb-0">Perbandingan skor prestasi 2025 dan 2026 (radar)</p>
                </div>
                <div class="position-relative flex-grow-1" style="min-height: 300px;">
                  <canvas id="isisaRadar" aria-label="Carta Radar Prestasi Perkhidmatan" role="img"></canvas>
                </div>
              </div>
            </div>

            <div class="col-12 col-lg-4 order-10 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4 h-100">
                <div class="mb-3">
                  <h3 class="h6 fw-semibold mb-1">Pautan Pantas</h3>
                  <p class="text-muted small mb-0">Capaian pantas ke modul yang kerap digunakan</p>
                </div>
                <!-- PHP: Gantikan href dengan laluan modul sebenar -->
                <nav class="d-flex flex-column gap-2" aria-label="Pautan Pantas">
                  <a href="#" class="quick-link">
                    <span class="quick-link-icon"><i class="bi bi-file-earmark-plus" aria-hidden="true"></i></span>
                    <span class="quick-link-text">Permohonan Baharu</span>
                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                  </a>
                  <a href="#" class="quick-link">
                    <span class="quick-link-icon"><i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i></span>
                    <span class="quick-link-text">Laporan Bulanan</span>
                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                  </a>
                  <a href="#" class="quick-link">
                    <span class="quick-link-icon"><i class="bi bi-building" aria-hidden="true"></i></span>
                    <span class="quick-link-text">Senarai Premis</span>
                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                  </a>
                  <a href="#" class="quick-link">
                    <span class="quick-link-icon"><i class="bi bi-recycle" aria-hidden="true"></i></span>
                    <span class="quick-link-text">Jadual Kutipan Sisa</span>
                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                  </a>
                  <a href="#" class="quick-link">
                    <span class="quick-link-icon"><i class="bi bi-question-circle" aria-hidden="true"></i></span>
                    <span class="quick-link-text">Bantuan &amp; Panduan</span>
                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                  </a>
                </nav>
              </div>
            </div>

            <!-- 4h. Kad Carta Garisan Bertindan -->
            <div class="col-12 order-11 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div>
                    <h3 class="h6 fw-semibold mb-1">Kutipan Sisa Mengikut Kategori</h3>
                    <p class="text-muted small mb-0">Tonase mingguan (garisan bertindan, tan)</p>
                  </div>
                  <span class="badge rounded-pill" style="background-color: var(--isisa-primary-soft); color: var(--isisa-primary); font-weight: 600;">
                    8 Minggu Terkini
                  </span>
                </div>
                <div class="position-relative flex-grow-1" style="min-height: 270px;">
                  <canvas id="isisaStackedLine" aria-label="Carta Garisan Bertindan Kutipan Sisa Mengikut Kategori" role="img"></canvas>
                </div>
              </div>
            </div>

            <!-- 4i. Kad Carta Combo Bar + Garisan (di bawah Kutipan Sisa Mengikut Kategori) -->
            <div class="col-12 order-12 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div>
                    <h3 class="h6 fw-semibold mb-1">Permohonan &amp; Kadar Kelulusan</h3>
                    <p class="text-muted small mb-0">Bilangan permohonan (bar) dan peratus kelulusan (garisan)</p>
                  </div>
                  <span class="badge rounded-pill" style="background-color: var(--isisa-primary-soft); color: var(--isisa-primary); font-weight: 600;">
                    12 Bulan
                  </span>
                </div>
                <div class="position-relative flex-grow-1" style="min-height: 300px;">
                  <canvas id="isisaComboChart" aria-label="Carta Combo Bar dan Garisan Permohonan dan Kadar Kelulusan" role="img"></canvas>
                </div>
              </div>
            </div>

            <!-- 5. Kad Jadual Aktiviti Terkini -->
            <div class="col-12 order-1 animate-in delay-4">
              <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                <div class="table-head mb-3">
                  <div>
                    <h3 class="h6 fw-semibold mb-1">Aktiviti Terkini</h3>
                    <p class="text-muted small mb-0">Kemas kini rekod terbaru merentas jabatan</p>
                  </div>

                  <!-- Bar Alat: Carian, Tapis, Set Semula & Tambah Data -->
                  <div class="table-toolbar">
                    <div class="toolbar-search">
                      <i class="bi bi-search" aria-hidden="true"></i>
                      <input type="search" id="tableSearch" class="form-control toolbar-ctl" placeholder="Cari rekod..." aria-label="Carian rekod">
                    </div>
                    <select id="filterStatus" class="form-select toolbar-ctl toolbar-select" aria-label="Tapis mengikut status">
                      <option value="">Semua Status</option>
                      <option value="success">Diluluskan</option>
                      <option value="warning">Semakan</option>
                      <option value="danger">Perlu Tindakan</option>
                    </select>
                    <select id="filterDept" class="form-select toolbar-ctl toolbar-select" aria-label="Tapis mengikut jabatan">
                      <option value="">Semua Jabatan</option>
                      <option>Jabatan Kejuruteraan</option>
                      <option>Jabatan Perancangan</option>
                      <option>Jabatan Pelesenan</option>
                      <option>Jabatan Landskap</option>
                      <option>Jabatan Khidmat Pengurusan</option>
                    </select>
                    <button type="button" id="btnFilter" class="btn btn-primary toolbar-ctl">
                      <i class="bi bi-funnel" aria-hidden="true"></i><span>Tapis</span>
                    </button>
                    <button type="button" id="btnReset" class="btn toolbar-ctl toolbar-ctl-outline">
                      <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i><span>Set Semula</span>
                    </button>
                    <button type="button" class="btn btn-primary toolbar-ctl" data-bs-toggle="modal" data-bs-target="#addDataModal">
                      <i class="bi bi-plus-lg" aria-hidden="true"></i><span>Tambah Data</span>
                    </button>
                  </div>
                </div>

                <!-- PHP: Gelung foreach untuk memaparkan baris jadual dari pangkalan data (gunakan htmlspecialchars()) -->
                <div class="table-responsive">
                  <table class="table table-isisa align-middle">
                    <thead>
                      <tr>
                        <th scope="col" style="width: 60px;">No.</th>
                        <th scope="col">No. Rujukan</th>
                        <th scope="col">Jabatan</th>
                        <th scope="col">Tarikh</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-center" style="width: 110px;">Tindakan</th>
                      </tr>
                    </thead>
                    <tbody class="tabular-nums">
                      <tr>
                        <td class="text-muted">1</td>
                        <td class="fw-semibold">ISISA-2026-0891</td>
                        <td>Jabatan Kejuruteraan</td>
                        <td class="text-muted">13 Nov 2026</td>
                        <td>
                          <span class="badge-status badge-status-success">
                            <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Diluluskan
                          </span>
                        </td>
                        <td class="text-center">
                          <div class="d-inline-flex gap-1">
                            <a href="#" class="btn-action" aria-label="Lihat rekod" title="Lihat"><i class="bi bi-eye" aria-hidden="true"></i></a>
                            <a href="#" class="btn-action" aria-label="Sunting rekod" title="Sunting"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-muted">2</td>
                        <td class="fw-semibold">ISISA-2026-0890</td>
                        <td>Jabatan Perancangan</td>
                        <td class="text-muted">13 Nov 2026</td>
                        <td>
                          <span class="badge-status badge-status-warning">
                            <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Semakan
                          </span>
                        </td>
                        <td class="text-center">
                          <div class="d-inline-flex gap-1">
                            <a href="#" class="btn-action" aria-label="Lihat rekod" title="Lihat"><i class="bi bi-eye" aria-hidden="true"></i></a>
                            <a href="#" class="btn-action" aria-label="Sunting rekod" title="Sunting"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-muted">3</td>
                        <td class="fw-semibold">ISISA-2026-0889</td>
                        <td>Jabatan Pelesenan</td>
                        <td class="text-muted">12 Nov 2026</td>
                        <td>
                          <span class="badge-status badge-status-success">
                            <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Diluluskan
                          </span>
                        </td>
                        <td class="text-center">
                          <div class="d-inline-flex gap-1">
                            <a href="#" class="btn-action" aria-label="Lihat rekod" title="Lihat"><i class="bi bi-eye" aria-hidden="true"></i></a>
                            <a href="#" class="btn-action" aria-label="Sunting rekod" title="Sunting"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-muted">4</td>
                        <td class="fw-semibold">ISISA-2026-0888</td>
                        <td>Jabatan Landskap</td>
                        <td class="text-muted">12 Nov 2026</td>
                        <td>
                          <span class="badge-status badge-status-danger">
                            <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Perlu Tindakan
                          </span>
                        </td>
                        <td class="text-center">
                          <div class="d-inline-flex gap-1">
                            <a href="#" class="btn-action" aria-label="Lihat rekod" title="Lihat"><i class="bi bi-eye" aria-hidden="true"></i></a>
                            <a href="#" class="btn-action" aria-label="Sunting rekod" title="Sunting"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-muted">5</td>
                        <td class="fw-semibold">ISISA-2026-0887</td>
                        <td>Jabatan Khidmat Pengurusan</td>
                        <td class="text-muted">11 Nov 2026</td>
                        <td>
                          <span class="badge-status badge-status-success">
                            <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Diluluskan
                          </span>
                        </td>
                        <td class="text-center">
                          <div class="d-inline-flex gap-1">
                            <a href="#" class="btn-action" aria-label="Lihat rekod" title="Lihat"><i class="bi bi-eye" aria-hidden="true"></i></a>
                            <a href="#" class="btn-action" aria-label="Sunting rekod" title="Sunting"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <!-- Pagination (10 rekod setiap halaman) -->
                <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 mt-3 pt-3 border-top">
                  <div class="small text-muted" id="tableInfo" aria-live="polite"></div>
                  <nav aria-label="Navigasi halaman jadual">
                    <ul class="pagination pagination-isisa mb-0" id="tablePagination"></ul>
                  </nav>
                </div>
              </div>
            </div>

          </section>

          <!-- 6. Komponen: Status Badge & Senarai Button -->
          <section class="row g-3 g-lg-4 mt-0" aria-label="Komponen UI">

            <!-- Status Badge -->
            <div class="col-12 col-lg-6 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4 h-100">
                <div class="mb-3">
                  <h3 class="h6 fw-semibold mb-1">Status Badge</h3>
                  <p class="text-muted small mb-0">Penanda status untuk jadual, senarai dan kad</p>
                </div>

                <div class="demo-label">Lembut (dengan titik)</div>
                <div class="d-flex flex-wrap gap-2 mb-4">
                  <span class="badge-status badge-status-success"><i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Diluluskan</span>
                  <span class="badge-status badge-status-warning"><i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Semakan</span>
                  <span class="badge-status badge-status-danger"><i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Perlu Tindakan</span>
                  <span class="badge-status badge-status-info"><i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Dalam Proses</span>
                  <span class="badge-status badge-status-neutral"><i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Draf</span>
                </div>

                <div class="demo-label">Dengan ikon</div>
                <div class="d-flex flex-wrap gap-2 mb-4">
                  <span class="badge-status badge-status-success"><i class="bi bi-check-circle" aria-hidden="true"></i>Selesai</span>
                  <span class="badge-status badge-status-warning"><i class="bi bi-hourglass-split" aria-hidden="true"></i>Menunggu</span>
                  <span class="badge-status badge-status-danger"><i class="bi bi-x-circle" aria-hidden="true"></i>Ditolak</span>
                  <span class="badge-status badge-status-info"><i class="bi bi-info-circle" aria-hidden="true"></i>Makluman</span>
                  <span class="badge-status badge-status-neutral"><i class="bi bi-dash-circle" aria-hidden="true"></i>Dibatalkan</span>
                </div>

                <div class="demo-label">Pepejal</div>
                <div class="d-flex flex-wrap gap-2">
                  <span class="badge-status badge-solid-success">Aktif</span>
                  <span class="badge-status badge-solid-warning">Tertunggak</span>
                  <span class="badge-status badge-solid-danger">Kritikal</span>
                  <span class="badge-status badge-solid-info">Baharu</span>
                  <span class="badge-status badge-solid-neutral">Tidak Aktif</span>
                </div>
              </div>
            </div>

            <!-- Senarai Button -->
            <div class="col-12 col-lg-6 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4 h-100">
                <div class="mb-3">
                  <h3 class="h6 fw-semibold mb-1">Senarai Button</h3>
                  <p class="text-muted small mb-0">Variasi butang yang konsisten dengan tema ISISA</p>
                </div>

                <div class="demo-label">Varian</div>
                <div class="d-flex flex-wrap gap-2 mb-4">
                  <button type="button" class="btn btn-primary toolbar-ctl">Utama</button>
                  <button type="button" class="btn toolbar-ctl btn-soft">Lembut</button>
                  <button type="button" class="btn toolbar-ctl toolbar-ctl-outline">Garis</button>
                  <button type="button" class="btn toolbar-ctl btn-danger-soft">Bahaya</button>
                  <button type="button" class="btn toolbar-ctl btn-link-isisa">Pautan</button>
                </div>

                <div class="demo-label">Dengan ikon</div>
                <div class="d-flex flex-wrap gap-2 mb-4">
                  <button type="button" class="btn btn-primary toolbar-ctl"><i class="bi bi-plus-lg" aria-hidden="true"></i><span>Tambah</span></button>
                  <button type="button" class="btn toolbar-ctl btn-soft"><i class="bi bi-download" aria-hidden="true"></i><span>Muat Turun</span></button>
                  <button type="button" class="btn toolbar-ctl toolbar-ctl-outline"><i class="bi bi-printer" aria-hidden="true"></i><span>Cetak</span></button>
                  <button type="button" class="btn toolbar-ctl btn-danger-soft"><i class="bi bi-trash" aria-hidden="true"></i><span>Padam</span></button>
                  <button type="button" class="btn toolbar-ctl btn-export btn-export-pdf"><i class="bi bi-file-earmark-pdf" aria-hidden="true"></i><span>PDF</span></button>
                  <button type="button" class="btn toolbar-ctl btn-export btn-export-excel"><i class="bi bi-file-earmark-excel" aria-hidden="true"></i><span>Excel</span></button>
                  <button type="button" class="btn toolbar-ctl btn-export btn-export-csv"><i class="bi bi-filetype-csv" aria-hidden="true"></i><span>CSV</span></button>
                </div>

                <div class="demo-label">Ikon sahaja &amp; kumpulan</div>
                <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                  <div class="d-inline-flex gap-1">
                    <a href="#" class="btn-action" aria-label="Lihat" title="Lihat"><i class="bi bi-eye" aria-hidden="true"></i></a>
                    <a href="#" class="btn-action" aria-label="Sunting" title="Sunting"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                    <a href="#" class="btn-action" aria-label="Padam" title="Padam"><i class="bi bi-trash" aria-hidden="true"></i></a>
                  </div>
                  <div class="btn-group" role="group" aria-label="Kumpulan butang">
                    <button type="button" class="btn toolbar-ctl toolbar-ctl-outline rounded-end-0">Hari</button>
                    <button type="button" class="btn toolbar-ctl btn-primary rounded-0">Minggu</button>
                    <button type="button" class="btn toolbar-ctl toolbar-ctl-outline rounded-start-0">Bulan</button>
                  </div>
                </div>

                <div class="demo-label">Tindakan permohonan</div>
                <div class="d-flex flex-wrap gap-2 mb-4">
                  <button type="button" class="btn btn-primary toolbar-ctl" data-flow="hantar"><i class="bi bi-send" aria-hidden="true"></i><span>Hantar</span></button>
                  <button type="button" class="btn toolbar-ctl toolbar-ctl-outline" data-flow="setsemula"><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i><span>Set Semula</span></button>
                  <button type="button" class="btn btn-primary toolbar-ctl" data-flow="terima"><i class="bi bi-check2-square" aria-hidden="true"></i><span>Terima Permohonan</span></button>
                  <button type="button" class="btn toolbar-ctl btn-danger-solid" data-flow="tolak"><i class="bi bi-x-square" aria-hidden="true"></i><span>Tolak Permohonan</span></button>
                  <button type="button" class="btn btn-primary toolbar-ctl" data-flow="lulus"><i class="bi bi-check-circle" aria-hidden="true"></i><span>Lulus</span></button>
                  <button type="button" class="btn toolbar-ctl btn-danger-solid" data-flow="gagal"><i class="bi bi-x-circle" aria-hidden="true"></i><span>Gagal</span></button>
                  <button type="button" class="btn toolbar-ctl btn-soft" data-flow="penyemak"><i class="bi bi-person-check" aria-hidden="true"></i><span>Hantar Kepada Penyemak</span></button>
                  <button type="button" class="btn toolbar-ctl btn-soft" data-flow="pelulus"><i class="bi bi-person-check-fill" aria-hidden="true"></i><span>Hantar Kepada Pelulus</span></button>
                </div>

                <div class="demo-label">Dilumpuhkan</div>
                <div class="d-flex flex-wrap gap-2">
                  <button type="button" class="btn btn-primary toolbar-ctl" disabled>Utama</button>
                  <button type="button" class="btn toolbar-ctl toolbar-ctl-outline" disabled>Garis</button>
                  <button type="button" class="btn toolbar-ctl btn-soft" disabled>Lembut</button>
                </div>
              </div>
            </div>

          </section>

          <!-- 7. Komponen: Alerts & Toastr -->
          <section class="row g-3 g-lg-4 mt-0" aria-label="Alerts dan Toastr">

            <!-- Alerts -->
            <div class="col-12 col-lg-7 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4 h-100">
                <div class="mb-3">
                  <h3 class="h6 fw-semibold mb-1">Alerts</h3>
                  <p class="text-muted small mb-0">Mesej dalam halaman untuk makluman, amaran dan ralat</p>
                </div>

                <div class="d-flex flex-column gap-3">
                  <div class="alert alert-success d-flex align-items-start gap-2 mb-0" role="alert">
                    <i class="bi bi-check-circle-fill alert-icon" aria-hidden="true"></i>
                    <div><strong>Berjaya!</strong> Permohonan anda telah dihantar dan sedang menunggu semakan.</div>
                  </div>

                  <div class="alert alert-info d-flex align-items-start gap-2 mb-0" role="alert">
                    <i class="bi bi-info-circle-fill alert-icon" aria-hidden="true"></i>
                    <div><strong>Makluman.</strong> Penyelenggaraan sistem dijadualkan pada 20 November 2026, 12:00–2:00 pagi.</div>
                  </div>

                  <div class="alert alert-warning d-flex align-items-start gap-2 mb-0" role="alert">
                    <i class="bi bi-exclamation-triangle-fill alert-icon" aria-hidden="true"></i>
                    <div><strong>Amaran.</strong> Lesen premis anda akan tamat tempoh dalam 14 hari.</div>
                  </div>

                  <div class="alert alert-danger d-flex align-items-start gap-2 mb-0" role="alert">
                    <i class="bi bi-x-circle-fill alert-icon" aria-hidden="true"></i>
                    <div><strong>Ralat.</strong> Rekod tidak dapat disimpan. Sila semak maklumat dan cuba lagi.</div>
                  </div>

                  <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                    <div class="d-flex align-items-start gap-2">
                      <i class="bi bi-check2-circle alert-icon" aria-hidden="true"></i>
                      <div><strong>Boleh ditutup.</strong> Klik butang silang untuk menutup alert ini.</div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Tutup"></button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Toastr -->
            <div class="col-12 col-lg-5 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4 h-100">
                <div class="mb-3">
                  <h3 class="h6 fw-semibold mb-1">Toastr</h3>
                  <p class="text-muted small mb-0">Notifikasi sementara yang muncul di penjuru skrin</p>
                </div>

                <div class="demo-label">Jenis notifikasi</div>
                <div class="d-flex flex-wrap gap-2 mb-4">
                  <button type="button" class="btn btn-primary toolbar-ctl" data-toast="success"><i class="bi bi-check-circle" aria-hidden="true"></i><span>Berjaya</span></button>
                  <button type="button" class="btn toolbar-ctl btn-soft" data-toast="info"><i class="bi bi-info-circle" aria-hidden="true"></i><span>Makluman</span></button>
                  <button type="button" class="btn toolbar-ctl toolbar-ctl-outline" data-toast="warning"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i><span>Amaran</span></button>
                  <button type="button" class="btn toolbar-ctl btn-danger-soft" data-toast="error"><i class="bi bi-x-circle" aria-hidden="true"></i><span>Ralat</span></button>
                </div>

                <div class="demo-label">Mesej tersuai (pilihan)</div>
                <div class="row g-2 mb-4 form-isisa">
                  <div class="col-12 col-sm-5">
                    <input type="text" class="form-control" id="toastTitle" placeholder="Tajuk" aria-label="Tajuk toastr">
                  </div>
                  <div class="col-12 col-sm-7">
                    <input type="text" class="form-control" id="toastText" placeholder="Mesej" aria-label="Mesej toastr">
                  </div>
                </div>

                <div class="demo-label">Tetapan</div>
                <div class="row g-3 mb-4 form-isisa">
                  <div class="col-12 col-sm-6">
                    <label for="toastPosition" class="form-label small mb-1">Kedudukan</label>
                    <select class="form-select" id="toastPosition">
                      <option value="toast-top-right" selected>Atas kanan</option>
                      <option value="toast-top-left">Atas kiri</option>
                      <option value="toast-top-center">Atas tengah</option>
                      <option value="toast-bottom-right">Bawah kanan</option>
                      <option value="toast-bottom-left">Bawah kiri</option>
                      <option value="toast-bottom-center">Bawah tengah</option>
                    </select>
                  </div>
                  <div class="col-12 col-sm-6">
                    <label for="toastDuration" class="form-label small mb-1">Tempoh paparan</label>
                    <select class="form-select" id="toastDuration">
                      <option value="2000">2 saat</option>
                      <option value="4000" selected>4 saat</option>
                      <option value="8000">8 saat</option>
                      <option value="0">Kekal (tutup manual)</option>
                    </select>
                  </div>
                  <div class="col-12 d-flex flex-wrap gap-x-4 column-gap-4 row-gap-1">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" role="switch" id="toastProgress" checked>
                      <label class="form-check-label small" for="toastProgress">Bar progres</label>
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" role="switch" id="toastClose" checked>
                      <label class="form-check-label small" for="toastClose">Butang tutup</label>
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" role="switch" id="toastNewest" checked>
                      <label class="form-check-label small" for="toastNewest">Terbaharu di atas</label>
                    </div>
                  </div>
                </div>

                <button type="button" class="btn toolbar-ctl toolbar-ctl-outline" id="btnClearToasts">
                  <i class="bi bi-x-lg" aria-hidden="true"></i><span>Kosongkan Semua</span>
                </button>
              </div>
            </div>

          </section>

          <!-- 8. Komponen: Accordion -->
          <section class="row g-3 g-lg-4 mt-0" aria-label="Accordion">

            <!-- Accordion Biasa (satu panel dibuka pada satu masa) -->
            <div class="col-12 col-lg-6 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4 h-100">
                <div class="mb-3">
                  <h3 class="h6 fw-semibold mb-1">Accordion</h3>
                  <p class="text-muted small mb-0">Soalan lazim — satu panel dibuka pada satu masa</p>
                </div>

                <div class="accordion accordion-isisa" id="faqAccordion">
                  <div class="accordion-item">
                    <h2 class="accordion-header">
                      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                        <i class="bi bi-file-earmark-plus acc-icon" aria-hidden="true"></i>Bagaimana membuat permohonan baharu?
                      </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                      <div class="accordion-body">Pilih menu <strong>Permohonan</strong>, klik <strong>Tambah Data</strong>, lengkapkan borang dan tekan <strong>Simpan</strong>. Status permohonan boleh dipantau dalam jadual Aktiviti Terkini.</div>
                    </div>
                  </div>

                  <div class="accordion-item">
                    <h2 class="accordion-header">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                        <i class="bi bi-clock-history acc-icon" aria-hidden="true"></i>Berapa lama tempoh semakan permohonan?
                      </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                      <div class="accordion-body">Tempoh semakan biasa ialah 7 hari bekerja selepas dokumen lengkap diterima oleh jabatan berkaitan.</div>
                    </div>
                  </div>

                  <div class="accordion-item">
                    <h2 class="accordion-header">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                        <i class="bi bi-key acc-icon" aria-hidden="true"></i>Bagaimana menetapkan semula kata laluan?
                      </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                      <div class="accordion-body">Di halaman log masuk, klik <strong>Lupa kata laluan?</strong> dan ikut arahan yang dihantar ke emel berdaftar anda.</div>
                    </div>
                  </div>

                  <div class="accordion-item">
                    <h2 class="accordion-header">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
                        <i class="bi bi-headset acc-icon" aria-hidden="true"></i>Siapa boleh dihubungi untuk bantuan?
                      </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                      <div class="accordion-body">Hubungi Jabatan Kesihatan dan Kelestarian Sisa, MBSA semasa waktu pejabat, Isnin hingga Jumaat.</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Accordion Terbuka Serentak -->
            <div class="col-12 col-lg-6 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4 h-100">
                <div class="mb-3">
                  <h3 class="h6 fw-semibold mb-1">Accordion Berbilang Panel</h3>
                  <p class="text-muted small mb-0">Beberapa panel boleh dibuka serentak, dengan lencana ringkas</p>
                </div>

                <div class="accordion accordion-isisa" id="detailAccordion">
                  <div class="accordion-item">
                    <h2 class="accordion-header">
                      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#acc1" aria-expanded="true" aria-controls="acc1">
                        <span class="flex-grow-1">Maklumat Premis</span>
                        <span class="badge-status badge-status-success me-2">Lengkap</span>
                      </button>
                    </h2>
                    <div id="acc1" class="accordion-collapse collapse show">
                      <div class="accordion-body">
                        <dl class="row mb-0 small">
                          <dt class="col-5 text-muted fw-medium">Nama Premis</dt>
                          <dd class="col-7">Restoran Seri Kenanga</dd>
                          <dt class="col-5 text-muted fw-medium">No. Lesen</dt>
                          <dd class="col-7 mb-0">MBSA-2026-01482</dd>
                        </dl>
                      </div>
                    </div>
                  </div>

                  <div class="accordion-item">
                    <h2 class="accordion-header">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#acc2" aria-expanded="false" aria-controls="acc2">
                        <span class="flex-grow-1">Dokumen Sokongan</span>
                        <span class="badge-status badge-status-warning me-2">2 Belum</span>
                      </button>
                    </h2>
                    <div id="acc2" class="accordion-collapse collapse">
                      <div class="accordion-body">
                        <ul class="list-unstyled mb-0 small d-flex flex-column gap-2">
                          <li><i class="bi bi-check-circle-fill text-success me-2" aria-hidden="true"></i>Salinan lesen perniagaan</li>
                          <li><i class="bi bi-circle text-muted me-2" aria-hidden="true"></i>Pelan lokasi premis</li>
                          <li><i class="bi bi-circle text-muted me-2" aria-hidden="true"></i>Perjanjian pengurusan sisa</li>
                        </ul>
                      </div>
                    </div>
                  </div>

                  <div class="accordion-item">
                    <h2 class="accordion-header">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#acc3" aria-expanded="false" aria-controls="acc3">
                        <span class="flex-grow-1">Sejarah Semakan</span>
                        <span class="badge-status badge-status-neutral me-2">3 Rekod</span>
                      </button>
                    </h2>
                    <div id="acc3" class="accordion-collapse collapse">
                      <div class="accordion-body small">
                        <div class="d-flex flex-column gap-2">
                          <div class="d-flex justify-content-between"><span>Permohonan diterima</span><span class="text-muted">10 Nov 2026</span></div>
                          <div class="d-flex justify-content-between"><span>Semakan dokumen</span><span class="text-muted">12 Nov 2026</span></div>
                          <div class="d-flex justify-content-between"><span>Menunggu kelulusan</span><span class="text-muted">13 Nov 2026</span></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </section>

          <!-- 9. Komponen: Borang (semua jenis input) -->
          <section class="row g-3 g-lg-4 mt-0" aria-label="Borang">
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
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="chk1" checked>
                        <label class="form-check-label" for="chk1">Domestik</label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="chk2">
                        <label class="form-check-label" for="chk2">Komersial</label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="chk3" disabled>
                        <label class="form-check-label" for="chk3">Industri (dilumpuhkan)</label>
                      </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                      <div class="form-label">Radio</div>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="radioJenis" id="rad1" checked>
                        <label class="form-check-label" for="rad1">Individu</label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="radioJenis" id="rad2">
                        <label class="form-check-label" for="rad2">Syarikat</label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="radioJenis" id="rad3">
                        <label class="form-check-label" for="rad3">Pertubuhan</label>
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

                  <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 pt-3 border-top">
                    <button type="reset" class="btn toolbar-ctl toolbar-ctl-outline"><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i><span>Set Semula</span></button>
                    <button type="submit" class="btn btn-primary toolbar-ctl"><i class="bi bi-check2" aria-hidden="true"></i><span>Hantar</span></button>
                  </div>
                </form>
              </div>
            </div>
          </section>

          <!-- 10. Komponen: Timeline -->
          <section class="row g-3 g-lg-4 mt-0" aria-label="Timeline">
            <div class="col-12 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-4">
                  <div>
                    <h3 class="h6 fw-semibold mb-1">Garis Masa Permohonan</h3>
                    <p class="text-muted small mb-0">Sejarah aktiviti bagi permohonan <strong>ISISA-2026-0891</strong></p>
                  </div>
                  <span class="badge-status badge-status-warning"><i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Menunggu Kelulusan</span>
                </div>

                <!-- PHP: Gelung foreach untuk memaparkan sejarah aktiviti dari pangkalan data (gunakan htmlspecialchars()) -->
                <ol class="timeline">
                  <li class="timeline-item">
                    <span class="timeline-dot timeline-dot-success"><i class="bi bi-send-check" aria-hidden="true"></i></span>
                    <div class="timeline-content">
                      <div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                        <h4 class="timeline-title">Permohonan Dihantar</h4>
                        <time class="timeline-time" datetime="2026-11-10T09:15">10 Nov 2026, 9:15 pagi</time>
                      </div>
                      <p class="timeline-desc">Permohonan lesen premis dihantar oleh pemohon melalui portal ISISA.</p>
                      <span class="timeline-meta"><i class="bi bi-person" aria-hidden="true"></i>Ahmad Faiz</span>
                    </div>
                  </li>

                  <li class="timeline-item">
                    <span class="timeline-dot timeline-dot-info"><i class="bi bi-clipboard-check" aria-hidden="true"></i></span>
                    <div class="timeline-content">
                      <div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                        <h4 class="timeline-title">Disemak oleh Penyemak</h4>
                        <time class="timeline-time" datetime="2026-11-11T14:40">11 Nov 2026, 2:40 petang</time>
                      </div>
                      <p class="timeline-desc">Semakan awal dokumen dan maklumat premis telah dilakukan.</p>
                      <span class="timeline-meta"><i class="bi bi-person" aria-hidden="true"></i>Nurul Aina · Jabatan Pelesenan</span>
                    </div>
                  </li>

                  <li class="timeline-item">
                    <span class="timeline-dot timeline-dot-warning"><i class="bi bi-exclamation-lg" aria-hidden="true"></i></span>
                    <div class="timeline-content">
                      <div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                        <h4 class="timeline-title">Dokumen Tambahan Diminta</h4>
                        <time class="timeline-time" datetime="2026-11-11T15:05">11 Nov 2026, 3:05 petang</time>
                      </div>
                      <p class="timeline-desc">Pemohon diminta mengemukakan pelan lokasi premis yang terkini.</p>
                      <span class="timeline-meta"><i class="bi bi-person" aria-hidden="true"></i>Nurul Aina · Jabatan Pelesenan</span>
                    </div>
                  </li>

                  <li class="timeline-item">
                    <span class="timeline-dot timeline-dot-neutral"><i class="bi bi-file-earmark-arrow-up" aria-hidden="true"></i></span>
                    <div class="timeline-content">
                      <div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                        <h4 class="timeline-title">Dokumen Dikemas Kini</h4>
                        <time class="timeline-time" datetime="2026-11-12T10:20">12 Nov 2026, 10:20 pagi</time>
                      </div>
                      <p class="timeline-desc">Pelan lokasi premis telah dimuat naik dan disahkan lengkap.</p>
                      <span class="timeline-meta"><i class="bi bi-paperclip" aria-hidden="true"></i>pelan-lokasi.pdf</span>
                    </div>
                  </li>

                  <li class="timeline-item timeline-item-current">
                    <span class="timeline-dot timeline-dot-current"><i class="bi bi-hourglass-split" aria-hidden="true"></i></span>
                    <div class="timeline-content">
                      <div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                        <h4 class="timeline-title">Menunggu Kelulusan</h4>
                        <time class="timeline-time" datetime="2026-11-13T08:30">13 Nov 2026, 8:30 pagi</time>
                      </div>
                      <p class="timeline-desc">Permohonan dihantar kepada pelulus untuk keputusan akhir.</p>
                      <span class="timeline-meta"><i class="bi bi-person" aria-hidden="true"></i>Pelulus: Dato' Rahman</span>
                    </div>
                  </li>

                  <li class="timeline-item timeline-item-pending">
                    <span class="timeline-dot timeline-dot-pending"><i class="bi bi-check2-all" aria-hidden="true"></i></span>
                    <div class="timeline-content">
                      <div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                        <h4 class="timeline-title">Keputusan Akhir</h4>
                        <span class="timeline-time">Belum selesai</span>
                      </div>
                      <p class="timeline-desc mb-0">Notifikasi keputusan akan dihantar kepada pemohon selepas kelulusan.</p>
                    </div>
                  </li>
                </ol>
              </div>
            </div>
          </section>

          <!-- 11. Komponen: Mesej Notifikasi -->
          <section class="row g-3 g-lg-4 mt-0" aria-label="Mesej Notifikasi">
            <div class="col-12 animate-in delay-5">
              <div class="isisa-card p-3 p-md-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
                  <div>
                    <h3 class="h6 fw-semibold mb-1 d-flex align-items-center gap-2">
                      Notifikasi
                      <span class="notif-count" id="notifUnreadCount" aria-live="polite">5</span>
                    </h3>
                    <p class="text-muted small mb-0">Mesej terkini berkaitan permohonan dan sistem</p>
                  </div>
                  <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="btn-group" role="group" aria-label="Tapis notifikasi">
                      <button type="button" class="btn toolbar-ctl btn-primary rounded-end-0" id="notifFilterAll" aria-pressed="true">Semua</button>
                      <button type="button" class="btn toolbar-ctl toolbar-ctl-outline rounded-start-0" id="notifFilterUnread" aria-pressed="false">Belum Dibaca</button>
                    </div>
                    <button type="button" class="btn toolbar-ctl toolbar-ctl-outline" id="notifMarkAll">
                      <i class="bi bi-check2-all" aria-hidden="true"></i><span>Tandakan Semua Dibaca</span>
                    </button>
                  </div>
                </div>

                <!-- PHP: Gelung foreach untuk memaparkan notifikasi dari pangkalan data (gunakan htmlspecialchars()) -->
                <ul class="notif-list" id="notifList">
                  <li class="notif-item unread" tabindex="0">
                    <span class="notif-icon notif-icon-success"><i class="bi bi-check-circle" aria-hidden="true"></i></span>
                    <div class="notif-body">
                      <div class="notif-title">Permohonan Diluluskan</div>
                      <div class="notif-text">Permohonan ISISA-2026-0889 telah diluluskan oleh Jabatan Pelesenan.</div>
                    </div>
                    <div class="notif-side"><span class="notif-time">5 minit lalu</span><span class="notif-dot" aria-label="Belum dibaca"></span></div>
                  </li>

                  <li class="notif-item unread" tabindex="0">
                    <span class="notif-icon notif-icon-warning"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i></span>
                    <div class="notif-body">
                      <div class="notif-title">Tindakan Diperlukan</div>
                      <div class="notif-text">Permohonan ISISA-2026-0888 memerlukan dokumen tambahan sebelum 20 November 2026.</div>
                    </div>
                    <div class="notif-side"><span class="notif-time">32 minit lalu</span><span class="notif-dot" aria-label="Belum dibaca"></span></div>
                  </li>

                  <li class="notif-item unread" tabindex="0">
                    <span class="notif-icon notif-icon-info"><i class="bi bi-person-check" aria-hidden="true"></i></span>
                    <div class="notif-body">
                      <div class="notif-title">Dihantar Kepada Penyemak</div>
                      <div class="notif-text">Permohonan ISISA-2026-0890 telah dihantar kepada Nurul Aina untuk semakan.</div>
                    </div>
                    <div class="notif-side"><span class="notif-time">1 jam lalu</span><span class="notif-dot" aria-label="Belum dibaca"></span></div>
                  </li>

                  <li class="notif-item unread" tabindex="0">
                    <span class="notif-icon notif-icon-danger"><i class="bi bi-x-circle" aria-hidden="true"></i></span>
                    <div class="notif-body">
                      <div class="notif-title">Permohonan Ditolak</div>
                      <div class="notif-text">Permohonan ISISA-2026-0885 tidak memenuhi syarat pelesenan. Sila semak catatan penolakan.</div>
                    </div>
                    <div class="notif-side"><span class="notif-time">3 jam lalu</span><span class="notif-dot" aria-label="Belum dibaca"></span></div>
                  </li>

                  <li class="notif-item unread" tabindex="0">
                    <span class="notif-icon notif-icon-neutral"><i class="bi bi-gear" aria-hidden="true"></i></span>
                    <div class="notif-body">
                      <div class="notif-title">Penyelenggaraan Sistem</div>
                      <div class="notif-text">Sistem tidak dapat dicapai pada 20 November 2026, 12:00–2:00 pagi.</div>
                    </div>
                    <div class="notif-side"><span class="notif-time">Semalam</span><span class="notif-dot" aria-label="Belum dibaca"></span></div>
                  </li>

                  <li class="notif-item" tabindex="0">
                    <span class="notif-icon notif-icon-success"><i class="bi bi-file-earmark-check" aria-hidden="true"></i></span>
                    <div class="notif-body">
                      <div class="notif-title">Laporan Bulanan Tersedia</div>
                      <div class="notif-text">Laporan pengurusan sisa bulan Oktober 2026 sedia untuk dimuat turun.</div>
                    </div>
                    <div class="notif-side"><span class="notif-time">2 hari lalu</span></div>
                  </li>

                  <li class="notif-item" tabindex="0">
                    <span class="notif-icon notif-icon-info"><i class="bi bi-megaphone" aria-hidden="true"></i></span>
                    <div class="notif-body">
                      <div class="notif-title">Pengumuman Baharu</div>
                      <div class="notif-text">Program Pengkomposan Sisa Organik Komuniti 2026 kini dibuka untuk permohonan.</div>
                    </div>
                    <div class="notif-side"><span class="notif-time">4 hari lalu</span></div>
                  </li>
                </ul>

                <div class="notif-empty text-center text-muted py-4 d-none" id="notifEmpty">
                  <i class="bi bi-bell-slash fs-3 d-block mb-2" aria-hidden="true"></i>
                  Tiada notifikasi belum dibaca.
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

    <!-- Butang Kembali ke Atas -->
    <button type="button" class="back-to-top" id="backToTop" aria-label="Kembali ke atas" title="Kembali ke atas">
      <i class="bi bi-arrow-up" aria-hidden="true"></i>
    </button>

    <!-- Modal Tambah Data -->
    <div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="addDataModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content needs-validation" id="addDataForm" novalidate style="border-radius: var(--isisa-radius-lg); border: 1px solid var(--isisa-border);">
          <!-- PHP: Hantar ke pelayan melalui POST bersama CSRF token & sahkan input di bahagian pelayan -->
          <div class="modal-header border-0 pb-0">
            <h2 class="modal-title h5 fw-bold" id="addDataModalLabel">Tambah Data</h2>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="fieldRujukan" class="form-label fw-medium small">No. Rujukan</label>
              <input type="text" class="form-control" id="fieldRujukan" placeholder="cth: ISISA-2026-0892" maxlength="30" required>
              <div class="invalid-feedback">Sila masukkan no. rujukan.</div>
            </div>
            <div class="mb-3">
              <label for="fieldJabatan" class="form-label fw-medium small">Jabatan</label>
              <select class="form-select" id="fieldJabatan" required>
                <option value="" selected disabled>Pilih jabatan</option>
                <option>Jabatan Kejuruteraan</option>
                <option>Jabatan Perancangan</option>
                <option>Jabatan Pelesenan</option>
                <option>Jabatan Landskap</option>
                <option>Jabatan Khidmat Pengurusan</option>
              </select>
              <div class="invalid-feedback">Sila pilih jabatan.</div>
            </div>
            <div class="row g-3">
              <div class="col-sm-6">
                <label for="fieldTarikh" class="form-label fw-medium small">Tarikh</label>
                <input type="date" class="form-control" id="fieldTarikh" required>
                <div class="invalid-feedback">Sila pilih tarikh.</div>
              </div>
              <div class="col-sm-6">
                <label for="fieldStatus" class="form-label fw-medium small">Status</label>
                <select class="form-select" id="fieldStatus" required>
                  <option value="success">Diluluskan</option>
                  <option value="warning">Semakan</option>
                  <option value="danger">Perlu Tindakan</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Libraries: jQuery, Bootstrap bundle, Axios, Chart.js, Select2, SweetAlert2, Toastr (see public/library/js-library.php) -->
    <?= jsLibrary('dashboard') ?>
    <!-- Page scripts: type="module" because main.js imports its modules. Must come AFTER the libraries. -->
    <script type="module" src="<?= e(asset('assets/js/dashboard-js/main.js', true)) ?>"></script>


  </body>

</html>