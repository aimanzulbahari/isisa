<!-- Program akan datang & berita Facebook -->
        <section class="sec" id="program">
            <div class="container">
                <div class="row gy-5 gx-lg-5">
                    <div class="col-lg-7">
                        <div class="feed-head">
                            <h2 class="feed-title"><i class="bi bi-calendar3" aria-hidden="true"></i>Program Akan Datang</h2>
                            <?php if ($upcoming !== []): ?><span class="feed-count"><?= e(count($upcoming)) ?> Program</span><?php endif; ?>
                        </div>
                        <?php if ($upcoming === []): ?>
                            <p class="feed-empty">Tiada program akan datang buat masa ini.</p>
                        <?php endif; ?>
                        <ul class="events">
                            <?php foreach ($upcoming as $ev): ?>
                                <li class="event<?= $ev['featured'] ? ' event-featured' : '' ?>">
                                    <div class="event-date" aria-hidden="true">
                                        <span class="event-month"><?= e($ev['month']) ?></span>
                                        <span class="event-day"><?= e($ev['day']) ?></span>
                                        <span class="event-year"><?= e($ev['year']) ?></span>
                                    </div>
                                    <div class="event-body">
                                        <div class="event-tags">
                                            <span class="tag tag-<?= e($ev['tone']) ?>"><?= e($ev['tag']) ?></span>
                                            <?php if ($ev['featured'] && $ev['featured_label'] !== ''): ?>
                                                <span class="tag tag-star"><i class="bi bi-star-fill" aria-hidden="true"></i><?= e($ev['featured_label']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <h3 class="event-title"><?= e($ev['title']) ?></h3>
                                        <p class="event-meta">
                                            <span><i class="bi bi-clock" aria-hidden="true"></i><?= e($ev['time']) ?></span>
                                            <span><i class="bi bi-geo-alt-fill" aria-hidden="true"></i><?= e($ev['place']) ?></span>
                                        </p>
                                        <p class="event-desc"><?= e($ev['desc']) ?></p>
                                    </div>
                                    <span class="event-status<?= $ev['today'] ? ' event-status-today' : '' ?>"><?= e($ev['status']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="col-lg-5">
                        <div class="feed-head">
                            <h2 class="feed-title"><i class="bi bi-facebook" aria-hidden="true"></i>Berita &amp; Aktiviti</h2>
                            <span class="feed-count">Terkini</span>
                        </div>
                        <!-- Kad Facebook: iframe dibina oleh public/assets/js/home-js/modules/facebook-feed.js apabila kad kelihatan -->
                        <div class="fb-card"<?= $facebookPageUrl !== '' ? ' data-fb-page="' . e($facebookPageUrl) . '"' : '' ?>>
                            <div class="fb-frame" data-fb-frame>
                                <?php if ($facebookPageUrl !== ''): ?>
                                    <p class="fb-placeholder">Memuatkan berita&hellip;</p>
                                <?php else: ?>
                                    <p class="fb-placeholder"><i class="bi bi-facebook" aria-hidden="true"></i>Halaman Facebook belum ditetapkan.</p>
                                <?php endif; ?>
                            </div>
                            <?php if ($facebookPageUrl !== ''): ?>
                                <a class="fb-link" href="<?= e($facebookPageUrl) ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>Buka di Facebook</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
