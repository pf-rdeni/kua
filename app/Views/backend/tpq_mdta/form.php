<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><?= isset($tpq) ? 'Edit' : 'Tambah' ?> Data TPQ / MDTA</h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->has('errors')) : ?>
                <div class="alert alert-danger">
                    <ul><?php foreach (session('errors') as $error) : ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
                </div>
            <?php endif ?>

            <div class="card card-primary">
                <form action="<?= isset($tpq) ? base_url('admin/tpq-mdta/update/' . $tpq['id_tpq_mdta']) : base_url('admin/tpq-mdta/store') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nama TPQ/MDTA *</label>
                            <input type="text" class="form-control" name="nama" value="<?= old('nama', $tpq['nama'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Masjid/Mushola (Opsional)</label>
                            <select class="form-control select2" name="id_masjid_mushola">
                                <option value="">-- Pilih Masjid/Mushola --</option>
                                <?php foreach ($masjidList as $masjid) : ?>
                                    <option value="<?= $masjid['id_masjid_mushola'] ?>" <?= old('id_masjid_mushola', $tpq['id_masjid_mushola'] ?? '') == $masjid['id_masjid_mushola'] ? 'selected' : '' ?>>
                                        <?= esc($masjid['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Biarkan kosong jika tidak berafiliasi dengan Masjid di database.</small>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea class="form-control" name="alamat"><?= old('alamat', $tpq['alamat'] ?? '') ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hari</label>
                                    <input type="text" class="form-control" name="hari" value="<?= old('hari', $tpq['hari'] ?? '') ?>" placeholder="Contoh: Senin">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Waktu</label>
                                    <input type="time" class="form-control" name="waktu" value="<?= old('waktu', $tpq['waktu'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nama Pimpinan</label>
                            <input type="text" class="form-control" name="pimpinan" value="<?= old('pimpinan', $tpq['pimpinan'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>No. HP Pimpinan</label>
                            <input type="text" class="form-control" name="no_hp_pimpinan" value="<?= old('no_hp_pimpinan', $tpq['no_hp_pimpinan'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Jumlah Santri</label>
                            <input type="number" class="form-control" name="jumlah_santri" value="<?= old('jumlah_santri', $tpq['jumlah_santri'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Foto Kegiatan/Struktur</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="foto" accept="image/*">
                                <label class="custom-file-label">Pilih file</label>
                            </div>
                            <small class="text-muted">Max: 2MB. Kosongkan jika tidak ubah.</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="<?= base_url('admin/tpq-mdta') ?>" class="btn btn-default float-right">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection(); ?>
