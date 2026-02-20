<?= $this->extend('frontend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="breadcrumbs">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Tugas Pokok & Fungsi</h2>
      <ol>
        <li><a href="<?= base_url() ?>">Beranda</a></li>
        <li>Profil</li>
        <li>Tupoksi</li>
      </ol>
    </div>
  </div>
</section>

<section class="inner-page">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <h3>Tugas Pokok</h3>
        <p>Berdasarkan Peraturan Menteri Agama (PMA) Nomor 34 Tahun 2016, KUA Kecamatan mempunyai tugas melaksanakan layanan dan bimbingan masyarakat Islam di wilayah kecamatan.</p>

        <h3>Fungsi</h3>
        <ul class="list-check">
            <li><i class="bi bi-check-circle-fill text-success"></i> Pelaksanaan pelayanan, pengawasan, pencatatan, dan pelaporan nikah dan rujuk.</li>
            <li><i class="bi bi-check-circle-fill text-success"></i> Penyusunan statistik layanan dan bimbingan masyarakat Islam.</li>
            <li><i class="bi bi-check-circle-fill text-success"></i> Pengelolaan dokumentasi dan sistem informasi manajemen KUA Kecamatan.</li>
            <li><i class="bi bi-check-circle-fill text-success"></i> Pelayanan bimbingan keluarga sakinah.</li>
            <li><i class="bi bi-check-circle-fill text-success"></i> Pelayanan bimbingan kemasjidan.</li>
            <li><i class="bi bi-check-circle-fill text-success"></i> Pelayanan bimbingan hisab rukyat dan pembinaan syariah.</li>
            <li><i class="bi bi-check-circle-fill text-success"></i> Pelayanan bimbingan dan penerangan Agama Islam.</li>
            <li><i class="bi bi-check-circle-fill text-success"></i> Pelayanan bimbingan zakat dan wakaf.</li>
            <li><i class="bi bi-check-circle-fill text-success"></i> Pelaksanaan ketatausahaan dan kerumahtanggaan KUA Kecamatan.</li>
        </ul>
      </div>
    </div>
  </div>
</section>
<?= $this->endSection(); ?>
