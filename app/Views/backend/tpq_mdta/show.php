<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Detail TPQ / MDTA</h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <?php if ($tpq['foto'] && file_exists('uploads/tpq_mdta/' . $tpq['foto'])) : ?>
                                    <img class="profile-user-img img-fluid img-circle" src="<?= base_url('uploads/tpq_mdta/' . $tpq['foto']) ?>" alt="Foto" style="width:150px;height:150px;object-fit:cover;">
                                <?php else : ?>
                                    <img class="profile-user-img img-fluid img-circle" src="<?= base_url('template/backend/dist/img/user2-160x160.jpg') ?>" alt="No Image">
                                <?php endif; ?>
                            </div>
                            <h3 class="profile-username text-center"><?= esc($tpq['nama']) ?></h3>
                            <p class="text-muted text-center"><?= esc($tpq['nama_masjid']) ?></p>
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Hari/Waktu</b> <a class="float-right"><?= esc($tpq['hari']) ?> / <?= esc($tpq['waktu']) ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Santri</b> <a class="float-right"><?= esc($tpq['jumlah_santri']) ?> Orang</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header p-2">
                            <h3 class="card-title">Detail</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('admin/tpq-mdta/edit/' . $tpq['id_tpq_mdta']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="<?= base_url('admin/tpq-mdta') ?>" class="btn btn-default btn-sm">Kembali</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <tr><th>Nama TPQ/MDTA</th><td><?= esc($tpq['nama']) ?></td></tr>
                                <tr><th>Masjid/Mushola</th><td><?= esc($tpq['nama_masjid'] ?: 'Tidak ada') ?></td></tr>
                                <tr><th>Pimpinan</th><td><?= esc($tpq['pimpinan']) ?></td></tr>
                                <tr><th>No HP Pimpinan</th><td><?= esc($tpq['no_hp_pimpinan']) ?></td></tr>
                                <tr><th>Alamat</th><td><?= esc($tpq['alamat']) ?></td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection(); ?>
