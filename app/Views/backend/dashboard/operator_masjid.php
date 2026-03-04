<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<?php
if (!function_exists('formatTanggalId')) {
    function formatTanggalId($tgl) {
        if (!$tgl) return '-';
        $namaHari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        $bulanId  = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $ts = strtotime($tgl);
        return $namaHari[date('w', $ts)] . ', ' . date('d', $ts) . ' ' . $bulanId[(int)date('m', $ts)] . ' ' . date('Y', $ts);
    }
}
?>

<?php if (!empty($belumSetup)): ?>
<!-- ===== PESAN BELUM SETUP ===== -->
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-outline card-warning text-center py-5">
            <div class="card-body">
                <i class="fas fa-mosque fa-4x text-warning mb-3"></i>
                <h4>Akun belum terhubung ke Masjid</h4>
                <p class="text-muted">Akun Anda belum dikaitkan dengan masjid atau mushola manapun. Mohon hubungi Administrator KUA untuk mengatur entitas Anda.</p>
            </div>
        </div>
    </div>
</div>
<?php else: ?>

<!-- ===== HEADER PROFIL MASJID ===== -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-body p-0">
                <div class="d-flex align-items-center p-3">
                    <?php if (!empty($masjid['foto'])): ?>
                        <img src="<?= base_url('uploads/masjid_mushola/' . esc($masjid['foto'])) ?>"
                             alt="Foto" class="img-thumbnail mr-3" style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
                    <?php else: ?>
                        <div class="mr-3 d-flex align-items-center justify-content-center rounded bg-primary text-white"
                             style="width:80px;height:80px;font-size:2rem;">
                            <i class="fas fa-mosque"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h4 class="mb-0 font-weight-bold"><?= esc($masjid['nama']) ?></h4>
                        <span class="badge badge-<?= $masjid['jenis'] === 'Masjid' ? 'success' : 'info' ?> mr-1"><?= esc($masjid['jenis']) ?></span>
                        <small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i><?= esc($masjid['alamat']) ?></small>
                        <div class="mt-1">
                            <small class="text-muted"><i class="fas fa-user mr-1"></i>Ketua DKM: <strong><?= esc($masjid['nama_ketua_dkm'] ?? '-') ?></strong></small>
                            <?php if (!empty($masjid['no_hp_ketua'])): ?>
                                <small class="text-muted ml-2"><i class="fas fa-phone mr-1"></i><?= esc($masjid['no_hp_ketua']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="ml-auto text-right d-none d-md-block">
                        <a href="<?= base_url('admin/masjid-mushola/edit/' . $masjid['id_masjid_mushola']) ?>"
                           class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-edit mr-1"></i> Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== STATISTIK RINGKAS (Card Counter) ===== -->
<div class="row">
    <!-- Total Display -->
    <div class="col-6 col-md-3">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3><?= count($displays) ?></h3>
                <p>Display TV</p>
            </div>
            <div class="icon"><i class="fas fa-tv"></i></div>
            <a href="<?= base_url('admin/display-masjid') ?>" class="small-box-footer">
                Lihat <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Display Aktif -->
    <div class="col-6 col-md-3">
        <?php $displayAktif = count(array_filter($displays, fn($d) => $d['aktif'] == 1)); ?>
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $displayAktif ?></h3>
                <p>Display Aktif</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <a href="<?= base_url('admin/display-masjid') ?>" class="small-box-footer">
                Kelola <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Konten Display -->
    <div class="col-6 col-md-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $jmlKonten ?></h3>
                <p>Konten Display</p>
            </div>
            <div class="icon"><i class="fas fa-photo-video"></i></div>
            <?php if (!empty($displays)): ?>
                <a href="<?= base_url('admin/display-masjid/konten/' . $displays[0]['id']) ?>" class="small-box-footer">
                    Kelola <i class="fas fa-arrow-circle-right"></i>
                </a>
            <?php else: ?>
                <a href="<?= base_url('admin/display-masjid/create') ?>" class="small-box-footer">
                    Tambah Display <i class="fas fa-arrow-circle-right"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Saldo Kas Bulan Ini -->
    <div class="col-6 col-md-3">
        <div class="small-box <?= $saldoKas >= 0 ? 'bg-info' : 'bg-danger' ?>">
            <div class="inner">
                <h3 style="font-size:2.2rem;">Rp <?= number_format(abs($saldoKas), 0, ',', '.') ?></h3>
                <p>Saldo Kas Bulan Ini<?= $saldoKas < 0 ? ' (Minus)' : '' ?></p>
            </div>
            <div class="icon"><i class="fas fa-wallet"></i></div>
            <a href="<?= base_url('admin/keuangan/kas/masjid_mushola') ?>" class="small-box-footer">
                Lihat Kas <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- ===== ROWS: DISPLAY & JADWAL ===== -->
<div class="row">

    <!-- Kolom Kiri: Daftar Display TV -->
    <div class="col-lg-5">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tv mr-2"></i>Display TV Saya</h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/display-masjid/create') ?>" class="btn btn-xs btn-primary">
                        <i class="fas fa-plus"></i> Tambah
                    </a>
                </div>
            </div>
            <div class="card-body p-2">
                <?php if (empty($displays)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-tv fa-2x mb-2 d-block"></i>
                        Belum ada Display TV.<br>
                        <a href="<?= base_url('admin/display-masjid/create') ?>">Tambah sekarang →</a>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($displays as $disp): ?>
                        <div class="list-group-item px-2 py-2">
                            <div class="d-flex align-items-center">
                                <div class="mr-2">
                                    <?php if ($disp['aktif']): ?>
                                        <span class="badge badge-success"><i class="fas fa-circle mr-1" style="font-size:.5rem;"></i>Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <strong><?= esc($disp['nama_display']) ?></strong>
                                    <div>
                                        <small class="text-muted">Template: <code><?= esc($disp['template_aktif']) ?></code></small>
                                        <small class="text-muted ml-2"><?= esc(ucfirst($disp['orientasi'])) ?></small>
                                    </div>
                                </div>
                                <div class="ml-2">
                                    <a href="<?= base_url('admin/display-masjid/edit/' . $disp['id']) ?>"
                                       class="btn btn-xs btn-warning" title="Setting">
                                        <i class="fas fa-cog"></i>
                                    </a>
                                    <a href="<?= base_url('admin/display-masjid/konten/' . $disp['id']) ?>"
                                       class="btn btn-xs btn-info" title="Kelola Konten">
                                        <i class="fas fa-photo-video"></i>
                                    </a>
                                    <a href="<?= base_url('display/' . $disp['id']) ?>" target="_blank"
                                       class="btn btn-xs btn-success" title="Buka Display">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Shortcut Menu -->
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-th mr-2"></i>Menu Cepat</h3>
            </div>
            <div class="card-body p-2">
                <div class="row text-center">
                    <div class="col-4 mb-3">
                        <a href="<?= base_url('admin/masjid-mushola') ?>" class="text-decoration-none">
                            <div class="p-2 rounded" style="background:#fff3e0;">
                                <i class="fas fa-mosque fa-2x text-warning"></i>
                                <div class="small mt-1 text-dark">Profil <?= esc($masjid['jenis']) ?></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-4 mb-3">
                        <a href="<?= base_url('admin/display-masjid') ?>" class="text-decoration-none">
                            <div class="p-2 rounded" style="background:#e3f2fd;">
                                <i class="fas fa-tv fa-2x text-primary"></i>
                                <div class="small mt-1 text-dark">Display</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-4 mb-3">
                        <a href="<?= base_url('admin/keuangan/transaksi/masjid_mushola') ?>" class="text-decoration-none">
                            <div class="p-2 rounded" style="background:#e8f5e9;">
                                <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                                <div class="small mt-1 text-dark">Transaksi</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-4 mb-3">
                        <a href="<?= base_url('admin/keuangan/kas/masjid_mushola') ?>" class="text-decoration-none">
                            <div class="p-2 rounded" style="background:#fce4ec;">
                                <i class="fas fa-wallet fa-2x text-danger"></i>
                                <div class="small mt-1 text-dark">Kas</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-4 mb-3">
                        <a href="<?= base_url('admin/akun/ganti-password') ?>" class="text-decoration-none">
                            <div class="p-2 rounded" style="background:#f3e5f5;">
                                <i class="fas fa-key fa-2x text-purple" style="color:#7b1fa2;"></i>
                                <div class="small mt-1 text-dark">Ganti Password</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-4 mb-3">
                        <a href="<?= base_url('admin/keuangan/iuran/masjid_mushola') ?>" class="text-decoration-none">
                            <div class="p-2 rounded" style="background:#e0f7fa;">
                                <i class="fas fa-hand-holding-heart fa-2x text-info"></i>
                                <div class="small mt-1 text-dark">Iuran</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Jadwal -->
    <div class="col-lg-7">

        <!-- ===== REMINDER: Agenda Mandiri Mendatang ===== -->
        <?php
        $jenisWarna = [
            'ceramah'      => 'primary',
            'ta_lim'       => 'success',
            'sosial'       => 'warning',
            'buka_bersama' => 'danger',
            'tadarus'      => 'info',
            'sahur'        => 'secondary',
            'lainnya'      => 'dark',
        ];
        $jenisNama = [
            'ceramah'      => 'Ceramah',
            'ta_lim'       => "Ta'lim",
            'sosial'       => 'Sosial',
            'buka_bersama' => 'Buka Bersama',
            'tadarus'      => 'Tadarus',
            'sahur'        => 'Sahur',
            'lainnya'      => 'Lainnya',
        ];
        ?>
        <div class="card card-outline card-<?= !empty($agendaMendatang) ? 'success' : 'secondary' ?>">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bell mr-2"></i>Agenda Mendatang (7 Hari)
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/agenda-masjid') ?>" class="btn btn-xs btn-outline-success">
                        <i class="fas fa-calendar-alt mr-1"></i>Semua Agenda
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($agendaMendatang)): ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-calendar-check fa-lg mb-1 d-block"></i>
                        <small>Tidak ada agenda dalam 7 hari ke depan.</small><br>
                        <a href="<?= base_url('admin/agenda-masjid/create') ?>" class="btn btn-xs btn-success mt-1">
                            <i class="fas fa-plus mr-1"></i>Buat Agenda
                        </a>
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($agendaMendatang as $ag):
                            $isHariIni = $ag['tanggal'] === date('Y-m-d');
                            $isBesok   = $ag['tanggal'] === date('Y-m-d', strtotime('+1 day'));
                            $warna     = $jenisWarna[$ag['jenis']] ?? 'dark';
                            $namaJenis = $jenisNama[$ag['jenis']] ?? 'Lainnya';
                            $namaPencer = $ag['nama_mubaligh_db'] ?: $ag['nama_penceramah'];
                        ?>
                        <li class="list-group-item py-2 px-3 <?= $isHariIni ? 'bg-light border-left border-success border-3' : '' ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="font-weight-bold small">
                                        <span class="badge badge-<?= $warna ?> mr-1"><?= $namaJenis ?></span>
                                        <?= esc($ag['judul_kegiatan']) ?>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar mr-1"></i><?= formatTanggalId($ag['tanggal']) ?>
                                        <?php if ($ag['waktu_mulai']): ?>
                                            &nbsp;<i class="fas fa-clock mr-1"></i><?= date('H:i', strtotime($ag['waktu_mulai'])) ?>
                                        <?php endif; ?>
                                    </small>
                                    <?php if ($namaPencer): ?>
                                        <div class="small text-muted"><i class="fas fa-user mr-1"></i><?= esc($namaPencer) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="ml-2 flex-shrink-0">
                                    <?php if ($isHariIni): ?>
                                        <span class="badge badge-success">Hari Ini</span>
                                    <?php elseif ($isBesok): ?>
                                        <span class="badge badge-info">Besok</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Jadwal Maghrib Mengaji -->
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-moon mr-2"></i>Jadwal Maghrib Mengaji (Hari Ini &amp; Besok)</h3>
            </div>
            <div class="card-body p-0">
                <?php if (empty($jadwalMaghrib)): ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-calendar-times fa-lg mb-1 d-block"></i>
                        <small>Tidak ada jadwal Maghrib Mengaji untuk hari ini &amp; besok di <?= esc($masjid['nama']) ?>.</small>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Petugas</th>
                                <th>Peran</th>
                                <th>No. HP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jadwalMaghrib as $jm): ?>
                            <tr>
                                <td>
                                    <?php $isToday = $jm['tanggal'] === date('Y-m-d'); ?>
                                    <?php if ($isToday): ?>
                                        <span class="badge badge-success">Hari Ini</span>
                                    <?php else: ?>
                                        <span class="badge badge-info">Besok</span>
                                    <?php endif; ?>
                                    <small><?= formatTanggalId($jm['tanggal']) ?></small>
                                </td>
                                <td>
                                    <?php if (!empty($jm['foto']) && file_exists(FCPATH . 'uploads/personil/' . $jm['foto'])): ?>
                                        <img src="<?= base_url('uploads/personil/' . esc($jm['foto'])) ?>"
                                             alt="Foto" class="img-circle mr-1" style="width:28px;height:28px;object-fit:cover;">
                                    <?php endif; ?>
                                    <?= esc($jm['nama_mubaligh']) ?>
                                </td>
                                <td><small><?= esc($jm['peran_petugas']) ?></small></td>
                                <td><small><?= esc($jm['no_hp'] ?? '-') ?></small></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Jadwal Khotib Jumat -->
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-star-and-crescent mr-2"></i>Jadwal Khotib Jumat (3 Minggu ke Depan)</h3>
            </div>
            <div class="card-body p-0">
                <?php if (empty($jadwalJumat)): ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-calendar-times fa-lg mb-1 d-block"></i>
                        <small>Tidak ada jadwal Khotib Jumat dalam 3 minggu ke depan di <?= esc($masjid['nama']) ?>.</small>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Tanggal Jumat</th>
                                <th>Khotib / Imam</th>
                                <th>No. HP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jadwalJumat as $jj): ?>
                            <tr>
                                <td>
                                    <?php if ($jj['tanggal'] === date('Y-m-d', strtotime('this friday'))): ?>
                                        <span class="badge badge-warning text-dark">Jumat Ini</span>
                                    <?php endif; ?>
                                    <?= formatTanggalId($jj['tanggal']) ?>
                                </td>
                                <td>
                                    <?php if (!empty($jj['foto']) && file_exists(FCPATH . 'uploads/personil/' . $jj['foto'])): ?>
                                        <img src="<?= base_url('uploads/personil/' . esc($jj['foto'])) ?>"
                                             alt="Foto" class="img-circle mr-1" style="width:28px;height:28px;object-fit:cover;">
                                    <?php endif; ?>
                                    <?= esc($jj['nama_mubaligh']) ?>
                                    <small class="text-muted">(<?= esc($jj['peran_petugas']) ?>)</small>
                                </td>
                                <td><small><?= esc($jj['no_hp'] ?? '-') ?></small></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /kolom kanan -->
</div>

<?php endif; ?>

<?= $this->endSection(); ?>
