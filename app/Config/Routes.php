<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================================
// FRONTEND ROUTES (Publik - tanpa autentikasi)
// ============================================================
// ============================================================
// FRONTEND ROUTES (Publik - tanpa autentikasi)
// ============================================================
$routes->get('/', 'Frontend\HomeController::index');

// Profil
$routes->group('profil', function($routes) {
    $routes->get('sejarah', 'Frontend\ProfilController::sejarah');
    $routes->get('visi-misi', 'Frontend\ProfilController::visi_misi');
    $routes->get('struktur-organisasi', 'Frontend\ProfilController::struktur_organisasi');
    $routes->get('tupoksi', 'Frontend\ProfilController::tupoksi');
});

// Layanan
$routes->group('layanan', function($routes) {
    $routes->get('persyaratan-nikah', 'Frontend\LayananController::persyaratan_nikah');
    $routes->get('rujuk', 'Frontend\LayananController::rujuk');
    $routes->get('legalisir', 'Frontend\LayananController::legalisir');
    $routes->get('konsultasi', 'Frontend\LayananController::konsultasi');
    $routes->get('wakaf', 'Frontend\LayananController::wakaf');
    $routes->get('kemasjidan', 'Frontend\LayananController::kemasjidan');
    $routes->get('haji', 'Frontend\LayananController::haji');
});

// Data Public (Dynamic)
$routes->group('data', function($routes) {
    $routes->get('masjid-mushola', 'Frontend\DataController::masjid_mushola');
    $routes->get('masjid-mushola/(:num)', 'Frontend\DataController::detail_masjid/$1');
    $routes->get('mubaligh', 'Frontend\DataController::mubaligh');
    $routes->get('imam-masjid', 'Frontend\DataController::imam_masjid');
    $routes->get('majelis-taklim', 'Frontend\DataController::majelis_taklim');
    $routes->get('tpq-mdta', 'Frontend\DataController::tpq_mdta');
    // Add others as needed
});

// ============================================================
// AUTH ROUTES (Myth:Auth) - Login, Logout, dll
// ============================================================
// Myth:Auth sudah auto-register routes melalui Config/Auth.php

$routes->get('login', '\App\Controllers\Auth\AuthController::login', ['as' => 'login']);
$routes->post('login', '\App\Controllers\Auth\AuthController::attemptLogin');
$routes->get('logout', '\App\Controllers\Auth\AuthController::logout');

// ============================================================
// BACKEND ROUTES (Area Admin - memerlukan login + role)
// ============================================================
$routes->group('admin', ['filter' => 'login'], function ($routes) {
    // -- General --
    // --- Dashboard ---
    $routes->get('dashboard', 'Backend\DashboardController::index');

    // --- Berkas Lampiran (Shared AJAX Routes) ---
    $routes->group('berkas', function ($routes) {
        $routes->post('upload', 'Backend\BerkasController::upload');
        $routes->post('delete/(:num)', 'Backend\BerkasController::delete/$1');
        $routes->get('get/(:num)', 'Backend\BerkasController::getById/$1');
        $routes->post('upload-profil', 'Backend\BerkasController::uploadProfil');
        $routes->post('delete-profil', 'Backend\BerkasController::deleteProfil');
    });

    // --- Setting Berkas Lampiran (SuperAdmin, Admin) ---
    $routes->group('setting-berkas', ['filter' => 'role:SuperAdmin,Admin'], function ($routes) {
        $routes->get('/', 'Backend\SettingBerkasController::index');
        $routes->get('create', 'Backend\SettingBerkasController::create');
        $routes->post('store', 'Backend\SettingBerkasController::store');
        $routes->get('edit/(:num)', 'Backend\SettingBerkasController::edit/$1');
        $routes->post('update/(:num)', 'Backend\SettingBerkasController::update/$1');
        $routes->post('delete/(:num)', 'Backend\SettingBerkasController::delete/$1');
    });

    // --- Berdasarkan Role ---
    // --- Mubaligh (SuperAdmin, Admin, OperatorMubaligh) ---
    $routes->group('mubaligh', ['filter' => 'role:SuperAdmin,Admin,OperatorMubaligh'], function ($routes) {
        $routes->get('/', 'Backend\MubalighController::index');
        $routes->get('create', 'Backend\MubalighController::create');
        $routes->post('store', 'Backend\MubalighController::store');
        $routes->get('edit/(:num)', 'Backend\MubalighController::edit/$1');
        $routes->post('update/(:num)', 'Backend\MubalighController::update/$1');
        $routes->get('delete/(:num)', 'Backend\MubalighController::delete/$1');
        $routes->get('show/(:num)', 'Backend\MubalighController::show/$1');
        $routes->get('berkas-lampiran', 'Backend\MubalighController::showBerkasLampiran');
    });

    // --- Masjid & Mushola (SuperAdmin, Admin, OperatorMasjidMushola) ---
    $routes->group('masjid-mushola', ['filter' => 'role:SuperAdmin,Admin,OperatorMasjidMushola'], function ($routes) {
        $routes->get('/', 'Backend\MasjidMusholaController::index');
        $routes->get('create', 'Backend\MasjidMusholaController::create');
        $routes->post('store', 'Backend\MasjidMusholaController::store');
        $routes->get('edit/(:num)', 'Backend\MasjidMusholaController::edit/$1');
        $routes->post('update/(:num)', 'Backend\MasjidMusholaController::update/$1');
        $routes->get('delete/(:num)', 'Backend\MasjidMusholaController::delete/$1');
        $routes->get('show/(:num)', 'Backend\MasjidMusholaController::show/$1');
    });

    // --- Imam Masjid (SuperAdmin, Admin, OperatorMasjidMushola) ---
    $routes->group('imam-masjid', ['filter' => 'role:SuperAdmin,Admin,OperatorMasjidMushola'], function ($routes) {
        $routes->get('/', 'Backend\ImamMasjidController::index');
        $routes->get('create', 'Backend\ImamMasjidController::create');
        $routes->post('store', 'Backend\ImamMasjidController::store');
        $routes->get('edit/(:num)', 'Backend\ImamMasjidController::edit/$1');
        $routes->post('update/(:num)', 'Backend\ImamMasjidController::update/$1');
        $routes->get('delete/(:num)', 'Backend\ImamMasjidController::delete/$1');
        $routes->get('show/(:num)', 'Backend\ImamMasjidController::show/$1');
    });

    // --- Pengurus Fardu Kifayah (SuperAdmin, Admin, OperatorFarduKifayah) ---
    $routes->group('fardu-kifayah', ['filter' => 'role:SuperAdmin,Admin,OperatorFarduKifayah'], function ($routes) {
        $routes->get('/', 'Backend\FarduKifayahController::index');
        $routes->get('create', 'Backend\FarduKifayahController::create');
        $routes->post('store', 'Backend\FarduKifayahController::store');
        $routes->get('edit/(:num)', 'Backend\FarduKifayahController::edit/$1');
        $routes->post('update/(:num)', 'Backend\FarduKifayahController::update/$1');
        $routes->get('delete/(:num)', 'Backend\FarduKifayahController::delete/$1');
        $routes->get('show/(:num)', 'Backend\FarduKifayahController::show/$1');
    });

    // --- Petugas Penggali Kubur (SuperAdmin, Admin, OperatorPenggaliKubur) ---
    $routes->group('penggali-kubur', ['filter' => 'role:SuperAdmin,Admin,OperatorPenggaliKubur'], function ($routes) {
        $routes->get('/', 'Backend\PenggaliKuburController::index');
        $routes->get('create', 'Backend\PenggaliKuburController::create');
        $routes->post('store', 'Backend\PenggaliKuburController::store');
        $routes->get('edit/(:num)', 'Backend\PenggaliKuburController::edit/$1');
        $routes->post('update/(:num)', 'Backend\PenggaliKuburController::update/$1');
        $routes->get('delete/(:num)', 'Backend\PenggaliKuburController::delete/$1');
        $routes->get('show/(:num)', 'Backend\PenggaliKuburController::show/$1');
    });

    // --- Majelis Taklim (SuperAdmin, Admin, OperatorMajelisTaklim) ---
    $routes->group('majelis-taklim', ['filter' => 'role:SuperAdmin,Admin,OperatorMajelisTaklim'], function ($routes) {
        $routes->get('/', 'Backend\MajelisTaklimController::index');
        $routes->get('create', 'Backend\MajelisTaklimController::create');
        $routes->post('store', 'Backend\MajelisTaklimController::store');
        $routes->get('edit/(:num)', 'Backend\MajelisTaklimController::edit/$1');
        $routes->post('update/(:num)', 'Backend\MajelisTaklimController::update/$1');
        $routes->get('delete/(:num)', 'Backend\MajelisTaklimController::delete/$1');
        $routes->get('show/(:num)', 'Backend\MajelisTaklimController::show/$1');
    });

    // --- Lembaga TPQ & MDTA (SuperAdmin, Admin, OperatorTpqMdta) ---
    $routes->group('tpq-mdta', ['filter' => 'role:SuperAdmin,Admin,OperatorTpqMdta'], function ($routes) {
        $routes->get('/', 'Backend\TpqMdtaController::index');
        $routes->get('create', 'Backend\TpqMdtaController::create');
        $routes->post('store', 'Backend\TpqMdtaController::store');
        $routes->get('edit/(:num)', 'Backend\TpqMdtaController::edit/$1');
        $routes->post('update/(:num)', 'Backend\TpqMdtaController::update/$1');
        $routes->get('delete/(:num)', 'Backend\TpqMdtaController::delete/$1');
        $routes->get('show/(:num)', 'Backend\TpqMdtaController::show/$1');
    });

    // --- User Management (SuperAdmin ONLY) ---
    $routes->group('users', ['filter' => 'role:SuperAdmin'], function ($routes) {
        $routes->get('/', 'Backend\UserController::index');
        $routes->get('create', 'Backend\UserController::create');
        $routes->post('store', 'Backend\UserController::store');
        $routes->get('edit/(:num)', 'Backend\UserController::edit/$1');
        $routes->post('update/(:num)', 'Backend\UserController::update/$1');
        $routes->get('delete/(:num)', 'Backend\UserController::delete/$1');
    });
});
