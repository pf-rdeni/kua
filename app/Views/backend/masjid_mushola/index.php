<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-mosque mr-2"></i>Data Masjid & Mushola
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/masjid-mushola/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Search Form -->
                <form action="<?= base_url('admin/masjid-mushola') ?>" method="get" class="mb-3">
                    <div class="input-group input-group-sm" style="max-width: 350px;">
                        <input type="text" name="keyword" class="form-control" placeholder="Cari Nama/Alamat/Ketua..." value="<?= esc($keyword ?? '') ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                             <?php if (!empty($keyword)): ?>
                            <a href="<?= base_url('admin/masjid-mushola') ?>" class="btn btn-default" title="Reset">
                                <i class="fas fa-times"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th>Alamat</th>
                                <th>Ketua DKM</th>
                                <th>No. HP</th>
                                <th style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($masjidList)): ?>
                                <?php $no = 1 + (10 * ($pager->getCurrentPage('masjid') - 1)); ?>
                                <?php foreach ($masjidList as $masjid): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($masjid['nama']) ?></td>
                                    <td>
                                        <span class="badge <?= $masjid['jenis'] == 'Masjid' ? 'badge-success' : 'badge-info' ?>">
                                            <?= esc($masjid['jenis']) ?>
                                        </span>
                                    </td>
                                    <td><?= esc(substr($masjid['alamat'], 0, 30)) . (strlen($masjid['alamat']) > 30 ? '...' : '') ?></td>
                                    <td><?= esc($masjid['nama_ketua_dkm']) ?></td>
                                    <td><?= esc($masjid['no_hp_ketua'] ?? '-') ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/masjid-mushola/' . $masjid['id_masjid_mushola']) ?>" class="btn btn-info btn-xs" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/masjid-mushola/edit/' . $masjid['id_masjid_mushola']) ?>" class="btn btn-warning btn-xs" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/masjid-mushola/delete/' . $masjid['id_masjid_mushola']) ?>" class="btn btn-danger btn-xs" onclick="return confirm('Apakah Anda yakin?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Belum ada data masjid/mushola.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    <?= $pager->links('masjid', 'default_full') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
