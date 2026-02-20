<?= $this->extend('frontend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="breadcrumbs">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Persyaratan Nikah</h2>
      <ol>
        <li><a href="<?= base_url() ?>">Beranda</a></li>
        <li>Layanan</li>
        <li>Nikah</li>
      </ol>
    </div>
  </div>
</section>

<section class="inner-page">
  <div class="container">
     <div class="alert alert-info">
        <h5><i class="bi bi-info-circle"></i> Informasi Penting</h5>
        Pendaftaran Nikah dapat dilakukan secara online melalui <strong>SIMKAH (Sistem Informasi Manajemen Nikah)</strong> di <a href="https://simkah.kemenag.go.id" target="_blank" class="alert-link">simkah.kemenag.go.id</a> atau datang langsung ke KUA.
     </div>

    <h3>Dokumen Persyaratan Nikah</h3>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-success text-white">Bagi Calon Suami & Istri</div>
                <div class="card-body">
                    <ul>
                        <li>Surat Pengantar Nikah dari Kelurahan/Desa (N1).</li>
                        <li>Fotokopi KTP, KK, Akta Kelahiran/Ijazah terakhir.</li>
                        <li>Pas foto latar biru ukuran 2x3 (4 lembar) dan 4x6 (2 lembar).</li>
                        <li>Surat Rekomendasi Nikah dari KUA asal (jika calon pengantin dari luar kecamatan).</li>
                        <li>Surat Izin Orang Tua (N5) jika usia di bawah 21 tahun.</li>
                        <li>Surat Dispensasi Pengadilan Agama jika usia di bawah 19 tahun.</li>
                        <li>Surat Akta Cerai (jika duda/janda cerai hidup).</li>
                        <li>Surat N6 (Kematian Suami/Istri) jika duda/janda cerai mati.</li>
                        <li>Surat Izin Komandan (bagi anggota TNI/Polri).</li>
                        <li>Surat Izin Poligami dari Pengadilan Agama (bagi yang hendak beristri lebih dari satu).</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-success text-white">Biaya Nikah</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Nikah di KUA (Jam Kerja)
                            <span class="badge bg-primary rounded-pill">Rp 0,- (Gratis)</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Nikah di Luar KUA / Luar Jam Kerja
                            <span class="badge bg-danger rounded-pill">Rp 600.000,-</span>
                        </li>
                    </ul>
                    <p class="mt-3 small text-muted">* Disetor langsung ke Kas Negara melalui Bank Persepsi (via Kode Billing simponi).</p>
                </div>
            </div>
        </div>
    </div>
  </div>
</section>
<?= $this->endSection(); ?>
