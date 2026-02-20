<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= isset($masjid) ? 'Edit' : 'Tambah' ?> Data Masjid/Mushola</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/masjid-mushola') ?>">Masjid & Mushola</a></li>
                        <li class="breadcrumb-item active">Form</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Validation Errors -->
            <?php if (session()->has('errors')) : ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Error!</h5>
                    <ul>
                        <?php foreach (session('errors') as $error) : ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form Data</h3>
                </div>
                <form action="<?= isset($masjid) ? base_url('admin/masjid-mushola/update/' . $masjid['id_masjid_mushola']) : base_url('admin/masjid-mushola/store') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Masjid/Mushola *</label>
                                    <input type="text" class="form-control" name="nama" value="<?= old('nama', $masjid['nama'] ?? '') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Jenis *</label>
                                    <select class="form-control select2" name="jenis" required>
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="Masjid" <?= old('jenis', $masjid['jenis'] ?? '') == 'Masjid' ? 'selected' : '' ?>>Masjid</option>
                                        <option value="Mushola" <?= old('jenis', $masjid['jenis'] ?? '') == 'Mushola' ? 'selected' : '' ?>>Mushola</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea class="form-control" name="alamat" rows="3"><?= old('alamat', $masjid['alamat'] ?? '') ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Tahun Berdiri</label>
                                    <input type="number" class="form-control" name="tahun_berdiri" value="<?= old('tahun_berdiri', $masjid['tahun_berdiri'] ?? '') ?>" placeholder="YYYY">
                                </div>
                                <div class="form-group">
                                    <label>Luas Bangunan (m2)</label>
                                    <input type="number" class="form-control" name="luas_bangunan" value="<?= old('luas_bangunan', $masjid['luas_bangunan'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status Tanah</label>
                                    <input type="text" class="form-control" name="status_tanah" value="<?= old('status_tanah', $masjid['status_tanah'] ?? '') ?>" placeholder="Contoh: Wakaf, SHM">
                                </div>
                                <div class="form-group">
                                    <label>Nama Ketua DKM</label>
                                    <input type="text" class="form-control" name="nama_ketua_dkm" value="<?= old('nama_ketua_dkm', $masjid['nama_ketua_dkm'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>No. HP Ketua DKM</label>
                                    <input type="text" class="form-control" name="no_hp_ketua" value="<?= old('no_hp_ketua', $masjid['no_hp_ketua'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Jumlah Jamaah</label>
                                    <input type="number" class="form-control" name="jumlah_jamaah" value="<?= old('jumlah_jamaah', $masjid['jumlah_jamaah'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Foto</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="foto" name="foto" accept="image/*">
                                        <label class="custom-file-label" for="foto">Pilih file</label>
                                    </div>
                                    <small class="text-muted">Format: JPG/PNG, Max: 2MB</small>
                                </div>
                                <div class="form-group row">
                                    <div class="col-6">
                                        <label>Latitude</label>
                                        <input type="text" class="form-control" name="latitude" value="<?= old('latitude', $masjid['latitude'] ?? '') ?>">
                                    </div>
                                    <div class="col-6">
                                        <label>Longitude</label>
                                        <input type="text" class="form-control" name="longitude" value="<?= old('longitude', $masjid['longitude'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="<?= base_url('admin/masjid-mushola') ?>" class="btn btn-default float-right">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection(); ?>
