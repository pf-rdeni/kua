<?php $this->extend('backend/template/template'); ?>
<?php $this->section('content'); ?>

<!-- Action Header -->
<div class="row mb-3">
    <div class="col-12 text-right">
        <a href="<?= base_url('admin/keuangan/transaksi/' . $entitasType) ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>Kembali
        </a>
    </div>
</div>

<!-- Alert Error Validasi -->
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <strong><i class="fas fa-exclamation-triangle mr-1"></i>Terdapat Kesalahan:</strong>
    <ul class="mb-0 mt-1">
        <?php foreach ((array)session()->getFlashdata('errors') as $err): ?>
        <li><?= esc($err) ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-12 col-md-12">
        <div class="card shadow-sm">
            <div class="card-header py-2 bg-light">
                <h6 class="mb-0"><i class="fas fa-file-invoice-dollar mr-1 text-info"></i>Form Transaksi Keuangan</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= $transaksi
                    ? base_url('admin/keuangan/transaksi/' . $entitasType . '/update/' . $transaksi['id'])
                    : base_url('admin/keuangan/transaksi/' . $entitasType . '/store') ?>"
                    enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row">
                        <!-- Jenis Transaksi -->
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-sm">Jenis Transaksi <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="jenis_masuk" name="jenis" value="pemasukan" class="custom-control-input"
                                           <?= (!$transaksi || $transaksi['jenis'] === 'pemasukan') ? 'checked' : '' ?>>
                                    <label class="custom-control-label text-success font-weight-bold" for="jenis_masuk">
                                        <i class="fas fa-arrow-down mr-1"></i>Pemasukan
                                    </label>
                                </div>
                                <div class="custom-control custom-radio ml-3">
                                    <input type="radio" id="jenis_keluar" name="jenis" value="pengeluaran" class="custom-control-input"
                                           <?= ($transaksi && $transaksi['jenis'] === 'pengeluaran') ? 'checked' : '' ?>>
                                    <label class="custom-control-label text-danger font-weight-bold" for="jenis_keluar">
                                        <i class="fas fa-arrow-up mr-1"></i>Pengeluaran
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Tanggal Transaksi -->
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-sm">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_transaksi" class="form-control form-control-sm"
                                   value="<?= esc($transaksi['tanggal_transaksi'] ?? date('Y-m-d')) ?>" required>
                        </div>

                        <!-- Jumlah -->
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-sm">Jumlah (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" name="jumlah" id="jumlah" class="form-control form-control-sm"
                                       value="<?= esc($transaksi['jumlah'] ?? '') ?>"
                                       placeholder="Contoh: 150000" min="1" step="any" required>
                            </div>
                            <small id="jumlah-terbilang" class="text-muted fst-italic"></small>
                        </div>

                        <!-- Kategori -->
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-sm">Kategori</label>
                            <select name="id_kategori" class="form-control form-control-sm" id="select-kategori">
                                <option value="">— Pilih Kategori —</option>
                                <?php foreach ($kategoriList as $kat): ?>
                                <option value="<?= $kat['id'] ?>"
                                    data-jenis="<?= $kat['jenis'] ?>"
                                    <?= ($transaksi && (string)$transaksi['id_kategori'] === (string)$kat['id']) ? 'selected' : '' ?>>
                                    <?= esc($kat['nama_kategori']) ?>
                                    (<?= $kat['jenis'] === 'keduanya' ? 'Umum' : ucfirst($kat['jenis']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Kas -->
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-sm">Kas / Rekening</label>
                            <select name="id_kas" class="form-control form-control-sm">
                                <option value="">— Tanpa Kas —</option>
                                <?php foreach ($kasList as $kas): ?>
                                <option value="<?= $kas['id'] ?>"
                                    <?= ($transaksi && (string)$transaksi['id_kas'] === (string)$kas['id']) ? 'selected' : '' ?>>
                                    <?= esc($kas['nama_kas']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($kasList)): ?>
                            <small class="text-warning"><i class="fas fa-exclamation-triangle mr-1"></i>
                                Belum ada kas. <a href="<?= base_url('admin/keuangan/kas/' . $entitasType) ?>">Tambah Kas</a>
                            </small>
                            <?php endif; ?>
                        </div>

                        <!-- Entitas ID (khusus masjid/mushola) -->
                        <?php if ($entitasType === 'masjid_mushola' && !empty($masjidList)): ?>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-sm">Masjid/Mushola</label>
                            <select name="entitas_id" class="form-control form-control-sm">
                                <option value="">— Pilih Masjid/Mushola —</option>
                                <?php foreach ($masjidList as $m): ?>
                                <option value="<?= $m['id_masjid_mushola'] ?>"
                                    <?= ($transaksi && (string)$transaksi['entitas_id'] === (string)$m['id_masjid_mushola']) ? 'selected' : '' ?>>
                                    <?= esc($m['nama']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <!-- No Referensi -->
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-sm">No. Referensi / Kuitansi</label>
                            <input type="text" name="no_referensi" class="form-control form-control-sm"
                                   value="<?= esc($transaksi['no_referensi'] ?? '') ?>"
                                   placeholder="Opsional, misal: KW/2025/001">
                        </div>

                        <!-- Keterangan -->
                        <div class="col-12 mb-3">
                            <label class="font-weight-bold text-sm">Keterangan</label>
                            <textarea name="keterangan" class="form-control form-control-sm" rows="3"
                                      placeholder="Deskripsi detail transaksi..."><?= esc($transaksi['keterangan'] ?? '') ?></textarea>
                        </div>

                        <!-- Upload Bukti -->
                        <div class="col-12 mb-3">
                            <label class="font-weight-bold text-sm">Bukti Transaksi (Foto/Scan)</label>
                            <?php if ($transaksi && !empty($transaksi['bukti'])): ?>
                            <div class="mb-1">
                                <a href="<?= base_url('uploads/keuangan/' . $transaksi['bukti']) ?>" target="_blank" class="btn btn-xs btn-info">
                                    <i class="fas fa-image mr-1"></i>Lihat Bukti Sekarang
                                </a>
                                <small class="text-muted ml-2">Upload baru untuk mengganti bukti yang ada.</small>
                            </div>
                            <?php endif; ?>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="bukti" name="bukti"
                                       accept="image/*,.pdf">
                                <label class="custom-file-label" for="bukti">Pilih file gambar atau PDF...</label>
                            </div>
                            <small class="text-muted">Maksimal 2MB. Format: JPG, PNG, PDF.</small>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('admin/keuangan/transaksi/' . $entitasType) ?>" class="btn btn-secondary">
                            <i class="fas fa-times mr-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i><?= $transaksi ? 'Update' : 'Simpan' ?> Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
$(function() {
    // Tampilkan nama file yang dipilih pada custom-file-input
    $('.custom-file-input').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName || 'Pilih file...');
    });

    // Format jumlah ke terbilang sederhana (sebagai hint)
    $('#jumlah').on('input', function () {
        var val = parseInt($(this).val().replace(/\D/g, '')) || 0;
        $('#jumlah-terbilang').text(val > 0 ? 'Rp ' + val.toLocaleString('id-ID') : '');
    });
});
</script>
<?php $this->endSection(); ?>
