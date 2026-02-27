<!-- Partial Form Iuran — dipakai di modal Tambah dan Edit -->
<?php
// Deteksi mode edit: jika $iuran === 'edit', gunakan prefix 'edit_' untuk ID field
$isEdit = ($iuran === 'edit');
$prefix = $isEdit ? 'edit_' : '';
?>
<div class="row">
    <!-- Nama Iuran -->
    <div class="col-md-8 mb-3">
        <label class="font-weight-bold text-sm">Nama Iuran <span class="text-danger">*</span></label>
        <input type="text" name="nama_iuran" id="<?= $prefix ?>nama_iuran" class="form-control form-control-sm"
               placeholder="Contoh: Iuran Bulanan, Iuran Tahunan 2025" required>
    </div>

    <!-- Periode -->
    <div class="col-md-4 mb-3">
        <label class="font-weight-bold text-sm">Periode <span class="text-danger">*</span></label>
        <select name="periode" id="<?= $prefix ?>periode" class="form-control form-control-sm" required>
            <option value="">— Pilih Periode —</option>
            <option value="harian">Harian</option>
            <option value="mingguan">Mingguan</option>
            <option value="bulanan">Bulanan</option>
            <option value="tahunan">Tahunan</option>
            <option value="sekali">Sekali (Tidak berulang)</option>
        </select>
    </div>

    <!-- Nominal -->
    <div class="col-md-6 mb-3">
        <label class="font-weight-bold small">Nominal (Rp) <span class="text-danger">*</span></label>
        <div class="input-group input-group-sm">
            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
            <input type="number" name="nominal" class="form-control form-control-sm" value="<?= esc($iuran['nominal'] ?? '') ?>" step="any" required>
        </div>
    </div>

    <!-- Tanggal Mulai -->
    <div class="col-md-4 mb-3">
        <label class="font-weight-bold text-sm">Tanggal Mulai <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_mulai" id="<?= $prefix ?>tanggal_mulai" class="form-control form-control-sm" required>
    </div>

    <!-- Tanggal Selesai -->
    <div class="col-md-4 mb-3">
        <label class="font-weight-bold text-sm">Tanggal Selesai</label>
        <input type="date" name="tanggal_selesai" id="<?= $prefix ?>tanggal_selesai" class="form-control form-control-sm">
        <small class="text-muted">Kosongkan jika tidak ada batas waktu.</small>
    </div>

    <!-- Status Aktif -->
    <div class="col-md-4 mb-3">
        <label class="font-weight-bold text-sm">Status</label>
        <select name="is_active" id="<?= $prefix ?>is_active" class="form-control form-control-sm">
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
        </select>
    </div>

    <!-- Keterangan -->
    <div class="col-12 mb-2">
        <label class="font-weight-bold text-sm">Keterangan</label>
        <textarea name="keterangan" id="<?= $prefix ?>keterangan" class="form-control form-control-sm"
                  rows="2" placeholder="Keterangan opsional..."></textarea>
    </div>
</div>
