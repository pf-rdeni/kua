<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jadwal Mubaligh | <?= esc($mubaligh['nama_lengkap']) ?></title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('template/backend/plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('template/backend/dist/css/adminlte.min.css') ?>">
    <!-- Select2 -->
    <link rel="stylesheet" href="<?= base_url('template/backend/plugins/select2/css/select2.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('template/backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="<?= base_url('template/backend/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') ?>">

    <style>
        body { background-color: #f4f6f9; }
        .jadwal-card { border-radius: 10px; border-top: 4px solid #28a745; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header-profile { background: linear-gradient(135deg, #1e7e34, #28a745); color: white; padding: 30px 20px; text-align: center; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; margin-bottom: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        .header-profile img { width: 100px; height: 100px; border-radius: 50%; border: 4px solid white; object-fit: cover; margin-bottom: 10px; background-color: #fff; }
        .status-badge { font-size: 0.9rem; padding: 5px 10px; }
    </style>
</head>
<body>

<div class="header-profile">
    <?php $foto = !empty($mubaligh['foto']) ? base_url('uploads/personil/' . $mubaligh['foto']) : base_url('dist/img/default-150x150.png'); ?>
    <img src="<?= esc($foto) ?>" alt="Foto Profil">
    <h4 class="mb-0 font-weight-bold"><?= esc($mubaligh['nama_lengkap']) ?></h4>
    <p class="mb-0 text-light"><i class="fas fa-calendar-alt"></i> Jadwal Kegiatan Keagamaan</p>
</div>

<div class="container pb-5">
    <?php if(empty($jadwal)): ?>
        <div class="alert alert-warning text-center">
            <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
            Anda belum memiliki jadwal kegiatan yang ditentukan.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach($jadwal as $j): ?>
            <?php 
                $bgClass = "bg-white"; 
                $isRamadhan = ($j['jenis_kegiatan'] == 'ramadhan');
                $isMaghribMengaji = ($j['jenis_kegiatan'] == 'maghrib_mengaji');
                $isJumat = ($j['jenis_kegiatan'] == 'jumat');

                $malamKe = intval($j['hari_ke']) + 1;
                $tglStr  = $j['tanggal'] ? tanggal_indo_panjang($j['tanggal']) : 'Belum Ditentukan';
                
                $badgeText = $isRamadhan ? "Malam Ke-" . $malamKe : ($isMaghribMengaji ? "Maghrib Mengaji" : "Khotib Jumat");
                $temaLabel = $isRamadhan ? "Tema Ceramah:" : "Peran Petugas:";
                $temaValue = $isRamadhan ? ($j['tema'] ?: 'Menyesuaikan') : strtoupper($j['peran_petugas']);
                // Styling berdasarkan status
                if ($j['status_kehadiran'] == 'hadir') {
                    $bgClass = "bg-success text-white";
                } else if ($j['status_kehadiran'] == 'diganti') {
                    $bgClass = "bg-warning";
                } else if ($j['status_kehadiran'] == 'tidak_hadir') {
                    $bgClass = "bg-danger text-white";
                }
            ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card jadwal-card <?= $bgClass ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                            <span class="badge <?= $isRamadhan ? 'badge-light' : 'badge-warning' ?> text-dark font-weight-bold" style="font-size:14px;"><?= esc($badgeText) ?></span>
                            <span class="small font-weight-bold"><i class="far fa-clock"></i> <?= $tglStr ?></span>
                        </div>
                        
                        <h5 class="font-weight-bold mb-1"><i class="fas fa-mosque"></i> <?= esc($j['nama_masjid']) ?></h5>
                        <p class="text-sm mb-2"><i class="fas fa-map-marker-alt"></i> <?= esc($j['alamat_masjid']) ?></p>
                        
                        <div class="p-2 bg-light text-dark rounded mb-3 text-center">
                            <small class="d-block text-muted"><?= esc($temaLabel) ?></small>
                            <strong><?= esc($temaValue) ?></strong>
                        </div>
                        
                        <?php if ($j['status_kehadiran'] == 'hadir'): ?>
                            <div class="text-center font-weight-bold">
                                <i class="fas fa-check-circle fa-2x"></i><br>Telah Dikonfirmasi Hadir
                            </div>
                        <?php elseif ($j['status_kehadiran'] == 'diganti'): ?>
                            <div class="text-center font-weight-bold text-dark">
                                <i class="fas fa-exchange-alt fa-2x mb-1"></i><br>
                                Digantikan (Delegasi)<br>
                                <small>Keterangan: <?= esc($j['keterangan_absensi']) ?></small>
                            </div>
                        <?php elseif ($j['status_kehadiran'] == 'tidak_hadir'): ?>
                            <div class="text-center font-weight-bold">
                                <i class="fas fa-times-circle fa-2x"></i><br>Dilaporkan Tidak Hadir
                            </div>
                        <?php else: ?>
                            <!-- Aksi jika belum diabsen -->
                            <div class="row">
                                <div class="col-6 pr-1">
                                    <button class="btn btn-success btn-block btn-hadir" data-id="<?= $j['id_jadwal'] ?>">
                                        <i class="fas fa-check"></i> Hadir
                                    </button>
                                </div>
                                <div class="col-6 pl-1">
                                    <button class="btn btn-outline-warning text-dark border-warning btn-block btn-delegasi" style="background-color: #fff;" data-id="<?= $j['id_jadwal'] ?>" data-malam="<?= $malamKe ?>" data-masjid="<?= esc($j['nama_masjid']) ?>">
                                        <i class="fas fa-user-friends"></i> Delegasikan
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Delegasi / Cari Pengganti -->
<div class="modal fade" id="modalDelegasi" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title font-weight-bold"><i class="fas fa-exchange-alt"></i> Ajukan Penceramah Pengganti</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info py-2 text-sm">
            Status pengajuan ini akan memindahkan jadwal Anda pada <b id="lblMalam"></b> di <b id="lblMasjid"></b> ke Mubaligh pengganti.
        </div>
        
        <form id="formDelegasi">
            <input type="hidden" id="del_id_jadwal" name="id_jadwal">
            <input type="hidden" name="token" value="<?= esc($token) ?>">
            
            <div class="form-group">
                <label>Pilih Mubaligh Pengganti <span class="text-danger">*</span></label>
                <select class="form-control select2" id="id_pengganti" name="id_pengganti" style="width: 100%;">
                    <!-- Diisi via AJAX dari endpoint search mubaligh admin -->
                </select>
                <small class="text-muted">Ketik nama untuk mencari. Sistem otomatis mencegah bentrok jadwal.</small>
            </div>
            
            <div class="form-group">
                <label>Alasan / Keterangan (Opsional)</label>
                <textarea class="form-control" name="alasan" rows="2" placeholder="Misal: Sedang kurang sehat..."></textarea>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-warning font-weight-bold" id="btnSubmitDelegasi">Ajukan Pengganti</button>
      </div>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="<?= base_url('template/backend/plugins/jquery/jquery.min.js') ?>"></script>
<!-- Bootstrap 4 -->
<script src="<?= base_url('template/backend/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- Select2 -->
<script src="<?= base_url('template/backend/plugins/select2/js/select2.full.min.js') ?>"></script>
<!-- SweetAlert2 -->
<script src="<?= base_url('template/backend/plugins/sweetalert2/sweetalert2.min.js') ?>"></script>

<script>
$(document).ready(function() {
    
    // Inisialisasi Select2 untuk pencarian Mubaligh
    $('.select2').select2({
        theme: 'bootstrap4',
        dropdownParent: $('#modalDelegasi'),
        placeholder: 'Ketik nama penceramah...',
        ajax: {
            // Kita numpang pakai endpoint pencarian mubaligh yang sudah ada di admin
            url: "<?= base_url('admin/jadwal-ramadhan/search-mubaligh') ?>", 
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 10) < data.total_count
                    }
                };
            },
            cache: true
        },
        minimumInputLength: 3, // Minimal 3 huruf baru nyari ke server
    });

    // Aksi Konfirmasi Hadir
    $('.btn-hadir').click(function() {
        let idJadwal = $(this).data('id');
        
        Swal.fire({
            title: 'Konfirmasi Kehadiran',
            text: "Apakah Anda menyatakan bersedia hadir untuk mengisi jadwal ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Saya Akan Hadir',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('jadwal-mubaligh/konfirmasi-hadir') ?>",
                    type: "POST",
                    data: {
                        id_jadwal: idJadwal,
                        token: "<?= esc($token) ?>"
                    },
                    success: function(res) {
                        if(res.status === 'success') {
                            Swal.fire('Berhasil!', res.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                    }
                });
            }
        });
    });

    // Menampilkan Modal Delegasi
    $('.btn-delegasi').click(function() {
        let idJadwal = $(this).data('id');
        let malam = $(this).data('malam');
        let masjid = $(this).data('masjid');
        
        $('#del_id_jadwal').val(idJadwal);
        $('#lblMalam').text('Malam Ke-' + malam);
        $('#lblMasjid').text(masjid);
        
        // Kosongkan form jika bekas dipakai
        $('#id_pengganti').val(null).trigger('change');
        $('textarea[name="alasan"]').val('');
        
        $('#modalDelegasi').modal('show');
    });

    // Aksi Submit Delegasi
    $('#btnSubmitDelegasi').click(function() {
        let nullCheck = $('#id_pengganti').val();
        if(!nullCheck) {
            Swal.fire('Perhatian', 'Harap pilih mubaligh pengganti terlebih dahulu.', 'warning');
            return;
        }

        let formData = $('#formDelegasi').serialize();
        
        // Disable tombol biar gak double click
        $(this).attr('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
        
        $.ajax({
            url: "<?= base_url('jadwal-mubaligh/ajukan-pengganti') ?>",
            type: "POST",
            data: formData,
            success: function(res) {
                if(res.status === 'success') {
                    $('#modalDelegasi').modal('hide');
                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', res.message, 'error');
                    $('#btnSubmitDelegasi').attr('disabled', false).html('Ajukan Pengganti');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                $('#btnSubmitDelegasi').attr('disabled', false).html('Ajukan Pengganti');
            }
        });
    });
});
</script>
</body>
</html>
