<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<?php
$pageTitle = 'Detail ' . $entitasConfig['nama_label'];
// Generate dinamis "NI" + huruf depan setiap kata
$words = explode(' ', $entitasConfig['nama_label'] ?? '');
$inisial = '';
foreach ($words as $w) {
    if(!empty($w)) $inisial .= strtoupper(substr($w, 0, 1));
}
$labelNIA = "NI" . ($inisial ?: 'A');
$breadcrumb = [
    ['title' => 'Home', 'url' => 'admin/dashboard'],
    ['title' => $entitasConfig['nama_label'], 'url' => 'admin/personil/' . $entitasType],
    ['title' => 'Detail', 'url' => ''],
];
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="<?= esc($entitasConfig['icon'] ?? 'fas fa-user') ?> mr-2"></i>Detail <?= esc($entitasConfig['nama_label']) ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/personil/' . $entitasType . '/edit/' . $personil['id']) ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <!-- Foto -->
                    <div class="col-md-4 text-center mb-3">
                        <?php 
                        if (!empty($personil['foto']) && file_exists(FCPATH . 'uploads/personil/' . $personil['foto'])): ?>
                            <img src="<?= base_url('uploads/personil/' . esc($personil['foto'])) ?>" alt="Foto" class="img-fluid img-thumbnail" style="max-height: 250px;">
                        <?php else: 
                            $words = explode(' ', trim($personil['nama_lengkap']));
                            $initials = count($words) > 1 ? strtoupper(substr($words[0], 0, 1) . substr($words[count($words)-1], 0, 1)) : strtoupper(substr($words[0], 0, 1));
                            $colors = ['#f56954', '#f39c12', '#00a65a', '#00c0ef', '#3c8dbc', '#605ca8', '#ff851b', '#39cccc'];
                            $bgColor = $colors[crc32($personil['nama_lengkap']) % count($colors)];
                            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200"><rect width="200" height="200" fill="'.$bgColor.'"/><text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" fill="#ffffff" font-family="Arial, sans-serif" font-size="80" font-weight="bold">'.$initials.'</text></svg>';
                            $fotoUrl = 'data:image/svg+xml;base64,' . base64_encode($svg);
                        ?>
                            <img src="<?= $fotoUrl ?>" alt="Foto" class="img-fluid img-thumbnail" style="width: 200px; height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="mt-2">
                            <?php if ($personil['status_aktif'] == 1): ?>
                                <span class="badge badge-success badge-lg">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-secondary badge-lg">Tidak Aktif</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Data -->
                    <div class="col-md-8">
                        <table class="table table-sm table-borderless">
                            <tr><th style="width: 180px;">Nama Lengkap</th><td>: <?= esc($personil['nama_lengkap']) ?></td></tr>
                            <tr><th>NIK</th><td>: <?= esc($personil['nik'] ?? '-') ?></td></tr>
                            <tr><th><?= $labelNIA ?></th><td>: <?= esc($personil['nia'] ?? '-') ?></td></tr>
                            <tr><th>Tempat, Tgl Lahir</th><td>: <?= esc($personil['tempat_lahir'] ?? '-') ?>, <?= $personil['tanggal_lahir'] ? date('d-m-Y', strtotime($personil['tanggal_lahir'])) : '-' ?></td></tr>
                            <tr><th>Jenis Kelamin</th><td>: <?= $personil['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td></tr>
                            <tr><th>Alamat</th><td>: <?= esc($personil['alamat'] ?? '-') ?></td></tr>
                            <tr><th>Kelurahan/Desa</th><td>: <?= esc($personil['kelurahan_desa'] ?? '-') ?></td></tr>
                            <tr><th>No. HP</th><td>: <?= esc($personil['no_hp'] ?? '-') ?></td></tr>
                            <tr><th>Pendidikan Terakhir</th><td>: <?= esc($personil['pendidikan_terakhir'] ?? '-') ?></td></tr>
                            <tr><th>Pekerjaan</th><td>: <?= esc($personil['pekerjaan'] ?? '-') ?></td></tr>
                            <?php if ($entitasConfig['has_masjid_link']): ?>
                            <tr><th>Masjid/Mushola</th><td>: <?= esc($personil['nama_masjid'] ?? '-') ?></td></tr>
                            <?php endif; ?>
                            <?php if ($entitasConfig['has_sk']): ?>
                            <tr><th>Status Jabatan</th><td>: <?= esc($personil['status'] ?? '-') ?></td></tr>
                            <tr><th>SK Pengangkatan</th><td>: 
                                <?php if (!empty($personil['sk_pengangkatan'])): ?>
                                    <a href="<?= base_url('uploads/personil/' . $personil['sk_pengangkatan']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-download mr-1"></i>Lihat SK
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td></tr>
                            <?php endif; ?>
                            <?php if (!empty($personil['latitude']) && !empty($personil['longitude'])): ?>
                            <tr><th>Koordinat</th><td>: <?= esc($personil['latitude']) ?>, <?= esc($personil['longitude']) ?></td></tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <a href="<?= base_url('admin/personil/' . $entitasType) ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
