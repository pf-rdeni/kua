<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<section class="content">
    <div class="container-fluid">
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Panduan Pengaturan Berkas & Smart PDF</h3>
            </div>
            <div class="card-body">
                <div class="callout callout-info">
                    <h5><i class="fas fa-info-circle mr-2"></i>Tentang Halaman Ini</h5>
                    <p>
                        <em>Alhamdulillah</em>, sistem KUA ini telah dilengkapi dengan fitur <strong>Pengaturan Berkas Dinamis</strong>. 
                        Halaman panduan ini akan membantu Admin dalam mengonfigurasi syarat-syarat dokumen yang wajib diunggah oleh entitas,
                        serta mengatur tata letak cetakan (resolusi & layout PDF) secara spesifik untuk masing-masing berkas.
                    </p>
                </div>

                <h4 class="mt-4"><i class="fas fa-file-pdf text-danger mr-2"></i>1. Kustomisasi Layout PDF (Smart PDF Layout)</h4>
                <p>
                    Anda diizinkan untuk mengubah bagaimana sebuah file pindaian (scan) akan dirender ke dalam kertas cetak PDF (A4) 
                    secara tanpa batas (*codeless*) langsung melalui menu <strong><i class="fas fa-cog"></i> Pengaturan Berkas</strong>.
                </p>
                <ul>
                    <li class="mb-2"><strong>Format Cetak PDF (Full Page vs Kolase):</strong> Gunakan <em>"Satu Halaman Penuh"</em> untuk dokumen sakral raksasa seperti <strong>Kartu Keluarga</strong> atau <strong>Ijazah</strong>. Sistem secara otomatis akan memutar arahnya (rotasi 90-derajat) jika file aslinya berbentuk memanjang (Landscape) agar rapi mengisi ruang kertas. Gunakan <em>"Gabungkan Sebaris (Kolase)"</em> untuk dokumen kecil seperti <strong>KTP</strong> atau <strong>Pas Foto</strong>.</li>
                    <li class="mb-2"><strong>Ukuran/Lebar Tampil (%):</strong> Jika hasil cetakan *Buku Rekening* atau struk terlalu kurus/kecil, isi kolom ini dengan angka hingga <code>500%</code> untuk mengeraskan *zoom* gambar fisik secara paksa pada kanvas PDF.</li>
                    <li class="mb-2"><strong>Smart Cascade Update:</strong> <em>Insya Allah</em>, jika Anda sewaktu-waktu harus mengganti Nama Berkas master di pengaturan ini (misalnya dari "KK" menjadi "Kartu Keluarga"), sistem di belakang layar akan mendeteksi dan secara serentak mengoreksi seluruh database riwayat unggahan pegawai lama yang terdampak agar tidak ada tautan PDF yang patah.</li>
                </ul>

                <h4 class="mt-5"><i class="fas fa-money-check text-success mr-2"></i>2. Menambahkan Syarat Akun/Buku Bank (Dinamic JSON)</h4>
                <p>
                    Aplikasi ini dirancang cerdas untuk mencegat proses pengunggahan file dan secara seketika (*on-the-fly*) mewajibkan pengguna 
                    untuk mengetikkan nomor rekening mereka ke dalam kotak dialog (SweetAlert) jika berkas tersebut ditandai sebagai "Buku Tabungan".
                </p>
                <ol>
                    <li class="mb-2">Pada form Edit Setting Berkas, aktifkan *toggle* <strong>Fungsi Berkas Khusus: Buku Tabungan / Rekening Bank</strong>.</li>
                    <li class="mb-2">Masukkan nilai ketat <strong>Validasi Panjang Nomor Rekening (Digit)</strong> (Contoh: isikan 10 untuk memaksa pengguna hanya mengetik 10 angka pasti). Kosongkan jika jumlah digit bebastak  terbatas.</li>
                    <li class="mb-2">Data Nomor Rekening yang dimasukkan operator akan secara otomatis dijahit dan dilindungi ke dalam brankas kolom JSON <code>rekening_bank</code> di basis data Personil, bersandingan dengan nama bank dari judul Dokumen tersebut (Contoh: <code>{"Buku Tabungan BPR": "8080xxxx"}</code>).</li>
                </ol>

                <h4 class="mt-5"><i class="fas fa-project-diagram text-primary mr-2"></i>3. Alur Kerja Sinkronisasi (Cascade Update) & Pembuatan Setting Baru</h4>
                <p>
                    Berikut gambaran teknis menyeluruh (Flowchart) saat Admin menambahkan atau memodifikasi atribut wajib dari sebuah dokumen di menu Pengaturan.
                </p>

                <pre class="mermaid text-center mt-4 bg-transparent border-0" style="white-space: pre-wrap;">
flowchart TD
    A([Menu Admin - Pengaturan Berkas]) --> B{Pilih Aksi?}
    
    %% Alur Tambah
    B -->|Klik Tambah| C[Isi Form Nama Target Entitas dan Rasio]
    C --> D{Butuh Rekening?}
    D -->|Ya| E[Set Limit Digit Validasi Bank]
    D -->|Tidak| F[Abaikan]
    E --> G[Atur Konfigurasi Cetak PDF]
    F --> G
    G --> H[Simpan Setting Baru dan Aktifkan]
    
    %% Alur Ubah / Edit
    B -->|Klik Edit| I[Ubah Nama atau Atribut Berkas]
    I --> J{Apakah Nama Master Berubah?}
    J -->|Tidak| K[Perbarui Konfigurasi Cetak dan Rekening]
    J -->|Ya| L[Jalankan Cascade Update]
    L --> M[Buru semua Histori Berkas Upload yang masih pakai nama lama]
    M --> N[Tulis Ulang semua label histori tersebut menjadi Nama Baru]
    N --> O([Tautan antara File dan PDF Tetap Terjaga Valid])
                </pre>

            </div>
            <div class="card-footer">
                <a href="<?= base_url('admin/setting-berkas') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-right mr-1"></i> Langsung Menuju Halaman Setting Berkas
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
                                <li><code class="text-pink">app/Controllers/Backend/SettingBerkasController.php</code> - Mengelola Input Admin, serta meramu logika sakti `Cascade Update` terhadap `tbl_berkas` saat nama diubah.</li>
                                <li><code class="text-pink">app/Controllers/Backend/PengajuanInsentifController.php</code> - Merender array base64 gambar, mengecek dimensi rasio via `getimagesize()`, merotasi otomatis, dan menyuntikkan setting Lebar (%) PDF ke View.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-warning" style="border-left-color: #ffc107;">
                            <h5>Database & Fields</h5>
                            <p class="text-sm">Tabel Pengaturan Cerdas:</p>
                            <ul class="text-sm">
                                <li><code class="text-pink">tbl_setting_berkas</code> - Memiliki dua pasang kolom baru: <code class="text-pink">cetak_tipe</code>, <code class="text-pink">cetak_lebar</code> (untuk PDF layout), serta <code class="text-pink">is_rekening</code>, <code class="text-pink">rekening_digit</code> (untuk cegatan validasi bank).</li>
                                <li><code class="text-pink">tbl_personil.rekening_bank</code> - Kolom JSON TEXT super bebas tak berskema untuk memeluk nilai array berbagai entitas Bank di 1 baris personil.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="callout callout-success" style="border-left-color: #28a745;">
                            <h5>Views & Client Libraries</h5>
                            <ul class="text-sm">
                                <li><code class="text-pink">pdf/lampiran_berkas.php</code> - View cetakan akhir PDF DomPDF yang sudah diselaraskan properti Inline-CSS nya untuk centering presisi.</li>
                                <li><code class="text-pink">berkas-helper.js</code> - Membaca `<button data-is-rekening="1">` lalu mendirikan Intercept Input-Text SweetAlert2 di sisi Client secara anggun tanpa reloading.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-danger" style="border-left-color: #dc3545;">
                            <h5>Routes</h5>
                            <ul class="text-sm">
                                <li><code class="text-pink">GET admin/setting-berkas</code></li>
                                <li><code class="text-pink">GET admin/setting-berkas/create</code></li>
                                <li><code class="text-pink">POST admin/setting-berkas/store</code></li>
                                <li><code class="text-pink">GET admin/pengajuan-insentif/cetak-lampiran/(:segment)/(:num)</code></li>
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
