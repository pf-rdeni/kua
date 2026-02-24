<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\PersonilModel;
use App\Models\EntitasTypeModel;
use App\Models\MasjidMusholaModel;
use App\Models\BerkasModel;
use App\Models\SettingBerkasModel;

/**
 * PersonilController — Unified controller untuk semua entitas tipe orang.
 * Menggantikan MubalighController, ImamMasjidController, FarduKifayahController, PenggaliKuburController.
 */
class PersonilController extends BaseController
{
    protected $personilModel;
    protected $entitasTypeModel;
    protected $masjidModel;

    public function __construct()
    {
        $this->personilModel   = new PersonilModel();
        $this->entitasTypeModel = new EntitasTypeModel();
        $this->masjidModel     = new MasjidMusholaModel();
    }

    /**
     * Resolve entitas config atau 404
     */
    private function getEntitasConfig(string $entitasType): array
    {
        $config = $this->entitasTypeModel->getByKode($entitasType);
        if (!$config) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Tipe entitas '{$entitasType}' tidak ditemukan.");
        }

        // --- DYNAMIC ROUTE AUTHORIZATION ---
        $allowedGroups = ['SuperAdmin', 'Admin'];
        if (!empty($config['operator_group'])) {
            $allowedGroups[] = $config['operator_group'];
        }

        if (! function_exists('in_groups')) {
            helper('auth');
        }

        if (! \in_groups($allowedGroups)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Akses Ditolak. Anda tidak memiliki izin operasi untuk data " . $config['nama_label']);
        }

        return $config;
    }

    /**
     * Tampilkan daftar personil berdasarkan entitas type
     */
    public function index(string $entitasType)
    {
        $config  = $this->getEntitasConfig($entitasType);

        if ($config['has_masjid_link']) {
            $query = $this->personilModel->getWithMasjid($entitasType);
        } else {
            $query = $this->personilModel->ofType($entitasType);
        }

        $query->orderBy('nama_lengkap', 'ASC');
        $list = $query->findAll();

        $data = [
            'title'        => 'Data ' . $config['nama_label'],
            'entitasType'  => $entitasType,
            'entitasConfig' => $config,
            'personilList' => $list,
        ];

        return view('backend/personil/index', $data);
    }

    /**
     * Tampilkan form tambah
     */
    public function create(string $entitasType)
    {
        $config = $this->getEntitasConfig($entitasType);

        $data = [
            'title'         => 'Tambah ' . $config['nama_label'],
            'entitasType'   => $entitasType,
            'entitasConfig' => $config,
            'validation'    => \Config\Services::validation(),
        ];

        if ($config['has_masjid_link']) {
            $data['masjidList'] = $this->masjidModel->findAll();
        }

        return view('backend/personil/form', $data);
    }

    /**
     * Simpan data baru
     */
    public function store(string $entitasType)
    {
        $config = $this->getEntitasConfig($entitasType);

        $rules = [
            'nama_lengkap'  => 'required|min_length[3]|max_length[255]',
            'nik'           => 'permit_empty|exact_length[16]|numeric',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'no_hp'         => 'permit_empty|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nikBaru = $this->request->getPost('nik');

        // [New Validation] Cek Duplikat NIK di Entitas yang Sama (Backend Protection)
        if (!empty($nikBaru)) {
            $duplicate = $this->personilModel->where('nik', $nikBaru)
                                             ->where('entitas_type', $entitasType)
                                             ->first();
            if ($duplicate) {
                return redirect()->back()->withInput()->with('error', 'NIK tersebut sudah didaftarkan pada entitas ini!');
            }
        }

        // Handle SK
        $skName = null;
        if ($config['has_sk']) {
            $sk = $this->request->getFile('sk_pengangkatan');
            if ($sk && $sk->isValid() && !$sk->hasMoved()) {
                $skName = $sk->getRandomName();
                $uploadDir = FCPATH . 'uploads/personil';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $sk->move($uploadDir, $skName);
            }
        }

        $saveData = [
            'entitas_type'        => $entitasType,
            'nama_lengkap'        => $this->request->getPost('nama_lengkap'),
            'nik'                 => $nikBaru,
            'tempat_lahir'        => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir'       => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin'       => $this->request->getPost('jenis_kelamin'),
            'alamat'              => $this->request->getPost('alamat'),
            'provinsi'            => $this->request->getPost('provinsi'),
            'kabupaten_kota'      => $this->request->getPost('kabupaten_kota'),
            'kecamatan'           => $this->request->getPost('kecamatan'),
            'kelurahan_desa'      => $this->request->getPost('kelurahan_desa'),
            'rt'                  => $this->request->getPost('rt'),
            'rw'                  => $this->request->getPost('rw'),
            'no_hp'               => $this->request->getPost('no_hp'),
            'pendidikan_terakhir' => $this->request->getPost('pendidikan_terakhir'),
            'pekerjaan'           => $this->request->getPost('pekerjaan'),
            'status_aktif'        => $this->request->getPost('status_aktif') ?? 1,
            'latitude'            => $this->request->getPost('latitude'),
            'longitude'           => $this->request->getPost('longitude'),
        ];

        if ($config['has_masjid_link']) {
            $saveData['id_masjid_mushola'] = $this->request->getPost('id_masjid_mushola');
        }
        if ($config['has_sk']) {
            $saveData['sk_pengangkatan'] = $skName;
            $saveData['status'] = $this->request->getPost('status');
        }

        $this->personilModel->save($saveData);

        return redirect()->to('/admin/personil/' . $entitasType)->with('success', 'Data ' . $config['nama_label'] . ' berhasil ditambahkan.');
    }

    /**
     * Detail
     */
    public function show(string $entitasType, $id)
    {
        $config = $this->getEntitasConfig($entitasType);

        if ($config['has_masjid_link']) {
            $personil = $this->personilModel->getWithMasjid($entitasType, $id);
        } else {
            $personil = $this->personilModel->ofType($entitasType)->find($id);
        }

        if (!$personil) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data tidak ditemukan.');
        }

        $data = [
            'title'         => 'Detail ' . $config['nama_label'],
            'entitasType'   => $entitasType,
            'entitasConfig' => $config,
            'personil'      => $personil,
        ];

        return view('backend/personil/show', $data);
    }

    /**
     * Form edit
     */
    public function edit(string $entitasType, $id)
    {
        $config   = $this->getEntitasConfig($entitasType);
        $personil = $this->personilModel->find($id);

        if (!$personil || $personil['entitas_type'] !== $entitasType) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data tidak ditemukan.');
        }

        $data = [
            'title'         => 'Edit ' . $config['nama_label'],
            'entitasType'   => $entitasType,
            'entitasConfig' => $config,
            'personil'      => $personil,
            'validation'    => \Config\Services::validation(),
        ];

        if ($config['has_masjid_link']) {
            $data['masjidList'] = $this->masjidModel->findAll();
        }

        return view('backend/personil/form', $data);
    }

    /**
     * Update
     */
    public function update(string $entitasType, $id)
    {
        $config   = $this->getEntitasConfig($entitasType);
        $personil = $this->personilModel->find($id);

        if (!$personil || $personil['entitas_type'] !== $entitasType) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data tidak ditemukan.');
        }

        $rules = [
            'nama_lengkap'  => 'required|min_length[3]|max_length[255]',
            'nik'           => 'permit_empty|exact_length[16]|numeric',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'no_hp'         => 'permit_empty|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nikUpdate = $this->request->getPost('nik');

        // [New Validation] Cek Duplikat NIK untuk Edit di entitas sendiri
        if (!empty($nikUpdate)) {
            $duplicate = $this->personilModel->where('nik', $nikUpdate)
                                             ->where('entitas_type', $entitasType)
                                             ->where('id !=', $id)
                                             ->first();
            if ($duplicate) {
                return redirect()->back()->withInput()->with('error', 'NIK tidak dapat diubah karena telah terpakai oleh ID lain di entitas ini!');
            }
        }

        // Handle SK
        $skName = $personil['sk_pengangkatan'] ?? null;
        if ($config['has_sk']) {
            $sk = $this->request->getFile('sk_pengangkatan');
            if ($sk && $sk->isValid() && !$sk->hasMoved()) {
                if ($skName && file_exists(FCPATH . 'uploads/personil/' . $skName)) {
                    @unlink(FCPATH . 'uploads/personil/' . $skName);
                }
                $skName = $sk->getRandomName();
                $uploadDir = FCPATH . 'uploads/personil';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $sk->move($uploadDir, $skName);
            }
        }

        $updateData = [
            'nama_lengkap'        => $this->request->getPost('nama_lengkap'),
            'nik'                 => $nikUpdate,
            'tempat_lahir'        => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir'       => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin'       => $this->request->getPost('jenis_kelamin'),
            'alamat'              => $this->request->getPost('alamat'),
            'provinsi'            => $this->request->getPost('provinsi'),
            'kabupaten_kota'      => $this->request->getPost('kabupaten_kota'),
            'kecamatan'           => $this->request->getPost('kecamatan'),
            'kelurahan_desa'      => $this->request->getPost('kelurahan_desa'),
            'rt'                  => $this->request->getPost('rt'),
            'rw'                  => $this->request->getPost('rw'),
            'no_hp'               => $this->request->getPost('no_hp'),
            'pendidikan_terakhir' => $this->request->getPost('pendidikan_terakhir'),
            'pekerjaan'           => $this->request->getPost('pekerjaan'),
            'status_aktif'        => $this->request->getPost('status_aktif') ?? 1,
            'latitude'            => $this->request->getPost('latitude'),
            'longitude'           => $this->request->getPost('longitude'),
        ];

        if ($config['has_masjid_link']) {
            $updateData['id_masjid_mushola'] = $this->request->getPost('id_masjid_mushola');
        }
        if ($config['has_sk']) {
            $updateData['sk_pengangkatan'] = $skName;
            $updateData['status'] = $this->request->getPost('status');
        }

        // Process Update Data untuk baris yang bersangkutan
        $this->personilModel->update($id, $updateData);

        // [Global Auto-Sync Flag Check]
        // Jika sync_all dikirimkan via POST dan NIK tidak kosong
        if ($this->request->getPost('sync_all') === '1' && !empty($nikUpdate)) {
            // Ambil semua ID lain yang memiliki NIK sama
            $otherMates = $this->personilModel->where('nik', $nikUpdate)->where('id !=', $id)->find();
            if(!empty($otherMates)){
                // Data dasar yang mutlak sama harus di-sync (tanpa ID, entitas_type, entitas khusus Masjid, form unik dll)
                $syncData = [
                    'nama_lengkap' => $updateData['nama_lengkap'],
                    'tempat_lahir' => $updateData['tempat_lahir'],
                    'tanggal_lahir' => $updateData['tanggal_lahir'],
                    'jenis_kelamin' => $updateData['jenis_kelamin'],
                    'no_hp' => $updateData['no_hp'],
                    'alamat' => $updateData['alamat'],
                    'provinsi' => $updateData['provinsi'],
                    'kabupaten_kota' => $updateData['kabupaten_kota'],
                    'kecamatan' => $updateData['kecamatan'],
                    'kelurahan_desa' => $updateData['kelurahan_desa'],
                    'rt' => $updateData['rt'],
                    'rw' => $updateData['rw'],
                    'pendidikan_terakhir' => $updateData['pendidikan_terakhir'],
                    'pekerjaan' => $updateData['pekerjaan'],
                    'latitude' => $updateData['latitude'],
                    'longitude' => $updateData['longitude']
                ];
                
                // Lakukan Update Berantai Serentak
                $this->personilModel->where('nik', $nikUpdate)->where('id !=', $id)->set($syncData)->update();
            }
        }

        return redirect()->to('/admin/personil/' . $entitasType)->with('success', 'Data ' . $config['nama_label'] . ' berhasil diperbarui.');
    }

    /**
     * Hapus
     */
    public function delete(string $entitasType, $id)
    {
        $config   = $this->getEntitasConfig($entitasType);
        $personil = $this->personilModel->find($id);

        if (!$personil || $personil['entitas_type'] !== $entitasType) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data tidak ditemukan.');
        }

        // Hapus foto
        if ($personil['foto'] && file_exists(FCPATH . 'uploads/personil/' . $personil['foto'])) {
            @unlink(FCPATH . 'uploads/personil/' . $personil['foto']);
        }
        // Hapus SK
        if (!empty($personil['sk_pengangkatan']) && file_exists(FCPATH . 'uploads/personil/' . $personil['sk_pengangkatan'])) {
            @unlink(FCPATH . 'uploads/personil/' . $personil['sk_pengangkatan']);
        }

        $this->personilModel->delete($id);

        return redirect()->to('/admin/personil/' . $entitasType)->with('success', 'Data ' . $config['nama_label'] . ' berhasil dihapus.');
    }

    /**
     * Tampilkan halaman berkas lampiran (per entitas type)
     */
    public function showBerkasLampiran(string $entitasType)
    {
        $config = $this->getEntitasConfig($entitasType);
        $berkasModel        = new BerkasModel();
        $settingBerkasModel = new SettingBerkasModel();

        // Get dynamic berkas settings for this entitas type
        $settingBerkas = $settingBerkasModel->getSettingByEntitas($entitasType);

        // Query semua personil untuk entitas ini
        $personilList = $this->personilModel->getByEntitas($entitasType);

        // Ambil data berkas untuk setiap personil
        $personilWithBerkas = [];
        foreach ($personilList as $p) {
            $berkasAktif = $berkasModel->getBerkasAktif($entitasType, $p['id']);

            // Organize berkas by type
            $berkasByType = [];
            foreach ($berkasAktif as $berkas) {
                $berkasByType[$berkas['nama_berkas']] = $berkas;
            }

            $personilWithBerkas[] = [
                'personil' => $p,
                'berkas'   => $berkasByType,
            ];
        }

        $data = [
            'title'              => 'Berkas Lampiran ' . $config['nama_label'],
            'breadcrumb'         => [
                ['title' => 'Home', 'url' => 'admin/dashboard'],
                ['title' => $config['nama_label'], 'url' => 'admin/personil/' . $entitasType],
                ['title' => 'Berkas Lampiran', 'url' => ''],
            ],
            'entitasType'        => $entitasType,
            'entitasConfig'      => $config,
            'personilWithBerkas' => $personilWithBerkas,
            'settingBerkas'      => $settingBerkas,
        ];

        return view('backend/personil/berkasLampiran', $data);
    }
}
