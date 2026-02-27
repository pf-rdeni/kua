<?= $this->extend('frontend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="breadcrumbs">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Data Mubaligh</h2>
      <ol>
        <li><a href="<?= base_url() ?>">Beranda</a></li>
        <li>Data</li>
        <li>Mubaligh</li>
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
                      <input type="text" name="keyword" class="form-control" placeholder="Cari Mubaligh..." value="<?= esc($keyword) ?>">
                      <button class="btn btn-primary" type="submit">Cari</button>
                  </div>
              </form>
          </div>
      </div>

      <div class="row">
          <?php foreach ($mubalighList as $mubaligh) : ?>
          <div class="col-lg-3 col-md-4 d-flex align-items-stretch mb-4">
            <div class="card w-100 shadow-sm text-center">
                <div class="card-body">
                   <?php 
                        $foto = '';
                        if (!empty($mubaligh['foto']) && file_exists(FCPATH . 'uploads/personil/' . $mubaligh['foto'])) {
                            $foto = base_url('uploads/personil/' . $mubaligh['foto']); 
                        } else {
                            $words = explode(' ', trim($mubaligh['nama_lengkap']));
                            $initials = count($words) > 1 ? strtoupper(substr($words[0], 0, 1) . substr($words[count($words)-1], 0, 1)) : strtoupper(substr($words[0], 0, 1));
                            $colors = ['#f56954', '#f39c12', '#00a65a', '#00c0ef', '#3c8dbc', '#605ca8', '#ff851b', '#39cccc'];
                            $bgColor = $colors[crc32($mubaligh['nama_lengkap']) % count($colors)];
                            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><rect width="100" height="100" fill="'.$bgColor.'"/><text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" fill="#ffffff" font-family="Arial, sans-serif" font-size="40" font-weight="bold">'.$initials.'</text></svg>';
                            $foto = 'data:image/svg+xml;base64,' . base64_encode($svg);
                        }
                   ?>
                        <img src="<?= $foto ?>" class="rounded-circle mb-3" alt="Foto" style="width: 100px; height: 100px; object-fit: cover;">
                    
                    <h5 class="card-title mb-1"><?= esc($mubaligh['nama_lengkap']) ?></h5>
                    <p class="text-muted small mb-2"><?= esc($mubaligh['alamat']) ?></p>
                    <p class="card-text small text-muted"><?= esc($mubaligh['no_hp']) ?></p>
                </div>
            </div>
          </div>
          <?php endforeach; ?>
          
          <?php if (empty($mubalighList)): ?>
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
