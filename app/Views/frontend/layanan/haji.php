<?= $this->extend('frontend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="breadcrumbs">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Layanan Haji & Umrah</h2>
      <ol>
        <li><a href="<?= base_url() ?>">Beranda</a></li>
        <li>Layanan</li>
        <li>Haji</li>
      </ol>
    </div>
  </div>
</section>

<section class="inner-page">
  <div class="container">
    <h3>Informasi Haji</h3>
    <p>Layanan pendaftaran Haji Reguler saat ini dilakukan melalui Siskohat di Kantor Kementerian Agama Kabupaten/Kota atau melalui aplikasi Pusaka Super Apps.</p>
    <p>KUA Kecamatan berfungsi sebagai pusat informasi dan bimbingan manasik haji di tingkat kecamatan.</p>
    
    <h4>Layanan di KUA:</h4>
    <ul>
        <li>Konsultasi informasi pendaftaran haji.</li>
        <li>Pelaksanaan Bimbingan Manasik Haji Kecamatan (bagi Jemaah Calon Haji yang akan berangkat).</li>
    </ul>
  </div>
</section>
<?= $this->endSection(); ?>
