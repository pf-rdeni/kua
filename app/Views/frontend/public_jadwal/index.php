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
    <?php 
        $foto = '';
        if (!empty($mubaligh['foto']) && file_exists(FCPATH . 'uploads/personil/' . $mubaligh['foto'])) {
            $foto = base_url('uploads/personil/' . $mubaligh['foto']); 
        } else {
            $words = explode(' ', trim($mubaligh['nama_lengkap']));
            $initials = count($words) > 1 ? strtoupper(substr($words[0], 0, 1) . substr($words[count($words)-1], 0, 1)) : strtoupper(substr($words[0], 0, 1));
            $colors = ['#f56954', '#f39c12', '#00a65a', '#00c0ef', '#3c8dbc', '#605ca8', '#ff851b', '#39cccc'];
            $bgColor = $colors[crc32($mubaligh['nama_lengkap']) % count($colors)];
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><rect width="100" height="100" fill="'.$bgColor.'"/><text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" fill="#ffffff" font-family="Arial, sans-serif" font-size="40" font-weight="bold">'.$initials.'</text></svg>';
            $foto = 'data:image/svg+xml;base64,' . base64_encode($svg);
        }
    ?>
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

                $isTugasPengganti = (!empty($j['id_personil_pengganti']) && $j['id_personil_pengganti'] == $mubaligh['id'] && $j['id_personil_asli'] != $mubaligh['id']);

                $malamKe = intval($j['hari_ke']) + 1;
                $tglStr  = $j['tanggal'] ? tanggal_indo_panjang($j['tanggal']) : 'Belum Ditentukan';
                
                if ($isRamadhan) {
                    $badgeText = "Malam Ke-" . $malamKe;
                    $temaLabel = "Tema Ceramah:";
                    $temaValue = $j['tema'] ?: 'Menyesuaikan';
                } else if ($isMaghribMengaji) {
                    $badgeText = "Maghrib Mengaji";
                    $temaLabel = "Peran / Tugas:";
                    $temaValue = !empty($j['peran_petugas']) ? strtoupper($j['peran_petugas']) : 'PENGAJAR / IMAM';
                } else {
                    $badgeText = "Khotib Jumat";
                    $temaLabel = "Tugas Keagamaan:";
                    $temaValue = "Khotib Sholat Jumat";
                }

                // Styling berdasarkan status
                if ($j['status_kehadiran'] == 'hadir') {
                    $bgClass = "bg-success text-white";
                } else if ($j['status_kehadiran'] == 'diganti') {
                    $bgClass = $isTugasPengganti ? "bg-white border-warning" : "bg-warning";
                } else if ($j['status_kehadiran'] == 'tidak_hadir') {
                    $bgClass = "bg-danger text-white";
                }
            ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card jadwal-card <?= $bgClass ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                            <?php if ($isTugasPengganti): ?>
                                <span class="badge badge-info text-white font-weight-bold" style="font-size:14px;"><i class="fas fa-user-friends"></i> <?= esc($badgeText) ?></span>
                            <?php else: ?>
                                <span class="badge <?= $isRamadhan ? 'badge-light' : 'badge-warning' ?> text-dark font-weight-bold" style="font-size:14px;"><?= esc($badgeText) ?></span>
                            <?php endif; ?>
                            <span class="small font-weight-bold"><i class="far fa-clock"></i> <?= $tglStr ?></span>
                        </div>
                        
                        <h5 class="font-weight-bold mb-1"><i class="fas fa-mosque"></i> <?= esc($j['nama_masjid']) ?></h5>
                        <p class="text-sm mb-2"><i class="fas fa-map-marker-alt"></i> <?= esc($j['alamat_masjid']) ?></p>
                        
                        <?php if ($isTugasPengganti): ?>
                            <div class="p-2 bg-light text-dark rounded mb-3 text-center border border-info">
                                <small class="d-block text-info font-weight-bold"><i class="fas fa-exchange-alt"></i> Menggantikan Petugas Asli:</small>
                                <strong><?= esc(($j['nia_asli'] ? $j['nia_asli'] . ' - ' : '') . $j['nama_asli']) ?></strong>
                                <?php if (!empty($j['keterangan_absensi'])): ?>
                                    <small class="d-block text-muted mt-1 font-weight-normal">Alasan: <?= esc($j['keterangan_absensi']) ?></small>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="p-2 bg-light text-dark rounded mb-3 text-center">
                                <small class="d-block text-muted"><?= esc($temaLabel) ?></small>
                                <strong><?= esc($temaValue) ?></strong>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($j['status_kehadiran'] == 'hadir'): ?>
                            <div class="text-center font-weight-bold">
                                <i class="fas fa-check-circle fa-2x"></i><br>Telah Dikonfirmasi Hadir
                            </div>
                        <?php elseif ($isTugasPengganti): ?>
                            <!-- Pengganti belum konfirmasi hadir -->
                            <button class="btn btn-success btn-block btn-hadir font-weight-bold" data-id="<?= $j['id_jadwal'] ?>">
                                <i class="fas fa-check"></i> Konfirmasi Hadir (Pengganti)
                            </button>
                        <?php elseif ($j['status_kehadiran'] == 'diganti'): ?>
                            <div class="text-center font-weight-bold text-dark">
                                <i class="fas fa-exchange-alt fa-2x mb-1"></i><br>
                                Digantikan (Delegasi)<br>
                                <?php if (!empty($j['nama_pengganti'])): ?>
                                    <div class="mt-2 p-2 rounded bg-light border border-warning text-sm">
                                        <span class="text-muted d-block font-weight-normal"><i class="fas fa-user-check text-success"></i> Pengganti:</span>
                                        <strong><?= esc(($j['nia_pengganti'] ? $j['nia_pengganti'] . ' - ' : '') . $j['nama_pengganti']) ?></strong>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($j['keterangan_absensi'])): ?>
                                    <small class="d-block mt-1 text-muted font-weight-normal">Keterangan: <?= esc($j['keterangan_absensi']) ?></small>
                                <?php endif; ?>
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
                                    <button class="btn btn-outline-warning text-dark border-warning btn-block btn-delegasi" style="background-color: #fff;" data-id="<?= $j['id_jadwal'] ?>" data-kegiatan="<?= esc($badgeText) ?>" data-tanggal="<?= esc($tglStr) ?>" data-masjid="<?= esc($j['nama_masjid']) ?>">
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
        <h5 class="modal-title font-weight-bold"><i class="fas fa-exchange-alt"></i> Ajukan Pengganti</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info py-2 text-sm">
            Status pengajuan ini akan memindahkan jadwal <b id="lblKegiatan"></b> pada <b id="lblTanggal"></b> di <b id="lblMasjid"></b> ke Mubaligh / Khotib pengganti.
        </div>
        
        <form id="formDelegasi">
            <input type="hidden" id="del_id_jadwal" name="id_jadwal">
            <input type="hidden" name="token" value="<?= esc($token) ?>">
            
            <div class="form-group">
                <label>Pilih Mubaligh / Khotib Pengganti <span class="text-danger">*</span></label>
                <select class="form-control select2" id="id_pengganti" name="id_pengganti" style="width: 100%;">
                </select>
                <small class="text-muted">Ketik NIA atau nama untuk mencari. Sistem otomatis mencegah bentrok jadwal.</small>
            </div>
            
            <div class="form-group">
                <label>Alasan / Keterangan (Opsional)</label>
                <textarea class="form-control" name="alasan" rows="2" placeholder="Misal: Halangan dinas luar / kurang sehat..."></textarea>
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
    
    // Inisialisasi Select2 untuk pencarian Mubaligh Pengganti (Public Endpoint)
    $('.select2').select2({
        theme: 'bootstrap4',
        dropdownParent: $('#modalDelegasi'),
        placeholder: 'Pilih / Ketik NIA atau Nama Pengganti...',
        allowClear: true,
        ajax: {
            url: "<?= base_url('jadwal-mubaligh/search-pengganti') ?>?token=<?= esc($token) ?>", 
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term || ''
                };
            },
            processResults: function (data) {
                return {
                    results: data.results || []
                };
            },
            cache: true
        },
        minimumInputLength: 0
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
        let kegiatan = $(this).data('kegiatan');
        let tanggal  = $(this).data('tanggal');
        let masjid   = $(this).data('masjid');
        
        $('#del_id_jadwal').val(idJadwal);
        $('#lblKegiatan').text(kegiatan);
        $('#lblTanggal').text(tanggal);
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
            Swal.fire('Perhatian', 'Harap pilih mubaligh / khotib pengganti terlebih dahulu.', 'warning');
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
