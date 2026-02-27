<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<style>
    .table-jadwal th, .table-jadwal td {
        vertical-align: middle;
    }
    .col-sticky-1 {
        position: sticky;
        left: 0;
        background-color: #f4f6f9;
        z-index: 2;
    }
    .col-sticky-2 {
        position: sticky;
        left: 250px;
        background-color: #f4f6f9;
        z-index: 2;
    }
    .col-sticky-3 {
        position: sticky;
        left: 400px;
        background-color: #f4f6f9;
        z-index: 2;
        border-right: 2px solid #dee2e6;
    }
    tbody .col-sticky-1, tbody .col-sticky-2, tbody .col-sticky-3 {
        background-color: #fff;
    }
    .table-jadwal thead th {
        background-color: #f4f6f9;
        z-index: 1; /* lower than sticky columns if needed, but sticky th needs higher */
    }
    .table-jadwal thead th.col-sticky-1, 
    .table-jadwal thead th.col-sticky-2, 
    .table-jadwal thead th.col-sticky-3 {
        z-index: 3;
    }
    .mubaligh-card {
        display: flex;
        align-items: center;
        margin-top: 5px;
        background: #f8f9fa;
        padding: 4px;
        border-radius: 4px;
        border: 1px solid #ced4da;
    }
    .mubaligh-card img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 8px;
    }
    .mubaligh-card .info {
        font-size: 0.8rem;
        line-height: 1.2;
    }
    .mubaligh-card .info strong {
        display: block;
    }
    /* Select2 Container Overrides */
    .select2-container .select2-selection--single {
        height: 30px !important;
    }
    .table-container {
        max-height: 70vh;
        overflow-y: auto;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">Matriks Jadwal Ceramah Ramadhan <?= esc($tahunHijriah) ?></h3>
                <div class="card-tools">
                    <form action="" method="get" class="form-inline">
                        <input type="date" class="form-control form-control-sm mr-2 input-tgl-mulai" 
                               data-tahun="<?= esc($tahunHijriah) ?>" 
                               value="<?= isset($tanggals[1]) ? esc($tanggals[1]) : '' ?>" 
                               title="Set Tanggal 1 Ramadhan untuk Generate Otomatis">
                        <select name="tahun" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                            <option value="1445 H" <?= $tahunHijriah == '1445 H' ? 'selected' : '' ?>>1445 H</option>
                            <option value="1446 H" <?= $tahunHijriah == '1446 H' ? 'selected' : '' ?>>1446 H</option>
                            <option value="1447 H" <?= $tahunHijriah == '1447 H' ? 'selected' : '' ?>>1447 H</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-danger mr-2" id="btnResetJadwal"><i class="fas fa-trash"></i> Reset Jadwal</button>
                        <button type="button" class="btn btn-sm btn-info mr-2" data-toggle="modal" data-target="#modalDuplikatJadwal"><i class="fas fa-copy"></i> Duplikat Jadwal</button>
                        <button type="button" class="btn btn-sm btn-primary mr-2" data-toggle="modal" data-target="#modalCetakMubaligh"><i class="fas fa-user"></i> Cetak Jadwal Mubaligh</button>
                        <button type="button" class="btn btn-sm btn-warning mr-2" data-toggle="modal" data-target="#modalCetakMasjid"><i class="fas fa-mosque"></i> Cetak Jadwal Masjid</button>
                        <button type="button" class="btn btn-sm btn-info" id="btnExportExcel"><i class="fas fa-file-excel"></i> Export Excel</button>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive table-container">
                    <table class="table table-bordered table-hover table-jadwal text-sm" id="matriksTable" style="min-width: 2000px;">
                        <thead>
                            <tr>
                                <th class="col-sticky-1 text-center" style="width: 250px; min-width: 250px;">Nama Masjid/Mushola</th>
                                <th class="col-sticky-2 text-center" style="width: 150px; min-width: 150px;">Desa/Kelurahan</th>
                                <th class="col-sticky-3 text-center" style="width: 200px; min-width: 200px;">Alamat Lengkap</th>
                                <?php for($i=1; $i<=30; $i++): ?>
                                <th class="text-center" style="width: 200px; min-width: 200px;">
                                    <?= $i ?> Ramadhan <?= esc($tahunHijriah) ?><br>
                                    <small class="text-muted font-weight-normal">
                                        <?= isset($tanggals[$i]) && $tanggals[$i] ? esc(tanggal_indo_panjang($tanggals[$i])) : '-' ?>
                                    </small>
                                </th>
                                <?php endfor; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($masjidList as $m): ?>
                            <tr>
                                <td class="col-sticky-1 font-weight-bold"><?= esc($m['nama']) ?></td>
                                <td class="col-sticky-2"><?= esc($m['kelurahan_desa']) ?></td>
                                <td class="col-sticky-3"><?= esc($m['alamat']) ?></td>
                                <?php for($i=1; $i<=30; $i++): ?>
                                    <?php 
                                        $cellData = $matriks[$m['id_masjid_mushola']][$i] ?? null; 
                                        $selectedId = $cellData ? $cellData['id_personil'] : '';
                                        $selectedText = $cellData ? ($cellData['nia_mubaligh'] . ' - ' . $cellData['nama_mubaligh']) : '';
                                        $foto = $cellData && $cellData['foto_mubaligh'] ? base_url('uploads/personil/' . $cellData['foto_mubaligh']) : base_url('dist/img/default-150x150.png');
                                    ?>
                                <td>
                                    <select class="form-control select2-mubaligh" 
                                            data-masjid="<?= $m['id_masjid_mushola'] ?>" 
                                            data-hari="<?= $i ?>"
                                            style="width: 100%;">
                                        <?php if ($selectedId): ?>
                                            <option value="<?= $selectedId ?>" selected="selected"><?= esc($selectedText) ?></option>
                                        <?php endif; ?>
                                    </select>
                                    
                                    <div class="mubaligh-card" id="card-<?= $m['id_masjid_mushola'] ?>-<?= $i ?>" style="<?= $selectedId ? '' : 'display:none;' ?>">
                                        <img src="<?= $selectedId ? $foto : '' ?>" alt="Foto" id="foto-<?= $m['id_masjid_mushola'] ?>-<?= $i ?>">
                                        <div class="info">
                                            <strong id="nama-<?= $m['id_masjid_mushola'] ?>-<?= $i ?>"><?= $cellData['nama_mubaligh'] ?? '' ?></strong>
                                            <span class="text-muted" id="nia-<?= $m['id_masjid_mushola'] ?>-<?= $i ?>">ID: <?= $cellData['nia_mubaligh'] ?? '' ?></span>
                                        </div>
                                    </div>
                                </td>
                                <?php endfor; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cetak Mubaligh -->
<div class="modal fade" id="modalCetakMubaligh" tabindex="-1" role="dialog" aria-labelledby="modalCetakMubalighLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCetakMubalighLabel">Cetak Jadwal Per Mubaligh</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group text-left">
            <label>Cari & Pilih Mubaligh</label>
            <select class="form-control select2-cetak-mubaligh" id="selectCetakMubaligh" style="width: 100%;"></select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btnSubmitCetakMubaligh"><i class="fas fa-print"></i> Cetak Tab Baru</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Cetak Masjid/Mushola -->
<div class="modal fade" id="modalCetakMasjid" tabindex="-1" role="dialog" aria-labelledby="modalCetakMasjidLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCetakMasjidLabel">Cetak Jadwal Per Masjid/Mushola</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group text-left">
            <label>Pilih Masjid/Mushola</label>
            <select class="form-control select2-cetak-masjid" id="selectCetakMasjid" style="width: 100%;">
                <option value="">Pilih Lokasi...</option>
                <?php foreach($masjidList as $m): ?>
                    <option value="<?= $m['id_masjid_mushola'] ?>"><?= esc($m['nama']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-warning" id="btnSubmitCetakMasjid"><i class="fas fa-print"></i> Cetak Tab Baru</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Duplikat Jadwal -->
<div class="modal fade" id="modalDuplikatJadwal" tabindex="-1" role="dialog" aria-labelledby="modalDuplikatJadwalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDuplikatJadwalLabel">Duplikat Matriks Jadwal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Anda akan menduplikasi <strong>seluruh susunan matriks jadwal</strong> dari tahun <strong class="text-primary"><?= esc($tahunHijriah) ?></strong> ke tahun tujuan.</p>
        <p class="text-danger"><small><i class="fas fa-exclamation-triangle"></i> Peringatan: Matriks jadwal di tahun tujuan akan <b>ditimpa/dihapus</b> dan diganti dengan data ini.</small></p>
        
        <div class="form-group text-left">
            <label>Salin Ke Tahun Tujuan</label>
            <select class="form-control" id="selectTargetTahunJadwal">
                <option value="">Pilih Tahun...</option>
                <?php $opts = ['1445 H', '1446 H', '1447 H']; ?>
                <?php foreach($opts as $opt): ?>
                    <?php if($opt != $tahunHijriah): ?>
                        <option value="<?= $opt ?>"><?= $opt ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-info" id="btnSubmitDuplikatJadwal"><i class="fas fa-copy"></i> Mulai Duplikasi</button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // JS for Tanggal Mulai Generation
    $('.input-tgl-mulai').on('change', function() {
        var tgl = $(this).val();
        var tahun = $(this).data('tahun');
        if(!tgl) return;
        
        Swal.fire({
            title: 'Generate Tanggal?',
            text: "Tanggal hari ke-2 sampai ke-30 akan disesuaikan otomatis berdasarkan tanggal awal ini.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Generate!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('admin/jadwal-ramadhan/generate-tanggal') ?>',
                    type: 'POST',
                    data: { 
                        tahun_hijriah: tahun, 
                        tanggal_mulai: tgl,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    success: function(res) {
                        if(res.status === 'success') {
                            Swal.fire({
                                icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan koneksi', 'error');
                    }
                });
            } else {
                // If cancelled, reset value if possible (optional)
            }
        });
    });
    // Initialize Select2 Make sure css and js are loaded properly in the layout
    // If AdminLTE layout doesn't have it we might need to load it but assuming it's available because other pages use it.
    
    function initSelect2() {
        $('.select2-mubaligh').select2({
            placeholder: "Pilih Mubaligh...",
            allowClear: true,
            ajax: {
                url: '<?= base_url('admin/jadwal-ramadhan/search-mubaligh') ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var el = $(this);
                    return {
                        q: params.term,
                        hari_ke: el.data('hari'),
                        tahun_hijriah: '<?= esc($tahunHijriah) ?>'
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            },
            templateResult: formatMubalighOption
        });
    }

    // Format option when selecting (in dropdown)
    function formatMubalighOption (mubaligh) {
        if (!mubaligh.id) {
            return mubaligh.text;
        }
        var photoUrl = mubaligh.foto ? mubaligh.foto : '<?= base_url('dist/img/default-150x150.png') ?>';
        var $container = $(
            "<div class='d-flex align-items-center'>" +
                "<img src='" + photoUrl + "' style='width:30px;height:30px;object-fit:cover;border-radius:50%;margin-right:10px;' />" +
                "<div>" + mubaligh.text + "</div>" +
            "</div>"
        );
        return $container;
    }

    initSelect2();

    // On Change event to save to DB via AJAX and update card
    $('.select2-mubaligh').on('change', function(e) {
        var id_masjid = $(this).data('masjid');
        var hari_ke = $(this).data('hari');
        var tahun_hijriah = '<?= esc($tahunHijriah) ?>';
        var id_personil = $(this).val();

        var cardContainer = $('#card-' + id_masjid + '-' + hari_ke);
        
        $.ajax({
            url: '<?= base_url('admin/jadwal-ramadhan/save-cell') ?>',
            type: 'POST',
            data: {
                id_masjid: id_masjid,
                hari_ke: hari_ke,
                tahun_hijriah: tahun_hijriah,
                id_personil: id_personil
            },
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    if (res.personil) {
                        $('#foto-' + id_masjid + '-' + hari_ke).attr('src', res.personil.foto);
                        $('#nama-' + id_masjid + '-' + hari_ke).text(res.personil.nama);
                        $('#nia-' + id_masjid + '-' + hari_ke).text('ID: ' + (res.personil.nia || ''));
                        cardContainer.show();
                    } else {
                        cardContainer.hide();
                    }
                } else {
                    Swal.fire('Gagal', 'Gagal menyimpan jadwal', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan koneksi', 'error');
            }
        });
    });

    $('#btnExportExcel').on('click', function() {
        window.location.href = '<?= base_url('admin/jadwal-ramadhan/export-excel') ?>?tahun=<?= esc($tahunHijriah) ?>';
    });

    // Modal select2 inisialisasi
    $('.select2-cetak-mubaligh').select2({
        placeholder: "Ketik Nama / NIA Khotib/Mubaligh...",
        allowClear: true,
        dropdownParent: $('#modalCetakMubaligh'),
        ajax: {
            url: '<?= base_url('admin/jadwal-ramadhan/search-mubaligh') ?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true
        }
    });

    $('.select2-cetak-masjid').select2({
        dropdownParent: $('#modalCetakMasjid')
    });

    // Action clicks
    $('#btnSubmitCetakMubaligh').on('click', function() {
        var id_personil = $('#selectCetakMubaligh').val();
        if(!id_personil) {
            Swal.fire('Peringatan', 'Silakan pilih mubaligh terlebih dahulu!', 'warning');
            return;
        }
        var url = '<?= base_url('admin/jadwal-ramadhan/cetak-mubaligh') ?>/' + id_personil + '?tahun=<?= esc($tahunHijriah) ?>';
        window.open(url, '_blank');
        $('#modalCetakMubaligh').modal('hide');
    });

    $('#btnSubmitCetakMasjid').on('click', function() {
        var id_masjid = $('#selectCetakMasjid').val();
        if(!id_masjid) {
            Swal.fire('Peringatan', 'Silakan pilih masjid/mushola terlebih dahulu!', 'warning');
            return;
        }
        var url = '<?= base_url('admin/jadwal-ramadhan/cetak-masjid') ?>/' + id_masjid + '?tahun=<?= esc($tahunHijriah) ?>';
        window.open(url, '_blank');
        $('#modalCetakMasjid').modal('hide');
    });

    $('#btnResetJadwal').on('click', function() {
        Swal.fire({
            title: 'Reset Semua Jadwal?',
            text: "Seluruh jadwal mubaligh tahun <?= esc($tahunHijriah) ?> akan dikosongkan secara permanen! Aksi ini tidak bisa dibatalkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Reset Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('admin/jadwal-ramadhan/reset-jadwal') ?>',
                    type: 'POST',
                    data: {
                        tahun_hijriah: '<?= esc($tahunHijriah) ?>',
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Terhapus!', res.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Oops...', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error Function!', 'Terjadi kesalahan sistem/jaringan.', 'error');
                    }
                });
            }
        });
    });

    // Duplikat Jadwal
    $('#btnSubmitDuplikatJadwal').on('click', function() {
        var to_year = $('#selectTargetTahunJadwal').val();
        var from_year = '<?= esc($tahunHijriah) ?>';

        if (!to_year) {
            Swal.fire('Peringatan', 'Pilih tahun tujuan terlebih dahulu!', 'warning');
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Proses...');

        $.ajax({
            url: '<?= base_url('admin/jadwal-ramadhan/duplicate-jadwal') ?>',
            type: 'POST',
            data: {
                from_year: from_year,
                to_year: to_year,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(res) {
                if(res.status === 'success') {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                        window.location.href = '<?= base_url('admin/jadwal-ramadhan') ?>?tahun=' + encodeURIComponent(to_year);
                    });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-copy"></i> Mulai Duplikasi');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                btn.prop('disabled', false).html('<i class="fas fa-copy"></i> Mulai Duplikasi');
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
