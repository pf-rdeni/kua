<?php $this->extend('backend/template/template'); ?>
<?php $this->section('content'); ?>

<!-- Action Header -->
<div class="row mb-3">
    <div class="col-12 text-right">
        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalTambahKas">
            <i class="fas fa-plus mr-1"></i>Tambah Kas
        </button>
    </div>
</div>

<!-- Alert -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle mr-1"></i><?= session()->getFlashdata('success') ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle mr-1"></i><?= session()->getFlashdata('error') ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show"><ul class="mb-0"><?php foreach ((array)session()->getFlashdata('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
<?php endif; ?>

<!-- Daftar Kas -->
<div class="row">
    <?php if (empty($kasList)): ?>
    <div class="col-12">
        <div class="alert alert-info"><i class="fas fa-info-circle mr-1"></i>Belum ada kas. Klik "Tambah Kas" untuk membuat kas baru.</div>
    </div>
    <?php endif; ?>

    <?php foreach ($kasList as $kas): ?>
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <strong><i class="fas fa-wallet text-primary mr-1"></i><?= esc($kas['nama_kas']) ?></strong>
                <span class="badge <?= $kas['is_active'] ? 'badge-success' : 'badge-secondary' ?>"><?= $kas['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
            </div>
            <div class="card-body py-2">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Saldo Awal</small>
                    <small>Rp <?= number_format($kas['saldo_awal'], 0, ',', '.') ?></small>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted font-weight-bold">Saldo Berjalan</small>
                    <strong class="<?= $kas['saldo_berjalan'] >= 0 ? 'text-success' : 'text-danger' ?>">
                        Rp <?= number_format($kas['saldo_berjalan'], 0, ',', '.') ?>
                    </strong>
                </div>
                <?php if ($kas['keterangan']): ?>
                <small class="text-muted d-block mt-1"><i class="fas fa-info-circle mr-1"></i><?= esc($kas['keterangan']) ?></small>
                <?php endif; ?>
            </div>
            <div class="card-footer py-2 text-right">
                <button class="btn btn-xs btn-warning btn-edit-kas"
                        data-id="<?= $kas['id'] ?>"
                        data-nama="<?= esc($kas['nama_kas']) ?>"
                        data-saldo="<?= $kas['saldo_awal'] ?>"
                        data-ket="<?= esc($kas['keterangan'] ?? '') ?>"
                        data-entitas-id="<?= $kas['entitas_id'] ?? '' ?>"
                        title="Edit Kas">
                    <i class="fas fa-edit mr-1"></i>Edit
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal Tambah Kas -->
<div class="modal fade" id="modalTambahKas" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title"><i class="fas fa-plus mr-1"></i>Tambah Kas Baru</h6>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="<?= base_url('admin/keuangan/kas/' . $entitasType . '/store') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-sm">Nama Kas <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kas" class="form-control form-control-sm" placeholder="Misal: Kas Masjid Al-Falah" required>
                    </div>
                    <?php if ($entitasType === 'masjid_mushola' && !empty($masjidList)): ?>
                    <div class="form-group">
                        <label class="font-weight-bold text-sm">Masjid/Mushola</label>
                        <select name="entitas_id" class="form-control form-control-sm">
                            <option value="">— Umum (tidak terkait masjid spesifik) —</option>
                            <?php foreach ($masjidList as $m): ?>
                            <option value="<?= $m['id_masjid_mushola'] ?>"><?= esc($m['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label class="font-weight-bold text-sm">Saldo Awal (Rp)</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" name="saldo_awal" class="form-control form-control-sm" value="0" min="0" step="100">
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-sm">Keterangan</label>
                        <textarea name="keterangan" class="form-control form-control-sm" rows="2" placeholder="Opsional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save mr-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kas -->
<div class="modal fade" id="modalEditKas" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning py-2">
                <h6 class="modal-title"><i class="fas fa-edit mr-1"></i>Edit Kas</h6>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="form-edit-kas" action="">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-sm">Nama Kas <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kas" id="edit_nama_kas" class="form-control form-control-sm" required>
                    </div>
                    <?php if ($entitasType === 'masjid_mushola' && !empty($masjidList)): ?>
                    <div class="form-group">
                        <label class="font-weight-bold text-sm">Masjid/Mushola</label>
                        <select name="entitas_id" id="edit_entitas_id" class="form-control form-control-sm">
                            <option value="">— Umum —</option>
                            <?php foreach ($masjidList as $m): ?>
                            <option value="<?= $m['id_masjid_mushola'] ?>"><?= esc($m['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="form-group mb-3">
                            <label class="font-weight-bold text-sm">Update Saldo Saat Ini <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                <input type="number" name="saldo_saat_ini" id="edit_saldo" class="form-control form-control-sm" step="any" required>
                            </div>
                        </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-sm">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-warning"><i class="fas fa-save mr-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
$(function () {
    // Isi modal Edit Kas
    $('.btn-edit-kas').on('click', function () {
        var baseUrl = '<?= base_url('admin/keuangan/kas/' . $entitasType . '/update/') ?>';
        $('#form-edit-kas').attr('action', baseUrl + $(this).data('id'));
        $('#edit_nama_kas').val($(this).data('nama'));
        $('#edit_saldo_awal').val($(this).data('saldo'));
        $('#edit_keterangan').val($(this).data('ket'));
        <?php if ($entitasType === 'masjid_mushola'): ?>
        $('#edit_entitas_id').val($(this).data('entitas-id'));
        <?php endif; ?>
        $('#modalEditKas').modal('show');
    });
});
</script>
<?php $this->endSection(); ?>
