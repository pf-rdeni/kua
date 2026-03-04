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
                        <?php if (isset($jenis) && in_array($jenis, ['pemasukan', 'pengeluaran'])): ?>
                            <input type="hidden" id="jenis_transaksi" name="jenis" value="<?= esc($jenis) ?>">
                        <?php else: ?>
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
                        <?php endif; ?>

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
                            <label class="font-weight-bold text-sm">Kas / Rekening <span class="text-danger">*</span></label>
                            <select name="id_kas" class="form-control form-control-sm" id="select-kas" required <?= empty($kasList) ? 'disabled' : '' ?>>
                                <option value="">— Pilih Kas —</option>
                                <?php foreach ($kasList as $kas): ?>
                                <option value="<?= $kas['id'] ?>"
                                    data-saldo="<?= esc($kas['saldo_berjalan']) ?>"
                                    <?= ($transaksi && (string)$transaksi['id_kas'] === (string)$kas['id']) || (!$transaksi && count($kasList) === 1) ? 'selected' : '' ?>>
                                    <?= esc($kas['nama_kas']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <!-- Area Informasi Saldo (Hanya tampil jika jenis = pengeluaran) -->
                            <div id="info-saldo-kas" class="mt-1" style="display:none; font-size:0.85rem;">
                                Sisa Kas: <span id="text-saldo-kas" class="font-weight-bold">Rp 0</span>
                                <div id="error-saldo-kas" class="text-danger mt-1" style="display:none; font-weight:600;">
                                    <i class="fas fa-times-circle mr-1"></i>Saldo tidak mencukupi untuk pengeluaran ini!
                                </div>
                            </div>

                            <?php if (empty($kasList)): ?>
                            <small class="text-danger font-weight-bold"><i class="fas fa-exclamation-triangle mr-1"></i>
                                Belum ada kas. Anda harus <a href="<?= base_url('admin/keuangan/kas/' . $entitasType) ?>">Membuat Kas</a> terlebih dahulu.
                            </small>
                            <?php endif; ?>
                        </div>

                        <!-- Entitas ID (Dinamis: Masjid / Majelis Taklim) -->
                        <?php if (in_array($entitasType, ['masjid_mushola', 'majelis_taklim'])): ?>
                        <?php if ($operatorEntitasId): ?>
                            <input type="hidden" name="entitas_id" value="<?= $operatorEntitasId ?>">
                        <?php elseif (!empty($entitasList)): ?>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-sm"><?= esc($entitasConfig['nama_label']) ?></label>
                            <select name="entitas_id" class="form-control form-control-sm">
                                <option value="">— Pilih <?= esc($entitasConfig['nama_label']) ?> —</option>
                                <?php foreach ($entitasList as $e): ?>
                                <?php $namaEntitas = isset($e['nama']) ? $e['nama'] : ($e['nama_majelis_taklim'] ?? 'Tanpa Nama'); ?>
                                <option value="<?= $e[$pkRow] ?>"
                                    <?= ($transaksi && (string)$transaksi['entitas_id'] === (string)$e[$pkRow]) ? 'selected' : '' ?>>
                                    <?= esc($namaEntitas) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
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
                        <button type="submit" id="btn-submit-transaksi" class="btn btn-primary" <?= empty($kasList) ? 'disabled title="Harap buat Kas terlebih dahulu"' : '' ?>>
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
        validasiSaldo();
    });

    // Filter Kategori berdasarkan Jenis Transaksi
    function filterKategori() {
        var jenis = $('#jenis_transaksi').val() || $('input[name="jenis"]:checked').val();
        
        $('#select-kategori option').each(function() {
            if ($(this).val() === "") return; // Abaikan option default
            
            var targetJenis = $(this).data('jenis');
            if (targetJenis === 'keduanya' || targetJenis === jenis) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        // Reset pilihan jika kategori yg sedang dipilih disembunyikan
        var selectedOption = $('#select-kategori option:selected');
        if (selectedOption.css('display') === 'none') {
            $('#select-kategori').val('');
        }
    }

    // Jalankan filter saat halaman dimuat
    filterKategori();

    // Jalankan filter saat radio button jenis diklik
    $('input[name="jenis"]').on('change', function() {
        filterKategori();
        validasiSaldo();
    });

    // Fitur Validasi Saldo Kas
    function validasiSaldo() {
        var jenis = $('#jenis_transaksi').val() || $('input[name="jenis"]:checked').val();
        var kasOption = $('#select-kas option:selected');
        
        if (jenis === 'pengeluaran' && kasOption.val()) {
            $('#info-saldo-kas').show();
            var saldo = parseFloat(kasOption.data('saldo')) || 0;
            var jumlahInput = parseFloat($('#jumlah').val()) || 0;
            
            $('#text-saldo-kas').text('Rp ' + saldo.toLocaleString('id-ID'));
            
            if (jumlahInput > saldo) {
                $('#error-saldo-kas').show();
                $('#btn-submit-transaksi').prop('disabled', true);
            } else {
                $('#error-saldo-kas').hide();
                $('#btn-submit-transaksi').prop('disabled', false);
            }
        } else {
            $('#info-saldo-kas').hide();
            $('#error-saldo-kas').hide();
            // Kembalikan ke normal, kecuali kas kosong sejak awal
            if ($('#select-kas option').length > 1) { 
                $('#btn-submit-transaksi').prop('disabled', false);
            }
        }
    }

    // Hitung ulang saldo setiap pindah dropdown kas
    $('#select-kas').on('change', validasiSaldo);

    // Initial load
    validasiSaldo();
});
</script>
<?php $this->endSection(); ?>
