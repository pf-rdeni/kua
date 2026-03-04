<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\KeuanganKategoriModel;
use App\Models\KeuanganKategoriHiddenModel;
use App\Models\EntitasTypeModel;

class KeuanganKategoriController extends BaseController
{
    protected $kategoriModel;
    protected $hiddenModel;
    protected $entitasTypeModel;

    public function __construct()
    {
        $this->kategoriModel    = new KeuanganKategoriModel();
        $this->hiddenModel      = new KeuanganKategoriHiddenModel();
        $this->entitasTypeModel = new EntitasTypeModel();
    }

    private function getEntitasConfig(string $entitasType)
    {
        $entitasConfig = $this->entitasTypeModel->where('kode', $entitasType)->first();
        if (!$entitasConfig) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return $entitasConfig;
    }

    private function getCurrentUserId(): ?int
    {
        if (!function_exists('user')) {
            helper('auth');
        }
        $currentUser = (function_exists('user') && logged_in()) ? user() : null;
        return $currentUser ? $currentUser->id : null;
    }

    private function isOperatorEntitas(array $config): bool
    {
        if (empty($config['operator_group'])) return false;
        return in_groups($config['operator_group']) && !in_groups('SuperAdmin') && !in_groups('Admin');
    }

    private function getOperatorEntitasId(array $config): ?int
    {
        if (!$this->isOperatorEntitas($config)) return null;
        $u = user();
        return ($u && $u->entitas_type === $config['kode']) ? (int)$u->entitas_id : null;
    }

    /**
     * Tampilkan daftar kategori
     */
    public function index(string $entitasType)
    {
        $config = $this->getEntitasConfig($entitasType);
        
        // Ambil ID dari entitas pengguna yang login secara aman jika ia adalah operator
        $entitasId = $this->getOperatorEntitasId($config);

        // Ambil semua kategori global
        $globalCategories = $this->kategoriModel->where('entitas_type IS NULL')->orderBy('nama_kategori', 'ASC')->findAll();
        
        // Ambil ID kategori global yang disembunyikan
        $hiddenIds = $this->hiddenModel->getHiddenCategoryIds($entitasType, $entitasId);

        // Tandai global categories apakah hidden atau tidak
        foreach ($globalCategories as &$kat) {
            $kat['is_hidden_by_entitas'] = in_array($kat['id'], $hiddenIds);
        }
        unset($kat);

        // Ambil kategori khusus entitas ini
        $builder = $this->kategoriModel->where('entitas_type', $entitasType);
        if ($entitasId !== null) {
            $builder->where('entitas_id', $entitasId);
        } else {
            $builder->where('entitas_id IS NULL');
        }
        $customCategories = $builder->orderBy('nama_kategori', 'ASC')->findAll();

        $data = [
            'title'            => 'Kelola Kategori Keuangan - ' . $config['nama_label'],
            'entitasType'      => $entitasType,
            'entitasId'        => $entitasId,
            'globalCategories' => $globalCategories,
            'customCategories' => $customCategories,
            'validation'       => \Config\Services::validation(),
        ];

        return view('backend/keuangan/kategori/index', $data);
    }

    /**
     * Simpan Kategori Kustom
     */
    public function store(string $entitasType)
    {
        $rules = [
            'nama_kategori' => 'required|min_length[3]|max_length[100]',
            'jenis'         => 'required|in_list[pemasukan,pengeluaran,keduanya]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $config = $this->getEntitasConfig($entitasType);
        $entitasId = $this->getOperatorEntitasId($config);

        // Jika admin global (SuperAdmin) yang buat dari menu ini, anggap kategori global jika tidak sedang melayani entitas spesifik.
        // Tapi asumsikan rute ini khusus untuk entitas level:
        $this->kategoriModel->save([
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'jenis'         => $this->request->getPost('jenis'),
            'warna_badge'   => $this->request->getPost('warna_badge') ?? 'secondary',
            'deskripsi'     => $this->request->getPost('deskripsi'),
            'entitas_type'  => $entitasType,
            'entitas_id'    => $entitasId,
            'is_active'     => 1
        ]);

        return redirect()->back()->with('success', 'Kategori kustom berhasil ditambahkan.');
    }

    /**
     * Hapus (Soft/Hard) Kategori Kustom
     */
    public function delete(string $entitasType, int $id)
    {
        $config = $this->getEntitasConfig($entitasType);
        $entitasId = $this->getOperatorEntitasId($config);

        $kat = $this->kategoriModel->find($id);
        if ($kat && $kat['entitas_type'] === $entitasType) {
            // Keamanan: Hanya hapus jika entitasId cocok (atau jika admin)
            if ($entitasId && (int)$kat['entitas_id'] !== $entitasId) {
                return redirect()->back()->with('error', 'Akses ditolak.');
            }
            $this->kategoriModel->delete($id);
            return redirect()->back()->with('success', 'Kategori kustom berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'Gagal menghapus kategori.');
    }

    /**
     * Toggle Sembunyikan Kategori Global
     */
    public function toggleHidden(string $entitasType, int $idKategori)
    {
        $config = $this->getEntitasConfig($entitasType);
        $entitasId = $this->getOperatorEntitasId($config);

        // Cek apakah sudah hidden
        $builder = $this->hiddenModel->where('id_kategori', $idKategori)
                                     ->where('entitas_type', $entitasType);
        if ($entitasId !== null) {
            $builder->where('entitas_id', $entitasId);
        } else {
            $builder->where('entitas_id IS NULL');
        }
        
        $existing = $builder->first();

        if ($existing) {
            // Un-hide
            $this->hiddenModel->delete($existing['id']);
            return redirect()->back()->with('success', 'Kategori kembali ditampilkan.');
        } else {
            // Hide
            $this->hiddenModel->insert([
                'id_kategori'  => $idKategori,
                'entitas_type' => $entitasType,
                'entitas_id'   => $entitasId,
            ]);
            return redirect()->back()->with('success', 'Kategori berhasil disembunyikan.');
        }
    }
}
