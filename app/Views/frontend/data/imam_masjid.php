<?= $this->extend('frontend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="breadcrumbs">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Data Imam Masjid</h2>
      <ol>
        <li><a href="<?= base_url() ?>">Beranda</a></li>
        <li>Data</li>
        <li>Imam Masjid</li>
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
                      <input type="text" name="keyword" class="form-control" placeholder="Cari Imam/Masjid..." value="<?= esc($keyword) ?>">
                      <button class="btn btn-primary" type="submit">Cari</button>
                  </div>
              </form>
          </div>
      </div>

      <div class="row">
          <?php foreach ($imamList as $imam) : ?>
          <div class="col-lg-3 col-md-4 d-flex align-items-stretch mb-4">
            <div class="card w-100 shadow-sm text-center">
                <div class="card-body">
                   <?php if ($imam['foto'] && file_exists('uploads/personil/' . $imam['foto'])) : ?>
                        <img src="<?= base_url('uploads/personil/' . $imam['foto']) ?>" class="rounded-circle mb-3" alt="Foto" style="width: 100px; height: 100px; object-fit: cover;">
                    <?php else : ?>
                        <img src="<?= base_url('template/backend/dist/img/user2-160x160.jpg') ?>" class="rounded-circle mb-3" alt="No Image" style="width: 100px; height: 100px;">
                    <?php endif; ?>
                    
                    <h5 class="card-title mb-1"><?= esc($imam['nama_lengkap']) ?></h5>
                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill"></i> <?= esc($imam['nama_masjid']) ?></p>
                    <p class="card-text small text-muted"><?= esc($imam['alamat']) ?></p>
                </div>
            </div>
          </div>
          <?php endforeach; ?>
          
          <?php if (empty($imamList)): ?>
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
