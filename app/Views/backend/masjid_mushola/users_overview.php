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

        <!-- Statistik ringkas -->
        <?php
            $totalMasjid   = count($masjidList);
            $totalUserAda  = count($userPerMasjid); // Masjid yang sudah punya user
            $totalBelumAda = $totalMasjid - $totalUserAda;
        ?>
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-mosque"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Masjid/Mushola</span>
                        <span class="info-box-number"><?= $totalMasjid ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-user-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Sudah Punya User Operator</span>
                        <span class="info-box-number"><?= $totalUserAda ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-user-slash"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Belum Ada User Operator</span>
                        <span class="info-box-number"><?= $totalBelumAda ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Utama -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users-cog mr-2"></i>Daftar User Operator Masjid &amp; Mushola
                </h3>
            </div>
            <div class="card-body p-0">

                <div class="callout callout-info mx-3 mt-3">
                    <small>
                        <i class="fas fa-info-circle mr-1"></i>
                        Klik tombol <strong><i class="fas fa-user-cog"></i> Kelola User</strong> untuk mengatur user operator tiap masjid secara detail,
                        termasuk reset password dan nonaktifkan akun.
                        Password default: <code><?= esc($defaultPw) ?></code>
                    </small>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm mb-0" id="tabelUserOperator">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Nama Masjid/Mushola</th>
                                <th>Jenis</th>
                                <th>User Operator</th>
                                <th>Status User</th>
                                <th style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($masjidList as $masjid): ?>
                            <?php
                                $mId     = $masjid['id_masjid_mushola'];
                                $users   = $userPerMasjid[$mId] ?? [];
                                $adaUser = !empty($users);
                            ?>
                            <tr class="<?= !$adaUser ? 'table-warning' : '' ?>">
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= esc($masjid['nama']) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= esc(substr($masjid['alamat'] ?? '', 0, 40)) ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $masjid['jenis'] === 'Masjid' ? 'success' : 'info' ?>">
                                        <?= esc($masjid['jenis']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($adaUser): ?>
                                        <?php foreach ($users as $u): ?>
                                        <code class="d-block"><?= esc($u['username']) ?></code>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size: 85%;">
                                            <i class="fas fa-exclamation-circle text-warning mr-1"></i>Belum ada
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($adaUser): ?>
                                        <?php foreach ($users as $u): ?>
                                        <?php if ($u['active']): ?>
                                            <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i>Nonaktif</span>
                                        <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- Tombol Kelola User - selalu tampil (untuk buat jika belum ada, kelola jika sudah ada) -->
                                    <a href="<?= base_url('admin/masjid-mushola/' . $mId . '/users') ?>"
                                       class="btn btn-<?= $adaUser ? 'primary' : 'warning' ?> btn-sm"
                                       title="<?= $adaUser ? 'Kelola User Operator' : 'Buat User Operator' ?>">
                                        <i class="fas fa-<?= $adaUser ? 'user-cog' : 'user-plus' ?> mr-1"></i>
                                        <?= $adaUser ? 'Kelola' : 'Buat User' ?>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->

            </div><!-- /.card-body -->
        </div><!-- /.card -->

    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Inisialisasi DataTable untuk tabel user operator
    $('#tabelUserOperator').DataTable({
        "responsive"   : true,
        "lengthChange" : false,
        "autoWidth"    : false,
        "pageLength"   : 25,
        "language"     : {
            "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
        },
        "columnDefs"   : [
            { "orderable": false, "targets": [-1] }
        ]
    });
});
</script>
<?= $this->endSection(); ?>
