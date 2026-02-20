<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Detail Majelis Taklim</h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <?php if ($majelis['foto'] && file_exists('uploads/majelis_taklim/' . $majelis['foto'])) : ?>
                                    <img class="profile-user-img img-fluid img-circle" src="<?= base_url('uploads/majelis_taklim/' . $majelis['foto']) ?>" alt="Foto" style="width:150px;height:150px;object-fit:cover;">
                                <?php else : ?>
                                    <img class="profile-user-img img-fluid img-circle" src="<?= base_url('template/backend/dist/img/user2-160x160.jpg') ?>" alt="No Image">
                                <?php endif; ?>
                            </div>
                            <h3 class="profile-username text-center"><?= esc($majelis['nama_majelis_taklim']) ?></h3>
                            <p class="text-muted text-center"><?= esc($majelis['nama_masjid']) ?></p>
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Hari/Waktu</b> <a class="float-right"><?= esc($majelis['hari']) ?> / <?= esc($majelis['waktu']) ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Jamaah</b> <a class="float-right"><?= esc($majelis['jumlah_jamaah']) ?> Orang</a>
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
                                <a href="<?= base_url('admin/majelis-taklim/edit/' . $majelis['id_majelis_taklim']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="<?= base_url('admin/majelis-taklim') ?>" class="btn btn-default btn-sm">Kembali</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <tr><th>Nama Majelis</th><td><?= esc($majelis['nama_majelis_taklim']) ?></td></tr>
                                <tr><th>Masjid/Mushola</th><td><?= esc($majelis['nama_masjid'] ?: 'Tidak ada') ?></td></tr>
                                <tr><th>Pimpinan</th><td><?= esc($majelis['pimpinan']) ?></td></tr>
                                <tr><th>No HP Pimpinan</th><td><?= esc($majelis['no_hp_pimpinan']) ?></td></tr>
                                <tr><th>Alamat</th><td><?= esc($majelis['alamat']) ?></td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection(); ?>
