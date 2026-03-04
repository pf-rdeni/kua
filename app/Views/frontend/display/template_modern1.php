<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display - <?= esc($namaMasjid) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/display/css/display-style.css') ?>">
    <style>
        /* ============================================================
           TEMPLATE MODERN 1 - Horizontal Prayer Bar
           Inspirasi: Display dengan kaligrafi tengah, bar jadwal bawah
           ============================================================ */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
            width: 100vw;
            height: 100vh;
            background: #000;
        }

        .m1-container {
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .m1-bg {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-size: cover;
            background-position: center;
            z-index: 0;
        }
        .m1-bg::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(180deg,
                rgba(0,20,40,0.85) 0%,
                rgba(0,20,40,0.5) 40%,
                rgba(0,20,40,0.5) 60%,
                rgba(0,20,40,0.9) 100%);
        }

        /* === TOP BAR === */
        .m1-topbar {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 30px;
            background: linear-gradient(90deg, rgba(0,40,80,0.95), rgba(0,60,100,0.9));
            border-bottom: 3px solid #ffd700;
        }
        .m1-topbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
            width: 300px; /* Fixed width to prevent center from moving */
        }
        .m1-topbar-left-text {
            display: flex;
            flex-direction: column;
        }
        .m1-topbar-left .tanggal-masehi {
            font-size: 1rem;
            color: rgba(255,255,255,0.8);
            font-weight: 400;
        }
        .m1-topbar-left .tanggal-hijri {
            font-size: 0.85rem;
            color: #ffd700;
            font-weight: 600;
        }
        .m1-topbar-center {
            text-align: center;
        }
        .m1-topbar-center .masjid-name {
            font-size: 1.4rem;
            font-weight: 700;
            color: #ffffff;
            text-shadow: 0 2px 8px rgba(0,0,0,0.5);
        }
        .m1-topbar-center .masjid-alamat {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.6);
        }
        .m1-topbar-right {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            width: 300px; /* Lebar tetap agar tidak geser */
        }
        .m1-topbar-right .jam-besar {
            font-size: 2.5rem;
            font-weight: 800;
            color: #ffffff;
            font-variant-numeric: tabular-nums;
            line-height: 1;
            width: 170px; /* Lebar tetap untuk jam */
            text-align: left; /* Teks rata kiri di dalam kotaknya */
        }

        /* === CENTER CONTENT (Slide/Kaligrafi) === */
        .m1-center {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0; /* Dipenuhkan ke tepi kiri dan kanan */
        }
        .m1-slide-area {
            width: 100%;
            height: 100%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .m1-slide-area .display-slide {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.8s ease;
        }
        .m1-slide-area .display-slide.slide-active {
            opacity: 1;
        }
        .m1-slide-area .slide-image {
            width: 100%;
            height: 100%;
            object-fit: fill; /* Memaksa gambar diregangkan agar pas tepi tanpa ada bagian yang terpotong */
        }
        .m1-default-content {
            text-align: center;
        }
        .m1-default-content .bismillah {
            font-family: 'Amiri', serif;
            font-size: 5rem;
            color: #fdfdfcff;
            text-shadow: 0 4px 20px rgba(255,215,0,0.3);
            line-height: 1.4;
        }

        /* === COUNTDOWN BAR (tengah bawah) === */
        .m1-countdown-bar {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            padding: 8px 30px;
            background: rgba(0,0,0,0.6);
            border-top: 1px solid rgba(255,215,0,0.2);
        }
        .m1-countdown-bar .countdown-label {
            font-size: 0.95rem;
            color: #ffd700;
            font-weight: 600;
            min-width: 130px; /* Lebar tetap agar tidak geser */
            text-align: right;
        }
        .m1-countdown-bar .countdown-waktu {
            font-size: 1.3rem;
            font-weight: 800;
            color: #ffffffff;
            font-variant-numeric: tabular-nums;
            min-width: 95px; /* Lebar tetap agar tidak geser */
            text-align: left;
        }

        /* === BOTTOM: Jadwal Sholat Horizontal Bar === */
        .m1-prayer-bar {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: stretch;
            gap: 0;
            background: linear-gradient(90deg, rgba(0,40,80,0.95), rgba(0,50,90,0.95));
            border-top: 3px solid #faf9f7ff;
        }
        .m1-prayer-item {
            flex: 1;
            text-align: center;
            padding: 12px 8px;
            position: relative;
            transition: all 0.3s ease;
        }
        .m1-prayer-item::after {
            content: '';
            position: absolute;
            right: 0;
            top: 15%;
            height: 70%;
            width: 1px;
            background: rgba(255,255,255,0.1);
        }
        .m1-prayer-item:last-child::after { display: none; }
        .m1-prayer-item.active {
            background: linear-gradient(135deg, #10b981, #059669) !important; /* Hijau cerah untuk waktu sekarang */
        }
        .m1-prayer-item.active .prayer-nama,
        .m1-prayer-item.active .prayer-jam {
            color: #ffffff !important;
        }

        .m1-prayer-item.next {
            background: linear-gradient(135deg, #ffd700, #ffaa00) !important; /* Kuning untuk waktu berikutnya */
        }
        .m1-prayer-item.next .prayer-nama,
        .m1-prayer-item.next .prayer-jam {
            color: #000 !important;
        }
        .m1-prayer-item .prayer-nama {
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .m1-prayer-item .prayer-jam {
            font-size: 1.4rem;
            font-weight: 800;
            color: #ffffff;
            font-variant-numeric: tabular-nums;
        }

        /* === RUNNING TEXT === */
        .m1-footer {
            position: relative;
            z-index: 2;
            background: #f7ec8bff;
            padding: 6px 0;
            overflow: hidden;
        }
        .m1-footer .running-text-content {
            display: inline-block;
            white-space: nowrap;
            animation: m1-scroll 30s linear infinite;
            font-size: 0.9rem;
            font-weight: 600;
            color: #000;
        }
        @keyframes m1-scroll {
            0% { transform: translateX(100vw); }
            100% { transform: translateX(-100%); }
        }

        /* Slide indicators */
        .m1-slide-indicators {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 6px;
            z-index: 3;
        }
        .m1-slide-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transition: all 0.3s;
        }
        .m1-slide-dot.active {
            background: #ffd700;
            width: 24px;
            border-radius: 4px;
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

<div class="m1-container <?= ($display['orientasi'] ?? 'horizontal') === 'vertikal' ? 'orientasi-vertikal' : '' ?>">
    <div class="m1-bg" style="background-image: url('<?= $wallpaper ?>')"></div>

    <!-- WRAPPER UNTUK AUTO-SCALING PROPORSI LAYAR TV -->
    <!-- Gunakan display-scale-wrapper standard yg memiliki transform-origin: center center; -->
    <div id="display-scaler" class="display-scale-wrapper">
    <div style="width: 1920px; height: 1080px; display: flex; flex-direction: column; position: relative; z-index: 1;">

    <!-- TOP BAR: Logo + Tanggal | Nama Masjid | Jam Digital -->
    <div class="m1-topbar">
        <div class="m1-topbar-left">
            <?php if (!empty($display['logo'])): ?>
                <!-- Logo dibiarkan proporsi asli, tinggi max 50px -->
                <img src="<?= base_url($display['logo']) ?>" alt="Logo" style="height:50px; max-width:80px; object-fit:contain;">
            <?php else: ?>
                <div style="width:50px; height:50px; border-radius:5px; background:rgba(255,255,255,0.1); display:flex; align-items:center; justify-content:center; border:2px solid #ffd700;">
                    <i class="fas fa-mosque" style="font-size:1.5rem; color:#ffd700;"></i>
                </div>
            <?php endif; ?>
            <div class="m1-topbar-left-text">
                <span class="tanggal-masehi" id="tanggal-masehi">Memuat...</span>
                <span class="tanggal-hijri" id="tanggal-hijriah">...</span>
            </div>
        </div>
        <div class="m1-topbar-center">
            <div class="masjid-name"><?= esc($namaMasjid) ?></div>
            <?php if (!empty($alamatDisplay)): ?>
                <div class="masjid-alamat"><?= esc($alamatDisplay) ?></div>
            <?php endif; ?>
        </div>
        <div class="m1-topbar-right">
            <div class="jam-besar" id="jam-digital">00:00:00</div>
        </div>
    </div>

    <!-- CENTER: Slide Konten / Kaligrafi Default -->
    <div class="m1-center">
        <div class="m1-slide-area" id="slide-container">
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
                    <?php if (!empty($slide['judul']) || !empty($slide['konten'])): ?>
                    <div style="position:absolute; bottom:0; left:0; right:0; padding:40px 50px 30px; background:linear-gradient(transparent, rgba(0,0,0,0.9));">
                        <?php if (!empty($slide['judul'])): ?>
                            <h2 style="font-size:1.6rem; color:#ffd700; font-weight:700;"><?= esc($slide['judul']) ?></h2>
                        <?php endif; ?>
                        <?php if (!empty($slide['konten'])): ?>
                            <p style="font-size:1rem; color:rgba(255,255,255,0.9);"><?= mb_substr(strip_tags($slide['konten']), 0, 200) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <div class="m1-slide-indicators slide-indicators">
                    <?php foreach ($allSlides as $idx => $s): ?>
                        <div class="m1-slide-dot slide-indicator <?= $idx === 0 ? 'active' : '' ?>"></div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="display-slide slide-active">
                    <div class="m1-default-content">
                        <div class="bismillah">بِسْمِ ٱللَّٰهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- COUNTDOWN BAR -->
    <div class="m1-countdown-bar">
        <span class="countdown-label" id="countdown-label">Menuju Sholat</span>
        <span class="countdown-waktu" id="countdown-waktu">00:00:00</span>
    </div>

    <!-- PRAYER TIME BAR (Horizontal) -->
    <div class="m1-prayer-bar">
        <div class="m1-prayer-item jadwal-row" data-waktu="subuh">
            <div class="prayer-nama">Subuh</div>
            <div class="prayer-jam" id="jadwal-subuh">--:--</div>
        </div>
        <div class="m1-prayer-item jadwal-row" data-waktu="terbit">
            <div class="prayer-nama">Terbit</div>
            <div class="prayer-jam" id="jadwal-terbit">--:--</div>
        </div>
        <div class="m1-prayer-item jadwal-row" data-waktu="dzuhur">
            <div class="prayer-nama">Dzuhur</div>
            <div class="prayer-jam" id="jadwal-dzuhur">--:--</div>
        </div>
        <div class="m1-prayer-item jadwal-row" data-waktu="ashar">
            <div class="prayer-nama">Ashar</div>
            <div class="prayer-jam" id="jadwal-ashar">--:--</div>
        </div>
        <div class="m1-prayer-item jadwal-row" data-waktu="maghrib">
            <div class="prayer-nama">Maghrib</div>
            <div class="prayer-jam" id="jadwal-maghrib">--:--</div>
        </div>
        <div class="m1-prayer-item jadwal-row" data-waktu="isya">
            <div class="prayer-nama">Isya</div>
            <div class="prayer-jam" id="jadwal-isya">--:--</div>
        </div>
    </div>

    <!-- Hidden elements untuk kompatibilitas display-engine.js -->
    <span id="jadwal-imsak" style="display:none">--:--</span>

    <!-- RUNNING TEXT -->
    <div class="m1-footer">
        <span class="running-text-content" id="running-text-content"><?= esc($display['running_text'] ?? 'Selamat datang di ' . $namaMasjid) ?></span>
    </div>
    
    </div> <!-- End inner 1920x1080 container -->
    </div> <!-- End display-scaler -->
</div> <!-- End m1-container -->

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
                isya: <?= (int)($display['koreksi_isya'] ?? 0) ?>,
                hijriah: <?= (int)($koreksiHijriah ?? 0) ?>
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

        
        // Pilihan tampilan template spesifik modern 1
        console.log("Modern 1 initialized");
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
