<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<?php
$pageTitle = 'Data ' . $entitasConfig['nama_label'];
$breadcrumb = [
    ['title' => 'Home', 'url' => 'admin/dashboard'],
    ['title' => $entitasConfig['nama_label'], 'url' => ''],
];
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="<?= esc($entitasConfig['icon'] ?? 'fas fa-users') ?> mr-2"></i>Daftar <?= esc($entitasConfig['nama_label']) ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/personil/' . $entitasType . '/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Search Form -->
                <form action="<?= base_url('admin/personil/' . $entitasType) ?>" method="get" class="mb-3">
                    <div class="input-group input-group-sm" style="max-width: 350px;">
                        <input type="text" name="keyword" class="form-control" placeholder="Cari nama, NIK, alamat..." value="<?= esc($keyword ?? '') ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if (!empty($keyword)): ?>
                            <a href="<?= base_url('admin/personil/' . $entitasType) ?>" class="btn btn-default" title="Reset">
                                <i class="fas fa-times"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Nama Lengkap</th>
                                <th>NIK</th>
                                <?php if ($entitasConfig['has_masjid_link']): ?>
                                <th>Masjid/Mushola</th>
                                <?php endif; ?>
                                <th>Kelurahan/Desa</th>
                                <th>No. HP</th>
                                <th>Status</th>
                                <th style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($personilList)): ?>
                                <?php $no = ($currentPage - 1) * 10 + 1; ?>
                                <?php foreach ($personilList as $p): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($p['nama_lengkap']) ?></td>
                                    <td><?= esc($p['nik'] ?? '-') ?></td>
                                    <?php if ($entitasConfig['has_masjid_link']): ?>
                                    <td><?= esc($p['nama_masjid'] ?? '-') ?></td>
                                    <?php endif; ?>
                                    <td><?= esc($p['kelurahan_desa'] ?? '-') ?></td>
                                    <td><?= esc($p['no_hp'] ?? '-') ?></td>
                                    <td>
                                        <?php if ($p['status_aktif'] == 1): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Tidak Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/personil/' . $entitasType . '/show/' . $p['id']) ?>" class="btn btn-info btn-xs" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/personil/' . $entitasType . '/edit/' . $p['id']) ?>" class="btn btn-warning btn-xs" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/personil/' . $entitasType . '/delete/' . $p['id']) ?>" class="btn btn-danger btn-xs btn-delete" data-name="<?= esc($p['nama_lengkap']) ?>" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?= $entitasConfig['has_masjid_link'] ? 8 : 7 ?>" class="text-center text-muted">Belum ada data <?= esc($entitasConfig['nama_label']) ?>.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (isset($pager)): ?>
                <div class="mt-3">
                    <?= $pager->links('personil', 'default_full') ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
