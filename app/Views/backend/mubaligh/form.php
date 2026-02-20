<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<?php
$isEdit = isset($mubaligh);
$pageTitle = $isEdit ? 'Edit Mubaligh' : 'Tambah Mubaligh';
$breadcrumb = [
    ['title' => 'Home', 'url' => 'admin/dashboard'],
    ['title' => 'Mubaligh', 'url' => 'admin/mubaligh'],
    ['title' => $pageTitle, 'url' => ''],
];
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-<?= $isEdit ? 'edit' : 'plus' ?> mr-2"></i><?= $pageTitle ?>
                </h3>
            </div>

            <form action="<?= base_url($isEdit ? 'admin/mubaligh/update/' . $mubaligh['id_mubaligh'] : 'admin/mubaligh/store') ?>" method="post" enctype="multipart/form-data">
                <div class="card-body">

                    <!-- Validation Errors -->
                    <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <h6><i class="fas fa-exclamation-triangle mr-2"></i>Periksa kembali input Anda:</h6>
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                                <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Nama Lengkap -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                       value="<?= old('nama_lengkap', $mubaligh['nama_lengkap'] ?? '') ?>" required>
                            </div>
                        </div>

                        <!-- NIK -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nik">NIK</label>
                                <input type="text" class="form-control" id="nik" name="nik" maxlength="16"
                                       value="<?= old('nik', $mubaligh['nik'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Tempat Lahir -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tempat_lahir">Tempat Lahir</label>
                                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir"
                                       value="<?= old('tempat_lahir', $mubaligh['tempat_lahir'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir"
                                       value="<?= old('tanggal_lahir', $mubaligh['tanggal_lahir'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Jenis Kelamin -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="L" <?= old('jenis_kelamin', $mubaligh['jenis_kelamin'] ?? '') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= old('jenis_kelamin', $mubaligh['jenis_kelamin'] ?? '') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <!-- No. HP -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_hp">No. HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp"
                                       value="<?= old('no_hp', $mubaligh['no_hp'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="2"><?= old('alamat', $mubaligh['alamat'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <!-- Kelurahan/Desa -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kelurahan_desa">Kelurahan/Desa</label>
                                <input type="text" class="form-control" id="kelurahan_desa" name="kelurahan_desa"
                                       value="<?= old('kelurahan_desa', $mubaligh['kelurahan_desa'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Pendidikan Terakhir -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pendidikan_terakhir">Pendidikan Terakhir</label>
                                <select class="form-control" id="pendidikan_terakhir" name="pendidikan_terakhir">
                                    <option value="">-- Pilih --</option>
                                    <?php 
                                    $pendidikanList = ['SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3'];
                                    foreach ($pendidikanList as $p): ?>
                                        <option value="<?= $p ?>" <?= old('pendidikan_terakhir', $mubaligh['pendidikan_terakhir'] ?? '') == $p ? 'selected' : '' ?>><?= $p ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Pekerjaan -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pekerjaan">Pekerjaan</label>
                                <input type="text" class="form-control" id="pekerjaan" name="pekerjaan"
                                       value="<?= old('pekerjaan', $mubaligh['pekerjaan'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Status Aktif -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status_aktif">Status</label>
                                <select class="form-control" id="status_aktif" name="status_aktif">
                                    <option value="1" <?= old('status_aktif', $mubaligh['status_aktif'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= old('status_aktif', $mubaligh['status_aktif'] ?? 1) == 0 ? 'selected' : '' ?>>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Latitude -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="latitude">Latitude</label>
                                <input type="text" class="form-control" id="latitude" name="latitude" placeholder="cth: 1.0456"
                                       value="<?= old('latitude', $mubaligh['latitude'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Longitude -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="longitude">Longitude</label>
                                <input type="text" class="form-control" id="longitude" name="longitude" placeholder="cth: 104.0123"
                                       value="<?= old('longitude', $mubaligh['longitude'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Foto -->
                    <div class="form-group">
                        <label for="foto">Foto</label>
                        <?php if ($isEdit && !empty($mubaligh['foto'])): ?>
                            <div class="mb-2">
                                <img src="<?= base_url('uploads/mubaligh/' . $mubaligh['foto']) ?>" alt="Foto" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="foto" name="foto" accept="image/*">
                            <label class="custom-file-label" for="foto">Pilih file...</label>
                        </div>
                        <small class="form-text text-muted">Format: JPG, PNG. Maksimal 2MB.</small>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> <?= $isEdit ? 'Update' : 'Simpan' ?>
                    </button>
                    <a href="<?= base_url('admin/mubaligh') ?>" class="btn btn-secondary ml-2">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
// Custom file input label update
$('.custom-file-input').on('change', function() {
    var fileName = $(this).val().split('\\').pop();
    $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
});
</script>
<?= $this->endSection(); ?>
