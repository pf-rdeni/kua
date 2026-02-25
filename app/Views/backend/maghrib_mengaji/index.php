<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card card-success card-outline mt-4">
            <div class="card-header">
                <h3 class="card-title">Matriks <?= esc($pageTitle) ?></h3>
            </div>
            <div class="card-body">

                <!-- Filter Tahun -->
                <form action="<?= base_url('admin/maghrib-mengaji') ?>" method="get" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Pilih Tahun Masehi</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="tahun" value="<?= esc($tahunPilih) ?>" min="2020" max="2100" required>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <!-- Form Matriks -->
                <form action="<?= base_url('admin/maghrib-mengaji/save-matrix') ?>" method="post" id="formMatrix">
                    <input type="hidden" name="tahun" value="<?= esc($tahunPilih) ?>">
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm text-center align-middle" style="min-width: 1000px;">
                            <thead class="bg-success">
                                <tr>
                                    <th rowspan="2" class="align-middle" style="width: 50px;">NO</th>
                                    <th rowspan="2" class="align-middle" style="width: 120px;">BULAN</th>
                                    <th rowspan="2" class="align-middle" style="width: 150px;">TANGGAL</th>
                                    <th rowspan="2" class="align-middle" style="width: 250px;">TEMPAT MASJID/MUSHOLA</th>
                                    <th colspan="3">PETUGAS</th>
                                </tr>
                                <tr>
                                    <th>MC (Ustadz)</th>
                                    <th>DO'A (Ustadz)</th>
                                    <th>KULTUM (Ustadz)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $namaBulan = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                                ?>
                                <?php for ($b = 1; $b <= 12; $b++): ?>
                                    <tr>
                                        <td class="align-middle"><?= $b ?></td>
                                        <td class="align-middle text-left font-weight-bold">
                                            <?= $namaBulan[$b] ?>
                                        </td>
                                        <td class="align-middle">
                                            <input type="date" class="form-control form-control-sm" 
                                                   name="matrix[<?= $b ?>][tanggal]" 
                                                   value="<?= esc($matrix[$b]['tanggal']) ?>">
                                        </td>
                                        <td class="align-middle text-left">
                                            <select class="form-control select2-masjid" name="matrix[<?= $b ?>][id_masjid]" style="width: 100%;">
                                                <option value="">-- Pilih --</option>
                                                <?php foreach ($masjidList as $m): ?>
                                                    <option value="<?= $m['id_masjid_mushola'] ?>" <?= ($matrix[$b]['id_masjid'] == $m['id_masjid_mushola']) ? 'selected' : '' ?>>
                                                        <?= esc($m['nama']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="align-middle text-left">
                                            <select class="form-control select2-petugas" name="matrix[<?= $b ?>][mc]" style="width: 100%;">
                                                <?php if (!empty($matrix[$b]['mc'])): ?>
                                                    <option value="<?= $matrix[$b]['mc'] ?>" selected 
                                                            data-nama="<?= esc($personilNames[$matrix[$b]['mc']]['nama'] ?? '') ?>" 
                                                            data-foto="<?= esc($personilNames[$matrix[$b]['mc']]['foto'] ?? '') ?>">
                                                        <?= esc($personilNames[$matrix[$b]['mc']]['nama'] ?? '') ?>
                                                    </option>
                                                <?php endif; ?>
                                            </select>
                                        </td>
                                        <td class="align-middle text-left">
                                            <select class="form-control select2-petugas" name="matrix[<?= $b ?>][doa]" style="width: 100%;">
                                                <?php if (!empty($matrix[$b]['doa'])): ?>
                                                    <option value="<?= $matrix[$b]['doa'] ?>" selected 
                                                            data-nama="<?= esc($personilNames[$matrix[$b]['doa']]['nama'] ?? '') ?>" 
                                                            data-foto="<?= esc($personilNames[$matrix[$b]['doa']]['foto'] ?? '') ?>">
                                                        <?= esc($personilNames[$matrix[$b]['doa']]['nama'] ?? '') ?>
                                                    </option>
                                                <?php endif; ?>
                                            </select>
                                        </td>
                                        <td class="align-middle text-left">
                                            <select class="form-control select2-petugas" name="matrix[<?= $b ?>][kultum]" style="width: 100%;">
                                                <?php if (!empty($matrix[$b]['kultum'])): ?>
                                                    <option value="<?= $matrix[$b]['kultum'] ?>" selected 
                                                            data-nama="<?= esc($personilNames[$matrix[$b]['kultum']]['nama'] ?? '') ?>" 
                                                            data-foto="<?= esc($personilNames[$matrix[$b]['kultum']]['foto'] ?? '') ?>">
                                                        <?= esc($personilNames[$matrix[$b]['kultum']]['nama'] ?? '') ?>
                                                    </option>
                                                <?php endif; ?>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary" id="btnSaveMatrix">
                            <i class="fas fa-save"></i> Simpan Matriks <?= esc($tahunPilih) ?>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Inisialisasi Select2 untuk Masjid dengan tema bootstrap 4
    $('.select2-masjid').select2({
        theme: 'bootstrap4',
        placeholder: '-- Pilih Tempat --',
        allowClear: true
    });

    function getInitials(name) {
        var parts = name.split(' ');
        var initials = '';
        if (parts.length > 0) initials += parts[0].charAt(0);
        if (parts.length > 1) initials += parts[1].charAt(0);
        return initials.toUpperCase();
    }

    function createAvatarHtml(mubaligh, sizeClass) {
        var size = sizeClass === 'small' ? '20px' : '25px';
        var fontSize = sizeClass === 'small' ? '10px' : '12px';
        
        // Cek jika fotonya adalah foto default sistem
        if (mubaligh.foto && !mubaligh.foto.includes('default-')) {
            return '<img src="' + mubaligh.foto + '" class="img-circle mr-2" style="width: '+size+'; height: '+size+'; object-fit: cover;" />';
        } else {
            // Buat avatar inisial dengan CSS
            var initials = getInitials(mubaligh.nama);
            // Generate warna random berdasarkan nama agar konsisten
            var colors = ['#007bff', '#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6610f2', '#e83e8c', '#fd7e14'];
            var colorIndex = mubaligh.nama.length % colors.length;
            var bgColor = colors[colorIndex];
            
            return '<span class="img-circle mr-2" style="display: inline-block; width: '+size+'; height: '+size+'; background-color: '+bgColor+'; color: white; text-align: center; line-height: '+size+'; font-size: '+fontSize+'; font-weight: bold;">' + initials + '</span>';
        }
    }

    function formatMubaligh (mubaligh) {
        if (!mubaligh.id) {
            return mubaligh.text;
        }
        var avatarHtml = createAvatarHtml(mubaligh, 'normal');
        var $mubaligh = $(
            '<span>' + avatarHtml + ' ' + mubaligh.nama + '</span>'
        );
        return $mubaligh;
    }

    function formatMubalighSelection (mubaligh) {
        if (!mubaligh.id) {
            return mubaligh.text;
        }
        var foto = mubaligh.foto;
        var nama = mubaligh.nama || mubaligh.text;

        // If data is from pre-loaded HTML option
        if (!foto && mubaligh.element && mubaligh.element.dataset.foto) {
            foto = mubaligh.element.dataset.foto;
            nama = mubaligh.element.dataset.nama;
        }

        if(foto) {
            var avatarHtml = createAvatarHtml({foto: foto, nama: nama}, 'small');
            return $(
                '<span>' + avatarHtml + ' ' + nama + '</span>'
            );
        }
        return mubaligh.text;
    }

    // Inisialisasi Select2 AJAX untuk 3 Peran Petugas menggunakan resource mubaligh ramadhan
    $('.select2-petugas').select2({
        theme: 'bootstrap4',
        placeholder: 'Cari Ustadz...',
        allowClear: true,
        ajax: {
            url: "<?= base_url('admin/jadwal-ramadhan/search-mubaligh') ?>",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                var mappedResults = data.results.map(function(item) {
                    var namaOnly = item.text.split(' - ')[1] || item.text;
                    return {
                        id: item.id,
                        text: namaOnly,
                        nama: namaOnly,
                        foto: item.foto
                    };
                });
                return {
                    results: mappedResults
                };
            },
            cache: true
        },
        minimumInputLength: 0,
        templateResult: formatMubaligh,
        templateSelection: formatMubalighSelection
    });
});
</script>
<?= $this->endSection(); ?>
