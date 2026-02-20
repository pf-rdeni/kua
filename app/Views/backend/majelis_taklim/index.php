<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chalkboard-teacher mr-2"></i>Data Majelis Taklim
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/majelis-taklim/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Search Form -->
                <form action="<?= base_url('admin/majelis-taklim') ?>" method="get" class="mb-3">
                    <div class="input-group input-group-sm" style="max-width: 350px;">
                        <input type="text" name="keyword" class="form-control" placeholder="Cari Nama/Pimpinan..." value="<?= esc($keyword ?? '') ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if (!empty($keyword)): ?>
                            <a href="<?= base_url('admin/majelis-taklim') ?>" class="btn btn-default" title="Reset">
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
                                <th>Nama Majelis</th>
                                <th>Pimpinan</th>
                                <th>Hari & Waktu</th>
                                <th>Masjid/Mushola</th>
                                <th>No. HP</th>
                                <th style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($mtList)): ?>
                                <?php $no = 1 + (10 * ($pager->getCurrentPage('majelis_taklim') - 1)); ?>
                                <?php foreach ($mtList as $mt): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($mt['nama_majelis_taklim']) ?></td>
                                    <td><?= esc($mt['pimpinan']) ?></td>
                                    <td><?= esc($mt['hari']) ?> - <?= esc($mt['waktu']) ?></td>
                                    <td><?= esc($mt['nama_masjid']) ?></td>
                                    <td><?= esc($mt['no_hp_pimpinan'] ?? '-') ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/majelis-taklim/' . $mt['id_majelis_taklim']) ?>" class="btn btn-info btn-xs" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/majelis-taklim/edit/' . $mt['id_majelis_taklim']) ?>" class="btn btn-warning btn-xs" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/majelis-taklim/delete/' . $mt['id_majelis_taklim']) ?>" class="btn btn-danger btn-xs" onclick="return confirm('Apakah Anda yakin?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Belum ada data majelis taklim.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    <?= $pager->links('majelis_taklim', 'default_full') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
