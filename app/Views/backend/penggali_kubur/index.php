<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-hard-hat mr-2"></i>Data Petugas Penggali Kubur
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/penggali-kubur/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Search Form -->
                <form action="<?= base_url('admin/penggali-kubur') ?>" method="get" class="mb-3">
                    <div class="input-group input-group-sm" style="max-width: 350px;">
                        <input type="text" name="keyword" class="form-control" placeholder="Cari Nama/Masjid..." value="<?= esc($keyword ?? '') ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if (!empty($keyword)): ?>
                            <a href="<?= base_url('admin/penggali-kubur') ?>" class="btn btn-default" title="Reset">
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
                                <th>Nama Lengkap</th>
                                <th>Masjid/Mushola</th>
                                <th>Status</th>
                                <th>No. HP</th>
                                <th style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pkList)): ?>
                                <?php $no = 1 + (10 * ($pager->getCurrentPage('penggali_kubur') - 1)); ?>
                                <?php foreach ($pkList as $pk): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($pk['nama']) ?></td>
                                    <td><?= esc($pk['nama_masjid']) ?></td>
                                    <td><?= esc($pk['status']) ?></td>
                                    <td><?= esc($pk['no_hp'] ?? '-') ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/penggali-kubur/' . $pk['id_penggali_kubur']) ?>" class="btn btn-info btn-xs" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/penggali-kubur/edit/' . $pk['id_penggali_kubur']) ?>" class="btn btn-warning btn-xs" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/penggali-kubur/delete/' . $pk['id_penggali_kubur']) ?>" class="btn btn-danger btn-xs" onclick="return confirm('Apakah Anda yakin?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada data petugas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    <?= $pager->links('penggali_kubur', 'default_full') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
