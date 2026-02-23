<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><?= esc($page_title) ?></h3>
            </div>
            
            <form action="<?= base_url('admin/setting-berkas/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="card-body">
                    
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif ?>

                    <div class="form-group">
                        <label for="nama_berkas">Nama Berkas <span class="text-danger">*</span></label>
                        <input type="text" name="nama_berkas" id="nama_berkas" class="form-control" value="<?= old('nama_berkas') ?>" placeholder="Misal: KTP, KK, Buku Rekening" required>
                        <small class="form-text text-muted">Pastikan nama berkas unik dan konsisten.</small>
                    </div>

                    <div class="form-group">
                        <label>Digunakan Oleh Entitas <span class="text-danger">*</span></label>
                        <select name="entitas_type[]" class="form-control select2" multiple="multiple" data-placeholder="Pilih Entitas" style="width: 100%;" required>
                            <option value="mubaligh">Mubaligh</option>
                            <option value="imam_masjid">Imam Masjid</option>
                            <option value="fardu_kifayah">Fardu Kifayah</option>
                            <option value="penggali_kubur">Penggali Kubur</option>
                            <option value="majelis_taklim">Majelis Taklim</option>
                            <option value="masjid_mushola">Masjid Mushola</option>
                            <option value="tpq_mdta">TPQ / MDTA</option>
                        </select>
                        <small class="form-text text-muted">Pilih satu atau lebih entitas yang membutuhkan lampiran ini.</small>
                    </div>

                    <div class="form-group" id="preset_rasio_group">
                        <label for="preset_rasio">Template Rasio Crop</label>
                        <select id="preset_rasio" class="form-control">
                            <option value="free">Free Crop (Bebas / Tanpa Proporsi)</option>
                            <option value="ktp_landscape">ID Card / KTP (Landscape) - 85.6 x 53.98</option>
                            <option value="ktp_portrait">ID Card / KTP (Portrait) - 53.98 x 85.6</option>
                            <option value="a4_landscape">Kertas A4 (Landscape) - 297 x 210</option>
                            <option value="a4_portrait">Kertas A4 (Portrait) - 210 x 297</option>
                            <option value="3x4">Pas Foto 3x4 (Portrait)</option>
                            <option value="4x6">Pas Foto 4x6 (Portrait)</option>
                            <option value="1x1">Square / Kotak (1:1)</option>
                            <option value="custom">Custom (Isi Ukuran Sendiri)</option>
                        </select>
                    </div>

                    <div class="row" id="custom_rasio_inputs" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="aspect_ratio_width">Aspect Ratio (Width/Lebar)</label>
                                <input type="number" step="any" name="aspect_ratio_width" id="aspect_ratio_width" class="form-control" value="<?= old('aspect_ratio_width') ?>" placeholder="Misal: 85.6">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="aspect_ratio_height">Aspect Ratio (Height/Tinggi)</label>
                                <input type="number" step="any" name="aspect_ratio_height" id="aspect_ratio_height" class="form-control" value="<?= old('aspect_ratio_height') ?>" placeholder="Misal: 53.98">
                            </div>
                        </div>
                        <div class="col-12">
                            <small class="form-text text-muted mb-3">Silakan isi lebar dan tinggi dalam satuan centimeter (cm) atau pixel (px). Sistem otomatis mengkonversinya menjadi proporsi perbandingan crop gambar.</small>
                        </div>
                    </div>

                    <div class="form-group border-top pt-3 mt-3">
                        <label for="is_rekening">Fungsi Berkas Khusus</label>
                        <select name="is_rekening" id="is_rekening" class="form-control">
                            <option value="0" <?= old('is_rekening') == '0' ? 'selected' : '' ?>>Berkas Biasa (KTP, Ijazah, dll)</option>
                            <option value="1" <?= old('is_rekening') == '1' ? 'selected' : '' ?>>Buku Tabungan / Rekening Bank</option>
                        </select>
                        <small class="form-text text-muted">Jika diubah ke Buku Tabungan, saat Admin/Operator mengupload file ini mereka wajib membarenginya dengan menginput Nomor Rekening yang sesuai.</small>
                    </div>

                    <div class="form-group" id="rekening_digit_group" style="display: <?= old('is_rekening') == '1' ? 'block' : 'none' ?>;">
                        <label for="rekening_digit">Validasi Panjang Nomor Rekening (Digit)</label>
                        <input type="number" name="rekening_digit" id="rekening_digit" class="form-control" value="<?= old('rekening_digit') ?>" placeholder="Misal: 10 atau 15">
                        <small class="form-text text-muted">Kosongkan jika panjang digit rekening bebas / bervariasi.</small>
                    </div>

                    <div class="form-group border-top pt-3 mt-3">
                        <label for="status_aktif">Status</label>
                        <select name="status_aktif" id="status_aktif" class="form-control">
                            <option value="1" <?= old('status_aktif') == '1' ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= old('status_aktif') == '0' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>

                </div>
                
                <div class="card-footer text-right">
                    <a href="<?= base_url('admin/setting-berkas') ?>" class="btn btn-default">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4'
        });

        const presetSelect = $('#preset_rasio');
        const widthInput = $('#aspect_ratio_width');
        const heightInput = $('#aspect_ratio_height');
        const customInputsDiv = $('#custom_rasio_inputs');
        
        const presets = {
            'free': { w: '', h: '' },
            'ktp_landscape': { w: 85.60, h: 53.98 },
            'ktp_portrait': { w: 53.98, h: 85.60 },
            'a4_landscape': { w: 297, h: 210 },
            'a4_portrait': { w: 210, h: 297 },
            '3x4': { w: 3, h: 4 },
            '4x6': { w: 4, h: 6 },
            '1x1': { w: 1, h: 1 }
        };

        function handlePresetChange() {
            const val = presetSelect.val();
            if (val === 'custom') {
                customInputsDiv.show();
            } else {
                customInputsDiv.hide();
                widthInput.val(presets[val].w);
                heightInput.val(presets[val].h);
            }
        }

        presetSelect.on('change', handlePresetChange);

        const initialW = widthInput.val();
        const initialH = heightInput.val();
        
        let matchedPreset = 'custom';
        if (!initialW && !initialH) {
            matchedPreset = 'free';
        } else {
            const floatW = parseFloat(initialW);
            const floatH = parseFloat(initialH);
            for (const [key, val] of Object.entries(presets)) {
                if (key !== 'free' && Math.abs(parseFloat(val.w) - floatW) < 0.01 && Math.abs(parseFloat(val.h) - floatH) < 0.01) {
                    matchedPreset = key;
                    break;
                }
            }
        }
        
        presetSelect.val(matchedPreset);
        handlePresetChange();

        // Toggle Input Syarat Rekening
        $('#is_rekening').change(function() {
            if ($(this).val() === '1') {
                $('#rekening_digit_group').slideDown();
            } else {
                $('#rekening_digit_group').slideUp();
            }
        });
        
        // Inisialisasi on-load
        if ($('#is_rekening').val() === '1') {
            $('#rekening_digit_group').show();
        }
    });
</script>
<?= $this->endSection() ?>
