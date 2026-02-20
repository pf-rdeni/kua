<?= $this->extend('frontend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="breadcrumbs">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Data Majelis Taklim</h2>
      <ol>
        <li><a href="<?= base_url() ?>">Beranda</a></li>
        <li>Data</li>
        <li>Majelis Taklim</li>
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
                      <input type="text" name="keyword" class="form-control" placeholder="Cari Majelis Taklim..." value="<?= esc($keyword) ?>">
                      <button class="btn btn-primary" type="submit">Cari</button>
                  </div>
              </form>
          </div>
      </div>

      <div class="row">
          <?php foreach ($mtList as $mt) : ?>
          <div class="col-lg-4 col-md-6 d-flex align-items-stretch mb-4">
            <div class="card w-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                         <?php if ($mt['foto'] && file_exists('uploads/majelis_taklim/' . $mt['foto'])) : ?>
                            <img src="<?= base_url('uploads/majelis_taklim/' . $mt['foto']) ?>" class="rounded mr-3" alt="Foto" style="width: 60px; height: 60px; object-fit: cover;">
                        <?php else : ?>
                            <div class="bg-light rounded mr-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-people text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div>
                            <h5 class="card-title mb-0"><?= esc($mt['nama_majelis_taklim']) ?></h5>
                            <small class="text-muted">Masjid: <?= esc($mt['nama_masjid']) ?></small>
                        </div>
                    </div>
                    <ul class="list-unstyled small">
                        <li><strong>Ketua:</strong> <?= esc($mt['nama_ketua']) ?></li>
                        <li><strong>Jumlah Jamaah:</strong> <?= esc($mt['jumlah_jamaah']) ?></li>
                        <li><strong>Hari/Waktu:</strong> <?= esc($mt['hari']) ?>, <?= esc($mt['waktu']) ?></li>
                    </ul>
                </div>
            </div>
          </div>
          <?php endforeach; ?>
          
          <?php if (empty($mtList)): ?>
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
