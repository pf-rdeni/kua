<?= $this->extend('frontend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="breadcrumbs">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Detail Masjid/Mushola</h2>
      <ol>
        <li><a href="<?= base_url() ?>">Beranda</a></li>
        <li><a href="<?= base_url('data/masjid-mushola') ?>">Data Masjid</a></li>
        <li>Detail</li>
      </ol>
    </div>
  </div>
</section>

<section class="inner-page">
  <div class="container">
      <div class="row">
          <div class="col-lg-5">
                <?php if ($masjid['foto'] && file_exists('uploads/masjid_mushola/' . $masjid['foto'])) : ?>
                    <img src="<?= base_url('uploads/masjid_mushola/' . $masjid['foto']) ?>" class="img-fluid rounded shadow" alt="Foto Masjid">
                <?php else : ?>
                    <img src="https://via.placeholder.com/600x400?text=No+Image" class="img-fluid rounded shadow" alt="No Image">
                <?php endif; ?>
          </div>
          <div class="col-lg-7 pt-4 pt-lg-0 content">
              <h3><?= esc($masjid['nama']) ?></h3>
              <p class="fst-italic text-muted"><?= esc($masjid['tipologi']) ?></p>
              
              <table class="table table-striped mt-3">
                  <tr>
                      <th style="width: 30%;">ID Masjid</th>
                      <td><?= esc($masjid['id_masjid']) ?></td>
                  </tr>
                  <tr>
                      <th>Alamat</th>
                      <td><?= esc($masjid['alamat']) ?></td>
                  </tr>
                  <tr>
                      <th>Luas Tanah</th>
                      <td><?= esc($masjid['luas_tanah']) ?> m²</td>
                  </tr>
                   <tr>
                      <th>Luas Bangunan</th>
                      <td><?= esc($masjid['luas_bangunan']) ?> m²</td>
                  </tr>
                   <tr>
                      <th>Status Tanah</th>
                      <td><?= esc($masjid['status_tanah']) ?></td>
                  </tr>
                   <tr>
                      <th>Tahun Berdiri</th>
                      <td><?= esc($masjid['tahun_berdiri']) ?></td>
                  </tr>
              </table>
          </div>
      </div>
  </div>
</section>
<?= $this->endSection(); ?>
