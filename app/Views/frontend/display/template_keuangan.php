<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Keuangan - <?= esc($namaMasjid) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/display/css/display-style.css') ?>">
    <style>
        /* Override khusus template keuangan */
        .template-keuangan .keuangan-header {
            text-align: center;
            padding: 8px;
            background: rgba(0,100,50,0.2);
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .template-keuangan .keuangan-header h3 {
            font-size: 1rem;
            color: #4ade80;
            margin: 0;
        }
        .template-keuangan .keuangan-header .periode {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.5);
        }
        .template-keuangan .chart-placeholder {
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
            padding: 20px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 20px;
            height: 180px;
        }
        .template-keuangan .chart-bar {
            width: 60px;
            border-radius: 6px 6px 0 0;
            text-align: center;
            transition: height 0.5s ease;
        }
        .template-keuangan .chart-bar .bar-label {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.6);
            margin-top: 5px;
        }
        .template-keuangan .chart-bar .bar-value {
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
<div class="display-container template-keuangan <?= ($display['orientasi'] ?? 'horizontal') === 'vertikal' ? 'orientasi-vertikal' : '' ?>"
     <?php
$logo = !empty($display['logo']) ? base_url($display['logo']) : base_url('assets/img/logo-kemenag.png');
$wallpaper = !empty($display['wallpaper']) ? base_url($display['wallpaper']) : base_url('assets/img/default-masjid.jpg');

// Parse opsi waktu sholat
$opsiWaktu = !empty($display['opsi_waktu_sholat']) ? json_decode($display['opsi_waktu_sholat'], true) : [];
$opsiQobliyah = $opsiWaktu['qobliyah'] ?? ['subuh'=>1, 'dzuhur'=>1, 'ashar'=>0, 'maghrib'=>1, 'isya'=>1];
$opsiBadiyah  = $opsiWaktu['badiyah']  ?? ['subuh'=>0, 'dzuhur'=>1, 'ashar'=>0, 'maghrib'=>1, 'isya'=>1];
?>
     style="background-image: url('<?= $wallpaper ?>')">
    <div class="display-overlay"></div>

    <!-- WRAPPER UNTUK AUTO-SCALING PROPORSI LAYAR TV -->
    <div id="display-scaler" class="display-scale-wrapper">
        <div class="display-content">

        <!-- ============================================================ -->
        <!-- HEADER -->
        <!-- ============================================================ -->
        <div class="display-header">
            <div class="header-left">
                <?php if (!empty($display['logo'])): ?>
                    <img src="<?= base_url($display['logo']) ?>" alt="Logo" class="header-logo">
                <?php else: ?>
                    <div class="header-logo" style="background: linear-gradient(135deg, #059669, #10b981); display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-mosque" style="font-size: 1.8rem; color: #fff;"></i>
                    </div>
                <?php endif; ?>
                <div class="header-info">
                    <h1><?= esc($namaMasjid) ?></h1>
                    <div class="alamat">Laporan Keuangan - <?= date('F Y') ?></div>
                </div>
            </div>
            <div class="header-right">
                <div class="tanggal" id="tanggal-masehi">Memuat...</div>
                <div class="tanggal-hijriah" id="tanggal-hijriah"></div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- BODY: Keuangan | Jadwal + Info -->
        <!-- ============================================================ -->
        <div class="display-body">
            <!-- PANEL KIRI: Laporan Keuangan -->
            <div class="panel-keuangan">
                <!-- Ringkasan Keuangan -->
                <div class="keuangan-panel">
                    <h3><i class="fas fa-chart-pie mr-1"></i> Laporan Keuangan <?= date('F Y') ?></h3>

                    <div class="keuangan-summary" id="keuangan-summary">
                        <div class="keuangan-card pemasukan">
                            <div class="label">Pemasukan</div>
                            <div class="nilai" id="keuangan-pemasukan">Rp 0</div>
                        </div>
                        <div class="keuangan-card pengeluaran">
                            <div class="label">Pengeluaran</div>
                            <div class="nilai" id="keuangan-pengeluaran">Rp 0</div>
                        </div>
                        <div class="keuangan-card saldo">
                            <div class="label">Saldo</div>
                            <div class="nilai" id="keuangan-saldo">Rp 0</div>
                        </div>
                    </div>

                    <!-- Visual Chart Sederhana -->
                    <div class="chart-placeholder" id="keuangan-chart">
                        <div class="chart-bar" style="height: 80px; background: linear-gradient(to top, #059669, #4ade80);">
                            <div class="bar-value" style="color:#4ade80;" id="chart-pemasukan-val">0</div>
                            <div class="bar-label">Pemasukan</div>
                        </div>
                        <div class="chart-bar" style="height: 50px; background: linear-gradient(to top, #dc2626, #f87171);">
                            <div class="bar-value" style="color:#f87171;" id="chart-pengeluaran-val">0</div>
                            <div class="bar-label">Pengeluaran</div>
                        </div>
                    </div>
                </div>

                <!-- Transaksi Terakhir -->
                <div class="keuangan-panel" style="flex:1;">
                    <h3><i class="fas fa-list mr-1"></i> Transaksi Terakhir</h3>
                    <div class="keuangan-transaksi" id="keuangan-transaksi">
                        <table>
                            <tbody id="tabel-transaksi-body">
                                <tr>
                                    <td colspan="3" style="text-align:center; color:rgba(255,255,255,0.3); padding:20px;">
                                        <i class="fas fa-spinner fa-spin"></i> Memuat data keuangan...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Konten Laporan Keuangan dari Admin -->
                <?php if (!empty($kontenByTipe['laporan_keuangan'])): ?>
                <div class="info-panel">
                    <?php foreach ($kontenByTipe['laporan_keuangan'] as $lap): ?>
                        <div class="info-item">
                            <h4><?= esc($lap['judul'] ?? '') ?></h4>
                            <p><?= $lap['konten'] ?? '' ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- PANEL KANAN: Jadwal + Info -->
            <div class="panel-info">
                <!-- Jam Digital -->
                <div class="jam-container">
                    <div class="jam-digital jam-digital-small" id="jam-digital">00:00:00</div>
                </div>

                <!-- Jadwal Sholat -->
                <div class="jadwal-container">
                    <div class="jadwal-title" style="font-size:0.85rem;">
                        <i class="fas fa-clock mr-1"></i> Jadwal Sholat
                    </div>
                    <table class="jadwal-table">
                        <tr class="jadwal-row" data-waktu="subuh"><td class="waktu-nama">Subuh</td><td class="waktu-jam" id="jadwal-subuh">--:--</td></tr>
                        <tr class="jadwal-row" data-waktu="dzuhur"><td class="waktu-nama">Dzuhur</td><td class="waktu-jam" id="jadwal-dzuhur">--:--</td></tr>
                        <tr class="jadwal-row" data-waktu="ashar"><td class="waktu-nama">Ashar</td><td class="waktu-jam" id="jadwal-ashar">--:--</td></tr>
                        <tr class="jadwal-row" data-waktu="maghrib"><td class="waktu-nama">Maghrib</td><td class="waktu-jam" id="jadwal-maghrib">--:--</td></tr>
                        <tr class="jadwal-row" data-waktu="isya"><td class="waktu-nama">Isya</td><td class="waktu-jam" id="jadwal-isya">--:--</td></tr>
                    </table>
                </div>

                <!-- Countdown -->
                <div class="countdown-container">
                    <div class="countdown-label" id="countdown-label">Menuju Sholat</div>
                    <div class="countdown-waktu countdown-waktu-small" id="countdown-waktu">00:00:00</div>
                    <div class="countdown-progress-bar">
                        <div class="progress-fill" id="countdown-progress" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Info Kegiatan -->
                <div class="info-panel" style="flex:1; overflow-y:auto;">
                    <h3><i class="fas fa-bullhorn mr-1"></i> Kegiatan</h3>
                    <div id="info-kegiatan-container">
                        <?php if (!empty($kontenByTipe['info_kegiatan'])): ?>
                            <?php foreach (array_slice($kontenByTipe['info_kegiatan'], 0, 5) as $info): ?>
                                <div class="info-item">
                                    <h4><?= esc($info['judul'] ?? '') ?></h4>
                                    <p><?= mb_substr(strip_tags($info['konten'] ?? ''), 0, 100) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color:rgba(255,255,255,0.3); text-align:center; font-size:0.8rem;">Belum ada kegiatan</p>
                        <?php endif; ?>
                    </div>
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

<!-- Hidden elements for imsak/terbit (used by engine) -->
<span id="jadwal-imsak" style="display:none;">--:--</span>
<span id="jadwal-terbit" style="display:none;">--:--</span>

<!-- Scripts -->
<script src="<?= base_url('assets/display/js/praytimes.js') ?>"></script>
<script src="<?= base_url('assets/display/js/display-engine.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Display Engine
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

        // Muat data keuangan
        muatDataKeuangan();
        // Refresh keuangan setiap 5 menit
        setInterval(muatDataKeuangan, 300000);
    });

    /**
     * Muat data keuangan dari API
     */
    function muatDataKeuangan() {
        var url = '<?= base_url('display/api_keuangan/' . $display['id']) ?>';
        fetch(url)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success && data.keuangan) {
                    var k = data.keuangan;

                    // Update ringkasan
                    var elPemasukan = document.getElementById('keuangan-pemasukan');
                    var elPengeluaran = document.getElementById('keuangan-pengeluaran');
                    var elSaldo = document.getElementById('keuangan-saldo');

                    if (elPemasukan) elPemasukan.textContent = formatRupiah(k.pemasukan);
                    if (elPengeluaran) elPengeluaran.textContent = formatRupiah(k.pengeluaran);
                    if (elSaldo) elSaldo.textContent = formatRupiah(k.saldo);

                    // Update chart bars
                    var maxVal = Math.max(k.pemasukan, k.pengeluaran, 1);
                    var barPemasukan = document.querySelector('.chart-bar:first-child');
                    var barPengeluaran = document.querySelector('.chart-bar:last-child');
                    if (barPemasukan) barPemasukan.style.height = Math.max(20, (k.pemasukan / maxVal) * 150) + 'px';
                    if (barPengeluaran) barPengeluaran.style.height = Math.max(20, (k.pengeluaran / maxVal) * 150) + 'px';

                    var chartPVal = document.getElementById('chart-pemasukan-val');
                    var chartEVal = document.getElementById('chart-pengeluaran-val');
                    if (chartPVal) chartPVal.textContent = formatRupiahShort(k.pemasukan);
                    if (chartEVal) chartEVal.textContent = formatRupiahShort(k.pengeluaran);

                    // Update tabel transaksi
                    var tbody = document.getElementById('tabel-transaksi-body');
                    if (tbody && k.transaksi) {
                        var html = '';
                        if (k.transaksi.length === 0) {
                            html = '<tr><td colspan="3" style="text-align:center; color:rgba(255,255,255,0.3); padding:15px;">Belum ada transaksi bulan ini</td></tr>';
                        } else {
                            k.transaksi.forEach(function(t) {
                                var cls = t.jenis === 'pemasukan' ? 'pemasukan' : 'pengeluaran';
                                var sign = t.jenis === 'pemasukan' ? '+' : '-';
                                html += '<tr>';
                                html += '<td>' + t.tanggal + '</td>';
                                html += '<td>' + escHtml(t.keterangan) + '</td>';
                                html += '<td class="' + cls + '" style="text-align:right;white-space:nowrap;">' + sign + ' ' + formatRupiah(t.jumlah) + '</td>';
                                html += '</tr>';
                            });
                        }
                        tbody.innerHTML = html;
                    }

                    // Simpan ke cache
                    try {
                        localStorage.setItem('display_<?= $display['id'] ?>_keuangan', JSON.stringify({
                            data: k, timestamp: new Date().toISOString()
                        }));
                    } catch(e) {}
                }
            })
            .catch(function(err) {
                console.warn('Gagal muat keuangan:', err);
                // Coba dari cache
                try {
                    var cached = localStorage.getItem('display_<?= $display['id'] ?>_keuangan');
                    if (cached) {
                        var c = JSON.parse(cached);
                        if (c.data) {
                            document.getElementById('keuangan-pemasukan').textContent = formatRupiah(c.data.pemasukan);
                            document.getElementById('keuangan-pengeluaran').textContent = formatRupiah(c.data.pengeluaran);
                            document.getElementById('keuangan-saldo').textContent = formatRupiah(c.data.saldo);
                        }
                    }
                } catch(e2) {}
            });
    }

    function formatRupiah(angka) {
        return 'Rp ' + Number(angka).toLocaleString('id-ID');
    }

    function formatRupiahShort(angka) {
        if (angka >= 1000000) return 'Rp ' + (angka / 1000000).toFixed(1) + 'Jt';
        if (angka >= 1000) return 'Rp ' + (angka / 1000).toFixed(0) + 'Rb';
        return 'Rp ' + angka;
    }

    function escHtml(str) {
        var div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }
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
