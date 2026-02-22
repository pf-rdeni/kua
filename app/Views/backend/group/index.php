<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<?php
$pageTitle = 'Manajemen Grup Akun';
$breadcrumb = [
    ['title' => 'Home', 'url' => 'admin/dashboard'],
    ['title' => 'Manajemen Grup', 'url' => ''],
];
?>

<div class="row">
    <div class="col-12">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <div class="card card-primary card-outline">
            <div class="card-header">
                <a href="<?= base_url('admin/groups/create') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Grup Baru
                </a>
            </div>
            <div class="card-body">
                <table id="table-groups" class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th width="25%">Nama Grup</th>
                            <th>Deskripsi / Keterangan</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($groups as $row) : ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td>
                                    <strong><?= esc($row['name']) ?></strong>
                                    <?php if (in_array($row['name'], ['SuperAdmin', 'Admin'])) : ?>
                                        <i class="fas fa-shield-alt text-success ms-1" title="System Group"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($row['description']) ?></td>
                                <td class="text-center">
                                    <?php if (!in_array($row['name'], ['SuperAdmin', 'Admin'])) : ?>
                                        <a href="<?= base_url('admin/groups/edit/' . $row['id']) ?>" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?= base_url('admin/groups/delete/' . $row['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Menghapus grup akan menghapus hak akses bagi user-user di dalamnya. Apakah Anda yakin?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Locked System</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('js'); ?>
<script>
    $(function() {
        $("#table-groups").DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });
</script>
<?= $this->endSection(); ?>
