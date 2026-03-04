<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<div class="row">
    <div class="col-12">

        <?php if (session()->has('success')) : ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fas fa-check"></i> <?= session('success') ?>
        </div>
        <?php endif; ?>

        <?php if (session()->has('error')) : ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fas fa-ban"></i> <?= session('error') ?>
        </div>
        <?php endif; ?>

        <?php
            $iconMap = [
                'masjid_mushola' => 'fa-mosque',
                'majelis_taklim' => 'fa-chalkboard-teacher',
                'mubaligh'       => 'fa-user-tie'
            ];
            $icon = $iconMap[$entitasType] ?? 'fa-building';
        ?>

        <!-- Info Entitas -->
        <div class="card card-primary card-outline mb-3">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center">
                    <i class="fas <?= $icon ?> fa-2x text-primary mr-3"></i>
                    <div>
                        <h5 class="mb-0"><?= esc($namaEntitas) ?></h5>
                        <small class="text-muted">
                            <?php if ($entitasType === 'masjid_mushola'): ?>
                                <span class="badge badge-<?= $entitas['jenis'] === 'Masjid' ? 'success' : 'info' ?>">
                                    <?= esc($entitas['jenis']) ?>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($entitas['nia'])): ?>
                                NIA: <?= esc($entitas['nia']) ?>
                            <?php elseif (!empty($entitas['alamat'])): ?>
                                <?= esc($entitas['alamat']) ?>
                            <?php endif; ?>
                        </small>
                    </div>
                    <div class="ml-auto">
                        <a href="<?= base_url($cfg['url_base']) ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu User Operator -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-cog mr-2"></i>User Akun - <?= esc($cfg['title']) ?>
                </h3>
                <?php if (empty($usersDetail)): ?>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahUser">
                        <i class="fas fa-plus mr-1"></i> Buat User Akun
                    </button>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body">

                <div class="callout callout-info mb-3">
                    <h5><i class="fas fa-info-circle"></i> Informasi</h5>
                    <p class="mb-1">
                        User akun ini digunakan untuk akses login entitas terkait.
                        Sistem saat ini menyarankan <strong>1 user akun aktif</strong> per entitas.
                    </p>
                    <?php if (!empty($usersDetail)): ?>
                    <p class="mb-0">
                        <strong>Password default:</strong> <code><?= esc($defaultPw) ?></code>
                        – Operator disarankan segera mengganti password setelah login pertama.
                    </p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($usersDetail)): ?>
                <!-- Tabel daftar user -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role / Grup</th>
                                <th>Status</th>
                                <th>Dibuat</th>
                                <th style="width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($usersDetail as $detail): ?>
                            <?php $user = $detail['user']; ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><code><?= esc($user->username) ?></code></strong>
                                </td>
                                <td><?= esc($user->email) ?></td>
                                <td>
                                    <?php if (!empty($detail['groups'])): ?>
                                        <?php foreach ($detail['groups'] as $grp): ?>
                                            <span class="badge badge-info"><?= esc($grp['name']) ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size: 85%;">No Role</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user->active): ?>
                                        <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i>Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?= date('d M Y', strtotime($user->created_at)) ?></small>
                                </td>
                                <td>
                                    <?php
                                        // Build URL base
                                        $actionBaseUrl = base_url($cfg['url_users']) . '/' . $entitasId . '/users';
                                    ?>
                                    <!-- Tombol Reset Password -->
                                    <button type="button"
                                        class="btn btn-warning btn-xs btn-reset-pw"
                                        data-url="<?= $actionBaseUrl . '/reset-password/' . $user->id ?>"
                                        data-username="<?= esc($user->username) ?>"
                                        title="Reset Password ke Default">
                                        <i class="fas fa-key"></i> Reset PW
                                    </button>

                                    <!-- Tombol Toggle Aktif -->
                                    <button type="button"
                                        class="btn btn-<?= $user->active ? 'secondary' : 'success' ?> btn-xs btn-toggle-active"
                                        data-url="<?= $actionBaseUrl . '/toggle-active/' . $user->id ?>"
                                        data-status="<?= $user->active ? '1' : '0' ?>"
                                        title="<?= $user->active ? 'Nonaktifkan' : 'Aktifkan' ?> User">
                                        <i class="fas fa-<?= $user->active ? 'ban' : 'check' ?>"></i>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <?php if (user_id() != $user->id): ?>
                                    <button type="button"
                                        class="btn btn-danger btn-xs btn-hapus-user"
                                        data-url="<?= $actionBaseUrl . '/delete/' . $user->id ?>"
                                        data-username="<?= esc($user->username) ?>"
                                        title="Hapus User">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <!-- Belum ada user -->
                <div class="text-center py-4">
                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada user akun terhubung.</p>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahUser">
                        <i class="fas fa-plus mr-1"></i> Buat User Akun
                    </button>
                </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" role="dialog" aria-labelledby="modalTambahUserLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php
                $storeUrl = base_url($cfg['url_users']) . '/' . $entitasId . '/users/store';
            ?>
            <form action="<?= $storeUrl ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="modalTambahUserLabel">
                        <i class="fas fa-user-plus mr-2"></i>Buat User Akun
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="callout callout-success mb-3">
                        <h6><i class="fas fa-magic mr-1"></i> Username Otomatis</h6>
                        <p class="mb-1">Username akan di-generate otomatis berdasarkan nama.</p>
                        <p class="mb-0">
                            Contoh: <strong><?= esc($namaEntitas) ?></strong>
                            → <code><?= esc((new \App\Models\UserModel())->generateUsernameForEntitas(
                                $entitasType,
                                $namaEntitas,
                                $entitasId
                            )) ?></code>
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope mr-1"></i>Email
                            <small class="text-muted">(opsional)</small>
                        </label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Kosongkan jika tidak ada">
                        <small class="form-text text-muted">Jika dikosongkan, akan dibuat email dummy otomatis.</small>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-key mr-1"></i>Password Default</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            </div>
                            <input type="text" class="form-control bg-light" value="<?= esc($defaultPw) ?>" readonly>
                        </div>
                        <small class="form-text text-warning">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Sampaikan password ini ke pemilik akun (Harap diganti setelah login pertama).
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Buat User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Forms for actions -->
<form id="formResetPw" method="POST" style="display:none;"><?= csrf_field() ?></form>
<form id="formToggleActive" method="POST" style="display:none;"><?= csrf_field() ?></form>
<form id="formHapusUser" method="POST" style="display:none;"><?= csrf_field() ?></form>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {

    $(document).on('click', '.btn-reset-pw', function() {
        var username  = $(this).data('username');
        var actionUrl = $(this).data('url');

        Swal.fire({
            icon: 'warning', title: 'Reset Password?',
            html: 'Password user <strong>' + username + '</strong> akan direset.<br><code><?= esc($defaultPw) ?></code>',
            showCancelButton: true, confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal', confirmButtonColor:'#ffc107',
        }).then(function(result) {
            if (result.isConfirmed) $('#formResetPw').attr('action', actionUrl).submit();
        });
    });

    $(document).on('click', '.btn-toggle-active', function() {
        var status    = $(this).data('status'); 
        var actionUrl = $(this).data('url');
        var pesanKonfirm = status == 1 
            ? 'User akan <strong>dinonaktifkan</strong>. Akun tidak akan bisa login.'
            : 'User akan <strong>diaktifkan</strong> kembali.';

        Swal.fire({
            icon: 'question', title: 'Ubah Status User?',
            html: pesanKonfirm,
            showCancelButton: true, confirmButtonText: 'Ya, Ubah!',
            cancelButtonText: 'Batal',
        }).then(function(result) {
            if (result.isConfirmed) $('#formToggleActive').attr('action', actionUrl).submit();
        });
    });

    $(document).on('click', '.btn-hapus-user', function() {
        var username  = $(this).data('username');
        var actionUrl = $(this).data('url');

        Swal.fire({
            icon: 'error', title: 'Hapus User?',
            html: 'User <strong>' + username + '</strong> akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.',
            showCancelButton: true, confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal', confirmButtonColor:'#dc3545',
        }).then(function(result) {
            if (result.isConfirmed) $('#formHapusUser').attr('action', actionUrl).submit();
        });
    });

});
</script>
<?= $this->endSection(); ?>
