<?php $this->extend('backend/template/template'); ?>
<?php $this->section('content'); ?>

<div class="row mb-3">
    <div class="col-12 text-right">
        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalTambahKategori">
            <i class="fas fa-plus mr-1"></i>Tambah Kategori Khusus
        </button>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle mr-1"></i><?= session()->getFlashdata('success') ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle mr-1"></i><?= session()->getFlashdata('error') ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show"><ul class="mb-0"><?php foreach ((array)session()->getFlashdata('errors') as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?></ul><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
<?php endif; ?>

<div class="row">

    <!-- Kolom 1: Kategori Global Standar -->
    <div class="col-lg-6 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0"><i class="fas fa-globe mr-1 text-primary"></i>Kategori Global (Standar Kemenag)</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (empty($globalCategories)): ?>
                    <li class="list-group-item text-muted text-center py-3">Belum ada kategori global.</li>
                    <?php endif; ?>

                    <?php foreach ($globalCategories as $gKat): ?>
                    <?php 
                        $isHidden = $gKat['is_hidden_by_entitas']; 
                        $badgeClass = $isHidden ? 'secondary' : $gKat['warna_badge'];
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center <?= $isHidden ? 'bg-light text-muted' : '' ?>">
                        <div>
                            <span class="badge badge-<?= $badgeClass ?> mr-2" style="width:20px"><i class="<?= $gKat['jenis'] === 'pemasukan' ? 'fas fa-arrow-down' : ($gKat['jenis'] === 'pengeluaran' ? 'fas fa-arrow-up' : 'fas fa-exchange-alt') ?>"></i></span>
                            <strong class="<?= $isHidden ? 'text-strikethrough' : '' ?>"><?= esc($gKat['nama_kategori']) ?></strong>
                            <small class="d-block mt-1"><?= esc($gKat['deskripsi'] ?? 'Tanpa deskripsi') ?></small>
                        </div>
                        <form method="POST" action="<?= base_url('admin/keuangan/kategori/' . $entitasType . '/toggle/' . $gKat['id']) ?>">
                            <?= csrf_field() ?>
                            <?php if ($isHidden): ?>
                            <button type="submit" class="btn btn-sm btn-outline-success" title="Tampilkan Kategori Ini">
                                <i class="fas fa-eye mr-1"></i>Tampilkan
                            </button>
                            <?php else: ?>
                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Sembunyikan dari Pilihan Transaksi">
                                <i class="fas fa-eye-slash mr-1"></i>Sembunyikan
                            </button>
                            <?php endif; ?>
                        </form>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="card-footer py-2 bg-light">
                <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>Kategori Global tidak dapat dihapus, tapi Anda bisa menyembunyikannya agar tidak muncul di form transaksi.</small>
            </div>
        </div>
    </div>

    <!-- Kolom 2: Kategori Custom Milik Masjid/Entitas -->
    <div class="col-lg-6 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-success text-white py-2">
                <h6 class="mb-0"><i class="fas fa-star mr-1"></i>Kategori Khusus Anda</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (empty($customCategories)): ?>
                    <li class="list-group-item text-muted text-center py-4">
                        Belum ada kategori milik sendiri.<br>
                        <small>Klik "Tambah Kategori Khusus" untuk membuat Kategori Baru (misal: "Sumbangan Pembangunan").</small>
                    </li>
                    <?php endif; ?>

                    <?php foreach ($customCategories as $cKat): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge badge-<?= esc($cKat['warna_badge']) ?> mr-2" style="width:20px"><i class="<?= $cKat['jenis'] === 'pemasukan' ? 'fas fa-arrow-down' : ($cKat['jenis'] === 'pengeluaran' ? 'fas fa-arrow-up' : 'fas fa-exchange-alt') ?>"></i></span>
                            <strong><?= esc($cKat['nama_kategori']) ?></strong>
                            <small class="d-block mt-1"><?= esc($cKat['deskripsi'] ?? 'Tanpa deskripsi') ?></small>
                        </div>
                        <form method="POST" action="<?= base_url('admin/keuangan/kategori/' . $entitasType . '/delete/' . $cKat['id']) ?>" onsubmit="return confirm('Yakin ingin menghapus kategori ini permanen?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Permanen">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="modalTambahKategori" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title"><i class="fas fa-plus mr-1"></i>Buat Kategori Transaksi</h6>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="<?= base_url('admin/keuangan/kategori/' . $entitasType . '/store') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-sm">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" class="form-control form-control-sm" placeholder="Misal: Uang Muka Qurban" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-sm">Sifat Transaksi <span class="text-danger">*</span></label>
                        <select name="jenis" class="form-control form-control-sm" required>
                            <option value="">— Pilih Jenis —</option>
                            <option value="pemasukan">Pemasukan Saja</option>
                            <option value="pengeluaran">Pengeluaran Saja</option>
                            <option value="keduanya">Keduanya (Pemasukan & Pengeluaran)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-sm">Warna Label</label>
                        <select name="warna_badge" class="form-control form-control-sm">
                            <option value="success">Hijau (Success)</option>
                            <option value="danger">Merah (Danger)</option>
                            <option value="primary">Biru (Primary)</option>
                            <option value="info">Biru Muda (Info)</option>
                            <option value="warning">Kuning (Warning)</option>
                            <option value="secondary" selected>Abu-abu (Secondary)</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-sm">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control form-control-sm" rows="2" placeholder="Catatan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save mr-1"></i>Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .text-strikethrough { text-decoration: line-through; }
</style>

<?php $this->endSection(); ?>
