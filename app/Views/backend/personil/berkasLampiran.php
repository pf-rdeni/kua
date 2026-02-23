<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<?php
$pageTitle = 'Berkas Lampiran ' . $entitasConfig['nama_label'];
$breadcrumb = [
    ['title' => 'Home', 'url' => 'admin/dashboard'],
    ['title' => $entitasConfig['nama_label'], 'url' => 'admin/personil/' . $entitasType],
    ['title' => 'Berkas Lampiran', 'url' => ''],
];
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-image mr-2"></i>Berkas Lampiran <?= esc($entitasConfig['nama_label']) ?>
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
                            <?php if (!empty($personilWithBerkas)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($personilWithBerkas as $item): ?>
                                    <?php
                                    $p = $item['personil'];
                                    $berkas = $item['berkas'];
                                    $fotoUrl = !empty($p['foto']) ? base_url('uploads/personil/' . $p['foto']) : null;
                                    
                                    // Use thumbnail if available
                                    $fotoThumbUrl = $fotoUrl;
                                    if (!empty($p['foto']) && file_exists(FCPATH . 'uploads/personil/thumbs/' . $p['foto'])) {
                                        $fotoThumbUrl = base_url('uploads/personil/thumbs/' . $p['foto']);
                                    }
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <div><strong><?= esc($p['nama_lengkap']) ?></strong></div>
                                            <small class="text-muted"><?= esc($p['nik'] ?? '-') ?></small>
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
                                                            onclick="berkasHelper.editProfil(<?= $p['id'] ?>, '<?= esc($p['nama_lengkap'], 'js') ?>', '<?= $fotoUrl ?>')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-xs" title="Hapus Profil"
                                                            onclick="berkasHelper.deleteProfil(<?= $p['id'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                        onclick="berkasHelper.openUploadProfilModal(<?= $p['id'] ?>, '<?= esc($p['nama_lengkap'], 'js') ?>')">
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
                                                    $isRek = $sb['is_rekening'] ? 1 : 0;
                                                    $rekDigit = $sb['rekening_digit'] ? $sb['rekening_digit'] : 'null';
                                                    
                                                    $existingRekening = '';
                                                    if ($isRek && !empty($p['rekening_bank'])) {
                                                        $rekArr = json_decode($p['rekening_bank'], true);
                                                        if (is_array($rekArr) && isset($rekArr[$nb])) {
                                                            $existingRekening = $rekArr[$nb];
                                                        }
                                                    }
                                                ?>
                                                <?php if (isset($berkas[$nb])): ?>
                                                    <?php 
                                                        $fileBerkas = $berkas[$nb]; 
                                                        $berkasFileName = $fileBerkas['nama_file'];
                                                        $berkasFullUrl = base_url('uploads/berkas/' . $berkasFileName);
                                                        
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
                                                                onclick="berkasHelper.editBerkas(<?= $fileBerkas['id'] ?>, <?= $p['id'] ?>, '<?= esc($p['nama_lengkap'], 'js') ?>', '<?= $nb ?>', <?= $w ?>, <?= $h ?>, <?= $isRek ?>, <?= $rekDigit ?>, '<?= esc($existingRekening, 'js') ?>')">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-xs" title="Hapus <?= $nb ?>"
                                                                onclick="berkasHelper.deleteBerkas(<?= $fileBerkas['id'] ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                                            onclick="berkasHelper.openUploadModal(<?= $p['id'] ?>, '<?= esc($p['nama_lengkap'], 'js') ?>', '<?= $nb ?>', <?= $w ?>, <?= $h ?>, <?= $isRek ?>, <?= $rekDigit ?>)">
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
                                    <td colspan="<?= $colspan ?>" class="text-center text-muted">Belum ada data <?= esc($entitasConfig['nama_label']) ?>.</td>
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
    'entitasType'  => $entitasType,
    'tipeBerkas'   => $tipeBerkasArr,
    'labelEntitas' => $entitasConfig['nama_label'],
];
?>
<?= $this->include('backend/partials/_berkas_helper') ?>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- Berkas Helper JS -->
<script src="<?= base_url('assets/js/berkas-helper.js') ?>"></script>
<script>
    // Initialize BerkasHelper with dynamic entitas type
    const berkasHelper = new BerkasHelper({
        entitasType: '<?= $entitasType ?>',
        tipeBerkas: <?= json_encode($tipeBerkasArr) ?>,
        uploadUrl: baseUrl + '/admin/berkas/upload',
        deleteUrl: baseUrl + '/admin/berkas/delete',
        getUrl: baseUrl + '/admin/berkas/get',
        profilUrl: baseUrl + '/admin/berkas/upload-profil',
        deleteProfilUrl: baseUrl + '/admin/berkas/delete-profil',
        berkasFileUrl: baseUrl + '/uploads/berkas/',
        profilDir: 'uploads/personil',
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
