<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chalkboard-teacher mr-2"></i>Data Majelis Taklim
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/majelis-taklim/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm" id="tabelMt">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Nama Majelis</th>
                                <th style="width: 60px; text-align: center;">Foto</th>
                                <th>Pimpinan</th>
                                <th>Hari & Waktu</th>
                                <th>Masjid/Mushola</th>
                                <th>No. HP</th>
                                <th style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($mtList)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($mtList as $mt): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($mt['nama_majelis_taklim']) ?></td>
                                    <td class="text-center">
                                        <?php if (!empty($mt['foto'])) : ?>
                                            <img src="<?= base_url('uploads/majelis_taklim/' . esc($mt['foto'])) ?>" alt="Foto" class="img-thumbnail img-fluid cursor-pointer view-edit-photo" data-id="<?= $mt['id_majelis_taklim'] ?>" data-src="<?= base_url('uploads/majelis_taklim/' . esc($mt['foto'])) ?>" data-title="<?= esc($mt['nama_majelis_taklim']) ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                        <?php else: ?>
                                            <?php 
                                            // Membangun Avatar 2 Huruf Initials
                                            $words = explode(' ', trim($mt['nama_majelis_taklim']));
                                            $initials = '';
                                            if (count($words) >= 2) {
                                                $initials = strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
                                            } elseif (count($words) == 1 && strlen($words[0]) >= 2) {
                                                $initials = strtoupper(substr($words[0], 0, 2));
                                            } else {
                                                $initials = strtoupper(substr($mt['nama_majelis_taklim'], 0, 1) ?: '?');
                                            }
                                            // Warna acak berdasarkan huruf awal
                                            $colors = ['#f56954', '#f39c12', '#00c0ef', '#00a65a', '#3c8dbc', '#d81b60', '#605ca8', '#ff851b', '#39cccc', '#001f3f'];
                                            $colorIndex = ord($initials[0]) % count($colors);
                                            $bgColor = $colors[$colorIndex] ?? '#6c757d';
                                            ?>
                                            <div class="d-inline-flex align-items-center justify-content-center text-white cursor-pointer view-edit-photo" data-id="<?= $mt['id_majelis_taklim'] ?>" data-src="" data-title="<?= esc($mt['nama_majelis_taklim']) ?>" style="width: 60px; height: 60px; border-radius: 4px; background-color: <?= $bgColor ?>; font-weight: bold; font-size: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                <?= $initials ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($mt['pimpinan']) ?></td>
                                    <td><?= esc($mt['hari']) ?> - <?= esc($mt['waktu']) ?></td>
                                    <td><?= esc($mt['nama_masjid']) ?></td>
                                    <td><?= esc($mt['no_hp_pimpinan'] ?? '-') ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/majelis-taklim/' . $mt['id_majelis_taklim']) ?>" class="btn btn-info btn-xs" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/majelis-taklim/edit/' . $mt['id_majelis_taklim']) ?>" class="btn btn-warning btn-xs" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/majelis-taklim/delete/' . $mt['id_majelis_taklim']) ?>" class="btn btn-danger btn-xs" onclick="return confirm('Apakah Anda yakin?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Belum ada data majelis taklim.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Cropper.js CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>

<!-- Interactive Photo Edit Modal -->
<div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalLabel">Detail & Edit Foto Majelis Taklim</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditFoto">
                    <input type="hidden" id="editFotoId" name="entitas_id">
                    
                    <div class="alert alert-info py-2" role="alert">
                        <small><i class="fas fa-info-circle mr-1"></i> Klik <strong>Pilih Foto Baru</strong> untuk mengganti gambar. Aspect ratio dikunci pada 1:1 (Persegi).</small>
                    </div>

                    <div class="form-group text-center mb-0">
                        <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="document.getElementById('inputFotoBaru').click()">
                            <i class="fas fa-camera"></i> Pilih Foto Baru
                        </button>
                        <input type="file" id="inputFotoBaru" accept="image/jpeg,image/jpg,image/png" style="display: none;">
                    </div>

                    <div id="cropContainerWrapper" style="width: 100%; max-height: 50vh; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa; border: 1px dashed #ced4da; border-radius: 4px; padding: 10px; overflow: hidden;">
                        <img id="lightboxImage" src="" alt="Foto" style="max-width: 100%; max-height: 48vh; object-fit: contain; display: none;">
                        <div id="noPhotoPlaceholder" class="text-muted" style="display: none;"><i class="fas fa-image fa-3x mb-2"></i><br>Belum Ada Foto</div>
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
        var table = $('#tabelMt').DataTable({
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
                { "orderable": false, "targets": [0, -1] }
            ]
        });

        // --- Logika Cropper & Modal Foto ---
        var cropperAvatar = null;
        var currentEntitasId = null;
        var imageElement = document.getElementById('lightboxImage');

        $(document).on('click', '.view-edit-photo', function() {
            var src = $(this).data('src');
            var nama = $(this).data('title');
            
            // Ambil ID langsung dari data-id (lebih reliabel)
            currentEntitasId = $(this).data('id');

            $('#photoModalLabel').text('Detail & Edit Foto - ' + nama);
            $('#editFotoId').val(currentEntitasId);
            
            if (cropperAvatar) {
                cropperAvatar.destroy();
                cropperAvatar = null;
            }
            
            if (src && src.trim() !== '') {
                $('#lightboxImage').attr('src', src).show();
                $('#noPhotoPlaceholder').hide();
            } else {
                $('#lightboxImage').hide();
                $('#noPhotoPlaceholder').show();
            }
            
            $('.btn-rotate').hide();
            $('#btnSelesaiEditFoto').hide();

            $('#photoModal').modal('show');
        });

        $('#inputFotoBaru').on('change', function(e) {
            var file = e.target.files[0];
            if (!file) return;

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
                
                $('#noPhotoPlaceholder').hide();
                $('#lightboxImage').show();
                imageElement.src = event.target.result;
                
                cropperAvatar = new Cropper(imageElement, {
                    aspectRatio: 1 / 1, // Persegi 1:1 format for Majelis Taklim
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                });

                $('.btn-rotate').show();
                $('#btnSelesaiEditFoto').show();
            };
            reader.readAsDataURL(file);
        });

        $('.btn-rotate').on('click', function() {
            var degree = $(this).data('deg');
            if (cropperAvatar) {
                cropperAvatar.rotate(parseInt(degree));
            }
        });

        $('#btnSelesaiEditFoto').on('click', function() {
            if (!cropperAvatar) return;

            var base64Image = cropperAvatar.getCroppedCanvas({
                width: 800,
                height: 800,
                fillColor: '#fff',
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            }).toDataURL('image/jpeg', 0.85);

            var entitasId = $('#editFotoId').val();
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            $.ajax({
                url: baseUrl + '/admin/berkas/upload-profil',
                type: 'POST',
                data: {
                    entitas_type: 'majelis_taklim',
                    entitas_id: currentEntitasId,
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

        $('#photoModal').on('hidden.bs.modal', function () {
            if(cropperAvatar) {
                cropperAvatar.destroy();
                cropperAvatar = null;
            }
            $('#lightboxImage').attr('src', '').hide();
            $('#noPhotoPlaceholder').hide();
            $('#inputFotoBaru').val('');
            $('.btn-rotate').hide();
            $('#btnSelesaiEditFoto').hide();
            $('#btnSelesaiEditFoto').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');
        });
    });
</script>
<?= $this->endSection(); ?>
