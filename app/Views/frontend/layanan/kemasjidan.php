<?= $this->extend('frontend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="breadcrumbs">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Layanan Kemasjidan</h2>
      <ol>
        <li><a href="<?= base_url() ?>">Beranda</a></li>
        <li>Layanan</li>
        <li>Kemasjidan</li>
      </ol>
    </div>
  </div>
</section>

<section class="inner-page">
  <div class="container">
    <h3>Pelayanan & Pembinaan Masjid/Mushola</h3>
    <p>KUA melayani:</p>
    <ul>
        <li>Rekomendasi Pendaftaran ID Masjid/Mushola (SIMAS).</li>
        <li>Pengukuran Arah Kiblat.</li>
        <li>Pembinaan Manajemen Masjid.</li>
        <li>Pembinaan Imam dan Khotib.</li>
    </ul>
    
    <h4>Persyaratan ID Masjid (SIMAS):</h4>
    <ol>
        <li>Surat Permohonan dari Takmir Masjid.</li>
        <li>Profil Masjid (Luas, Sejarah Singkat, Foto).</li>
        <li>SK Takmir Masjid.</li>
        <li>Surat Keterangan Domisili Masjid dari Desa/Kelurahan.</li>
        <li>Status Tanah (Sertifikat/Wakaf).</li>
    </ol>
  </div>
</section>
<?= $this->endSection(); ?>
