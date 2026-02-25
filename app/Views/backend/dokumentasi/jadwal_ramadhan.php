<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Cara Penggunaan: Modul Jadwal Ramadhan</h3>
            </div>
            <div class="card-body">
                <p>Panduan lengkap siklus pengelolaan <b>Matriks Jadwal Ramadhan</b> dan <b>Tema Ceramah</b>.</p>

                <h4 class="mt-4">1. Manajemen Tema Ceramah</h4>
                <ul>
                    <li>Arahkan ke menu <b>Jadwal Ramadhan</b> &gt; <b>Tema Ceramah</b>.</li>
                    <li>Sistem otomatis membuat 30 baris/hari berdasarkan tahun berjalan (contoh: 1445 H).</li>
                    <li>Ketikkan tema di input teks lalu klik tombol <b>Simpan</b>.</li>
                    <li>Gunakan tombol <b>Duplikat Tema</b> untuk menyalin seluruh daftar tema ke tahun berikutnya.</li>
                </ul>

                <h4 class="mt-4">2. Manajemen Matriks Jadwal Masjid</h4>
                <ul>
                    <li>Arahkan ke menu <b>Jadwal Ramadhan</b> &gt; <b>Matriks Jadwal</b>.</li>
                    <li>Pilih Tahun dan Tanggal 1 Ramadhan Masehi di bagian <i>header</i> halaman untuk men-generate tanggal yang sesuai.</li>
                    <li>Klik <i>dropdown</i> (Select2) pada sel manapun (perpotongan Masjid dan Hari Ke-) untuk mencari nama Mubaligh.</li>
                    <li>Ketika nama Mubaligh dipilih, data otomatis tersimpan (AJAX Autoshave) tanpa memuat ulang (<i>refresh</i>) halaman.</li>
                    <li>Gunakan fitur <b>Duplikat Jadwal</b> untuk menyalin/mengkopi seluruh <i>plot</i> matriks jamaah dari suatu tahun ke tahun lain.</li>
                    <li>Gunakan tombol <b>Reset Jadwal</b> secara hati-hati bila ingin mengosongkan keseluruhan blok matriks.</li>
                </ul>
                
                <h4 class="mt-4">3. Cetak, Export & Warning</h4>
                <ul>
                    <li>Untuk mencetak keseluruhan data menjadi arsip format <i>spreadsheet</i>, tekan <b>Export Excel</b>.</li>
                    <li>Gunakan tombol <b>Cetak Jadwal Mubaligh</b> untuk membuat format print A4 berisi list penugasan 1 individu secara spesifik.</li>
                    <li>Gunakan tombol <b>Cetak Jadwal Masjid</b> untuk mendownload/print lembar format 30 hari ceramah per 1 Masjid.</li>
                </ul>

                <h4 class="mt-4">4. Reminder Otomatis</h4>
                <p>Sistem akan secara cerdas memunculkan daftar pengingat ("Reminder Jadwal Penceramah Terdekat") di halaman <b>Dashboard Utama</b> jika terdapat rekam jadwal untuk <i>Hari Ini</i> atau <i>Esok Hari</i>. Menu reminder sudah dilengkapi fitur cetak otomatis (Kirim Blast WA) yang akan me-_redirect_ ke Whatsapp Web / App dilengkapi format chat yang siap dikirim.</p>

                <h4 class="mt-4 text-center">Flowchart & Proses Alur Data</h4>
                <pre class="mermaid text-center mt-3 bg-transparent border-0" style="white-space: pre-wrap;">
graph TD
    A["Buka Matriks Jadwal"] --> B["Sistem Load Layout Tabel xHari / yMasjid"]
    B --> C["User Pilih Cell Hari-X di Masjid-Y"]
    C --> D{"AJAX Fetch Pencarian Mubaligh"}
    D --> E["Mubaligh &gt; Query ke Tabel Personil Aktif"]
    E --> F{"Filter Data Duplicate / Bentrok"}
    F -->|"Sudah ada di Cell lain pd Hari sama"| G["Nama Tidak Muncul"]
    F -->|"Available"| H["Nama Terpilih"]
    H --> I["Trigger Auto-Save Ke Tabel jadwal_kegiatan"]
    I --> J["Render Avatar / Thumbnail pada tabel"]
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
                            <ul class="text-sm">
                                <li><code class="text-pink">JadwalRamadhanController.php</code> - Controller utama yang mengatur seluruh <i>backend logic</i>.</li>
                                <li><code class="text-pink">index()</code> & <code class="text-pink">tema()</code> - Bertugas merender matrix builder dan editor tema harian.</li>
                                <li><code class="text-pink">save_cell()</code> & <code class="text-pink">search_mubaligh()</code> - Merupakan API Endpoint interaktif (AJAX) bagi <i>Select2</i>, dilengkapi dengan validasi anti-bentrok.</li>
                                <li><code class="text-pink">duplicate_tema()</code>, <code class="text-pink">duplicate_jadwal()</code> & <code class="text-pink">reset_jadwal()</code> - Modul Eksekutor Operasi Massal.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-warning" style="border-left-color: #ffc107;">
                            <h5>Database & Fields</h5>
                            <ul class="text-sm">
                                <li><code class="text-pink">tbl_tema_ceramah</code> - Menyimpan meta teks tema harian, diidentifikasi oleh relasi <code>tahun_hijriah</code> & <code>hari_ke</code>.</li>
                                <li><code class="text-pink">tbl_jadwal_kegiatan</code> - Inti matriks relasi Masjid dan Personil (Mubaligh). Disertai <i>field</i> <code>jenis_kegiatan</code> ('ramadhan') untuk filter lanjut.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="callout callout-success" style="border-left-color: #28a745;">
                            <h5>Views & Client Libraries</h5>
                            <ul class="text-sm">
                                <li><code class="text-pink">backend/jadwal_ramadhan/index.php</code> - Tampilan tabel Matriks Raksasa (30 Hari) yang memanfaatkan <code>Select2 (AJAX Mode)</code> untuk dropdown pencarian asinkron.</li>
                                <li><code class="text-pink">backend/jadwal_ramadhan/tema.php</code> - Form CRUD yang dipadukan dengan Modal <i>SweetAlert2</i> untuk Interaksi.</li>
                                <li><code class="text-pink">backend/jadwal_ramadhan/print_*.php</code> - View HTML murni yang di-tune secara spesifik untuk mode <i>Print-Out Format Kertas A4</i>.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-danger" style="border-left-color: #dc3545;">
                            <h5>Routes</h5>
                            <p class="text-sm">Berkumpul dalam <i>Group Route</i> <code class="text-pink">jadwal-ramadhan</code> di <code>Routes.php</code>:</p>
                            <ul class="text-sm">
                                <li><code class="text-pink">admin/jadwal-ramadhan/</code> (Matriks)</li>
                                <li><code class="text-pink">admin/jadwal-ramadhan/tema</code></li>
                                <li><code class="text-pink">admin/jadwal-ramadhan/cetak-mubaligh/(:num)</code></li>
                                <li><code class="text-pink">admin/jadwal-ramadhan/save-cell</code> (POST)</li>
                                <li>... dan Rute POST / GET pendukung lainnya.</li>
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
