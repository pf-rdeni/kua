<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<?php
$isEdit  = !empty($agenda);
$formUrl = $isEdit
    ? base_url('admin/agenda-masjid/update/' . $agenda['id'])
    : base_url('admin/agenda-masjid/store');

$namaEntitas = $entitas['_nama_entitas'] ?? 'Entitas';

$jenisOptions = [
    'ceramah'      => '🎙️ Ceramah / Kajian',
    'ta_lim'       => '📖 Ta\'lim / Pengajian',
    'sosial'       => '🤲 Kegiatan Sosial',
    'buka_bersama' => '🍽️ Buka Bersama',
    'tadarus'      => '📿 Tadarus Al-Qur\'an',
    'sahur'        => '🌙 Sahur Bersama',
    'lainnya'      => '⭐ Lainnya',
];
?>

<!-- Flash errors -->
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger alert-dismissible">
    <button class="close" data-dismiss="alert">&times;</button>
    <ul class="mb-0">
        <?php foreach (session()->getFlashdata('errors') as $e): ?>
            <li><?= esc($e) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form action="<?= $formUrl ?>" method="POST">
    <?= csrf_field() ?>

    <div class="row">
        <div class="col-lg-7">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-plus mr-2"></i><?= $isEdit ? 'Edit' : 'Tambah' ?> Agenda Kegiatan</h3>
                    <div class="card-tools">
                        <small class="text-muted mr-2">
                            <i class="fas fa-<?= $entitasType === 'masjid_mushola' ? 'mosque' : 'chalkboard-teacher' ?> mr-1"></i><?= esc($namaEntitas) ?>
                        </small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="judul_kegiatan">Judul Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" name="judul_kegiatan" id="judul_kegiatan" class="form-control"
                               value="<?= old('judul_kegiatan', $agenda['judul_kegiatan'] ?? '') ?>"
                               placeholder="Contoh: Ceramah Tarawih, Tadarus Al-Qur'an Bersama..." required>
                    </div>

                    <div class="form-group">
                        <label for="jenis">Jenis Kegiatan <span class="text-danger">*</span></label>
                        <select name="jenis" id="jenis" class="form-control" required>
                            <?php foreach ($jenisOptions as $k => $v): ?>
                                <option value="<?= $k ?>" <?= old('jenis', $agenda['jenis'] ?? 'ceramah') === $k ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control"
                                       value="<?= old('tanggal', $agenda['tanggal'] ?? date('Y-m-d')) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="waktu_mulai">Waktu Mulai</label>
                                <input type="time" name="waktu_mulai" id="waktu_mulai" class="form-control"
                                       value="<?= old('waktu_mulai', $agenda['waktu_mulai'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="waktu_selesai">Waktu Selesai</label>
                                <input type="time" name="waktu_selesai" id="waktu_selesai" class="form-control"
                                       value="<?= old('waktu_selesai', $agenda['waktu_selesai'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Penceramah / Pengisi</label>
                        <div class="mb-2">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="mode_teks" name="mode_penceramah" value="teks" class="custom-control-input"
                                       <?= empty(old('id_personil', $agenda['id_personil'] ?? '')) ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="mode_teks">Isi nama langsung</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="mode_db" name="mode_penceramah" value="db" class="custom-control-input"
                                       <?= !empty(old('id_personil', $agenda['id_personil'] ?? '')) ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="mode_db">Pilih dari database mubaligh</label>
                            </div>
                        </div>

                        <div id="penceramah-teks">
                            <input type="text" name="nama_penceramah" id="nama_penceramah" class="form-control"
                                   value="<?= old('nama_penceramah', (!empty($agenda['id_personil']) ? '' : ($agenda['nama_penceramah'] ?? ''))) ?>"
                                   placeholder="Nama penceramah atau pengisi kegiatan...">
                        </div>

                        <div id="penceramah-db" style="display:none;">
                            <select name="id_personil" id="id_personil" class="form-control select2-mubaligh">
                                <?php if (!empty($agenda['id_personil'])): ?>
                                    <option value="<?= $agenda['id_personil'] ?>" selected>
                                        <?= esc($agenda['nama_penceramah'] ?? '') ?>
                                    </option>
                                <?php else: ?>
                                    <option value=""></option>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Cari berdasarkan nama atau NIA mubaligh</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lokasi">Lokasi <small class="text-muted">(Opsional)</small></label>
                        <input type="text" name="lokasi" id="lokasi" class="form-control"
                               value="<?= old('lokasi', $agenda['lokasi'] ?? '') ?>"
                               placeholder="Contoh: Aula Masjid Lantai 2, Halaman Masjid...">
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi / Keterangan</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"
                                  placeholder="Keterangan tambahan..."><?= old('deskripsi', $agenda['deskripsi'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-cog mr-2"></i>Pengaturan</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Status</label>
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="is_published" value="0">
                            <input type="checkbox" name="is_published" value="1" class="custom-control-input" id="is_published"
                                   <?= old('is_published', $agenda['is_published'] ?? 1) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="is_published">Aktif / Publikasikan</label>
                        </div>
                        <small class="text-muted">Draft tidak akan muncul di reminder dashboard.</small>
                    </div>

                    <div class="callout callout-info mt-3">
                        <h6><i class="fas fa-info-circle mr-1"></i>Info</h6>
                        <p class="mb-0 small">Agenda aktif akan muncul sebagai <strong>pengingat di dashboard</strong> selama 7 hari ke depan.</p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body d-flex justify-content-between">
                    <a href="<?= base_url('admin/agenda-masjid') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> <?= $isEdit ? 'Perbarui' : 'Simpan' ?> Agenda
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    function togglePenceramah() {
        const mode = $('input[name="mode_penceramah"]:checked').val();
        if (mode === 'db') {
            $('#penceramah-teks').hide().find('input').val('');
            $('#penceramah-db').show();
        } else {
            $('#penceramah-db').hide().find('select').val(null).trigger('change');
            $('#penceramah-teks').show();
        }
    }
    $('input[name="mode_penceramah"]').on('change', togglePenceramah);
    togglePenceramah();

    $('#id_personil').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Cari nama atau NIA mubaligh...',
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: '<?= base_url('admin/agenda-masjid/search-mubaligh') ?>',
            dataType: 'json',
            delay: 300,
            data: function(params) { return { q: params.term }; },
            processResults: function(data) { return { results: data.results }; }
        }
    });
});
</script>
<?= $this->endSection(); ?>
