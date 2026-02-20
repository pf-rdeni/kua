<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<?php
$pageTitle = 'Detail Mubaligh';
$breadcrumb = [
    ['title' => 'Home', 'url' => 'admin/dashboard'],
    ['title' => 'Mubaligh', 'url' => 'admin/mubaligh'],
    ['title' => 'Detail', 'url' => ''],
];
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-tie mr-2"></i>Detail Mubaligh
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/mubaligh/edit/' . $mubaligh['id_mubaligh']) ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <!-- Foto -->
                    <div class="col-md-4 text-center mb-3">
                        <?php if (!empty($mubaligh['foto'])): ?>
                            <img src="<?= base_url('uploads/mubaligh/' . $mubaligh['foto']) ?>" alt="Foto" class="img-fluid img-thumbnail" style="max-height: 250px;">
                        <?php else: ?>
                            <div class="d-flex justify-content-center align-items-center bg-light border rounded" style="height: 200px;">
                                <i class="fas fa-user fa-5x text-secondary"></i>
                            </div>
                        <?php endif; ?>
                        <div class="mt-2">
                            <?php if ($mubaligh['status_aktif'] == 1): ?>
                                <span class="badge badge-success badge-lg">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-secondary badge-lg">Tidak Aktif</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Data -->
                    <div class="col-md-8">
                        <table class="table table-sm table-borderless">
                            <tr><th style="width: 180px;">Nama Lengkap</th><td>: <?= esc($mubaligh['nama_lengkap']) ?></td></tr>
                            <tr><th>NIK</th><td>: <?= esc($mubaligh['nik'] ?? '-') ?></td></tr>
                            <tr><th>Tempat, Tgl Lahir</th><td>: <?= esc($mubaligh['tempat_lahir'] ?? '-') ?>, <?= $mubaligh['tanggal_lahir'] ? date('d-m-Y', strtotime($mubaligh['tanggal_lahir'])) : '-' ?></td></tr>
                            <tr><th>Jenis Kelamin</th><td>: <?= $mubaligh['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td></tr>
                            <tr><th>Alamat</th><td>: <?= esc($mubaligh['alamat'] ?? '-') ?></td></tr>
                            <tr><th>Kelurahan/Desa</th><td>: <?= esc($mubaligh['kelurahan_desa'] ?? '-') ?></td></tr>
                            <tr><th>No. HP</th><td>: <?= esc($mubaligh['no_hp'] ?? '-') ?></td></tr>
                            <tr><th>Pendidikan Terakhir</th><td>: <?= esc($mubaligh['pendidikan_terakhir'] ?? '-') ?></td></tr>
                            <tr><th>Pekerjaan</th><td>: <?= esc($mubaligh['pekerjaan'] ?? '-') ?></td></tr>
                            <?php if (!empty($mubaligh['latitude']) && !empty($mubaligh['longitude'])): ?>
                            <tr><th>Koordinat</th><td>: <?= esc($mubaligh['latitude']) ?>, <?= esc($mubaligh['longitude']) ?></td></tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <a href="<?= base_url('admin/mubaligh') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
