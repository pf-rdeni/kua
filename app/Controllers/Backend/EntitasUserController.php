<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\MasjidMusholaModel;
use App\Models\MajelisTaklimModel;
use App\Models\PersonilModel;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\GroupModel;
use Myth\Auth\Password;

/**
 * EntitasUserController
 *
 * Controller generik untuk manajemen user operator berbagai entitas.
 * (Misal: Majelis Taklim, Mubaligh).
 * Menggantikan pola MasjidUserController untuk penggunaan yang lebih luas.
 */
class EntitasUserController extends BaseController
{
    protected $userModel;
    protected $groupModel;
    
    // Model Spesifik
    protected $masjidModel;
    protected $majelisModel;
    protected $personilModel;

    // Password default untuk user baru / reset
    const DEFAULT_PASSWORD = 'Kua@12345';

    public function __construct()
    {
        $this->userModel     = new UserModel();
        $this->groupModel    = new GroupModel();
        
        $this->masjidModel   = new MasjidMusholaModel();
        $this->majelisModel  = new MajelisTaklimModel();
        $this->personilModel = new PersonilModel();
    }

    /**
     * Konfigurasi meta per entitas_type
     */
    private function getConfig(string $entitasType): array
    {
        switch ($entitasType) {
            case 'masjid_mushola':
                return [
                    'title'      => 'KUA Masjid & Mushola',
                    'group_name' => 'OperatorMasjidMushola',
                    'url_base'   => 'admin/masjid-mushola',
                    'url_users'  => 'admin/masjid-mushola', // route utamanya
                    'model'      => $this->masjidModel,
                    'pk'         => 'id_masjid_mushola',
                    'name_field' => 'nama',
                    'prefix'     => 'msj_', // Diurus di UserModel
                ];
            case 'majelis_taklim':
                return [
                    'title'      => 'Majelis Taklim',
                    'group_name' => 'OperatorMajelisTaklim',
                    'url_base'   => 'admin/majelis-taklim',
                    'url_users'  => 'admin/majelis-taklim',
                    'model'      => $this->majelisModel,
                    'pk'         => 'id_majelis_taklim',
                    'name_field' => 'nama_majelis_taklim',
                    'prefix'     => 'mt_',
                ];
            case 'mubaligh':
                return [
                    'title'      => 'Mubaligh',
                    'group_name' => 'OperatorMubaligh',
                    'url_base'   => 'admin/personil/mubaligh',
                    'url_users'  => 'admin/mubaligh-users', 
                    'model'      => $this->personilModel,
                    'pk'         => 'id',
                    'name_field' => 'nama_lengkap',
                    'prefix'     => 'mub_',
                ];
            default:
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Tipe entitas tidak valid.');
        }
    }

    /**
     * Tampilkan data list entitas untuk overview
     */
    private function getEntitasList(string $entitasType)
    {
        $cfg = $this->getConfig($entitasType);
        $model = $cfg['model'];
        
        if ($entitasType === 'mubaligh') {
            return $model->where('status_aktif', 1)->ofType('mubaligh')->findAll();
        }
        return $model->findAll();
    }

    /**
     * Overview: Tampilkan SEMUA user operator untuk satu jenis entitas.
     * Diakses dari menu sidebar terkait. (Hanya Admin / SuperAdmin).
     */
    public function overview(string $entitasType)
    {
        $cfg = $this->getConfig($entitasType);

        // Ambil data base entitas
        $entitasList   = $this->getEntitasList($entitasType);
        $usersEntitas  = $this->userModel->getUsersWithEntitas($entitasType);

        // Kelompokkan user berdasarkan entitas_id
        $userPerEntitas = [];
        foreach ($usersEntitas as $u) {
            $userPerEntitas[$u['entitas_id']][] = $u;
        }

        $data = [
            'title'          => 'User Akun - ' . $cfg['title'],
            'entitasType'    => $entitasType,
            'cfg'            => $cfg,
            'breadcrumb'     => [
                ['title' => 'Home',              'url' => 'admin/dashboard'],
                ['title' => $cfg['title'],       'url' => $cfg['url_base']],
                ['title' => 'User Manajemen',    'url' => ''],
            ],
            'entitasList'    => $entitasList,
            'userPerEntitas' => $userPerEntitas,
            'defaultPw'      => self::DEFAULT_PASSWORD,
        ];

        return view('backend/entitas_user/overview', $data);
    }

    /**
     * Daftar user operator untuk satu ID entitas tertentu.
     */
    public function index(string $entitasType, int $entitasId)
    {
        $cfg = $this->getConfig($entitasType);

        $entitas = $cfg['model']->find($entitasId);
        if (!$entitas) {
            return redirect()->to($cfg['url_base'])->with('error', 'Data entitas tidak ditemukan.');
        }

        // Ambil semua user yang terhubung ke entitas ini
        $users       = $this->userModel->getUsersByEntitas($entitasType, $entitasId);
        $usersDetail = [];

        foreach ($users as $user) {
            $groups = $this->groupModel->getGroupsForUser($user->id);
            $usersDetail[] = [
                'user'   => $user,
                'groups' => $groups,
            ];
        }

        $namaEntitas = $entitas[$cfg['name_field']];

        $data = [
            'title'       => 'User Akun - ' . esc($namaEntitas),
            'entitasType' => $entitasType,
            'entitasId'   => $entitasId,
            'cfg'         => $cfg,
            'breadcrumb'  => [
                ['title' => 'Home',              'url' => 'admin/dashboard'],
                ['title' => $cfg['title'],       'url' => $cfg['url_base']],
                ['title' => esc($namaEntitas),   'url' => $cfg['url_base'] . '/' . $entitasId],
                ['title' => 'User Manajemen',    'url' => ''],
            ],
            'entitas'      => $entitas,
            'namaEntitas'  => $namaEntitas,
            'usersDetail'  => $usersDetail,
            'defaultPw'    => self::DEFAULT_PASSWORD,
        ];

        return view('backend/entitas_user/users', $data);
    }

    /**
     * Store: Buat user baru untuk entitas tersebut
     */
    public function store(string $entitasType, int $entitasId)
    {
        $cfg = $this->getConfig($entitasType);
        
        $entitas = $cfg['model']->find($entitasId);
        if (!$entitas) {
            return redirect()->to($cfg['url_base'])->with('error', 'Data entitas tidak ditemukan.');
        }

        $namaEntitas = $entitas[$cfg['name_field']];

        // Cek apakah entitas sudah punya user aktif (hanya 1 user per entitas dlm sistem skrg)
        $usersExist = $this->userModel->getUsersByEntitas($entitasType, $entitasId);
        if (!empty($usersExist)) {
            return redirect()->to($cfg['url_users'] . '/' . $entitasId . '/users')
                ->with('error', 'Entitas ini sudah memiliki user. Hapus user lama terlebih dahulu jika ingin mengganti.');
        }

        // Generate Username
        $username = $this->userModel->generateUsernameForEntitas(
            $entitasType,
            $namaEntitas,
            $entitasId
        );

        $email = $this->request->getPost('email');
        if (empty($email)) {
            $email = $username . '@kua-skl.local';
        }

        $rules = [
            'email' => "permit_empty|valid_email|is_unique[users.email]",
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = new User([
            'username'     => $username,
            'email'        => $email,
            'password'     => self::DEFAULT_PASSWORD,
            'entitas_type' => $entitasType,
            'entitas_id'   => $entitasId,
        ]);

        $user->activate();

        $this->userModel->skipValidation(false);
        if (!$this->userModel->save($user)) {
            return redirect()->back()->with('errors', $this->userModel->errors());
        }

        $userId = $this->userModel->getInsertID();

        // Add to Group
        $grup = $this->groupModel->where('name', $cfg['group_name'])->first();
        if ($grup) {
            $this->groupModel->addUserToGroup($userId, $grup->id);
        }

        $redirectUrl = $cfg['url_users'] . '/' . $entitasId . '/users';
        if ($entitasType === 'masjid_mushola') {
            $redirectUrl = "admin/masjid-mushola/{$entitasId}/users";
        }

        return redirect()->to($redirectUrl)
            ->with('success', "Akun berhasil dibuat. Username: <strong>{$username}</strong> | Password default: <strong>" . self::DEFAULT_PASSWORD . "</strong>");
    }

    /**
     * Reset password ke default
     */
    public function resetPassword(string $entitasType, int $entitasId, int $userId)
    {
        $cfg = $this->getConfig($entitasType);
        $redirectUrl = $entitasType === 'masjid_mushola' ? "admin/masjid-mushola/{$entitasId}/users" : $cfg['url_users'] . '/' . $entitasId . '/users';

        $user = $this->userModel->find($userId);
        if (!$user || $user->entitas_type !== $entitasType || (int) $user->entitas_id !== $entitasId) {
            return redirect()->to($redirectUrl)->with('error', 'User tidak valid.');
        }

        $user->password         = self::DEFAULT_PASSWORD;
        $user->force_pass_reset = false;

        if (!$this->userModel->save($user)) {
            return redirect()->to($redirectUrl)->with('error', 'Gagal mereset password.');
        }

        return redirect()->to($redirectUrl)
            ->with('success', "Password berhasil direset ke: <strong>" . self::DEFAULT_PASSWORD . "</strong>");
    }

    /**
     * Toggle Aktif/Nonaktif
     */
    public function toggleActive(string $entitasType, int $entitasId, int $userId)
    {
        $cfg = $this->getConfig($entitasType);
        $redirectUrl = $entitasType === 'masjid_mushola' ? "admin/masjid-mushola/{$entitasId}/users" : $cfg['url_users'] . '/' . $entitasId . '/users';

        $user = $this->userModel->find($userId);
        if (!$user || $user->entitas_type !== $entitasType || (int) $user->entitas_id !== $entitasId) {
            return redirect()->to($redirectUrl)->with('error', 'User tidak valid.');
        }

        if ($user->active) {
            $user->deactivate();
            $pesan = 'User berhasil dinonaktifkan.';
        } else {
            $user->activate();
            $pesan = 'User berhasil diaktifkan.';
        }

        $this->userModel->save($user);

        return redirect()->to($redirectUrl)->with('success', $pesan);
    }

    /**
     * Soft Delete user
     */
    public function delete(string $entitasType, int $entitasId, int $userId)
    {
        $cfg = $this->getConfig($entitasType);
        $redirectUrl = $entitasType === 'masjid_mushola' ? "admin/masjid-mushola/{$entitasId}/users" : $cfg['url_users'] . '/' . $entitasId . '/users';

        if (user_id() == $userId) {
            return redirect()->to($redirectUrl)->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user = $this->userModel->find($userId);
        if (!$user || $user->entitas_type !== $entitasType || (int) $user->entitas_id !== $entitasId) {
            return redirect()->to($redirectUrl)->with('error', 'User tidak valid.');
        }

        $this->groupModel->removeUserFromAllGroups($userId);
        $this->userModel->delete($userId);

        return redirect()->to($redirectUrl)->with('success', 'User berhasil dihapus.');
    }
}
