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
            background: transparent;
        }

        /* === bagian Header === */
        .m1-topbar {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: stretch; 
            justify-content: space-between;
            padding: 15px 30px;
            /* Transparansi background 65% */
            background: rgba(255, 255, 255, 0.65); 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            /* Border radius bottom */
            border-bottom-left-radius: 25px;
            border-bottom-right-radius: 25px;
        }
        /* === Bagian Kiri Jam Besar === */
        .m1-topbar-left {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 420px;
            border-right: 3px solid #dda02b;
            padding-right: 20px;
        }
        .m1-topbar-left .jam-besar {
            font-size: 4rem;
            font-weight: 800;
            color: #222222;
            font-variant-numeric: tabular-nums;
            line-height: 1;
            width: 220px;
            text-align: center;
        }
        /* === Bagian Tengah Nama Masjid === */
        .m1-topbar-center {
            flex: 1;
            text-align: center;
            padding: 0 20px;
        }
        .m1-topbar-center .masjid-name {
            font-size: 2.8rem;
            font-weight: 800;
            color: #222222;
        }
        .m1-topbar-center .masjid-alamat {
            font-size: 1rem;
            color: #555555;
            margin-top: 5px;
            font-weight: 600;
        }
        /* === Bagian Kanan Tanggal === */
        .m1-topbar-right {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 420px;
            border-left: 3px solid #dda02b;
            padding-left: 20px;
        }
        .m1-topbar-right-text {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .m1-topbar-right .tanggal-masehi {
            font-size: 2.2rem;
            color: #222222;
            font-weight: 800;
        }
        .m1-topbar-right .tanggal-hijri {
            font-size: 1.5rem;
            color: #555555;
            font-weight: 600;
            margin-top: 5px;
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

        /* === WRAPPER COUNTDOWN (Statis di atas jadwal) === */
        .m1-countdown-wrapper {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0; /* Menempel di ujung kiri, kanan, bawah */
            width: 100%;
        }

        /* === COUNTDOWN BAR SHOLAT === */
        .m1-countdown-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px; /* Jarak dibuat rapat */
            padding: 8px 30px;
            background: rgba(255, 255, 255, 0.80); /* Background putih transparan 50% */
            border-top-right-radius: 25px; /* Ujung kanan atas melengkung */
        }
        .m1-countdown-bar .countdown-label {
            font-size: 1.8rem;
            color: #222222; /* Teks Hitam */
            font-weight: 800;
        }
        .m1-countdown-bar .countdown-waktu {
            font-size: 1.8rem;
            font-weight: 800;
            color: #b8860b; /* Teks Kuning Keemasan */
            font-variant-numeric: tabular-nums;
            min-width: 105px; /* Lebar statis agar kotak tidak memanjang/memendek saat angka detik berubah */
            text-align: left;
            display: inline-block;
        }

        /* === EVENT COUNTDOWN BAR === */
        .m1-event-countdown {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 8px 30px;
            background: rgba(255, 255, 255, 0.8); /* Background putih transparan 50% */
            border-top-left-radius: 25px; /* Ujung kiri atas melengkung */
        }
        .m1-event-countdown .event-label {
            font-size: 1.8rem;
            color: #222222; /* Teks Hitam */
            font-weight: 800;
        }
        .m1-event-countdown .event-waktu {
            font-size: 1.8rem;
            font-weight: 800;
            color: #b8860b; /* Teks Kuning Keemasan */
            font-variant-numeric: tabular-nums;
            text-align: left;
            display: inline-block;
        }

        /* === BOTTOM: Jadwal Sholat Horizontal Bar === */
        .m1-prayer-bar {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: stretch;
            gap: 0;
            background: rgba(255, 255, 255, 0.5); /* Latar belakang bar transparan 50% */
            border-top: 1px solid #faf9f7ff;
        }
        .m1-prayer-item {
            flex: 1;
            text-align: center;
            padding: 0; /* Padding dihilangkan semua untuk mengecilkan jarak ruang */
            position: relative;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            border-right: 3px solid rgba(255,255,255,0.9); /* Garis pemisah antar jadwal */
        }
        .m1-prayer-item:last-child {
            border-right: none;
        }
        /* Static colors for each prayer time */
        .m1-prayer-item.bg-imsak   .prayer-nama { background: #0b8a3e; color: #fff; } /* Hijau Imsak */
        .m1-prayer-item.bg-subuh   .prayer-nama { background: #1c7ed6; color: #fff; } /* Biru Subuh */
        .m1-prayer-item.bg-terbit .prayer-nama { background: #12b886; color: #fff; } /* Teal Syuruq/Terbit */
        .m1-prayer-item.bg-dzuhur  .prayer-nama { background: #f59f00; color: #fff; } /* Oranye Dzuhur */
        .m1-prayer-item.bg-ashar   .prayer-nama { background: #be4bdb; color: #fff; } /* Ungu Ashar */
        .m1-prayer-item.bg-maghrib .prayer-nama { background: #e8590c; color: #fff; } /* Merah/Oranye-tua Maghrib */
        .m1-prayer-item.bg-isya    .prayer-nama { background: #5f3dc4; color: #fff; } /* Ungu-Tua Isya */

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
            font-size: 1.9rem;
            font-weight: 550;
            color: rgba(255,255,255,0.8);
            text-transform: capitalize;
            letter-spacing: 0.5px;
            margin: 0; /* Full width tidak ada margin luar */
            padding: 3px 0;
            /* Border radius bottom */
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
            display: block;   /* Berubah dari inline-block menjadi full block */
            width: 100%;      /* Memenuhi ruangan kontainer .m1-prayer-item */
            margin-bottom: 1px; /* Jarak dibuat sangat rapat dengan angka jam */
        }
        /* Jam waktu sholat */
        .m1-prayer-item .prayer-jam {
            font-size: 4.8rem;
            font-weight: 650;
            color: #222222; /* Warna teks jam menjadi hitam gelap untuk kontras di base putih */
            font-variant-numeric: tabular-nums;
        }

        /* === QUOTE BAR (Kutipan Statis) === */
        .m1-quote-bar {
            position: relative;
            z-index: 2;
            background: #d32f2f; /* Merah pekat ala Kemenag */
            padding: 8px 15px;
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.2);
            border-bottom: 2px solid #b71c1c;
        }
        .m1-quote-bar .quote-text {
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: 500;
            margin: 0;
            letter-spacing: 0.5px;
        }

        /* === RUNNING TEXT === */
        .m1-footer {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.55);
            overflow: hidden;
        }
        .m1-footer .running-text-content {
            display: inline-block;
            white-space: nowrap;
            animation: m1-scroll 45s linear infinite; /* Kecepatan diperlambat dari 30s ke 45s */
            font-size: 2rem; /* Ukuran font diperbesar dari 0.9rem ke 1.5rem */
            font-weight: 500;
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

// Parse Pengaturan Khusus Modern 1
$displaySettingArr = !empty($display['display_setting']) ? json_decode($display['display_setting'], true) : [];
$modern1Data = $displaySettingArr['modern1'] ?? [];
$m1Event = $modern1Data['event_countdown'] ?? ['tampilkan' => true, 'label' => 'Ramadhan', 'tanggal_target' => date('Y') . '-03-01 18:00:00'];
$m1Quote = $modern1Data['kutipan'] ?? ['tampilkan' => true, 'teks' => '"Barangsiapa yang menempuh jalan untuk mencari ilmu, maka Allah akan mudahkan baginya jalan menuju surga." (HR. Muslim)'];
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
            <div class="jam-besar" id="jam-digital">00:00:00</div>
        </div>
        <div class="m1-topbar-center">
            <div style="display:flex; align-items:center; justify-content:center; gap:15px;">
                <?php if (!empty($display['logo'])): ?>
                    <!-- Logo dibiarkan proporsi asli, tinggi max 70px -->
                    <img src="<?= base_url($display['logo']) ?>" alt="Logo" style="height:70px; max-width:100px; object-fit:contain;">
                <?php endif; ?>
                <div class="masjid-name"><?= esc($namaMasjid) ?></div>
            </div>
            <?php if (!empty($alamatDisplay)): ?>
                <div class="masjid-alamat"><?= esc($alamatDisplay) ?></div>
            <?php endif; ?>
        </div>
        <div class="m1-topbar-right">
            <div class="m1-topbar-right-text">
                <span class="tanggal-masehi" id="tanggal-masehi">Memuat...</span>
                <span class="tanggal-hijri" id="tanggal-hijriah">...</span>
            </div>
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

    <!-- WRAPPER COUNTDOWN KIRI & KANAN -->
    <div class="m1-countdown-wrapper">
        <!-- COUNTDOWN BAR -->
        <div class="m1-countdown-bar">
            <span class="countdown-label" id="countdown-label">Menuju Sholat</span>
            <span class="countdown-waktu" id="countdown-waktu">00:00:00</span>
        </div>

        <!-- EVENT COUNTDOWN BAR -->
        <?php if ($m1Event['tampilkan']): ?>
        <div class="m1-event-countdown" data-target-date="<?= esc($m1Event['tanggal_target']) ?>">
            <span class="event-label" id="event-label"><?= esc($m1Event['label']) ?></span>
            <span class="event-waktu" id="event-waktu">... Menghitung ...</span>
        </div>
        <?php endif; ?>
    </div>

    <!-- PRAYER TIME BAR (Horizontal) -->
    <div class="m1-prayer-bar">
        <!-- Default imsak hidden, will be shown via JS -->
        <div class="m1-prayer-item jadwal-row bg-imsak" data-waktu="imsak" style="display:none;" id="box-imsak">
            <div class="prayer-nama">Imsyak</div>
            <div class="prayer-jam" id="jadwal-imsak">--:--</div>
        </div>
        <div class="m1-prayer-item jadwal-row bg-subuh" data-waktu="subuh">
            <div class="prayer-nama">Subuh</div>
            <div class="prayer-jam" id="jadwal-subuh">--:--</div>
        </div>
        <div class="m1-prayer-item jadwal-row bg-terbit" data-waktu="terbit">
            <div class="prayer-nama">Syuruq</div>
            <div class="prayer-jam" id="jadwal-terbit">--:--</div>
        </div>
        <div class="m1-prayer-item jadwal-row bg-dzuhur" data-waktu="dzuhur">
            <div class="prayer-nama">Dzuhur</div>
            <div class="prayer-jam" id="jadwal-dzuhur">--:--</div>
        </div>
        <div class="m1-prayer-item jadwal-row bg-ashar" data-waktu="ashar">
            <div class="prayer-nama">Ashar</div>
            <div class="prayer-jam" id="jadwal-ashar">--:--</div>
        </div>
        <div class="m1-prayer-item jadwal-row bg-maghrib" data-waktu="maghrib">
            <div class="prayer-nama">Maghrib</div>
            <div class="prayer-jam" id="jadwal-maghrib">--:--</div>
        </div>
        <div class="m1-prayer-item jadwal-row bg-isya" data-waktu="isya">
            <div class="prayer-nama">Isya</div>
            <div class="prayer-jam" id="jadwal-isya">--:--</div>
        </div>
    </div>

    <!-- QUOTE BAR (Kutipan Statis Dinamis) -->
    <?php if ($m1Quote['tampilkan']): ?>
    <div class="m1-quote-bar">
        <p class="quote-text" id="quote-text-content"><?= esc($m1Quote['teks']) ?></p>
    </div>
    <?php endif; ?>

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
