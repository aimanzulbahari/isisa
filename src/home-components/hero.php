<!-- Hero -->
        <header class="hero" id="atas">
            <div class="container">
                <div class="row align-items-center gy-5 gx-lg-5">
                    <div class="col-lg-6">
                        <p class="pill"><i class="bi bi-patch-check" aria-hidden="true"></i> Sistem pengurusan data sisa pepejal</p>
                        <h1>Data sisa Shah Alam, dalam satu platform</h1>
                        <p class="lead">ISISA membantu Majlis Bandaraya Shah Alam memantau dan mengurus data sisa pepejal serta aktiviti ekonomi kitaran, supaya keputusan dibuat berdasarkan data.</p>
                        <div class="d-flex flex-wrap gap-3">
                            <a class="btn btn-isisa btn-lg" href="<?= e($loginHref) ?>"><i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i><?= e($loginText) ?></a>
                            <a class="btn btn-ghost btn-lg" href="#dashboard">Lihat dashboard</a>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="deck" role="img" aria-label="Contoh paparan Dashboard Eksekutif ISISA: empat kad KPI, carta bar kadar pengalihan sisa bulanan dan carta donat sisa mengikut sektor. Semua angka ialah contoh, bukan data sebenar.">
                            <div class="deck-layer l3"></div>
                            <div class="deck-layer l2"></div>
                            <div class="deck-main">
                                <div class="deck-top">
                                    <span class="deck-title"><i class="bi bi-speedometer2"></i>Dashboard eksekutif</span>
                                    <span class="deck-tag">Contoh paparan</span>
                                </div>
                                <div class="kpis">
                                    <div class="kpi"><span class="kpi-l">Sisa dilupuskan</span><span class="kpi-v"><b data-count="12480">12,480</b><small>tan</small></span></div>
                                    <div class="kpi"><span class="kpi-l">Kadar kitar semula</span><span class="kpi-v"><b data-count="34">34</b><small>%</small></span></div>
                                    <div class="kpi"><span class="kpi-l">Kadar pengalihan</span><span class="kpi-v"><b data-count="41">41</b><small>%</small></span></div>
                                    <div class="kpi"><span class="kpi-l">Anggaran penjimatan karbon</span><span class="kpi-v"><b data-count="3260">3,260</b><small>tCO₂e</small></span></div>
                                </div>
                                <div class="deck-grid">
                                    <div>
                                        <span class="chart-t">Kadar pengalihan sisa mengikut bulan</span>
                                        <div class="bars">
                                            <i style="--h:38%;--i:0"></i><i style="--h:41%;--i:1"></i><i style="--h:40%;--i:2"></i><i style="--h:44%;--i:3"></i>
                                            <i style="--h:47%;--i:4"></i><i style="--h:46%;--i:5"></i><i style="--h:51%;--i:6"></i><i style="--h:54%;--i:7"></i>
                                            <i style="--h:53%;--i:8"></i><i style="--h:58%;--i:9"></i><i style="--h:61%;--i:10"></i><i style="--h:64%;--i:11"></i>
                                        </div>
                                        <div class="months"><span>J</span><span>F</span><span>M</span><span>A</span><span>M</span><span>J</span><span>J</span><span>O</span><span>S</span><span>O</span><span>N</span><span>D</span></div>
                                    </div>
                                    <div class="donut-wrap">
                                        <div class="donut"></div>
                                        <ul class="leg">
                                            <li style="--c:var(--lime)">Kediaman</li>
                                            <li style="--c:var(--sky)">Komersial</li>
                                            <li style="--c:var(--amber)">Institusi</li>
                                            <li style="--c:var(--violet)">Industri</li>
                                        </ul>
                                    </div>
                                </div>
                                <p class="deck-note">Semua angka di sini ialah contoh, bukan data sebenar.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
