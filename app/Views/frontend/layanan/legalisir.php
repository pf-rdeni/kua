<?= $this->extend('frontend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="breadcrumbs">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Legalisir Dokumen</h2>
      <ol>
        <li><a href="<?= base_url() ?>">Beranda</a></li>
        <li>Layanan</li>
        <li>Legalisir</li>
      </ol>
    </div>
  </div>
</section>

<section class="inner-page">
  <div class="container">
    <h3>Legalisir Buku Nikah</h3>
    <p>Layanan legalisir fotokopi Buku Nikah dapat dilakukan di KUA tempat nikah dicatatkan.</p>
    
    <h4>Persyaratan:</h4>
    <ul>
        <li>Membawa Buku Nikah Asli (Suami & Istri).</li>
        <li>Fotokopi Buku Nikah yang akan dilegalisir (Maksimal 5 lembar).</li>
        <li>KTP Pemohon.</li>
    </ul>

    <div class="alert alert-success mt-3">
        Biaya: <strong>Gratis (Rp 0,-)</strong>
    </div>
  </div>
</section>
<?= $this->endSection(); ?>
