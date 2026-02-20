<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><?= isset($petugas) ? 'Edit' : 'Tambah' ?> Data Petugas Fardu Kifayah</h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->has('errors')) : ?>
                <div class="alert alert-danger">
                    <ul><?php foreach (session('errors') as $error) : ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
                </div>
            <?php endif ?>

            <div class="card card-primary">
                <form action="<?= isset($petugas) ? base_url('admin/fardu-kifayah/update/' . $petugas['id_fardu_kifayah']) : base_url('admin/fardu-kifayah/store') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Masjid/Mushola *</label>
                            <select class="form-control select2" name="id_masjid_mushola" required>
                                <option value="">-- Pilih Masjid/Mushola --</option>
                                <?php foreach ($masjidList as $masjid) : ?>
                                    <option value="<?= $masjid['id_masjid_mushola'] ?>" <?= old('id_masjid_mushola', $petugas['id_masjid_mushola'] ?? '') == $masjid['id_masjid_mushola'] ? 'selected' : '' ?>>
                                        <?= esc($masjid['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nama Petugas *</label>
                            <input type="text" class="form-control" name="nama" value="<?= old('nama', $petugas['nama'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Status *</label>
                            <select class="form-control" name="status" required>
                                <option value="">-- Pilih Status --</option>
                                <?php $statuses = ['Ketua', 'Anggota', 'Pemandu', 'Pengurus', 'Lainnya']; ?>
                                <?php foreach ($statuses as $status) : ?>
                                    <option value="<?= $status ?>" <?= old('status', $petugas['status'] ?? '') == $status ? 'selected' : '' ?>><?= $status ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea class="form-control" name="alamat"><?= old('alamat', $petugas['alamat'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>No. HP</label>
                            <input type="text" class="form-control" name="no_hp" value="<?= old('no_hp', $petugas['no_hp'] ?? '') ?>">
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
                        <a href="<?= base_url('admin/fardu-kifayah') ?>" class="btn btn-default float-right">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection(); ?>
