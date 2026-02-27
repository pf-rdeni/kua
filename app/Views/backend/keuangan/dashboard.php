<?php $this->extend('backend/template/template'); ?>
<?php $this->section('content'); ?>

<!-- Action Header -->
<div class="row mb-3">
    <div class="col-12 text-right">
        <a href="<?= base_url('admin/keuangan/laporan') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-file-alt mr-1"></i>Laporan Umum
        </a>
    </div>
</div>

<!-- Kartu Rekap Total -->
<div class="row mb-2">
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="info-box shadow-sm border-left border-success">
            <span class="info-box-icon bg-success"><i class="fas fa-arrow-circle-down"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Total Pemasukan</span>
                <span class="info-box-number text-success">Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></span>
                <small class="text-muted">Semua entitas, tahun <?= esc($tahun) ?></small>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="info-box shadow-sm border-left border-danger">
            <span class="info-box-icon bg-danger"><i class="fas fa-arrow-circle-up"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Total Pengeluaran</span>
                <span class="info-box-number text-danger">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></span>
                <small class="text-muted">Semua entitas, tahun <?= esc($tahun) ?></small>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="info-box shadow-sm border-left <?= $totalSaldo >= 0 ? 'border-primary' : 'border-warning' ?>">
            <span class="info-box-icon <?= $totalSaldo >= 0 ? 'bg-primary' : 'bg-warning' ?>"><i class="fas fa-balance-scale"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Saldo Bersih</span>
                <span class="info-box-number <?= $totalSaldo >= 0 ? 'text-primary' : 'text-warning' ?>">
                    Rp <?= number_format(abs($totalSaldo), 0, ',', '.') ?>
                    <?= $totalSaldo < 0 ? '<small>(defisit)</small>' : '' ?>
                </span>
                <small class="text-muted">Pemasukan – Pengeluaran</small>
            </div>
        </div>
    </div>
</div>

<!-- Rekap Per Entitas -->
<div class="row mb-3">
    <?php foreach ($rekapEntitas as $r): ?>
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <span>
                    <i class="<?= esc($r['entitas']['icon'] ?? 'fas fa-building') ?> mr-1 text-info"></i>
                    <strong><?= esc($r['entitas']['nama_label']) ?></strong>
                </span>
                <div>
                    <a href="<?= base_url('admin/keuangan/transaksi/' . $r['entitas']['kode']) ?>" class="btn btn-xs btn-outline-info" title="Lihat Transaksi">
                        <i class="fas fa-exchange-alt"></i> Transaksi
                    </a>
                    <a href="<?= base_url('admin/keuangan/iuran/' . $r['entitas']['kode']) ?>" class="btn btn-xs btn-outline-success ml-1" title="Iuran Anggota">
                        <i class="fas fa-users"></i> Iuran
                    </a>
                </div>
            </div>
            <div class="card-body py-2">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted"><i class="fas fa-plus-circle text-success mr-1"></i>Pemasukan</small>
                    <small class="font-weight-bold text-success">Rp <?= number_format($r['total_pemasukan'], 0, ',', '.') ?></small>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted"><i class="fas fa-minus-circle text-danger mr-1"></i>Pengeluaran</small>
                    <small class="font-weight-bold text-danger">Rp <?= number_format($r['total_pengeluaran'], 0, ',', '.') ?></small>
                </div>
                <hr class="my-1">
                <div class="d-flex justify-content-between">
                    <small class="text-muted"><i class="fas fa-wallet mr-1"></i>Saldo</small>
                    <small class="font-weight-bold <?= $r['saldo'] >= 0 ? 'text-primary' : 'text-warning' ?>">
                        Rp <?= number_format(abs($r['saldo']), 0, ',', '.') ?>
                        <?= $r['saldo'] < 0 ? '<span class="badge badge-warning">Defisit</span>' : '' ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (empty($rekapEntitas)): ?>
    <div class="col-12">
        <div class="alert alert-info"><i class="fas fa-info-circle mr-1"></i> Tidak ada entitas yang dapat diakses.</div>
    </div>
    <?php endif; ?>
</div>

<!-- Transaksi Terakhir -->
<div class="card shadow-sm">
    <div class="card-header py-2">
        <h6 class="mb-0"><i class="fas fa-history mr-1 text-secondary"></i>10 Transaksi Terakhir</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Entitas</th>
                        <th>Kategori</th>
                        <th>Jenis</th>
                        <th class="text-right">Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transaksiTerakhir)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-3">Belum ada transaksi.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($transaksiTerakhir as $t): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($t['tanggal_transaksi'])) ?></td>
                        <td><span class="badge badge-secondary"><?= esc($t['entitas_type']) ?></span></td>
                        <td>
                            <?php if ($t['nama_kategori']): ?>
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
                            Rp <?= number_format($t['jumlah'], 0, ',', '.') ?>
                        </td>
                        <td><?= esc(mb_strimwidth($t['keterangan'] ?? '-', 0, 40, '...')) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer py-2 text-right">
        <a href="<?= base_url('admin/keuangan/laporan') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-list mr-1"></i>Lihat Semua Transaksi
        </a>
    </div>
</div>

<?php $this->endSection(); ?>
