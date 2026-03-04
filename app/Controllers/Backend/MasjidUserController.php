<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\MasjidMusholaModel;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\GroupModel;
use Myth\Auth\Password;

/**
 * MasjidUserController
 *
 * Controller untuk manajemen user operator Masjid/Mushola.
 * Admin/SuperAdmin bisa lihat & kelola user semua masjid.
 * Setiap masjid hanya memiliki 1 user operator aktif.
 */
class MasjidUserController extends BaseController
{
    protected $userModel;
    protected $masjidModel;
    protected $groupModel;

    // Password default untuk user operator baru atau saat reset
    const DEFAULT_PASSWORD = 'Kua@12345';

    public function __construct()
    {
        $this->userModel   = new UserModel();
        $this->masjidModel = new MasjidMusholaModel();
        $this->groupModel  = new GroupModel();
    }

    /**
     * Overview: Tampilkan SEMUA user operator dari semua masjid.
     * Diakses dari menu sidebar "User Operator Masjid".
     * Hanya Admin/SuperAdmin yang bisa mengakses.
     */
    public function overview()
    {
        // Ambil semua masjid yang sudah memiliki user operator
        $semauMasjid   = $this->masjidModel->findAll();
        $usersEntitas  = $this->userModel->getUsersWithEntitas('masjid_mushola');

        // Kelompokkan user berdasarkan entitas_id (id_masjid_mushola)
        $userPerMasjid = [];
        foreach ($usersEntitas as $u) {
            $userPerMasjid[$u['entitas_id']][] = $u;
        }

        $data = [
            'title'      => 'User Operator Masjid & Mushola',
            'breadcrumb' => [
                ['title' => 'Home',                    'url' => 'admin/dashboard'],
                ['title' => 'Masjid & Mushola',        'url' => 'admin/masjid-mushola'],
                ['title' => 'User Operator Masjid',    'url' => ''],
            ],
            'masjidList'    => $semauMasjid,
            'userPerMasjid' => $userPerMasjid,
            'defaultPw'     => self::DEFAULT_PASSWORD,
        ];

        return view('backend/masjid_mushola/users_overview', $data);
    }

    /**
     * Daftar user operator untuk satu masjid tertentu.
     *
     * @param int $masjidId ID masjid pada tbl_masjid_mushola
     */
    public function index(int $masjidId)
    {
        // Ambil data masjid untuk breadcrumb dan tampilan
        $masjid = $this->masjidModel->find($masjidId);
        if (!$masjid) {
            return redirect()->to('admin/masjid-mushola')->with('error', 'Data Masjid tidak ditemukan.');
        }

        // Ambil semua user yang terhubung ke masjid ini
        $users       = $this->userModel->getUsersByEntitas('masjid_mushola', $masjidId);
        $usersDetail = [];

        foreach ($users as $user) {
            // Ambil grup/role untuk setiap user
            $groups = $this->groupModel->getGroupsForUser($user->id);
            $usersDetail[] = [
                'user'   => $user,
                'groups' => $groups,
            ];
        }

        $data = [
            'title'      => 'User Operator - ' . $masjid['nama'],
            'breadcrumb' => [
                ['title' => 'Home',              'url' => 'admin/dashboard'],
                ['title' => 'Masjid & Mushola',  'url' => 'admin/masjid-mushola'],
                ['title' => esc($masjid['nama']), 'url' => 'admin/masjid-mushola/' . $masjidId],
                ['title' => 'User Operator',     'url' => ''],
            ],
            'masjid'       => $masjid,
            'usersDetail'  => $usersDetail,
            'defaultPw'    => self::DEFAULT_PASSWORD,
        ];

        return view('backend/masjid_mushola/users', $data);
    }

    /**
     * Proses membuat user operator baru untuk sebuah masjid.
     * Username di-generate otomatis berdasarkan nama masjid.
     *
     * @param int $masjidId ID masjid
     */
    public function store(int $masjidId)
    {
        // Validasi data masjid
        $masjid = $this->masjidModel->find($masjidId);
        if (!$masjid) {
            return redirect()->to('admin/masjid-mushola')->with('error', 'Data Masjid tidak ditemukan.');
        }

        // Cek apakah masjid sudah punya user operator aktif
        $usersExist = $this->userModel->getUsersByEntitas('masjid_mushola', $masjidId);
        if (!empty($usersExist)) {
            return redirect()->to("admin/masjid-mushola/{$masjidId}/users")
                ->with('error', 'Masjid ini sudah memiliki user operator. Hapus user lama terlebih dahulu atau reset passwordnya.');
        }

        // Generate username otomatis berdasarkan nama masjid
        $username = $this->userModel->generateUsernameForMasjid(
            $masjid['nama'],
            $masjid['jenis'],
            $masjidId
        );

        // Email opsional: generate email dummy jika tidak diisi
        $email = $this->request->getPost('email');
        if (empty($email)) {
            // Email dummy berbasis username agar valid dan unik
            $email = $username . '@kua-skl.local';
        }

        // Validasi email jika diisi manual
        $rules = [
            'email' => "permit_empty|valid_email|is_unique[users.email]",
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Buat entity user baru
        $config = config('Auth');
        $user   = new User([
            'username'     => $username,
            'email'        => $email,
            'password'     => self::DEFAULT_PASSWORD,
            'entitas_type' => 'masjid_mushola',
            'entitas_id'   => $masjidId,
        ]);

        // Langsung aktifkan user tanpa perlu konfirmasi email
        $user->activate();

        // Simpan user
        $this->userModel->skipValidation(false);
        if (!$this->userModel->save($user)) {
            return redirect()->back()->with('errors', $this->userModel->errors());
        }

        $userId = $this->userModel->getInsertID();

        // Tambahkan ke grup OperatorMasjidMushola
        $grup = $this->groupModel->where('name', 'OperatorMasjidMushola')->first();
        if ($grup) {
            $this->groupModel->addUserToGroup($userId, $grup->id);
        }

        return redirect()->to("admin/masjid-mushola/{$masjidId}/users")
            ->with('success', "User operator berhasil dibuat. Username: <strong>{$username}</strong> | Password default: <strong>" . self::DEFAULT_PASSWORD . "</strong>");
    }

    /**
     * Reset password user operator ke password default.
     * Hanya Admin/SuperAdmin yang dapat melakukan ini.
     *
     * @param int $masjidId ID masjid
     * @param int $userId   ID user yang akan direset
     */
    public function resetPassword(int $masjidId, int $userId)
    {
        // Validasi kepemilikan user ke masjid ini (security check)
        $user = $this->userModel->find($userId);
        if (!$user || $user->entitas_type !== 'masjid_mushola' || (int) $user->entitas_id !== $masjidId) {
            return redirect()->to("admin/masjid-mushola/{$masjidId}/users")
                ->with('error', 'User tidak valid atau bukan milik masjid ini.');
        }

        // Set password baru (default password)
        $user->password         = self::DEFAULT_PASSWORD;
        $user->force_pass_reset = false; // Tidak paksa reset setelah login

        if (!$this->userModel->save($user)) {
            return redirect()->to("admin/masjid-mushola/{$masjidId}/users")
                ->with('error', 'Gagal mereset password.');
        }

        return redirect()->to("admin/masjid-mushola/{$masjidId}/users")
            ->with('success', "Password berhasil direset ke: <strong>" . self::DEFAULT_PASSWORD . "</strong>");
    }

    /**
     * Toggle status aktif/nonaktif user operator.
     *
     * @param int $masjidId ID masjid
     * @param int $userId   ID user
     */
    public function toggleActive(int $masjidId, int $userId)
    {
        // Security check: pastikan user adalah operator masjid ini
        $user = $this->userModel->find($userId);
        if (!$user || $user->entitas_type !== 'masjid_mushola' || (int) $user->entitas_id !== $masjidId) {
            return redirect()->to("admin/masjid-mushola/{$masjidId}/users")
                ->with('error', 'User tidak valid atau bukan milik masjid ini.');
        }

        // Toggle status aktif
        if ($user->active) {
            $user->deactivate();
            $pesan = 'User operator berhasil dinonaktifkan.';
        } else {
            $user->activate();
            $pesan = 'User operator berhasil diaktifkan.';
        }

        $this->userModel->save($user);

        return redirect()->to("admin/masjid-mushola/{$masjidId}/users")
            ->with('success', $pesan);
    }

    /**
     * Hapus user operator (soft delete).
     *
     * @param int $masjidId ID masjid
     * @param int $userId   ID user yang akan dihapus
     */
    public function delete(int $masjidId, int $userId)
    {
        // Jangan hapus akun sendiri
        if (user_id() == $userId) {
            return redirect()->to("admin/masjid-mushola/{$masjidId}/users")
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Security check: pastikan user adalah operator masjid ini
        $user = $this->userModel->find($userId);
        if (!$user || $user->entitas_type !== 'masjid_mushola' || (int) $user->entitas_id !== $masjidId) {
            return redirect()->to("admin/masjid-mushola/{$masjidId}/users")
                ->with('error', 'User tidak valid atau bukan milik masjid ini.');
        }

        // Hapus user dari semua grup dulu
        $this->groupModel->removeUserFromAllGroups($userId);

        // Soft delete user
        $this->userModel->delete($userId);

        return redirect()->to("admin/masjid-mushola/{$masjidId}/users")
            ->with('success', 'User operator berhasil dihapus.');
    }
}
