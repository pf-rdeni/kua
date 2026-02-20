<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<?php
$pageTitle = 'Data Mubaligh';
$breadcrumb = [
    ['title' => 'Home', 'url' => 'admin/dashboard'],
    ['title' => 'Mubaligh', 'url' => ''],
];
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-tie mr-2"></i>Daftar Mubaligh
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/mubaligh/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Search Form -->
                <form action="<?= base_url('admin/mubaligh') ?>" method="get" class="mb-3">
                    <div class="input-group input-group-sm" style="max-width: 350px;">
                        <input type="text" name="keyword" class="form-control" placeholder="Cari nama, NIK, alamat..." value="<?= esc($keyword ?? '') ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if (!empty($keyword)): ?>
                            <a href="<?= base_url('admin/mubaligh') ?>" class="btn btn-default" title="Reset">
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
                                <th>Kelurahan/Desa</th>
                                <th>No. HP</th>
                                <th>Pekerjaan</th>
                                <th>Status</th>
                                <th style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($mubalighList)): ?>
                                <?php $no = ($currentPage - 1) * 10 + 1; ?>
                                <?php foreach ($mubalighList as $m): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($m['nama_lengkap']) ?></td>
                                    <td><?= esc($m['nik'] ?? '-') ?></td>
                                    <td><?= esc($m['kelurahan_desa'] ?? '-') ?></td>
                                    <td><?= esc($m['no_hp'] ?? '-') ?></td>
                                    <td><?= esc($m['pekerjaan'] ?? '-') ?></td>
                                    <td>
                                        <?php if ($m['status_aktif'] == 1): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Tidak Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/mubaligh/show/' . $m['id_mubaligh']) ?>" class="btn btn-info btn-xs" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/mubaligh/edit/' . $m['id_mubaligh']) ?>" class="btn btn-warning btn-xs" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/mubaligh/delete/' . $m['id_mubaligh']) ?>" class="btn btn-danger btn-xs btn-delete" data-name="<?= esc($m['nama_lengkap']) ?>" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Belum ada data mubaligh.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (isset($pager)): ?>
                <div class="mt-3">
                    <?= $pager->links('mubaligh', 'default_full') ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
