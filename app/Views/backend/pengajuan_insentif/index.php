<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<?php
$pageTitle = 'Pengajuan Insentif - ' . $entitasConfig['nama_label'];
$breadcrumb = [
    ['title' => 'Home', 'url' => 'admin/dashboard'],
    ['title' => $entitasConfig['nama_label'], 'url' => 'admin/personil/' . $entitasType],
    ['title' => 'Pengajuan Insentif', 'url' => ''],
];
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>Pengajuan Insentif — <?= esc($entitasConfig['nama_label']) ?>
                </h3>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm" id="tabelInsentif">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>NIK / Nama</th>
                                <th style="width: 130px; text-align: center;">Profil</th>
                                <?php foreach ($settingBerkas as $sb): ?>
                                    <th style="width: 100px; text-align: center;"><?= esc($sb['nama_berkas']) ?></th>
                                <?php endforeach; ?>
                                <th style="width: 280px; text-align: center;">Cetak Surat</th>
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
                                        <!-- Foto Profil -->
                                        <td class="text-center" style="vertical-align: middle;">
                                            <?php if ($fotoUrl): ?>
                                                <img src="<?= $fotoThumbUrl ?>" alt="Profil" class="img-thumbnail"
                                                     style="max-width: 60px; max-height: 80px; object-fit: cover;">
                                            <?php else: ?>
                                                <span class="text-muted"><i class="fas fa-user fa-2x"></i></span>
                                            <?php endif; ?>
                                        </td>
                                        <!-- Status Berkas (ikon centang/silang) -->
                                        <?php foreach ($settingBerkas as $sb): ?>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <?php if (isset($berkas[$sb['nama_berkas']])): ?>
                                                    <span class="text-success"><i class="fas fa-check-circle fa-lg"></i></span>
                                                <?php else: ?>
                                                    <span class="text-danger"><i class="fas fa-times-circle fa-lg"></i></span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <!-- Tombol Cetak -->
                                        <td class="text-center" style="vertical-align: middle;">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?= base_url('admin/pengajuan-insentif/' . $entitasType . '/cetak-asn/' . $p['id']) ?>"
                                                   target="_blank" class="btn btn-outline-primary" title="Surat Pernyataan ASN">
                                                    <i class="fas fa-file-pdf"></i> ASN
                                                </a>
                                                <a href="<?= base_url('admin/pengajuan-insentif/' . $entitasType . '/cetak-insentif/' . $p['id']) ?>"
                                                   target="_blank" class="btn btn-outline-success" title="Surat Pernyataan Insentif">
                                                    <i class="fas fa-file-pdf"></i> Insentif
                                                </a>
                                                <a href="<?= base_url('admin/pengajuan-insentif/' . $entitasType . '/cetak-rekomendasi/' . $p['id']) ?>"
                                                   target="_blank" class="btn btn-outline-warning" title="Surat Rekomendasi">
                                                    <i class="fas fa-file-pdf"></i> Rekom
                                                </a>
                                                <a href="<?= base_url('admin/pengajuan-insentif/' . $entitasType . '/cetak-lampiran/' . $p['id']) ?>"
                                                   target="_blank" class="btn btn-outline-info" title="Lampiran Berkas">
                                                    <i class="fas fa-file-image"></i> Lamp
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <?php $colspan = 4 + count($settingBerkas); ?>
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

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    $('#tabelInsentif').DataTable({
        pageLength: 25,
        lengthChange: true,
        ordering: true,
        searching: true,
    });
});
</script>
<?= $this->endSection(); ?>
