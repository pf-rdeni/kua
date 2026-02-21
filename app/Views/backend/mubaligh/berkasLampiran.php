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
                                <?php foreach ($settingBerkas as $sb): ?>
                                    <th style="width: 160px; text-align: center;"><?= esc($sb['nama_berkas']) ?></th>
                                <?php endforeach; ?>
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
                                    
                                    // Use thumbnail if available
                                    $fotoThumbUrl = $fotoUrl;
                                    if (!empty($m['foto']) && file_exists(FCPATH . 'uploads/mubaligh/thumbs/' . $m['foto'])) {
                                        $fotoThumbUrl = base_url('uploads/mubaligh/thumbs/' . $m['foto']);
                                    }
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
                                                    <img src="<?= $fotoThumbUrl ?>" alt="Profil" class="img-thumbnail preview-image"
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
                                        <!-- Berkas Dinamis -->
                                        <?php foreach ($settingBerkas as $sb): ?>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <?php 
                                                    $nb = $sb['nama_berkas']; 
                                                    $w = $sb['aspect_ratio_width'] ? $sb['aspect_ratio_width'] : 'null';
                                                    $h = $sb['aspect_ratio_height'] ? $sb['aspect_ratio_height'] : 'null';
                                                ?>
                                                <?php if (isset($berkas[$nb])): ?>
                                                    <?php 
                                                        $fileBerkas = $berkas[$nb]; 
                                                        $berkasFileName = $fileBerkas['nama_file'];
                                                        $berkasFullUrl = base_url('uploads/berkas/' . $berkasFileName);
                                                        
                                                        // Use thumbnail if available
                                                        $berkasThumbUrl = $berkasFullUrl;
                                                        if (file_exists(FCPATH . 'uploads/berkas/thumbs/' . $berkasFileName)) {
                                                            $berkasThumbUrl = base_url('uploads/berkas/thumbs/' . $berkasFileName);
                                                        }
                                                    ?>
                                                    <div class="mb-2" style="display: flex; justify-content: center; align-items: center; height: 100px;">
                                                        <img src="<?= $berkasThumbUrl ?>" alt="<?= $nb ?>"
                                                             class="img-thumbnail preview-image shadow-sm"
                                                             style="max-width: 130px; max-height: 100%; cursor: pointer; object-fit: contain; padding: 2px;"
                                                             data-image-url="<?= $berkasFullUrl ?>">
                                                    </div>
                                                    <div>
                                                        <button type="button" class="btn btn-warning btn-xs" title="Edit <?= $nb ?>"
                                                                onclick="berkasHelper.editBerkas(<?= $fileBerkas['id'] ?>, <?= $m['id_mubaligh'] ?>, '<?= esc($m['nama_lengkap'], 'js') ?>', '<?= $nb ?>', <?= $w ?>, <?= $h ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-xs" title="Hapus <?= $nb ?>"
                                                                onclick="berkasHelper.deleteBerkas(<?= $fileBerkas['id'] ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                                            onclick="berkasHelper.openUploadModal(<?= $m['id_mubaligh'] ?>, '<?= esc($m['nama_lengkap'], 'js') ?>', '<?= $nb ?>', <?= $w ?>, <?= $h ?>)">
                                                        <i class="fas fa-upload"></i> Upload
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <?php $colspan = 3 + count($settingBerkas); ?>
                                    <td colspan="<?= $colspan ?>" class="text-center text-muted">Belum ada data mubaligh.</td>
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
$tipeBerkasArr = !empty($settingBerkas) ? array_column($settingBerkas, 'nama_berkas') : [];
$berkasConfig = [
    'entitasType'  => 'mubaligh',
    'tipeBerkas'   => $tipeBerkasArr,
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
        tipeBerkas: <?= json_encode($tipeBerkasArr) ?>,
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
