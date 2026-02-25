<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card card-success card-outline mt-4">
            <div class="card-header">
                <h3 class="card-title">Matriks <?= esc($pageTitle) ?></h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/maghrib-mengaji/cetak?tahun=' . $tahunPilih) ?>" target="_blank" class="btn btn-sm btn-danger">
                        <i class="fas fa-file-pdf"></i> Cetak Jadwal PDF
                    </a>
                </div>
            </div>
            <div class="card-body">

                <!-- Filter Tahun -->
                <form action="<?= base_url('admin/maghrib-mengaji') ?>" method="get" class="mb-4" id="filterYearForm">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Pilih Tahun Masehi</label>
                            <select name="tahun" class="form-control select2-filter-year" onchange="this.form.submit()">
                                <?php foreach ($years as $y): ?>
                                    <option value="<?= $y ?>" <?= ($tahunPilih == $y) ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endforeach; ?>
                            </select>
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

                <!-- Matriks Table -->
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
                                <?php
                                // Smart Month & Year Constraint
                                $firstDay = sprintf('%04d-%02d-01', $tahunPilih, $b);
                                $lastDay  = date('Y-m-t', strtotime($firstDay));
                                ?>
                                <tr>
                                    <td class="align-middle"><?= $b ?></td>
                                    <td class="align-middle text-left font-weight-bold">
                                        <?= $namaBulan[$b] ?>
                                    </td>
                                    <td class="align-middle">
                                        <input type="date" class="form-control form-control-sm auto-save" 
                                               data-bulan="<?= $b ?>" data-field="tanggal"
                                               min="<?= $firstDay ?>" max="<?= $lastDay ?>"
                                               value="<?= esc($matrix[$b]['tanggal']) ?>">
                                    </td>
                                    <td class="align-middle text-left">
                                        <select class="form-control select2-masjid auto-save" 
                                                data-bulan="<?= $b ?>" data-field="id_masjid" style="width: 100%;">
                                            <option value="">-- Pilih --</option>
                                            <?php foreach ($masjidList as $m): ?>
                                                <option value="<?= $m['id_masjid_mushola'] ?>" <?= ($matrix[$b]['id_masjid'] == $m['id_masjid_mushola']) ? 'selected' : '' ?>>
                                                    <?= esc($m['nama']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="align-middle text-left">
                                        <select class="form-control select2-petugas auto-save" 
                                                data-bulan="<?= $b ?>" data-field="mc" style="width: 100%;">
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
                                        <select class="form-control select2-petugas auto-save" 
                                                data-bulan="<?= $b ?>" data-field="doa" style="width: 100%;">
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
                                        <select class="form-control select2-petugas auto-save" 
                                                data-bulan="<?= $b ?>" data-field="kultum" style="width: 100%;">
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

            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Inisialisasi Select2 untuk Filter Tahun
    $('.select2-filter-year').select2({
        theme: 'bootstrap4',
        minimumResultsForSearch: -1
    });

    // Inisialisasi Select2 untuk Masjid
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
        
        if (mubaligh.foto && !mubaligh.foto.includes('default-')) {
            return '<img src="' + mubaligh.foto + '" class="img-circle mr-2" style="width: '+size+'; height: '+size+'; object-fit: cover;" />';
        } else {
            var initials = getInitials(mubaligh.nama);
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

    $('.select2-petugas').select2({
        theme: 'bootstrap4',
        placeholder: 'Cari Ustadz...',
        allowClear: true,
        ajax: {
            url: "<?= base_url('admin/maghrib-mengaji/search-mubaligh') ?>",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var el = $(this);
                var row = el.closest('tr');
                var excludeIds = [];
                row.find('.select2-petugas').not(el).each(function() {
                    var val = $(this).val();
                    if (val) excludeIds.push(val);
                });

                return {
                    q: params.term,
                    exclude_ids: excludeIds
                };
            },
            processResults: function (data) {
                return {
                    results: data.results
                };
            },
            cache: true
        },
        minimumInputLength: 0,
        templateResult: formatMubaligh,
        templateSelection: formatMubalighSelection
    });

    // Auto Save via AJAX
    $(document).on('change', '.auto-save', function() {
        var el = $(this);
        var bulan = el.data('bulan');
        var field = el.data('field');
        var value = el.val();

        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });

        $.ajax({
            url: "<?= base_url('admin/maghrib-mengaji/save-cell') ?>",
            type: "POST",
            data: {
                tahun: "<?= $tahunPilih ?>",
                bulan: bulan,
                field: field,
                value: value,
                <?= csrf_token() ?>: "<?= csrf_hash() ?>"
            },
            dataType: "json",
            success: function(response) {
                if(response.status === 'success') {
                    Toast.fire({ icon: 'success', title: 'Tersimpan.' });
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Terjadi kesalahan sistem', 'error');
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
