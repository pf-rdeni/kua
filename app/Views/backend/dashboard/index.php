<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<?php
$pageTitle = 'Dashboard';
$breadcrumb = [
    ['title' => 'Home', 'url' => 'admin/dashboard'],
    ['title' => 'Dashboard', 'url' => ''],
];
?>

<!-- Info Boxes -->
<div class="row">
    <!-- Mubaligh -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $totalMubaligh ?? 0 ?></h3>
                <p>Mubaligh</p>
            </div>
            <div class="icon"><i class="fas fa-user-tie"></i></div>
            <a href="<?= base_url('admin/mubaligh') ?>" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Masjid & Mushola -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $totalMasjidMushola ?? 0 ?></h3>
                <p>Masjid & Mushola</p>
            </div>
            <div class="icon"><i class="fas fa-mosque"></i></div>
            <a href="<?= base_url('admin/masjid-mushola') ?>" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Imam Masjid -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $totalImamMasjid ?? 0 ?></h3>
                <p>Imam Masjid</p>
            </div>
            <div class="icon"><i class="fas fa-user-check"></i></div>
            <a href="<?= base_url('admin/imam-masjid') ?>" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Fardu Kifayah -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= $totalFarduKifayah ?? 0 ?></h3>
                <p>Fardu Kifayah</p>
            </div>
            <div class="icon"><i class="fas fa-hands-helping"></i></div>
            <a href="<?= base_url('admin/fardu-kifayah') ?>" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Penggali Kubur -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3><?= $totalPenggaliKubur ?? 0 ?></h3>
                <p>Penggali Kubur</p>
            </div>
            <div class="icon"><i class="fas fa-hard-hat"></i></div>
            <a href="<?= base_url('admin/penggali-kubur') ?>" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Majelis Taklim -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3><?= $totalMajelisTaklim ?? 0 ?></h3>
                <p>Majelis Taklim</p>
            </div>
            <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <a href="<?= base_url('admin/majelis-taklim') ?>" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- TPQ & MDTA -->
    <div class="col-lg-3 col-6">
        <div class="small-box" style="background-color: #6f42c1; color: #fff;">
            <div class="inner">
                <h3><?= $totalTpqMdta ?? 0 ?></h3>
                <p>TPQ & MDTA</p>
            </div>
            <div class="icon"><i class="fas fa-school"></i></div>
            <a href="<?= base_url('admin/tpq-mdta') ?>" class="small-box-footer" style="color: rgba(255,255,255,.8);">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
