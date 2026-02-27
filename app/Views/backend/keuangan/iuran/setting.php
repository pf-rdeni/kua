<?php $this->extend('backend/template/template'); ?>
<?php $this->section('content'); ?>

<!-- Action Header -->
<div class="row mb-3">
    <div class="col-12 text-right">
        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalTambahIuran">
            <i class="fas fa-plus mr-1"></i>Tambah Jenis Iuran
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
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0"><?php foreach ((array)session()->getFlashdata('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<!-- Statistik -->
<div class="row mb-3">
    <div class="col-md-4">
        <div class="info-box shadow-sm"><span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
            <div class="info-box-content"><span class="info-box-text">Total Anggota Aktif</span><span class="info-box-number"><?= $jumlahAnggota ?></span></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box shadow-sm"><span class="info-box-icon bg-success"><i class="fas fa-list-ul"></i></span>
            <div class="info-box-content"><span class="info-box-text">Jenis Iuran Terdaftar</span><span class="info-box-number"><?= count($iuranList) ?></span></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box shadow-sm"><span class="info-box-icon bg-warning"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Iuran Aktif</span>
                <span class="info-box-number"><?= count(array_filter($iuranList, fn($i) => $i['is_active'])) ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Setting Iuran -->
<div class="card shadow-sm">
    <div class="card-header py-2"><h6 class="mb-0"><i class="fas fa-table mr-1 text-secondary"></i>Daftar Jenis Iuran</h6></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabel-iuran" class="table table-striped table-bordered table-sm" style="width:100%">
                <thead class="thead-light">
                    <tr>
                        <th width="40">#</th>
                        <th>Nama Iuran</th>
                        <th>Periode</th>
                        <th class="text-right">Nominal</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th width="130">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($iuranList)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">Belum ada setting iuran. Klik "+ Tambah Jenis Iuran" untuk memulai.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($iuranList as $i => $iuran): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td class="font-weight-bold"><?= esc($iuran['nama_iuran']) ?></td>
                        <td>
                            <?php
                            $periodeBadge = [
                                'harian'   => ['badge-warning', 'fa-sun', 'Harian'],
                                'mingguan' => ['badge-primary', 'fa-calendar-week', 'Mingguan'],
                                'bulanan'  => ['badge-info', 'fa-calendar-alt', 'Bulanan'],
                                'tahunan'  => ['badge-success', 'fa-calendar', 'Tahunan'],
                                'sekali'   => ['badge-secondary', 'fa-dot-circle', 'Sekali'],
                            ];
                            $pb = $periodeBadge[$iuran['periode']] ?? ['badge-secondary', 'fa-circle', $iuran['periode']];
                            ?>
                            <span class="badge <?= $pb[0] ?>"><i class="fas <?= $pb[1] ?> mr-1"></i><?= $pb[2] ?></span>
                        </td>
                        <td class="text-right font-weight-bold">Rp <?= number_format($iuran['nominal'], 0, ',', '.') ?></td>
                        <td><?= date('d M Y', strtotime($iuran['tanggal_mulai'])) ?></td>
                        <td><?= $iuran['tanggal_selesai'] ? date('d M Y', strtotime($iuran['tanggal_selesai'])) : '<span class="text-muted">Tidak ada batas</span>' ?></td>
                        <td>
                            <span class="badge <?= $iuran['is_active'] ? 'badge-success' : 'badge-secondary' ?>">
                                <?= $iuran['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </td>
                        <td><?= esc($iuran['keterangan'] ?? '-') ?></td>
                        <td>
                            <!-- Tombol Lihat Anggota -->
                            <a href="<?= base_url('admin/keuangan/iuran/' . $entitasType . '/anggota/' . $iuran['id']) ?>"
                               class="btn btn-xs btn-info mb-1" title="Laporan Anggota">
                                <i class="fas fa-users"></i> Anggota
                            </a>
                            <!-- Tombol Edit (buka modal) -->
                            <button class="btn btn-xs btn-warning mb-1 btn-edit-iuran"
                                    data-id="<?= $iuran['id'] ?>"
                                    data-nama="<?= esc($iuran['nama_iuran']) ?>"
                                    data-periode="<?= $iuran['periode'] ?>"
                                    data-nominal="<?= $iuran['nominal'] ?>"
                                    data-mulai="<?= $iuran['tanggal_mulai'] ?>"
                                    data-selesai="<?= $iuran['tanggal_selesai'] ?? '' ?>"
                                    data-keterangan="<?= esc($iuran['keterangan'] ?? '') ?>"
                                    data-active="<?= $iuran['is_active'] ?>"
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <!-- Tombol Hapus -->
                            <form method="POST" action="<?= base_url('admin/keuangan/iuran/' . $entitasType . '/delete-setting/' . $iuran['id']) ?>"
                                  class="d-inline" onsubmit="return confirm('Hapus setting iuran ini? Data tidak dapat dikembalikan jika sudah ada pembayaran yang terkait!')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-xs btn-danger mb-1" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Iuran -->
<div class="modal fade" id="modalTambahIuran" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title"><i class="fas fa-plus mr-1"></i>Tambah Jenis Iuran</h6>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="<?= base_url('admin/keuangan/iuran/' . $entitasType . '/store-setting') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <?php echo view('backend/keuangan/iuran/_form_iuran', ['iuran' => null]) ?>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Batal</button>
                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save mr-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Iuran -->
<div class="modal fade" id="modalEditIuran" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning py-2">
                <h6 class="modal-title"><i class="fas fa-edit mr-1"></i>Edit Jenis Iuran</h6>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="form-edit-iuran" action="">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <?php echo view('backend/keuangan/iuran/_form_iuran', ['iuran' => 'edit']) ?>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Batal</button>
                    <button type="submit" class="btn btn-sm btn-warning"><i class="fas fa-save mr-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
$(function() {
    // Inisialisasi DataTables
    $('#tabel-iuran').DataTable({
        responsive: true,
        pageLength: 25,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excelHtml5', text: '<i class="fas fa-file-excel mr-1"></i>Excel', className: 'btn btn-sm btn-success', exportOptions: {columns: ':not(:last-child)'} },
            { extend: 'pdfHtml5',   text: '<i class="fas fa-file-pdf mr-1"></i>PDF',   className: 'btn btn-sm btn-danger', exportOptions: {columns: ':not(:last-child)'} }
        ]
    });

    // Isi modal Edit dengan data dari tombol
    $('.btn-edit-iuran').on('click', function () {
        var baseUrl = '<?= base_url('admin/keuangan/iuran/' . $entitasType . '/update-setting/') ?>';
        var id       = $(this).data('id');
        $('#form-edit-iuran').attr('action', baseUrl + id);
        $('#edit_nama_iuran').val($(this).data('nama'));
        $('#edit_periode').val($(this).data('periode'));
        $('#edit_nominal').val($(this).data('nominal'));
        $('#edit_tanggal_mulai').val($(this).data('mulai'));
        $('#edit_tanggal_selesai').val($(this).data('selesai'));
        $('#edit_keterangan').val($(this).data('keterangan'));
        $('#edit_is_active').val($(this).data('active'));
        $('#modalEditIuran').modal('show');
    });
});
</script>
<?php $this->endSection(); ?>
