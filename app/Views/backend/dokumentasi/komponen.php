<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>


<section class="content">
    <div class="container-fluid">
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Proses Berkas Lampiran</h3>
            </div>
            <div class="card-body">
                <p>Halaman ini memaparkan tata kelola dokumen persyaratan yang harus diunggah personil sebelum dinyatakan Valid.</p>

                <h4 class="mt-4">1. Pengaturan Dokumen Wajib</h4>
                <p>Menggunakan tabel `tbl_setting_berkas` yang merelasikan jenis dokumen (KTP, Buku Rekening, SK, dsb) dengan Entitas tertentu (Mubaligh, Penyuluh, dll).</p>

                <h4 class="mt-4">2. Sinkronisasi Unggahan</h4>
                <pre class="mermaid text-center mt-3 bg-transparent border-0" style="white-space: pre-wrap;">
graph TD
    A["Admin/Operator Membuka Profil Personil"] --> B["Lihat Daftar Berkas Lampiran"]
    B --> C["Hitung Jumlah Berkas Terunggah"]
    C --> D{"Apakah Sesuai tbl_setting_berkas?"}
    D -->|"Belum Lengkap"| E["Tampilkan Status Merah / Warning"]
    D -->|"Lengkap"| F["Tampilkan Mode Hijau (Valid)"]
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
                            <p class="text-sm">Controller: <code class="text-pink">app/Controllers/Backend/BerkasController.php</code></p>
                            <ul class="text-sm">
                                <li><code class="text-pink">manageBerkas()</code> - Tampil halaman berkas (UI List Berkas & Profil)</li>
                                <li><code class="text-pink">uploadBerkas()</code> - Menyimpan Ijazah, Rekening, SP, dll</li>
                                <li><code class="text-pink">deleteBerkas($id)</code> - Hapus berkas lampiran dari Database & Storage</li>
                                <li><code class="text-pink">uploadProfilPhoto()</code> / <code class="text-pink">deleteProfilPhoto()</code> - Hapus rincian Pas Foto</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-warning" style="border-left-color: #ffc107;">
                            <h5>Database & Model</h5>
                            <p class="text-sm">Tables (Sinkronisasi status valid):</p>
                            <ul class="text-sm">
                                <li><code class="text-pink">tbl_personil.foto</code> - Foto profil spesifik</li>
                                <li><code class="text-pink">tbl_berkas</code> - Memetakan Id_personil_relasi dengan Tipe Dokumen (KTP, Buku Rekening, SK_Pegawai) dan path file-nya.</li>
                                <li><code class="text-pink">tbl_setting_berkas</code> - Kamus wajib berisi "Apakah profesi ini mewajibkan KTP? (Is_Wajib 1/0)"</li>
                            </ul>
                            <p class="text-sm">Models: <code class="text-pink">BerkasModel.php</code>, <code class="text-pink">SettingBerkasModel.php</code></p>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="callout callout-success" style="border-left-color: #28a745;">
                            <h5>View File</h5>
                            <ul class="text-sm">
                                <li><code class="text-pink">app/Views/backend/berkas_lampiran/index.php</code></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-danger" style="border-left-color: #dc3545;">
                            <h5>Routes</h5>
                            <ul class="text-sm">
                                <li><code class="text-pink">GET admin/berkas_lampiran/(:any)/(:num)</code></li>
                                <li><code class="text-pink">POST admin/berkas_lampiran/upload</code></li>
                                <li><code class="text-pink">POST admin/berkas_lampiran/delete/(:num)</code></li>
                                <li><code class="text-pink">POST admin/berkas_lampiran/uploadProfilPhoto</code></li>
                                <li><code class="text-pink">POST admin/berkas_lampiran/deleteProfilPhoto/(:any)/(:num)</code></li>
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
