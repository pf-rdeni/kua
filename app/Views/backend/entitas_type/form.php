<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<?php
$pageTitle = isset($entitas) ? 'Edit Entitas' : 'Tambah Entitas';
$breadcrumb = [
    ['title' => 'Home', 'url' => 'admin/dashboard'],
    ['title' => 'Manajemen Entitas', 'url' => 'admin/entitas-type'],
    ['title' => $pageTitle, 'url' => ''],
];
?>

<div class="row row-justify-content-center">
    <div class="col-md-8 mx-auto">
        <div class="card card-<?= isset($entitas) ? 'warning' : 'primary' ?> card-outline">
            <div class="card-header">
                <h3 class="card-title"><?= $pageTitle ?></h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/entitas-type') ?>" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <?php
            $actionUrl = isset($entitas) ? base_url('admin/entitas-type/update/' . $entitas['id']) : base_url('admin/entitas-type/store');
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
                        <label class="col-sm-4 col-form-label">Kode Entitas <span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="kode" value="<?= old('kode', $entitas['kode'] ?? '') ?>" placeholder="Misal: mubaligh, imam_masjid" required <?= isset($entitas) ? 'readonly title="Kode tidak bolah diubah"' : '' ?>>
                            <small class="text-muted">Kode unik, huruf kecil, tanpa spasi (gunakan underscore). Contoh: <code>penyuluh_agama</code></small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Nama Label <span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="nama_label" value="<?= old('nama_label', $entitas['nama_label'] ?? '') ?>" placeholder="Misal: Mubaligh, Imam Masjid" required>
                            <small class="text-muted">Teks yang akan tampil di Sidebar Menu dan Judul Halaman.</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Icon (FontAwesome)</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="<?= old('icon', $entitas['icon'] ?? 'fas fa-users') ?>"></i></span>
                                </div>
                                <input type="text" class="form-control" name="icon" value="<?= old('icon', $entitas['icon'] ?? 'fas fa-users') ?>" placeholder="Misal: fas fa-user-tie">
                            </div>
                            <small class="text-muted">Class FontAwesome, misal: <code>fas fa-chalkboard-teacher</code></small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Grup Operator <span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <select class="form-control select2bs4" name="operator_group" required style="width: 100%;">
                                <option value="">-- Pilih Grup Akses --</option>
                                <?php foreach ($authGroups as $group) : ?>
                                    <!-- Abaikan SuperAdmin dan Admin krn punya full access otomatis -->
                                    <?php if ($group['name'] !== 'SuperAdmin' && $group['name'] !== 'Admin') : ?>
                                        <option value="<?= $group['name'] ?>" <?= old('operator_group', $entitas['operator_group'] ?? '') == $group['name'] ? 'selected' : '' ?>>
                                            <?= esc($group['name']) ?> - <?= esc($group['description']) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Pilih grup user (di luar Admin) yang berhak mengelola data entitas ini.</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Konfigurasi Kolom Ekstra</label>
                        <div class="col-sm-8">
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input" id="has_masjid_link" name="has_masjid_link" value="1" <?= old('has_masjid_link', $entitas['has_masjid_link'] ?? 0) ? 'checked' : '' ?>>
                                <label class="custom-control-label fw-normal" for="has_masjid_link">Terhubung dengan Masjid/Mushola?</label>
                                <br><small class="text-muted ms-4">Jika dicentang, form input akan memiliki kolom "Pilih Masjid/Mushola".</small>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="has_sk" name="has_sk" value="1" <?= old('has_sk', $entitas['has_sk'] ?? 0) ? 'checked' : '' ?>>
                                <label class="custom-control-label fw-normal" for="has_sk">Memerlukan SK Pengangkatan?</label>
                                <br><small class="text-muted ms-4">Jika dicentang, form input akan memiliki kolom "SK Pengangkatan".</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Urutan Tampil (Sidebar)</label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" name="urutan" value="<?= old('urutan', $entitas['urutan'] ?? 0) ?>" min="0">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Status Aktif</label>
                        <div class="col-sm-8">
                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" <?= old('is_active', $entitas['is_active'] ?? 1) ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="is_active">Aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Keterangan / Deskripsi</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" name="deskripsi" rows="3"><?= old('deskripsi', $entitas['deskripsi'] ?? '') ?></textarea>
                        </div>
                    </div>

                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
                    <a href="<?= base_url('admin/entitas-type') ?>" class="btn btn-default">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('js'); ?>
<script>
    $(function () {
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        });
    });
</script>
<?= $this->endSection(); ?>
