<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>


<section class="content">
    <div class="container-fluid">
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">General System (Unified Refactoring)</h3>
            </div>
            <div class="card-body">
                <p>Dokumen ini menjelaskan struktur intisari dari aplikasi KUA yang telah direfaktor. Sebelumnya Aplikasi memiliki banyak tabel terpisah (tbl_mubaligh, tbl_imam_masjid dll). Sekarang dilebur menjadi satu tabel utama yaitu <code>tbl_personil</code> yang perilakunya dikendalikan oleh Master Data <code>tbl_entitas_type</code>.</p>

                <h4 class="mt-4">1. Tujuan Penyatuan (Unified)</h4>
                <ul>
                    <li><strong>Skalabilitas</strong>: Menambah profesi baru cukup tambahkan satu baris di <code>tbl_entitas_type</code>. Sistem KUA akan otomatis meng-generate Rute, Form, dan validasi berkasnya tanpa menyewa programmer lagi.</li>
                    <li><strong>Konsistensi Data</strong>: Relasi dokumen lampiran terpusat di satu entitas utama.</li>
                </ul>

                <h4 class="mt-4">2. Diagram Relasi Entitas Personil</h4>
                <pre class="mermaid text-center mt-3 bg-transparent border-0" style="white-space: pre-wrap;">
erDiagram
    TBL_ENTITAS_TYPE ||--o{ TBL_PERSONIL : "mengendalikan form & rute"
    TBL_MASJID_MUSHOLA ||--o{ TBL_PERSONIL : "opsional relasi jika has_masjid_link=1"
    TBL_PERSONIL ||--o{ TBL_BERKAS : "memiliki 1/lebih"
    TBL_ENTITAS_TYPE ||..o{ TBL_SETTING_BERKAS : "menentukan wajib/tidaknya"

    TBL_ENTITAS_TYPE {
        int id PK
        string kode "Contoh: penyuluh_agama"
        string nama_label "Di UI: Penyuluh Agama"
        string operator_group "Membatasi Hak Akses"
        boolean has_masjid_link
        boolean has_sk
    }

    TBL_PERSONIL {
        int id PK
        string entitas_type FK "Merujuk kode entitas_type"
        string nama_lengkap
        string entitas_type
        int id_masjid_mushola FK "Bisa NULL"
    }
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
                            <p class="text-sm">Controller Utama: <code class="text-pink">app/Controllers/Backend/PersonilController.php</code></p>
                            <ul class="text-sm">
                                <li><code class="text-pink">index()</code> - Melayani tampilan DataTables (Read)</li>
                                <li><code class="text-pink">loadForm()</code> - Mengembalikan format form modal HTML dinamis via AJAX</li>
                                <li><code class="text-pink">save()</code> - Menangani Create/Update data utama personil</li>
                                <li><code class="text-pink">delete()</code> - Menghapus data personil</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-warning" style="border-left-color: #ffc107;">
                            <h5>Database & Model</h5>
                            <p class="text-sm">Master Data Controller:</p>
                            <ul class="text-sm">
                                <li><code class="text-pink">tbl_entitas_type</code> - Mengatur struktur form & validasi tabel (Kode, Nama, Has_Masjid, Has_SK)</li>
                                <li><code class="text-pink">tbl_personil</code> - Gudang simpan seluruh entitas menjadi satu</li>
                            </ul>
                            <p class="text-sm">Models: <code class="text-pink">EntitasTypeModel.php</code>, <code class="text-pink">PersonilModel.php</code></p>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="callout callout-success" style="border-left-color: #28a745;">
                            <h5>View File</h5>
                            <ul class="text-sm">
                                <li><code class="text-pink">app/Views/backend/personil/index.php</code></li>
                                <li><code class="text-pink">app/Views/backend/template/sidebar.php</code></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-danger" style="border-left-color: #dc3545;">
                            <h5>Routes</h5>
                            <ul class="text-sm">
                                <li><code class="text-pink">GET admin/personil/(:any)</code></li>
                                <li><code class="text-pink">GET admin/personil/(:any)/data</code></li>
                                <li><code class="text-pink">POST admin/personil/(:any)/save</code></li>
                                <li><code class="text-pink">POST admin/personil/(:any)/delete/(:num)</code></li>
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
