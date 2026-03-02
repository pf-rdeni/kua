<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display - <?= esc($namaMasjid) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/display/css/display-style.css') ?>">
    <style>
        /* Khusus template klasik - override tambahan */
        .template-klasik .jadwal-table-lg .waktu-icon {
            width: 30px; text-align: center; color: rgba(255,255,255,0.5);
        }
    </style>
</head>
<body>
<div class="display-container template-klasik <?= ($display['orientasi'] ?? 'horizontal') === 'vertikal' ? 'orientasi-vertikal' : '' ?>"
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
    <div class="display-content">

        <!-- ============================================================ -->
        <!-- HEADER: Logo + Nama Masjid + Tanggal -->
        <!-- ============================================================ -->
        <div class="display-header">
            <div class="header-left">
                <?php if (!empty($display['logo'])): ?>
                    <img src="<?= base_url($display['logo']) ?>" alt="Logo" class="header-logo">
                <?php else: ?>
                    <div class="header-logo d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #059669, #10b981); display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-mosque" style="font-size: 1.8rem; color: #fff;"></i>
                    </div>
                <?php endif; ?>
                <div class="header-info">
                    <h1><?= esc($namaMasjid) ?></h1>
                    <?php if (!empty($alamatDisplay)): ?>
                        <div class="alamat"><?= esc($alamatDisplay) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="header-right">
                <div class="tanggal" id="tanggal-masehi">Memuat...</div>
                <div class="tanggal-hijriah" id="tanggal-hijriah"></div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- BODY: Jam | Jadwal Sholat | Info Kegiatan -->
        <!-- ============================================================ -->
        <div class="display-body">
            <!-- PANEL KIRI: Jam Digital + Countdown -->
            <div class="panel-kiri">
                <div class="jam-container">
                    <div class="jam-digital" id="jam-digital">00:00:00</div>
                </div>

                <!-- Countdown ke waktu sholat berikutnya -->
                <div class="countdown-container">
                    <div class="countdown-label" id="countdown-label">Menuju Sholat</div>
                    <div class="countdown-waktu" id="countdown-waktu">00:00:00</div>
                    <div class="countdown-progress-bar">
                        <div class="progress-fill" id="countdown-progress" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Status koneksi -->
                <div style="text-align:center; margin-top: 10px;">
                    <span id="status-koneksi" class="status-online" style="font-size:0.7rem; opacity:0.5;">
                        <i class="fas fa-wifi"></i>
                    </span>
                </div>
            </div>

            <!-- PANEL TENGAH: Tabel Jadwal Sholat -->
            <div class="panel-tengah">
                <div class="jadwal-container" style="flex:1;">
                    <div class="jadwal-title">
                        <i class="fas fa-clock mr-1"></i> Jadwal Sholat Hari Ini
                    </div>
                    <table class="jadwal-table jadwal-table-lg">
                        <tr class="jadwal-row" data-waktu="imsak">
                            <td class="waktu-icon"><i class="fas fa-moon"></i></td>
                            <td class="waktu-nama">Imsak</td>
                            <td class="waktu-jam" id="jadwal-imsak">--:--</td>
                        </tr>
                        <tr class="jadwal-row" data-waktu="subuh">
                            <td class="waktu-icon"><i class="fas fa-cloud-moon"></i></td>
                            <td class="waktu-nama">Subuh</td>
                            <td class="waktu-jam" id="jadwal-subuh">--:--</td>
                        </tr>
                        <tr class="jadwal-row" data-waktu="terbit">
                            <td class="waktu-icon"><i class="fas fa-sun"></i></td>
                            <td class="waktu-nama">Terbit</td>
                            <td class="waktu-jam" id="jadwal-terbit">--:--</td>
                        </tr>
                        <tr class="jadwal-row" data-waktu="dzuhur">
                            <td class="waktu-icon"><i class="fas fa-sun" style="color:#fbbf24"></i></td>
                            <td class="waktu-nama">Dzuhur</td>
                            <td class="waktu-jam" id="jadwal-dzuhur">--:--</td>
                        </tr>
                        <tr class="jadwal-row" data-waktu="ashar">
                            <td class="waktu-icon"><i class="fas fa-cloud-sun"></i></td>
                            <td class="waktu-nama">Ashar</td>
                            <td class="waktu-jam" id="jadwal-ashar">--:--</td>
                        </tr>
                        <tr class="jadwal-row" data-waktu="maghrib">
                            <td class="waktu-icon"><i class="fas fa-cloud-moon" style="color:#f97316"></i></td>
                            <td class="waktu-nama">Maghrib</td>
                            <td class="waktu-jam" id="jadwal-maghrib">--:--</td>
                        </tr>
                        <tr class="jadwal-row" data-waktu="isya">
                            <td class="waktu-icon"><i class="fas fa-star-and-crescent"></i></td>
                            <td class="waktu-nama">Isya</td>
                            <td class="waktu-jam" id="jadwal-isya">--:--</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- PANEL KANAN: Info Kegiatan / Slide -->
            <div class="panel-kanan">
                <div class="info-panel" style="flex:1;">
                    <h3><i class="fas fa-bullhorn mr-1"></i> Informasi</h3>
                    <div id="info-kegiatan-container">
                        <?php if (!empty($kontenByTipe['info_kegiatan'])): ?>
                            <?php foreach ($kontenByTipe['info_kegiatan'] as $info): ?>
                                <div class="info-item">
                                    <h4><?= esc($info['judul'] ?? '') ?></h4>
                                    <p><?= $info['konten'] ?? '' ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!empty($kontenByTipe['pengumuman'])): ?>
                            <?php foreach ($kontenByTipe['pengumuman'] as $peng): ?>
                                <div class="info-item">
                                    <h4><i class="fas fa-megaphone mr-1"></i> <?= esc($peng['judul'] ?? '') ?></h4>
                                    <p><?= $peng['konten'] ?? '' ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (empty($kontenByTipe['info_kegiatan']) && empty($kontenByTipe['pengumuman'])): ?>
                            <div class="info-item">
                                <p style="color: rgba(255,255,255,0.4); text-align: center; padding: 20px 0;">
                                    <i class="fas fa-info-circle"></i><br>
                                    Belum ada informasi
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Gambar Slide (jika ada) -->
                <?php if (!empty($kontenByTipe['gambar_slide'])): ?>
                <div class="slide-container" style="min-height: 200px;">
                    <?php foreach ($kontenByTipe['gambar_slide'] as $idx => $slide): ?>
                        <div class="display-slide <?= $idx === 0 ? 'slide-active' : '' ?>">
                            <?php if (!empty($slide['gambar'])): ?>
                                <img src="<?= base_url($slide['gambar']) ?>" alt="<?= esc($slide['judul'] ?? '') ?>" class="slide-image">
                            <?php endif; ?>
                            <?php if (!empty($slide['judul'])): ?>
                                <div class="slide-text" style="position:absolute; bottom:10px; background:rgba(0,0,0,0.6); border-radius:8px; padding:10px 15px;">
                                    <h3 style="font-size:1rem; margin:0;"><?= esc($slide['judul']) ?></h3>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <div class="slide-indicators">
                        <?php foreach ($kontenByTipe['gambar_slide'] as $idx => $slide): ?>
                            <div class="slide-indicator <?= $idx === 0 ? 'active' : '' ?>"></div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
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

    </div>
</div>

<!-- Scripts -->
<script src="<?= base_url('assets/display/js/praytimes.js') ?>"></script>
<script src="<?= base_url('assets/display/js/display-engine.js') ?>"></script>
<script>
    // Inisialisasi Display Engine
    document.addEventListener('DOMContentLoaded', function() {
        DisplayEngine.init({
            displayId: <?= (int)$display['id'] ?>,
            apiUrl: '<?= base_url('display/api/' . $display['id']) ?>',
            apiKeuanganUrl: '<?= base_url('display/api_keuangan/' . $display['id']) ?>',
            apiCheckUpdateUrl: '<?= base_url("display/check_update/" . $display['id']) ?>',
            latitude: <?= (float)$latitude ?>,
            longitude: <?= (float)$longitude ?>,
            timezone: 7, // WIB
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
