<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<?php
$pageTitle = 'Manajemen Entitas Personil';
$breadcrumb = [
    ['title' => 'Home', 'url' => 'admin/dashboard'],
    ['title' => 'Manajemen Entitas', 'url' => ''],
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
                <a href="<?= base_url('admin/entitas-type/create') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Entitas Baru
                </a>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Icon</th>
                            <th>Kode</th>
                            <th>Nama Label</th>
                            <th>Grup Operator</th>
                            <th>Masjid?</th>
                            <th>Punya SK?</th>
                            <th>Urutan</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($entitasTypes as $row) : ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center"><i class="<?= esc($row['icon']) ?>"></i></td>
                                <td><code><?= esc($row['kode']) ?></code></td>
                                <td><?= esc($row['nama_label']) ?></td>
                                <td><?= esc($row['operator_group']) ?></td>
                                <td class="text-center"><?= $row['has_masjid_link'] ? '<span class="badge badge-success">Ya</span>' : '<span class="badge badge-secondary">Tidak</span>' ?></td>
                                <td class="text-center"><?= $row['has_sk'] ? '<span class="badge badge-success">Ya</span>' : '<span class="badge badge-secondary">Tidak</span>' ?></td>
                                <td class="text-center"><?= esc($row['urutan']) ?></td>
                                <td class="text-center">
                                    <?php if ($row['is_active']) : ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else : ?>
                                        <span class="badge badge-danger">Non-aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('admin/entitas-type/edit/' . $row['id']) ?>" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?= base_url('admin/entitas-type/delete/' . $row['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data entitas ini? Data personil MUNGKIN menjadi tak bisa diakses. Harap berhati-hati.');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
        $("#example1").DataTable({
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
