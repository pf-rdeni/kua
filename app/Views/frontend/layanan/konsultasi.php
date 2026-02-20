<?= $this->extend('frontend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="breadcrumbs">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Konsultasi Keluarga</h2>
      <ol>
        <li><a href="<?= base_url() ?>">Beranda</a></li>
        <li>Layanan</li>
        <li>Konsultasi</li>
      </ol>
    </div>
  </div>
</section>

<section class="inner-page">
  <div class="container">
    <h3>Badan Penasihatan Pembinaan dan Pelestarian Perkawinan (BP4)</h3>
    <p>KUA menyediakan layanan konsultasi keluarga dan perkawinan untuk membantu masyarakat dalam mengatasi permasalahan rumah tangga dan mewujudkan keluarga Sakinah Mawaddah Warahmah.</p>

    <h4>Jenis Layanan:</h4>
    <ul>
        <li>Konsultasi Pranikah (Bimbingan Perkawinan).</li>
        <li>Konsultasi Keharmonisan Rumah Tangga.</li>
        <li>Mediasi Perselisihan Suami Istri.</li>
    </ul>

    <p>Silakan datang langsung ke KUA pada jam kerja untuk mendapatkan layanan konsultasi ini.</p>
  </div>
</section>
<?= $this->endSection(); ?>
