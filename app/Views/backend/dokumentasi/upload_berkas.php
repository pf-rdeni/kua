<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<section class="content">
    <div class="container-fluid">
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Panduan Upload Berkas Lampiran</h3>
            </div>
            <div class="card-body">
                <div class="callout callout-info">
                    <h5><i class="fas fa-info-circle mr-2"></i>Tentang Halaman Ini</h5>
                    <p>
                        Panduan ini menjelaskan langkah-langkah bagi admin dan operator untuk <strong>Mengunggah (Upload)</strong> 
                        serta <strong>Mengeksekusi Pas Foto (Crop)</strong> dan berkas lampiran lain (seperti KTP, KK, 
                        Buku Rekening, Ijazah, dll) yang diwajibkan oleh setiap personil.
                    </p>
                </div>

                <h4 class="mt-4"><i class="fas fa-upload text-success mr-2"></i>1. Cara Mengupload Berkas Lampiran</h4>
                <ol>
                    <li class="mb-2">Buka menu data personil (cth: <strong>Data Mubaligh</strong>) melalui sidebar.</li>
                    <li class="mb-2">Cari nama personil yang akan diupload berkasnya, kemudian klik tombol <strong><i class="fas fa-folder-open"></i> Berkas</strong> pada baris tabel tersebut.</li>
                    <li class="mb-2">Sistem akan menampilkan tabel kelengkapan. Kolom yang masih kosong akan memiliki tombol <strong><i class="fas fa-plus"></i> Upload</strong>.</li>
                    <li class="mb-2">Pilih <em>File Gambar (JPG/PNG)</em> atau gunakan kamera/scanner yang tersedia di perangkat Anda.</li>
                    <li class="mb-2">Layar pemotongan gambar <strong>(Crop)</strong> akan tampil. Sesuaikan rasio dan orientasi dokumen, kemudian klik <strong>Crop & Selesai</strong>.</li>
                    <li class="mb-2">Terakhir, konfirmasikan ukuran file dan klik tombol hijau <strong><i class="fas fa-save"></i> Simpan</strong>.</li>
                </ol>

                <h4 class="mt-5"><i class="fas fa-sync text-primary mr-2"></i>2. Fitur Sinkronisasi Cerdas (NIK Ganda)</h4>
                <p>
                    KUA System dilengkapi dengan teknologi penyimpanan cerdas bernama <em>File Sharing Architecture</em>. Jika seorang 
                    personil memiliki NIK yang terdaftar ganda di lebih dari satu tipe entitas (misalnya: sebagai <strong>Penyuluh</strong> 
                    sekaligus <strong>Imam Masjid</strong>), file pribadi seperti <strong>KTP</strong>, <strong>KK</strong>, atau <strong>Pas Foto</strong> 
                    cukup diunggah satu kali saja.
                </p>

                <div class="callout callout-warning">
                    <h5><i class="fas fa-exclamation-triangle mr-2"></i>Pop-up Konfirmasi Sinkronisasi</h5>
                    <p>
                        Jika sistem mendeteksi NIK yang diunggah memiliki profil ganda, sebuah kotak dialog otomatis (SweetAlert) akan 
                        muncul dan bertanya kepada Anda:
                        <br><br>
                        <em>"Apakah file yang Anda upload juga akan disinkronkan ke profil Personil lain yang menggunakan NIK ini secara otomatis?"</em>
                    </p>
                    <ul>
                        <li><strong>Ya, Sinkronkan Semua:</strong> Sistem akan menempelkan file terbaru ini ke seluruh form entitas yang bersangkutan secara massal.</li>
                        <li><strong>Tidak, Hanya Ini Saja:</strong> File hanya akan berlaku untuk entitas yang sedang dibuka halamannya.</li>
                    </ul>
                </div>

                <h4 class="mt-5"><i class="fas fa-user-circle text-info mr-2"></i>3. Mengupload Foto Profil</h4>
                <p>
                    Foto profil memiliki alur yang sedikit berbeda dari berkas lampiran lainnya. Foto Profil wajib memiliki rasio <strong>3:4 (Portrait)</strong>. 
                </p>
                <ul>
                    <li>Untuk menambahkan, tekan tombol <strong><i class="fas fa-upload"></i> Upload</strong> di bawah ilustrasi gambar orang.</li>
                    <li>Sistem otomatis mengunci layar <em>Crop</em> secara memanjang ke bawah (Vertikal). Pastikan wajah personil berada di tengah bingkai.</li>
                </ul>

                <pre class="mermaid text-center mt-4 bg-transparent border-0" style="white-space: pre-wrap;">
flowchart TD
    A([Pilih Pas Foto dari Komputer]) --> B[Modal Cropping Tampil]
    B --> C{Paskan Bingkai 3:4}
    C --> D[Klik Selesai]
    D --> E[Sistem mengubah gambar ke Resolusi Optimal]
    E --> F{Cek NIK Berkembar?}
    F -->|YA| G[Pop-Up Konfirmasi Sinkron Semua]
    F -->|TIDAK| H[Simpan Foto langsung]
    G --> I([Data Tersimpan Kapanpun Diakses])
    H --> I
                </pre>
            </div>
            <div class="card-footer">
                <a href="<?= base_url('admin/personil/mubaligh/berkas-lampiran') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-right mr-1"></i> Langsung Menuju Halaman Berkas (Mubaligh)
                </a>
            </div>
        </div>

        <!-- Informasi Teknis -->
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
                                <li><code class="text-pink">app/Controllers/Backend/BerkasController.php</code> - Menangani logika inti <code class="text-pink">upload()</code> dan <code class="text-pink">uploadProfil()</code> beserta <em>Cross-Entity Broadcasting</em> jika <code class="text-pink">$_POST['sync_all'] == 'true'</code>.</li>
                                <li><code class="text-pink">app/Controllers/Backend/PersonilApiController.php</code> - Endpoint RESTful <code class="text-pink">checkNikSharing()</code> untuk mengidentifikasi akun ganda lintas peran.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-warning" style="border-left-color: #ffc107;">
                            <h5>Database & Auth Tables</h5>
                            <p class="text-sm">Tabel Pengelolaan Berkas:</p>
                            <ul class="text-sm">
                                <li><code class="text-pink">tbl_berkas</code> - Menyimpan data nama file fisik (KTP, KK, Rekening dll) yang bereferensi ke masing-masing personil.</li>
                                <li><code class="text-pink">tbl_setting_berkas</code> - Konfigurasi persyaratan wajib unggah untuk tipe entitas tertentu.</li>
                                <li><code class="text-pink">tbl_personil.foto</code> - Kolom khusus yang tersimpan terpisah memegang tautan (nama) file foto profil personil.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="callout callout-success" style="border-left-color: #28a745;">
                            <h5>View & Frontend Libraries</h5>
                            <ul class="text-sm">
                                <li><code class="text-pink">public/assets/js/berkas-helper.js</code> - Memusatkan semua logika AJAX, trigger tombol, dan manipulasi DOM Upload Berkas.</li>
                                <li><code class="text-pink">Cropper.js</code> - Library HTML5 Canvas untuk memotong (crop) rasio gambar di sisi UI dan mengubahnya ke base64 secara on-the-fly.</li>
                                <li><code class="text-pink">SweetAlert2</code> - Jendela dialog yang menahan (block) antrian HTTP request untuk konfirmasi pengguna "Sinkronisasi Massal File".</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-danger" style="border-left-color: #dc3545;">
                            <h5>Routes</h5>
                            <ul class="text-sm">
                                <li><code class="text-pink">POST admin/berkas/upload</code></li>
                                <li><code class="text-pink">POST admin/berkas/delete/(:num)</code></li>
                                <li><code class="text-pink">GET admin/berkas/get/(:num)</code></li>
                                <li><code class="text-pink">POST admin/berkas/upload-profil</code></li>
                                <li><code class="text-pink">POST admin/berkas/delete-profil</code></li>
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
<script type="module">
    import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
    mermaid.initialize({ startOnLoad: true });

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
