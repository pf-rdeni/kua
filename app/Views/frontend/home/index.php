<?= $this->extend('frontend/template/template'); ?>

<?= $this->section('content'); ?>

<div class="text-center py-5">
    <h2>Selamat Datang di</h2>
    <h1 class="text-primary font-weight-bold">Sistem Administrasi Pembantu KUA</h1>
    <h3>Kecamatan Seri Kuala Lobam</h3>
    <hr class="my-4" style="max-width: 400px; margin: auto;">

    <!-- Statistik Publik -->
    <div class="row justify-content-center mt-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info"><i class="fas fa-user-tie"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Mubaligh</span>
                    <span class="info-box-number"><?= $totalMubaligh ?? 0 ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success"><i class="fas fa-mosque"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Masjid & Mushola</span>
                    <span class="info-box-number"><?= $totalMasjidMushola ?? 0 ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning"><i class="fas fa-school"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">TPQ & MDTA</span>
                    <span class="info-box-number"><?= $totalTpqMdta ?? 0 ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-secondary"><i class="fas fa-chalkboard-teacher"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Majelis Taklim</span>
                    <span class="info-box-number"><?= $totalMajelisTaklim ?? 0 ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
