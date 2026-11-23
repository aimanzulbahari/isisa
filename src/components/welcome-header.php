<section class="welcome-banner mb-4 animate-in delay-1" aria-label="Alu-aluan Pengguna">
    <div class="position-relative" style="z-index: 1;">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <div class="welcome-date-badge">
                    <i class="bi bi-calendar3" aria-hidden="true"></i>
                    <span id="currentDateDisplay">Memuatkan tarikh...</span>
                </div>
                <!-- Nama pengguna yang sedang log masuk, dalam HURUF BESAR (sama seperti nama dalam header).
                     welcome-header.js menambah salam ikut waktu (pagi/petang...) di hadapan nama ini.
                     Hanya nama yang dibesarkan, salamnya tidak. Nama dalam pangkalan data tidak berubah. -->
                <?php $welcomeName = mb_strtoupper(userDisplayName()); ?>
                <h2 class="welcome-title" id="welcomeGreeting" data-username="<?= e($welcomeName) ?>">
                    Selamat sejahtera, <?= e($welcomeName) ?>
                </h2>
                <p class="mb-0 text-white text-opacity-75" style="font-size: 0.925rem;">
                    Berikut ringkasan aktiviti ISISA anda hari ini.
                </p>
            </div>

            <div class="flex-shrink-0">
                <a href="#" class="btn btn-welcome-action">
                    <i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i>
                    <span>Lihat Laporan</span>
                </a>
            </div>
        </div>
    </div>
</section>