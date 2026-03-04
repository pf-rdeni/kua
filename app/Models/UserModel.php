<?php

namespace App\Models;

use CodeIgniter\Model;
use Faker\Generator;
use Myth\Auth\Authorization\GroupModel;
use Myth\Auth\Entities\User;

/**
 * @method User|null first()
 */
class UserModel extends Model
{
    protected $table          = 'users';
    protected $primaryKey     = 'id';
    protected $returnType     = 'App\Entities\User';
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'email', 'username', 'password_hash', 'reset_hash', 'reset_at', 'reset_expires', 'activate_hash',
        'status', 'status_message', 'active', 'force_pass_reset', 'permissions', 'deleted_at',
        // Kolom tambahan untuk menghubungkan user ke entitas tertentu
        'entitas_type', 'entitas_id',
    ];
    protected $useTimestamps   = true;
    protected $validationRules = [
        'email'         => 'required|valid_email|is_unique[users.email,id,{id}]',
        'username'      => 'required|alpha_numeric_punct|min_length[3]|max_length[30]|is_unique[users.username,id,{id}]',
        'password_hash' => 'required',
    ];
    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $afterInsert        = ['addToGroup'];

    /**
     * ID grup yang akan di-assign saat user dibuat.
     * Di-set secara internal oleh withGroup().
     *
     * @var int|null
     */
    protected $assignGroup;

    /**
     * Logs a password reset attempt for posterity sake.
     */
    public function logResetAttempt(string $email, ?string $token = null, ?string $ipAddress = null, ?string $userAgent = null)
    {
        $this->db->table('auth_reset_attempts')->insert([
            'email'      => $email,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'token'      => $token,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Logs an activation attempt for posterity sake.
     */
    public function logActivationAttempt(?string $token = null, ?string $ipAddress = null, ?string $userAgent = null)
    {
        $this->db->table('auth_activation_attempts')->insert([
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'token'      => $token,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Sets the group to assign any users created.
     *
     * @return $this
     */
    public function withGroup(string $groupName)
    {
        $group = $this->db->table('auth_groups')->where('name', $groupName)->get()->getFirstRow();

        $this->assignGroup = $group->id;

        return $this;
    }

    /**
     * Clears the group to assign to newly created users.
     *
     * @return $this
     */
    public function clearGroup()
    {
        $this->assignGroup = null;

        return $this;
    }

    /**
     * Jika ada default group di Config\Auth, assign ke user baru.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    protected function addToGroup($data)
    {
        if (is_numeric($this->assignGroup)) {
            $groupModel = model(GroupModel::class);
            $groupModel->addUserToGroup($data['id'], $this->assignGroup);
        }

        return $data;
    }

    /**
     * Faked data for Fabricator.
     */
    public function fake(Generator &$faker): User
    {
        return new User([
            'email'    => $faker->email,
            'username' => $faker->userName,
            'password' => bin2hex(random_bytes(16)),
        ]);
    }

    // =====================================================================
    // Method Tambahan: Manajemen User Entitas
    // =====================================================================

    /**
     * Ambil semua user yang terhubung ke entitas tertentu.
     *
     * @param string $entitasType  Tipe entitas, misal: masjid_mushola
     * @param int    $entitasId    ID entitas terkait
     * @return array
     */
    public function getUsersByEntitas(string $entitasType, int $entitasId): array
    {
        return $this->where('entitas_type', $entitasType)
                    ->where('entitas_id', $entitasId)
                    ->where('deleted_at', null)
                    ->findAll();
    }

    /**
     * Ambil semua user yang memiliki entitas (untuk tampilan Admin).
     * Mengembalikan data user beserta info entitas untuk tampilan lebih informatif.
     *
     * @param string|null $entitasType  Filter tipe entitas (opsional)
     * @return array
     */
    public function getUsersWithEntitas(?string $entitasType = null): array
    {
        $builder = $this->db->table('users')
            ->select('users.id, users.username, users.email, users.active, users.entitas_type, users.entitas_id, users.created_at')
            ->where('users.deleted_at', null)
            ->where('users.entitas_type IS NOT NULL', null, false)
            ->orderBy('users.entitas_type', 'ASC')
            ->orderBy('users.entitas_id', 'ASC');

        if ($entitasType) {
            $builder->where('users.entitas_type', $entitasType);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Generate username otomatis untuk operator Masjid/Mushola.
     *
     * Aturan penamaan:
     * - Masjid  → prefix "msj_"
     * - Mushola → prefix "msh_"
     * - Nama disanitasi: lowercase, hapus spasi dan karakter non-alfanumerik
     * - Jika username sudah ada (duplikat) → tambahkan id_masjid di belakang
     *
     * Contoh:
     * - Masjid Al-Amin         → msj_alamin
     * - Mushola Al-Madinah     → msh_almadinah
     * - Duplikat msj_alamin    → msj_alamin7  (id = 7)
     *
     * @param string $namaEntitas  Nama masjid/mushola
     * @param string $jenisEntitas Jenis: 'Masjid' atau 'Mushola'
     * @param int    $entitasId    ID masjid/mushola (untuk fallback duplikat)
     * @return string
     */
    public function generateUsernameForMasjid(string $namaEntitas, string $jenisEntitas, int $entitasId): string
    {
        // Tentukan prefix berdasarkan jenis entitas
        $prefix = ($jenisEntitas === 'Mushola') ? 'msh_' : 'msj_';

        // Sanitasi nama: lowercase, hapus kata generik masjid/mushola
        $namaBersih = strtolower($namaEntitas);

        // Hapus kata generik yang sudah tercermin di prefix
        $kataGenerik = ['masjid', 'mushola', 'musholla', 'msj', 'msh', 'al-', 'al '];
        foreach ($kataGenerik as $kata) {
            $namaBersih = str_replace(strtolower($kata), '', $namaBersih);
        }

        // Hapus semua karakter non-alfanumerik (termasuk tanda hubung, spasi, dll)
        $namaBersih = preg_replace('/[^a-z0-9]/', '', $namaBersih);
        $namaBersih = trim($namaBersih);

        // Fallback jika nama kosong setelah sanitasi (misal hanya nama "Al")
        if (empty($namaBersih)) {
            $namaBersih = (string) $entitasId;
        }

        // Gabungkan prefix + nama bersih
        $usernameKandidat = $prefix . $namaBersih;

        // Cek apakah username sudah dipakai, jika ya tambahkan ID di belakang
        $cekDuplikat = $this->db->table('users')
            ->where('username', $usernameKandidat)
            ->where('deleted_at IS NULL', null, false)
            ->countAllResults();

        if ($cekDuplikat > 0) {
            // Tambahkan ID entitas di belakang untuk menjamin keunikan
            $usernameKandidat = $prefix . $namaBersih . $entitasId;
        }

        return $usernameKandidat;
    }

    /**
     * Generate username otomatis untuk operator entitas generik (Multi-Entitas).
     *
     * @param string $entitasType  Tipe entitas (masjid_mushola, majelis_taklim, mubaligh)
     * @param string $namaEntitas  Nama entitas/personil
     * @param int    $entitasId    ID entitas (untuk fallback duplikat)
     * @return string
     */
    public function generateUsernameForEntitas(string $entitasType, string $namaEntitas, int $entitasId): string
    {
        // Tentukan prefix dan kata generik yang akan dihilangkan
        $prefix = '';
        $kataGenerik = [];
        
        switch ($entitasType) {
            case 'masjid_mushola':
                // Untuk masjid, lebih aman panggil method lama atau implementasi serupa (kita asumsikan fallback prefix ke msj_)
                $prefix = 'msj_';
                // Jika ingin deteksi mushola dari nama
                if (stripos($namaEntitas, 'mushola') !== false || stripos($namaEntitas, 'musholla') !== false) {
                    $prefix = 'msh_';
                }
                $kataGenerik = ['masjid', 'mushola', 'musholla', 'msj', 'msh', 'al-', 'al '];
                break;
            case 'majelis_taklim':
                $prefix = 'mt_';
                $kataGenerik = ['majelis', 'taklim', "ta'lim", 'mt ', 'al-', 'al '];
                break;
            case 'mubaligh':
                $prefix = 'mub_';
                $kataGenerik = ['ustadz', 'ustdz', 'kh ', 'kh.', 'kiyai', 'haji', 'h.', 'hj.'];
                break;
            default:
                $prefix = 'usr_';
        }

        $namaBersih = strtolower($namaEntitas);

        // Hapus kata generik
        foreach ($kataGenerik as $kata) {
            $namaBersih = str_replace(strtolower($kata), '', $namaBersih);
        }

        // Hapus semua karakter non-alfanumerik
        $namaBersih = preg_replace('/[^a-z0-9]/', '', $namaBersih);
        $namaBersih = trim($namaBersih);

        if (empty($namaBersih)) {
            $namaBersih = (string) $entitasId;
        }

        $usernameKandidat = $prefix . $namaBersih;

        // Cek duplikat
        $cekDuplikat = $this->db->table('users')
            ->where('username', $usernameKandidat)
            ->where('deleted_at IS NULL', null, false)
            ->countAllResults();

        if ($cekDuplikat > 0) {
            $usernameKandidat = $prefix . $namaBersih . $entitasId;
        }

        return $usernameKandidat;
    }
}
