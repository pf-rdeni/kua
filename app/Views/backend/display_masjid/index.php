<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <i class="fas fa-exclamation-circle mr-1"></i> <?= session()->getFlashdata('error') ?>
</div>
<?php endif; ?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-tv mr-2"></i>Daftar Display Masjid</h3>
        <div class="card-tools">
            <a href="<?= base_url('admin/display-masjid/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Display
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tabelDisplay" class="table table-bordered table-striped table-hover">
                <thead class="bg-primary text-white">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Display</th>
                        <th>Masjid/Mushola</th>
                        <th width="10%">Template</th>
                        <th width="10%">Orientasi</th>
                        <th width="8%">Status</th>
                        <th width="25%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($displays as $d): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td>
                            <strong><?= esc($d['nama_display']) ?></strong>
                            <?php if (!empty($d['nama_masjid_display'])): ?>
                                <br><small class="text-muted"><?= esc($d['nama_masjid_display']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($d['nama_masjid'] ?? '-') ?></td>
                        <td class="text-center">
                            <?php
                            $badgeClass = [
                                'klasik'   => 'badge-info',
                                'modern'   => 'badge-success',
                                'keuangan' => 'badge-warning',
                            ];
                            $badge = $badgeClass[$d['template_aktif']] ?? 'badge-secondary';
                            ?>
                            <span class="badge <?= $badge ?>"><?= ucfirst($d['template_aktif']) ?></span>
                        </td>
                        <td class="text-center">
                            <?php if ($d['orientasi'] === 'vertikal'): ?>
                                <span class="badge badge-outline badge-dark"><i class="fas fa-mobile-alt mr-1"></i>Vertikal</span>
                            <?php else: ?>
                                <span class="badge badge-outline badge-dark"><i class="fas fa-desktop mr-1"></i>Horizontal</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($d['aktif']): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <!-- Tombol Preview Display -->
                            <a href="<?= base_url('display/' . $d['id']) ?>" target="_blank"
                               class="btn btn-success btn-sm" title="Buka Display Fullscreen">
                                <i class="fas fa-external-link-alt"></i> Preview
                            </a>
                            <!-- Tombol Kelola Konten -->
                            <a href="<?= base_url('admin/display-masjid/konten/' . $d['id']) ?>"
                               class="btn btn-info btn-sm" title="Kelola Konten">
                                <i class="fas fa-images"></i> Konten
                            </a>
                            <!-- Tombol Edit -->
                            <a href="<?= base_url('admin/display-masjid/edit/' . $d['id']) ?>"
                               class="btn btn-warning btn-sm" title="Edit Pengaturan">
                                <i class="fas fa-edit"></i>
                            </a>
                            <!-- Tombol Hapus -->
                            <form action="<?= base_url('admin/display-masjid/delete/' . $d['id']) ?>" method="get"
                                  class="d-inline" onsubmit="return confirm('Yakin hapus display ini beserta seluruh kontennya?')">
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Info Cara Penggunaan -->
<div class="card card-outline card-success collapsed-card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Panduan Penggunaan Display Masjid</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
        </div>
    </div>
    <div class="card-body">
        <ol>
            <li><strong>Tambah Display</strong> - Klik tombol "Tambah Display" untuk membuat konfigurasi display baru untuk sebuah masjid/mushola.</li>
            <li><strong>Pilih Template</strong> - Pilih salah satu dari 3 template: Klasik, Modern, atau Keuangan.</li>
            <li><strong>Atur Orientasi</strong> - Pilih orientasi Horizontal (landscape, default) atau Vertikal (portrait) sesuai posisi TV.</li>
            <li><strong>Kelola Konten</strong> - Tambahkan info kegiatan, gambar slide, pengumuman, laporan keuangan, dll.</li>
            <li><strong>Preview</strong> - Buka display dalam mode fullscreen pada browser. URL ini juga yang digunakan pada TV/mini-PC.</li>
            <li><strong>Offline</strong> - Display akan menyimpan data cache. Jika internet mati, display tetap berjalan dari cache.</li>
        </ol>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Inisialisasi DataTable jika ada data
    $('#tabelDisplay').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
        }
    });
});
</script>
<?= $this->endSection(); ?>
