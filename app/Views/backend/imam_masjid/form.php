<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <!-- Header title omitted for brevity -->
            <h1><?= isset($imam) ? 'Edit' : 'Tambah' ?> Data Imam Masjid</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Errors display block omitted for brevity, assume standard -->
            <?php if (session()->has('errors')) : ?>
                <div class="alert alert-danger">
                    <ul><?php foreach (session('errors') as $error) : ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
                </div>
            <?php endif ?>

            <div class="card card-primary">
                <form action="<?= isset($imam) ? base_url('admin/imam-masjid/update/' . $imam['id_imam_masjid']) : base_url('admin/imam-masjid/store') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Masjid/Mushola *</label>
                            <select class="form-control select2" name="id_masjid_mushola" required>
                                <option value="">-- Pilih Masjid/Mushola --</option>
                                <?php foreach ($masjidList as $masjid) : ?>
                                    <option value="<?= $masjid['id_masjid_mushola'] ?>" <?= old('id_masjid_mushola', $imam['id_masjid_mushola'] ?? '') == $masjid['id_masjid_mushola'] ? 'selected' : '' ?>>
                                        <?= esc($masjid['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nama Imam *</label>
                            <input type="text" class="form-control" name="nama" value="<?= old('nama', $imam['nama'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Status *</label>
                            <select class="form-control" name="status" required>
                                <option value="">-- Pilih Status --</option>
                                <?php $statuses = ['Imam Tetap', 'Imam Rawatib', 'Imam Badal', 'Lainnya']; ?>
                                <?php foreach ($statuses as $status) : ?>
                                    <option value="<?= $status ?>" <?= old('status', $imam['status'] ?? '') == $status ? 'selected' : '' ?>><?= $status ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea class="form-control" name="alamat"><?= old('alamat', $imam['alamat'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>No. HP</label>
                            <input type="text" class="form-control" name="no_hp" value="<?= old('no_hp', $imam['no_hp'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Foto</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="foto" accept="image/*">
                                <label class="custom-file-label">Pilih file</label>
                            </div>
                            <small class="text-muted">Max: 2MB. Kosongkan jika tidak ubah.</small>
                        </div>
                        <div class="form-group">
                            <label>SK Pengangkatan (PDF/Image)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="sk_pengangkatan" accept=".pdf,image/*">
                                <label class="custom-file-label">Pilih file</label>
                            </div>
                            <small class="text-muted">Max: 2MB. Kosongkan jika tidak ubah.</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="<?= base_url('admin/imam-masjid') ?>" class="btn btn-default float-right">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection(); ?>
