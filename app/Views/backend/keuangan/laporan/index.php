<?php $this->extend('backend/template/template'); ?>
<?php $this->section('content'); ?>

<!-- Action Header -->
<div class="row mb-3">
    <div class="col-12 text-right">
        <a href="<?= base_url('admin/keuangan/dashboard') ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>Dashboard Keuangan
        </a>
    </div>
</div>

<!-- Kartu Rekap -->
<div class="row mb-3">
    <div class="col-md-4 mb-2">
        <div class="small-box bg-success mb-0">
            <div class="inner">
                <h4>Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></h4>
                <p>Total Pemasukan</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-down"></i></div>
        </div>
    </div>
    <div class="col-md-4 mb-2">
        <div class="small-box bg-danger mb-0">
            <div class="inner">
                <h4>Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></h4>
                <p>Total Pengeluaran</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-up"></i></div>
        </div>
    </div>
    <div class="col-md-4 mb-2">
        <div class="small-box <?= $totalSaldo >= 0 ? 'bg-primary' : 'bg-warning' ?> mb-0">
            <div class="inner">
                <h4>Rp <?= number_format(abs($totalSaldo), 0, ',', '.') ?></h4>
                <p>Saldo <?= $totalSaldo < 0 ? '(Defisit)' : '' ?></p>
            </div>
            <div class="icon"><i class="fas fa-balance-scale"></i></div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card shadow-sm mb-3">
    <div class="card-header py-2 bg-light"><h6 class="mb-0"><i class="fas fa-filter mr-1"></i>Filter Laporan</h6></div>
    <div class="card-body py-2">
        <form method="GET" action="" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="small font-weight-bold">Entitas</label>
                <select name="entitas_type" class="form-control form-control-sm">
                    <option value="">Semua Entitas</option>
                    <?php foreach ($accessibleEntitas as $et): ?>
                    <option value="<?= $et['kode'] ?>" <?= ($filters['entitas_type'] === $et['kode']) ? 'selected' : '' ?>>
                        <?= esc($et['nama_label']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <label class="small font-weight-bold">Jenis</label>
                <select name="jenis" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    <option value="pemasukan" <?= ($filters['jenis'] === 'pemasukan') ? 'selected' : '' ?>>Pemasukan</option>
                    <option value="pengeluaran" <?= ($filters['jenis'] === 'pengeluaran') ? 'selected' : '' ?>>Pengeluaran</option>
                </select>
            </div>
            <div class="col-auto">
                <label class="small font-weight-bold">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" class="form-control form-control-sm" value="<?= esc($filters['tanggal_dari']) ?>">
            </div>
            <div class="col-auto">
                <label class="small font-weight-bold">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" class="form-control form-control-sm" value="<?= esc($filters['tanggal_sampai']) ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search mr-1"></i>Filter</button>
                <a href="<?= base_url('admin/keuangan/laporan') ?>" class="btn btn-sm btn-secondary ml-1"><i class="fas fa-redo mr-1"></i>Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Laporan -->
<div class="card shadow-sm">
    <div class="card-header py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-table mr-1"></i>Data Transaksi</h6>
        <small class="text-muted"><?= count($transaksiList) ?> transaksi</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabel-laporan" class="table table-striped table-bordered table-sm" style="width:100%">
                <thead class="thead-light">
                    <tr>
                        <th width="40">#</th>
                        <th>Tanggal</th>
                        <th>Entitas</th>
                        <th>Kas</th>
                        <th>Kategori</th>
                        <th>Jenis</th>
                        <th class="text-right">Jumlah (Rp)</th>
                        <th>Keterangan</th>
                        <th>Diinput Oleh</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="bg-light font-weight-bold">
                        <td colspan="6" class="text-right">TOTAL:</td>
                        <td class="text-right">
                            <span class="text-success">+<?= number_format($totalPemasukan, 0, ',', '.') ?></span><br>
                            <span class="text-danger">-<?= number_format($totalPengeluaran, 0, ',', '.') ?></span>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                <tbody>
                    <?php if (empty($transaksiList)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada transaksi untuk filter yang dipilih.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($transaksiList as $i => $t): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td data-order="<?= $t['tanggal_transaksi'] ?>"><?= date('d M Y', strtotime($t['tanggal_transaksi'])) ?></td>
                        <td><span class="badge badge-secondary"><?= esc($t['entitas_type']) ?></span></td>
                        <td><?= esc($t['nama_kas'] ?? '-') ?></td>
                        <td>
                            <?php if (!empty($t['nama_kategori'])): ?>
                            <span class="badge badge-<?= esc($t['warna_badge'] ?? 'secondary') ?>"><?= esc($t['nama_kategori']) ?></span>
                            <?php else: ?>
                            <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($t['jenis'] === 'pemasukan'): ?>
                            <span class="badge badge-success"><i class="fas fa-arrow-down mr-1"></i>Masuk</span>
                            <?php else: ?>
                            <span class="badge badge-danger"><i class="fas fa-arrow-up mr-1"></i>Keluar</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right font-weight-bold <?= $t['jenis'] === 'pemasukan' ? 'text-success' : 'text-danger' ?>">
                            <?= $t['jenis'] === 'pemasukan' ? '+' : '-' ?><?= number_format($t['jumlah'], 0, ',', '.') ?>
                        </td>
                        <td><?= esc($t['keterangan'] ?? '-') ?></td>
                        <td><?= esc($t['nama_input'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
$(function() {
    $('#tabel-laporan').DataTable({
        responsive: true,
        pageLength: 50,
        order: [[1, 'desc']],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excelHtml5', text: '<i class="fas fa-file-excel mr-1"></i>Excel', className: 'btn btn-sm btn-success', exportOptions: {columns: ':visible'} },
            { extend: 'pdfHtml5',   text: '<i class="fas fa-file-pdf mr-1"></i>PDF',   className: 'btn btn-sm btn-danger', orientation: 'landscape', pageSize: 'A4', exportOptions: {columns: ':visible'} },
            { extend: 'print',      text: '<i class="fas fa-print mr-1"></i>Print',    className: 'btn btn-sm btn-secondary', exportOptions: {columns: ':visible'} }
        ]
    });
});
</script>
<?php $this->endSection(); ?>
