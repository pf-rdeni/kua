<?= $this->extend('frontend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="breadcrumbs">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Layanan Wakaf</h2>
      <ol>
        <li><a href="<?= base_url() ?>">Beranda</a></li>
        <li>Layanan</li>
        <li>Wakaf</li>
      </ol>
    </div>
  </div>
</section>

<section class="inner-page">
  <div class="container">
    <h3>Akta Ikrar Wakaf (AIW)</h3>
    <p>Kepala KUA bertindak sebagai Pejabat Pembuat Akta Ikrar Wakaf (PPAIW) untuk wilayah kecamatan. Berikut prosedur wakaf tanah:</p>

    <h4>Persyaratan:</h4>
    <ul>
        <li>Waqif (Orang yang mewakafkan) hadir.</li>
        <li>Nazhir (Penerima amanat wakaf) hadir.</li>
        <li>Dua orang saksi.</li>
        <li>Sertifikat Tanah Asli / Surat Kepemilikan yang sah.</li>
        <li>KTP Waqif, Nazhir, dan Saksi.</li>
        <li>Surat Pengantar dari Desa/Kelurahan.</li>
    </ul>
    
    <p>Proses selanjutnya adalah pengukuran dan penerbitan Akta Ikrar Wakaf (AIW) untuk kemudian diproses sertifikat wakaf di BPN.</p>
  </div>
</section>
<?= $this->endSection(); ?>
