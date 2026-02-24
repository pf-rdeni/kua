<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<?php
$isEdit = isset($personil);
$pageTitle = $isEdit ? 'Edit ' . $entitasConfig['nama_label'] : 'Tambah ' . $entitasConfig['nama_label'];
$breadcrumb = [
    ['title' => 'Home', 'url' => 'admin/dashboard'],
    ['title' => $entitasConfig['nama_label'], 'url' => 'admin/personil/' . $entitasType],
    ['title' => $pageTitle, 'url' => ''],
];
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-<?= $isEdit ? 'edit' : 'plus' ?> mr-2"></i><?= $pageTitle ?>
                </h3>
            </div>

            <form id="form-personil" action="<?= base_url($isEdit ? 'admin/personil/' . $entitasType . '/update/' . $personil['id'] : 'admin/personil/' . $entitasType . '/store') ?>" method="post" enctype="multipart/form-data">
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

                    <?php if ($entitasConfig['has_masjid_link'] && !empty($masjidList)): ?>
                    <!-- Masjid/Mushola -->
                    <div class="form-group">
                        <label for="id_masjid_mushola">Masjid/Mushola <span class="text-danger">*</span></label>
                        <select class="form-control" id="id_masjid_mushola" name="id_masjid_mushola" required>
                            <option value="">-- Pilih Masjid/Mushola --</option>
                            <?php foreach ($masjidList as $masjid): ?>
                                <option value="<?= $masjid['id_masjid_mushola'] ?>"
                                    <?= old('id_masjid_mushola', $personil['id_masjid_mushola'] ?? '') == $masjid['id_masjid_mushola'] ? 'selected' : '' ?>>
                                    <?= esc($masjid['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- NIK -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nik">NIK <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="nik" name="nik" required style="width: 100%;">
                                    <?php if(old('nik', $personil['nik'] ?? '')): ?>
                                        <option value="<?= old('nik', $personil['nik'] ?? '') ?>" selected><?= old('nik', $personil['nik'] ?? '') ?></option>
                                    <?php endif; ?>
                                </select>
                                <small class="text-muted">Ketik 16 Digit NIK. Jika sudah ada di database, pilih dari daftar untuk auto-fill data.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Nama Lengkap -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                       value="<?= old('nama_lengkap', $personil['nama_lengkap'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Tempat Lahir -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="tempat_lahir">Tempat Lahir</label>
                                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir"
                                       value="<?= old('tempat_lahir', $personil['tempat_lahir'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir"
                                       value="<?= old('tanggal_lahir', $personil['tanggal_lahir'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Jenis Kelamin -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="L" <?= old('jenis_kelamin', $personil['jenis_kelamin'] ?? '') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= old('jenis_kelamin', $personil['jenis_kelamin'] ?? '') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <!-- No. HP -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="no_hp">No. HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp"
                                       value="<?= old('no_hp', $personil['no_hp'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="2"><?= old('alamat', $personil['alamat'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <!-- Provinsi -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="provinsi">Provinsi <i class="fas fa-spinner fa-spin d-none ml-1" id="loading-provinsi"></i></label>
                                <select class="form-control select2-regional" id="provinsi" name="provinsi" style="width: 100%;">
                                    <option value="">-- Sedang Memuat Data... --</option>
                                </select>
                            </div>
                        </div>

                        <!-- Kabupaten/Kota -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kabupaten_kota">Kabupaten/Kota <i class="fas fa-spinner fa-spin d-none ml-1" id="loading-kababupaten"></i></label>
                                <select class="form-control select2-regional" id="kabupaten_kota" name="kabupaten_kota" style="width: 100%;">
                                    <option value="">-- Pilih Provinsi Dahulu --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Kecamatan -->
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
                        <!-- Kelurahan/Desa -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kelurahan_desa">Kelurahan/Desa <i class="fas fa-spinner fa-spin d-none ml-1" id="loading-desa"></i></label>
                                <select class="form-control select2-regional" id="kelurahan_desa" name="kelurahan_desa" style="width: 100%;">
                                    <option value="">-- Pilih Kecamatan Dahulu --</option>
                                </select>
                                <small class="text-muted">Pilih atau ketik manual jika tidak ada dalam sistem Emsifa.</small>
                            </div>
                        </div>

                        <!-- RT -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="rt">RT</label>
                                <input type="text" class="form-control" id="rt" name="rt" placeholder="cth: 001"
                                       value="<?= old('rt', $personil['rt'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- RW -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="rw">RW</label>
                                <input type="text" class="form-control" id="rw" name="rw" placeholder="cth: 002"
                                       value="<?= old('rw', $personil['rw'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Pendidikan Terakhir -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="pendidikan_terakhir">Pendidikan Terakhir</label>
                                <select class="form-control" id="pendidikan_terakhir" name="pendidikan_terakhir">
                                    <option value="">-- Pilih --</option>
                                    <?php
                                    $pendidikanList = ['SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3'];
                                    foreach ($pendidikanList as $p): ?>
                                        <option value="<?= $p ?>" <?= old('pendidikan_terakhir', $personil['pendidikan_terakhir'] ?? '') == $p ? 'selected' : '' ?>><?= $p ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Pekerjaan -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="pekerjaan">Pekerjaan</label>
                                <?php 
                                    $jobList = [
                                        'PNS / ASN', 'TNI / POLRI', 'Pegawai BUMN / BUMD', 
                                        'Karyawan Swasta', 'Wiraswasta / Pengusaha', 'Petani / Peternak', 
                                        'Nelayan', 'Buruh / Tukang', 'Guru / Dosen', 'Pelajar / Mahasiswa', 
                                        'Mengurus Rumah Tangga', 'Pensiunan'
                                    ];
                                    $currJob = old('pekerjaan', $personil['pekerjaan'] ?? '');
                                    
                                    // Jika jabatan saat ini ada tapi tidak tercantum di list baku, masukkan sementara sebagai opsi
                                    if (!empty($currJob) && !in_array($currJob, $jobList)) {
                                        array_unshift($jobList, $currJob);
                                    }
                                ?>
                                <select class="form-control select2-pekerjaan" id="pekerjaan" name="pekerjaan" style="width: 100%;">
                                    <option value="">-- Pilih atau Ketik Baru --</option>
                                    <?php foreach($jobList as $job): ?>
                                        <option value="<?= esc($job) ?>" <?= $currJob === $job ? 'selected' : '' ?>><?= esc($job) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Status Aktif -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="status_aktif">Status Aktif</label>
                                <select class="form-control" id="status_aktif" name="status_aktif">
                                    <option value="1" <?= old('status_aktif', $personil['status_aktif'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= old('status_aktif', $personil['status_aktif'] ?? 1) == 0 ? 'selected' : '' ?>>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <?php if ($entitasConfig['has_sk']): ?>
                    <div class="row">
                        <!-- Status Jabatan -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="status">Status Jabatan</label>
                                <input type="text" class="form-control" id="status" name="status" placeholder="cth: Aktif, Pensiun"
                                       value="<?= old('status', $personil['status'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- SK Pengangkatan -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="sk_pengangkatan">SK Pengangkatan</label>
                                <?php if ($isEdit && !empty($personil['sk_pengangkatan'])): ?>
                                    <div class="mb-1">
                                        <small class="text-muted">File saat ini: <?= esc($personil['sk_pengangkatan']) ?></small>
                                    </div>
                                <?php endif; ?>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="sk_pengangkatan" name="sk_pengangkatan" accept=".pdf,.jpg,.jpeg,.png">
                                    <label class="custom-file-label" for="sk_pengangkatan">Pilih file...</label>
                                </div>
                                <small class="form-text text-muted">Format: PDF, JPG, PNG. Maksimal 2MB.</small>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Latitude -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="latitude">Latitude</label>
                                <input type="text" class="form-control" id="latitude" name="latitude" placeholder="cth: 1.0456"
                                       value="<?= old('latitude', $personil['latitude'] ?? '') ?>" readonly>
                            </div>
                        </div>

                        <!-- Longitude -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="longitude">Longitude</label>
                                <input type="text" class="form-control" id="longitude" name="longitude" placeholder="cth: 104.0123"
                                       value="<?= old('longitude', $personil['longitude'] ?? '') ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Map Container -->
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="mb-0">Pilih Lokasi di Peta</label>
                            <button type="button" class="btn btn-info btn-sm" id="btn-detect-location">
                                <i class="fas fa-map-marker-alt mr-1"></i> Deteksi Lokasi Saya
                            </button>
                        </div>
                        <div id="map-container" style="height: 350px; border: 1px solid #ced4da; border-radius: 4px; z-index: 1;"></div>
                        <small class="text-muted mt-1">Geser penanda merah (marker) pada peta untuk mengubah koordinat secara presisi.</small>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" id="btn-submit">
                        <i class="fas fa-save mr-1"></i> <?= $isEdit ? 'Update' : 'Simpan' ?>
                    </button>
                    <button type="button" class="btn btn-secondary ml-2" id="btn-cancel">
                        <i class="fas fa-arrow-left mr-1"></i> Batal Kembali
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<!-- Leaflet Geocoder CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
$(document).ready(function() {
    // 1. Initialize NIK Select2 with Tags (Allowing new entries & AJAX Search)
    $('#nik').select2({
        theme: 'bootstrap4',
        tags: true,
        placeholder: '-- Ketik 16 Digit NIK --',
        language: {
            noResults: function() { return "Tekan Enter untuk menggunakan NIK baru ini"; },
            searching: function() { return "Mencari di Database..."; }
        },
        ajax: {
            url: '<?= base_url('admin/api/personil/search-nik') ?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return { results: data };
            },
            cache: true
        },
        minimumInputLength: 3
    });

    // 1-A. Limit Dropdown Input: Hanya Angka & Maksimal 16 Digit
    $('#nik').on('select2:open', function() {
        // Karena input Select2 ter-generate dinamis secara eksternal, kita ambil elemennya setelah pop-up dropdown terbuka
        let searchField = document.querySelector('.select2-search__field');
        if (searchField) {
            // Pasang max-length pada HTML Attribute
            searchField.maxLength = 16;
            searchField.setAttribute('type', 'tel'); // Paksa NumPad di HP
            
            // Pasang Event Listener Regex untuk Mencegah Huruf / Simbol
            searchField.addEventListener('input', function(e) {
                // Hapus segala karakter selain angka 0-9 secara real-time
                this.value = this.value.replace(/[^0-9]/g, '');
            });
            
            // Cegah Paste teks sembarangan (Hanya Tempel Angka)
            searchField.addEventListener('paste', function(e) {
                e.preventDefault();
                let pastedData = (e.clipboardData || window.clipboardData).getData('text');
                let numericData = pastedData.replace(/[^0-9]/g, '').substring(0, 16);
                document.execCommand('insertText', false, numericData);
            });
        }
    });

    // 1-A2. Logika Tombol Batal & Deteksi Perubahan Data
    let originalFormData = $('#form-personil').serialize(); // Simpan state awal saat halaman dimuat
    
    $('#btn-cancel').on('click', function(e) {
        let currentFormData = $('#form-personil').serialize(); // Ambil state saat tombol batal diklik
        let isEditMode = <?= $isEdit ? 'true' : 'false' ?>;
        
        // Pengecekan ada beda / tidak
        if (originalFormData !== currentFormData) {
            Swal.fire({
                icon: 'warning',
                title: 'Batalkan Perubahan?',
                text: isEditMode ? 'Terdapat perubahan data yang belum disimpan. Anda yakin ingin membatalkan dan mengembalikan form ke keadaan awal?' : 'Ada form yang sudah diisi. Anda yakin ingin keluar dan menghapus isian ini?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kembalikan & Keluar',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('admin/personil/' . $entitasType) ?>';
                }
            });
        } else {
            // Langsung keluar jika tidak ada yang dirubah
            window.location.href = '<?= base_url('admin/personil/' . $entitasType) ?>';
        }
    });

    // 1-B. Inisialisasi Pekerjaan Dropdown + Custom Text (Tags)
    $('.select2-pekerjaan').select2({
        theme: 'bootstrap4',
        tags: true, // Memungkinkan input teks manual jika tidak ada di list
        placeholder: '-- Pilih Pekerjaan atau Ketik Baru --'
    });

    // 1-C. Standarisasi dan Validasi Nomor HP (Hanya Angka & Ubah +62/62 di awal menjadi 0)
    $('#no_hp').on('input paste', function(e) {
        setTimeout(() => { // Beri delay sejenak untuk menangkap paste teks
            let val = $(this).val();
            
            // Hapus semua karakter selain angka dan tanda '+'
            val = val.replace(/[^0-9+]/g, '');
            
            // Konversi +62 atau 62 di awal menjadi 0 untuk standard lokal
            if (val.startsWith('+62')) {
                val = '0' + val.substring(3);
            } else if (val.startsWith('62')) {
                val = '0' + val.substring(2);
            }
            
            // Jika '+' mendadak tertekan di tengah-tengah input angka (typo), buang
            val = val.replace(/\+/g, '');
            
            $(this).val(val);
        }, 10);
    });

    // 1-D. Pembatasan Input RT RW (Hanya Angka)
    $('#rt, #rw').on('input paste', function(e) {
        setTimeout(() => { 
            let val = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(val);
        }, 10);
    });

    // 1-E. Integrasi API Wilayah Indonesia Emsifa (Cascading berjenjang)
    $('.select2-regional').select2({
        theme: 'bootstrap4',
        tags: true // Tetap izinkan input manual jika API mati
    });

    const API_BASE = 'https://www.emsifa.com/api-wilayah-indonesia/api';

    // [PENTING UNTUK DEVELOPER]: Ganti ID Wilayah di bawah jika aplikasi digunakan di daerah lain
    const DEFAULT_PROV_ID = '21';      // KEPULAUAN RIAU
    const DEFAULT_KAB_ID  = '2102';    // KABUPATEN BINTAN
    const DEFAULT_KEC_ID  = '2102052'; // SERI KUALA LOBAM

    // Existing saved values (Mode Edit / Validasi Gagal). Jika kosong rata (Tambah Baru), set Default.
    let sProv = '<?= old('provinsi', $personil['provinsi'] ?? '') ?>';
    let sKab  = '<?= old('kabupaten_kota', $personil['kabupaten_kota'] ?? '') ?>';
    let sKec  = '<?= old('kecamatan', $personil['kecamatan'] ?? '') ?>';
    let sDesa = '<?= old('kelurahan_desa', $personil['kelurahan_desa'] ?? '') ?>';

    // Helper: Formatter Title Case
    function toTitleCase(str) {
        return str.toLowerCase().replace(/\b[a-z]/g, function(letter) { return letter.toUpperCase(); });
    }

    // Helper: Build Options & Auto-Select
    function populateDropdown(selector, data, placeholder, savedValue, defaultIdTarget) {
        let options = `<option value="">${placeholder}</option>`;
        let isSavedInList = false;
        
        data.sort((a, b) => a.name.localeCompare(b.name));
        data.forEach(item => {
            let itemName = toTitleCase(item.name);
            let itemVal = item.id + '|' + itemName; // Value = "ID|Nama"
            options += `<option value="${itemVal}">${itemName}</option>`;
            
            // Logika Auto-Select:
            // 1. Cek apabila string nama savedValue cocok persis
            if (savedValue && savedValue.toUpperCase() === itemName.toUpperCase()) {
                isSavedInList = itemVal;
            } 
            // 2. Cek apakah savedValue cocok persis dengan "ID|Nama"
            else if (savedValue && savedValue === itemVal) {
                isSavedInList = itemVal;
            }
            // 3. Fallback: Jika mode form kosong (Tambah Baru), pilih berdasarkan ID Default
            else if (!savedValue && defaultIdTarget && item.id === defaultIdTarget) {
                isSavedInList = itemVal;
            }
        });

        // Paksa insert jika tidak cocok tapi punya saved value (hasil tag ketik manual)
        if (savedValue && !isSavedInList) {
            options = `<option value="${savedValue}">${savedValue}</option>` + options;
            isSavedInList = savedValue;
        }

        $(selector).html(options);
        if (isSavedInList) {
            $(selector).val(isSavedInList).trigger('change');
        }
    }

    // Fetch Provinces on Load
    $('#loading-provinsi').removeClass('d-none');
    fetch(`${API_BASE}/provinces.json`).then(res => res.json()).then(data => {
        populateDropdown('#provinsi', data, '-- Pilih Provinsi --', sProv, DEFAULT_PROV_ID);
        $('#loading-provinsi').addClass('d-none');
    }).catch(() => { $('#loading-provinsi').addClass('d-none'); });

    // On Provinsi Change -> Fetch Kabupaten
    $('#provinsi').on('change', function(e) {
        let val = $(this).val();
        if (!val) { $('#kabupaten_kota').html('<option value="">-- Pilih Provinsi Dahulu --</option>').trigger('change'); return; }
        let parts = val.split('|');
        if (parts.length < 2) return; // Ignore manual tag
        
        let id_prov = parts[0];
        $('#loading-kabupaten').removeClass('d-none');
        fetch(`${API_BASE}/regencies/${id_prov}.json`).then(res => res.json()).then(data => {
            populateDropdown('#kabupaten_kota', data, '-- Pilih Kabupaten/Kota --', sKab, DEFAULT_KAB_ID);
            $('#loading-kabupaten').addClass('d-none');
            sKab = ''; // Reset flag saved setelah 1x pakai agar tidak macet saat pilih beda provinsi via UI
        }).catch(() => { $('#loading-kabupaten').addClass('d-none'); });
    });

    // On Kabupaten Change -> Fetch Kecamatan
    $('#kabupaten_kota').on('change', function(e) {
        let val = $(this).val();
        if (!val) { $('#kecamatan').html('<option value="">-- Pilih Kabupaten Dahulu --</option>').trigger('change'); return; }
        let parts = val.split('|');
        if (parts.length < 2) return;
        
        let id_kab = parts[0];
        $('#loading-kecamatan').removeClass('d-none');
        fetch(`${API_BASE}/districts/${id_kab}.json`).then(res => res.json()).then(data => {
            populateDropdown('#kecamatan', data, '-- Pilih Kecamatan --', sKec, DEFAULT_KEC_ID);
            $('#loading-kecamatan').addClass('d-none');
            sKec = '';
        }).catch(() => { $('#loading-kecamatan').addClass('d-none'); });
    });

    // On Kecamatan Change -> Fetch Desa
    $('#kecamatan').on('change', function(e) {
        let val = $(this).val();
        if (!val) { $('#kelurahan_desa').html('<option value="">-- Pilih Kecamatan Dahulu --</option>'); return; }
        let parts = val.split('|');
        if (parts.length < 2) return;
        
        let id_kec = parts[0];
        $('#loading-desa').removeClass('d-none');
        fetch(`${API_BASE}/villages/${id_kec}.json`).then(res => res.json()).then(data => {
            populateDropdown('#kelurahan_desa', data, '-- Pilih Kelurahan/Desa --', sDesa, null);
            $('#loading-desa').addClass('d-none');
            sDesa = '';
        }).catch(() => { $('#loading-desa').addClass('d-none'); });
    });

    // 1-F. Intercept Submit untuk membuang ID Prefix pada Value Dropdown
    $('#form-personil').on('submit', function(e) {
        let cleans = ['provinsi', 'kabupaten_kota', 'kecamatan', 'kelurahan_desa'];
        cleans.forEach(id => {
            let el = $('#' + id);
            if(el.length) {
                let val = el.val() || '';
                let parts = val.split('|');
                if(parts.length > 1) {
                    // Buat Hidden element penimpa khusus namanya saja agar lolos validasi form POST
                    $('<input>').attr({type: 'hidden', name: id, value: parts[1]}).appendTo(this);
                    el.removeAttr('name'); // Cabut atribut name original form yg berisi ID
                }
            }
        });
    });

    // Event saat NIK dipilih (Dari hasil suggestion atau Enter tag baru)
    $('#nik').on('select2:select', function (e) {
        var selectedNik = e.params.data.id;
        
        // Cek Peringatan Panjang NIK Minimal
        if (selectedNik.length < 16) {
            Swal.fire({toast: true, position: 'top-end', icon: 'warning', title: 'Perhatian: NIK ini kurang dari 16 Digit!', showConfirmButton: false, timer: 4000});
        }

        // Munculkan Loading Swal
        Swal.fire({
            title: 'Memeriksa Data NIK...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        // Tembak API Pengecekan
        $.ajax({
            url: '<?= base_url('admin/api/personil/get-by-nik') ?>',
            type: 'GET',
            data: { nik: selectedNik },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var pData = response.data;
                    var registeredIn = response.registered_entities;
                    var currentEntity = '<?= $entitasType ?>';

                    // [Validasi 1] NIK Dilarang Sama di Entitas yang Sama
                    if (registeredIn.includes(currentEntity)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Terduplikasi!',
                            html: `Personil dengan NIK <b>${selectedNik}</b> (a.n <b>${pData.nama_lengkap}</b>) telah terdaftar aktif di entitas <b><?= esc($entitasConfig['nama_label']) ?></b> ini.<br><br>Harap masukkan NIK lain atau cari data tersebut di halaman daftar.`,
                            confirmButtonText: 'Kembali'
                        });
                        $('#nik').val(null).trigger('change');
                        return; // Berhenti memproses Auto-fill
                    }

                    // [Validasi 2] NIK Sama, tapi di Entitas Berbeda -> Tawarkan Auto-Fill
                    let arrayLabels = registeredIn.map(str => str.toUpperCase()).join(' & ');
                    
                    Swal.fire({
                        icon: 'info',
                        title: 'Ditemukan di Entitas Lain!',
                        html: `Sistem mendeteksi NIK ini telah terdaftar sebagai <b>${arrayLabels}</b> atas nama <b>${pData.nama_lengkap}</b>.<br><br>Apakah Anda ingin menarik data tersebut untuk otomatis mengisi form ini?`,
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-magic mr-1"></i> Ya, Isi Otomatis',
                        cancelButtonText: 'Tidak, Mulai Kosong',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Inject Data ke DOM Text Inputs
                            $('#nama_lengkap').val(pData.nama_lengkap);
                            $('#tempat_lahir').val(pData.tempat_lahir);
                            $('#tanggal_lahir').val(pData.tanggal_lahir);
                            $('#jenis_kelamin').val(pData.jenis_kelamin);
                            $('#no_hp').val(pData.no_hp);
                            $('#alamat').val(pData.alamat);
                            
                            // Re-Inject Regional Vars Globals to restart auto-select chain cascade
                            sProv = pData.provinsi;
                            sKab = pData.kabupaten_kota;
                            sKec = pData.kecamatan;
                            sDesa = pData.kelurahan_desa;
                            
                            // 1. Coba loop apakah namanya ada di dalam option list Provinsi
                            let foundProv = false;
                            $('#provinsi option').each(function() {
                                // Nama di option adalah teks asli, value-nya adalah "ID|Nama"
                                if($(this).text().toUpperCase() === (sProv || '').toUpperCase()) {
                                    $(this).prop('selected', true);
                                    foundProv = true;
                                }
                            });
                            
                            // 2. Jika tidak ketemu di list Emsifa, paksa buat option baru agar Trigger Change bisa jalan
                            if(!foundProv && sProv) {
                                $('#provinsi').append(new Option(sProv, sProv, true, true));
                            }
                            $('#provinsi').trigger('change'); // Kickstart the cascade!
                            
                            $('#rt').val(pData.rt);
                            $('#rw').val(pData.rw);
                            $('#pendidikan_terakhir').val(pData.pendidikan_terakhir);
                            $('#pekerjaan').val(pData.pekerjaan).trigger('change');
                            
                            // Map Coordinates Auto-Placement
                            if (pData.latitude && pData.longitude && typeof marker !== 'undefined' && typeof map !== 'undefined') {
                                document.getElementById('latitude').value = pData.latitude;
                                document.getElementById('longitude').value = pData.longitude;
                                const newLatLng = new L.LatLng(pData.latitude, pData.longitude);
                                marker.setLatLng(newLatLng);
                                map.flyTo(newLatLng, 16);
                            }
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Data Personil Berhasil Tersalin!', showConfirmButton: false, timer: 3000 });
                        } else {
                            // User memilih No, artinya mereka sadar ada NIK ini tapi ingin isi manual (Boleh Saja)
                        }
                    });

                } else {
                    Swal.close(); // Tidak ada di DB (Personil pure baru), tutup loading Swal
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal memverifikasi NIK ke server Node.', 'error');
            }
        });
    });

    // 2. Custom file input label update
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
    });

    // 3. Initialize Leaflet Map
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const btnDetect = document.getElementById('btn-detect-location');

    // Default coordinate (KUA Seri Kuala Lobam rough area or standard fallback)
    let initialLat = latInput.value ? parseFloat(latInput.value) : 1.03451;
    let initialLng = lngInput.value ? parseFloat(lngInput.value) : 104.22345;

    // Pastikan nilai default ditanam jika kosong
    if(!latInput.value) {
        latInput.value = initialLat;
        lngInput.value = initialLng;
    }

    const map = L.map('map-container').setView([initialLat, initialLng], 13);
    
    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Draggable marker
    let marker = L.marker([initialLat, initialLng], {draggable: true}).addTo(map);

    // Add Geocoder Search Control
    let geocoder = L.Control.geocoder({
        defaultMarkGeocode: false,
        geocoder: L.Control.Geocoder.photon(), // Bypass Nominatim CORS (Error 425) di Localhost
        placeholder: "Cari tempat atau jalan..."
    })
    .on('markgeocode', function(e) {
        let latlng = e.geocode.center;
        
        // Photon kadang tidak mengembalikan Bounding Box (bbox) yang valid
        if (e.geocode.bbox && typeof e.geocode.bbox.getSouthEast === 'function') {
            try {
                let bbox = e.geocode.bbox;
                let poly = L.polygon([
                    bbox.getSouthEast(),
                    bbox.getNorthEast(),
                    bbox.getNorthWest(),
                    bbox.getSouthWest()
                ]);
                map.fitBounds(poly);
            } catch (error) {
                map.setView(latlng, 16); // Fallback jika BBox nge-bug
            }
        } else {
            map.setView(latlng, 16); // Fallback utama untuk Photon
        }
        
        marker.setLatLng(latlng);
        latInput.value = latlng.lat.toFixed(6);
        lngInput.value = latlng.lng.toFixed(6);
    })
    .addTo(map);

    // Event listener when marker is dragged
    marker.on('dragend', function(e) {
        const position = marker.getLatLng();
        latInput.value = position.lat.toFixed(6);
        lngInput.value = position.lng.toFixed(6);
    });

    // Detect My Location
    btnDetect.addEventListener('click', function() {
        if (navigator.geolocation) {
            btnDetect.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mendeteksi...';
            btnDetect.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    const newLatLng = new L.LatLng(lat, lng);
                    marker.setLatLng(newLatLng);
                    map.flyTo(newLatLng, 16);
                    
                    latInput.value = lat.toFixed(6);
                    lngInput.value = lng.toFixed(6);

                    btnDetect.innerHTML = '<i class="fas fa-map-marker-alt mr-1"></i> Deteksi Lokasi Saya';
                    btnDetect.disabled = false;
                    
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'success', title: 'Lokasi Anda ditemukan!', showConfirmButton: false, timer: 3000
                    });
                },
                function(error) {
                    btnDetect.innerHTML = '<i class="fas fa-map-marker-alt mr-1"></i> Deteksi Lokasi Saya';
                    btnDetect.disabled = false;
                    Swal.fire('Error', 'Gagal mendapatkan lokasi GPS. Pastikan Izin Lokasi/GPS aktif di browser/perangkat Anda.', 'error');
                },
                { enableHighAccuracy: true }
            );
        } else {
            Swal.fire('Error', 'Browser Anda tidak mendukung Geolokasi.', 'error');
        }
    });

    // 4. Intercept Form Submit untuk Global Sync Confirmation (Hanya mode EDIT)
    <?php if ($isEdit): ?>
    $('#form-personil').on('submit', function(e) {
        e.preventDefault(); // Pause submission
        var form = this;
        var currentNik = $('#nik').val();
        
        if (!currentNik) {
            $(form).off('submit').submit();
            return;
        }

        Swal.fire({ title: 'Memeriksa Relasi Data...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        $.ajax({
            url: '<?= base_url('admin/api/personil/get-by-nik') ?>',
            type: 'GET',
            data: { nik: currentNik },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.registered_entities && response.registered_entities.length > 1) {
                    
                    let rawEntities = response.registered_entities.filter(item => item !== '<?= $entitasType ?>');
                    
                    if (rawEntities.length > 0) {
                        let arrayLabels = rawEntities.map(str => str.toUpperCase()).join(' & ');
                        
                        // Kumpulkan Data Form Saat Ini
                        let currentData = {
                            nama_lengkap: $('#nama_lengkap').val(),
                            tempat_lahir: $('#tempat_lahir').val(),
                            tanggal_lahir: $('#tanggal_lahir').val(),
                            jenis_kelamin: $('#jenis_kelamin').val(),
                            no_hp: $('#no_hp').val(),
                            alamat: $('#alamat').val(),
                            provinsi: ($('#provinsi').val() || '').split('|')[1] || '',
                            kabupaten_kota: ($('#kabupaten_kota').val() || '').split('|')[1] || '',
                            kecamatan: ($('#kecamatan').val() || '').split('|')[1] || '',
                            kelurahan_desa: ($('#kelurahan_desa').val() || '').split('|')[1] || '',
                            rt: $('#rt').val(),
                            rw: $('#rw').val(),
                            pendidikan_terakhir: $('#pendidikan_terakhir').val(),
                            pekerjaan: $('#pekerjaan').val()
                        };

                        // Deteksi Perubahan (Diff)
                        let diffHtml = '';
                        let hasChanges = false;
                        let labels = {
                            nama_lengkap: 'Nama Lengkap', tempat_lahir: 'Tempat Lahir', tanggal_lahir: 'Tgl Lahir',
                            jenis_kelamin: 'Jenis Kelamin', no_hp: 'No. HP', alamat: 'Alamat', provinsi: 'Provinsi',
                            kabupaten_kota: 'Kab/Kota', kecamatan: 'Kecamatan', kelurahan_desa: 'Desa/Kel',
                            rt: 'RT', rw: 'RW', pendidikan_terakhir: 'Pendidikan', pekerjaan: 'Pekerjaan'
                        };

                        let pData = response.data; // Data Asli dari DB
                        
                        for (let key in currentData) {
                            let oldVal = (pData[key] || '').toString().trim();
                            let newVal = (currentData[key] || '').toString().trim();
                            
                            // Normalisasi komparasi case-insensitive khusus dropdown dinamis
                            if (oldVal.toUpperCase() !== newVal.toUpperCase()) {
                                hasChanges = true;
                                diffHtml += `<tr>
                                    <td class="text-left py-1 text-sm border-bottom"><b>${labels[key]}</b></td>
                                    <td class="text-left py-1 text-sm border-bottom text-muted"><s>${oldVal || '<i>(Kosong)</i>'}</s></td>
                                    <td class="text-left py-1 text-sm border-bottom text-success"><i class="fas fa-arrow-right mr-1"></i> ${newVal || '<i>(Dikosongkan)</i>'}</td>
                                </tr>`;
                            }
                        }

                        // Susun Pesan Akhir
                        let finalHtml = `Personil ini juga berafiliasi sebagai <b>${arrayLabels}</b>.<br><br>`;
                        
                        if (hasChanges) {
                            finalHtml += `
                                <div class="text-left mb-2 text-sm"><b>Rincian Perubahan Profil:</b></div>
                                <div class="table-responsive mb-3" style="max-height: 200px; overflow-y: auto;">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tbody>${diffHtml}</tbody>
                                    </table>
                                </div>
                                Apakah Anda setuju untuk otomatis <b>menyinkronkan</b> perubahan ini ke entitas tersebut sekaligus?`;
                        } else {
                            finalHtml += `Sistem tidak mendeteksi adanya perubahan pada Profil Utama (Nama, Alamat, Kontak, dll). Sinkronisasi mungkin tidak berdampak signifikan. Tetap Lanjutkan?`;
                        }
                        
                        Swal.fire({
                            icon: 'question',
                            title: 'Update Data Serentak?',
                            html: finalHtml,
                            width: hasChanges ? '600px' : '500px',
                            showCancelButton: true,
                            allowOutsideClick: true, // Mengizinkan klik diluar alert untuk cancel
                            confirmButtonText: '<i class="fas fa-sync-alt mr-1"></i> Ya, Sinkronisasi Semua',
                            cancelButtonText: 'Tidak, Update Entitas Ini Saja',
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#6c757d'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('<input>').attr({type: 'hidden', name: 'sync_all', value: '1'}).appendTo(form);
                                $(form).off('submit').submit(); // Resume submission
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                // User explicitly clicked "Tidak, Update Entitas Ini Saja"
                                $('<input>').attr({type: 'hidden', name: 'sync_all', value: '0'}).appendTo(form);
                                $(form).off('submit').submit(); 
                            } else {
                                // Di-cancel karena klik diluar (dismissed) or escape key -> Jangan submit apapun
                                Swal.fire({toast: true, position: 'top-end', icon: 'info', title: 'Update Dibatalkan', showConfirmButton: false, timer: 3000});
                                return false; // Form tidak jadi disubmit
                            }
                        });
                    } else {
                        $(form).off('submit').submit(); // Tidak ada entitas lain
                    }
                } else {
                    $(form).off('submit').submit(); // Tidak ada di DB atau cuma 1
                }
            },
            error: function() {
                $(form).off('submit').submit(); // Fallback
            }
        });
    });
    <?php endif; ?>

});
</script>
<?= $this->endSection(); ?>
