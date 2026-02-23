<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Setting Berkas Lampiran</h3>
        <div class="card-tools">
            <a href="<?= base_url('admin/setting-berkas/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Setting
            </a>
        </div>
    </div>
    <div class="card-body">
        <table id="tableSetting" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Berkas</th>
                    <th>Digunakan Oleh</th>
                    <th>Setting</th>
                    <th>Status</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($settings as $row): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($row['nama_berkas']) ?></td>
                    <td>
                        <?php
                        $entitas = explode(',', $row['entitas_type']);
                        foreach($entitas as $e) {
                            if(trim($e) !== '') {
                                echo '<span class="badge badge-info mr-1">'.esc(trim($e)).'</span>';
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <div class="mb-1">
                            <small class="text-muted d-block">Crop Ratio:</small>
                            <?php if($row['aspect_ratio_width'] && $row['aspect_ratio_height']): ?>
                                <span class="badge badge-dark"><?= $row['aspect_ratio_width'] ?> : <?= $row['aspect_ratio_height'] ?></span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Free</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <small class="text-muted d-block">Tipe Dokumen:</small>
                            <?php if (isset($row['is_rekening']) && $row['is_rekening'] == 1): ?>
                                <span class="badge badge-info"><i class="fas fa-university"></i> Buku Tabungan</span>
                                <?php if (!empty($row['rekening_digit'])): ?>
                                    <div class="mt-1"><small class="text-danger"><i class="fas fa-info-circle"></i> Validasi: <?= $row['rekening_digit'] ?> Digit</small></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge badge-light border">Berkas Biasa</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <?= $row['status_aktif'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Nonaktif</span>' ?>
                    </td>
                    <td>
                        <a href="<?= base_url('admin/setting-berkas/edit/'.$row['id']) ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                        <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="<?= $row['id'] ?>" title="Hapus"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#tableSetting').DataTable();

        $('.btn-delete').on('click', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Data setting berkas ini akan dihapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('admin/setting-berkas/delete') ?>/' + id,
                        type: 'POST',
                        dataType: 'json',
                        headers: {'X-Requested-With': 'XMLHttpRequest'},
                        success: function(response) {
                            if(response.success) {
                                Swal.fire('Terhapus!', response.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Gagal!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
