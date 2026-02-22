<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>


<section class="content">
    <div class="container-fluid">
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Proses Input Data Personil & Otentikasi Dinamis</h3>
            </div>
            <div class="card-body">
                <p>Sidebar dan Controller Personil tidak memilki route spesifik yang di "hard-code", melainkan dicek perlapis berdasarkan kolom <code>operator_group</code>.</p>

                <h4 class="mt-4">1. Manajemen Hak Akses Operator</h4>
                <ul>
                    <li>SuperAdmin memiliki kendali penuh menentukan grup operator apa yang berhak mengurus Form Input Data entitas tertentu lewat menu Pengaturan Entitas.</li>
                    <li>Sistem menggunakan built-in helper <code>in_groups()</code> milik Myth/Auth.</li>
                </ul>

                <h4 class="mt-4">2. Alur Form Personil (Advanced Entry)</h4>
                <ul>
                    <li>Form tidak lagi konvensional. <b>NIK</b> menggunakan plugin <b>Select2</b> yang terhubung ke REST API internal.</li>
                    <li>Sistem mengenali apakah NIK sudah dipakai di entitas yang sama (diblokir) atau di entitas lain (ditawarkan auto-fill via <b>SweetAlert2</b>).</li>
                    <li>Pemilihan peta terintegrasi dengan <b>Leaflet.js</b> untuk akurasi Latitude/Longitude.</li>
                    <li>Sistem bisa melakukan sinkronisasi data NIK paralel secara global jika disetujui pengguna (Popup Konfirmasi saat Update).</li>
                </ul>

                <pre class="mermaid text-center mt-3 bg-transparent border-0" style="white-space: pre-wrap;">
flowchart TD
    A([User Ketik NIK di Select2]) --> B{Pilih/Tekan Enter}
    B --> C[AJAX GET: api/personil/get-by-nik]
    C --> D{Apakah NIK ada di DB?}
    D -->|TIDAK| E([Teruskan Isi Form Manual])
    D -->|YA| F{Cek registered_entities}
    F -->|Termasuk Entitas Saat Ini| G[SweetAlert ERROR: Data Terduplikasi!]
    G --> H([Blokir Input])
    F -->|Hanya Entitas Lain| I[SweetAlert INFO: Tawarkan Auto-Fill]
    I --> J{User Setuju?}
    J -->|YA| K[Inject JSON ke Form Data]
    J -->|TIDAK| E
    K --> L([Simpan / Update Form])
    L --> M{Mode Edit: Cek Relasi Lain}
    M -->|Ada| N[SweetAlert QUESTION: Sinkronisasi Serentak?]
    M -->|Tidak Ada| O([End: Simpan Normal])
    N -->|YA| P[Update Global Berdasarkan NIK]
    N -->|TIDAK| O
                </pre>

                <h4 class="mt-5 border-top pt-3">3. Alur Verifikasi Rute URL</h4>
                <pre class="mermaid text-center mt-3 bg-transparent border-0" style="white-space: pre-wrap;">
flowchart LR
    U(["User Buka Menu: Data Imam Masjid"]) --> F["Filter Myth/Auth Login"]
    F --> C["PersonilController::getEntitasConfig'imam_masjid'"]
    C --> Q[("Ambil Data Setting di tbl_entitas_type")]
    Q --> R{"Cek operator_group"}
    R --> S["Contoh: 'OperatorImamMasjid'"]
    S --> T{"Apakah Session User memiliki Akses Grup Operator tsb?"}
    T -->|"YA"| OK(["Buka Halaman Form / Tabel"])
    T -->|"TIDAK"| FAIL(["Lemparkan Error 404/403: Akses Ditolak"])
                </pre>
            </div>
        </div>

        <div class="card collapsed-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-code mr-2"></i>Informasi Teknis (Untuk Developer)</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Expand">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="callout callout-info" style="border-left-color: #17a2b8;">
                            <h5>Backend Components</h5>
                            <p class="text-sm">Controllers:</p>
                            <ul class="text-sm">
                                <li><code class="text-pink">app/Controllers/Backend/PersonilController.php</code> - Mengurus CRUD Utama & Logika Global Sync NIK.</li>
                                <li><code class="text-pink">app/Controllers/Backend/PersonilApiController.php</code> - Mengurus REST API `searchNik()` & `getByNik()` untuk Select2.</li>
                                <li><code class="text-pink">app/Controllers/Backend/AuthController.php</code> - Memproses Login khusus KUA.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-warning" style="border-left-color: #ffc107;">
                            <h5>Database & Auth Tables</h5>
                            <p class="text-sm">Tabel Kunci Hak Akses:</p>
                            <ul class="text-sm">
                                <li><code class="text-pink">users</code> & <code class="text-pink">auth_groups</code> - Standar bawaan dari Myth/Auth</li>
                                <li><code class="text-pink">auth_groups_users</code> - Tabel Pivot (Relasi User dan Grup)</li>
                                <li><code class="text-pink">tbl_entitas_type.operator_group</code> - Kunci unik pengait visibilitas Rute.</li>
                            </ul>
                            <p class="text-sm">Models: <code class="text-pink">Myth\Auth\Models\UserModel</code></p>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="callout callout-success" style="border-left-color: #28a745;">
                            <h5>View & Frontend Libraries</h5>
                            <ul class="text-sm">
                                <li><code class="text-pink">app/Views/backend/personil/form.php</code> - Titik kumpul utama logika DOM & SweetAlert.</li>
                                <li><code class="text-pink">Select2</code> (AJAX NIK Search & Kelurahan/Desa Dropdown)</li>
                                <li><code class="text-pink">SweetAlert2</code> (Blocking rules & Pop-up Confirmations)</li>
                                <li><code class="text-pink">Leaflet.js</code> & OpenStreetMap (Pemilihan Titik Koordinat GPS)</li>
                                <li class="text-primary mt-2"><b>API Integrasi Wilayah (Emsifa):</b> Menampilkan form interaktif Provinsi -> Kab/Kota -> Kecamatan -> Desa.<br>Untuk mengubah daerah <i>Default</i> yang terpilih secara otomatis saat Input Baru, ubah variabel <code class="text-pink">DEFAULT_PROV_ID</code>, <code class="text-pink">DEFAULT_KAB_ID</code>, dan <code class="text-pink">DEFAULT_KEC_ID</code> di dalam file <code>form.php</code>.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-danger" style="border-left-color: #dc3545;">
                            <h5>Routes</h5>
                            <ul class="text-sm">
                                <li><code class="text-pink">GET login</code>, <code class="text-pink">POST login</code></li>
                                <li><code class="text-pink">GET logout</code></li>
                                <li><code class="text-pink">$routes->group('admin', ['filter' => 'role:SuperAdmin,Admin,...'])</code></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- Script inisialisasi persis seperti implementasi di repo tpqsmart/backend/template/scripts.php -->
<script type="module">
    import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
    mermaid.initialize({ startOnLoad: true });

    // Global listener for AdminLTE card expansion to re-render Mermaid diagrams
    document.addEventListener('DOMContentLoaded', function() {
        $(document).on('expanded.lte.cardwidget', function(event) {
            const card = $(event.target).closest('.card');
            const mermaidDivs = card.find('.mermaid');
            
            mermaidDivs.each(function() {
                const el = $(this);
                if (!el.find('svg').length) {
                    const content = el.text().trim();
                    if (content) {
                        el.removeAttr('data-processed');
                        mermaid.init(undefined, el[0]);
                    }
                }
            });
        });
    });
</script>
<?= $this->endSection(); ?>
