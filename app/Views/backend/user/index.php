<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>




<div class="row">
    <div class="col-12">
        <?php if (session()->has('message')) : ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <?= session('message') ?>
        </div>
        <?php endif ?>
        <?php if (session()->has('error')) : ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <?= session('error') ?>
        </div>
        <?php endif ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users-cog mr-2"></i>Data User
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah User
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm text-nowrap">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($users as $user) : ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= esc($user->email); ?></td>
                                        <td><?= esc($user->username); ?></td>
                                        <td>
                                            <?php 
                                                $groupModel = new \Myth\Auth\Models\GroupModel();
                                                $groups = $groupModel->getGroupsForUser($user->id);

                                                if (!empty($groups)) {
                                                    foreach($groups as $group) {
                                                        echo '<span class="badge badge-info">' . esc($group['name']) . '</span> ';
                                                    }
                                                } else {
                                                    echo '<span class="text-muted" style="font-size: 85%;">No Role</span>';
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?= $user->active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>' ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('admin/users/edit/' . $user->id) ?>" class="btn btn-warning btn-xs" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if (user_id() != $user->id): ?>
                                            <a href="<?= base_url('admin/users/delete/' . $user->id) ?>" class="btn btn-danger btn-xs" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada user.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
