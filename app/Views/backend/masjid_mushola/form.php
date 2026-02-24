<?= $this->extend('backend/template/template'); ?>

<?php
$isEdit = isset($masjid);
$pageTitle = $isEdit ? 'Edit Data Masjid/Mushola' : 'Tambah Data Masjid/Mushola';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => 'admin/dashboard'],
    ['title' => 'Masjid & Mushola', 'url' => 'admin/masjid-mushola'],
    ['title' => 'Form', 'url' => ''],
];
// Mem-pass variabel ke view utama agar dipakai oleh header.php
$this->setVar('pageTitle', $pageTitle);
$this->setVar('breadcrumb', $breadcrumb);
?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-md-12">
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
                                    <input type="text" class="form-control" id="no_hp_ketua" name="no_hp_ketua" value="<?= old('no_hp_ketua', $masjid['no_hp_ketua'] ?? '') ?>">
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
                            </div>
                        </div>

                        <!-- Full Width Map & Location Group -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-map-marked-alt mr-2"></i>Koordinat Geografis</h5>
                                
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label>Latitude</label>
                                        <input type="text" class="form-control" id="latitude" name="latitude" value="<?= old('latitude', $masjid['latitude'] ?? '') ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Longitude</label>
                                        <input type="text" class="form-control" id="longitude" name="longitude" value="<?= old('longitude', $masjid['longitude'] ?? '') ?>" readonly>
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
                                    <div id="map-container" style="height: 400px; border: 1px solid #ced4da; border-radius: 4px; z-index: 1;"></div>
                                    <small class="text-muted mt-1">Geser penanda merah (marker) pada peta untuk mengubah koordinat secara presisi.</small>
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
    // 1. Standarisasi dan Validasi Nomor HP (Hanya Angka & Ubah +62/62 di awal menjadi 0)
    $('#no_hp_ketua').on('input paste', function(e) {
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
});
</script>
<?= $this->endSection(); ?>
