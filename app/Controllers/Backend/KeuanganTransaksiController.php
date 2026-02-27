<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\KeuanganTransaksiModel;
use App\Models\KeuanganKasModel;
use App\Models\KeuanganKategoriModel;
use App\Models\EntitasTypeModel;
use App\Models\MasjidMusholaModel;

/**
 * KeuanganTransaksiController
 * CRUD transaksi pemasukan dan pengeluaran per entitas.
 * Akses dikontrol: Admin bisa akses semua, Operator hanya entitasnya.
 */
class KeuanganTransaksiController extends BaseController
{
    protected $transaksiModel;
    protected $kasModel;
    protected $kategoriModel;
    protected $entitasTypeModel;
    protected $masjidModel;

    public function __construct()
    {
        $this->transaksiModel   = new KeuanganTransaksiModel();
        $this->kasModel         = new KeuanganKasModel();
        $this->kategoriModel    = new KeuanganKategoriModel();
        $this->entitasTypeModel = new EntitasTypeModel();
        $this->masjidModel      = new MasjidMusholaModel();
    }

    /**
     * Resolve config entitas atau 404, sekaligus cek otorisasi user
     */
    private function getEntitasConfig(string $entitasType): array
    {
        $config = $this->entitasTypeModel->getByKode($entitasType);
        if (!$config) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Tipe entitas '{$entitasType}' tidak ditemukan.");
        }

        // Cek otorisasi dinamis (sama dengan PersonilController)
        $allowedGroups = ['SuperAdmin', 'Admin'];
        if (!empty($config['operator_group'])) {
            $allowedGroups[] = $config['operator_group'];
        }
        if (!function_exists('in_groups')) {
            helper('auth');
        }
        if (!\in_groups($allowedGroups)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Akses Ditolak. Anda tidak punya izin untuk data keuangan " . $config['nama_label']);
        }

        return $config;
    }

    /**
     * Ambil ID user login saat ini
     */
    private function getCurrentUserId(): ?int
    {
        if (!function_exists('user')) {
            helper('auth');
        }
        $currentUser = (function_exists('user') && logged_in()) ? user() : null;
        return $currentUser ? $currentUser->id : null;
    }

    /**
     * Tampilkan daftar transaksi per entitas
     */
    public function index(string $entitasType)
    {
        $config = $this->getEntitasConfig($entitasType);

        // Ambil filter dari GET
        $filters = [
            'entitas_type'   => $entitasType,
            'jenis'          => $this->request->getGet('jenis') ?? '',
            'bulan'          => $this->request->getGet('bulan') ?? '',
            'tahun'          => $this->request->getGet('tahun') ?? date('Y'),
            'id_kategori'    => $this->request->getGet('id_kategori') ?? '',
        ];

        // Untuk masjid/mushola: ambil list masjid untuk filter tambahan
        $masjidList   = [];
        $filterEntitasId = $this->request->getGet('entitas_id') ?? '';
        if ($entitasType === 'masjid_mushola') {
            $masjidList = $this->masjidModel->findAll();
            if ($filterEntitasId !== '') {
                $filters['entitas_id'] = $filterEntitasId;
            }
        }

        $transaksiList = $this->transaksiModel->getWithDetail($filters);
        $rekap         = $this->transaksiModel->getRekap($filters);

        // Ambil data kas untuk entitas ini
        $kasList = $this->kasModel->getByEntitasType($entitasType);

        // Data tren bulanan untuk grafik mini
        $trenBulanan = $this->transaksiModel->getTrenBulanan($entitasType, null, (int)($filters['tahun'] ?: date('Y')));

        $data = [
            'title'          => 'Keuangan ' . $config['nama_label'],
            'entitasType'    => $entitasType,
            'entitasConfig'  => $config,
            'transaksiList'  => $transaksiList,
            'filters'        => $filters,
            'rekap'          => $rekap,
            'kasList'        => $kasList,
            'kategoriList'   => $this->kategoriModel->getActive(),
            'masjidList'     => $masjidList,
            'filterEntitasId'=> $filterEntitasId,
            'trenBulanan'    => $trenBulanan,
            'tahunList'      => range(date('Y'), date('Y') - 5),
        ];

        return view('backend/keuangan/transaksi/index', $data);
    }

    /**
     * Form tambah transaksi
     */
    public function create(string $entitasType)
    {
        $config = $this->getEntitasConfig($entitasType);

        $masjidList = [];
        if ($entitasType === 'masjid_mushola') {
            $masjidList = $this->masjidModel->findAll();
        }

        $data = [
            'title'         => 'Tambah Transaksi - ' . $config['nama_label'],
            'entitasType'   => $entitasType,
            'entitasConfig' => $config,
            'kasList'       => $this->kasModel->getByEntitasType($entitasType),
            'kategoriList'  => $this->kategoriModel->getActive(),
            'masjidList'    => $masjidList,
            'transaksi'     => null, // mode tambah
            'validation'    => \Config\Services::validation(),
        ];

        return view('backend/keuangan/transaksi/form', $data);
    }

    /**
     * Simpan transaksi baru
     */
    public function store(string $entitasType)
    {
        $config = $this->getEntitasConfig($entitasType);

        $rules = [
            'jenis'              => 'required|in_list[pemasukan,pengeluaran]',
            'jumlah'             => 'required|numeric|greater_than[0]',
            'tanggal_transaksi'  => 'required|valid_date[Y-m-d]',
            'id_kategori'        => 'permit_empty|numeric',
            'keterangan'         => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle upload bukti pembayaran
        $buktiName = null;
        $bukti = $this->request->getFile('bukti');
        if ($bukti && $bukti->isValid() && !$bukti->hasMoved()) {
            $buktiName = $bukti->getRandomName();
            $uploadDir = FCPATH . 'uploads/keuangan';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $bukti->move($uploadDir, $buktiName);
        }

        $entitasId = $this->request->getPost('entitas_id') ?? null;

        $saveData = [
            'id_kas'            => $this->request->getPost('id_kas') ?: null,
            'entitas_type'      => $entitasType,
            'entitas_id'        => $entitasId ?: null,
            'id_kategori'       => $this->request->getPost('id_kategori') ?: null,
            'jenis'             => $this->request->getPost('jenis'),
            'jumlah'            => $this->request->getPost('jumlah'),
            'keterangan'        => $this->request->getPost('keterangan'),
            'tanggal_transaksi' => $this->request->getPost('tanggal_transaksi'),
            'no_referensi'      => $this->request->getPost('no_referensi') ?: null,
            'bukti'             => $buktiName,
            'created_by'        => $this->getCurrentUserId(),
        ];

        $this->transaksiModel->save($saveData);

        return redirect()->to('/admin/keuangan/transaksi/' . $entitasType)
                         ->with('success', 'Transaksi berhasil disimpan.');
    }

    /**
     * Form edit transaksi
     */
    public function edit(string $entitasType, int $id)
    {
        $config    = $this->getEntitasConfig($entitasType);
        $transaksi = $this->transaksiModel->find($id);

        if (!$transaksi || $transaksi['entitas_type'] !== $entitasType) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data transaksi tidak ditemukan.');
        }

        $masjidList = [];
        if ($entitasType === 'masjid_mushola') {
            $masjidList = $this->masjidModel->findAll();
        }

        $data = [
            'title'         => 'Edit Transaksi - ' . $config['nama_label'],
            'entitasType'   => $entitasType,
            'entitasConfig' => $config,
            'kasList'       => $this->kasModel->getByEntitasType($entitasType),
            'kategoriList'  => $this->kategoriModel->getActive(),
            'masjidList'    => $masjidList,
            'transaksi'     => $transaksi,
            'validation'    => \Config\Services::validation(),
        ];

        return view('backend/keuangan/transaksi/form', $data);
    }

    /**
     * Update transaksi
     */
    public function update(string $entitasType, int $id)
    {
        $config    = $this->getEntitasConfig($entitasType);
        $transaksi = $this->transaksiModel->find($id);

        if (!$transaksi || $transaksi['entitas_type'] !== $entitasType) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data transaksi tidak ditemukan.');
        }

        $rules = [
            'jenis'             => 'required|in_list[pemasukan,pengeluaran]',
            'jumlah'            => 'required|numeric|greater_than[0]',
            'tanggal_transaksi' => 'required|valid_date[Y-m-d]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle upload bukti (ganti file jika ada yang baru)
        $buktiName = $transaksi['bukti'];
        $bukti = $this->request->getFile('bukti');
        if ($bukti && $bukti->isValid() && !$bukti->hasMoved()) {
            // Hapus bukti lama jika ada
            if ($buktiName && file_exists(FCPATH . 'uploads/keuangan/' . $buktiName)) {
                @unlink(FCPATH . 'uploads/keuangan/' . $buktiName);
            }
            $buktiName = $bukti->getRandomName();
            $uploadDir = FCPATH . 'uploads/keuangan';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $bukti->move($uploadDir, $buktiName);
        }

        $entitasId = $this->request->getPost('entitas_id') ?? null;

        $updateData = [
            'id_kas'            => $this->request->getPost('id_kas') ?: null,
            'entitas_id'        => $entitasId ?: null,
            'id_kategori'       => $this->request->getPost('id_kategori') ?: null,
            'jenis'             => $this->request->getPost('jenis'),
            'jumlah'            => $this->request->getPost('jumlah'),
            'keterangan'        => $this->request->getPost('keterangan'),
            'tanggal_transaksi' => $this->request->getPost('tanggal_transaksi'),
            'no_referensi'      => $this->request->getPost('no_referensi') ?: null,
            'bukti'             => $buktiName,
        ];

        $this->transaksiModel->update($id, $updateData);

        return redirect()->to('/admin/keuangan/transaksi/' . $entitasType)
                         ->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Hapus transaksi
     */
    public function delete(string $entitasType, int $id)
    {
        $config    = $this->getEntitasConfig($entitasType);
        $transaksi = $this->transaksiModel->find($id);

        if (!$transaksi || $transaksi['entitas_type'] !== $entitasType) {
            return redirect()->back()->with('error', 'Data transaksi tidak ditemukan.');
        }

        // Hapus file bukti jika ada
        if ($transaksi['bukti'] && file_exists(FCPATH . 'uploads/keuangan/' . $transaksi['bukti'])) {
            @unlink(FCPATH . 'uploads/keuangan/' . $transaksi['bukti']);
        }

        $this->transaksiModel->delete($id);

        return redirect()->to('/admin/keuangan/transaksi/' . $entitasType)
                         ->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Manajemen Kas per entitas
     * Tambah/edit kas (khusus masjid: satu kas per masjid)
     */
    public function kas(string $entitasType)
    {
        $config = $this->getEntitasConfig($entitasType);

        $masjidList = [];
        if ($entitasType === 'masjid_mushola') {
            $masjidList = $this->masjidModel->findAll();
        }

        // Ambil semua kas untuk entitas ini dengan saldo berjalan
        $kasList = $this->kasModel->getByEntitasType($entitasType);
        foreach ($kasList as &$kas) {
            $kas['saldo_berjalan'] = $this->kasModel->hitungSaldo($kas['id']);
        }
        unset($kas);

        $data = [
            'title'         => 'Manajemen Kas - ' . $config['nama_label'],
            'entitasType'   => $entitasType,
            'entitasConfig' => $config,
            'kasList'       => $kasList,
            'masjidList'    => $masjidList,
            'validation'    => \Config\Services::validation(),
        ];

        return view('backend/keuangan/kas/index', $data);
    }

    /**
     * Simpan kas baru
     */
    public function storeKas(string $entitasType)
    {
        $this->getEntitasConfig($entitasType);

        $rules = [
            'nama_kas'   => 'required|min_length[3]|max_length[150]',
            'saldo_awal' => 'permit_empty|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kasModel->save([
            'entitas_type' => $entitasType,
            'entitas_id'   => $this->request->getPost('entitas_id') ?: null,
            'nama_kas'     => $this->request->getPost('nama_kas'),
            'saldo_awal'   => $this->request->getPost('saldo_awal') ?? 0,
            'keterangan'   => $this->request->getPost('keterangan') ?: null,
            'is_active'    => 1,
            'created_by'   => $this->getCurrentUserId(),
        ]);

        return redirect()->to('/admin/keuangan/kas/' . $entitasType)
                         ->with('success', 'Kas berhasil ditambahkan.');
    }

    /**
     * Update kas
     */
    public function updateKas(string $entitasType, int $id)
    {
        $this->getEntitasConfig($entitasType);
        $kas = $this->kasModel->find($id);

        if (!$kas || $kas['entitas_type'] !== $entitasType) {
            return redirect()->back()->with('error', 'Data kas tidak ditemukan.');
        }

        $rules = [
            'nama_kas'   => 'required|min_length[3]|max_length[150]',
            'saldo_awal' => 'permit_empty|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kasModel->update($id, [
            'nama_kas'   => $this->request->getPost('nama_kas'),
            'saldo_awal' => $this->request->getPost('saldo_awal') ?? 0,
            'keterangan' => $this->request->getPost('keterangan') ?: null,
            'entitas_id' => $this->request->getPost('entitas_id') ?: null,
        ]);

        return redirect()->to('/admin/keuangan/kas/' . $entitasType)
                         ->with('success', 'Kas berhasil diperbarui.');
    }
}
