<?= $this->extend('frontend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="breadcrumbs">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Data Masjid & Mushola</h2>
      <ol>
        <li><a href="<?= base_url() ?>">Beranda</a></li>
        <li>Data</li>
        <li>Masjid & Mushola</li>
      </ol>
    </div>
  </div>
</section>

<section class="inner-page">
  <div class="container">
      <div class="row mb-4">
          <div class="col-md-6">
              <form action="" method="get">
                  <div class="input-group">
                      <input type="text" name="keyword" class="form-control" placeholder="Cari Masjid/Mushola..." value="<?= esc($keyword) ?>">
                      <button class="btn btn-primary" type="submit">Cari</button>
                  </div>
              </form>
          </div>
      </div>

      <div class="row">
          <?php foreach ($masjidList as $masjid) : ?>
          <div class="col-lg-4 col-md-6 d-flex align-items-stretch mb-4">
            <div class="card w-100 shadow-sm">
                <?php if ($masjid['foto'] && file_exists('uploads/masjid_mushola/' . $masjid['foto'])) : ?>
                    <img src="<?= base_url('uploads/masjid_mushola/' . $masjid['foto']) ?>" class="card-img-top" alt="Foto Masjid" style="height: 200px; object-fit: cover;">
                <?php else : ?>
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                    </div>
                <?php endif; ?>
                
              <div class="card-body">
                <h5 class="card-title"><a href="<?= base_url('data/masjid-mushola/' . $masjid['id_masjid_mushola']) ?>"><?= esc($masjid['nama']) ?></a></h5>
                <h6 class="card-subtitle mb-2 text-muted"><?= esc($masjid['tipologi']) ?></h6>
                <p class="card-text small"><i class="bi bi-geo-alt"></i> <?= esc($masjid['alamat']) ?></p>
              </div>
              <div class="card-footer bg-white border-top-0">
                  <a href="<?= base_url('data/masjid-mushola/' . $masjid['id_masjid_mushola']) ?>" class="btn btn-sm btn-outline-primary w-100">Lihat Detail</a>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
          
          <?php if (empty($masjidList)): ?>
              <div class="col-12 text-center">
                  <p>Tidak ada data ditemukan.</p>
              </div>
          <?php endif; ?>
      </div>
      
      <div class="d-flex justify-content-center">
          <?= $pager->links('data', 'default_full') ?>
      </div>
  </div>
</section>
<?= $this->endSection(); ?>
