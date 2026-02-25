<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">List Tema Ceramah Ramadhan <?= esc($tahunHijriah) ?></h3>
                <div class="card-tools">
                    <form action="" method="get" class="form-inline">
                        <select name="tahun" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                            <option value="1445 H" <?= $tahunHijriah == '1445 H' ? 'selected' : '' ?>>1445 H</option>
                            <option value="1446 H" <?= $tahunHijriah == '1446 H' ? 'selected' : '' ?>>1446 H</option>
                            <option value="1447 H" <?= $tahunHijriah == '1447 H' ? 'selected' : '' ?>>1447 H</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalDuplikatTema"><i class="fas fa-copy"></i> Duplikat Tema</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 100px; text-align: center;">Hari Ke</th>
                                <th>Tema / Judul Ceramah</th>
                                <th style="width: 150px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($temaList as $t): ?>
                            <tr>
                                <td class="text-center font-weight-bold"><?= $t['hari_ke'] ?></td>
                                <td>
                                    <input type="text" class="form-control input-tema" data-id="<?= $t['id'] ?>" value="<?= esc($t['tema']) ?>" placeholder="Tulis tema di sini...">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-secondary btn-save-tema" data-id="<?= $t['id'] ?>">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Duplikat Tema -->
<div class="modal fade" id="modalDuplikatTema" tabindex="-1" role="dialog" aria-labelledby="modalDuplikatTemaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDuplikatTemaLabel">Duplikat Tema Ceramah</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Anda akan menduplikasi <strong>seluruh daftar tema</strong> dari tahun <strong class="text-primary"><?= esc($tahunHijriah) ?></strong> ke tahun tujuan.</p>
        <p class="text-danger"><small><i class="fas fa-exclamation-triangle"></i> Peringatan: Daftar tema di tahun tujuan akan <b>ditimpa/dihapus</b> dan diganti dengan data ini.</small></p>
        
        <div class="form-group text-left">
            <label>Salin Ke Tahun Tujuan</label>
            <select class="form-control" id="selectTargetTahunTema">
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
        <button type="button" class="btn btn-info" id="btnSubmitDuplikatTema"><i class="fas fa-copy"></i> Mulai Duplikasi</button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Detect changes to adjust button color
    $('.input-tema').on('input', function() {
        var id = $(this).data('id');
        var btn = $('.btn-save-tema[data-id="' + id + '"]');
        
        // Change from secondary/success to primary (blue) on change
        btn.removeClass('btn-secondary btn-success').addClass('btn-primary');
        btn.html('<i class="fas fa-save"></i> Simpan');
    });

    $('.btn-save-tema').on('click', function() {
        var id = $(this).data('id');
        var inputTema = $('input.input-tema[data-id="' + id + '"]');
        var tema = inputTema.val();
        var btn = $(this);
        
        // Only run if it's blue (btn-primary), meaning there are changes
        if (!btn.hasClass('btn-primary')) {
            return; 
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '<?= base_url('admin/jadwal-ramadhan/save-tema') ?>',
            type: 'POST',
            data: { id: id, tema: tema },
            success: function(res) {
                if(res.status === 'success') {
                    // Change to green on success
                    btn.removeClass('btn-primary').addClass('btn-success');
                    btn.html('<i class="fas fa-check"></i> Tersimpan');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message, // "Tersimpan"
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    btn.html('<i class="fas fa-save"></i> Simpan');
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: function() {
                btn.html('<i class="fas fa-save"></i> Simpan');
                Swal.fire('Error', 'Terjadi kesalahan koneksi', 'error');
            },
            complete: function() {
                btn.prop('disabled', false);
            }
        });
    });

    // Save on enter key
    $('.input-tema').on('keypress', function(e) {
        if(e.which == 13) {
            var id = $(this).data('id');
            $('.btn-save-tema[data-id="' + id + '"]').click();
        }
    });

    // Duplikat Tema
    $('#btnSubmitDuplikatTema').on('click', function() {
        var to_year = $('#selectTargetTahunTema').val();
        var from_year = '<?= esc($tahunHijriah) ?>';

        if (!to_year) {
            Swal.fire('Peringatan', 'Pilih tahun tujuan terlebih dahulu!', 'warning');
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Proses...');

        $.ajax({
            url: '<?= base_url('admin/jadwal-ramadhan/duplicate-tema') ?>',
            type: 'POST',
            data: {
                from_year: from_year,
                to_year: to_year,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(res) {
                if(res.status === 'success') {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                        // Redirect view to the new duplicated year
                        window.location.href = '<?= base_url('admin/jadwal-ramadhan/tema') ?>?tahun=' + encodeURIComponent(to_year);
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
