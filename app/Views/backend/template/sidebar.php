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

                <?php if (in_array('SuperAdmin', $userGroups) || in_array('Admin', $userGroups) || in_array('OperatorMubaligh', $userGroups)): ?>
                <!-- Mubaligh (Treeview) -->
                <li class="nav-item has-treeview <?= strpos(uri_string(), 'admin/mubaligh') !== false ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= strpos(uri_string(), 'admin/mubaligh') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>
                            Mubaligh
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('admin/mubaligh') ?>" class="nav-link <?= uri_string() == 'admin/mubaligh' || (strpos(uri_string(), 'admin/mubaligh') !== false && strpos(uri_string(), 'berkas-lampiran') === false) ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Mubaligh</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/mubaligh/berkas-lampiran') ?>" class="nav-link <?= strpos(uri_string(), 'admin/mubaligh/berkas-lampiran') !== false ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Berkas Lampiran</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if (in_array('SuperAdmin', $userGroups) || in_array('Admin', $userGroups) || in_array('OperatorMasjidMushola', $userGroups)): ?>
                <!-- Masjid & Mushola -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/masjid-mushola') ?>" class="nav-link <?= strpos(uri_string(), 'admin/masjid-mushola') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-mosque"></i>
                        <p>Masjid & Mushola</p>
                    </a>
                </li>
                <!-- Imam Masjid -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/imam-masjid') ?>" class="nav-link <?= strpos(uri_string(), 'admin/imam-masjid') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-check"></i>
                        <p>Imam Masjid</p>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (in_array('SuperAdmin', $userGroups) || in_array('Admin', $userGroups) || in_array('OperatorFarduKifayah', $userGroups)): ?>
                <!-- Pengurus Fardu Kifayah -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/fardu-kifayah') ?>" class="nav-link <?= strpos(uri_string(), 'admin/fardu-kifayah') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-hands-helping"></i>
                        <p>Fardu Kifayah</p>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (in_array('SuperAdmin', $userGroups) || in_array('Admin', $userGroups) || in_array('OperatorPenggaliKubur', $userGroups)): ?>
                <!-- Petugas Penggali Kubur -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/penggali-kubur') ?>" class="nav-link <?= strpos(uri_string(), 'admin/penggali-kubur') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-hard-hat"></i>
                        <p>Penggali Kubur</p>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (in_array('SuperAdmin', $userGroups) || in_array('Admin', $userGroups) || in_array('OperatorMajelisTaklim', $userGroups)): ?>
                <!-- Majelis Taklim -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/majelis-taklim') ?>" class="nav-link <?= strpos(uri_string(), 'admin/majelis-taklim') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-chalkboard-teacher"></i>
                        <p>Majelis Taklim</p>
                    </a>
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

                <?php if (in_array('SuperAdmin', $userGroups)): ?>
                <!-- Separator PENGATURAN -->
                <li class="nav-header">PENGATURAN</li>

                <!-- Manajemen User (SuperAdmin Only) -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/users') ?>" class="nav-link <?= strpos(uri_string(), 'admin/users') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>Manajemen User</p>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Separator AKUN -->
                <li class="nav-header">AKUN</li>
                
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
