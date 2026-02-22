<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<?php
$pageTitle = isset($group) ? 'Edit Grup Akun' : 'Tambah Grup Akun';
$breadcrumb = [
    ['title' => 'Home', 'url' => 'admin/dashboard'],
    ['title' => 'Manajemen Grup', 'url' => 'admin/groups'],
    ['title' => $pageTitle, 'url' => ''],
];
?>

<div class="row justify-content-center">
    <div class="col-md-8 mx-auto">
        <div class="card card-<?= isset($group) ? 'warning' : 'primary' ?> card-outline">
            <div class="card-header">
                <h3 class="card-title"><?= $pageTitle ?></h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/groups') ?>" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <?php
            $actionUrl = isset($group) ? base_url('admin/groups/update/' . $group['id']) : base_url('admin/groups/store');
            ?>
            <form action="<?= $actionUrl ?>" method="post">
                <?= csrf_field() ?>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul>
                                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Nama Grup Akses <span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="name" value="<?= old('name', $group['name'] ?? '') ?>" placeholder="Misal: Operator Penyuluh" required>
                            <small class="text-muted">Gunakan karakter huruf/angka saja. Spasi akan dihilangkan otomatis oleh sistem (menjadi: <code>OperatorPenyuluh</code>).</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Deskripsi <span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="description" value="<?= old('description', $group['description'] ?? '') ?>" placeholder="Penjelasan mengenai peran/grup ini" required>
                            <small class="text-muted">Misal: <em>Grup untuk pegawai yang mengelola data penyuluh agama.</em></small>
                        </div>
                    </div>

                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Akses</button>
                    <a href="<?= base_url('admin/groups') ?>" class="btn btn-default">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
