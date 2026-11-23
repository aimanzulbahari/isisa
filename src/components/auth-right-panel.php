<?php
// The announcements shown in the carousel come from the announcement table (see AnnouncementModel::published()).
// This panel is used by the Login, Forgot-Password and Reset-Password pages, so it loads its own data.
require_once dirname(__DIR__, 2) . '/MyModules/Broadcast/Announcement/AnnouncementModel.php';
$panelAnnouncements = AnnouncementModel::published();
$panelCount         = count($panelAnnouncements);
?>
<aside class="d-none d-lg-flex col-lg-7 branding-column" aria-label="Maklumat dan Pengumuman Sistem ISISA">
    <!-- Hiasan Latar Abstrak CSS -->
    <div class="bg-decor-grid" aria-hidden="true"></div>
    <div class="bg-decor-orb-1" aria-hidden="true"></div>
    <div class="bg-decor-orb-2" aria-hidden="true"></div>
    <div class="bg-decor-leaf" aria-hidden="true"></div>

    <div class="branding-content animate-entry animate-delay-1">
        <div class="welcome-pill">
            <i class="bi bi-recycle" aria-hidden="true"></i>
            <span>Portal Bersepadu Kelestarian Bandar</span>
        </div>

        <h2 class="branding-heading">Informasi Sisa Shah Alam</h2>
        <p class="branding-desc">
            platform digital rasmi Majlis Bandaraya Shah Alam bagi pemantauan pengurusan sisa pepejal
        </p>

        <!-- Announcement Card Glassmorphism Besar -->
        <div class="glass-announcement-card">
            <div id="isisaAnnouncementCarousel" class="carousel slide" <?= $panelCount > 1 ? 'data-bs-ride="carousel" data-bs-interval="5000"' : '' ?>>

                <div class="carousel-inner">
                    <?php if ($panelCount === 0): ?>
                        <!-- Tiada pengumuman aktif -->
                        <div class="carousel-item active">
                            <div class="announcement-slide-body">
                                <div>
                                    <h3 class="announcement-title">Tiada pengumuman buat masa ini</h3>
                                    <p class="announcement-body">Pengumuman terkini akan dipaparkan di sini.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($panelAnnouncements as $i => $item): ?>
                        <div class="carousel-item<?= $i === 0 ? ' active' : '' ?>">
                            <div class="announcement-slide-body">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <span class="announcement-badge">
                                            <i class="bi bi-megaphone-fill" aria-hidden="true"></i> <?= e($item['type_description']) ?>
                                        </span>
                                        <span class="announcement-date"><?= e(formatDateLong($item['announcement_start_date'])) ?></span>
                                    </div>
                                    <h3 class="announcement-title"><?= e($item['announcement_title']) ?></h3>
                                    <?php if (trim((string) $item['announcement_description']) !== ''): ?>
                                        <p class="announcement-body"><?= e(mb_strimwidth((string) $item['announcement_description'], 0, 220, '...', 'UTF-8')) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($panelCount > 1): ?>
                    <!-- Kawalan Carousel Minimalis & Indicators Bentuk Pil (hanya jika lebih daripada satu pengumuman) -->
                    <div class="carousel-footer-controls">
                        <div class="carousel-indicators custom-carousel-indicators">
                            <?php for ($i = 0; $i < $panelCount; $i++): ?>
                                <button type="button" data-bs-target="#isisaAnnouncementCarousel" data-bs-slide-to="<?= $i ?>"<?= $i === 0 ? ' class="active" aria-current="true"' : '' ?> aria-label="Pengumuman <?= $i + 1 ?>"></button>
                            <?php endfor; ?>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button class="btn-carousel-nav" type="button" data-bs-target="#isisaAnnouncementCarousel" data-bs-slide="prev" aria-label="Pengumuman sebelumnya">
                                <i class="bi bi-chevron-left" aria-hidden="true"></i>
                            </button>
                            <button class="btn-carousel-nav" type="button" data-bs-target="#isisaAnnouncementCarousel" data-bs-slide="next" aria-label="Pengumuman seterusnya">
                                <i class="bi bi-chevron-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</aside>
