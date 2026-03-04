<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<div class="row">
    <div class="col-md-12 col-12 mx-auto">

        <?php if (session()->has('success')) : ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fas fa-check"></i> <?= session('success') ?>
        </div>
        <?php endif; ?>

        <?php if (session()->has('error')) : ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fas fa-ban"></i> <?= session('error') ?>
        </div>
        <?php endif; ?>

        <?php if (session()->has('errors')) : ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <ul class="mb-0">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-key mr-2"></i>Ganti Password
                </h3>
            </div>

            <form action="<?= base_url('admin/akun/ganti-password') ?>" method="POST" id="formGantiPw">
                <?= csrf_field() ?>
                <div class="card-body">

                    <!-- Info keamanan -->
                    <div class="callout callout-info mb-3">
                        <h6><i class="fas fa-shield-alt mr-1"></i> Tips Keamanan</h6>
                        <ul class="mb-0 pl-3" style="font-size: 13px;">
                            <li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol.</li>
                            <li>Minimal 6 karakter.</li>
                            <li>Jangan gunakan tanggal lahir atau nama yang mudah ditebak.</li>
                        </ul>
                    </div>

                    <!-- Password Lama -->
                    <div class="form-group">
                        <label for="password_lama">
                            <i class="fas fa-lock mr-1 text-danger"></i>Password Lama
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password"
                                   class="form-control"
                                   id="password_lama"
                                   name="password_lama"
                                   placeholder="Masukkan password saat ini"
                                   required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary btn-toggle-pw" type="button" data-target="password_lama">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Password Baru -->
                    <div class="form-group">
                        <label for="password_baru">
                            <i class="fas fa-lock mr-1 text-success"></i>Password Baru
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password"
                                   class="form-control"
                                   id="password_baru"
                                   name="password_baru"
                                   placeholder="Masukkan password baru"
                                   required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary btn-toggle-pw" type="button" data-target="password_baru">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Indikator kekuatan password -->
                        <div class="progress mt-2" style="height: 5px; display: none;" id="pwStrengthBar">
                            <div class="progress-bar" id="pwStrengthFill" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small id="pwStrengthText" class="form-text"></small>
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div class="form-group">
                        <label for="konfirmasi_baru">
                            <i class="fas fa-lock mr-1 text-success"></i>Konfirmasi Password Baru
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password"
                                   class="form-control"
                                   id="konfirmasi_baru"
                                   name="konfirmasi_baru"
                                   placeholder="Ulangi password baru"
                                   required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary btn-toggle-pw" type="button" data-target="konfirmasi_baru">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <small id="matchInfo" class="form-text"></small>
                    </div>

                </div><!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" id="btnSimpan">
                        <i class="fas fa-save mr-1"></i> Simpan Password Baru
                    </button>
                    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary ml-2">
                        <i class="fas fa-times mr-1"></i> Batal
                    </a>
                </div>
            </form>

        </div><!-- /.card -->
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {

    // --- Toggle Tampil/Sembunyikan Password ---
    $(document).on('click', '.btn-toggle-pw', function() {
        var targetId = $(this).data('target');
        var input    = document.getElementById(targetId);
        var icon     = $(this).find('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // --- Indikator Kekuatan Password ---
    $('#password_baru').on('input', function() {
        var pw     = $(this).val();
        var bar    = $('#pwStrengthBar');
        var fill   = $('#pwStrengthFill');
        var text   = $('#pwStrengthText');

        if (pw.length === 0) {
            bar.hide(); text.text('');
            return;
        }

        bar.show();

        var score = 0;
        if (pw.length >= 6)  score++;
        if (pw.length >= 10) score++;
        if (/[A-Z]/.test(pw)) score++;
        if (/[0-9]/.test(pw)) score++;
        if (/[^a-zA-Z0-9]/.test(pw)) score++;

        var persen = (score / 5) * 100;
        fill.css('width', persen + '%');

        if (score <= 1) {
            fill.removeClass().addClass('progress-bar bg-danger');
            text.removeClass().addClass('form-text text-danger').text('Terlalu lemah');
        } else if (score <= 2) {
            fill.removeClass().addClass('progress-bar bg-warning');
            text.removeClass().addClass('form-text text-warning').text('Cukup lemah');
        } else if (score <= 3) {
            fill.removeClass().addClass('progress-bar bg-info');
            text.removeClass().addClass('form-text text-info').text('Cukup kuat');
        } else if (score <= 4) {
            fill.removeClass().addClass('progress-bar bg-primary');
            text.removeClass().addClass('form-text text-primary').text('Kuat');
        } else {
            fill.removeClass().addClass('progress-bar bg-success');
            text.removeClass().addClass('form-text text-success').text('Sangat kuat');
        }

        // Cek juga kesesuaian dengan konfirmasi
        cekKesesuaian();
    });

    // --- Cek Kesesuaian Konfirmasi Password ---
    function cekKesesuaian() {
        var pw1     = $('#password_baru').val();
        var pw2     = $('#konfirmasi_baru').val();
        var matchEl = $('#matchInfo');

        if (pw2.length === 0) { matchEl.text(''); return; }

        if (pw1 === pw2) {
            matchEl.removeClass().addClass('form-text text-success').html('<i class="fas fa-check mr-1"></i>Password cocok');
            $('#konfirmasi_baru').removeClass('is-invalid').addClass('is-valid');
        } else {
            matchEl.removeClass().addClass('form-text text-danger').html('<i class="fas fa-times mr-1"></i>Password tidak cocok');
            $('#konfirmasi_baru').removeClass('is-valid').addClass('is-invalid');
        }
    }

    $('#konfirmasi_baru').on('input', cekKesesuaian);

    // --- Validasi sebelum submit ---
    $('#formGantiPw').on('submit', function(e) {
        var pw1 = $('#password_baru').val();
        var pw2 = $('#konfirmasi_baru').val();

        if (pw1 !== pw2) {
            e.preventDefault();
            Swal.fire('Gagal', 'Password baru dan konfirmasi tidak cocok.', 'error');
            return false;
        }

        if (pw1.length < 6) {
            e.preventDefault();
            Swal.fire('Gagal', 'Password baru minimal 6 karakter.', 'error');
            return false;
        }
    });

});
</script>
<?= $this->endSection(); ?>
