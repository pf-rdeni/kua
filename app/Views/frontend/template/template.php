<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistem Administrasi Pembantu KUA Kecamatan Seri Kuala Lobam">
    <title><?= $title ?? 'KUA Seri Kuala Lobam' ?></title>
    <link rel="icon" href="<?= base_url('assets/icon/logo_kua.png') ?>" type="image/png" sizes="32x32">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="<?= base_url('template/backend/plugins/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('template/backend/dist/css/adminlte.min.css') ?>">
    <style>
        /* Desktop Default */
        .bottom-nav { display: none; }
        
        /* Mobile View Customization */
        @media (max-width: 768px) {
            /* Hide standard navbar links */
            .navbar-nav { display: none; }
            .navbar-toggler { display: none; }
            
            /* Center Brand */
            .navbar-brand { 
                margin: 0 auto; 
                display: flex;
                align-items: center;
            }

            /* Adjust Content Padding for Bottom Nav */
            body { padding-bottom: 70px; }

            /* Bottom Navigation Bar */
            .bottom-nav {
                display: flex;
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 65px;
                background: #ffffff;
                box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
                z-index: 1000;
                justify-content: space-around;
                align-items: center;
                padding-bottom: env(safe-area-inset-bottom); /* iOS Safe Area */
            }

            .bottom-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-decoration: none !important;
                color: #6c757d;
                font-size: 0.75rem;
                flex: 1;
                padding: 8px 0;
            }

            .bottom-nav-item i {
                font-size: 1.25rem;
                margin-bottom: 4px;
            }

            .bottom-nav-item.active {
                color: #145A32; /* Green KUA */
                font-weight: 600;
            }

            /* Modal Customization for Mobile Menus */
            .mobile-menu-modal .modal-dialog {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                margin: 0;
                max-width: 100%;
            }
            .mobile-menu-modal .modal-content {
                border-radius: 15px 15px 0 0;
                border: none;
            }
            .mobile-menu-link {
                display: block;
                padding: 12px 15px;
                color: #333;
                border-bottom: 1px solid #eee;
                text-decoration: none;
                font-size: 1rem;
            }
            .mobile-menu-link:last-child { border-bottom: none; }
            .mobile-menu-link i { margin-right: 10px; color: #145A32; }
        }
    </style>
</head>
<body class="hold-transition layout-top-nav">
    <div class="wrapper">
        <!-- Navbar (Top) -->
        <nav class="main-header navbar navbar-expand-md navbar-light navbar-white border-bottom-0 shadow-sm">
            <div class="container">
                <a href="<?= base_url('/') ?>" class="navbar-brand">
                    <!-- Icon/Logo Placeholder if needed -->
                    <i class="fas fa-mosque text-success mr-2"></i>
                    <span class="brand-text font-weight-bold text-dark">KUA Seri Kuala Lobam</span>
                </a>
                
                <!-- Desktop Menu (Hidden on Mobile via CSS) -->
                <button class="navbar-toggler p-0 border-0" type="button" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a href="<?= base_url('/') ?>" class="nav-link">Beranda</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Profil</a>
                            <div class="dropdown-menu border-0 shadow">
                                <a href="<?= base_url('profil/sejarah') ?>" class="dropdown-item">Sejarah</a>
                                <a href="<?= base_url('profil/visi-misi') ?>" class="dropdown-item">Visi & Misi</a>
                                <a href="<?= base_url('profil/struktur-organisasi') ?>" class="dropdown-item">Struktur Organisasi</a>
                                <a href="<?= base_url('profil/tupoksi') ?>" class="dropdown-item">Tupoksi</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Layanan</a>
                            <div class="dropdown-menu border-0 shadow">
                                <a href="<?= base_url('layanan/persyaratan-nikah') ?>" class="dropdown-item">Persyaratan Nikah</a>
                                <a href="<?= base_url('layanan/rujuk') ?>" class="dropdown-item">Rujuk</a>
                                <a href="<?= base_url('layanan/legalisir') ?>" class="dropdown-item">Legalisir</a>
                                <a href="<?= base_url('layanan/konsultasi') ?>" class="dropdown-item">Konsultasi Keluarga</a>
                                <a href="<?= base_url('layanan/wakaf') ?>" class="dropdown-item">Wakaf</a>
                                <a href="<?= base_url('layanan/kemasjidan') ?>" class="dropdown-item">Kemasjidan</a>
                                <a href="<?= base_url('layanan/haji') ?>" class="dropdown-item">Haji</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Data</a>
                            <div class="dropdown-menu border-0 shadow">
                                <a href="<?= base_url('data/masjid-mushola') ?>" class="dropdown-item">Masjid & Mushola</a>
                                <a href="<?= base_url('data/mubaligh') ?>" class="dropdown-item">Mubaligh</a>
                                <a href="<?= base_url('data/imam-masjid') ?>" class="dropdown-item">Imam Masjid</a>
                                <a href="<?= base_url('data/majelis-taklim') ?>" class="dropdown-item">Majelis Taklim</a>
                                <a href="<?= base_url('data/tpq-mdta') ?>" class="dropdown-item">TPQ / MDTA</a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('login') ?>" class="nav-link btn btn-success text-white ml-2 px-3 shadow-sm rounded-pill">
                                <i class="fas fa-sign-in-alt mr-1"></i> Login
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="content-wrapper" style="min-height: 80vh; background-color: #f4f6f9;">
            <div class="content">
                <div class="container py-4">
                    <?= $this->renderSection('content'); ?>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <div class="container">
                <div class="float-right d-none d-sm-inline">
                    Sistem Informasi KUA
                </div>
                <strong>&copy; <?= date('Y') ?> KUA Kec. Seri Kuala Lobam.</strong>
            </div>
        </footer>

        <!-- Mobile Bottom Nav -->
        <div class="bottom-nav">
            <a href="<?= base_url('/') ?>" class="bottom-nav-item active">
                <i class="fas fa-home"></i>
                <span>Beranda</span>
            </a>
            <a href="#" class="bottom-nav-item" data-toggle="modal" data-target="#modal-profil">
                <i class="fas fa-info-circle"></i>
                <span>Profil</span>
            </a>
            <a href="#" class="bottom-nav-item" data-toggle="modal" data-target="#modal-layanan">
                <i class="fas fa-concierge-bell"></i>
                <span>Layanan</span>
            </a>
            <a href="#" class="bottom-nav-item" data-toggle="modal" data-target="#modal-data">
                <i class="fas fa-database"></i>
                <span>Data</span>
            </a>
            <a href="<?= base_url('login') ?>" class="bottom-nav-item">
                <i class="fas fa-user-circle"></i>
                <span>Login</span>
            </a>
        </div>

        <!-- Mobile Modals (Bottom Sheets) -->
        
        <!-- Modal Profil -->
        <div class="modal fade mobile-menu-modal" id="modal-profil" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-info-circle mr-2 text-success"></i> Profil KUA</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body p-0">
                        <a href="<?= base_url('profil/sejarah') ?>" class="mobile-menu-link">Sejarah</a>
                        <a href="<?= base_url('profil/visi-misi') ?>" class="mobile-menu-link">Visi & Misi</a>
                        <a href="<?= base_url('profil/struktur-organisasi') ?>" class="mobile-menu-link">Struktur Organisasi</a>
                        <a href="<?= base_url('profil/tupoksi') ?>" class="mobile-menu-link">Tupoksi</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Layanan -->
        <div class="modal fade mobile-menu-modal" id="modal-layanan" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-concierge-bell mr-2 text-success"></i> Layanan KUA</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body p-0">
                        <a href="<?= base_url('layanan/persyaratan-nikah') ?>" class="mobile-menu-link">Persyaratan Nikah</a>
                        <a href="<?= base_url('layanan/rujuk') ?>" class="mobile-menu-link">Rujuk</a>
                        <a href="<?= base_url('layanan/legalisir') ?>" class="mobile-menu-link">Legalisir</a>
                        <a href="<?= base_url('layanan/konsultasi') ?>" class="mobile-menu-link">Konsultasi Keluarga</a>
                        <a href="<?= base_url('layanan/wakaf') ?>" class="mobile-menu-link">Wakaf</a>
                        <a href="<?= base_url('layanan/kemasjidan') ?>" class="mobile-menu-link">Kemasjidan</a>
                        <a href="<?= base_url('layanan/haji') ?>" class="mobile-menu-link">Haji</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Data -->
        <div class="modal fade mobile-menu-modal" id="modal-data" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-database mr-2 text-success"></i> Data KUA</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body p-0">
                        <a href="<?= base_url('data/masjid-mushola') ?>" class="mobile-menu-link">Masjid & Mushola</a>
                        <a href="<?= base_url('data/mubaligh') ?>" class="mobile-menu-link">Mubaligh</a>
                        <a href="<?= base_url('data/imam-masjid') ?>" class="mobile-menu-link">Imam Masjid</a>
                        <a href="<?= base_url('data/majelis-taklim') ?>" class="mobile-menu-link">Majelis Taklim</a>
                        <a href="<?= base_url('data/tpq-mdta') ?>" class="mobile-menu-link">TPQ / MDTA</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Fix for Modals in Bottom Nav -->
    <script>
        // Close modal when link is clicked
        $('.mobile-menu-link').on('click', function(){
            $('.modal').modal('hide');
        });
    </script>
    
    <script src="<?= base_url('template/backend/plugins/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('template/backend/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('template/backend/dist/js/adminlte.js') ?>"></script>
</body>
</html>
