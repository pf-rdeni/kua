/**
 * Display Engine - Mesin Utama Display Masjid
 * Mengelola jam digital, jadwal sholat, countdown, slide, running text,
 * sinkronisasi data server, dan cache offline.
 *
 * Dependensi: praytimes.js
 */
var DisplayEngine = (function () {
    'use strict';

    // ============================================================
    // KONFIGURASI
    // ============================================================
    var config = {
        displayId: 0,
        apiUrl: '',
        apiKeuanganUrl: '',
        latitude: 1.0408,
        longitude: 104.2417,
        timezone: 7, // WIB
        metode: 'Kemenag',
        intervalSync: 60,    // detik
        intervalSlide: 8,    // detik per slide
        koreksi: { subuh: 0, dzuhur: 0, ashar: 0, maghrib: 0, isya: 0 },
        iqomah: { subuh: 10, dzuhur: 10, ashar: 10, maghrib: 5, isya: 10 },
        sholatJumat: true,
        // Mode event sholat
        modeSholat: {
            menjelangAdzan: { aktif: 1, durasi: 10, gambar: null },
            adzan: { aktif: 1, durasi: 7, gambar: null },
            qobliyah: { aktif: 0, durasi: 5, gambar: null },
            iqomah: { aktif: 1, gambar: null },
            sholat: { aktif: 1, durasi: 15, gambar: null },
            badiyah: { aktif: 0, durasi: 5, gambar: null },
            tarawih: { aktif: 0, durasi: 60, gambar: null },
            idulAdha: { aktif: 0, durasi: 60, gambar: null, tanggal: null },
            idulFitri: { aktif: 0, durasi: 60, gambar: null, tanggal: null },
        },
    };

    // State internal
    var state = {
        jadwalSholat: {},
        konten: {},
        currentSlide: 0,
        totalSlides: 0,
        isOnline: navigator.onLine,
        lastSync: null,
        syncTimer: null,
        clockTimer: null,
        slideTimer: null,
        // State prayer mode
        prayerMode: 'normal',      // mode aktif saat ini
        prayerModeWaktu: '',       // nama waktu sholat yang sedang aktif
        prayerModeTimer: null,
        // Polling fast update
        lastUpdatedServer: null,
        fastSyncTimer: null,
    };

    // Nama hari dalam bahasa Indonesia
    var namaHari = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    var namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    // ============================================================
    // INISIALISASI
    // ============================================================
    function init(options) {
        // Merge konfigurasi
        Object.assign(config, options || {});

        // Setup PrayTimes
        if (typeof PrayTimes !== 'undefined') {
            PrayTimes.setMethod(config.metode);
            PrayTimes.tune({
                fajr: config.koreksi.subuh,
                dhuhr: config.koreksi.dzuhur,
                asr: config.koreksi.ashar,
                maghrib: config.koreksi.maghrib,
                isha: config.koreksi.isya
            });
        }

        // Hitung jadwal sholat hari ini
        hitungJadwalSholat();

        // Mulai jam digital
        mulaiJamDigital();

        // Mulai countdown
        mulaiCountdown();

        // Mulai slide konten
        mulaiSlide();

        // Mulai running text
        mulaiRunningText();

        // Mulai prayer mode state machine
        mulaiPrayerMode();

        // Mulai sinkronisasi
        mulaiSync();

        // Monitor koneksi internet
        monitorKoneksi();

        // Muat dari cache jika ada
        muatDariCache();

        // Inisialisasi auto-scaling untuk TV/Monitor (Proposional 1920x1080)
        initAutoScaling();

        console.log('[DisplayEngine] Diinisialisasi dengan ID:', config.displayId);
    }

    // ============================================================
    // AUTO SCALING ENGINE (Responsive Canvas)
    // ============================================================
    function initAutoScaling() {
        var scaleWrapper = document.getElementById('display-scaler');
        var mainContainer = document.querySelector('.display-container') || document.querySelector('.m1-container') || document.querySelector('.m2-container');

        if (!scaleWrapper) {
            console.warn('[DisplayEngine] display-scaler tidak ditemukan, auto-scaling diabaikan.');
            return;
        }

        // Cek orientasi dari class parent container
        var isVertical = mainContainer && mainContainer.classList.contains('orientasi-vertikal');

        // Atur dimensi canvas dasar berdasarkan orientasi
        var baseWidth = isVertical ? 1080 : 1920;
        var baseHeight = isVertical ? 1920 : 1080;

        // Terapkan paksa css width & heightnya agar sesuai orientasi
        scaleWrapper.style.width = baseWidth + 'px';
        scaleWrapper.style.height = baseHeight + 'px';

        // Untuk template modern1 & modern2, ada div child dengan fixed width/height yang juga perlu disesuaikan
        if (scaleWrapper.children.length === 1 && scaleWrapper.children[0].tagName === 'DIV' && scaleWrapper.children[0].style.width === '1920px') {
            scaleWrapper.children[0].style.width = baseWidth + 'px';
            scaleWrapper.children[0].style.height = baseHeight + 'px';
        }

        function calculateScale() {
            var windowWidth = window.innerWidth;
            var windowHeight = window.innerHeight;

            // Hitung rasio scale untuk lebar dan tinggi
            var scaleX = windowWidth / baseWidth;
            var scaleY = windowHeight / baseHeight;

            // Ambil nilai terkecil agar seluruh elemen muat 100% tanpa terpotong (fit)
            var scaleValue = Math.min(scaleX, scaleY);

            // Terapkan CSS Transform ke wrapper
            scaleWrapper.style.transform = 'translate(-50%, -50%) scale(' + scaleValue + ')';

            // (Opsional) Log untuk debugging di konsol
            // console.log('[AutoScaling] Layar:', windowWidth + 'x' + windowHeight, '| Scale:', scaleValue.toFixed(3));
        }

        // Jalankan saat pertama kali buka
        calculateScale();

        // Daftarkan listener saat mendeteksi resize / fullscreen
        window.addEventListener('resize', function () {
            // Gunakan sedikit delay (debounce) agar tidak boros CPU saat di-resize
            clearTimeout(state.resizeTimer);
            state.resizeTimer = setTimeout(calculateScale, 100);
        });
    }

    // ============================================================
    // JADWAL SHOLAT
    // ============================================================
    function hitungJadwalSholat() {
        if (typeof PrayTimes === 'undefined') {
            console.warn('[DisplayEngine] PrayTimes.js belum dimuat');
            return;
        }

        var now = new Date();
        var times = PrayTimes.getTimes(now, [config.latitude, config.longitude], config.timezone);

        state.jadwalSholat = {
            imsak: times.imsak,
            subuh: times.fajr,
            terbit: times.sunrise,
            dzuhur: times.dhuhr,
            ashar: times.asr,
            maghrib: times.maghrib,
            isya: times.isha
        };

        // Update tampilan jadwal
        updateTampilanJadwal();

        // Simpan ke cache
        simpanKeCache('jadwalSholat', state.jadwalSholat);
    }

    function updateTampilanJadwal() {
        var jadwal = state.jadwalSholat;
        var elMap = {
            'jadwal-imsak': jadwal.imsak,
            'jadwal-subuh': jadwal.subuh,
            'jadwal-terbit': jadwal.terbit,
            'jadwal-dzuhur': jadwal.dzuhur,
            'jadwal-ashar': jadwal.ashar,
            'jadwal-maghrib': jadwal.maghrib,
            'jadwal-isya': jadwal.isya
        };

        for (var id in elMap) {
            var el = document.getElementById(id);
            if (el) el.textContent = elMap[id] || '--:--';
        }

        // Highlight waktu sholat aktif
        highlightWaktuAktif();
    }

    function highlightWaktuAktif() {
        var now = new Date();
        var currentMinutes = now.getHours() * 60 + now.getMinutes();
        var jadwal = state.jadwalSholat;

        var waktuList = [
            { nama: 'subuh', waktu: jadwal.subuh },
            { nama: 'dzuhur', waktu: jadwal.dzuhur },
            { nama: 'ashar', waktu: jadwal.ashar },
            { nama: 'maghrib', waktu: jadwal.maghrib },
            { nama: 'isya', waktu: jadwal.isya }
        ];

        var waktuAktif = '';
        var waktuBerikutnya = '';

        for (var i = 0; i < waktuList.length; i++) {
            var mins = timeToMinutes(waktuList[i].waktu);
            if (currentMinutes < mins) {
                waktuBerikutnya = waktuList[i].nama;
                break;
            }
            waktuAktif = waktuList[i].nama;
        }

        // Jika sudah lewat Isya, berikutnya Subuh esok
        if (!waktuBerikutnya) {
            waktuBerikutnya = 'subuh';
        }

        // Hapus semua highlight
        document.querySelectorAll('.jadwal-row').forEach(function (el) {
            el.classList.remove('active', 'next');
        });

        // Set highlight
        var elAktif = document.querySelector('.jadwal-row[data-waktu="' + waktuAktif + '"]');
        if (elAktif) elAktif.classList.add('active');

        var elNext = document.querySelector('.jadwal-row[data-waktu="' + waktuBerikutnya + '"]');
        if (elNext) elNext.classList.add('next');

        // Update label waktu berikutnya
        var elLabel = document.getElementById('waktu-berikutnya-label');
        if (elLabel) elLabel.textContent = capitalizeFirst(waktuBerikutnya);
    }

    // ============================================================
    // JAM DIGITAL
    // ============================================================
    function mulaiJamDigital() {
        updateJam();
        state.clockTimer = setInterval(updateJam, 1000);
    }

    function updateJam() {
        var now = new Date();

        // Jam digital
        var elJam = document.getElementById('jam-digital');
        if (elJam) {
            var jam = padZero(now.getHours()) + ':' + padZero(now.getMinutes()) + ':' + padZero(now.getSeconds());
            elJam.textContent = jam;
        }

        // Jam tanpa detik
        var elJamShort = document.getElementById('jam-short');
        if (elJamShort) {
            elJamShort.textContent = padZero(now.getHours()) + ':' + padZero(now.getMinutes());
        }

        // Tanggal Masehi
        var elTanggal = document.getElementById('tanggal-masehi');
        if (elTanggal) {
            var tgl = namaHari[now.getDay()] + ', ' + now.getDate() + ' ' + namaBulan[now.getMonth()] + ' ' + now.getFullYear();
            elTanggal.textContent = tgl;
        }

        // Tanggal Hijriah (perkiraan sederhana)
        var elHijriah = document.getElementById('tanggal-hijriah');
        if (elHijriah) {
            elHijriah.textContent = getHijriDate(now);
        }

        // Cek pergantian hari (recalc jadwal)
        if (now.getHours() === 0 && now.getMinutes() === 0 && now.getSeconds() === 0) {
            hitungJadwalSholat();
        }
    }

    // ============================================================
    // COUNTDOWN
    // ============================================================
    function mulaiCountdown() {
        updateCountdown();
        setInterval(updateCountdown, 1000);
    }

    function updateCountdown() {
        var now = new Date();
        var currentMinutes = now.getHours() * 60 + now.getMinutes();
        var jadwal = state.jadwalSholat;

        var waktuList = [
            { nama: 'Subuh', waktu: jadwal.subuh },
            { nama: 'Dzuhur', waktu: jadwal.dzuhur },
            { nama: 'Ashar', waktu: jadwal.ashar },
            { nama: 'Maghrib', waktu: jadwal.maghrib },
            { nama: 'Isya', waktu: jadwal.isya }
        ];

        var nextNama = '';
        var nextMinutes = 0;
        var selisih = 0;

        for (var i = 0; i < waktuList.length; i++) {
            var mins = timeToMinutes(waktuList[i].waktu);
            if (currentMinutes < mins) {
                nextNama = waktuList[i].nama;
                nextMinutes = mins;
                selisih = nextMinutes - currentMinutes;
                break;
            }
        }

        // Jika sudah lewat Isya, hitung ke Subuh besok
        if (!nextNama) {
            nextNama = 'Subuh';
            var subuhMins = timeToMinutes(jadwal.subuh || '05:00');
            selisih = (24 * 60 - currentMinutes) + subuhMins;
        }

        // Kurangi detik
        var nowSeconds = now.getSeconds();
        var totalDetik = selisih * 60 - nowSeconds;
        if (totalDetik < 0) totalDetik = 0;

        var jamCountdown = Math.floor(totalDetik / 3600);
        var menitCountdown = Math.floor((totalDetik % 3600) / 60);
        var detikCountdown = totalDetik % 60;

        // Update elemen countdown
        var elCountdown = document.getElementById('countdown-waktu');
        if (elCountdown) {
            elCountdown.textContent = padZero(jamCountdown) + ':' + padZero(menitCountdown) + ':' + padZero(detikCountdown);
        }

        var elCountdownLabel = document.getElementById('countdown-label');
        if (elCountdownLabel) {
            elCountdownLabel.textContent = 'Menuju ' + nextNama;
        }

        // Update waktu berikutnya
        var elNextLabel = document.getElementById('waktu-berikutnya-label');
        if (elNextLabel) {
            elNextLabel.textContent = nextNama;
        }

        // Progress bar
        var elProgress = document.getElementById('countdown-progress');
        if (elProgress) {
            var maxSelisih = 6 * 60 * 60; // 6 jam dalam detik
            var pct = Math.max(0, Math.min(100, ((maxSelisih - totalDetik) / maxSelisih) * 100));
            elProgress.style.width = pct + '%';
        }
    }

    // ============================================================
    // PRAYER MODE STATE MACHINE
    // ============================================================
    function mulaiPrayerMode() {
        // Buat overlay container jika belum ada
        if (!document.getElementById('prayer-mode-overlay')) {
            var overlay = document.createElement('div');
            overlay.id = 'prayer-mode-overlay';
            overlay.className = 'prayer-overlay';
            overlay.innerHTML = ''
                + '<div class="prayer-overlay-bg" id="prayer-overlay-bg"></div>'
                + '<div class="prayer-overlay-content" id="prayer-overlay-content">'
                + '<div class="prayer-overlay-icon" id="prayer-overlay-icon"><i class="fas fa-mosque"></i></div>'
                + '<div class="prayer-overlay-title" id="prayer-overlay-title"></div>'
                + '<div class="prayer-overlay-subtitle" id="prayer-overlay-subtitle"></div>'
                + '<div class="prayer-overlay-timer" id="prayer-overlay-timer"></div>'
                + '<div class="prayer-overlay-clock" id="prayer-overlay-clock"></div>'
                + '</div>';
            document.body.appendChild(overlay);
        }

        // Cek mode setiap detik
        state.prayerModeTimer = setInterval(cekPrayerMode, 1000);
        cekPrayerMode(); // cek pertama
    }

    /**
     * Cek dan tentukan prayer mode berdasarkan waktu saat ini
     * Alur per waktu sholat:
     * [menjelangAdzan] → [adzan] → [qobliyah] → [iqomah] → [sholat] → [badiyah] → normal
     */
    function cekPrayerMode() {
        var now = new Date();
        var currentSec = now.getHours() * 3600 + now.getMinutes() * 60 + now.getSeconds();
        var jadwal = state.jadwalSholat;
        var ms = config.modeSholat;
        var todayStr = now.getFullYear() + '-' + padZero(now.getMonth() + 1) + '-' + padZero(now.getDate());

        // Cek mode khusus Hari Raya terlebih dahulu
        if (ms.idulFitri && ms.idulFitri.aktif && ms.idulFitri.tanggal === todayStr) {
            var waktuSubuhSec = timeToSeconds(jadwal.subuh || '05:00');
            if (currentSec >= waktuSubuhSec && currentSec < waktuSubuhSec + ms.idulFitri.durasi * 60) {
                setPrayerMode('idul_fitri', 'Idul Fitri', ms.idulFitri, waktuSubuhSec);
                return;
            }
        }
        if (ms.idulAdha && ms.idulAdha.aktif && ms.idulAdha.tanggal === todayStr) {
            var waktuSubuhSec2 = timeToSeconds(jadwal.subuh || '05:00');
            if (currentSec >= waktuSubuhSec2 && currentSec < waktuSubuhSec2 + ms.idulAdha.durasi * 60) {
                setPrayerMode('idul_adha', 'Idul Adha', ms.idulAdha, waktuSubuhSec2);
                return;
            }
        }

        // Daftar waktu sholat untuk pengecekan mode reguler
        var sholatList = [
            { nama: 'Subuh', waktu: jadwal.subuh, iqomah: config.iqomah.subuh },
            { nama: 'Dzuhur', waktu: jadwal.dzuhur, iqomah: config.iqomah.dzuhur },
            { nama: 'Ashar', waktu: jadwal.ashar, iqomah: config.iqomah.ashar },
            { nama: 'Maghrib', waktu: jadwal.maghrib, iqomah: config.iqomah.maghrib },
            { nama: 'Isya', waktu: jadwal.isya, iqomah: config.iqomah.isya },
        ];

        // Jumat: ganti Dzuhur jadi Jumat
        if (config.sholatJumat && now.getDay() === 5) {
            sholatList[1].nama = 'Jumat';
        }

        var modeFound = false;

        for (var i = 0; i < sholatList.length; i++) {
            var s = sholatList[i];
            var waktuSec = timeToSeconds(s.waktu);
            if (!waktuSec) continue;

            // Hitung batas waktu setiap fase (dalam detik)
            var dMenjelang = (ms.menjelangAdzan && ms.menjelangAdzan.aktif) ? ms.menjelangAdzan.durasi * 60 : 0;
            var dAdzan = (ms.adzan && ms.adzan.aktif) ? ms.adzan.durasi * 60 : 0;
            var dQobliyah = (ms.qobliyah && ms.qobliyah.aktif) ? ms.qobliyah.durasi * 60 : 0;
            var dIqomah = s.iqomah * 60;
            var dSholat = (ms.sholat && ms.sholat.aktif) ? ms.sholat.durasi * 60 : 0;
            var dBadiyah = (ms.badiyah && ms.badiyah.aktif) ? ms.badiyah.durasi * 60 : 0;

            var tMenjelang = waktuSec - dMenjelang;
            var tAdzan = waktuSec;
            var tQobliyah = tAdzan + dAdzan;
            var tIqomah = tQobliyah + dQobliyah;
            var tSholat = tIqomah + dIqomah;
            var tBadiyah = tSholat + dSholat;
            var tEnd = tBadiyah + dBadiyah;

            // Fase 1: Menjelang Adzan
            if (ms.menjelangAdzan && ms.menjelangAdzan.aktif && currentSec >= tMenjelang && currentSec < tAdzan) {
                var sisaSec = tAdzan - currentSec;
                setPrayerMode('menjelang_adzan', 'Menjelang ' + s.nama, ms.menjelangAdzan, tMenjelang, sisaSec);
                modeFound = true; break;
            }
            // Fase 2: Adzan
            if (ms.adzan && ms.adzan.aktif && currentSec >= tAdzan && currentSec < tQobliyah) {
                var sisaSec2 = tQobliyah - currentSec;
                setPrayerMode('adzan', 'Adzan ' + s.nama, ms.adzan, tAdzan, sisaSec2);
                modeFound = true; break;
            }
            // Fase 3: Qobliyah
            var isQobliyahAktif = ms.qobliyah && ms.qobliyah.aktif && (ms.qobliyah.opsi && ms.qobliyah.opsi[s.nama.toLowerCase()] == 1);
            if (isQobliyahAktif && currentSec >= tQobliyah && currentSec < tIqomah) {
                var sisaSec3 = tIqomah - currentSec;
                setPrayerMode('qobliyah', 'Sholat Qobliyah ' + s.nama, ms.qobliyah, tQobliyah, sisaSec3);
                modeFound = true; break;
            }
            // Fase 4: Iqomah (countdown timer) - sekarang menggunakan config ms.iqomah terpisah
            var iqomahAktif = ms.iqomah ? (ms.iqomah.aktif !== undefined ? ms.iqomah.aktif : 1) : 1;
            if (iqomahAktif && currentSec >= tIqomah && currentSec < tSholat) {
                var sisaSec4 = tSholat - currentSec;
                setPrayerMode('iqomah', 'Iqomah ' + s.nama, ms.iqomah || ms.sholat, tIqomah, sisaSec4);
                modeFound = true; break;
            }
            // Fase 5: Sholat Berlangsung (dengan jam berjalan)
            if (ms.sholat && ms.sholat.aktif && currentSec >= tSholat && currentSec < tBadiyah) {
                var sisaSec5 = tBadiyah - currentSec;
                setPrayerMode('sholat', 'Sholat ' + s.nama + ' Berlangsung', ms.sholat, tSholat, sisaSec5, true);
                modeFound = true; break;
            }
            // Fase 6: Ba'diyah
            var isBadiyahAktif = ms.badiyah && ms.badiyah.aktif && (ms.badiyah.opsi && ms.badiyah.opsi[s.nama.toLowerCase()] == 1);
            if (isBadiyahAktif && currentSec >= tBadiyah && currentSec < tEnd) {
                var sisaSec6 = tEnd - currentSec;
                setPrayerMode('badiyah', 'Sholat Ba\'diyah ' + s.nama, ms.badiyah, tBadiyah, sisaSec6);
                modeFound = true; break;
            }
        }

        // Cek mode Tarawih (setelah Isya, hanya bulan Ramadhan - diaktifkan manual)
        if (!modeFound && ms.tarawih && ms.tarawih.aktif) {
            var isyaSec = timeToSeconds(jadwal.isya || '19:00');
            // Tarawih mulai setelah seluruh fase Isya selesai
            var dAdzanT = (ms.adzan && ms.adzan.aktif) ? ms.adzan.durasi * 60 : 0;
            var dQobliyahT = (ms.qobliyah && ms.qobliyah.aktif) ? ms.qobliyah.durasi * 60 : 0;
            var dIqomahT = config.iqomah.isya * 60;
            var dSholatT = (ms.sholat && ms.sholat.aktif) ? ms.sholat.durasi * 60 : 0;
            var dBadiyahT = (ms.badiyah && ms.badiyah.aktif) ? ms.badiyah.durasi * 60 : 0;
            var tarawihStart = isyaSec + dAdzanT + dQobliyahT + dIqomahT + dSholatT + dBadiyahT;
            var tarawihEnd = tarawihStart + ms.tarawih.durasi * 60;

            if (currentSec >= tarawihStart && currentSec < tarawihEnd) {
                var sisaTar = tarawihEnd - currentSec;
                setPrayerMode('tarawih', 'Sholat Tarawih', ms.tarawih, tarawihStart, sisaTar, true);
                modeFound = true;
            }
        }

        // Jika tidak ada mode aktif, kembali ke normal
        if (!modeFound && state.prayerMode !== 'normal') {
            setPrayerMode('normal', '', null);
        }
    }

    /**
     * Set prayer mode overlay
     */
    function setPrayerMode(mode, title, modeCfg, startSec, sisaSec, showClock) {
        var overlay = document.getElementById('prayer-mode-overlay');
        if (!overlay) return;

        if (mode === 'normal') {
            // Sembunyikan overlay
            overlay.classList.remove('active');
            state.prayerMode = 'normal';
            state.prayerModeWaktu = '';
            return;
        }

        // Tampilkan overlay
        overlay.classList.add('active');
        state.prayerMode = mode;

        // Background image
        var bg = document.getElementById('prayer-overlay-bg');
        if (bg && modeCfg && modeCfg.gambar) {
            bg.style.backgroundImage = 'url(' + modeCfg.gambar + ')';
            bg.classList.add('has-image');
        } else if (bg) {
            bg.style.backgroundImage = 'none';
            bg.classList.remove('has-image');
        }

        // Icon berdasarkan mode
        var iconMap = {
            'menjelang_adzan': 'fas fa-bell fa-3x',
            'adzan': 'fas fa-volume-up fa-3x',
            'qobliyah': 'fas fa-praying-hands fa-3x',
            'iqomah': 'fas fa-hourglass-half fa-3x',
            'sholat': 'fas fa-kaaba fa-3x',
            'badiyah': 'fas fa-hands fa-3x',
            'tarawih': 'fas fa-moon fa-3x',
            'idul_adha': 'fas fa-star-and-crescent fa-3x',
            'idul_fitri': 'fas fa-star fa-3x',
        };
        var iconEl = document.getElementById('prayer-overlay-icon');
        if (iconEl) iconEl.innerHTML = '<i class="' + (iconMap[mode] || 'fas fa-mosque fa-3x') + '"></i>';

        // Title
        var titleEl = document.getElementById('prayer-overlay-title');
        if (titleEl) titleEl.textContent = title;

        // Subtitle berdasarkan mode
        var subtitleMap = {
            'menjelang_adzan': 'Bersiap untuk menunaikan ibadah sholat',
            'adzan': 'Hayya \'alash Sholah — Hayya \'alal Falaah',
            'qobliyah': 'Waktu sholat sunnah qobliyah',
            'iqomah': 'Luruskan dan rapatkan shaf',
            'sholat': 'Harap menjaga ketenangan',
            'badiyah': 'Waktu sholat sunnah ba\'diyah',
            'tarawih': 'Sholat Tarawih sedang berlangsung',
            'idul_adha': 'Taqobbalallahu Minna Wa Minkum',
            'idul_fitri': 'Taqobbalallahu Minna Wa Minkum',
        };
        var subtitleEl = document.getElementById('prayer-overlay-subtitle');
        if (subtitleEl) subtitleEl.textContent = subtitleMap[mode] || '';

        // Timer countdown
        var timerEl = document.getElementById('prayer-overlay-timer');
        if (timerEl) {
            if (sisaSec && sisaSec > 0) {
                var jam = Math.floor(sisaSec / 3600);
                var men = Math.floor((sisaSec % 3600) / 60);
                var det = Math.floor(sisaSec % 60);
                if (jam > 0) {
                    timerEl.textContent = padZero(jam) + ':' + padZero(men) + ':' + padZero(det);
                } else {
                    timerEl.textContent = padZero(men) + ':' + padZero(det);
                }
                timerEl.style.display = 'block';
            } else {
                timerEl.style.display = 'none';
            }
        }

        // Jam berjalan (saat sholat berlangsung)
        var clockEl = document.getElementById('prayer-overlay-clock');
        if (clockEl) {
            if (showClock) {
                var now = new Date();
                clockEl.textContent = padZero(now.getHours()) + ':' + padZero(now.getMinutes()) + ':' + padZero(now.getSeconds());
                clockEl.style.display = 'block';
            } else {
                clockEl.style.display = 'none';
            }
        }

        // Set class mode pada overlay untuk styling berbeda
        overlay.className = 'prayer-overlay active mode-' + mode;
    }

    /**
     * Konversi string waktu HH:MM ke total detik
     */
    function timeToSeconds(timeStr) {
        if (!timeStr || timeStr === '--:--') return 0;
        var parts = timeStr.split(':');
        return parseInt(parts[0]) * 3600 + parseInt(parts[1]) * 60;
    }

    // ============================================================
    // SLIDE KONTEN
    // ============================================================
    function mulaiSlide() {
        var slides = document.querySelectorAll('.display-slide');
        state.totalSlides = slides.length;

        if (state.totalSlides <= 1) return;

        state.slideTimer = setInterval(function () {
            // Sembunyikan slide saat ini
            slides[state.currentSlide].classList.remove('slide-active');
            slides[state.currentSlide].classList.add('slide-exit');

            // Pindah ke slide berikutnya
            state.currentSlide = (state.currentSlide + 1) % state.totalSlides;
            slides[state.currentSlide].classList.remove('slide-exit');
            slides[state.currentSlide].classList.add('slide-active');

            // Update indikator slide
            updateSlideIndicator();
        }, config.intervalSlide * 1000);
    }

    function updateSlideIndicator() {
        var indicators = document.querySelectorAll('.slide-indicator');
        indicators.forEach(function (ind, idx) {
            ind.classList.toggle('active', idx === state.currentSlide);
        });
    }

    // ============================================================
    // RUNNING TEXT
    // ============================================================
    function mulaiRunningText() {
        var el = document.getElementById('running-text-content');
        if (!el) return;

        // Running text sudah dihandle oleh CSS marquee
        // Kita hanya perlu set konten
    }

    function updateRunningText(text) {
        var el = document.getElementById('running-text-content');
        if (el && text) {
            el.textContent = text;
        }
    }

    // ============================================================
    // SINKRONISASI DATA
    // ============================================================
    function mulaiSync() {
        // Sync pertama kali
        syncData();

        // Interval sync reguler (fallback jikalau polling ringan gagal)
        state.syncTimer = setInterval(function () {
            if (state.isOnline) {
                syncData();
            }
        }, config.intervalSync * 1000);

        // Interval polling ringan (fast update)
        if (config.apiCheckUpdateUrl) {
            state.fastSyncTimer = setInterval(function () {
                if (state.isOnline) checkUpdateMode();
            }, 2000); // Tiap 2 detik
        }
    }

    /**
     * Hit endpoint super ringan untuk mendeteksi perubahan `updated_at`
     */
    function checkUpdateMode() {
        if (!config.apiCheckUpdateUrl) return;

        fetch(config.apiCheckUpdateUrl)
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success && data.updated_at) {
                    if (state.lastUpdatedServer === null) {
                        state.lastUpdatedServer = data.updated_at;
                    } else if (state.lastUpdatedServer !== data.updated_at) {
                        console.log('[DisplayEngine] Perubahan terdeteksi:', data.updated_at);
                        state.lastUpdatedServer = data.updated_at;
                        // Trigger full sync
                        syncData();
                    }
                }
            })
            .catch(function (err) { /* ignore polling errors */ });
    }

    function syncData() {
        if (!config.apiUrl) return;

        fetch(config.apiUrl)
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.success) {
                    // Update konfigurasi dari server
                    if (data.display) {
                        var d = data.display;
                        config.latitude = d.latitude;
                        config.longitude = d.longitude;
                        config.metode = d.metode_hitung;
                        config.intervalSync = d.interval_sync;
                        config.koreksi = d.koreksi;
                        config.iqomah = d.iqomah;

                        config.modeSholat = d.modeSholat;

                        // Re-set PrayTimes
                        if (typeof PrayTimes !== 'undefined') {
                            PrayTimes.setMethod(config.metode);
                            PrayTimes.tune({
                                fajr: config.koreksi.subuh,
                                dhuhr: config.koreksi.dzuhur,
                                asr: config.koreksi.ashar,
                                maghrib: config.koreksi.maghrib,
                                isha: config.koreksi.isya
                            });
                        }

                        // Recalc jadwal sholat
                        hitungJadwalSholat();

                        // Update running text
                        updateRunningText(d.running_text);

                        // Update wallpaper
                        if (d.wallpaper) {
                            var body = document.querySelector('.display-container');
                            if (body) {
                                body.style.backgroundImage = 'url(' + d.wallpaper + ')';
                            }
                        }
                    }

                    // Update konten slide
                    if (data.konten) {
                        state.konten = data.konten;
                        updateSlideKonten(data.konten);
                    }

                    // Pre-fetch gambar-gambar penting agar masuk ke Cache Service Worker
                    prefetchGambarPending(data.display);

                    // Simpan ke cache untuk offline
                    simpanKeCache('displayData', data);
                    state.lastSync = new Date().toISOString();

                    // Update indikator status
                    updateStatusIndikator(true);

                }
            })
            .catch(function (err) {
                console.warn('[DisplayEngine] Sync gagal:', err);
                updateStatusIndikator(false);
            });
    }

    function updateSlideKonten(kontenByTipe) {
        // Update info kegiatan jika elemennya ada
        var elInfo = document.getElementById('info-kegiatan-container');
        if (elInfo && kontenByTipe.info_kegiatan) {
            var html = '';
            kontenByTipe.info_kegiatan.forEach(function (k) {
                html += '<div class="info-item">';
                html += '<h4>' + escHtml(k.judul || '') + '</h4>';
                html += '<p>' + (k.konten || '') + '</p>';
                html += '</div>';
            });
            elInfo.innerHTML = html;
        }

        // Update pengumuman
        var elPengumuman = document.getElementById('pengumuman-container');
        if (elPengumuman && kontenByTipe.pengumuman) {
            var htmlP = '';
            kontenByTipe.pengumuman.forEach(function (k) {
                htmlP += '<div class="pengumuman-item">';
                htmlP += '<strong>' + escHtml(k.judul || '') + '</strong>';
                htmlP += '<p>' + (k.konten || '') + '</p>';
                htmlP += '</div>';
            });
            elPengumuman.innerHTML = htmlP;
        }
    }

    // ============================================================
    // CACHE OFFLINE (localStorage)
    // ============================================================
    function simpanKeCache(key, data) {
        try {
            var cacheKey = 'display_' + config.displayId + '_' + key;
            localStorage.setItem(cacheKey, JSON.stringify({
                data: data,
                timestamp: new Date().toISOString()
            }));
        } catch (e) {
            console.warn('[DisplayEngine] Gagal simpan cache:', e);
        }
    }

    /**
     * Memaksa browser mengunduh gambar secara transparan (background)
     * sehingga Service Worker bisa mencegat dan menyimpannya di Cache.
     */
    function prefetchGambarPending(displayData) {
        if (!displayData) return;

        var imagesToFetch = [];

        // 1. Wallpaper utama
        if (displayData.wallpaper) {
            imagesToFetch.push(displayData.wallpaper);
        }

        // 2. Gambar overlay mode sholat (jika ada)
        if (displayData.modeSholat) {
            var modes = ['menjelangAdzan', 'adzan', 'qobliyah', 'iqomah', 'sholat', 'badiyah', 'tarawih', 'idulAdha', 'idulFitri'];
            modes.forEach(function (m) {
                if (displayData.modeSholat[m] && displayData.modeSholat[m].gambar) {
                    imagesToFetch.push(displayData.modeSholat[m].gambar);
                }
            });
        }

        // 3. (Opsional) Gambar slide konten - jika diperlukan untuk full offline
        // Di sini kita fokus ke gambar overlay/wallpaper dulu sesuai request.

        // Hapus duplikat
        var uniqueImages = imagesToFetch.filter(function (item, pos) {
            return imagesToFetch.indexOf(item) == pos && item !== '';
        });

        // Lakukan fetching diam-diam menggunakan elemen Image
        uniqueImages.forEach(function (url) {
            var imgUrl = url;
            // Jika path relatif (dimulai dari /uploads), pastikan base URL benar
            if (url.startsWith('/uploads/')) {
                // Konversi URL relatif ke absolute agar service worker bisa tangkap dengan benar
                var baseUrl = window.location.origin;
                var pathArray = window.location.pathname.split('/');
                if (pathArray.length > 1 && pathArray[1] !== 'display') {
                    // Asumsi subfolder, misal ditaruh di localhost/kua/
                    baseUrl += '/' + pathArray[1];
                }
                imgUrl = baseUrl + url;
            }

            var img = new Image();
            img.src = imgUrl; // Proses ini akan memicu permintaan GET yang ditangkap Service Worker
        });
    }

    function bacaDariCache(key) {
        try {
            var cacheKey = 'display_' + config.displayId + '_' + key;
            var cached = localStorage.getItem(cacheKey);
            if (cached) {
                return JSON.parse(cached);
            }
        } catch (e) {
            console.warn('[DisplayEngine] Gagal baca cache:', e);
        }
        return null;
    }

    function muatDariCache() {
        // Muat jadwal sholat dari cache jika belum terhitung
        var cachedJadwal = bacaDariCache('jadwalSholat');
        if (cachedJadwal && cachedJadwal.data && !state.jadwalSholat.subuh) {
            state.jadwalSholat = cachedJadwal.data;
            updateTampilanJadwal();
            console.log('[DisplayEngine] Jadwal dimuat dari cache');
        }

        // Selalu muat data display dari cache untuk restore configurasi terakhir 
        // walau sedang initial load (sebelum sync) atau offline penuh
        var cachedData = bacaDariCache('displayData');
        if (cachedData && cachedData.data) {
            console.log('[DisplayEngine] Memuat pengaturan dari cache lokal');
            var d = cachedData.data.display;

            if (d) {
                // Restore Config
                if (d.latitude !== undefined) config.latitude = d.latitude;
                if (d.longitude !== undefined) config.longitude = d.longitude;
                if (d.metode_hitung !== undefined) config.metode = d.metode_hitung;
                if (d.interval_sync !== undefined) config.intervalSync = d.interval_sync;
                if (d.koreksi) config.koreksi = d.koreksi;
                if (d.iqomah) config.iqomah = d.iqomah;

                if (d.modeSholat) {
                    config.modeSholat = d.modeSholat;
                }

                // Set parameter di PrayTimes untuk hitungan manual (offline)
                if (typeof PrayTimes !== 'undefined') {
                    PrayTimes.setMethod(config.metode);
                    PrayTimes.tune({
                        fajr: config.koreksi.subuh,
                        dhuhr: config.koreksi.dzuhur,
                        asr: config.koreksi.ashar,
                        maghrib: config.koreksi.maghrib,
                        isha: config.koreksi.isya
                    });
                }

                if (d.running_text) {
                    updateRunningText(d.running_text);
                }

                // Restore wallpaper setting
                if (d.wallpaper) {
                    var body = document.querySelector('.display-container');
                    if (body) body.style.backgroundImage = 'url(' + d.wallpaper + ')';
                }
            }

            if (cachedData.data.konten) {
                state.konten = cachedData.data.konten;
                updateSlideKonten(cachedData.data.konten);
            }
        }
    }

    // ============================================================
    // MONITOR KONEKSI
    // ============================================================
    function monitorKoneksi() {
        window.addEventListener('online', function () {
            state.isOnline = true;
            updateStatusIndikator(true);
            syncData(); // Sync segera saat online kembali
            console.log('[DisplayEngine] Koneksi online');
        });

        window.addEventListener('offline', function () {
            state.isOnline = false;
            updateStatusIndikator(false);
            console.log('[DisplayEngine] Koneksi offline - menggunakan cache');
        });
    }

    function updateStatusIndikator(isOnline) {
        var el = document.getElementById('status-koneksi');
        if (el) {
            if (isOnline) {
                el.innerHTML = '<i class="fas fa-wifi"></i>';
                el.className = 'status-online';
            } else {
                el.innerHTML = '<i class="fas fa-wifi-slash"></i> Offline';
                el.className = 'status-offline';
            }
        }
    }

    // ============================================================
    // UTILITAS
    // ============================================================
    function padZero(n) {
        return (n < 10 ? '0' : '') + n;
    }

    function timeToMinutes(timeStr) {
        if (!timeStr || timeStr === '--:--') return 0;
        var parts = timeStr.split(':');
        return parseInt(parts[0]) * 60 + parseInt(parts[1]);
    }

    function capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function escHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    /**
     * Konversi tanggal Masehi ke Hijriah
     * Menggunakan algoritma tabular Kuwaiti via Julian Day Number (JDN)
     * Akurasi: ±1-2 hari dari kalender observasi
     */
    function getHijriDate(date) {
        // Clone object date agar tidak merubah referensi aslinya
        var baseDate = new Date(date.getTime());

        // --- LOGIKA 1: Pergantian hari Islam (Ba'da Maghrib) ---
        // Jika jam saat ini sudah melewati jam Maghrib hari ini, maka kalender Hijriah sudah masuk hari esok.
        if (state.jadwalSholat && state.jadwalSholat.maghrib) {
            var maghribMins = timeToMinutes(state.jadwalSholat.maghrib);
            var currentMins = baseDate.getHours() * 60 + baseDate.getMinutes();
            if (currentMins >= maghribMins) {
                baseDate.setDate(baseDate.getDate() + 1);
            }
        }

        // --- LOGIKA 2: Koreksi Manual dari Admin ---
        // Penyesuaian +1 atau -1 hari sesuai input form Admin.
        var koreksiHijriah = (config.koreksi && config.koreksi.hijriah) ? parseInt(config.koreksi.hijriah) : 0;
        if (koreksiHijriah !== 0 && !isNaN(koreksiHijriah)) {
            baseDate.setDate(baseDate.getDate() + koreksiHijriah);
        }

        var namaBulanHijri = [
            'Muharram', 'Safar', 'Rabiul Awal', 'Rabiul Akhir',
            'Jumadil Awal', 'Jumadil Akhir', 'Rajab', 'Syaban',
            'Ramadhan', 'Syawal', 'Dzulqaidah', 'Dzulhijjah'
        ];

        // Langkah 1: Hitung Julian Day Number dari tanggal Masehi (Gregorian) yang sudah dimodifikasi
        var gYear = baseDate.getFullYear();
        var gMonth = baseDate.getMonth() + 1;
        var gDay = baseDate.getDate();

        var a = Math.floor((14 - gMonth) / 12);
        var y = gYear + 4800 - a;
        var m = gMonth + 12 * a - 3;
        var jdn = gDay + Math.floor((153 * m + 2) / 5) + 365 * y
            + Math.floor(y / 4) - Math.floor(y / 100)
            + Math.floor(y / 400) - 32045;

        // Langkah 2: Konversi JDN ke kalender Hijriah (algoritma tabular Kuwaiti)
        var l = jdn - 1948440 + 10632;
        var n = Math.floor((l - 1) / 10631);
        l = l - 10631 * n + 354;
        var j = Math.floor((10985 - l) / 5316) * Math.floor((50 * l) / 17719)
            + Math.floor(l / 5670) * Math.floor((43 * l) / 15238);
        l = l - Math.floor((30 - j) / 15) * Math.floor((17719 * j) / 50)
            - Math.floor(j / 16) * Math.floor((15238 * j) / 43) + 29;

        var hMonth = Math.floor((24 * l) / 709);
        var hDay = l - Math.floor((709 * hMonth) / 24);
        var hYear = 30 * n + j - 30;

        return hDay + ' ' + namaBulanHijri[hMonth - 1] + ' ' + hYear + ' H';
    }

    // ============================================================
    // PUBLIC API
    // ============================================================
    return {
        init: init,
        syncData: syncData,
        hitungJadwalSholat: hitungJadwalSholat,
        getState: function () { return state; },
        getConfig: function () { return config; }
    };
})();
