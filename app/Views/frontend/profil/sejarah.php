<?= $this->extend('frontend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="breadcrumbs">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Sejarah</h2>
      <ol>
        <li><a href="<?= base_url() ?>">Beranda</a></li>
        <li>Profil</li>
        <li>Sejarah</li>
      </ol>
    </div>
  </div>
</section>

<section class="inner-page">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <h3>Sejarah KUA Kecamatan</h3>
        <p>
            Kantor Urusan Agama (KUA) Kecamatan merupakan unit kerja terdepan Kementerian Agama yang melaksanakan sebagian tugas pemerintah di bidang agama di tingkat kecamatan.
            Sejarah berdirinya KUA Kecamatan ini tidak terlepas dari perkembangan administrasi keagamaan di wilayah ini.
        </p>
        <p>
            Pada awalnya, pelayanan nikah dan rujuk dilakukan secara sederhana oleh tokoh agama setempat. Seiring dengan tertib administrasi pemerintahan, maka dibentuklah Kantor Urusan Agama
            untuk memberikan pelayanan yang lebih terstruktur, akuntabel, dan transparan kepada masyarakat.
        </p>
        <p>
            Hingga saat ini, KUA Kecamatan terus berbenah meningkatkan kualitas pelayanan publik, sarana dan prasarana, serta sumber daya manusia untuk mewujudkan pelayanan prima yang bersih dan melayani.
        </p>
      </div>
    </div>
  </div>
</section>
<?= $this->endSection(); ?>
