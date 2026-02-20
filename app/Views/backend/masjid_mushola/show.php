<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Masjid & Mushola</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/masjid-mushola') ?>">Masjid & Mushola</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <!-- Profile Image -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <?php if ($masjid['foto'] && file_exists('uploads/masjid_mushola/' . $masjid['foto'])) : ?>
                                    <img class="profile-user-img img-fluid img-circle"
                                         src="<?= base_url('uploads/masjid_mushola/' . $masjid['foto']) ?>"
                                         alt="Foto Masjid" style="width: 150px; height: 150px; object-fit: cover;">
                                <?php else : ?>
                                    <img class="profile-user-img img-fluid img-circle"
                                         src="<?= base_url('template/backend/dist/img/AdminLTELogo.png') ?>"
                                         alt="No Image">
                                <?php endif; ?>
                            </div>

                            <h3 class="profile-username text-center"><?= esc($masjid['nama']) ?></h3>
                            <p class="text-muted text-center"><?= esc($masjid['jenis']) ?></p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Tahun Berdiri</b> <a class="float-right"><?= esc($masjid['tahun_berdiri']) ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Luas Bangunan</b> <a class="float-right"><?= esc($masjid['luas_bangunan']) ?> m²</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header p-2">
                            <h3 class="card-title">Informasi Detail</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('admin/masjid-mushola/edit/' . $masjid['id_masjid_mushola']) ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-pencil-alt"></i> Edit
                                </a>
                                <a href="<?= base_url('admin/masjid-mushola') ?>" class="btn btn-default btn-sm">Kembali</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <tr>
                                    <th style="width: 200px">Nama Masjid/Mushola</th>
                                    <td><?= esc($masjid['nama']) ?></td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td><?= esc($masjid['alamat']) ?></td>
                                </tr>
                                <tr>
                                    <th>Status Tanah</th>
                                    <td><?= esc($masjid['status_tanah']) ?></td>
                                </tr>
                                <tr>
                                    <th>Nama Ketua DKM</th>
                                    <td><?= esc($masjid['nama_ketua_dkm']) ?></td>
                                </tr>
                                <tr>
                                    <th>No. HP Ketua DKM</th>
                                    <td><?= esc($masjid['no_hp_ketua']) ?></td>
                                </tr>
                                <tr>
                                    <th>Jumlah Jamaah</th>
                                    <td><?= esc($masjid['jumlah_jamaah']) ?></td>
                                </tr>
                                <tr>
                                    <th>Koordinat (Lat, Long)</th>
                                    <td><?= esc($masjid['latitude']) ?>, <?= esc($masjid['longitude']) ?></td>
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
