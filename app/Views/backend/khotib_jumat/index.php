<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card card-info card-outline mt-4">
            <div class="card-header">
                <h3 class="card-title">Matriks <?= esc($pageTitle) ?></h3>
            </div>
            <div class="card-body">

                <!-- Filter Tahun & Kuartal -->
                <form action="<?= base_url('admin/khotib-jumat') ?>" method="get" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Tahun Masehi</label>
                            <input type="number" class="form-control" name="tahun" value="<?= esc($tahunPilih) ?>" min="2020" max="2100" required>
                        </div>
                        <div class="col-md-3">
                            <label>Triwulan / Kuartal</label>
                            <select name="kuartal" class="form-control">
                                <option value="1" <?= $kuartalPilih == 1 ? 'selected' : '' ?>>Kuartal 1 (Jan - Mar)</option>
                                <option value="2" <?= $kuartalPilih == 2 ? 'selected' : '' ?>>Kuartal 2 (Apr - Jun)</option>
                                <option value="3" <?= $kuartalPilih == 3 ? 'selected' : '' ?>>Kuartal 3 (Jul - Sep)</option>
                                <option value="4" <?= $kuartalPilih == 4 ? 'selected' : '' ?>>Kuartal 4 (Okt - Des)</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary" type="submit" style="width: 100%;">
                                <i class="fas fa-search"></i> Tampilkan
                            </button>
                        </div>
                    </div>
                </form>

                <div class="row mb-3">
                    <div class="col-12 text-right">
                        <div class="btn-group mr-2">
                            <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-file-export"></i> Export Jadwal
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="<?= base_url('admin/khotib-jumat/export-excel?tahun=' . esc($tahunPilih) . '&kuartal=' . esc($kuartalPilih)) ?>" target="_blank">
                                    <i class="fas fa-file-excel text-success mr-2"></i> Export Excel
                                </a>
                                <a class="dropdown-item" href="<?= base_url('admin/khotib-jumat/export-pdf?tahun=' . esc($tahunPilih) . '&kuartal=' . esc($kuartalPilih)) ?>" target="_blank">
                                    <i class="fas fa-file-pdf text-danger mr-2"></i> Export PDF
                                </a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success mr-2" data-toggle="modal" data-target="#modalWaMubaligh">
                            <i class="fab fa-whatsapp"></i> Kirim Jadwal Khotib (WA)
                        </button>
                        <button type="button" class="btn btn-sm btn-primary mr-2" data-toggle="modal" data-target="#modalCetakMubaligh">
                            <i class="fas fa-user text-white"></i> Cetak Jadwal Khotib
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalCetakMasjid">
                            <i class="fas fa-mosque"></i> Cetak Jadwal Masjid
                        </button>
                    </div>
                </div>

                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-info"></i> Petunjuk!</h5>
                    Pilih nama Khotib pada kolom tanggal Jumat. Data akan tersimpan **otomatis** setiap kali nama Khotib dipilih.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm text-center align-middle" style="min-width: 1500px;">
                        <thead class="bg-info" style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th class="align-middle" rowspan="2" style="width: 50px;">NO</th>
                                <th class="align-middle" rowspan="2" style="width: 250px; position: sticky; left: 0; z-index: 11; background-color: #17a2b8;">NAMA MASJID</th>
                                
                                <?php
                                    // Group fridays by Month to show month headers safely
                                    $months = [];
                                    foreach ($fridays as $f) {
                                        $m = date('F', strtotime($f));
                                        if(!isset($months[$m])) $months[$m] = 0;
                                        $months[$m]++;
                                    }
                                ?>
                                <?php foreach ($months as $monthName => $colspan): ?>
                                    <th colspan="<?= $colspan ?>"><?= strtoupper($monthName) ?></th>
                                <?php endforeach; ?>
                            </tr>
                            <tr>
                                <?php foreach($fridays as $tgl): ?>
                                    <th><?= date('d', strtotime($tgl)) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($masjidList as $m): ?>
                                <tr>
                                    <td class="align-middle"><?= $no++ ?></td>
                                    <td class="align-middle text-left font-weight-bold" style="position: sticky; left: 0; background-color: #f8f9fa; z-index: 2;">
                                        <?= esc($m['nama']) ?>
                                    </td>
                                    
                                    <?php foreach ($fridays as $tgl): ?>
                                        <td class="align-middle" style="width: 150px;">
                                            <select class="form-control select2-petugas" 
                                                    data-masjid="<?= $m['id_masjid_mushola'] ?>" 
                                                    data-tanggal="<?= $tgl ?>" 
                                                    style="width: 100%;">
                                                    
                                                <?php 
                                                    $selectedId = $matrixIds[$m['id_masjid_mushola']][$tgl] ?? ''; 
                                                ?>
                                                <option value="">-- Pilih --</option>
                                                <?php if (!empty($selectedId)): ?>
                                                    <option value="<?= $selectedId ?>" selected 
                                                            data-nama="<?= esc($personilNames[$selectedId]['nama'] ?? '') ?>" 
                                                            data-foto="<?= esc($personilNames[$selectedId]['foto'] ?? '') ?>">
                                                        <?= esc($personilNames[$selectedId]['nama'] ?? '') ?>
                                                    </option>
                                                <?php endif; ?>
                                            </select>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Kirim WA Khotib -->
<div class="modal fade" id="modalWaMubaligh" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fab fa-whatsapp"></i> Kirim Jadwal Khotib via WhatsApp</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Pilih Khotib</label>
                    <select class="form-control" id="selectWaMubaligh" style="width: 100%;"></select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnProsesWaMubaligh"><i class="fas fa-eye"></i> Lihat Jadwal</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview WA -->
<div class="modal fade" id="modalWaPreview" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fab fa-whatsapp"></i> Format Pesan WhatsApp</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>No. WhatsApp Khotib:</label>
                    <input type="text" class="form-control" id="waNoHpDisplay" readonly>
                </div>
                <div class="form-group">
                    <label>Teks Pesan:</label>
                    <textarea class="form-control" id="waTextPreview" rows="10" readonly style="font-family: monospace; background-color: #f8f9fa;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-info" id="btnCopyWaText"><i class="fas fa-copy"></i> Salin Teks</button>
                <a href="#" target="_blank" class="btn btn-success" id="btnOpenWaLink"><i class="fab fa-whatsapp"></i> Buka WhatsApp</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cetak Khotib -->
<div class="modal fade" id="modalCetakMubaligh" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cetak Jadwal per Khotib</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Pilih Khotib</label>
                    <select class="form-control" id="selectCetakMubaligh" style="width: 100%;"></select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnProsesCetakMubaligh"><i class="fas fa-print"></i> Cetak</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cetak Masjid -->
<div class="modal fade" id="modalCetakMasjid" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cetak Jadwal per Masjid</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Pilih Masjid/Mushola</label>
                    <select class="form-control select2-modal-masjid" id="selectCetakMasjid" style="width: 100%;">
                        <option value="">-- Pilih Masjid --</option>
                        <?php foreach($masjidList as $m): ?>
                            <option value="<?= $m['id_masjid_mushola'] ?>"><?= esc($m['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnProsesCetakMasjid"><i class="fas fa-print"></i> Cetak</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    
    function getInitials(name) {
        if (!name) return '';
        if (name.indexOf(' - ') !== -1) {
            name = name.split(' - ').slice(1).join(' - ');
        }
        var parts = name.trim().split(' ');
        var initials = '';
        if (parts.length > 0) initials += parts[0].charAt(0);
        if (parts.length > 1) initials += parts[1].charAt(0);
        return initials.toUpperCase();
    }

    function createAvatarHtml(mubaligh, sizeClass) {
        var size = sizeClass === 'small' ? '20px' : '25px';
        var fontSize = sizeClass === 'small' ? '10px' : '12px';
        var namaStr = mubaligh.nama || mubaligh.text || '';
        
        // Cek jika fotonya adalah foto default sistem
        if (mubaligh.foto && !mubaligh.foto.includes('default-')) {
            return '<img src="' + mubaligh.foto + '" class="img-circle mr-2" style="width: '+size+'; height: '+size+'; object-fit: cover;" />';
        } else {
            // Buat avatar inisial dengan CSS
            var initials = getInitials(namaStr);
            // Generate warna random berdasarkan nama agar konsisten
            var colors = ['#007bff', '#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6610f2', '#e83e8c', '#fd7e14'];
            var colorIndex = namaStr.length % colors.length;
            var bgColor = colors[colorIndex];
            
            return '<span class="img-circle mr-2" style="display: inline-block; width: '+size+'; height: '+size+'; background-color: '+bgColor+'; color: white; text-align: center; line-height: '+size+'; font-size: '+fontSize+'; font-weight: bold;">' + initials + '</span>';
        }
    }

    function formatMubaligh (mubaligh) {
        if (!mubaligh.id) {
            return mubaligh.text;
        }
        var displayName = mubaligh.nama || mubaligh.text;
        var avatarHtml = createAvatarHtml({foto: mubaligh.foto, nama: displayName}, 'normal');
        var $mubaligh = $(
            '<span>' + avatarHtml + ' ' + displayName + '</span>'
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

    // Inisialisasi Select2 AJAX
    $('.select2-petugas').select2({
        theme: 'bootstrap4',
        placeholder: 'Pilih Khotib...',
        allowClear: true,
        ajax: {
            url: "<?= base_url('admin/khotib-jumat/search-mubaligh') ?>",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var el = $(this);
                return {
                    q: params.term,
                    tanggal: el.data('tanggal')
                };
            },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true
        },
        minimumInputLength: 0,
        templateResult: formatMubaligh,
        templateSelection: formatMubalighSelection
    });

    // Auto Save via AJAX saat personil dipilih/diubah
    $('.select2-petugas').on('change', function() {
        var idMasjid = $(this).data('masjid');
        var tanggal = $(this).data('tanggal');
        var idMubaligh = $(this).val();

        // Tampilkan loading kecil
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        $.ajax({
            url: "<?= base_url('admin/khotib-jumat/save-cell') ?>",
            type: "POST",
            data: {
                id_masjid: idMasjid,
                tanggal: tanggal,
                id_mubaligh: idMubaligh,
                <?= csrf_token() ?>: "<?= csrf_hash() ?>"
            },
            dataType: "json",
            success: function(response) {
                if(response.status === 'success') {
                    Toast.fire({ icon: 'success', title: 'Tersimpan.' });
                    console.log("SQL Debug:", response.debug_query);
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                    // Reset value jika terjadi bentrok/error
                    setTimeout(() => { location.reload(); }, 1500);
                }
            },
            error: function() {
                Swal.fire('Error!', 'Terjadi kesalahan sistem', 'error');
            }
        });
    });

    // Inisialisasi Select2 untuk Modal WA Khotib
    $('#selectWaMubaligh').select2({
        theme: 'bootstrap4',
        placeholder: 'Ketik NIP / NIA / Nama Khotib...',
        dropdownParent: $('#modalWaMubaligh'),
        ajax: {
            url: "<?= base_url('admin/khotib-jumat/search-mubaligh') ?>",
            dataType: 'json',
            delay: 250,
            data: function (params) { return { q: params.term }; },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true
        },
        templateResult: formatMubaligh,
        templateSelection: formatMubalighSelection
    });

    // Inisialisasi Select2 untuk Modal Cetak Khotib
    $('#selectCetakMubaligh').select2({
        theme: 'bootstrap4',
        placeholder: 'Ketik NIP / NIA / Nama Khotib...',
        dropdownParent: $('#modalCetakMubaligh'),
        ajax: {
            url: "<?= base_url('admin/khotib-jumat/search-mubaligh') ?>",
            dataType: 'json',
            delay: 250,
            data: function (params) { return { q: params.term }; },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true
        },
        templateResult: formatMubaligh,
        templateSelection: formatMubalighSelection
    });

    // Inisialisasi Select2 untuk Modal Cetak Masjid
    $('#selectCetakMasjid').select2({
        theme: 'bootstrap4',
        dropdownParent: $('#modalCetakMasjid')
    });

    // Action Kirim WA Khotib
    $('#btnProsesWaMubaligh').click(function() {
        var id = $('#selectWaMubaligh').val();
        if(!id) { Swal.fire('Peringatan', 'Silakan pilih Khotib terlebih dahulu', 'warning'); return; }

        $.ajax({
            url: "<?= base_url('admin/khotib-jumat/get-wa-mubaligh') ?>",
            type: "GET",
            data: {
                id_personil: id,
                tahun: "<?= esc($tahunPilih) ?>",
                kuartal: "<?= esc($kuartalPilih) ?>"
            },
            dataType: "json",
            success: function(response) {
                if(response.status === 'success') {
                    $('#modalWaMubaligh').modal('hide');
                    $('#waNoHpDisplay').val(response.no_hp).data('hp', response.hp_formatted);
                    $('#waTextPreview').val(response.pesan);
                    
                    var waPhone = response.hp_formatted || '';
                    var waUrl = 'https://api.whatsapp.com/send?' + (waPhone ? 'phone=' + waPhone + '&' : '') + 'text=' + encodeURIComponent(response.pesan);
                    $('#btnOpenWaLink').attr('href', waUrl);
                    $('#modalWaPreview').modal('show');
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Gagal mengambil data jadwal Khotib', 'error');
            }
        });
    });

    // Dynamic URL update on click Buka WhatsApp
    $('#btnOpenWaLink').click(function(e) {
        var text = $('#waTextPreview').val();
        var phone = $('#waNoHpDisplay').data('hp') || '';
        var url = 'https://api.whatsapp.com/send?' + (phone ? 'phone=' + phone + '&' : '') + 'text=' + encodeURIComponent(text);
        $(this).attr('href', url);
    });

    // Copy Text WA
    $('#btnCopyWaText').click(function() {
        var copyText = document.getElementById("waTextPreview");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(copyText.value).then(function() {
                Swal.fire({ icon: 'success', title: 'Tersalin!', text: 'Teks pesan WhatsApp berhasil disalin.', timer: 2000, showConfirmButton: false });
            });
        } else {
            document.execCommand("copy");
            Swal.fire({ icon: 'success', title: 'Tersalin!', text: 'Teks pesan WhatsApp berhasil disalin.', timer: 2000, showConfirmButton: false });
        }
    });

    // Action Cetak
    $('#btnProsesCetakMubaligh').click(function() {
        var id = $('#selectCetakMubaligh').val();
        if(!id) { Swal.fire('Peringatan', 'Silakan pilih Khotib terlebih dahulu', 'warning'); return; }
        window.open('<?= base_url('admin/khotib-jumat/cetak-mubaligh') ?>/' + id + '?tahun=<?= esc($tahunPilih) ?>', '_blank');
        $('#modalCetakMubaligh').modal('hide');
    });

    $('#btnProsesCetakMasjid').click(function() {
        var id = $('#selectCetakMasjid').val();
        if(!id) { Swal.fire('Peringatan', 'Silakan pilih Masjid terlebih dahulu', 'warning'); return; }
        window.open('<?= base_url('admin/khotib-jumat/cetak-masjid') ?>/' + id + '?tahun=<?= esc($tahunPilih) ?>', '_blank');
        $('#modalCetakMasjid').modal('hide');
    });

});
</script>
<?= $this->endSection(); ?>
