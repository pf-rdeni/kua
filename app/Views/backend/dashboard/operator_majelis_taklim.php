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
                <i class="fas fa-users fa-4x text-warning mb-3"></i>
                <h4>Akun belum terhubung ke Majelis Taklim</h4>
                <p class="text-muted">Akun Anda belum dikaitkan dengan Majelis Taklim manapun. Mohon hubungi Administrator KUA untuk mengatur entitas Anda.</p>
            </div>
        </div>
    </div>
</div>
<?php else: ?>

<!-- ===== HEADER PROFIL MAJELIS TAKLIM ===== -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-body p-0">
                <div class="d-flex align-items-center p-3">
                    <?php if (!empty($majelis['foto'])): ?>
                        <img src="<?= base_url('uploads/majelis_taklim/' . esc($majelis['foto'])) ?>"
                             alt="Foto" class="img-thumbnail mr-3" style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
                    <?php else: ?>
                        <div class="mr-3 d-flex align-items-center justify-content-center rounded bg-primary text-white"
                             style="width:80px;height:80px;font-size:2rem;">
                            <i class="fas fa-users"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h4 class="mb-0 font-weight-bold"><?= esc($majelis['nama_majelis_taklim']) ?></h4>
                        <span class="badge badge-info mr-1">Majelis Taklim</span>
                        <small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i><?= esc($majelis['alamat'] ?? 'Alamat belum diisi') ?></small>
                        <div class="mt-1">
                            <small class="text-muted"><i class="fas fa-user mr-1"></i>Pimpinan: <strong><?= esc($majelis['pimpinan'] ?? '-') ?></strong></small>
                            <?php if (!empty($majelis['no_hp'])): ?>
                                <small class="text-muted ml-2"><i class="fas fa-phone mr-1"></i><?= esc($majelis['no_hp']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="ml-auto text-right d-none d-md-block">
                        <a href="<?= base_url('admin/majelis-taklim/edit/' . $majelis['id_majelis_taklim']) ?>"
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
    <!-- Total Saldo Kas -->
    <div class="col-12 col-md-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rp <?= number_format($saldoKas, 0, ',', '.') ?></h3>
                <p>Total Saldo Kas</p>
            </div>
            <div class="icon"><i class="fas fa-wallet"></i></div>
            <a href="<?= base_url('admin/keuangan/kas/majelis_taklim') ?>" class="small-box-footer">
                Lihat Kas <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Agenda Terdekat (Count) -->
    <div class="col-12 col-md-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= count($agendaTerdekat) ?></h3>
                <p>Agenda dalam 30 hari ke depan</p>
            </div>
            <div class="icon"><i class="fas fa-calendar-alt"></i></div>
            <a href="<?= base_url('admin/agenda-masjid?entitas_type=majelis_taklim') ?>" class="small-box-footer">
                Jadwal Kegiatan <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- ===== KONTEN UTAMA: AGENDA MENDATANG ===== -->
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-info">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title text-bold"><i class="fas fa-calendar-alt mr-1"></i> Agenda Kegiatan Terdekat (30 Hari)</h3>
            </div>
            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 text-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>Nama Kegiatan</th>
                                <th>Deskripsi Singkat</th>
                                <th>Penceramah / Mubaligh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($agendaTerdekat)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                    Belum ada agenda kegiatan dalam 30 hari ke depan
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($agendaTerdekat as $ag): ?>
                                <tr>
                                    <td class="align-middle">
                                        <strong><?= formatTanggalId($ag['tanggal']) ?></strong><br>
                                        <small class="text-muted">
                                            <i class="far fa-clock mr-1"></i>
                                            <?= date('H:i', strtotime($ag['waktu_mulai'])) ?> 
                                            <?= !empty($ag['waktu_selesai']) ? ' - ' . date('H:i', strtotime($ag['waktu_selesai'])) : '' ?>
                                        </small>
                                    </td>
                                    <td class="align-middle">
                                        <span class="d-block font-weight-bold"><?= esc($ag['judul_kegiatan']) ?></span>
                                        <span class="badge badge-info"><?= esc($ag['jenis']) ?></span>
                                    </td>
                                    <td class="align-middle text-muted">
                                        <?= esc(strlen($ag['deskripsi'] ?? '') > 50 ? substr($ag['deskripsi'], 0, 50) . '...' : ($ag['deskripsi'] ?? '-')) ?>
                                    </td>
                                    <td class="align-middle">
                                        <?php if (!empty($ag['nama_mubaligh_db'])): ?>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2" style="width:28px;height:28px;font-size:10px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <span class="font-weight-bold text-dark"><?= esc($ag['nama_mubaligh_db']) ?></span>
                                            </div>
                                        <?php elseif (!empty($ag['nama_penceramah'])): ?>
                                            <span class="text-dark"><i class="fas fa-user-circle mr-1 text-muted"></i><?= esc($ag['nama_penceramah']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted font-italic">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center bg-white border-top">
                <a href="<?= base_url('admin/agenda-masjid?entitas_type=majelis_taklim') ?>" class="text-info font-weight-bold">
                    Lihat Semua Agenda <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?= $this->endSection(); ?>
