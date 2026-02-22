<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>


<section class="content">
    <div class="container-fluid">
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Proses Pengajuan Insentif</h3>
            </div>
            <div class="card-body">
                <p>Diagram alur proses pencetakan Surat Otomatis dari sistem setelah dokumen personil rampung.</p>

                <h4 class="mt-4">1. Dokumen Generatif</h4>
                <ul>
                    <li>Surat Pernyataan ASN</li>
                    <li>Surat Pernyataan Insentif (Kesesediaan)</li>
                    <li>Surat Rekomendasi</li>
                    <li>Merjer Profil Otomatis</li>
                </ul>

                <h4 class="mt-4">2. Siklus Fitur Cetak</h4>
                <pre class="mermaid text-center mt-3 bg-transparent border-0" style="white-space: pre-wrap;">
graph TD
    A["Buka Halaman Pengajuan Insentif"] --> B["Sistem Cek entitas_type di URL"]
    B --> C{"Ambil Data Parameter Wajib dari TBL_SETTING_BERKAS"}
    C --> D["Sistem Scan TBL_BERKAS milik Personil Ybs"]
    D --> E{"Apakah JUMLAH Berkas == JUMLAH Wajib?"}
    E -->|"TIDAK"| F["Link Cetak Dinonaktifkan Merah"]
    E -->|"YA"| H["Trigger Un-disable 4 Tombol Print PDF"]
    H --> I("Eksekusi Dompdf -> Download Surat")
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
                            <p class="text-sm">Controller: <code class="text-pink">app/Controllers/Backend/PengajuanInsentifController.php</code></p>
                            <ul class="text-sm">
                                <li><code class="text-pink">index()</code> - Melayani UI cetak Insentif (Menghitung Total Dokumen Wajib vs Terunggah)</li>
                                <li><code class="text-pink">cetakSuratAsn()</code> - Generasi PDF Surat Pernyataan ASN</li>
                                <li><code class="text-pink">cetakSuratRekomendasi()</code> - Generasi PDF Rekomendasi</li>
                                <li><code class="text-pink">cetakProfil()</code> - Ekspor HTML Foto+KTP Data Diri menjadi Dokumen</li>
                            </ul>
                            <p class="text-sm mt-2">Library Pendukung Terintegrasi: <strong>Dompdf</strong> digunakan untuk merender blade HTML murni menjadi dokumen kertas ukuran F4/A4.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-warning" style="border-left-color: #ffc107;">
                            <h5>Database & Model</h5>
                            <p class="text-sm">Proses Perhitungan Kelayakan (Eligibility Scan):</p>
                            <ul class="text-sm">
                                <li>Meng-query (Count baris) di <code class="text-pink">tbl_setting_berkas</code> bersyarat <code>entitas_type = X AND param_kategori IN ('W', 'O')</code></li>
                                <li>Kemudian membandingkannya langsung dengan hasil hitungan dari <code class="text-pink">BerkasModel->countBerkasPerPersonil()</code></li>
                            </ul>
                            <p class="text-sm mt-3">Helpers: <code class="text-pink">App\Helpers\tanggal_indonesia_helper</code> (Penting dalam formating Tanggal Hijriah/Surat)</p>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="callout callout-success" style="border-left-color: #28a745;">
                            <h5>View File</h5>
                            <ul class="text-sm">
                                <li><code class="text-pink">app/Views/backend/pengajuan_insentif/index.php</code> (Tampilan UI Link)</li>
                                <li><code class="text-pink">app/Views/backend/pengajuan_insentif/pdf/*.php</code> (File template kerangka Kop Surat/Kertas)</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-danger" style="border-left-color: #dc3545;">
                            <h5>Routes</h5>
                            <ul class="text-sm">
                                <li><code class="text-pink">GET admin/pengajuan_insentif/(:any)</code></li>
                                <li><code class="text-pink">GET admin/pengajuan_insentif/cetak_asn/(:any)/(:num)</code></li>
                                <li><code class="text-pink">GET admin/pengajuan_insentif/cetak_kesediaan/(:any)/(:num)</code></li>
                                <li><code class="text-pink">GET admin/pengajuan_insentif/cetak_profil/(:any)/(:num)</code></li>
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
