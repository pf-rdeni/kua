<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<?php
$pageTitle = 'Berkas Lampiran Mubaligh';
$breadcrumb = [
    ['title' => 'Home', 'url' => 'admin/dashboard'],
    ['title' => 'Mubaligh', 'url' => 'admin/mubaligh'],
    ['title' => 'Berkas Lampiran', 'url' => ''],
];
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-image mr-2"></i>Berkas Lampiran Mubaligh
                </h3>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm" id="tabelBerkasLampiran">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>NIK / Nama</th>
                                <th style="width: 130px; text-align: center;">Profil</th>
                                <th style="width: 160px; text-align: center;">KTP</th>
                                <th style="width: 160px; text-align: center;">KK</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($mubalighWithBerkas)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($mubalighWithBerkas as $item): ?>
                                    <?php
                                    $m = $item['mubaligh'];
                                    $berkas = $item['berkas'];
                                    $fotoUrl = !empty($m['foto']) ? base_url('uploads/mubaligh/' . $m['foto']) : null;
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <div><strong><?= esc($m['nama_lengkap']) ?></strong></div>
                                            <small class="text-muted"><?= esc($m['nik'] ?? '-') ?></small>
                                        </td>
                                        <!-- Profil -->
                                        <td class="text-center" style="vertical-align: middle;">
                                            <?php if ($fotoUrl): ?>
                                                <div class="mb-1">
                                                    <img src="<?= $fotoUrl ?>" alt="Profil" class="img-thumbnail preview-image"
                                                         style="max-width: 80px; max-height: 100px; cursor: pointer; object-fit: cover;"
                                                         data-image-url="<?= $fotoUrl ?>">
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-warning btn-xs" title="Edit Profil"
                                                            onclick="berkasHelper.editProfil(<?= $m['id_mubaligh'] ?>, '<?= esc($m['nama_lengkap'], 'js') ?>', '<?= $fotoUrl ?>')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-xs" title="Hapus Profil"
                                                            onclick="berkasHelper.deleteProfil(<?= $m['id_mubaligh'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                        onclick="berkasHelper.openUploadProfilModal(<?= $m['id_mubaligh'] ?>, '<?= esc($m['nama_lengkap'], 'js') ?>')">
                                                    <i class="fas fa-upload"></i> Upload
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                        <!-- KTP -->
                                        <td class="text-center" style="vertical-align: middle;">
                                            <?php if (isset($berkas['KTP'])): ?>
                                                <?php $ktp = $berkas['KTP']; ?>
                                                <div class="mb-1">
                                                    <img src="<?= base_url('uploads/berkas/' . $ktp['nama_file']) ?>" alt="KTP"
                                                         class="img-thumbnail preview-image"
                                                         style="max-width: 120px; max-height: 80px; cursor: pointer; object-fit: cover;"
                                                         data-image-url="<?= base_url('uploads/berkas/' . $ktp['nama_file']) ?>">
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-warning btn-xs" title="Edit KTP"
                                                            onclick="berkasHelper.editBerkas(<?= $ktp['id'] ?>, <?= $m['id_mubaligh'] ?>, '<?= esc($m['nama_lengkap'], 'js') ?>', 'KTP')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-xs" title="Hapus KTP"
                                                            onclick="berkasHelper.deleteBerkas(<?= $ktp['id'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                        onclick="berkasHelper.openUploadModal(<?= $m['id_mubaligh'] ?>, '<?= esc($m['nama_lengkap'], 'js') ?>', 'KTP')">
                                                    <i class="fas fa-upload"></i> Upload
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                        <!-- KK -->
                                        <td class="text-center" style="vertical-align: middle;">
                                            <?php if (isset($berkas['KK'])): ?>
                                                <?php $kk = $berkas['KK']; ?>
                                                <div class="mb-1">
                                                    <img src="<?= base_url('uploads/berkas/' . $kk['nama_file']) ?>" alt="KK"
                                                         class="img-thumbnail preview-image"
                                                         style="max-width: 120px; max-height: 80px; cursor: pointer; object-fit: cover;"
                                                         data-image-url="<?= base_url('uploads/berkas/' . $kk['nama_file']) ?>">
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-warning btn-xs" title="Edit KK"
                                                            onclick="berkasHelper.editBerkas(<?= $kk['id'] ?>, <?= $m['id_mubaligh'] ?>, '<?= esc($m['nama_lengkap'], 'js') ?>', 'KK')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-xs" title="Hapus KK"
                                                            onclick="berkasHelper.deleteBerkas(<?= $kk['id'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                        onclick="berkasHelper.openUploadModal(<?= $m['id_mubaligh'] ?>, '<?= esc($m['nama_lengkap'], 'js') ?>', 'KK')">
                                                    <i class="fas fa-upload"></i> Upload
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada data mubaligh.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Berkas Helper (Modals + CSS + Cropper.js CDN) -->
<?php
$berkasConfig = [
    'entitasType'  => 'mubaligh',
    'tipeBerkas'   => ['KTP', 'KK'],
    'labelEntitas' => 'Mubaligh',
];
?>
<?= $this->include('backend/partials/_berkas_helper') ?>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- Berkas Helper JS -->
<script src="<?= base_url('assets/js/berkas-helper.js') ?>"></script>
<script>
    // Initialize BerkasHelper for Mubaligh
    const berkasHelper = new BerkasHelper({
        entitasType: 'mubaligh',
        tipeBerkas: ['KTP', 'KK'],
        uploadUrl: baseUrl + '/admin/berkas/upload',
        deleteUrl: baseUrl + '/admin/berkas/delete',
        getUrl: baseUrl + '/admin/berkas/get',
        profilUrl: baseUrl + '/admin/berkas/upload-profil',
        deleteProfilUrl: baseUrl + '/admin/berkas/delete-profil',
        berkasFileUrl: baseUrl + '/uploads/berkas/',
        profilDir: 'uploads/mubaligh',
    });

    // Initialize DataTable
    $(document).ready(function() {
        $('#tabelBerkasLampiran').DataTable({
            pageLength: 25,
            lengthChange: true,
            ordering: true,
            searching: true,
        });
    });
</script>
<?= $this->endSection(); ?>
