<?= $this->extend('frontend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="breadcrumbs">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Layanan Rujuk</h2>
      <ol>
        <li><a href="<?= base_url() ?>">Beranda</a></li>
        <li>Layanan</li>
        <li>Rujuk</li>
      </ol>
    </div>
  </div>
</section>

<section class="inner-page">
  <div class="container">
    <h3>Persyaratan Rujuk</h3>
    <p>Rujuk adalah kembalinya suami kepada istri yang ditalak raj'i dalam masa iddah. Berikut persyaratannya:</p>
    <ul>
        <li>Suami yang merujuk dan istri yang dirujuk harus hadir.</li>
        <li>Membawa Buku Nikah asli suami dan istri.</li>
        <li>Membawa KTP dan KK suami istri (Asli & Fotokopi).</li>
        <li>Membawa 2 orang saksi laki-laki dewasa (muslim).</li>
        <li>Surat Keterangan dari Kelurahan/Desa.</li>
        <li>Masih dalam masa Iddah Talak Raj'i.</li>
    </ul>
    
    <div class="alert alert-warning mt-3">
        <strong>Catatan:</strong> Jika masa iddah telah habis, maka harus melalui prosedur akad nikah baru.
    </div>
  </div>
</section>
<?= $this->endSection(); ?>
