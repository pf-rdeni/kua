<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================================
// AUTH ROUTES (Override Myth/Auth agar redirect ke dashboard)
// ============================================================
$routes->get('login', 'AuthController::login', ['as' => 'login']);
$routes->post('login', 'AuthController::attemptLogin');
$routes->get('logout', 'AuthController::logout');

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
// ============================================================
// Data Public (Dynamic)
// ============================================================
$routes->group('data', function($routes) {
    $routes->get('masjid-mushola', 'Frontend\DataController::masjid_mushola');
    $routes->get('masjid-mushola/(:num)', 'Frontend\DataController::detail_masjid/$1');
    $routes->get('mubaligh', 'Frontend\DataController::mubaligh');
    $routes->get('imam-masjid', 'Frontend\DataController::imam_masjid');
    $routes->get('majelis-taklim', 'Frontend\DataController::majelis_taklim');
    $routes->get('tpq-mdta', 'Frontend\DataController::tpq_mdta');
    // Add others as needed
});

// Jadwal Publik Mubaligh (Pakai URL Bebas Tanpa Filter Login)
$routes->get('jadwal-mubaligh/(:any)', 'Frontend\PublicJadwalController::view/$1');
$routes->post('jadwal-mubaligh/konfirmasi-hadir', 'Frontend\PublicJadwalController::konfirmasi_hadir');
$routes->post('jadwal-mubaligh/ajukan-pengganti', 'Frontend\PublicJadwalController::ajukan_pengganti');

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

    // Grup Dokumentasi Developer (Khusus SuperAdmin)
    $routes->group('dokumentasi', ['filter' => 'role:SuperAdmin'], static function ($routes) {
        $routes->get('/', 'Backend\DokumentasiController::index');
        $routes->get('arsitektur', 'Backend\DokumentasiController::arsitektur');
        $routes->get('komponen', 'Backend\DokumentasiController::komponen');
        $routes->get('auth', 'Backend\DokumentasiController::auth');
        $routes->get('komponen', 'Backend\DokumentasiController::komponen');
        $routes->get('auth', 'Backend\DokumentasiController::auth');
        $routes->get('alur-insentif', 'Backend\DokumentasiController::alurInsentif');
        $routes->get('upload-berkas', 'Backend\DokumentasiController::uploadBerkas');
        $routes->get('setting-berkas', 'Backend\DokumentasiController::settingBerkas');
        $routes->get('setting-entitas', 'Backend\DokumentasiController::settingEntitas');
        $routes->get('jadwal-ramadhan', 'Backend\DokumentasiController::jadwalRamadhan');
    });
    // --- API & AJAX Endpoints ---
    $routes->group('api', function ($routes) {
        $routes->get('personil/search-nik', 'Backend\PersonilApiController::searchNik');
        $routes->get('personil/get-by-nik', 'Backend\PersonilApiController::getByNik');
        $routes->get('personil/check-nik-sharing', 'Backend\PersonilApiController::checkNikSharing');
    });

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

    // --- Manajemen Entitas Type (SuperAdmin, Admin) ---
    $routes->group('entitas-type', ['filter' => 'role:SuperAdmin,Admin'], function ($routes) {
        $routes->get('/', 'Backend\EntitasTypeController::index');
        $routes->get('create', 'Backend\EntitasTypeController::create');
        $routes->post('store', 'Backend\EntitasTypeController::store');
        $routes->get('edit/(:num)', 'Backend\EntitasTypeController::edit/$1');
        $routes->post('update/(:num)', 'Backend\EntitasTypeController::update/$1');
        $routes->post('delete/(:num)', 'Backend\EntitasTypeController::delete/$1');
    });

    // --- Manajemen Grup Akun (SuperAdmin Only) ---
    $routes->group('groups', ['filter' => 'role:SuperAdmin'], function ($routes) {
        $routes->get('/', 'Backend\GroupController::index');
        $routes->get('create', 'Backend\GroupController::create');
        $routes->post('store', 'Backend\GroupController::store');
        $routes->get('edit/(:num)', 'Backend\GroupController::edit/$1');
        $routes->post('update/(:num)', 'Backend\GroupController::update/$1');
        $routes->post('delete/(:num)', 'Backend\GroupController::delete/$1');
    });

    // --- Dokumentasi Internal Sistem (SuperAdmin Only) ---
    $routes->get('dokumentasi', 'Backend\DokumentasiController::index', ['filter' => 'role:SuperAdmin']);

    // --- Personil (Unified: mubaligh, imam_masjid, fardu_kifayah, penggali_kubur) ---
    $routes->group('personil', function ($routes) {
        $routes->get('(:segment)', 'Backend\PersonilController::index/$1');
        $routes->get('(:segment)/create', 'Backend\PersonilController::create/$1');
        $routes->post('(:segment)/store', 'Backend\PersonilController::store/$1');
        $routes->get('(:segment)/edit/(:num)', 'Backend\PersonilController::edit/$1/$2');
        $routes->post('(:segment)/update/(:num)', 'Backend\PersonilController::update/$1/$2');
        $routes->get('(:segment)/delete/(:num)', 'Backend\PersonilController::delete/$1/$2');
        $routes->get('(:segment)/show/(:num)', 'Backend\PersonilController::show/$1/$2');
        $routes->get('(:segment)/berkas-lampiran', 'Backend\PersonilController::showBerkasLampiran/$1');
    });

    // --- Jadwal Ramadhan ---
    $routes->group('jadwal-ramadhan', ['filter' => 'role:SuperAdmin,Admin,OperatorMubaligh'], function ($routes) {
        $routes->get('/', 'Backend\JadwalRamadhanController::index');
        $routes->get('tema', 'Backend\JadwalRamadhanController::tema');
        $routes->post('save-tema', 'Backend\JadwalRamadhanController::save_tema');
        $routes->post('duplicate-tema', 'Backend\JadwalRamadhanController::duplicate_tema');
        $routes->post('duplicate-jadwal', 'Backend\JadwalRamadhanController::duplicate_jadwal');
        $routes->post('generate-tanggal', 'Backend\JadwalRamadhanController::generate_tanggal_masehi');
        $routes->get('search-mubaligh', 'Backend\JadwalRamadhanController::search_mubaligh');
        $routes->post('save-cell', 'Backend\JadwalRamadhanController::save_cell');
        $routes->post('reset-jadwal', 'Backend\JadwalRamadhanController::reset_jadwal');
        $routes->get('export-excel', 'Backend\JadwalRamadhanController::export_excel');
        $routes->get('cetak-mubaligh/(:num)', 'Backend\JadwalRamadhanController::cetak_mubaligh/$1');
        $routes->get('cetak-masjid/(:num)', 'Backend\JadwalRamadhanController::cetak_masjid/$1');
        
        // Absensi Ramadhan
        $routes->get('absensi', 'Backend\AbsensiRamadhanController::index');
        $routes->post('save-absensi', 'Backend\AbsensiRamadhanController::save_absensi_admin');
    });

    // --- Maghrib Mengaji ---
    $routes->group('maghrib-mengaji', ['filter' => 'role:SuperAdmin,Admin,OperatorMubaligh'], function ($routes) {
        $routes->get('/', 'Backend\MaghribMengajiController::index');
        $routes->post('save-matrix', 'Backend\MaghribMengajiController::save_matrix');
        $routes->post('save-cell', 'Backend\MaghribMengajiController::save_cell');
        $routes->get('search-mubaligh', 'Backend\MaghribMengajiController::search_mubaligh');
        $routes->get('cetak', 'Backend\MaghribMengajiController::get_cetak');
    });

    // --- Khotib Jumat ---
    $routes->group('khotib-jumat', ['filter' => 'role:SuperAdmin,Admin,OperatorMubaligh'], function ($routes) {
        $routes->get('/', 'Backend\KhotibJumatController::index');
        $routes->post('save-cell', 'Backend\KhotibJumatController::save_cell');
        $routes->get('search-mubaligh', 'Backend\KhotibJumatController::search_mubaligh');
        $routes->get('cetak-mubaligh/(:num)', 'Backend\KhotibJumatController::cetak_mubaligh/$1');
        $routes->get('cetak-masjid/(:num)', 'Backend\KhotibJumatController::cetak_masjid/$1');
    });

    // --- Pengajuan Insentif (per entitas type) ---
    $routes->group('pengajuan-insentif', function ($routes) {
        $routes->get('(:segment)', 'Backend\PengajuanInsentifController::index/$1');
        $routes->get('(:segment)/cetak-asn/(:num)', 'Backend\PengajuanInsentifController::cetakSuratAsn/$1/$2');
        $routes->get('(:segment)/cetak-insentif/(:num)', 'Backend\PengajuanInsentifController::cetakSuratInsentif/$1/$2');
        $routes->get('(:segment)/cetak-rekomendasi/(:num)', 'Backend\PengajuanInsentifController::cetakSuratRekomendasi/$1/$2');
        $routes->get('(:segment)/cetak-lampiran/(:num)', 'Backend\PengajuanInsentifController::cetakLampiran/$1/$2');
        $routes->get('(:segment)/cetak-gabungan/(:num)', 'Backend\PengajuanInsentifController::cetakGabungan/$1/$2');
        $routes->get('(:segment)/cetak-bulk-zip', 'Backend\PengajuanInsentifController::cetakBulkZip/$1');
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
