<?php $this->extend('backend/template/template'); ?>
<?php $this->section('content'); ?>

<!-- Action Header -->
<div class="row mb-3">
    <div class="col-12 text-right">
        <a href="<?= base_url('admin/keuangan/kas/' . $entitasType) ?>" class="btn btn-sm btn-outline-secondary mr-1">
            <i class="fas fa-piggy-bank mr-1"></i>Kelola Kas
        </a>
        <a href="<?= base_url('admin/keuangan/transaksi/' . $entitasType . '/create') ?>" class="btn btn-sm btn-success">
            <i class="fas fa-plus mr-1"></i>Tambah Transaksi
        </a>
    </div>
</div>

<!-- Kartu Rekap Periode -->
<div class="row mb-3">
    <div class="col-lg-4 col-md-6 mb-2">
        <div class="small-box bg-success mb-0">
            <div class="inner">
                <h4>Rp <?= number_format($rekap['total_pemasukan'], 0, ',', '.') ?></h4>
                <p>Total Pemasukan</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-down"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-2">
        <div class="small-box bg-danger mb-0">
            <div class="inner">
                <h4>Rp <?= number_format($rekap['total_pengeluaran'], 0, ',', '.') ?></h4>
                <p>Total Pengeluaran</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-up"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-2">
        <div class="small-box <?= $rekap['saldo'] >= 0 ? 'bg-primary' : 'bg-warning' ?> mb-0">
            <div class="inner">
                <h4>Rp <?= number_format(abs($rekap['saldo']), 0, ',', '.') ?></h4>
                <p>Saldo Bersih <?= $rekap['saldo'] < 0 ? '(Defisit)' : '' ?></p>
            </div>
            <div class="icon"><i class="fas fa-balance-scale"></i></div>
        </div>
    </div>
</div>

<!-- Alert Pesan -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-1"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle mr-1"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<!-- Filter Transaksi -->
<div class="card shadow-sm mb-3">
    <div class="card-header py-2 bg-light">
        <h6 class="mb-0"><i class="fas fa-filter mr-1"></i>Filter Transaksi</h6>
    </div>
    <div class="card-body py-2">
        <form method="GET" action="" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="small font-weight-bold">Jenis</label>
                <select name="jenis" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    <option value="pemasukan" <?= ($filters['jenis'] === 'pemasukan') ? 'selected' : '' ?>>Pemasukan</option>
                    <option value="pengeluaran" <?= ($filters['jenis'] === 'pengeluaran') ? 'selected' : '' ?>>Pengeluaran</option>
                </select>
            </div>
            <div class="col-auto">
                <label class="small font-weight-bold">Bulan</label>
                <select name="bulan" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?= $i ?>" <?= ((int)$filters['bulan'] === $i) ? 'selected' : '' ?>>
                        <?= date('F', mktime(0,0,0,$i,1)) ?>
                    </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-auto">
                <label class="small font-weight-bold">Tahun</label>
                <select name="tahun" class="form-control form-control-sm">
                    <?php foreach ($tahunList as $th): ?>
                    <option value="<?= $th ?>" <?= ((string)$filters['tahun'] === (string)$th) ? 'selected' : '' ?>><?= $th ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($entitasType === 'masjid_mushola' && !empty($masjidList)): ?>
            <div class="col-auto">
                <label class="small font-weight-bold">Masjid/Mushola</label>
                <select name="entitas_id" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    <?php foreach ($masjidList as $m): ?>
                    <option value="<?= $m['id_masjid_mushola'] ?>" <?= ((string)$filterEntitasId === (string)$m['id_masjid_mushola']) ? 'selected' : '' ?>>
                        <?= esc($m['nama']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="col-auto">
                <label class="small font-weight-bold">Kategori</label>
                <select name="id_kategori" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    <?php foreach ($kategoriList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= ((string)$filters['id_kategori'] === (string)$k['id']) ? 'selected' : '' ?>>
                        <?= esc($k['nama_kategori']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search mr-1"></i>Filter</button>
                <a href="<?= base_url('admin/keuangan/transaksi/' . $entitasType) ?>" class="btn btn-sm btn-secondary ml-1"><i class="fas fa-redo mr-1"></i>Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Transaksi dengan DataTables -->
<div class="card shadow-sm">
    <div class="card-header py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-table mr-1 text-secondary"></i>Data Transaksi</h6>
        <small class="text-muted"><?= count($transaksiList) ?> data ditemukan</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabel-transaksi" class="table table-striped table-bordered table-sm" style="width:100%">
                <thead class="thead-light">
                    <tr>
                        <th width="40">#</th>
                        <th>Tanggal</th>
                        <?php if ($entitasType === 'masjid_mushola'): ?>
                        <th>Masjid/Mushola</th>
                        <?php endif; ?>
                        <th>Kas</th>
                        <th>Kategori</th>
                        <th>Jenis</th>
                        <th class="text-right">Jumlah (Rp)</th>
                        <th>Keterangan</th>
                        <th>No. Ref.</th>
                        <th>Bukti</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="bg-light font-weight-bold">
                        <td colspan="<?= $entitasType === 'masjid_mushola' ? 6 : 5 ?>" class="text-right">TOTAL:</td>
                        <td class="text-right">
                            <!-- Pemasukan: --> <span class="text-success">+<?= number_format($rekap['total_pemasukan'], 0, ',', '.') ?></span><br>
                            <!-- Pengeluaran: --> <span class="text-danger">-<?= number_format($rekap['total_pengeluaran'], 0, ',', '.') ?></span>
                        </td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
                <tbody>
                    <?php if (empty($transaksiList)): ?>
                    <tr><td colspan="11" class="text-center text-muted py-4">Belum ada data transaksi untuk filter yang dipilih.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($transaksiList as $i => $t): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td data-order="<?= $t['tanggal_transaksi'] ?>">
                            <?= date('d M Y', strtotime($t['tanggal_transaksi'])) ?>
                        </td>
                        <?php if ($entitasType === 'masjid_mushola'): ?>
                        <td><?= esc($t['nama_masjid'] ?? '-') ?></td>
                        <?php endif; ?>
                        <td><?= esc($t['nama_kas'] ?? '-') ?></td>
                        <td>
                            <?php if (!empty($t['nama_kategori'])): ?>
                            <span class="badge badge-<?= esc($t['warna_badge'] ?? 'secondary') ?>">
                                <?= esc($t['nama_kategori']) ?>
                            </span>
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
                            <?= $t['jenis'] === 'pemasukan' ? '+' : '-' . '' ?>
                            <?= number_format($t['jumlah'], 0, ',', '.') ?>
                        </td>
                        <td><?= esc($t['keterangan'] ?? '-') ?></td>
                        <td><?= esc($t['no_referensi'] ?? '-') ?></td>
                        <td class="text-center">
                            <?php if (!empty($t['bukti'])): ?>
                            <a href="<?= base_url('uploads/keuangan/' . $t['bukti']) ?>" target="_blank" class="btn btn-xs btn-info" title="Lihat Bukti">
                                <i class="fas fa-image"></i>
                            </a>
                            <?php else: ?>
                            <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url('admin/keuangan/transaksi/' . $entitasType . '/edit/' . $t['id']) ?>"
                               class="btn btn-xs btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="<?= base_url('admin/keuangan/transaksi/' . $entitasType . '/delete/' . $t['id']) ?>"
                                  class="d-inline" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-xs btn-danger" title="Hapus">
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

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
$(function() {
    // Inisialisasi DataTables dengan fitur export Excel dan PDF
    $('#tabel-transaksi').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[1, 'desc']], // Urutkan berdasarkan tanggal terbaru
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
        },
        // Tombol export: copy, Excel, PDF, Print
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel mr-1"></i>Excel',
                className: 'btn btn-sm btn-success',
                exportOptions: { columns: ':not(:last-child)' } // Kecuali kolom Aksi
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf mr-1"></i>PDF',
                className: 'btn btn-sm btn-danger',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print mr-1"></i>Print',
                className: 'btn btn-sm btn-secondary',
                exportOptions: { columns: ':not(:last-child)' }
            }
        ]
    });
});
</script>
<?php $this->endSection(); ?>
