<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<?php
// Tentukan apakah mode edit atau create
$isEdit    = !empty($display);
$formUrl   = $isEdit ? base_url('admin/display-masjid/update/' . $display['id']) : base_url('admin/display-masjid/store');
?>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <ul class="mb-0">
        <?php foreach (session()->getFlashdata('errors') as $err): ?>
            <li><?= esc($err) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form id="form-display" action="<?= $formUrl ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row">
        <!-- KOLOM KIRI: Pengaturan Umum -->
        <div class="col-lg-7">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-cog mr-2"></i>Pengaturan Umum</h3>
                </div>
                <div class="card-body">
                    <!-- Pilih Masjid -->
                    <div class="form-group">
                        <label for="id_masjid_mushola">Masjid / Mushola <span class="text-danger">*</span></label>
                        <?php $isOperatorMode = in_groups('OperatorMasjidMushola') && !in_groups('SuperAdmin') && !in_groups('Admin'); ?>
                        
                        <select name="id_masjid_mushola" id="id_masjid_mushola" class="form-control <?= $isOperatorMode ? '' : 'select2' ?>" required <?= $isOperatorMode || count($masjidList) == 1 ? 'disabled' : '' ?>>
                            <?php if (!$isOperatorMode): ?>
                                <option value="">-- Pilih Masjid/Mushola --</option>
                            <?php endif; ?>
                            
                            <?php foreach ($masjidList as $m): ?>
                                <option value="<?= $m['id_masjid_mushola'] ?>"
                                    <?= old('id_masjid_mushola', $display['id_masjid_mushola'] ?? ($selectedMasjidId ?? '')) == $m['id_masjid_mushola'] ? 'selected' : '' ?>>
                                    <?= esc($m['nama']) ?> (<?= esc($m['jenis']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <?php if ($isOperatorMode || count($masjidList) == 1): ?>
                            <!-- Kirim value tersembunyi karena select disabled tidak ikut tersubmit (meski backend sudah antisipasi) -->
                            <input type="hidden" name="id_masjid_mushola" value="<?= $selectedMasjidId ?? ($display['id_masjid_mushola'] ?? $masjidList[0]['id_masjid_mushola']) ?>">
                        <?php endif; ?>
                    </div>

                    <!-- Nama Display -->
                    <div class="form-group">
                        <label for="nama_display">Nama Display <span class="text-danger">*</span></label>
                        <input type="text" name="nama_display" id="nama_display" class="form-control"
                               value="<?= old('nama_display', $display['nama_display'] ?? 'Display Utama') ?>"
                               placeholder="Contoh: Display Utama, Display Lantai 2" required>
                    </div>

                    <!-- Nama Masjid di Display (override) -->
                    <div class="form-group">
                        <label for="nama_masjid_display">Nama Masjid (Tampil di Display)</label>
                        <input type="text" name="nama_masjid_display" id="nama_masjid_display" class="form-control"
                               value="<?= old('nama_masjid_display', $display['nama_masjid_display'] ?? '') ?>"
                               placeholder="Kosongkan untuk menggunakan nama masjid dari data">
                        <small class="form-text text-muted">Opsional. Jika diisi, akan menggantikan nama masjid dari database.</small>
                    </div>

                    <!-- Alamat Display -->
                    <div class="form-group">
                        <label for="alamat_display">Alamat (Tampil di Display)</label>
                        <textarea name="alamat_display" id="alamat_display" class="form-control" rows="2"
                                  placeholder="Kosongkan untuk menggunakan alamat dari data masjid"><?= old('alamat_display', $display['alamat_display'] ?? '') ?></textarea>
                    </div>

                    <!-- Template & Orientasi -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="template_aktif">Template Display <span class="text-danger">*</span></label>
                                <select name="template_aktif" id="template_aktif" class="form-control" required>
                                    <?php
                                    $templates = [
                                        'klasik'   => '🕌 Klasik - Jadwal Sholat Tradisional',
                                        'modern'   => '✨ Modern - Slide Informasi + Jadwal',
                                        'modern1'  => '🌙 Modern 1 - Bar Jadwal Horizontal + Kaligrafi',
                                        'modern2'  => '🕋 Modern 2 - Jadwal Vertikal + Jam Besar',
                                        'keuangan' => '💰 Keuangan - Laporan + Jadwal',
                                    ];
                                    foreach ($templates as $key => $label):
                                    ?>
                                        <option value="<?= $key ?>"
                                            <?= old('template_aktif', $display['template_aktif'] ?? 'klasik') === $key ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="orientasi">Orientasi Display <span class="text-danger">*</span></label>
                                <select name="orientasi" id="orientasi" class="form-control" required>
                                    <option value="horizontal" <?= old('orientasi', $display['orientasi'] ?? 'horizontal') === 'horizontal' ? 'selected' : '' ?>>
                                        🖥️ Horizontal (Landscape) - Default
                                    </option>
                                    <option value="vertikal" <?= old('orientasi', $display['orientasi'] ?? 'horizontal') === 'vertikal' ? 'selected' : '' ?>>
                                        📱 Vertikal (Portrait)
                                    </option>
                                </select>
                                <small class="form-text text-muted">Pilih sesuai posisi TV/monitor.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Running Text -->
                    <div class="form-group">
                        <label for="running_text">Running Text (Teks Berjalan)</label>
                        <textarea name="running_text" id="running_text" class="form-control" rows="3"
                                  placeholder="Teks berjalan di bagian bawah display. Pisahkan dengan | untuk multiple teks."><?= old('running_text', $display['running_text'] ?? '') ?></textarea>
                        <small class="form-text text-muted">Contoh: Selamat datang di Masjid Al-Ikhlas | Jadwal Pengajian Rutin Setiap Malam Selasa</small>
                    </div>

                    <!-- Logo & Wallpaper -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="logo">Logo Masjid</label>
                                <?php if ($isEdit && !empty($display['logo'])): ?>
                                    <div class="mb-2">
                                        <img src="<?= base_url($display['logo']) ?>" alt="Logo" class="img-thumbnail" style="max-height: 80px;">
                                    </div>
                                <?php endif; ?>
                                <div class="custom-file">
                                    <input type="file" name="logo" id="logo" class="custom-file-input" accept="image/*">
                                    <label class="custom-file-label" for="logo">Pilih file...</label>
                                </div>
                                <small class="form-text text-muted">Format: JPG, PNG. Maks 2MB.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="wallpaper">Wallpaper / Background</label>
                                <?php if ($isEdit && !empty($display['wallpaper'])): ?>
                                    <div class="mb-2">
                                        <img src="<?= base_url($display['wallpaper']) ?>" alt="Wallpaper" class="img-thumbnail" style="max-height: 80px;">
                                    </div>
                                <?php endif; ?>
                                <div class="custom-file">
                                    <input type="file" name="wallpaper" id="wallpaper" class="custom-file-input" accept="image/*">
                                    <label class="custom-file-label" for="wallpaper">Pilih file...</label>
                                </div>
                                <small class="form-text text-muted">Format: JPG, PNG. Maks 5MB. Resolusi disarankan 1920x1080.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Status & Interval -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="aktif">Status Display</label>
                                <select name="aktif" id="aktif" class="form-control">
                                    <option value="1" <?= old('aktif', $display['aktif'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= old('aktif', $display['aktif'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="interval_sync">Interval Sync (detik)</label>
                                <input type="number" name="interval_sync" id="interval_sync" class="form-control"
                                       value="<?= old('interval_sync', $display['interval_sync'] ?? 60) ?>" min="10" max="3600">
                                <small class="form-text text-muted">Interval sinkronisasi data dari server.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sholat_jumat">Mode Sholat Jumat</label>
                                <select name="sholat_jumat" id="sholat_jumat" class="form-control">
                                    <option value="1" <?= old('sholat_jumat', $display['sholat_jumat'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= old('sholat_jumat', $display['sholat_jumat'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: Pengaturan Jadwal Sholat -->
        <div class="col-lg-5">
            <!-- Metode Perhitungan -->
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock mr-2"></i>Pengaturan Jadwal Sholat</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="metode_hitung">Metode Perhitungan</label>
                        <select name="metode_hitung" id="metode_hitung" class="form-control">
                            <?php
                            $metodeList = [
                                'Kemenag' => 'Kemenag RI (Rekomendasi Indonesia)',
                                'MWL'     => 'Muslim World League',
                                'ISNA'    => 'Islamic Society of North America',
                                'Egypt'   => 'Egyptian General Authority of Survey',
                                'Makkah'  => 'Umm al-Qura University, Makkah',
                                'Karachi' => 'University of Islamic Sciences, Karachi',
                                'Tehran'  => 'Institute of Geophysics, University of Tehran',
                                'Jafari'  => 'Shia Ithna Ashari (Ja\'fari)',
                            ];
                            foreach ($metodeList as $key => $label):
                            ?>
                                <option value="<?= $key ?>"
                                    <?= old('metode_hitung', $display['metode_hitung'] ?? 'Kemenag') === $key ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Secara default, koordinat diambil otomatis dari profil masjid jika koordinat opsional di bawah ini kosong.</small>
                    </div>

                    <!-- Koordinat Manual -->
                    <?php
                    $opsiData = !empty($display['opsi_waktu_sholat']) ? json_decode($display['opsi_waktu_sholat'], true) : [];
                    $manualLat = $opsiData['koordinat']['latitude'] ?? '';
                    $manualLon = $opsiData['koordinat']['longitude'] ?? '';
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="latitude" class="font-weight-bold">Latitude (Opsional)</label>
                                <input type="number" step="any" name="latitude" id="latitude" class="form-control"
                                       value="<?= old('latitude', $manualLat) ?>" placeholder="Contoh: -6.200000">
                                <small class="form-text text-muted">Isi untuk mengganti latitude default.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="longitude" class="font-weight-bold">Longitude (Opsional)</label>
                                <input type="number" step="any" name="longitude" id="longitude" class="form-control"
                                       value="<?= old('longitude', $manualLon) ?>" placeholder="Contoh: 106.816666">
                                <small class="form-text text-muted">Isi untuk mengganti longitude default.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Koreksi Waktu Sholat -->
                    <div style="border-left: 4px solid #007bff; background: #f0f7ff; border-radius: 0 8px 8px 0; padding: 12px 16px; margin-top:14px; margin-bottom:10px;">
                        <h6 class="font-weight-bold mb-2" style="color: #0056b3;">
                            <i class="fas fa-sliders-h mr-1"></i> Koreksi Waktu Sholat (menit)
                        </h6>
                        <p class="text-muted small mb-2">Penyesuaian waktu sholat. Contoh: +2 untuk ihtiyati Kemenag, +7 untuk Maghrib Bintan.</p>

                        <?php
                        // Parse koreksi waktu dari JSON
                        $koreksiData = !empty($display['koreksi_waktu']) ? json_decode($display['koreksi_waktu'], true) : [];
                        ?>
                        <div class="row">
                            <?php
                            $koreksiFields = [
                                'koreksi_subuh'   => ['Subuh', 'subuh'],
                                'koreksi_dzuhur'  => ['Dzuhur', 'dzuhur'],
                                'koreksi_ashar'   => ['Ashar', 'ashar'],
                                'koreksi_maghrib' => ['Maghrib', 'maghrib'],
                                'koreksi_isya'    => ['Isya', 'isya'],
                            ];
                            foreach ($koreksiFields as $field => $info):
                            ?>
                            <div class="col-6 col-md-4">
                                <div class="form-group mb-2">
                                    <label for="<?= $field ?>" class="small font-weight-bold" style="color:#0056b3;"><?= $info[0] ?></label>
                                    <input type="number" name="<?= $field ?>" id="<?= $field ?>" class="form-control form-control-sm"
                                           value="<?= old($field, $koreksiData[$info[1]] ?? 0) ?>" min="-60" max="60">
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php
                    // Parse timer iqomah dari JSON
                    $iqomahData = !empty($display['timer_iqomah']) ? json_decode($display['timer_iqomah'], true) : [];
                    ?>

                    <div style="border-left: 4px solid #fd7e14; background: #fff8f3; border-radius: 0 8px 8px 0; padding: 12px 16px; margin-bottom:10px;">
                        <h6 class="font-weight-bold mb-2" style="color: #fd7e14;">
                            <i class="fas fa-stopwatch mr-1"></i> Durasi Iqomah (menit)
                        </h6>
                        <p class="text-muted small mb-2">Lama waktu tampil layar iqomah sebelum sholat dimulai.</p>
                        <div class="row">
                            <?php
                            $iqomahFields = [
                                'durasi_iqomah_subuh'   => ['Subuh', 'subuh', 10],
                                'durasi_iqomah_dzuhur'  => ['Dzuhur', 'dzuhur', 10],
                                'durasi_iqomah_ashar'   => ['Ashar', 'ashar', 10],
                                'durasi_iqomah_maghrib' => ['Maghrib', 'maghrib', 5],
                                'durasi_iqomah_isya'    => ['Isya', 'isya', 10],
                            ];
                            foreach ($iqomahFields as $field => $info):
                            ?>
                            <div class="col-6 col-md-4">
                                <div class="form-group mb-2">
                                    <label for="<?= $field ?>" class="small font-weight-bold" style="color:#b85c00;"><?= $info[0] ?></label>
                                    <input type="number" name="<?= $field ?>" id="<?= $field ?>" class="form-control form-control-sm"
                                           value="<?= old($field, $iqomahData[$info[1]] ?? $info[2]) ?>" min="1" max="60">
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div style="border-left: 4px solid #28a745; background: #f3fbf5; border-radius: 0 8px 8px 0; padding: 12px 16px; margin-bottom:4px;">
                        <h6 class="font-weight-bold mb-2" style="color: #1e7e34;">
                            <i class="fas fa-calendar-alt mr-1"></i> Koreksi Tanggal Hijriah
                        </h6>
                        <p class="text-muted small mb-2">
                            Sesuaikan jika penetapan 1 Ramadhan / Idul Fitri berbeda dari perhitungan otomatis.<br>
                            <small><i class="fas fa-info-circle text-info mr-1"></i>Tanggal Hijriah di TV juga akan otomatis berganti setelah waktu Maghrib (sesuai kaidah Islami).</small>
                        </p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="koreksi_hijriah" class="small font-weight-bold" style="color:#1e7e34;">Koreksi Hari</label>
                                    <select name="koreksi_hijriah" id="koreksi_hijriah" class="form-control form-control-sm">
                                        <?php
                                        $valHijri = old('koreksi_hijriah', $koreksiData['hijriah'] ?? 0);
                                        for ($h = -2; $h <= 2; $h++):
                                            $labelH = $h == 0 ? '0 — Default (Sesuai Masehi)' : ($h > 0 ? "+$h Hari" : "$h Hari");
                                        ?>
                                            <option value="<?= $h ?>" <?= $valHijri == $h ? 'selected' : '' ?>><?= $labelH ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <small class="text-muted pb-1"><i class="fas fa-mosque mr-1 text-success"></i>Berguna saat ada keputusan sidang isbat Kemenag / NU / Muhammadiyah berbeda.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- BARIS BARU: Mode Event Sholat -->
    <!-- ============================================================ -->
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-mosque mr-2"></i>Mode Display Event Sholat</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Setiap mode akan overlay tampilan utama secara otomatis sesuai waktu sholat.
                        Upload gambar untuk setiap mode sebagai background overlay.
                        Alur: <strong>Menjelang Adzan → Adzan → Qobliyah → Iqomah → Sholat → Ba'diyah → Normal</strong>
                    </p>

                    <?php
                    // Parse JSON columns
                    $modeEventData = !empty($display['mode_sholat_event']) ? json_decode($display['mode_sholat_event'], true) : [];
                    $modeTarawihData = !empty($display['mode_tarawih_json']) ? json_decode($display['mode_tarawih_json'], true) : [];
                    $modeHariRayaData = !empty($display['mode_hari_raya']) ? json_decode($display['mode_hari_raya'], true) : [];
                    $opsiWaktu = !empty($display['opsi_waktu_sholat']) ? json_decode($display['opsi_waktu_sholat'], true) : [];
                    $opsiQobliyah = $opsiWaktu['qobliyah'] ?? ['subuh'=>1, 'dzuhur'=>1, 'ashar'=>0, 'maghrib'=>1, 'isya'=>1];
                    $opsiBadiyah  = $opsiWaktu['badiyah']  ?? ['subuh'=>0, 'dzuhur'=>1, 'ashar'=>0, 'maghrib'=>1, 'isya'=>1];

                    // Definisi 9 mode event sholat (Iqomah & Sholat sekarang terpisah)
                    $modeEvents = [
                        [
                            'key'       => 'menjelang_adzan',
                            'label'     => '1. Menjelang Adzan',
                            'icon'      => 'fas fa-bell',
                            'color'     => 'info',
                            'desc'      => 'Countdown menjelang waktu adzan',
                            'durLabel'  => 'Menit sebelum adzan',
                            'durDefault'=> 10,
                            'durMin'    => 1,
                            'durMax'    => 30,
                            'jsonSrc'   => 'mode_sholat_event',
                        ],
                        [
                            'key'       => 'adzan',
                            'label'     => '2. Saat Adzan',
                            'icon'      => 'fas fa-volume-up',
                            'color'     => 'success',
                            'desc'      => 'Tampilan saat adzan berkumandang',
                            'durLabel'  => 'Durasi adzan (menit)',
                            'durDefault'=> 7,
                            'durMin'    => 1,
                            'durMax'    => 15,
                            'jsonSrc'   => 'mode_sholat_event',
                        ],
                        [
                            'key'       => 'qobliyah',
                            'label'     => '3. Sholat Qobliyah',
                            'icon'      => 'fas fa-praying-hands',
                            'color'     => 'primary',
                            'desc'      => 'Waktu sholat sunnah sebelum fardhu',
                            'durLabel'  => 'Durasi qobliyah (menit)',
                            'durDefault'=> 5,
                            'durMin'    => 1,
                            'durMax'    => 15,
                            'jsonSrc'   => 'mode_sholat_event',
                        ],
                        [
                            'key'       => 'iqomah',
                            'label'     => '4. Timer Masuk Iqomah',
                            'icon'      => 'fas fa-hourglass-half',
                            'color'     => 'warning',
                            'desc'      => 'Timer countdown iqomah (durasi per waktu diatur di kotak Pengaturan Jadwal)',
                            'noDurasi'  => true,
                            'jsonSrc'   => 'mode_sholat_event',
                        ],
                        [
                            'key'       => 'sholat',
                            'label'     => '5. Sholat Berlangsung',
                            'icon'      => 'fas fa-kaaba',
                            'color'     => 'danger',
                            'desc'      => 'Informasi sholat fardhu sedang berlangsung (dengan jam berjalan)',
                            'durLabel'  => 'Estimasi durasi sholat (menit)',
                            'durDefault'=> 15,
                            'durMin'    => 1,
                            'durMax'    => 45,
                            'jsonSrc'   => 'mode_sholat_event',
                        ],
                        [
                            'key'       => 'badiyah',
                            'label'     => '6. Sholat Ba\'diyah',
                            'icon'      => 'fas fa-hands',
                            'color'     => 'secondary',
                            'desc'      => 'Waktu sholat sunnah setelah fardhu',
                            'durLabel'  => 'Durasi ba\'diyah (menit)',
                            'durDefault'=> 5,
                            'durMin'    => 1,
                            'durMax'    => 15,
                            'jsonSrc'   => 'mode_sholat_event',
                        ],
                        [
                            'key'       => 'tarawih',
                            'label'     => '7. Sholat Tarawih (Ramadhan)',
                            'icon'      => 'fas fa-moon',
                            'color'     => 'primary',
                            'desc'      => 'Otomatis tampil setelah ba\'diyah isya (bila aktif)',
                            'durLabel'  => 'Durasi tarawih (menit)',
                            'durDefault'=> 60,
                            'durMin'    => 1,
                            'durMax'    => 120,
                            'jsonSrc'   => 'mode_tarawih_json',
                        ],
                        [
                            'key'       => 'idul_adha',
                            'label'     => '8. Sholat Idul Adha',
                            'icon'      => 'fas fa-star-and-crescent',
                            'color'     => 'success',
                            'desc'      => 'Otomatis tampil 1 hari pada tanggal di bawah',
                            'durLabel'  => 'Durasi (menit) sejak subuh',
                            'durDefault'=> 120,
                            'durMin'    => 1,
                            'durMax'    => 240,
                            'hasDate'   => true,
                            'dateField' => 'tanggal_idul_adha',
                            'jsonSrc'   => 'mode_hari_raya',
                            'jsonSubKey'=> 'idul_adha',
                        ],
                        [
                            'key'       => 'idul_fitri',
                            'label'     => '9. Sholat Idul Fitri',
                            'icon'      => 'fas fa-star',
                            'color'     => 'warning',
                            'desc'      => 'Otomatis tampil 1 hari pada tanggal di bawah',
                            'durLabel'  => 'Durasi (menit) sejak subuh',
                            'durDefault'=> 120,
                            'durMin'    => 1,
                            'durMax'    => 240,
                            'hasDate'   => true,
                            'dateField' => 'tanggal_idul_fitri',
                            'jsonSrc'   => 'mode_hari_raya',
                            'jsonSubKey'=> 'idul_fitri',
                        ],
                    ];
                    ?>

                    <div class="row">
                    <?php foreach ($modeEvents as $idx => $mode):
                        $modeField  = 'mode_' . $mode['key'];
                        $durField   = 'durasi_' . $mode['key'];
                        $imgField   = 'gambar_' . $mode['key'];

                        // Resolve value dari JSON column yang sesuai
                        $jsonSrc = $mode['jsonSrc'] ?? 'mode_sholat_event';
                        $jsonSubKey = $mode['jsonSubKey'] ?? null;
                        if ($jsonSrc === 'mode_sholat_event') {
                            $srcData = $modeEventData[$mode['key']] ?? [];
                        } elseif ($jsonSrc === 'mode_tarawih_json') {
                            $srcData = $modeTarawihData;
                        } elseif ($jsonSrc === 'mode_hari_raya' && $jsonSubKey) {
                            $srcData = $modeHariRayaData[$jsonSubKey] ?? [];
                        } else {
                            $srcData = [];
                        }

                        $isActive = old($modeField, $srcData['aktif'] ?? 0);
                        $durValue = old($durField, $srcData['durasi'] ?? ($mode['durDefault'] ?? 0));
                        $imgValue = $srcData['gambar'] ?? null;
                    ?>
                        <div class="col-lg-6 col-xl-4">
                            <div class="card card-outline card-<?= $mode['color'] ?> mb-3" style="border-width: 2px;">
                                <div class="card-header py-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>
                                            <i class="<?= $mode['icon'] ?> mr-1"></i>
                                            <strong><?= $mode['label'] ?></strong>
                                        </span>
                                        <div class="custom-control custom-switch">
                                            <input type="hidden" name="<?= $modeField ?>" value="0">
                                            <input type="checkbox" name="<?= $modeField ?>" value="1"
                                                   class="custom-control-input" id="<?= $modeField ?>"
                                                   <?= $isActive ? 'checked' : '' ?>>
                                            <label class="custom-control-label" for="<?= $modeField ?>"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body py-2">
                                    <small class="text-muted d-block mb-2"><?= $mode['desc'] ?></small>

                                    <?php if (empty($mode['noDurasi'])): ?>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mb-2">
                                                <label class="small"><?= $mode['durLabel'] ?? 'Durasi (menit)' ?></label>
                                                <input type="number" name="<?= $durField ?>" class="form-control form-control-sm"
                                                       value="<?= $durValue ?>"
                                                       min="<?= $mode['durMin'] ?? 1 ?>" max="<?= $mode['durMax'] ?? 120 ?>">
                                            </div>
                                        </div>
                                        <?php if (!empty($mode['hasDate'])): ?>
                                        <div class="col-6">
                                            <div class="form-group mb-2">
                                                <label class="small">Tanggal</label>
                                                <input type="date" name="<?= $mode['dateField'] ?>" class="form-control form-control-sm"
                                                       value="<?= old($mode['dateField'], $srcData['tanggal'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Upload gambar overlay -->
                                    <div class="form-group mb-1">
                                        <label class="small">Gambar Overlay</label>
                                        <div class="mb-1" id="preview-wrap-<?= $imgField ?>">
                                            <?php if ($isEdit && !empty($imgValue)): ?>
                                                <img src="<?= base_url($imgValue) ?>" alt="Overlay" class="img-thumbnail img-preview" style="max-height:50px;">
                                            <?php endif; ?>
                                        </div>
                                        <div class="custom-file custom-file-sm">
                                            <input type="file" name="<?= $imgField ?>" class="custom-file-input mode-image-input"
                                                   data-preview="preview-wrap-<?= $imgField ?>" accept="image/*">
                                            <label class="custom-file-label small" style="font-size:0.75rem;">Pilih gambar...</label>
                                        </div>
                                    </div>

                                    <?php if ($mode['key'] == 'qobliyah' || $mode['key'] == 'badiyah'): 
                                        $opsiData = ($mode['key'] == 'qobliyah') ? $opsiQobliyah : $opsiBadiyah;
                                    ?>
                                    <!-- Opsi Waktu Sholat -->
                                    <div class="form-group mb-0 mt-3 border-top pt-2">
                                        <label class="small text-muted d-block mb-1">Tampilkan Pada:</label>
                                        <div class="d-flex flex-wrap" style="gap: 10px;">
                                            <?php foreach(['subuh','dzuhur','ashar','maghrib','isya'] as $wkt): ?>
                                            <div class="custom-control custom-checkbox custom-control-inline m-0">
                                                <input type="hidden" name="opsi_<?= $mode['key'] ?>[<?= $wkt ?>]" value="0">
                                                <input type="checkbox" name="opsi_<?= $mode['key'] ?>[<?= $wkt ?>]" value="1" 
                                                       class="custom-control-input" id="opsi_<?= $mode['key'] ?>_<?= $wkt ?>"
                                                       <?= ($opsiData[$wkt] ?? 0) == 1 ? 'checked' : '' ?>>
                                                <label class="custom-control-label small" for="opsi_<?= $mode['key'] ?>_<?= $wkt ?>">
                                                    <?= ucfirst($wkt) ?>
                                                </label>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Aksi & Notifikasi Auto-Save -->
    <div class="card">
        <div class="card-body d-flex justify-content-between align-items-center">
            <a href="<?= base_url('admin/display-masjid') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
            </a>
            <div class="text-right">
                <!-- Notifikasi menggunakan SweetAlert popup topside -->
                <?php if (!$isEdit): ?>
                    <button type="submit" class="btn btn-primary" id="btn-save-new">
                        <i class="fas fa-save mr-1"></i> Simpan Display
                    </button>
                <?php else: ?>
                    <span class="text-success small mr-2 d-none d-md-inline-block">
                        <i class="fas fa-check-circle"></i> Auto-save aktif
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>

<!-- Cropper.js CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>

<!-- ====================================================================== -->
<!-- Modal Crop Gambar Overlay Display -->
<!-- ====================================================================== -->
<div class="modal fade" id="modalDisplayCrop" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 95%; margin: 10px auto;">
        <div class="modal-content" style="height: calc(100vh - 20px); display: flex; flex-direction: column;">
            <div class="modal-header" style="flex-shrink: 0;">
                <h5 class="modal-title">
                    <i class="fas fa-crop-alt mr-1"></i>
                    Crop Gambar Overlay — <span id="cropModalLabel"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="alert alert-info m-0" style="flex-shrink: 0; border-radius: 0; padding: 10px 15px; margin-bottom: 0 !important;">
                <small>
                    <i class="fas fa-info-circle"></i> <strong>Panduan:</strong>
                    Geser (drag) untuk memindahkan area crop • Resize untuk mengubah ukuran •
                    Gunakan tombol Putar Kiri/Kanan untuk memutar gambar •
                    Aspect ratio: <strong id="cropAspectLabel">16:9</strong> (otomatis sesuai orientasi display) •
                    Klik <strong>Selesai & Simpan</strong> untuk crop dan upload
                </small>
            </div>
            <div class="modal-body" style="flex: 1; overflow: hidden; padding: 15px; display: flex; align-items: center; justify-content: center;">
                <div id="displayCropContainer" style="width: 100%; height: 100%; max-height: calc(100vh - 220px); overflow: hidden; display: flex; align-items: center; justify-content: center; position: relative;">
                    <img id="displayImageToCrop" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
            </div>
            <div class="modal-footer" style="flex-shrink: 0;">
                <div class="mr-auto">
                    <button type="button" class="btn btn-info btn-sm" id="displayBtnRotateLeft" title="Putar 90° ke kiri">
                        <i class="fas fa-undo"></i> Putar Kiri
                    </button>
                    <button type="button" class="btn btn-info btn-sm" id="displayBtnRotateRight" title="Putar 90° ke kanan">
                        <i class="fas fa-redo"></i> Putar Kanan
                    </button>
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="displayBtnCropDone">
                    <i class="fas fa-check"></i> Selesai & Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Crop modal styles */
    #modalDisplayCrop .modal-body { min-height: 0; }
    #displayCropContainer { position: relative; }
    #displayImageToCrop { max-width: 100%; max-height: 100%; object-fit: contain; }
    @media (max-height: 600px) {
        #modalDisplayCrop .modal-content { height: calc(100vh - 10px); }
        #displayCropContainer { max-height: calc(100vh - 170px); }
    }
</style>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Inisialisasi Select2 untuk dropdown masjid
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: '-- Pilih Masjid/Mushola --',
        allowClear: true
    });

    // Update label custom file input
    $('input[type="file"]').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').text(fileName || 'Pilih file...');
        
        // Langsung auto-save file kalau diganti
        if ($(this).val()) {
            triggerAutoSave(true);
        }
    });

    // AUTO-SAVE FUNCTIONALITY
    let saveTimeout;
    let isDirty = false;
    let isSaving = false; // Mencegah double-save

    // Saat VALUE berubah → tandai dirty & trigger save
    $('#form-display').on('change', 'input, select, textarea', function() {
        isDirty = true;
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(function() {
            triggerAutoSave();
        }, 800);
    });

    function triggerAutoSave() {
        <?php if(!$isEdit): ?> return; <?php endif; ?>
        if (!isDirty || isSaving) return;

        isDirty = false;
        isSaving = true;

        // Tampilkan loading popup
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Sedang menyimpan perubahan...',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        var formData = new FormData($('#form-display')[0]);

        $.ajax({
            url: "<?= base_url('admin/display-masjid/update/' . ($display['id'] ?? '')) ?>",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(data) {
                isSaving = false;

                // Parse response
                var resp = data;
                if (typeof resp === 'string') {
                    try { resp = JSON.parse(resp); } catch(e) { resp = null; }
                }

                // Tampilkan notifikasi sukses (SweetAlert2 Toast)
                Swal.fire({
                    icon: 'success',
                    title: 'Tersimpan!',
                    text: 'Perubahan berhasil disimpan otomatis.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });

                // Hapus badge "Belum tersimpan" dari preview gambar
                $('.img-preview').siblings('.badge-info').fadeOut(300, function() { $(this).remove(); });

                // Update CSRF token jika tersedia
                if (resp && resp.csrf_token) {
                    $('input[name="<?= csrf_token() ?>"]').val(resp.csrf_token);
                }
            },
            error: function(xhr, status, err) {
                isSaving = false;
                console.error('[AutoSave] Error:', status, err);

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: 'Terjadi kesalahan saat auto-save: ' + (err || status),
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            }
        });
    }

    // Mencegah form disubmit secara tradisional (ENTER key) hanya pada mode edit
    $('#form-display').on('submit', function(e) {
        <?php if ($isEdit): ?>
            e.preventDefault();
            isDirty = true;
            triggerAutoSave(true);
        <?php else: ?>
            // Izinkan form disubmit untuk data baru
            $('#btn-save-new').html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...').prop('disabled', true);
        <?php endif; ?>
    });

    // ============================================================
    // CROP IMAGE WORKFLOW
    // ============================================================
    var displayCropper = null;
    var currentCropField = null; // nama field yang sedang di-crop (e.g. 'gambar_iqomah')
    var currentCropPreview = null; // ID preview container

    /**
     * Saat user memilih file gambar overlay → buka crop modal
     */
    $(document).on('change', '.mode-image-input', function() {
        var input = this;
        currentCropField = $(input).attr('name');
        currentCropPreview = $(input).data('preview');

        if (!input.files || !input.files[0]) return;

        var file = input.files[0];

        // Validasi tipe file
        if (!file.type.match(/^image\/(jpeg|jpg|png|webp|svg\+xml|gif)$/)) {
            Swal.fire({ icon: 'warning', title: 'Format Tidak Didukung', text: 'Gunakan format JPG, PNG, WebP, SVG atau GIF.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            input.value = '';
            return;
        }

        // Update label
        $(input).next('.custom-file-label').text(file.name);

        // Baca file dan buka crop modal
        var reader = new FileReader();
        reader.onload = function(e) {
            openCropModal(e.target.result);
        };
        reader.readAsDataURL(file);
    });

    /**
     * Buka modal crop dengan aspek ratio sesuai orientasi display
     */
    function openCropModal(imageSrc) {
        var orientasi = $('#orientasi').val() || 'horizontal';
        var aspectRatio = (orientasi === 'vertikal') ? (9 / 16) : (16 / 9);
        var aspectLabel = (orientasi === 'vertikal') ? '9:16 (Vertikal)' : '16:9 (Horizontal)';

        // Update label di modal
        var modeLabel = currentCropField ? currentCropField.replace('gambar_', '').replace(/_/g, ' ') : '';
        $('#cropModalLabel').text(modeLabel.charAt(0).toUpperCase() + modeLabel.slice(1));
        $('#cropAspectLabel').text(aspectLabel);

        // Set image source
        var imgEl = document.getElementById('displayImageToCrop');
        imgEl.src = imageSrc;

        // Destroy old cropper
        if (displayCropper) {
            displayCropper.destroy();
            displayCropper = null;
        }

        // Show modal
        $('#modalDisplayCrop').modal('show');

        // Inisialisasi Cropper setelah modal shown
        $('#modalDisplayCrop').off('shown.bs.modal.cropper').on('shown.bs.modal.cropper', function() {
            if (displayCropper) {
                displayCropper.destroy();
            }
            displayCropper = new Cropper(imgEl, {
                aspectRatio: aspectRatio,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.9,
                restore: false,
                guides: true,
                center: true,
                highlight: true,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        });
    }

    // Rotate buttons
    $('#displayBtnRotateLeft').on('click', function() {
        if (displayCropper) displayCropper.rotate(-90);
    });
    $('#displayBtnRotateRight').on('click', function() {
        if (displayCropper) displayCropper.rotate(90);
    });

    /**
     * Selesai crop → ambil canvas → resize → base64 → simpan + preview
     */
    $('#displayBtnCropDone').on('click', function() {
        if (!displayCropper) return;

        var orientasi = $('#orientasi').val() || 'horizontal';
        // Resolusi output: Full HD sesuai orientasi
        var outputWidth, outputHeight;
        if (orientasi === 'vertikal') {
            outputWidth = 1080;
            outputHeight = 1920;
        } else {
            outputWidth = 1920;
            outputHeight = 1080;
        }

        var croppedCanvas = displayCropper.getCroppedCanvas({
            width: outputWidth,
            height: outputHeight,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        if (!croppedCanvas) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat memproses gambar.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            return;
        }

        // Convert ke base64 JPEG kualitas tinggi (0.92) untuk ukuran kecil tapi tajam
        var base64Data = croppedCanvas.toDataURL('image/jpeg', 0.92);

        // Simpan base64 ke hidden input (buat jika belum ada)
        var hiddenId = 'cropped_' + currentCropField;
        var $hidden = $('#' + hiddenId);
        if ($hidden.length === 0) {
            $hidden = $('<input type="hidden" id="' + hiddenId + '" name="' + hiddenId + '">');
            $('#form-display').append($hidden);
        }
        $hidden.val(base64Data);

        // Kosongkan file input (base64 yang akan dikirim, bukan file)
        $('input[name="' + currentCropField + '"]').val('');

        // Update preview thumbnail
        if (currentCropPreview) {
            $('#' + currentCropPreview).html(
                '<img src="' + base64Data + '" alt="Preview" class="img-thumbnail img-preview" style="max-height:50px;">' +
                '<span class="badge badge-info ml-1" style="font-size:0.65rem;">Belum tersimpan</span>'
            );
        }

        // Close modal
        $('#modalDisplayCrop').modal('hide');

        // Trigger auto-save
        isDirty = true;
        triggerAutoSave();
    });

    // Cleanup saat modal ditutup
    $('#modalDisplayCrop').on('hidden.bs.modal', function() {
        if (displayCropper) {
            displayCropper.destroy();
            displayCropper = null;
        }
    });

    // Handle preview untuk logo & wallpaper (tanpa crop)
    $(document).on('change', 'input[name="logo"], input[name="wallpaper"]', function() {
        var input = this;
        if (input.files && input.files[0]) {
            $(input).next('.custom-file-label').text(input.files[0].name);
            isDirty = true;
            triggerAutoSave();
        }
    });
});
</script>
<?= $this->endSection(); ?>
