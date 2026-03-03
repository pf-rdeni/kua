<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display - <?= esc($namaMasjid) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/display/css/display-style.css') ?>">
    <style>
        /* ============================================================
           TEMPLATE MODERN 2 - Vertical Prayer List + Clock
           Inspirasi: Jadwal vertikal kiri (warna-warni), jam besar kanan
           ============================================================ */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
            width: 100vw;
            height: 100vh;
            background: #0a0e1a;
        }

        .m2-container {
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .m2-bg {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-size: cover;
            background-position: center;
            z-index: 0;
        }
        .m2-bg::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(90deg,
                rgba(10,14,26,0.92) 0%,
                rgba(10,14,26,0.75) 35%,
                rgba(10,14,26,0.5) 100%);
        }

        /* === MAIN BODY (2 panels) === */
        .m2-body {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            overflow: hidden;
        }

        /* === LEFT PANEL: Prayer Times Vertical === */
        .m2-left {
            width: 320px;
            min-width: 320px;
            display: flex;
            flex-direction: column;
            background: rgba(0,0,0,0.4);
            backdrop-filter: blur(8px);
            border-right: 1px solid rgba(255,255,255,0.05);
        }

        .m2-prayer-list {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .m2-prayer-item {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 0 20px;
            position: relative;
            transition: all 0.4s ease;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .m2-prayer-item .prayer-label {
            width: 90px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255,255,255,0.9);
            padding: 6px 12px;
            border-radius: 6px;
            text-align: center;
        }
        .m2-prayer-item .prayer-time {
            font-size: 2.2rem;
            font-weight: 900;
            color: #fff;
            font-variant-numeric: tabular-nums;
            margin-left: 15px;
            text-shadow: 0 2px 15px rgba(0,0,0,0.5);
        }

        /* Warna per waktu sholat */
        .m2-prayer-item[data-waktu="subuh"] .prayer-label   { background: linear-gradient(135deg, #6366f1, #818cf8); }
        .m2-prayer-item[data-waktu="terbit"] .prayer-label   { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
        .m2-prayer-item[data-waktu="dzuhur"] .prayer-label   { background: linear-gradient(135deg, #10b981, #34d399); }
        .m2-prayer-item[data-waktu="ashar"] .prayer-label    { background: linear-gradient(135deg, #f97316, #fb923c); }
        .m2-prayer-item[data-waktu="maghrib"] .prayer-label  { background: linear-gradient(135deg, #ef4444, #f87171); }
        .m2-prayer-item[data-waktu="isya"] .prayer-label     { background: linear-gradient(135deg, #3b82f6, #60a5fa); }

        .m2-prayer-item.waktu-aktif {
            background: rgba(255,255,255,0.08);
        }
        .m2-prayer-item.waktu-aktif::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: #ffd700;
        }

        /* === RIGHT PANEL: Clock + Info + Slide === */
        .m2-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Top Info Bar */
        .m2-info-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 30px;
            background: rgba(0,0,0,0.3);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .m2-info-bar .masjid-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #ffffff;
        }
        .m2-info-bar .masjid-alamat {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.5);
        }
        .m2-info-bar .info-dates {
            text-align: right;
        }
        .m2-info-bar .tanggal-masehi {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.7);
        }
        .m2-info-bar .tanggal-hijri {
            font-size: 0.8rem;
            color: #60a5fa;
            font-weight: 600;
        }

        /* Clock Area */
        .m2-clock-area {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 30px;
        }
        .m2-jam-besar {
            font-size: 7rem;
            font-weight: 900;
            color: #ffffff;
            font-variant-numeric: tabular-nums;
            text-shadow: 0 4px 30px rgba(255,255,255,0.15);
            letter-spacing: -3px;
            line-height: 1;
        }
        .m2-jam-detik {
            font-size: 2.5rem;
            color: rgba(255,255,255,0.4);
            font-weight: 600;
            vertical-align: top;
            margin-left: 5px;
        }

        /* Countdown */
        .m2-countdown {
            text-align: center;
            padding: 0 30px 15px;
        }
        .m2-countdown .countdown-label {
            font-size: 0.9rem;
            color: #60a5fa;
            font-weight: 600;
        }
        .m2-countdown .countdown-waktu {
            font-size: 1.6rem;
            font-weight: 800;
            color: #ffd700;
            font-variant-numeric: tabular-nums;
        }

        /* Slide Content Area */
        .m2-slide-area {
            flex: 1;
            position: relative;
            margin: 0 20px 15px;
            border-radius: 16px;
            overflow: hidden;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
        }
        .m2-slide-area .display-slide {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.8s ease;
        }
        .m2-slide-area .display-slide.slide-active { opacity: 1; }
        .m2-slide-area .slide-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .m2-slide-overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            padding: 25px;
            background: linear-gradient(transparent, rgba(0,0,0,0.85));
        }
        .m2-slide-overlay h2 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 4px;
        }
        .m2-slide-overlay p {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.7);
        }

        .m2-slide-indicators {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 6px;
            z-index: 3;
        }
        .m2-slide-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            transition: all 0.3s;
        }
        .m2-slide-dot.active {
            background: #60a5fa;
            width: 24px;
            border-radius: 4px;
        }

        /* === FOOTER: Running Text === */
        .m2-footer {
            position: relative;
            z-index: 2;
            background: linear-gradient(90deg, #1e3a5f, #0f172a);
            padding: 8px 0;
            overflow: hidden;
            border-top: 2px solid #3b82f6;
        }
        .m2-footer .running-text-content {
            display: inline-block;
            white-space: nowrap;
            animation: m2-scroll 30s linear infinite;
            font-size: 0.95rem;
            font-weight: 600;
            color: #e2e8f0;
        }
        @keyframes m2-scroll {
            0% { transform: translateX(100vw); }
            100% { transform: translateX(-100%); }
        }
    </style>
</head>
<body>
<?php
$wallpaper = !empty($display['wallpaper']) ? base_url($display['wallpaper']) : base_url('assets/img/default-masjid.jpg');
$opsiWaktu = !empty($display['opsi_waktu_sholat']) ? json_decode($display['opsi_waktu_sholat'], true) : [];
$opsiQobliyah = $opsiWaktu['qobliyah'] ?? ['subuh'=>1, 'dzuhur'=>1, 'ashar'=>0, 'maghrib'=>1, 'isya'=>1];
$opsiBadiyah  = $opsiWaktu['badiyah']  ?? ['subuh'=>0, 'dzuhur'=>1, 'ashar'=>0, 'maghrib'=>1, 'isya'=>1];
?>

<div class="m2-container">
    <div class="m2-bg" style="background-image: url('<?= $wallpaper ?>')"></div>

    <!-- WRAPPER UNTUK AUTO-SCALING PROPORSI LAYAR TV -->
    <!-- Gunakan display-scale-wrapper standard yg memiliki transform-origin: center center; -->
    <div id="display-scaler" class="display-scale-wrapper">
    <div style="width: 1920px; height: 1080px; display: flex; flex-direction: column; position: relative; z-index: 1;">

    <div class="m2-body">
        <!-- LEFT PANEL: Jadwal Sholat Vertikal -->
        <div class="m2-left">
            <div class="m2-prayer-list">
                <div class="m2-prayer-item jadwal-row" data-waktu="subuh">
                    <span class="prayer-label">Subuh</span>
                    <span class="prayer-time" id="jadwal-subuh">--:--</span>
                </div>
                <div class="m2-prayer-item jadwal-row" data-waktu="terbit">
                    <span class="prayer-label">Terbit</span>
                    <span class="prayer-time" id="jadwal-terbit">--:--</span>
                </div>
                <div class="m2-prayer-item jadwal-row" data-waktu="dzuhur">
                    <span class="prayer-label" id="label-dzuhur">Dzuhur</span>
                    <span class="prayer-time" id="jadwal-dzuhur">--:--</span>
                </div>
                <div class="m2-prayer-item jadwal-row" data-waktu="ashar">
                    <span class="prayer-label">Ashar</span>
                    <span class="prayer-time" id="jadwal-ashar">--:--</span>
                </div>
                <div class="m2-prayer-item jadwal-row" data-waktu="maghrib">
                    <span class="prayer-label">Maghrib</span>
                    <span class="prayer-time" id="jadwal-maghrib">--:--</span>
                </div>
                <div class="m2-prayer-item jadwal-row" data-waktu="isya">
                    <span class="prayer-label">Isya</span>
                    <span class="prayer-time" id="jadwal-isya">--:--</span>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Info + Clock + Slide -->
        <div class="m2-right">
            <!-- Info Bar -->
            <div class="m2-info-bar">
                <div>
                    <div class="masjid-name"><?= esc($namaMasjid) ?></div>
                    <?php if (!empty($alamatDisplay)): ?>
                        <div class="masjid-alamat"><?= esc($alamatDisplay) ?></div>
                    <?php endif; ?>
                </div>
                <div class="info-dates">
                    <div class="tanggal-masehi" id="tanggal-masehi">Memuat...</div>
                    <div class="tanggal-hijri" id="tanggal-hijriah">...</div>
                </div>
            </div>

            <!-- Clock -->
            <div class="m2-clock-area">
                <span class="m2-jam-besar" id="jam-digital-hm">00:00</span>
                <span class="m2-jam-detik" id="jam-digital-detik">00</span>
            </div>

            <!-- Countdown -->
            <div class="m2-countdown">
                <span class="countdown-label" id="countdown-label">Menuju Sholat</span>
                <span class="countdown-waktu" id="countdown-waktu">00:00:00</span>
            </div>

            <!-- Slide Content -->
            <div class="m2-slide-area" id="slide-container">
                <?php
                $allSlides = [];
                if (!empty($kontenByTipe['gambar_slide'])) $allSlides = array_merge($allSlides, $kontenByTipe['gambar_slide']);
                if (!empty($kontenByTipe['info_kegiatan'])) $allSlides = array_merge($allSlides, $kontenByTipe['info_kegiatan']);
                if (!empty($kontenByTipe['pengumuman'])) $allSlides = array_merge($allSlides, $kontenByTipe['pengumuman']);
                ?>

                <?php if (!empty($allSlides)): ?>
                    <?php foreach ($allSlides as $idx => $slide): ?>
                    <div class="display-slide <?= $idx === 0 ? 'slide-active' : '' ?>">
                        <?php if (!empty($slide['gambar'])): ?>
                            <img src="<?= base_url($slide['gambar']) ?>" alt="" class="slide-image">
                        <?php endif; ?>
                        <div class="m2-slide-overlay">
                            <?php if (!empty($slide['judul'])): ?>
                                <h2><?= esc($slide['judul']) ?></h2>
                            <?php endif; ?>
                            <?php if (!empty($slide['konten'])): ?>
                                <p><?= mb_substr(strip_tags($slide['konten']), 0, 180) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <div class="m2-slide-indicators slide-indicators">
                        <?php foreach ($allSlides as $idx => $s): ?>
                            <div class="m2-slide-dot slide-indicator <?= $idx === 0 ? 'active' : '' ?>"></div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="display-slide slide-active">
                        <div style="text-align:center; padding:30px;">
                            <i class="fas fa-mosque" style="font-size:4rem; color:rgba(96,165,250,0.3); margin-bottom:15px;"></i>
                            <h2 style="color:#60a5fa; font-size:1.5rem;"><?= esc($namaMasjid) ?></h2>
                            <?php if (!empty($alamatDisplay)): ?>
                                <p style="color:rgba(255,255,255,0.4); margin-top:5px; font-size:0.9rem;"><?= esc($alamatDisplay) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Hidden elements untuk kompatibilitas display-engine.js -->
    <span id="jadwal-imsak" style="display:none">--:--</span>
    <!-- Elemen jam utama tersembunyi untuk display-engine.js -->
    <span id="jam-digital" style="display:none">00:00:00</span>

    <!-- RUNNING TEXT -->
    <div class="m2-footer">
        <span class="running-text-content" id="running-text-content"><?= esc($display['running_text'] ?? 'Selamat datang di ' . $namaMasjid) ?></span>
    </div>
    
    </div> <!-- End inner 1920x1080 container -->
    </div> <!-- End display-scaler -->
</div> <!-- End m2-container -->

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

        // Custom: Update jam besar terpisah (HH:MM dan SS terpisah)
        setInterval(function() {
            var now = new Date();
            var hm = ('0' + now.getHours()).slice(-2) + ':' + ('0' + now.getMinutes()).slice(-2);
            var ss = ('0' + now.getSeconds()).slice(-2);
            var elHm = document.getElementById('jam-digital-hm');
            var elSs = document.getElementById('jam-digital-detik');
            if (elHm) elHm.textContent = hm;
            if (elSs) elSs.textContent = ss;

            // Update label Dzuhur → Jumat jika hari Jumat
            if (now.getDay() === 5) {
                var lbl = document.getElementById('label-dzuhur');
                if (lbl) lbl.textContent = 'Jumat';
            }
        }, 1000);
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
