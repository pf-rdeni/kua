<?= $this->extend('frontend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="breadcrumbs">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Visi & Misi</h2>
      <ol>
        <li><a href="<?= base_url() ?>">Beranda</a></li>
        <li>Profil</li>
        <li>Visi Misi</li>
      </ol>
    </div>
  </div>
</section>

<section class="inner-page">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <h3>Visi</h3>
        <blockquote class="blockquote">
            <p class="mb-0">"Terwujudnya Masyarakat yang Taat Beragama, Rukun, Cerdas, dan Sejahtera Lahir Batin dalam rangka mewujudkan Indonesia yang Berdaulat, Mandiri, dan Berkepribadian Berlandaskan Gotong Royong."</p>
        </blockquote>

        <h3 class="mt-4">Misi</h3>
        <ol>
            <li>Meningkatkan kualitas kesalehan umat beragama.</li>
            <li>Memperkuat moderasi beragama dan kerukunan umat beragama.</li>
            <li>Meningkatkan layanan keagamaan yang adil, mudah dan merata.</li>
            <li>Meningkatkan pelayanan nikah dan rujuk yang berkualitas.</li>
            <li>Meningkatkan pemberdayaan ekonomi umat dan kelembagaan keagamaan.</li>
            <li>Mewujudkan tata kelola pemerintahan yang bersih, melayani, dan akuntabel.</li>
        </ol>
      </div>
    </div>
  </div>
</section>
<?= $this->endSection(); ?>
