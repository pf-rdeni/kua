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

<!-- Info Display -->
<div class="callout callout-info">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0"><i class="fas fa-tv mr-2"></i><?= esc($display['nama_display']) ?></h5>
            <small class="text-muted">
                Masjid: <strong><?= esc($display['nama_masjid'] ?? '-') ?></strong> |
                Template: <span class="badge badge-primary"><?= ucfirst($display['template_aktif']) ?></span> |
                Orientasi: <span class="badge badge-dark"><?= ucfirst($display['orientasi']) ?></span>
            </small>
        </div>
        <div>
            <a href="<?= base_url('display/' . $display['id']) ?>" target="_blank" class="btn btn-success btn-sm">
                <i class="fas fa-external-link-alt mr-1"></i> Preview Display
            </a>
            <a href="<?= base_url('admin/display-masjid') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<!-- Tombol Tambah Konten -->
<div class="mb-3">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahKonten">
        <i class="fas fa-plus mr-1"></i> Tambah Konten
    </button>
</div>

<!-- Tabel Konten -->
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-images mr-2"></i>Daftar Konten Display</h3>
    </div>
    <div class="card-body">
        <?php if (empty($kontens)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">Belum ada konten. Klik "Tambah Konten" untuk memulai.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Tipe</th>
                            <th>Judul</th>
                            <th width="10%">Gambar</th>
                            <th width="8%">Urutan</th>
                            <th width="12%">Periode</th>
                            <th width="8%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($kontens as $k): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td>
                                <?php
                                $tipeBadge = [
                                    'info_kegiatan'     => ['Info Kegiatan', 'badge-info'],
                                    'gambar_slide'      => ['Gambar Slide', 'badge-primary'],
                                    'laporan_keuangan'  => ['Lap. Keuangan', 'badge-warning'],
                                    'jadwal_imsyakiyah' => ['Imsyakiyah', 'badge-success'],
                                    'pengumuman'        => ['Pengumuman', 'badge-danger'],
                                ];
                                $tb = $tipeBadge[$k['tipe']] ?? ['Lainnya', 'badge-secondary'];
                                ?>
                                <span class="badge <?= $tb[1] ?>"><?= $tb[0] ?></span>
                            </td>
                            <td>
                                <strong><?= esc($k['judul'] ?? '-') ?></strong>
                                <?php if (!empty($k['konten'])): ?>
                                    <br><small class="text-muted"><?= esc(mb_substr(strip_tags($k['konten']), 0, 80)) ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($k['gambar'])): ?>
                                    <img src="<?= base_url($k['gambar']) ?>" alt="Gambar" class="img-thumbnail" style="max-height: 50px;">
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= $k['urutan'] ?></td>
                            <td class="text-center small">
                                <?php if ($k['tanggal_mulai'] || $k['tanggal_selesai']): ?>
                                    <?= $k['tanggal_mulai'] ? date('d/m/Y', strtotime($k['tanggal_mulai'])) : '∞' ?>
                                    <br>s.d.<br>
                                    <?= $k['tanggal_selesai'] ? date('d/m/Y', strtotime($k['tanggal_selesai'])) : '∞' ?>
                                <?php else: ?>
                                    <span class="text-muted">Selalu</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($k['aktif']): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Off</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <!-- Tombol Edit -->
                                <button type="button" class="btn btn-warning btn-sm btn-edit-konten"
                                        data-id="<?= $k['id'] ?>"
                                        data-tipe="<?= esc($k['tipe']) ?>"
                                        data-judul="<?= esc($k['judul']) ?>"
                                        data-konten="<?= esc($k['konten']) ?>"
                                        data-urutan="<?= $k['urutan'] ?>"
                                        data-aktif="<?= $k['aktif'] ?>"
                                        data-tanggal-mulai="<?= $k['tanggal_mulai'] ?>"
                                        data-tanggal-selesai="<?= $k['tanggal_selesai'] ?>"
                                        title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- Tombol Hapus -->
                                <form action="<?= base_url('admin/display-masjid/konten/' . $display['id'] . '/delete/' . $k['id']) ?>"
                                      method="post" class="d-inline"
                                      onsubmit="return confirm('Yakin hapus konten ini?')">
                                    <?= csrf_field() ?>
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
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tambah Konten -->
<div class="modal fade" id="modalTambahKonten" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?= base_url('admin/display-masjid/konten/' . $display['id'] . '/store') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle mr-2"></i>Tambah Konten</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipe Konten <span class="text-danger">*</span></label>
                                <select name="tipe" class="form-control" required>
                                    <?php foreach ($tipeList as $key => $label): ?>
                                        <option value="<?= $key ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Judul</label>
                                <input type="text" name="judul" class="form-control" placeholder="Judul konten">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Isi Konten</label>
                        <textarea name="konten" class="form-control" rows="4" placeholder="Isi konten (teks/HTML)"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Gambar</label>
                        <div class="custom-file">
                            <input type="file" name="gambar" class="custom-file-input" accept="image/*">
                            <label class="custom-file-label">Pilih file gambar...</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Urutan</label>
                                <input type="number" name="urutan" class="form-control" value="0" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="aktif" class="form-control">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Konten -->
<div class="modal fade" id="modalEditKonten" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formEditKonten" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Konten</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipe Konten <span class="text-danger">*</span></label>
                                <select name="tipe" id="editTipe" class="form-control" required>
                                    <?php foreach ($tipeList as $key => $label): ?>
                                        <option value="<?= $key ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Judul</label>
                                <input type="text" name="judul" id="editJudul" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Isi Konten</label>
                        <textarea name="konten" id="editKonten" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Gambar (Baru)</label>
                        <div class="custom-file">
                            <input type="file" name="gambar" class="custom-file-input" accept="image/*">
                            <label class="custom-file-label">Pilih file gambar baru...</label>
                        </div>
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti gambar.</small>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Urutan</label>
                                <input type="number" name="urutan" id="editUrutan" class="form-control" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="aktif" id="editAktif" class="form-control">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="editTanggalMulai" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="editTanggalSelesai" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i> Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Handler tombol edit konten - isi data ke modal
    $('.btn-edit-konten').on('click', function() {
        var id = $(this).data('id');
        var baseUrl = '<?= base_url('admin/display-masjid/konten/' . $display['id'] . '/update/') ?>';

        // Set action form ke URL update
        $('#formEditKonten').attr('action', baseUrl + id);

        // Isi field modal dengan data dari tombol
        $('#editTipe').val($(this).data('tipe'));
        $('#editJudul').val($(this).data('judul'));
        $('#editKonten').val($(this).data('konten'));
        $('#editUrutan').val($(this).data('urutan'));
        $('#editAktif').val($(this).data('aktif'));
        $('#editTanggalMulai').val($(this).data('tanggal-mulai'));
        $('#editTanggalSelesai').val($(this).data('tanggal-selesai'));

        // Tampilkan modal
        $('#modalEditKonten').modal('show');
    });

    // Update label file input
    $('input[type="file"]').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').text(fileName || 'Pilih file...');
    });
});
</script>
<?= $this->endSection(); ?>
