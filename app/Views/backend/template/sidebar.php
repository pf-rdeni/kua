<!-- Sidebar - Menu Navigasi Kiri -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= base_url('/') ?>" class="brand-link">
        <img src="<?= base_url('assets/icon/logo_kua.png') ?>" alt="KUA Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">KUA SKL</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <?php
        // Load auth helper & get current user safely
        if (!function_exists('user')) {
            helper('auth');
        }
        $currentUser = (function_exists('user') && logged_in()) ? user() : null;
        $username = $currentUser ? $currentUser->username : 'User';
        $userGroups = [];
        if ($currentUser) {
            $groupModel = new \Myth\Auth\Models\GroupModel();
            $groups = $groupModel->getGroupsForUser($currentUser->id);
            $userGroups = array_column($groups, 'name');
        }

        /**
         * Fungsi helper: Ambil jenis masjid/mushola dari database untuk operator yang sedang login.
         * Mengembalikan string 'Masjid' atau 'Mushola' jika ditemukan, atau null jika tidak.
         * Berguna untuk membuat label dinamis di seluruh sidebar.
         */
        function getJenisMasjidOperator(array $userGroups): ?string
        {
            if (!in_array('OperatorMasjidMushola', $userGroups)
                || in_array('SuperAdmin', $userGroups)
                || in_array('Admin', $userGroups)) {
                return null; // Bukan operator, tidak perlu query
            }

            $u = (function_exists('user') && logged_in()) ? user() : null;
            if (!$u || empty($u->entitas_id)) {
                return null;
            }

            $db  = \Config\Database::connect();
            $row = $db->table('tbl_masjid_mushola')
                      ->select('jenis')
                      ->where('id_masjid_mushola', $u->entitas_id)
                      ->get()->getRow();

            return ($row && !empty($row->jenis)) ? $row->jenis : null;
        }
        ?>
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <div class="img-circle elevation-2 d-flex justify-content-center align-items-center bg-info text-white font-weight-bold" 
                     style="width: 34px; height: 34px; font-size: 0.85rem; user-select: none;">
                    <?= strtoupper(substr($username, 0, 2)) ?>
                </div>
            </div>
            <div class="info">
                <a href="#" class="d-block" style="white-space: normal;">
                    <?= esc($username) ?>
                </a>
                <small class="text-muted" style="display: block; margin-top: 2px;">
                    <?= esc(ucfirst($userGroups[0] ?? 'User')) ?>
                </small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <!-- Dashboard (semua role) -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/dashboard') ?>" class="nav-link <?= uri_string() == 'admin/dashboard' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Separator PENDATAAN -->
                <li class="nav-header">PENDATAAN</li>

                <?php
                // Load entitas types dinamis dari database
                $entitasTypeModel = new \App\Models\EntitasTypeModel();
                $entitasTypes = $entitasTypeModel->getAccessibleForUser($userGroups);
                ?>
                <?php foreach ($entitasTypes as $et): ?>
                <?php
                    // Sembunyikan masjid_mushola dan majelis_taklim dari menu loop PENDATAAN
                    // majelis_taklim punya blok menu tersendiri di bawah
                    if ($et['kode'] === 'masjid_mushola') continue;
                    if ($et['kode'] === 'majelis_taklim') continue;

                    $personilUrl  = 'admin/personil/' . $et['kode'];
                    $berkasUrl    = 'admin/personil/' . $et['kode'] . '/berkas-lampiran';
                    $insentifUrl  = 'admin/pengajuan-insentif/' . $et['kode'];
                    $isMenuOpen   = strpos(uri_string(), $personilUrl) !== false || strpos(uri_string(), $insentifUrl) !== false;
                    $isDataActive   = (uri_string() == $personilUrl || (strpos(uri_string(), $personilUrl) !== false && strpos(uri_string(), 'berkas-lampiran') === false));
                    $isBerkasActive  = strpos(uri_string(), $berkasUrl) !== false;
                    $isInsentifActive = strpos(uri_string(), $insentifUrl) !== false;
                ?>
                <li class="nav-item has-treeview <?= $isMenuOpen ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= $isMenuOpen ? 'active' : '' ?>">
                        <i class="nav-icon <?= esc($et['icon'] ?? 'fas fa-users') ?>"></i>
                        <p>
                            <?= esc($et['nama_label']) ?>
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url($personilUrl) ?>" class="nav-link <?= $isDataActive ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data <?= esc($et['nama_label']) ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url($berkasUrl) ?>" class="nav-link <?= $isBerkasActive ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Berkas Lampiran</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url($insentifUrl) ?>" class="nav-link <?= $isInsentifActive ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pengajuan Insentif</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endforeach; ?>



                <?php
                    $isMajelisAreaOpen = strpos(uri_string(), 'admin/majelis-taklim') !== false
                                      || (strpos(uri_string(), 'admin/agenda-masjid') !== false && isset($_GET['entitas_type']) && $_GET['entitas_type'] === 'majelis_taklim')
                                      || (strpos(uri_string(), 'admin/keuangan/laporan') !== false && isset($_GET['entitas_type']) && $_GET['entitas_type'] === 'majelis_taklim');
                    $isMajelisDataActive = strpos(uri_string(), 'admin/majelis-taklim') !== false
                                        && strpos(uri_string(), '/users') === false;
                    $isMajelisAgendaActive = strpos(uri_string(), 'admin/agenda-masjid') !== false && isset($_GET['entitas_type']) && $_GET['entitas_type'] === 'majelis_taklim';
                    $isMajelisKeuanganActive = strpos(uri_string(), 'admin/keuangan/laporan') !== false && isset($_GET['entitas_type']) && $_GET['entitas_type'] === 'majelis_taklim';
                    $isMajelisUserActive = strpos(uri_string(), 'admin/majelis-taklim/users') !== false;
                ?>
                <?php if (in_array('SuperAdmin', $userGroups) || in_array('Admin', $userGroups) || in_array('OperatorMajelisTaklim', $userGroups)): ?>
                <!-- Majelis Taklim (Treeview) -->
                <li class="nav-item has-treeview <?= $isMajelisAreaOpen ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= $isMajelisAreaOpen ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-chalkboard-teacher"></i>
                        <p>
                            Majelis Taklim
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <!-- Sub-menu: Data Majelis Taklim -->
                        <li class="nav-item">
                            <a href="<?= base_url('admin/majelis-taklim') ?>" class="nav-link <?= $isMajelisDataActive ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Majelis Taklim</p>
                            </a>
                        </li>

                        <!-- Sub-menu: Agenda Kegiatan -->
                        <li class="nav-item">
                            <a href="<?= base_url('admin/agenda-masjid?entitas_type=majelis_taklim') ?>" class="nav-link <?= $isMajelisAgendaActive ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon text-success"></i>
                                <p>Agenda Kegiatan</p>
                            </a>
                        </li>

                        <!-- Sub-menu: Laporan Keuangan -->
                        <li class="nav-item">
                            <a href="<?= base_url('admin/keuangan/laporan?entitas_type=majelis_taklim') ?>" class="nav-link <?= $isMajelisKeuanganActive ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon text-info"></i>
                                <p>Laporan Keuangan</p>
                            </a>
                        </li>

                        <?php if (in_array('SuperAdmin', $userGroups) || in_array('Admin', $userGroups)): ?>
                        <!-- Sub-menu: User Akun (Admin/SuperAdmin) -->
                        <li class="nav-item">
                            <a href="<?= base_url('admin/majelis-taklim/users') ?>" class="nav-link <?= $isMajelisUserActive ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon text-warning"></i>
                                <p>User Akun Majelis</p>
                            </a>
                        </li>
                <?php endif; ?>
                    </ul>
                </li>
                <?php endif; // end Majelis Taklim menu ?>

                <?php if (in_array('SuperAdmin', $userGroups) || in_array('Admin', $userGroups) || in_array('OperatorMasjidMushola', $userGroups)): ?>
                <?php
                    // Cek apakah sedang di area Masjid & Mushola atau Display Masjid
                    $isMasjidAreaOpen = strpos(uri_string(), 'admin/masjid-mushola') !== false
                                     || strpos(uri_string(), 'admin/display-masjid') !== false;
                    $isMasjidDataActive    = strpos(uri_string(), 'admin/masjid-mushola') !== false
                                          && strpos(uri_string(), '/users') === false
                                          && strpos(uri_string(), 'admin/masjid-mushola/create') === false; // Exclude create page
                    $isMasjidDisplayActive = strpos(uri_string(), 'admin/display-masjid') !== false;
                    $isMasjidUserActive    = strpos(uri_string(), 'admin/masjid-mushola/users') !== false;

                    // Label dinamis pakai fungsi helper
                    $jenisMasjid      = getJenisMasjidOperator($userGroups);
                    $menuLabelMasjid  = $jenisMasjid ?? 'Masjid &amp; Mushola';
                    $subMenuLabelData = 'Data ' . ($jenisMasjid ?? 'Masjid &amp; Mushola');
                ?>
                <!-- Masjid & Mushola (Treeview) -->
                <li class="nav-item has-treeview <?= $isMasjidAreaOpen ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= $isMasjidAreaOpen ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-mosque"></i>
                        <p>
                            <?= $menuLabelMasjid ?>
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <!-- Sub-menu: Data Masjid & Mushola -->
                        <li class="nav-item">
                            <a href="<?= base_url('admin/masjid-mushola') ?>" class="nav-link <?= $isMasjidDataActive ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?= $subMenuLabelData ?></p>
                            </a>
                        </li>

                        <?php if (in_array('SuperAdmin', $userGroups) || in_array('Admin', $userGroups) || in_array('OperatorMasjidMushola', $userGroups)): ?>
                        <!-- Sub-menu: Display Masjid (Admin, SuperAdmin, dan OperatorMasjidMushola) -->
                        <li class="nav-item">
                            <a href="<?= base_url('admin/display-masjid') ?>" class="nav-link <?= $isMasjidDisplayActive ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon text-info"></i>
                                <p>Display Masjid</p>
                            </a>
                        </li>

                        <!-- Sub-menu: Agenda Kegiatan Ramadhan (Admin, SuperAdmin, OperatorMasjidMushola) -->
                        <li class="nav-item">
                            <a href="<?= base_url('admin/agenda-masjid') ?>" class="nav-link <?= strpos(uri_string(), 'admin/agenda-masjid') !== false ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon text-success"></i>
                                <p>Agenda Ramadhan</p>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (in_array('SuperAdmin', $userGroups) || in_array('Admin', $userGroups)): ?>
                        <!-- Sub-menu: User Operator (Admin/SuperAdmin saja yang lihat) -->
                        <li class="nav-item">
                            <a href="<?= base_url('admin/masjid-mushola/users') ?>" class="nav-link <?= $isMasjidUserActive ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon text-warning"></i>
                                <p>User Operator Masjid</p>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if (in_array('SuperAdmin', $userGroups) || in_array('Admin', $userGroups) || in_array('OperatorTpqMdta', $userGroups)): ?>
                <!-- Lembaga TPQ & MDTA -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/tpq-mdta') ?>" class="nav-link <?= strpos(uri_string(), 'admin/tpq-mdta') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-school"></i>
                        <p>TPQ & MDTA</p>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (in_array('SuperAdmin', $userGroups) || in_array('Admin', $userGroups) || in_array('OperatorMubaligh', $userGroups)): ?>
                <!-- Separator PENJADWALAN -->
                <li class="nav-header">PENJADWALAN</li>

                <?php
                    $isRamadhanActive = strpos(uri_string(), 'admin/jadwal-ramadhan') !== false;
                ?>
                <li class="nav-item has-treeview <?= $isRamadhanActive ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= $isRamadhanActive ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>
                            Jadwal Ramadhan
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('admin/jadwal-ramadhan') ?>" class="nav-link <?= url_is('admin/jadwal-ramadhan') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Matriks Jadwal</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/jadwal-ramadhan/tema') ?>" class="nav-link <?= url_is('admin/jadwal-ramadhan/tema') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tema Ceramah</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/jadwal-ramadhan/absensi') ?>" class="nav-link <?= url_is('admin/jadwal-ramadhan/absensi') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon text-success"></i>
                                <p>Absensi Kehadiran</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Menu Khotib Jumat -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/khotib-jumat') ?>" class="nav-link <?= strpos(uri_string(), 'admin/khotib-jumat') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-bullhorn"></i>
                        <p>Khotib Jumat</p>
                    </a>
                </li>
                
                <!-- Menu Maghrib Mengaji -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/maghrib-mengaji') ?>" class="nav-link <?= strpos(uri_string(), 'admin/maghrib-mengaji') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-book-reader"></i>
                        <p>Maghrib Mengaji</p>
                    </a>
                </li>

                <?php if (in_array('SuperAdmin', $userGroups) || in_array('Admin', $userGroups)): ?>
                <!-- Menu User Mubaligh -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/mubaligh-users') ?>" class="nav-link <?= strpos(uri_string(), 'admin/mubaligh-users') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-users text-warning"></i>
                        <p>User Akun Mubaligh</p>
                    </a>
                </li>
                <?php endif; ?>

                <?php endif; ?>

                <!-- ============================== -->
                <!-- Separator KEUANGAN -->
                <!-- ============================== -->
                <?php
                // Menu keuangan tampil jika user punya akses ke minimal satu entitas
                $keuanganEntitas = $entitasTypeModel->getAccessibleForUser($userGroups);
                
                // Jika user adalah Operator Masjid, pastikan hanya Kas Masjid yang tampil di sidebar Keuangan (sembunyikan kas Imam Masjid dsb)
                if (in_array('OperatorMasjidMushola', $userGroups) && !in_array('SuperAdmin', $userGroups) && !in_array('Admin', $userGroups)) {
                    $keuanganEntitas = array_filter($keuanganEntitas, function($k) {
                        return $k['kode'] === 'masjid_mushola';
                    });
                }

                $isKeuanganOpen  = strpos(uri_string(), 'admin/keuangan') !== false;
                if (!empty($keuanganEntitas)):
                ?>
                <li class="nav-header">KEUANGAN</li>

                <!-- Dashboard Keuangan -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/keuangan/dashboard') ?>" class="nav-link <?= url_is('admin/keuangan/dashboard') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-wallet text-success"></i>
                        <p>Dashboard Keuangan</p>
                    </a>
                </li>

                <!-- Laporan Umum -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/keuangan/laporan') ?>" class="nav-link <?= url_is('admin/keuangan/laporan') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-file-alt text-info"></i>
                        <p>Laporan Keuangan</p>
                    </a>
                </li>

                <!-- Sub-menu Keuangan per Entitas yang bisa diakses -->
                <?php foreach ($keuanganEntitas as $ke): ?>
                <?php
                    $kTransUrl = 'admin/keuangan/transaksi/' . $ke['kode'];
                    $kIuranUrl = 'admin/keuangan/iuran/' . $ke['kode'];
                    $kKasUrl   = 'admin/keuangan/kas/' . $ke['kode'];
                    $kKategoriUrl = 'admin/keuangan/kategori/' . $ke['kode'];
                    $isKEntitasOpen = strpos(uri_string(), $kTransUrl) !== false
                                  || strpos(uri_string(), $kIuranUrl) !== false
                                  || strpos(uri_string(), $kKasUrl) !== false
                                  || strpos(uri_string(), $kKategoriUrl) !== false;

                    // Label dinamis pakai fungsi helper
                    $kLabel = $ke['nama_label'];
                    if ($ke['kode'] === 'masjid_mushola') {
                        $jenisMasjidK = getJenisMasjidOperator($userGroups);
                        if ($jenisMasjidK) {
                            $kLabel = 'Keuangan ' . $jenisMasjidK;
                        }
                    }
                ?>
                <li class="nav-item has-treeview <?= $isKEntitasOpen ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= $isKEntitasOpen ? 'active' : '' ?>">
                        <i class="nav-icon <?= esc($ke['icon'] ?? 'fas fa-coins') ?>"></i>
                        <p>
                            <?= $kLabel ?>
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url($kTransUrl) ?>" class="nav-link <?= strpos(uri_string(), $kTransUrl) !== false ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon text-success"></i>
                                <p>Transaksi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url($kIuranUrl) ?>" class="nav-link <?= strpos(uri_string(), $kIuranUrl) !== false ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon text-warning"></i>
                                <p>Iuran Anggota</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url($kKasUrl) ?>" class="nav-link <?= strpos(uri_string(), $kKasUrl) !== false ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon text-info"></i>
                                <p>Kelola Kas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url($kKategoriUrl) ?>" class="nav-link <?= strpos(uri_string(), $kKategoriUrl) !== false ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon text-secondary"></i>
                                <p>Kelola Kategori</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endforeach; ?>
                <?php endif; ?>


                <?php if (in_array('SuperAdmin', $userGroups) || in_array('Admin', $userGroups)): ?>
                <!-- Separator PENGATURAN -->
                <li class="nav-header">PENGATURAN</li>

                <!-- Setting Berkas Lampiran -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/setting-berkas') ?>" class="nav-link <?= strpos(uri_string(), 'admin/setting-berkas') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Setting Berkas</p>
                    </a>
                </li>

                <!-- Manajemen Entitas Type -->
                <?php
                $settingEntitasUrl = 'admin/entitas-type';
                $isSettingEntitasActive = strpos(uri_string(), $settingEntitasUrl) !== false;
                ?>
                <li class="nav-item">
                    <a href="<?= base_url($settingEntitasUrl) ?>" class="nav-link <?= $isSettingEntitasActive ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-list-ul"></i>
                        <p>Pengaturan Entitas</p>
                    </a>
                </li>

                <?php if (in_array('SuperAdmin', $userGroups)): ?>
                <!-- Manajemen User (SuperAdmin Only) -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/users') ?>" class="nav-link <?= strpos(uri_string(), 'admin/users') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>Manajemen User</p>
                    </a>
                </li>
                
                <!-- Manajemen Grup Akun (SuperAdmin Only) -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/groups') ?>" class="nav-link <?= strpos(uri_string(), 'admin/groups') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>Manajemen Grup</p>
                    </a>
                </li>
                
                <!-- Separator DOKUMENTASI -->
                <li class="nav-header">DOKUMENTASI</li>
                
                <?php
                    $isDokumentasiActive = strpos(uri_string(), 'admin/dokumentasi') !== false;
                ?>
                <li class="nav-item has-treeview <?= $isDokumentasiActive ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= $isDokumentasiActive ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-book-open"></i>
                        <p>
                            Dokumentasi Sistem
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('admin/dokumentasi/arsitektur') ?>" class="nav-link <?= url_is('admin/dokumentasi/arsitektur') || url_is('admin/dokumentasi') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>General System</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/dokumentasi/auth') ?>" class="nav-link <?= url_is('admin/dokumentasi/auth') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Input Data Personil</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/dokumentasi/komponen') ?>" class="nav-link <?= url_is('admin/dokumentasi/komponen') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Berkas Lampiran</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/dokumentasi/alur-insentif') ?>" class="nav-link <?= url_is('admin/dokumentasi/alur-insentif') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pengajuan Insentif</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/dokumentasi/upload-berkas') ?>" class="nav-link <?= url_is('admin/dokumentasi/upload-berkas') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Panduan Upload Berkas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/dokumentasi/setting-berkas') ?>" class="nav-link <?= url_is('admin/dokumentasi/setting-berkas') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon text-warning"></i>
                                <p>Panduan Setting Berkas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/dokumentasi/setting-entitas') ?>" class="nav-link <?= url_is('admin/dokumentasi/setting-entitas') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon text-success"></i>
                                <p>Panduan Setting Entitas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/dokumentasi/jadwal-ramadhan') ?>" class="nav-link <?= url_is('admin/dokumentasi/jadwal-ramadhan') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon text-info"></i>
                                <p>Jadwal Ramadhan</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <!-- Separator AKUN -->
                <li class="nav-header">AKUN</li>

                <!-- Ganti Password (semua user yang login) -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/akun/ganti-password') ?>" class="nav-link <?= strpos(uri_string(), 'admin/akun/ganti-password') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-key text-warning"></i>
                        <p>Ganti Password</p>
                    </a>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <a href="<?= base_url('logout') ?>" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                        <p class="text-danger">Logout</p>
                    </a>
                </li>
                
            </ul>
        </nav>
    </div>
</aside>
