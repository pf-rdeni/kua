<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\KeuanganIuranSettingModel;
use App\Models\KeuanganIuranAnggotaModel;
use App\Models\PersonilModel;
use App\Models\EntitasTypeModel;

/**
 * KeuanganIuranController
 * Mengelola setting iuran per entitas dan pencatatan pembayaran iuran anggota.
 * Iuran dapat bersifat: harian, mingguan, bulanan, tahunan, sekali.
 */
class KeuanganIuranController extends BaseController
{
    protected $iuranSettingModel;
    protected $iuranAnggotaModel;
    protected $personilModel;
    protected $entitasTypeModel;

    public function __construct()
    {
        $this->iuranSettingModel = new KeuanganIuranSettingModel();
        $this->iuranAnggotaModel = new KeuanganIuranAnggotaModel();
        $this->personilModel     = new PersonilModel();
        $this->entitasTypeModel  = new EntitasTypeModel();
    }

    /**
     * Resolve config entitas atau 404, sekaligus cek otorisasi
     */
    private function getEntitasConfig(string $entitasType): array
    {
        $config = $this->entitasTypeModel->getByKode($entitasType);
        if (!$config) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Tipe entitas '{$entitasType}' tidak ditemukan.");
        }

        $allowedGroups = ['SuperAdmin', 'Admin'];
        if (!empty($config['operator_group'])) {
            $allowedGroups[] = $config['operator_group'];
        }
        if (!function_exists('in_groups')) {
            helper('auth');
        }
        if (!\in_groups($allowedGroups)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Akses Ditolak untuk data iuran " . $config['nama_label']);
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
     * Halaman daftar setting iuran per entitas.
     * Menampilkan semua jenis iuran dan link ke laporan anggota masing-masing.
     */
    public function setting(string $entitasType)
    {
        $config    = $this->getEntitasConfig($entitasType);
        $entitasId = $this->getOperatorEntitasId($config);
        $iuranList = $this->iuranSettingModel->getAllByEntitasType($entitasType, $entitasId);

        // Hitung jumlah anggota aktif untuk entitas ini
        $builder = $this->personilModel->where('entitas_type', $entitasType)
                                       ->where('status_aktif', 1);
        if ($entitasId !== null) {
            // Tabel Personil saat ini hanya memiliki foreign key id_masjid_mushola
            if ($entitasType === 'masjid_mushola') {
                $builder->where('id_masjid_mushola', $entitasId);
            }
            // TODO: Jika tabel personil ditambahkan kolom id_majelis_taklim, tambahkan else if di sini
        }
        $jumlahAnggota = $builder->countAllResults();

        $data = [
            'title'         => 'Setting Iuran - ' . $config['nama_label'],
            'entitasType'   => $entitasType,
            'entitasConfig' => $config,
            'iuranList'     => $iuranList,
            'jumlahAnggota' => $jumlahAnggota,
            'validation'    => \Config\Services::validation(),
        ];

        return view('backend/keuangan/iuran/setting', $data);
    }

    /**
     * Simpan setting iuran baru
     */
    public function storeSetting(string $entitasType)
    {
        $config = $this->getEntitasConfig($entitasType);
        $entitasId = $this->getOperatorEntitasId($config);

        $rules = [
            'nama_iuran'     => 'required|min_length[3]|max_length[150]',
            'periode'        => 'required|in_list[harian,mingguan,bulanan,tahunan,sekali]',
            'nominal'        => 'required|numeric|greater_than_equal_to[0]',
            'tanggal_mulai'  => 'required|valid_date[Y-m-d]',
            'tanggal_selesai'=> 'permit_empty|valid_date[Y-m-d]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->iuranSettingModel->save([
            'entitas_type'    => $entitasType,
            'entitas_id'      => $entitasId ?? ($this->request->getPost('entitas_id') ?: null),
            'nama_iuran'      => $this->request->getPost('nama_iuran'),
            'periode'         => $this->request->getPost('periode'),
            'nominal'         => $this->request->getPost('nominal'),
            'tanggal_mulai'   => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: null,
            'keterangan'      => $this->request->getPost('keterangan') ?: null,
            'is_active'       => $this->request->getPost('is_active') ?? 1,
            'created_by'      => $this->getCurrentUserId(),
        ]);

        return redirect()->to('/admin/keuangan/iuran/' . $entitasType)
                         ->with('success', 'Setting iuran "' . $this->request->getPost('nama_iuran') . '" berhasil disimpan.');
    }

    /**
     * Update setting iuran
     */
    public function updateSetting(string $entitasType, int $id)
    {
        $config  = $this->getEntitasConfig($entitasType);
        $entitasId = $this->getOperatorEntitasId($config);
        $setting = $this->iuranSettingModel->find($id);

        if (!$setting || $setting['entitas_type'] !== $entitasType) {
            return redirect()->back()->with('error', 'Setting iuran tidak ditemukan.');
        }

        if ($entitasId !== null && $setting['entitas_id'] !== null && (int)$setting['entitas_id'] !== $entitasId) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $rules = [
            'nama_iuran'     => 'required|min_length[3]|max_length[150]',
            'periode'        => 'required|in_list[harian,mingguan,bulanan,tahunan,sekali]',
            'nominal'        => 'required|numeric|greater_than_equal_to[0]',
            'tanggal_mulai'  => 'required|valid_date[Y-m-d]',
            'tanggal_selesai'=> 'permit_empty|valid_date[Y-m-d]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->iuranSettingModel->update($id, [
            'nama_iuran'      => $this->request->getPost('nama_iuran'),
            'periode'         => $this->request->getPost('periode'),
            'nominal'         => $this->request->getPost('nominal'),
            'tanggal_mulai'   => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: null,
            'keterangan'      => $this->request->getPost('keterangan') ?: null,
            'is_active'       => $this->request->getPost('is_active') ?? 1,
        ]);

        return redirect()->to('/admin/keuangan/iuran/' . $entitasType)
                         ->with('success', 'Setting iuran berhasil diperbarui.');
    }

    /**
     * Hapus setting iuran (hanya jika belum ada data bayar)
     */
    public function deleteSetting(string $entitasType, int $id)
    {
        $config  = $this->getEntitasConfig($entitasType);
        $entitasId = $this->getOperatorEntitasId($config);
        $setting = $this->iuranSettingModel->find($id);

        if (!$setting || $setting['entitas_type'] !== $entitasType) {
            return redirect()->back()->with('error', 'Setting iuran tidak ditemukan.');
        }

        if ($entitasId !== null && $setting['entitas_id'] !== null && (int)$setting['entitas_id'] !== $entitasId) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        // Cek apakah sudah ada data pembayaran
        $jumlahBayar = $this->iuranAnggotaModel->where('id_iuran_setting', $id)->countAllResults();
        if ($jumlahBayar > 0) {
            return redirect()->back()->with('error', 'Setting iuran tidak bisa dihapus karena sudah ada ' . $jumlahBayar . ' data pembayaran. Nonaktifkan saja.');
        }

        $this->iuranSettingModel->delete($id);

        return redirect()->to('/admin/keuangan/iuran/' . $entitasType)
                         ->with('success', 'Setting iuran berhasil dihapus.');
    }

    /**
     * Laporan iuran anggota — siapa sudah/belum bayar per periode
     */
    public function anggota(string $entitasType, int $idSetting)
    {
        $config  = $this->getEntitasConfig($entitasType);
        $entitasId = $this->getOperatorEntitasId($config);
        $setting = $this->iuranSettingModel->find($idSetting);

        if (!$setting || $setting['entitas_type'] !== $entitasType) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Setting iuran tidak ditemukan.');
        }

        if ($entitasId !== null && $setting['entitas_id'] !== null && (int)$setting['entitas_id'] !== $entitasId) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Akses ditolak.');
        }

        // Generate semua periode berdasarkan setting
        $semuaPeriode = $this->iuranSettingModel->generatePeriode($setting);

        // Ambil semua periode yang di-filter (default: 6 periode terakhir)
        $filterPeriodeData = $this->request->getGet('periode');
        $filterPeriodeArray = [];
        if (is_array($filterPeriodeData)) {
            $filterPeriodeArray = $filterPeriodeData;
        } elseif (!empty($filterPeriodeData)) {
            // Handle if periode passed as comma separated string
            $filterPeriodeArray = explode(',', $filterPeriodeData);
        }

        if (!empty($filterPeriodeArray)) {
            // Sort ascending (karena list dari db turun)
            sort($filterPeriodeArray);
            $periodeAktif = $filterPeriodeArray;
        } else {
            // Tampilkan 6 periode terakhir secara default
            $periodeAktif = array_slice(array_reverse($semuaPeriode), 0, 6);
            $periodeAktif = array_reverse($periodeAktif);
        }
        
        // Simpan string comma separated untuk dibundle di form view
        $filterPeriode = implode(',', $filterPeriodeArray);

        // Ambil semua personil aktif untuk entitas ini
        $builder = $this->personilModel
            ->where('entitas_type', $entitasType)
            ->where('status_aktif', 1);
        if ($entitasId !== null) {
            // Tabel Personil saat ini hanya memiliki foreign key id_masjid_mushola
            if ($entitasType === 'masjid_mushola') {
                $builder->where('id_masjid_mushola', $entitasId);
            }
            // TODO: Jika tabel personil ditambahkan kolom id_majelis_taklim, tambahkan else if di sini
        }
        $personilList = $builder->orderBy('nama_lengkap', 'ASC')->findAll();

        // Ambil semua data bayar untuk setting ini
        $bayaranRows = $this->iuranAnggotaModel->where('id_iuran_setting', $idSetting)->findAll();

        // Buat map: [id_personil][periode_bayar] => data bayar
        $bayaranMap = [];
        foreach ($bayaranRows as $bayar) {
            $bayaranMap[$bayar['id_personil']][$bayar['periode_bayar']] = $bayar;
        }

        // Hitung rekap per periode yang ditampilkan
        $rekapPeriode = [];
        foreach ($periodeAktif as $periode) {
            $lunas = $sebagian = 0;
            foreach ($personilList as $p) {
                $status = $bayaranMap[$p['id']][$periode]['status'] ?? 'belum';
                if ($status === 'lunas') $lunas++;
                elseif ($status === 'sebagian') $sebagian++;
            }
            $rekapPeriode[$periode] = [
                'lunas'    => $lunas,
                'sebagian' => $sebagian,
                'belum'    => count($personilList) - $lunas - $sebagian,
            ];
        }

        $data = [
            'title'         => 'Iuran ' . $setting['nama_iuran'] . ' - ' . $config['nama_label'],
            'entitasType'   => $entitasType,
            'entitasConfig' => $config,
            'setting'       => $setting,
            'personilList'  => $personilList,
            'periodeAktif'  => $periodeAktif,
            'semuaPeriode'  => $semuaPeriode,
            'bayaranMap'    => $bayaranMap,
            'rekapPeriode'  => $rekapPeriode,
            'filterPeriode' => $filterPeriode,
        ];

        return view('backend/keuangan/iuran/anggota', $data);
    }

    /**
     * Catat pembayaran iuran anggota (via modal AJAX-style form)
     */
    public function bayar(string $entitasType)
    {
        $config = $this->getEntitasConfig($entitasType);
        $entitasId = $this->getOperatorEntitasId($config);

        $rules = [
            'id_iuran_setting' => 'required|numeric',
            'id_personil'      => 'required|numeric',
            'periode_bayar'    => 'required|max_length[20]',
            'jumlah_bayar'     => 'required|numeric|greater_than[0]',
            'status'           => 'required|in_list[lunas,sebagian,belum]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idSetting  = (int)$this->request->getPost('id_iuran_setting');
        $idPersonil = (int)$this->request->getPost('id_personil');
        $periode    = $this->request->getPost('periode_bayar');

        // Cek apakah sudah ada data bayar sebelumnya (update jika ada)
        $existing = $this->iuranAnggotaModel->sudahBayar($idPersonil, $idSetting, $periode);

        $saveData = [
            'id_iuran_setting' => $idSetting,
            'id_personil'      => $idPersonil,
            'periode_bayar'    => $periode,
            'tanggal_bayar'    => $this->request->getPost('tanggal_bayar') ?: date('Y-m-d'),
            'jumlah_bayar'     => $this->request->getPost('jumlah_bayar'),
            'status'           => $this->request->getPost('status'),
            'keterangan'       => $this->request->getPost('keterangan') ?: null,
            'created_by'       => $this->getCurrentUserId(),
        ];

        if ($existing) {
            // Update data bayar yang sudah ada
            $this->iuranAnggotaModel->update($existing['id'], $saveData);
            $msg = 'Data pembayaran iuran berhasil diperbarui.';
        } else {
            // Simpan data bayar baru
            $this->iuranAnggotaModel->save($saveData);
            $msg = 'Pembayaran iuran berhasil dicatat.';
        }

        $redirectUrl = '/admin/keuangan/iuran/' . $entitasType . '/anggota/' . $idSetting;
        $periodeFilterPost = $this->request->getPost('periode_filter');
        if (!empty($periodeFilterPost)) {
            // periode_filter is comma separated
            $arrPeriodeUrl = explode(',', $periodeFilterPost);
            $qStringArray = [];
            foreach ($arrPeriodeUrl as $idx => $p) {
                $qStringArray[] = 'periode[' . $idx . ']=' . urlencode($p);
            }
            $redirectUrl .= '?' . implode('&', $qStringArray);
        }

        return redirect()->to($redirectUrl)->with('success', $msg);
    }

    /**
     * Hapus catatan pembayaran iuran
     */
    public function deleteBayar(string $entitasType, int $id)
    {
        $config = $this->getEntitasConfig($entitasType);
        $entitasId = $this->getOperatorEntitasId($config);
        $bayar = $this->iuranAnggotaModel->find($id);

        if (!$bayar) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $idSetting = $bayar['id_iuran_setting'];
        $this->iuranAnggotaModel->delete($id);

        return redirect()->to('/admin/keuangan/iuran/' . $entitasType . '/anggota/' . $idSetting)
                         ->with('success', 'Catatan pembayaran berhasil dihapus.');
    }
}
