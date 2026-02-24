<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<section class="content">
    <div class="container-fluid">
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Panduan Mahakarya: Pengaturan Entitas Dinamis</h3>
            </div>
            <div class="card-body">
                <div class="callout callout-info">
                    <h5><i class="fas fa-info-circle mr-2"></i>Tentang Halaman Ini</h5>
                    <p>
                        <em>Alhamdulillah</em>, arsitektur KUA System ini telah disuntikkan kemampuan "Penciptaan Mandiri". 
                        Halaman panduan ini menuntun Admin dalam merakit form biodata baru untuk profesi/komunitas apapun (misal: "Guru Ngaji", "Amil Zakat") 
                        tanpa perlu merepotkan *programmer* untuk *coding* ulang database, rute, atau tampilan dasbornya.
                    </p>
                </div>

                <h4 class="mt-4"><i class="fas fa-magic text-purple mr-2"></i>1. Keajaiban Master Entitas (`admin/entitas-type`)</h4>
                <p>
                    Saat Anda menciptakan satu buah Master Entitas baru di menu <strong><i class="fas fa-list-ul"></i> Pengaturan Entitas</strong>, sistem seketika akan menyulap dan membukakan jalan secara otomatis untuk hal-hal berikut:
                </p>
                <ul>
                    <li class="mb-2"><strong>Navigasi Sidebar Ajaib:</strong> Menu baru (Lengkap dengan Sub-Menu Data, Lampiran, & Insentif) akan langsung tumbuh di *Sidebar* kiri sesuai nama dan ikon yang Anda pilih.</li>
                    <li class="mb-2"><strong>Form Biodata Presisi:</strong> Kolom isian data (seperti Pendidikan, NPWP, Foto Profile, dsb) dapat dimatikan/dinyalakan untuk jenis entitas tersebut. Contoh: Entitas <em>Marbot Masjid</em> mungkin tidak wajib mengisi Pendidikan, tapi <em>Mubaligh</em> wajib.</li>
                    <li class="mb-2"><strong>Gerbang Akses (Role Base):</strong> Hanya akun dengan hak akses (*Role*) yang Anda izinkan yang bisa melihat dan mengubah data personil entitas tersebut.</li>
                </ul>

                <h4 class="mt-5"><i class="fas fa-project-diagram text-primary mr-2"></i>2. Alur Visual: Penambahan Entitas & Pembangkitan Sistem</h4>
                <p>
                    Diagram ini mendemonstrasikan bagaimana satu kali klik <em>Simpan</em> dapat merombak wajah keseluruhan *Dashboard* Admin KUA.
                </p>

                <pre class="mermaid text-center mt-4 bg-transparent border-0" style="white-space: pre-wrap;">
flowchart TD
    A([Admin Masuk ke Pengaturan Entitas]) --> B[Klik Tambah Master Baru]
    B --> C[Isi Nama, Kode Unik, dan Pilih Ikon]
    C --> D[Pilih Grup/Role yang Boleh Mengakses]
    D --> E[Checklist Kolom Wajib Form: NPWP, Foto, dsb]
    E --> F[Pilih Status - Aktif]
    F --> G[Klik Simpan]
    
    %% Magic Effects
    G --> H[[Tersimpan di tbl_entitas_type]]
    
    H --> I(Pembangkitan Menu)
    H --> J(Pembangkitan Rute)
    H --> K(Pembangkitan Form Input)
    
    I -.->|Sidebar Terbarui| L([Muncul Folder Baru di Navigasi Kiri])
    J -.->|Validasi Koding| M([URL admin/personil/kode_baru Aktif])
    K -.->|View Merespons| N([Tampilan Form Menyesuaikan Checklist Admin])
                </pre>

            </div>
            <div class="card-footer">
                <a href="<?= base_url('admin/entitas-type') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-right mr-1"></i> Langsung Menuju Halaman Pengaturan Entitas
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
                            <ul class="text-sm">
                                <li><code class="text-pink">EntitasTypeController.php</code> - *Controller* mandor untuk memanajemen baris konfigurasi (CRUD) di Master Entitas.</li>
                                <li><code class="text-pink">PersonilController.php</code> - Otak dinamis yang mencegat Rute <code>/personil/(:segment)</code>, lalu mengubah rupa <code>view('backend/personil/index')</code> berdasarkan <code>$entitasType->show_npwp</code> dkk.</li>
                                <li><code class="text-pink">Sidebar Builder</code> - Di dalam <code>template/sidebar.php</code>, terdapat *looping* otomatis yang membaca <code>akses_groups</code> dari pengguna yang sedang *login*, memastikan menu dirender hanya untuk *Role* yang diizinkan.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-warning" style="border-left-color: #ffc107;">
                            <h5>Database & Fields</h5>
                            <ul class="text-sm">
                                <li><code class="text-pink">tbl_entitas_type</code> - Tabel Master dengan puluhan struktur <code>TINYINT(1)</code> seperti <code>show_pendidikan</code>, <code>show_foto</code>, hingga kolom ajaib <code>akses_groups</code> yang berisi *JSON* Array daftar nama *Group* (Role) `Myth\Auth` (Contoh: <code>["SuperAdmin", "Admin"]</code>).</li>
                                <li><code class="text-pink">tbl_personil</code> - Menggunakan teknik *Polymorphic / Universal Table*. Kolom <code>entitas_type</code> diisi nilai kode string (misal: <code>"imam_masjid"</code>) untuk mengisolasi *query* data tanpa repot membuat tabel baru.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="callout callout-success" style="border-left-color: #28a745;">
                            <h5>Views & Client Libraries</h5>
                            <ul class="text-sm">
                                <li><code class="text-pink">backend/personil/form.php</code> - Dibangun dengan pola blok `if ($entitasType['show_...']) { render_input() }` berlapis yang elegan.</li>
                                <li><code class="text-pink">backend/template/sidebar.php</code> - Mengandung sintaks <code>EntitasTypeModel->getAccessibleForUser($userGroups)</code> untuk me-load hierarki Sub-Menu Lampiran & Insentif secara masal dan dinamis.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-danger" style="border-left-color: #dc3545;">
                            <h5>Routes</h5>
                            <p class="text-sm">Bukan di-*hardcode*, melainkan disedot via <code class="text-pink">(:segment)</code> dinamis di <code>Routes.php</code>:</p>
                            <ul class="text-sm">
                                <li><code class="text-pink">admin/personil/(:segment)</code></li>
                                <li><code class="text-pink">admin/personil/(:segment)/berkas-lampiran</code></li>
                                <li><code class="text-pink">admin/pengajuan-insentif/(:segment)</code></li>
                                <li><code class="text-pink">admin/dokumentasi/setting-entitas</code> (Halaman ini)</li>
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
