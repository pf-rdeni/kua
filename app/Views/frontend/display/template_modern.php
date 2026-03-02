<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display - <?= esc($namaMasjid) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/display/css/display-style.css') ?>">
    <style>
        /* Override khusus template modern */
        .template-modern .smart-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 25px;
            background: rgba(0,0,0,0.4);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(74,222,128,0.2);
        }
        .template-modern .smart-bar .masjid-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #4ade80;
        }
        .template-modern .smart-bar .smart-clock {
            font-size: 2.5rem; /* Ukuran jam diperbesar */
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            color: #ffffff;
            line-height: 1;
        }
        .template-modern .running-text-content {
            font-size: 1.5rem; /* Ukuran teks berjalan diperbesar */
            font-weight: 600;
        }
        .template-modern .smart-bar .smart-date {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.7);
        }
        .template-modern .slide-overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            padding: 30px 25px 20px;
        }
        .template-modern .slide-overlay h2 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }
        .template-modern .slide-overlay p {
            font-size: 1rem;
            color: rgba(255,255,255,0.8);
            line-height: 1.5;
        }
        .template-modern .jadwal-mini {
            background: rgba(0,0,0,0.25);
            border-radius: 12px;
            padding: 12px;
            border: 1px solid rgba(255,255,255,0.08);
        }
        .template-modern .jadwal-mini .jadwal-compact td {
            padding: 6px 10px;
            font-size: 1.5rem; /* Diperbesar 2x lipat */
            font-weight: 600;
        }
        .template-modern .jadwal-mini .jadwal-compact .waktu-jam {
            font-size: 1.5rem; /* Diperbesar 2x lipat */
            font-weight: 700;
        }
    </style>
</head>
<body>
<div class="display-container template-modern <?= ($display['orientasi'] ?? 'horizontal') === 'vertikal' ? 'orientasi-vertikal' : '' ?>"
     <?php
$logo = !empty($display['logo']) ? base_url($display['logo']) : base_url('assets/img/logo-kemenag.png');
$wallpaper = !empty($display['wallpaper']) ? base_url($display['wallpaper']) : base_url('assets/img/default-masjid.jpg');

// Parse opsi waktu sholat
$opsiWaktu = !empty($display['opsi_waktu_sholat']) ? json_decode($display['opsi_waktu_sholat'], true) : [];
$opsiQobliyah = $opsiWaktu['qobliyah'] ?? ['subuh'=>1, 'dzuhur'=>1, 'ashar'=>0, 'maghrib'=>1, 'isya'=>1];
$opsiBadiyah  = $opsiWaktu['badiyah']  ?? ['subuh'=>0, 'dzuhur'=>1, 'ashar'=>0, 'maghrib'=>1, 'isya'=>1];
?>
     style="background-image: url('<?= $wallpaper ?>')"
     >
    <div class="display-overlay"></div>
    
    <!-- WRAPPER UNTUK AUTO-SCALING PROPORSI LAYAR TV -->
    <div id="display-scaler" class="display-scale-wrapper">
        <div class="display-content">

        <!-- ============================================================ -->
        <!-- SMART BAR: Nama Masjid + Jam + Tanggal -->
        <!-- ============================================================ -->
        <div class="smart-bar">
            <div style="display:flex; align-items:center; gap:12px;">
                <?php if (!empty($display['logo'])): ?>
                    <img src="<?= base_url($display['logo']) ?>" alt="Logo" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                <?php else: ?>
                    <i class="fas fa-mosque" style="font-size:1.5rem; color:#4ade80;"></i>
                <?php endif; ?>
                <div>
                    <div class="masjid-name"><?= esc($namaMasjid) ?></div>
                    <?php if (!empty($alamatDisplay)): ?>
                        <div style="font-size: 0.8rem; color: #ffffff;"><?= esc($alamatDisplay) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div style="text-align:center;">
                <div class="smart-clock" id="jam-digital">00:00:00</div>
            </div>
            <div style="text-align:right;">
                <div class="smart-date" id="tanggal-masehi">Memuat...</div>
                <div style="font-size:0.8rem; color:#4ade80;" id="tanggal-hijriah"></div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- BODY: Slide Utama | Sidebar Jadwal -->
        <!-- ============================================================ -->
        <div class="display-body">
            <!-- PANEL UTAMA: Full Slide Konten -->
            <div class="panel-utama">
                <?php
                // Gabungkan semua konten yang bisa ditampilkan sebagai slide
                $allSlides = [];
                if (!empty($kontenByTipe['gambar_slide'])) $allSlides = array_merge($allSlides, $kontenByTipe['gambar_slide']);
                if (!empty($kontenByTipe['info_kegiatan'])) $allSlides = array_merge($allSlides, $kontenByTipe['info_kegiatan']);
                if (!empty($kontenByTipe['pengumuman'])) $allSlides = array_merge($allSlides, $kontenByTipe['pengumuman']);
                ?>

                <?php if (!empty($allSlides)): ?>
                    <?php foreach ($allSlides as $idx => $slide): ?>
                    <div class="display-slide <?= $idx === 0 ? 'slide-active' : '' ?>" style="background: rgba(0,0,0,0.3); border-radius:12px;">
                        <?php if (!empty($slide['gambar'])): ?>
                            <img src="<?= base_url($slide['gambar']) ?>" alt="" class="slide-image" style="position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; border-radius:12px;">
                        <?php endif; ?>
                        <div class="slide-overlay">
                            <?php if (!empty($slide['judul'])): ?>
                                <h2><?= esc($slide['judul']) ?></h2>
                            <?php endif; ?>
                            <?php if (!empty($slide['konten'])): ?>
                                <p><?= mb_substr(strip_tags($slide['konten']), 0, 200) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <div class="slide-indicators">
                        <?php foreach ($allSlides as $idx => $s): ?>
                            <div class="slide-indicator <?= $idx === 0 ? 'active' : '' ?>"></div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="display-slide slide-active" style="background: rgba(0,0,0,0.2); border-radius:12px;">
                        <div style="text-align:center;">
                            <i class="fas fa-mosque" style="font-size:5rem; color:rgba(74,222,128,0.3); margin-bottom:20px;"></i>
                            <h2 style="color:#4ade80; font-size:2rem;"><?= esc($namaMasjid) ?></h2>
                            <?php if (!empty($alamatDisplay)): ?>
                                <p style="color:rgba(255,255,255,0.5); margin-top:8px;"><?= esc($alamatDisplay) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- PANEL SIDEBAR: Jadwal + Countdown -->
            <div class="panel-sidebar">
                <!-- Countdown -->
                <div class="countdown-container">
                    <div class="countdown-label" id="countdown-label">Menuju Sholat</div>
                    <div class="countdown-waktu countdown-waktu-small" id="countdown-waktu">00:00:00</div>
                    <div class="countdown-progress-bar">
                        <div class="progress-fill" id="countdown-progress" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Jadwal Sholat Mini -->
                <div class="jadwal-mini" style="flex:1;">
                    <div style="text-align:center; margin-bottom:8px; font-weight:600; color:#4ade80; font-size:0.85rem; text-transform:uppercase; letter-spacing:1px;">
                        <i class="fas fa-clock mr-1"></i> Jadwal Sholat
                    </div>
                    <table class="jadwal-table jadwal-compact" style="width:100%;">
                        <tr class="jadwal-row" data-waktu="imsak"><td class="waktu-nama">Imsak</td><td class="waktu-jam" id="jadwal-imsak">--:--</td></tr>
                        <tr class="jadwal-row" data-waktu="subuh"><td class="waktu-nama">Subuh</td><td class="waktu-jam" id="jadwal-subuh">--:--</td></tr>
                        <tr class="jadwal-row" data-waktu="terbit"><td class="waktu-nama">Terbit</td><td class="waktu-jam" id="jadwal-terbit">--:--</td></tr>
                        <tr class="jadwal-row" data-waktu="dzuhur"><td class="waktu-nama">Dzuhur</td><td class="waktu-jam" id="jadwal-dzuhur">--:--</td></tr>
                        <tr class="jadwal-row" data-waktu="ashar"><td class="waktu-nama">Ashar</td><td class="waktu-jam" id="jadwal-ashar">--:--</td></tr>
                        <tr class="jadwal-row" data-waktu="maghrib"><td class="waktu-nama">Maghrib</td><td class="waktu-jam" id="jadwal-maghrib">--:--</td></tr>
                        <tr class="jadwal-row" data-waktu="isya"><td class="waktu-nama">Isya</td><td class="waktu-jam" id="jadwal-isya">--:--</td></tr>
                    </table>
                </div>

                <!-- Info Pengumuman (jika cukup ruang) -->
                <div id="pengumuman-container" class="info-panel" style="max-height: 150px; overflow-y: auto;">
                    <h3 style="font-size:0.8rem;"><i class="fas fa-bell mr-1"></i> Pengumuman</h3>
                    <?php if (!empty($kontenByTipe['pengumuman'])): ?>
                        <?php foreach (array_slice($kontenByTipe['pengumuman'], 0, 3) as $p): ?>
                        <div class="info-item" style="padding:5px 0;">
                            <h4 style="font-size:0.8rem;"><?= esc($p['judul'] ?? '') ?></h4>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color:rgba(255,255,255,0.3); font-size:0.8rem; text-align:center;">-</p>
                    <?php endif; ?>
                </div>

                <!-- Status -->
                <div style="text-align:center;">
                    <span id="status-koneksi" class="status-online" style="font-size:0.65rem; opacity:0.4;">
                        <i class="fas fa-wifi"></i>
                    </span>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- FOOTER: Running Text -->
        <!-- ============================================================ -->
        <div class="display-footer">
            <div class="running-text-wrapper">
                <span class="running-text-content" id="running-text-content"><?= esc($display['running_text'] ?? 'Selamat datang di ' . $namaMasjid) ?></span>
            </div>
        </div>

        </div> <!-- End display-content -->
    </div> <!-- End display-scaler -->
</div> <!-- End display-container -->

<!-- Scripts -->
<script src="<?= base_url('assets/display/js/praytimes.js') ?>"></script>
<script src="<?= base_url('assets/display/js/display-engine.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        DisplayEngine.init({
            displayId: <?= (int)$display['id'] ?>,
            apiUrl: '<?= base_url('display/api/' . $display['id']) ?>',
            apiKeuanganUrl: '<?= base_url('display/api_keuangan/' . $display['id']) ?>',
            apiCheckUpdateUrl: '<?= base_url("display/check_update/" . $display['id']) ?>',
            latitude: <?= (float)$latitude ?>,
            longitude: <?= (float)$longitude ?>,
            timezone: 7,
            metode: '<?= esc($display['metode_hitung'] ?? 'Kemenag') ?>',
            intervalSync: <?= (int)($display['interval_sync'] ?? 60) ?>,
            intervalSlide: 8,
            koreksi: {
                subuh: <?= (int)($display['koreksi_subuh'] ?? 0) ?>,
                dzuhur: <?= (int)($display['koreksi_dzuhur'] ?? 0) ?>,
                ashar: <?= (int)($display['koreksi_ashar'] ?? 0) ?>,
                maghrib: <?= (int)($display['koreksi_maghrib'] ?? 0) ?>,
                isya: <?= (int)($display['koreksi_isya'] ?? 0) ?>
            },
            iqomah: {
                subuh: <?= (int)($display['durasi_iqomah_subuh'] ?? 10) ?>,
                dzuhur: <?= (int)($display['durasi_iqomah_dzuhur'] ?? 10) ?>,
                ashar: <?= (int)($display['durasi_iqomah_ashar'] ?? 10) ?>,
                maghrib: <?= (int)($display['durasi_iqomah_maghrib'] ?? 5) ?>,
                isya: <?= (int)($display['durasi_iqomah_isya'] ?? 10) ?>
            },
            sholatJumat: <?= ($display['sholat_jumat'] ?? 1) ? 'true' : 'false' ?>,
            modeSholat: {
                menjelangAdzan: { aktif: <?= (int)($display['mode_menjelang_adzan'] ?? 1) ?>, durasi: <?= (int)($display['durasi_menjelang_adzan'] ?? 10) ?>, gambar: <?= !empty($display['gambar_menjelang_adzan']) ? "'" . base_url($display['gambar_menjelang_adzan']) . "'" : 'null' ?> },
                adzan:          { aktif: <?= (int)($display['mode_adzan'] ?? 1) ?>, durasi: <?= (int)($display['durasi_adzan'] ?? 7) ?>, gambar: <?= !empty($display['gambar_adzan']) ? "'" . base_url($display['gambar_adzan']) . "'" : 'null' ?> },
                qobliyah:       { aktif: <?= (int)($display['mode_qobliyah'] ?? 0) ?>, durasi: <?= (int)($display['durasi_qobliyah'] ?? 5) ?>, gambar: <?= !empty($display['gambar_qobliyah']) ? "'" . base_url($display['gambar_qobliyah']) . "'" : 'null' ?>, opsi: <?= json_encode($opsiQobliyah) ?> },
                sholat:         { aktif: <?= (int)($display['mode_sholat'] ?? 1) ?>, durasi: <?= (int)($display['durasi_sholat'] ?? 15) ?>, gambar: <?= !empty($display['gambar_sholat']) ? "'" . base_url($display['gambar_sholat']) . "'" : 'null' ?> },
                badiyah:        { aktif: <?= (int)($display['mode_badiyah'] ?? 0) ?>, durasi: <?= (int)($display['durasi_badiyah'] ?? 5) ?>, gambar: <?= !empty($display['gambar_badiyah']) ? "'" . base_url($display['gambar_badiyah']) . "'" : 'null' ?>, opsi: <?= json_encode($opsiBadiyah) ?> },
                tarawih:        { aktif: <?= (int)($display['mode_tarawih'] ?? 0) ?>, durasi: <?= (int)($display['durasi_tarawih'] ?? 60) ?>, gambar: <?= !empty($display['gambar_tarawih']) ? "'" . base_url($display['gambar_tarawih']) . "'" : 'null' ?> },
                idulAdha:       { aktif: <?= (int)($display['mode_idul_adha'] ?? 0) ?>, durasi: <?= (int)($display['durasi_idul_adha'] ?? 60) ?>, tanggal: <?= !empty($display['tanggal_idul_adha']) ? "'" . $display['tanggal_idul_adha'] . "'" : 'null' ?>, gambar: <?= !empty($display['gambar_idul_adha']) ? "'" . base_url($display['gambar_idul_adha']) . "'" : 'null' ?> },
                idulFitri:      { aktif: <?= (int)($display['mode_idul_fitri'] ?? 0) ?>, durasi: <?= (int)($display['durasi_idul_fitri'] ?? 60) ?>, tanggal: <?= !empty($display['tanggal_idul_fitri']) ? "'" . $display['tanggal_idul_fitri'] . "'" : 'null' ?>, gambar: <?= !empty($display['gambar_idul_fitri']) ? "'" . base_url($display['gambar_idul_fitri']) . "'" : 'null' ?> }
            }
        });
    });
    // Register Service Worker untuk Web Offline App (PWA)
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('<?= base_url('sw-display.js') ?>', { scope: '/' })
                .then(function(registration) {
                    console.log('[ServiceWorker] Registrasi berhasil dengan scope:', registration.scope);
                })
                .catch(function(error) {
                    console.error('[ServiceWorker] Registrasi gagal:', error);
                });
        });
    }
</script>
</body>
</html>
