<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Detail Petugas Fardu Kifayah</h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <?php if ($petugas['foto'] && file_exists('uploads/fardu_kifayah/' . $petugas['foto'])) : ?>
                                    <img class="profile-user-img img-fluid img-circle" src="<?= base_url('uploads/fardu_kifayah/' . $petugas['foto']) ?>" alt="Foto" style="width:150px;height:150px;object-fit:cover;">
                                <?php else : ?>
                                    <img class="profile-user-img img-fluid img-circle" src="<?= base_url('template/backend/dist/img/user2-160x160.jpg') ?>" alt="No Image">
                                <?php endif; ?>
                            </div>
                            <h3 class="profile-username text-center"><?= esc($petugas['nama']) ?></h3>
                            <p class="text-muted text-center"><?= esc($petugas['status']) ?></p>
                            <p class="text-center"><b><?= esc($petugas['nama_masjid']) ?></b></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header p-2">
                            <h3 class="card-title">Detail</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('admin/fardu-kifayah/edit/' . $petugas['id_fardu_kifayah']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="<?= base_url('admin/fardu-kifayah') ?>" class="btn btn-default btn-sm">Kembali</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <tr><th>Nama</th><td><?= esc($petugas['nama']) ?></td></tr>
                                <tr><th>Masjid</th><td><?= esc($petugas['nama_masjid']) ?></td></tr>
                                <tr><th>Status</th><td><?= esc($petugas['status']) ?></td></tr>
                                <tr><th>Alamat</th><td><?= esc($petugas['alamat']) ?></td></tr>
                                <tr><th>No HP</th><td><?= esc($petugas['no_hp']) ?></td></tr>
                                <tr>
                                    <th>SK Pengangkatan</th>
                                    <td>
                                        <?php if ($petugas['sk_pengangkatan'] && file_exists('uploads/fardu_kifayah/' . $petugas['sk_pengangkatan'])) : ?>
                                            <a href="<?= base_url('uploads/fardu_kifayah/' . $petugas['sk_pengangkatan']) ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-file-download"></i> Lihat/Download SK
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Tidak ada file SK</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection(); ?>
