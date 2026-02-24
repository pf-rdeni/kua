<?= $this->extend('backend/template/template'); ?>
<?php
$isEdit = isset($majelis);
$pageTitle = $isEdit ? 'Edit Data Majelis Taklim' : 'Tambah Data Majelis Taklim';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => 'admin/dashboard'],
    ['title' => 'Majelis Taklim', 'url' => 'admin/majelis-taklim'],
    ['title' => 'Form', 'url' => ''],
];
// Mem-pass variabel ke view utama agar dipakai oleh header.php
$this->setVar('pageTitle', $pageTitle);
$this->setVar('breadcrumb', $breadcrumb);
?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-md-12">
        <?php if (session()->has('errors')) : ?>
            <div class="alert alert-danger">
                <ul><?php foreach (session('errors') as $error) : ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
            </div>
        <?php endif ?>

        <div class="card card-primary">
            <form action="<?= $isEdit ? base_url('admin/majelis-taklim/update/' . $majelis['id_majelis_taklim']) : base_url('admin/majelis-taklim/store') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nama Majelis Taklim *</label>
                            <input type="text" class="form-control" name="nama_majelis_taklim" value="<?= old('nama_majelis_taklim', $majelis['nama_majelis_taklim'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Masjid/Mushola (Opsional)</label>
                            <select class="form-control select2" name="id_masjid_mushola">
                                <option value="">-- Pilih Masjid/Mushola --</option>
                                <?php foreach ($masjidList as $masjid) : ?>
                                    <option value="<?= $masjid['id_masjid_mushola'] ?>" <?= old('id_masjid_mushola', $majelis['id_masjid_mushola'] ?? '') == $masjid['id_masjid_mushola'] ? 'selected' : '' ?>>
                                        <?= esc($masjid['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Biarkan kosong jika tidak berafiliasi dengan Masjid di database.</small>
                        </div>
                        <div class="form-group">
                            <label>Alamat Gedung/Jalan</label>
                            <textarea class="form-control" name="alamat" rows="2"><?= old('alamat', $majelis['alamat'] ?? '') ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="provinsi">Provinsi <i class="fas fa-spinner fa-spin d-none ml-1" id="loading-provinsi"></i></label>
                                    <select class="form-control select2-regional" id="provinsi" name="provinsi" style="width: 100%;">
                                        <option value="">-- Sedang Memuat Data... --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kabupaten_kota">Kabupaten/Kota <i class="fas fa-spinner fa-spin d-none ml-1" id="loading-kabupaten"></i></label>
                                    <select class="form-control select2-regional" id="kabupaten_kota" name="kabupaten_kota" style="width: 100%;">
                                        <option value="">-- Pilih Provinsi Dahulu --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="kecamatan">Kecamatan <i class="fas fa-spinner fa-spin d-none ml-1" id="loading-kecamatan"></i></label>
                                    <select class="form-control select2-regional" id="kecamatan" name="kecamatan" style="width: 100%;">
                                        <option value="">-- Pilih Kabupaten/Kota Dahulu --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kelurahan_desa">Kelurahan/Desa <i class="fas fa-spinner fa-spin d-none ml-1" id="loading-desa"></i></label>
                                    <select class="form-control select2-regional" id="kelurahan_desa" name="kelurahan_desa" style="width: 100%;">
                                        <option value="">-- Pilih Kecamatan Dahulu --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="rt">RT</label>
                                    <input type="text" class="form-control" id="rt" name="rt" placeholder="001" value="<?= old('rt', $majelis['rt'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="rw">RW</label>
                                    <input type="text" class="form-control" id="rw" name="rw" placeholder="002" value="<?= old('rw', $majelis['rw'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hari</label>
                                    <input type="text" class="form-control" name="hari" value="<?= old('hari', $majelis['hari'] ?? '') ?>" placeholder="Contoh: Senin">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Waktu</label>
                                    <input type="time" class="form-control" name="waktu" value="<?= old('waktu', $majelis['waktu'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nama Pimpinan</label>
                            <input type="text" class="form-control" name="pimpinan" value="<?= old('pimpinan', $majelis['pimpinan'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>No. HP Pimpinan</label>
                            <input type="text" class="form-control" id="no_hp_pimpinan" name="no_hp_pimpinan" value="<?= old('no_hp_pimpinan', $majelis['no_hp_pimpinan'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Jumlah Jamaah</label>
                            <input type="number" class="form-control" name="jumlah_jamaah" value="<?= old('jumlah_jamaah', $majelis['jumlah_jamaah'] ?? '') ?>">
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="<?= base_url('admin/majelis-taklim') ?>" class="btn btn-default float-right">Batal</a>
                    </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // 1. Standarisasi dan Validasi Nomor HP (Hanya Angka & Ubah +62/62 di awal menjadi 0)
    $('#no_hp_pimpinan').on('input paste', function(e) {
        setTimeout(() => { 
            let val = $(this).val();
            // Hapus semua karakter selain angka dan tanda '+'
            val = val.replace(/[^0-9+]/g, '');
            // Konversi +62 atau 62 di awal menjadi 0 untuk standard lokal
            if (val.startsWith('+62')) { val = '0' + val.substring(3); } 
            else if (val.startsWith('62')) { val = '0' + val.substring(2); }
            // Jika '+' mendadak tertekan di tengah-tengah input angka (typo), buang
            val = val.replace(/\+/g, '');
            $(this).val(val);
        }, 10);
    });



    // --- EMSIFA API WILAYAH INTEGRATION ---
    $('.select2-regional').select2({ theme: 'bootstrap4', tags: true });

    const API_BASE = 'https://www.emsifa.com/api-wilayah-indonesia/api';
    let sProv = '<?= old("provinsi", $majelis["provinsi"] ?? "") ?>';
    let sKab = '<?= old("kabupaten_kota", $majelis["kabupaten_kota"] ?? "") ?>';
    let sKec = '<?= old("kecamatan", $majelis["kecamatan"] ?? "") ?>';
    let sDesa = '<?= old("kelurahan_desa", $majelis["kelurahan_desa"] ?? "") ?>';

    // Set Default: Kepulauan Riau (21), Bintan (21.01), Seri Kuala Lobam (21.01.12)
    const DEFAULT_PROV_ID = '21';      // KEPULAUAN RIAU
    const DEFAULT_KAB_ID  = '2102';    // KABUPATEN BINTAN
    const DEFAULT_KEC_ID  = '2102052'; // SERI KUALA LOBAM

    function populateDropdown(selector, data, defaultOptionText, savedValue, defaultIdTarget = null) {
        let options = `<option value="">${defaultOptionText}</option>`;
        let isSavedInList = false;

        data.forEach(item => {
            let itemVal = `${item.id}|${item.name}`;
            
            // 1. Prioritas Utama: Apakah string item name cocok dengan nilai database $savedValue (kasus edit)
            if (savedValue && item.name.trim().toUpperCase() === savedValue.trim().toUpperCase()) {
                isSavedInList = itemVal;
            }
            // 2. Prioritas Kedua: Apakah ini item dengan ID sama persis dengan $savedValue (kasus form invalid callback)
            else if (savedValue && savedValue === itemVal) {
                isSavedInList = itemVal;
            }
            // 3. Fallback: Jika mode form kosong (Tambah Baru), pilih berdasarkan ID Default
            else if (!savedValue && defaultIdTarget && item.id === defaultIdTarget) {
                isSavedInList = itemVal;
            }
        });

        if (savedValue && !isSavedInList) {
            options = `<option value="${savedValue}">${savedValue}</option>` + options;
            isSavedInList = savedValue;
        }

        data.forEach(item => {
            let itemVal = `${item.id}|${item.name}`;
            let selected = (itemVal === isSavedInList) ? 'selected' : '';
            options += `<option value="${itemVal}" ${selected}>${item.name}</option>`;
        });

        $(selector).html(options);
        if (isSavedInList) {
            $(selector).val(isSavedInList).trigger('change');
        }
    }

    // 1. Fetch Provinces
    $('#loading-provinsi').removeClass('d-none');
    fetch(`${API_BASE}/provinces.json`).then(res => res.json()).then(data => {
        populateDropdown('#provinsi', data, '-- Pilih Provinsi --', sProv, DEFAULT_PROV_ID);
        $('#loading-provinsi').addClass('d-none');
    });

    // 2. Fetch Kab on Prov Change
    $('#provinsi').on('change', function() {
        let val = $(this).val();
        if (!val) { $('#kabupaten_kota').html('<option value="">-- Pilih Provinsi Dahulu --</option>').trigger('change'); return; }
        let id_prov = val.split('|')[0];
        $('#loading-kabupaten').removeClass('d-none');
        fetch(`${API_BASE}/regencies/${id_prov}.json`).then(res => res.json()).then(data => {
            populateDropdown('#kabupaten_kota', data, '-- Pilih Kabupaten/Kota --', sKab, DEFAULT_KAB_ID);
            $('#loading-kabupaten').addClass('d-none');
            sKab = '';
        });
    });

    // 3. Fetch Kec on Kab Change
    $('#kabupaten_kota').on('change', function() {
        let val = $(this).val();
        if (!val) { $('#kecamatan').html('<option value="">-- Pilih Kabupaten Dahulu --</option>').trigger('change'); return; }
        let id_kab = val.split('|')[0];
        $('#loading-kecamatan').removeClass('d-none');
        fetch(`${API_BASE}/districts/${id_kab}.json`).then(res => res.json()).then(data => {
            populateDropdown('#kecamatan', data, '-- Pilih Kecamatan --', sKec, DEFAULT_KEC_ID);
            $('#loading-kecamatan').addClass('d-none');
            sKec = '';
        });
    });

    // 4. Fetch Desa on Kec Change
    $('#kecamatan').on('change', function() {
        let val = $(this).val();
        if (!val) { $('#kelurahan_desa').html('<option value="">-- Pilih Kecamatan Dahulu --</option>'); return; }
        let id_kec = val.split('|')[0];
        $('#loading-desa').removeClass('d-none');
        fetch(`${API_BASE}/villages/${id_kec}.json`).then(res => res.json()).then(data => {
            populateDropdown('#kelurahan_desa', data, '-- Pilih Kelurahan/Desa --', sDesa, null);
            $('#loading-desa').addClass('d-none');
            sDesa = '';
        });
    });

    // 5. Intercept Form Submission u/ hilangkan Prefix ID angka (opsional jika controller sdh tangkap, tapi aman)
    $('form').on('submit', function(e) {
        let cleans = ['provinsi', 'kabupaten_kota', 'kecamatan', 'kelurahan_desa'];
        cleans.forEach(id => {
            let el = $('#' + id);
            if(el.length) {
                let val = el.val() || '';
                let parts = val.split('|');
                if(parts.length > 1) {
                    $('<input>').attr({type: 'hidden', name: id, value: parts[1]}).appendTo(this);
                    el.removeAttr('name');
                }
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
