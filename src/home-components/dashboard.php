<!-- Dashboard -->
        <section class="sec" id="dashboard">
            <div class="container">
                <h2>Empat dashboard, satu sumber data</h2>
                <p class="sec-lead mb-3">Semua dashboard membaca daripada pangkalan data yang sama dan hanya mengira data yang telah disahkan.</p>
                <p class="dash-note"><i class="bi bi-info-circle" aria-hidden="true"></i> Contoh paparan, bukan data sebenar</p>

                <ul class="nav dash-tabs mt-4" id="dashTabs" role="tablist" aria-label="Pilih dashboard">
                    <li class="nav-item" role="presentation"><button class="nav-link active" id="t-eks" data-key="eks" data-bs-toggle="tab" data-bs-target="#p-eks" type="button" role="tab" aria-controls="p-eks" aria-selected="true">Eksekutif</button></li>
                    <li class="nav-item" role="presentation"><button class="nav-link" id="t-ops" data-key="ops" data-bs-toggle="tab" data-bs-target="#p-ops" type="button" role="tab" aria-controls="p-ops" aria-selected="false" tabindex="-1">Operasi dan data</button></li>
                    <li class="nav-item" role="presentation"><button class="nav-link" id="t-kpi" data-key="kpi" data-bs-toggle="tab" data-bs-target="#p-kpi" type="button" role="tab" aria-controls="p-kpi" aria-selected="false" tabindex="-1">KPI &amp; analitik</button></li>
                    <li class="nav-item" role="presentation"><button class="nav-link" id="t-prog" data-key="prog" data-bs-toggle="tab" data-bs-target="#p-prog" type="button" role="tab" aria-controls="p-prog" aria-selected="false" tabindex="-1">Program &amp; aktiviti</button></li>
                </ul>

                <div class="tab-content">

                    <!-- Eksekutif -->
                    <div class="tab-pane fade show active" id="p-eks" role="tabpanel" aria-labelledby="t-eks" tabindex="0">
                        <p class="dash-intro">Gambaran menyeluruh untuk keputusan strategik Datuk Bandar, pengurusan tertinggi dan Pengarah Jabatan.</p>
                        <div class="kpi-row">
                            <div class="kpi-tile"><span class="kpi-l">Sisa dilupuskan</span><span class="kpi-v">12,480<small>tan</small></span></div>
                            <div class="kpi-tile"><span class="kpi-l">Kadar kitar semula</span><span class="kpi-v">34<small>%</small></span></div>
                            <div class="kpi-tile"><span class="kpi-l">Kadar pengalihan daripada pelupusan</span><span class="kpi-v">41<small>%</small></span></div>
                            <div class="kpi-tile"><span class="kpi-l">Anggaran penjimatan karbon</span><span class="kpi-v">3,260<small>tCO₂e</small></span></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-lg-8">
                                <div class="cc">
                                    <h3>Prestasi mengikut bulan</h3>
                                    <p>Sisa dilupuskan, dikitar semula dan dikompos (tan), dengan kadar pengalihan (%)</p>
                                    <div class="cc-body tall"><canvas id="c-bulan" role="img" aria-label="Carta bar bertindan bulanan sisa dilupuskan, dikitar semula dan dikompos, dengan garisan kadar pengalihan yang meningkat daripada kira-kira 36 peratus pada Januari kepada 47 peratus pada Disember. Data contoh."></canvas></div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="cc">
                                    <h3>Prestasi mengikut zon</h3>
                                    <p>Bahagian sisa dilupuskan (%)</p>
                                    <div class="cc-body tall"><canvas id="c-zon" role="img" aria-label="Carta donat sisa dilupuskan mengikut enam zon, Zon 1 paling besar pada 24 peratus. Data contoh."></canvas></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="cc">
                                    <h3>Prestasi mengikut tahun</h3>
                                    <p>Tan, 2022 hingga 2025</p>
                                    <div class="cc-body"><canvas id="c-tahun" role="img" aria-label="Carta bar bertindan tahunan menunjukkan sisa dilupuskan menurun dan kitar semula meningkat dari 2022 hingga 2025. Data contoh."></canvas></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="cc">
                                    <h3>Prestasi mengikut sektor</h3>
                                    <p>Bahagian sisa dijana (%)</p>
                                    <div class="cc-body"><canvas id="c-sektor" role="img" aria-label="Carta donat sisa mengikut sektor: kediaman 48 peratus, komersial 27, institusi 10, perindustrian 9 dan pembinaan 6. Data contoh."></canvas></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="cc">
                                    <h3>Status pencapaian KPI berbanding sasaran</h3>
                                    <p>Ikon dan teks status melengkapi warna.</p>
                                    <div class="table-responsive">
                                        <table class="table kpi-table align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">KPI</th>
                                                    <th scope="col">Sasaran</th>
                                                    <th scope="col">Prestasi</th>
                                                    <th scope="col">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th scope="row">Kadar kitar semula</th>
                                                    <td>35%</td>
                                                    <td>34%</td>
                                                    <td><span class="status" style="--c:var(--amber)"><i class="bi bi-dash-circle-fill" aria-hidden="true"></i>Hampir</span></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Kadar pengalihan sisa daripada pelupusan</th>
                                                    <td>40%</td>
                                                    <td>41%</td>
                                                    <td><span class="status" style="--c:var(--lime)"><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Tercapai</span></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Janaan sisa per kapita (kg/orang/hari)</th>
                                                    <td>1.10 atau kurang</td>
                                                    <td>1.20</td>
                                                    <td><span class="status" style="--c:var(--rose)"><i class="bi bi-x-circle-fill" aria-hidden="true"></i>Belum tercapai</span></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Anggaran penjimatan karbon (tCO₂e)</th>
                                                    <td>3,000</td>
                                                    <td>3,260</td>
                                                    <td><span class="status" style="--c:var(--lime)"><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Tercapai</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Operasi dan data -->
                    <div class="tab-pane fade" id="p-ops" role="tabpanel" aria-labelledby="t-ops" tabindex="0">
                        <p class="dash-intro">Laporan bulanan sisa binaan (P&amp;P) daripada McSynergy, sebelum pengesahan. Sistem mengasingkan berat diterima, dipulihkan, dilupuskan dan baki.</p>
                        <div class="kpi-row">
                            <div class="kpi-tile"><span class="kpi-l">Jumlah trip</span><span class="kpi-v">694<small>trip</small></span></div>
                            <div class="kpi-tile"><span class="kpi-l">Jumlah berat diterima</span><span class="kpi-v">1,494<small>tan</small></span></div>
                            <div class="kpi-tile"><span class="kpi-l">Berat sisa P&amp;P</span><span class="kpi-v">1,464<small>tan</small></span></div>
                            <div class="kpi-tile"><span class="kpi-l">Berat sisa kayu</span><span class="kpi-v">30<small>tan</small></span></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-lg-5">
                                <div class="cc">
                                    <h3>Sumber sisa</h3>
                                    <p>Berat diterima mengikut asal (tan)</p>
                                    <div class="cc-body tall"><canvas id="c-sumber" role="img" aria-label="Carta donat sumber sisa: luar Shah Alam 1,088 tan (73 peratus), kawasan Shah Alam 348 tan (23 peratus) dan panel MBSA 58 tan (4 peratus). Data contoh."></canvas></div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="cc">
                                    <h3>Pecahan pengurusan sisa</h3>
                                    <p>Bahagian daripada 1,597 tan (%)</p>
                                    <div class="cc-body tall"><canvas id="c-urus" role="img" aria-label="Carta bar mendatar: batu 83.1 peratus, sisa pelupusan 15.9 peratus, besi 0.8 peratus, kotak 0.1, plastik 0.1 dan kayu 0.0. Data contoh."></canvas></div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="cc">
                                    <h3>Berat diterima mengikut bulan</h3>
                                    <p>Tan, Januari hingga September</p>
                                    <div class="cc-body"><canvas id="c-terima" role="img" aria-label="Carta bar berat diterima bulanan, meningkat daripada 1,240 tan pada Januari kepada 1,494 tan pada September. Data contoh."></canvas></div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="cc">
                                    <h3>Rekonsiliasi berat sebelum pengesahan</h3>
                                    <p>Rekod tidak boleh disahkan jika jumlah tidak sepadan tanpa penjelasan.</p>
                                    <table class="table kpi-table align-middle mb-0">
                                        <tbody>
                                            <tr>
                                                <th scope="row">Berat diterima</th>
                                                <td class="num">1,494 tan</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Bahan dipulihkan</th>
                                                <td class="num">1,343 tan</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Sisa dilupuskan</th>
                                                <td class="num">254 tan</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Jumlah pengurusan sisa</th>
                                                <td class="num">1,597 tan</td>
                                            </tr>
                                            <tr class="diff">
                                                <th scope="row"><i class="bi bi-exclamation-triangle me-2" aria-hidden="true"></i>Beza, perlu dijelaskan</th>
                                                <td class="num">103 tan</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KPI dan analitik -->
                    <div class="tab-pane fade" id="p-kpi" role="tabpanel" aria-labelledby="t-kpi" tabindex="0">
                        <p class="dash-intro">Penunjuk dikira automatik daripada data yang telah disahkan, supaya trend boleh dibandingkan antara bulan.</p>
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <div class="cc">
                                    <h3>Kadar kitar semula dan pengalihan</h3>
                                    <p>Peratus daripada jumlah sisa dijana, berbanding sasaran pengalihan 40%</p>
                                    <div class="cc-body tall"><canvas id="c-kadar" role="img" aria-label="Carta garisan kadar kitar semula (kira-kira 30 hingga 39 peratus) dan kadar pengalihan (36 hingga 47 peratus) yang meningkat sepanjang tahun, melepasi sasaran pengalihan 40 peratus dari sekitar bulan Mei. Data contoh."></canvas></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="cc">
                                    <h3>Janaan sisa per kapita</h3>
                                    <p>kg/orang/hari</p>
                                    <div class="cc-body tall"><canvas id="c-kapita" role="img" aria-label="Carta garisan janaan sisa per kapita yang menurun perlahan daripada 1.24 kepada 1.16 kg per orang sehari. Data contoh."></canvas></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="cc">
                                    <h3>Trend sisa mengikut kategori</h3>
                                    <p>Tan sebulan, kawasan bertindan</p>
                                    <div class="cc-body tall"><canvas id="c-kategori" role="img" aria-label="Carta kawasan bertindan sisa dijana mengikut kategori: sisa makanan, plastik, kertas dan lain-lain. Sisa makanan berkurang manakala plastik dan kertas meningkat. Data contoh."></canvas></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="cc">
                                    <h3>Unjuran janaan sisa</h3>
                                    <p>Jumlah sisa dijana setahun (tan), sejarah dan dua senario unjuran</p>
                                    <div class="cc-body tall"><canvas id="c-unjuran" role="img" aria-label="Carta garisan jumlah sisa dijana dari 2020 hingga 2025 (sejarah) dan unjuran hingga 2030 mengikut dua senario: trend semasa naik kira-kira 1.5 peratus setahun dan senario pengurangan naik 0.5 peratus setahun. Data contoh."></canvas></div>
                                </div>
                            </div>
                        </div>
                        <p class="dash-foot">Angka karbon dilabel sebagai anggaran sehingga metodologi diterima pakai.</p>
                    </div>

                    <!-- Program dan aktiviti -->
                    <div class="tab-pane fade" id="p-prog" role="tabpanel" aria-labelledby="t-prog" tabindex="0">
                        <p class="dash-intro">Program dan aktiviti jabatan, dengan bukti pelaksanaan dan laporan seragam yang boleh ditapis.</p>
                        <div class="kpi-row">
                            <div class="kpi-tile"><span class="kpi-l">Jumlah program</span><span class="kpi-v">128</span></div>
                            <div class="kpi-tile"><span class="kpi-l">Sedang dilaksanakan</span><span class="kpi-v">48</span></div>
                            <div class="kpi-tile"><span class="kpi-l">Selesai</span><span class="kpi-v">36</span></div>
                            <div class="kpi-tile"><span class="kpi-l">Laporan disahkan</span><span class="kpi-v">72</span></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-lg-4">
                                <div class="cc">
                                    <h3>Status program</h3>
                                    <p>Bilangan program</p>
                                    <div class="cc-body"><canvas id="c-status" role="img" aria-label="Carta donat status program: dirancang 32, sedang dilaksanakan 48, selesai 36 dan ditangguhkan 12. Data contoh."></canvas></div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="cc">
                                    <h3>Status laporan program</h3>
                                    <p>Bilangan laporan</p>
                                    <div class="cc-body"><canvas id="c-laporan" role="img" aria-label="Carta bar mendatar status laporan: draf 18, untuk semakan 24, perlu pembetulan 14 dan disahkan 72. Data contoh."></canvas></div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="cc">
                                    <h3>Pencapaian sasaran</h3>
                                    <p>Program berbanding sasaran tahunan</p>
                                    <div class="big-n">85<small>daripada sasaran 100</small></div>
                                    <div class="progress mt-4" role="progressbar" aria-label="Pencapaian sasaran" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width:85%"></div>
                                    </div>
                                    <p class="dash-foot mb-0">85% daripada sasaran program tahun ini.</p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="cc">
                                    <h3>Trend aktiviti</h3>
                                    <p>Bilangan program mengikut bulan</p>
                                    <div class="cc-body"><canvas id="c-aktiviti" role="img" aria-label="Carta bar bilangan program mengikut bulan, meningkat daripada 5 pada Januari kepada kemuncak 16 pada November. Data contoh."></canvas></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>
