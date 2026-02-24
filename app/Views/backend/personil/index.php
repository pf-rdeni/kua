 <?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<?php
$pageTitle = 'Data ' . $entitasConfig['nama_label'];
$breadcrumb = [
    ['title' => 'Home', 'url' => 'admin/dashboard'],
    ['title' => $entitasConfig['nama_label'], 'url' => ''],
];
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="<?= esc($entitasConfig['icon'] ?? 'fas fa-users') ?> mr-2"></i>Daftar <?= esc($entitasConfig['nama_label']) ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/personil/' . $entitasType . '/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm" id="tabelPersonil">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th style="width: 60px; text-align: center;">Foto</th>
                                <th>Nama Lengkap</th>
                                <th>NIK</th>
                                <?php if ($entitasConfig['has_masjid_link']): ?>
                                <th>Masjid/Mushola</th>
                                <?php endif; ?>
                                <th>Kelurahan/Desa</th>
                                <th>No. HP</th>
                                <th>Status</th>
                                <th style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($personilList)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($personilList as $p): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="text-center">
                                        <?php if (!empty($p['foto'])): ?>
                                            <img src="<?= base_url('uploads/personil/' . esc($p['foto'])) ?>" alt="Foto" class="img-thumbnail img-fluid cursor-pointer view-photo" data-id="<?= $p['id'] ?>" data-src="<?= base_url('uploads/personil/' . esc($p['foto'])) ?>" data-title="<?= esc($p['nama_lengkap']) ?>" style="width: 45px; height: 45px; object-fit: cover; border-radius: 50%;">
                                        <?php else: ?>
                                            <?php 
                                            // Membangun Avatar 2 Huruf Initials
                                            $words = explode(' ', trim($p['nama_lengkap']));
                                            $initials = '';
                                            if (count($words) >= 2) {
                                                $initials = strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
                                            } elseif (count($words) == 1 && strlen($words[0]) >= 2) {
                                                $initials = strtoupper(substr($words[0], 0, 2));
                                            } else {
                                                $initials = strtoupper(substr($p['nama_lengkap'], 0, 1) ?: '?');
                                            }
                                            // Warna acak pastel berdasarkan huruf awal
                                            $colors = ['#f56954', '#f39c12', '#00c0ef', '#00a65a', '#3c8dbc', '#d81b60', '#605ca8', '#ff851b', '#39cccc', '#001f3f'];
                                            $colorIndex = ord($initials[0]) % count($colors);
                                            $bgColor = $colors[$colorIndex] ?? '#6c757d';
                                            ?>
                                            <div class="d-inline-flex align-items-center justify-content-center text-white cursor-pointer view-photo" data-id="<?= $p['id'] ?>" data-src="<?= base_url('assets/img/avatar-fallback.png') ?>" data-title="<?= esc($p['nama_lengkap']) ?>" style="width: 45px; height: 45px; border-radius: 50%; background-color: <?= $bgColor ?>; font-weight: bold; font-size: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                <?= $initials ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($p['nama_lengkap']) ?></td>
                                    <td><?= esc($p['nik'] ?? '-') ?></td>
                                    <?php if ($entitasConfig['has_masjid_link']): ?>
                                    <td><?= esc($p['nama_masjid'] ?? '-') ?></td>
                                    <?php endif; ?>
                                    <td><?= esc($p['kelurahan_desa'] ?? '-') ?></td>
                                    <td><?= esc($p['no_hp'] ?? '-') ?></td>
                                    <td>
                                        <?php if ($p['status_aktif'] == 1): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Tidak Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/personil/' . $entitasType . '/show/' . $p['id']) ?>" class="btn btn-info btn-xs" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/personil/' . $entitasType . '/edit/' . $p['id']) ?>" class="btn btn-warning btn-xs" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/personil/' . $entitasType . '/delete/' . $p['id']) ?>" class="btn btn-danger btn-xs btn-delete" data-name="<?= esc($p['nama_lengkap']) ?>" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?= $entitasConfig['has_masjid_link'] ? 9 : 8 ?>" class="text-center text-muted">Belum ada data <?= esc($entitasConfig['nama_label']) ?>.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cropper.js CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>

<!-- Interactive Avatar Modal -->
<div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalLabel">Detail & Edit Foto Profil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditFoto">
                    <input type="hidden" id="editFotoId" name="entitas_id">
                    
                    <div class="alert alert-info py-2" role="alert">
                        <small><i class="fas fa-info-circle mr-1"></i> Klik <strong>Pilih Foto Baru</strong> untuk mengganti gambar. Aspect ratio dikunci pada 3:4 (Potret).</small>
                    </div>

                    <div class="form-group text-center mb-0">
                        <!-- Tombol untuk memicu input file tersembunyi -->
                        <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="document.getElementById('inputFotoBaru').click()">
                            <i class="fas fa-camera"></i> Pilih Foto Baru
                        </button>
                        <input type="file" id="inputFotoBaru" accept="image/jpeg,image/jpg,image/png" style="display: none;">
                    </div>

                    <div id="cropContainerWrapper" style="width: 100%; max-height: 50vh; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa; border: 1px dashed #ced4da; border-radius: 4px; padding: 10px; overflow: hidden;">
                        <img id="lightboxImage" src="" alt="Foto" style="max-width: 100%; max-height: 48vh; object-fit: contain;">
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <div>
                    <button type="button" class="btn btn-info btn-sm btn-rotate" data-deg="-90" style="display: none;"><i class="fas fa-undo"></i> Kiri</button>
                    <button type="button" class="btn btn-info btn-sm btn-rotate" data-deg="90" style="display: none;"><i class="fas fa-redo"></i> Kanan</button>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success" id="btnSelesaiEditFoto" style="display: none;">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        var table = $('#tabelPersonil').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "dom": "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "buttons": [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm'
                }
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            },
            "columnDefs": [
                { "orderable": false, "targets": [0, -1] } // Disable sorting on No and Aksi columns
            ]
        });

        // --- Logika Cropper & Modal Foto ---
        var cropperAvatar = null;
        var currentEntitasId = null;
        var imageElement = document.getElementById('lightboxImage');

        $(document).on('click', '.view-photo', function() {
            var src = $(this).data('src');
            var nama = $(this).data('title');
            currentEntitasId = $(this).data('id');

            $('#photoModalLabel').text('Detail & Edit Foto Profil - ' + nama);
            $('#editFotoId').val(currentEntitasId);
            
            // Reset Cropper UI
            if (cropperAvatar) {
                cropperAvatar.destroy();
                cropperAvatar = null;
            }
            $('#lightboxImage').attr('src', src);
            $('.btn-rotate').hide();
            $('#btnSelesaiEditFoto').hide();

            $('#photoModal').modal('show');
        });

        // Trigger saat user memilih file gambar dari device mereka
        $('#inputFotoBaru').on('change', function(e) {
            var file = e.target.files[0];
            if (!file) return;

            // Validasi client-side
            if (file.size > 10485760) {
                Swal.fire('Gagal', 'Ukuran gambar max 10MB', 'error');
                $(this).val(''); return;
            }
            if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
                Swal.fire('Gagal', 'Hanya format JPG, JPEG, PNG diperbolehkan', 'error');
                $(this).val(''); return;
            }

            var reader = new FileReader();
            reader.onload = function(event) {
                if (cropperAvatar) {
                    cropperAvatar.destroy();
                }
                
                imageElement.src = event.target.result;
                
                // Tempelkan Cropper
                cropperAvatar = new Cropper(imageElement, {
                    aspectRatio: 3 / 4, // Format 3:4 untuk pas foto potret
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                });

                // Munculkan tombol rotasi & simpan
                $('.btn-rotate').show();
                $('#btnSelesaiEditFoto').show();
            };
            reader.readAsDataURL(file);
        });

        // Tombol Rotate Gambar
        $('.btn-rotate').on('click', function() {
            var degree = $(this).data('deg');
            if (cropperAvatar) {
                cropperAvatar.rotate(parseInt(degree));
            }
        });

        // Tombol Konfirmasi Simpan
        $('#btnSelesaiEditFoto').on('click', function() {
            if (!cropperAvatar) return;

            var base64Image = cropperAvatar.getCroppedCanvas({
                width: 500,
                height: 500,
                fillColor: '#fff',
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            }).toDataURL('image/jpeg', 0.85);

            var personilId = $('#editFotoId').val();

            // Kunci tombol biar ngga dobel klik
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            $.ajax({
                url: baseUrl + '/admin/berkas/upload-profil',
                type: 'POST',
                data: {
                    entitas_type: '<?= $entitasType ?>',
                    entitas_id: personilId,
                    profil_cropped: base64Image,
                    sync_all: 'false'
                },
                dataType: 'json',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    if (response.success) {
                        $('#photoModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Render ulan baris spesifik tanpa memuat page sepenuhnya, 
                            // atau cara teraman Reload
                            window.location.reload();
                        });
                    } else {
                        btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');
                        Swal.fire('Gagal', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');
                    Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                }
            });
        });

        // Bersihkan data pas modal ditutup
        $('#photoModal').on('hidden.bs.modal', function () {
            if(cropperAvatar) {
                cropperAvatar.destroy();
                cropperAvatar = null;
            }
            $('#lightboxImage').attr('src', '');
            $('#inputFotoBaru').val('');
            $('.btn-rotate').hide();
            $('#btnSelesaiEditFoto').hide();
            $('#btnSelesaiEditFoto').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');
        });
    });
</script>
<?= $this->endSection(); ?>
