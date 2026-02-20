<?= $this->extend('frontend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="breadcrumbs">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Struktur Organisasi</h2>
      <ol>
        <li><a href="<?= base_url() ?>">Beranda</a></li>
        <li>Profil</li>
        <li>Struktur Organisasi</li>
      </ol>
    </div>
  </div>
</section>

<section class="inner-page">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 text-center">
        <h3>Struktur Organisasi KUA Kecamatan</h3>
        <p>Berikut adalah struktur organisasi pada Kantor Urusan Agama Kecamatan:</p>
        
        <!-- Placeholder for Structure Image or Chart -->
        <div class="card mt-4">
            <div class="card-body">
                <i class="bi bi-diagram-3" style="font-size: 5rem; color: #5fcf80;"></i>
                <h5 class="mt-3">Kepala KUA</h5>
                <p>Penghulu Madya/Muda</p>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <h6>Penghulu</h6>
                    </div>
                    <div class="col-md-4">
                        <h6>Penyuluh Agama Islam</h6>
                    </div>
                    <div class="col-md-4">
                        <h6>Staf Pelaksana/JFU</h6>
                    </div>
                </div>
            </div>
        </div>
        
      </div>
    </div>
  </div>
</section>
<?= $this->endSection(); ?>
