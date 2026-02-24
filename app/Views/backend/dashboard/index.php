<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<?php
$pageTitle = 'Dashboard';
$breadcrumb = [
    ['title' => 'Home', 'url' => 'admin/dashboard'],
    ['title' => 'Dashboard', 'url' => ''],
];
?>

<!-- Info Boxes -->
<div class="row">
    <!-- Mubaligh -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $totalMubaligh ?? 0 ?></h3>
                <p>Mubaligh</p>
            </div>
            <div class="icon"><i class="fas fa-user-tie"></i></div>
            <a href="<?= base_url('admin/personil/mubaligh') ?>" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Masjid & Mushola -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $totalMasjidMushola ?? 0 ?></h3>
                <p>Masjid & Mushola</p>
            </div>
            <div class="icon"><i class="fas fa-mosque"></i></div>
            <a href="<?= base_url('admin/masjid-mushola') ?>" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Imam Masjid -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $totalImamMasjid ?? 0 ?></h3>
                <p>Imam Masjid</p>
            </div>
            <div class="icon"><i class="fas fa-user-check"></i></div>
            <a href="<?= base_url('admin/personil/imam_masjid') ?>" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Fardu Kifayah -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= $totalFarduKifayah ?? 0 ?></h3>
                <p>Fardu Kifayah</p>
            </div>
            <div class="icon"><i class="fas fa-hands-helping"></i></div>
            <a href="<?= base_url('admin/personil/fardu_kifayah') ?>" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Penggali Kubur -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3><?= $totalPenggaliKubur ?? 0 ?></h3>
                <p>Penggali Kubur</p>
            </div>
            <div class="icon"><i class="fas fa-hard-hat"></i></div>
            <a href="<?= base_url('admin/personil/penggali_kubur') ?>" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Majelis Taklim -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3><?= $totalMajelisTaklim ?? 0 ?></h3>
                <p>Majelis Taklim</p>
            </div>
            <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <a href="<?= base_url('admin/majelis-taklim') ?>" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- TPQ & MDTA -->
    <div class="col-lg-3 col-6">
        <div class="small-box" style="background-color: #6f42c1; color: #fff;">
            <div class="inner">
                <h3><?= $totalTpqMdta ?? 0 ?></h3>
                <p>TPQ & MDTA</p>
            </div>
            <div class="icon"><i class="fas fa-school"></i></div>
            <a href="<?= base_url('admin/tpq-mdta') ?>" class="small-box-footer" style="color: rgba(255,255,255,.8);">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Map Distribution Section -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title mr-auto" style="line-height: 2.2rem;">
                    <i class="fas fa-map-marked-alt mr-1"></i>
                    Peta Persebaran Lokasi
                </h3>
                <div class="card-tools d-flex align-items-center">
                    <!-- Dropdown Pencarian Map -->
                    <div style="width: 280px; margin-right: 15px;">
                        <select id="map-search" class="form-control select2" style="width: 100%;">
                            <option value="">Cari lokasi atau nama entitas...</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="maximize">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="dashboard-map" style="height: 500px; width: 100%; z-index: 1;"></div>
            </div>
            <div class="card-footer bg-white">
                <ul class="list-unstyled mb-0 d-flex flex-wrap justify-content-center" style="font-size: 14px;">
                    <li class="mr-4"><i class="fas fa-map-marker-alt" style="color: #28a745; text-shadow: 1px 1px 1px #000;"></i> Masjid / Mushola</li>
                    <li class="mr-4"><i class="fas fa-map-marker-alt" style="color: #6f42c1; text-shadow: 1px 1px 1px #000;"></i> TPQ / MDTA</li>
                    <li><i class="fas fa-map-marker-alt" style="color: #dc3545; text-shadow: 1px 1px 1px #000;"></i> Personil (Mubaligh, dll)</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- Select2 CSS & JS (CDN) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
/* Style override untuk marker HTML agar tertata lurus di titik point-nya */
.custom-div-icon {
    background: transparent;
    border: none;
    display: flex;
    justify-content: center;
    align-items: flex-end;
}
.custom-div-icon i { margin-bottom: -4px; width: 24px; text-align: center; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Ambil data JSON base64 dari Controller
    const rawData = '<?= $mapLocationsJson ?? '' ?>';
    if (!rawData) return; // Jika tidak ada data passing, exit

    let locations = [];
    try {
        locations = JSON.parse(atob(rawData));
    } catch(e) {
        console.error("Gagal mem-parsing data koordinat peta", e);
    }

    // 2. Inisialisasi Peta (Default Center: Bintan secara kasar)
    // Jika ada data, gunakan titik data pertama sebagai sentral
    let centerLat = 1.03451;
    let centerLng = 104.22345;
    if (locations.length > 0) {
        centerLat = parseFloat(locations[0].latitude);
        centerLng = parseFloat(locations[0].longitude);
    }
    
    const map = L.map('dashboard-map').setView([centerLat, centerLng], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // 3. Define Custom Icons menggunakan HTML + CSS DivIcon bawaan Leaflet
    function createCustomIcon(colorHex) {
        return L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="color: ${colorHex}; font-size: 32px; text-shadow: 1px 1px 3px rgba(0,0,0,0.8);"><i class="fas fa-map-marker-alt"></i></div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });
    }

    const iconMasjid = createCustomIcon('#28a745'); // Hijau
    const iconTpq = createCustomIcon('#6f42c1'); // Ungu
    const iconPersonil = createCustomIcon('#dc3545'); // Merah

    // Fungsi untuk membuat Avatar Inisial menggunakan Canvas HTML5
    function generateAvatar(name, bgColor) {
        const canvas = document.createElement('canvas');
        canvas.width = 80;
        canvas.height = 80;
        const ctx = canvas.getContext('2d');
        
        ctx.fillStyle = bgColor;
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        ctx.font = 'bold 36px Arial';
        ctx.fillStyle = '#ffffff';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        
        let words = name.trim().split(/\s+/);
        let initials = '';
        if (words.length > 1) {
            initials = (words[0][0] + words[words.length - 1][0]).toUpperCase();
        } else if (words.length === 1 && words[0].length > 0) {
            initials = words[0][0].toUpperCase();
        } else {
            initials = '?';
        }
        
        ctx.fillText(initials, canvas.width / 2, (canvas.height / 2) + 2);
        return canvas.toDataURL('image/png');
    }

    // 4. Looping data dan tanam Marker
    const markersGroup = L.featureGroup(); // Group layer untuk menampung agar map.fitBounds jalan
    const markerMap = {}; // Penyimpan referensi marker untuk dipanggil dari pencarian
    const searchSelect = $('#map-search');
    
    // Escaper untuk string nama yg memiliki tanda petik
    function escapeHtml(text) {
        return text.replace(/'/g, "\\'").replace(/"/g, "&quot;");
    }

    locations.forEach(loc => {
        let lat = parseFloat(loc.latitude);
        let lng = parseFloat(loc.longitude);
        if(isNaN(lat) || isNaN(lng)) return;

        // Tentukan Icon berdasarkan Tipe
        let targetIcon = iconPersonil;
        let shadowColor = '#dc3545';
        if(loc.tipe === 'Masjid' || loc.tipe === 'Mushola') {
            targetIcon = iconMasjid;
            shadowColor = '#28a745';
        } else if(loc.tipe === 'TPQ / MDTA') {
            targetIcon = iconTpq;
            shadowColor = '#6f42c1';
        }

        // Tentukan path direktori foto
        let folder = 'personil';
        if (loc.tipe === 'Masjid' || loc.tipe === 'Mushola') folder = 'masjid_mushola';
        if (loc.tipe === 'TPQ / MDTA') folder = 'tpq_mdta';
        
        // Buat url default image fallback
        let safeName = escapeHtml(loc.nama);
        let generatedAvatar = generateAvatar(loc.nama, shadowColor);
        let fotoUrl = loc.foto ? '<?= base_url("uploads/") ?>' + folder + '/' + loc.foto : generatedAvatar;

        // Build HTML untuk Popup Info
        const popupHtml = `
            <div style="text-align: center; min-width: 160px; padding-top: 5px;">
                <img src="${fotoUrl}" alt="Foto" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 2px solid ${shadowColor}; margin-bottom: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);" onerror="this.onerror=null; this.src='${generatedAvatar}'">
                <h6 style="margin: 0; font-weight: 700; color: #333;">${loc.nama}</h6>
                <div style="margin-top: 5px; margin-bottom: 5px;">
                   <span class="badge" style="background-color: ${shadowColor}; color: white;">${loc.tipe}</span>
                </div>
                <p style="margin: 0; font-size: 13px; color: #666; max-height: 55px; overflow-y: hidden; line-height: 1.2;">
                   ${loc.alamat || '<i class="text-muted">Alamat kosong</i>'}
                </p>
                <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank" class="btn btn-sm btn-info btn-block mt-2" style="font-size: 12px; border-radius: 4px;">
                    <i class="fas fa-directions mr-1"></i> Rute ke Lokasi
                </a>
            </div>
        `;

        // Tancapkan Maker ke layer Peta
        let marker = L.marker([lat, lng], { icon: targetIcon });
        marker.bindPopup(popupHtml);
        marker.addTo(markersGroup);

        // Rekam referensi marker ini berdasarkan index datanya
        let indexKey = locations.indexOf(loc);
        markerMap[indexKey] = marker;

        // Tambahkan kedalam Select2 Options
        let optionText = `${loc.nama} (${loc.tipe})`;
        let option = new Option(optionText, indexKey, false, false);
        searchSelect.append(option);
    });

    // Tempelkan semua penanda ke peta
    markersGroup.addTo(map);

    // Zoom otomatis (fitToBounds) jika minimal ada 1 marker
    if(locations.length > 0) {
        setTimeout(() => {
            map.fitBounds(markersGroup.getBounds(), { padding: [40, 40], maxZoom: 16 });
        }, 500); // beri delay sejenak agar container siap sempurna
    }

    // 5. Inisialisasi plugin Select2
    searchSelect.select2({
        placeholder: "Cari lokasi atau nama entitas...",
        allowClear: true
    });

    // 6. Tangkap *Event* ketika sebuah nama dicari & dipilih
    searchSelect.on('select2:select', function (e) {
        let selectedIndex = e.params.data.id;
        
        // Panggil referensi marker yang sesuai dengan index yang dipilih
        if (selectedIndex !== "" && markerMap[selectedIndex]) {
            let m = markerMap[selectedIndex];
            
            // Terbang (flyTo) menuju koordinat tersebut dengan animasi
            map.flyTo(m.getLatLng(), 18, {
                animate: true,
                duration: 1.5
            });
            
            // Buka kotak PopUp-nya setelah sampai tujuan
            m.openPopup();
        }
    });

    // Kembalikan ke titik tengah global apabila teks pencarian dihapus (clear)
    searchSelect.on('select2:clear', function (e) {
        if(locations.length > 0) {
            map.fitBounds(markersGroup.getBounds(), { padding: [40, 40], maxZoom: 16 });
            map.closePopup();
        }
    });
});
</script>
<?= $this->endSection(); ?>
