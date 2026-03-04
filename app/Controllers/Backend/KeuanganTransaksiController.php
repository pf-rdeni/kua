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
     * Apakah user login adalah Operator untuk entitas spesifik (bukan Admin/SuperAdmin)?
     */
    private function isOperatorEntitas(array $config): bool
    {
        if (empty($config['operator_group'])) return false;
        return in_groups($config['operator_group']) && !in_groups('SuperAdmin') && !in_groups('Admin');
    }

    /**
     * Ambil entitas_id milik operator yang login, atau null jika admin.
     */
    private function getOperatorEntitasId(array $config): ?int
    {
        if (!$this->isOperatorEntitas($config)) return null;
        $u = user();
        return ($u && $u->entitas_type === $config['kode']) ? (int)$u->entitas_id : null;
    }

    /**
     * Tampilkan daftar transaksi per entitas
     */
    public function index(string $entitasType)
    {
        $config = $this->getEntitasConfig($entitasType);

        $operatorEntitasId = $this->getOperatorEntitasId($config);

        // Ambil filter dari GET
        $filters = [
            'entitas_type'   => $entitasType,
            'jenis'          => $this->request->getGet('jenis') ?? '',
            'bulan'          => $this->request->getGet('bulan') ?? '',
            'tahun'          => $this->request->getGet('tahun') ?? date('Y'),
            'id_kategori'    => $this->request->getGet('id_kategori') ?? '',
        ];

        // Ambil list entitas (masjid/majelis) untuk dropdown filter
        $entitasList     = [];
        $filterEntitasId = $this->request->getGet('entitas_id') ?? '';
        
        $modelEntitas = null;
        $pkRow = '';
        if ($entitasType === 'masjid_mushola') {
            $modelEntitas = $this->masjidModel;
            $pkRow = 'id_masjid_mushola';
        } elseif ($entitasType === 'majelis_taklim') {
            $modelEntitas = new \App\Models\MajelisTaklimModel();
            $pkRow = 'id_majelis_taklim';
        }

        if ($modelEntitas) {
            if ($operatorEntitasId) {
                // Operator hanya lihat data entitas sendiri — kunci tanpa pilihan
                $entitasList = $modelEntitas->where($pkRow, $operatorEntitasId)->findAll();
                $filterEntitasId = $operatorEntitasId;
                $filters['entitas_id'] = $operatorEntitasId;
            } else {
                $entitasList = $modelEntitas->findAll();
                if ($filterEntitasId !== '') {
                    $filters['entitas_id'] = $filterEntitasId;
                }
            }
        }

        $transaksiList = $this->transaksiModel->getWithDetail($filters);
        $rekap         = $this->transaksiModel->getRekap($filters);

        // Ambil data kas untuk entitas ini (filter per entitas jika operator)
        $kasList = $operatorEntitasId
            ? $this->kasModel->where('entitas_id', $operatorEntitasId)->where('entitas_type', $entitasType)->findAll()
            : $this->kasModel->getByEntitasType($entitasType);

        // Siapkan Entitas ID untuk mengambil list Kategori
        $entitasIdForCategory = $operatorEntitasId ?? (logged_in() ? user()->entitas_id : null);

        // Data tren bulanan untuk grafik mini
        $trenBulanan = $this->transaksiModel->getTrenBulanan($entitasType, $operatorEntitasId, (int)($filters['tahun'] ?: date('Y')));

        $data = [
            'title'           => 'Keuangan ' . $config['nama_label'],
            'entitasType'     => $entitasType,
            'entitasConfig'   => $config,
            'transaksiList'   => $transaksiList,
            'filters'         => $filters,
            'rekap'           => $rekap,
            'kasList'         => $kasList,
            'kategoriList'    => $this->kategoriModel->getActiveForEntitas($entitasType, $entitasIdForCategory),
            'entitasList'     => $entitasList,
            'pkRow'           => $pkRow,
            'filterEntitasId' => $filterEntitasId,
            'trenBulanan'     => $trenBulanan,
            'tahunList'       => range(date('Y'), date('Y') - 5),
            'isOperatorEntitas'=> $this->isOperatorEntitas($config),
        ];

        return view('backend/keuangan/transaksi/index', $data);
    }

    /**
     * Form tambah transaksi
     */
    public function create(string $entitasType)
    {
        $config = $this->getEntitasConfig($entitasType);
        $operatorEntitasId = $this->getOperatorEntitasId($config);

        $entitasList = [];
        $pkRow = '';
        if ($entitasType === 'masjid_mushola') {
            $entitasList = $operatorEntitasId
                ? $this->masjidModel->where('id_masjid_mushola', $operatorEntitasId)->findAll()
                : $this->masjidModel->findAll();
            $pkRow = 'id_masjid_mushola';
        } elseif ($entitasType === 'majelis_taklim') {
            $modelMt = new \App\Models\MajelisTaklimModel();
            $entitasList = $operatorEntitasId
                ? $modelMt->where('id_majelis_taklim', $operatorEntitasId)->findAll()
                : $modelMt->findAll();
            $pkRow = 'id_majelis_taklim';
        }

        $entitasIdForCategory = $operatorEntitasId ?? user()->entitas_id;
        $kasList = $operatorEntitasId
            ? $this->kasModel->where('entitas_id', $operatorEntitasId)->where('entitas_type', $entitasType)->findAll()
            : $this->kasModel->getByEntitasType($entitasType);
        foreach ($kasList as &$k) {
            $k['saldo_berjalan'] = $this->kasModel->hitungSaldo($k['id']);
        }

        $data = [
            'title'             => 'Tambah Transaksi - ' . $config['nama_label'],
            'entitasType'       => $entitasType,
            'entitasConfig'     => $config,
            'kasList'           => $kasList,
            'kategoriList'      => $this->kategoriModel->getActiveForEntitas($entitasType, $entitasIdForCategory),
            'entitasList'       => $entitasList,
            'pkRow'             => $pkRow,
            'transaksi'         => null,
            'jenis'             => $this->request->getGet('jenis'),
            'validation'        => \Config\Services::validation(),
            'operatorEntitasId' => $operatorEntitasId,
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

        // --- VALIDASI SISA KAS UNTUK PENGELUARAN ---
        $jenis = $this->request->getPost('jenis');
        $idKas = $this->request->getPost('id_kas');
        $jumlah = (float)$this->request->getPost('jumlah');

        if ($jenis === 'pengeluaran' && !empty($idKas)) {
            $dataKas = $this->kasModel->find($idKas);
            if ($dataKas) {
                $saldoSaatIni = $this->kasModel->hitungSaldo($idKas);
                if ($jumlah > $saldoSaatIni) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Transaksi gagal disimpan. Saldo kas saat ini (Rp ' . number_format($saldoSaatIni, 0, ',', '.') . ') tidak mencukupi untuk jumlah pengeluaran ini.');
                }
            }
        }
        // -------------------------------------------

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
        // Jika operator, paksa gunakan id entitas sendiri (keamanan: cegah manipulasi POST)
        $operatorEntitasId = $this->getOperatorEntitasId($config);
        if ($operatorEntitasId) {
            $entitasId = $operatorEntitasId;
        }

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
        $config = $this->getEntitasConfig($entitasType);
        $operatorEntitasId = $this->getOperatorEntitasId($config);

        $transaksi = $this->transaksiModel->find($id);
        if (!$transaksi || $transaksi['entitas_type'] !== $entitasType) {
            return redirect()->to('admin/keuangan/transaksi/'.$entitasType)->with('error', 'Transaksi tidak ditemukan.');
        }

        // Keamanan: cegah operator edit transaksi entitas lain
        if ($operatorEntitasId && (int)$transaksi['entitas_id'] !== $operatorEntitasId) {
            return redirect()->to('admin/keuangan/transaksi/'.$entitasType)->with('error', 'Akses ditolak.');
        }

        $entitasList = [];
        $pkRow = '';
        if ($entitasType === 'masjid_mushola') {
            $entitasList = $operatorEntitasId
                ? $this->masjidModel->where('id_masjid_mushola', $operatorEntitasId)->findAll()
                : $this->masjidModel->findAll();
            $pkRow = 'id_masjid_mushola';
        } elseif ($entitasType === 'majelis_taklim') {
            $modelMt = new \App\Models\MajelisTaklimModel();
            $entitasList = $operatorEntitasId
                ? $modelMt->where('id_majelis_taklim', $operatorEntitasId)->findAll()
                : $modelMt->findAll();
            $pkRow = 'id_majelis_taklim';
        }

        $entitasIdForCategory = $operatorEntitasId ?? (logged_in() ? user()->entitas_id : null);
        $kasList = $operatorEntitasId
            ? $this->kasModel->where('entitas_id', $operatorEntitasId)->where('entitas_type', $entitasType)->findAll()
            : $this->kasModel->getByEntitasType($entitasType);
        foreach ($kasList as &$k) {
            $k['saldo_berjalan'] = $this->kasModel->hitungSaldo($k['id']);
        }

        $data = [
            'title'             => 'Edit Transaksi - ' . $config['nama_label'],
            'entitasType'       => $entitasType,
            'entitasConfig'     => $config,
            'kasList'           => $kasList,
            'kategoriList'      => $this->kategoriModel->getActiveForEntitas($entitasType, $entitasIdForCategory),
            'entitasList'       => $entitasList,
            'pkRow'             => $pkRow,
            'transaksi'         => $transaksi,
            'jenis'             => $transaksi['jenis'],
            'validation'        => \Config\Services::validation(),
            'operatorEntitasId' => $operatorEntitasId,
        ];

        return view('backend/keuangan/transaksi/form', $data);
    }

    /**
     * Update transaksi
     */
    public function update(string $entitasType, int $id)
    {
        $config    = $this->getEntitasConfig($entitasType);
        $operatorEntitasId = $this->getOperatorEntitasId($config);
        $transaksi = $this->transaksiModel->find($id);

        if (!$transaksi || $transaksi['entitas_type'] !== $entitasType) {
            return redirect()->to('admin/keuangan/transaksi/'.$entitasType)->with('error', 'Transaksi tidak ditemukan.');
        }

        // Keamanan: cegah operator update transaksi entitas lain
        if ($operatorEntitasId && (int)$transaksi['entitas_id'] !== $operatorEntitasId) {
            return redirect()->to('admin/keuangan/transaksi/'.$entitasType)->with('error', 'Akses ditolak.');
        }

        $rules = [
            'jenis'             => 'required|in_list[pemasukan,pengeluaran]',
            'jumlah'            => 'required|numeric|greater_than[0]',
            'tanggal_transaksi' => 'required|valid_date[Y-m-d]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // --- VALIDASI SISA KAS UNTUK PENGELUARAN SAAT UPDATE ---
        $jenis     = $this->request->getPost('jenis');
        $idKasBaru = $this->request->getPost('id_kas');
        $jumlahTrx = (float)$this->request->getPost('jumlah');

        if ($jenis === 'pengeluaran' && !empty($idKasBaru)) {
            $dataKas = $this->kasModel->find($idKasBaru);
            if ($dataKas) {
                // Kalkulasi Saldo Real Terakhir sebelum Update (jika kas yang digunakan sama)
                // Jika ganti kas, periksa saldo utuh kas baru.
                // Jika kas tetap sama, tambahkan kembali nilai pengeluaran lama seolah-olah dibatalkan terlebih dulu
                $saldoReal = $this->kasModel->hitungSaldo($idKasBaru);
                if ($transaksi['jenis'] === 'pengeluaran' && (string)$transaksi['id_kas'] === (string)$idKasBaru) {
                    $saldoReal += (float)$transaksi['jumlah'];
                } else if ($transaksi['jenis'] === 'pemasukan' && (string)$transaksi['id_kas'] === (string)$idKasBaru) {
                    $saldoReal -= (float)$transaksi['jumlah'];
                }

                if ($jumlahTrx > $saldoReal) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Transaksi gagal diupdate. Total Saldo kas (Rp ' . number_format($saldoReal, 0, ',', '.') . ') tidak mencukupi untuk jumlah pengeluaran ini.');
                }
            }
        }
        // -------------------------------------------------------

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
        // Jika operator, paksa gunakan id entitas sendiri (keamanan: cegah manipulasi POST)
        if ($operatorEntitasId) {
            $entitasId = $operatorEntitasId;
        }

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
        $config = $this->getEntitasConfig($entitasType);
        $operatorEntitasId = $this->getOperatorEntitasId($config);

        $transaksi = $this->transaksiModel->find($id);
        if (!$transaksi || $transaksi['entitas_type'] !== $entitasType) {
            return redirect()->back()->with('error', 'Data transaksi tidak ditemukan.');
        }

        // Keamanan: cegah operator hapus transaksi entitas lain
        if ($operatorEntitasId && (int)$transaksi['entitas_id'] !== $operatorEntitasId) {
            return redirect()->to('admin/keuangan/transaksi/'.$entitasType)->with('error', 'Akses ditolak.');
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
        $operatorEntitasId = $this->getOperatorEntitasId($config);

        $kasList = $operatorEntitasId
            ? $this->kasModel->where('entitas_id', $operatorEntitasId)->where('entitas_type', $entitasType)->findAll()
            : $this->kasModel->getByEntitasType($entitasType);

        foreach ($kasList as &$k) {
            $k['saldo_berjalan'] = $this->kasModel->hitungSaldo($k['id']);
        }
        
        $entitasList = [];
        $pkRow = '';
        if ($entitasType === 'masjid_mushola') {
            $entitasList = $operatorEntitasId
                ? $this->masjidModel->where('id_masjid_mushola', $operatorEntitasId)->findAll()
                : $this->masjidModel->findAll();
            $pkRow = 'id_masjid_mushola';
        } elseif ($entitasType === 'majelis_taklim') {
            $modelMt = new \App\Models\MajelisTaklimModel();
            $entitasList = $operatorEntitasId
                ? $modelMt->where('id_majelis_taklim', $operatorEntitasId)->findAll()
                : $modelMt->findAll();
            $pkRow = 'id_majelis_taklim';
        }

        $data = [
            'title'             => 'Kelola Kas - ' . $config['nama_label'],
            'entitasType'       => $entitasType,
            'entitasConfig'     => $config,
            'kasList'           => $kasList,
            'entitasList'       => $entitasList,
            'pkRow'             => $pkRow,
            'operatorEntitasId' => $operatorEntitasId,
            'validation'        => \Config\Services::validation(),
        ];

        return view('backend/keuangan/kas/index', $data);
    }

    /**
     * Simpan kas baru
     */
    public function storeKas(string $entitasType)
    {
        $config = $this->getEntitasConfig($entitasType);

        $rules = [
            'nama_kas'   => 'required|min_length[3]|max_length[150]',
            'saldo_awal' => 'permit_empty|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Jika operator, paksa gunakan id entitas sendiri (keamanan)
        $operatorEntitasId = $this->getOperatorEntitasId($config);
        $entitasIdKas = $operatorEntitasId ?? ($this->request->getPost('entitas_id') ?: null);

        $this->kasModel->save([
            'entitas_type' => $entitasType,
            'entitas_id'   => $entitasIdKas,
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
        $config = $this->getEntitasConfig($entitasType);
        $operatorEntitasId = $this->getOperatorEntitasId($config);

        $kas = $this->kasModel->find($id);
        if (!$kas || $kas['entitas_type'] !== $entitasType) {
            return redirect()->back()->with('error', 'Rekening kas tidak ditemukan.');
        }

        // Keamanan: cegah operator edit kas entitas lain
        if ($operatorEntitasId && (int)$kas['entitas_id'] !== $operatorEntitasId) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $rules = [
            'nama_kas'   => 'required|min_length[3]|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kasModel->update($id, [
            'nama_kas'   => $this->request->getPost('nama_kas'),
            'entitas_id' => $this->request->getPost('entitas_id') ?: null,
            'keterangan' => $this->request->getPost('keterangan') ?: null,
        ]);

        return redirect()->to('/admin/keuangan/kas/' . $entitasType)
                         ->with('success', 'Data kas berhasil diperbarui.');
    }

    /**
     * Hapus kas (jika tidak ada transaksi terkait)
     */
    public function deleteKas(string $entitasType, int $id)
    {
        $config = $this->getEntitasConfig($entitasType);
        $operatorEntitasId = $this->getOperatorEntitasId($config);

        $kas = $this->kasModel->find($id);
        if (!$kas || $kas['entitas_type'] !== $entitasType) {
            return redirect()->back()->with('error', 'Rekening kas tidak ditemukan.');
        }

        // Keamanan: cegah operator hapus kas entitas lain
        if ($operatorEntitasId && (int)$kas['entitas_id'] !== $operatorEntitasId) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        // Cek apakah kas sudah memiliki transaksi
        $jumlahTransaksi = $this->transaksiModel->where('id_kas', $id)->countAllResults();
        if ($jumlahTransaksi > 0) {
            return redirect()->back()->with('error', 'Kas tidak bisa dihapus karena memiliki ' . $jumlahTransaksi . ' riwayat transaksi.');
        }

        $this->kasModel->delete($id);

        return redirect()->to('/admin/keuangan/kas/' . $entitasType)
                         ->with('success', 'Rekening kas berhasil dihapus.');
    }
}
