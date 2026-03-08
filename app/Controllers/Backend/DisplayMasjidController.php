<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\DisplaySettingModel;
use App\Models\DisplayKontenModel;
use App\Models\MasjidMusholaModel;

/**
 * Controller Admin untuk mengelola Display Masjid
 * Menangani CRUD pengaturan display dan konten dinamis
 */
class DisplayMasjidController extends BaseController
{
    protected $displayModel;
    protected $kontenModel;
    protected $masjidModel;

    public function __construct()
    {
        $this->displayModel = new DisplaySettingModel();
        $this->kontenModel  = new DisplayKontenModel();
        $this->masjidModel  = new MasjidMusholaModel();
    }

    /**
     * Halaman daftar semua display masjid
     */
    public function index()
    {
        $displays = [];
        if (in_groups('OperatorMasjidMushola') && !in_groups('SuperAdmin') && !in_groups('Admin')) {
            $currentUser = user();
            if ($currentUser && $currentUser->entitas_type === 'masjid_mushola' && !empty($currentUser->entitas_id)) {
                $displays = $this->displayModel->select('tbl_display_setting.*, tbl_masjid_mushola.nama as nama_masjid, tbl_masjid_mushola.latitude, tbl_masjid_mushola.longitude')
                                               ->join('tbl_masjid_mushola', 'tbl_masjid_mushola.id_masjid_mushola = tbl_display_setting.id_masjid_mushola', 'left')
                                               ->where('tbl_display_setting.id_masjid_mushola', $currentUser->entitas_id)
                                               ->orderBy('tbl_display_setting.id', 'DESC')
                                               ->findAll();
            }
        } else {
            $displays = $this->displayModel->getDisplayDenganMasjid();
        }

        $data = [
            'title'    => 'Display Masjid',
            'displays' => $displays,
        ];
        return view('backend/display_masjid/index', $data);
    }

    /**
     * Form tambah display baru
     */
    public function create()
    {
        $masjidList = [];
        $selectedMasjidId = null;

        if (in_groups('OperatorMasjidMushola') && !in_groups('SuperAdmin') && !in_groups('Admin')) {
            $currentUser = user();
            if ($currentUser && $currentUser->entitas_type === 'masjid_mushola' && !empty($currentUser->entitas_id)) {
                $masjidList = $this->masjidModel->where('id_masjid_mushola', $currentUser->entitas_id)->findAll();
                $selectedMasjidId = $currentUser->entitas_id;
            }
        } else {
            $masjidList = $this->masjidModel->orderBy('nama', 'ASC')->findAll();
        }

        $data = [
            'title'      => 'Tambah Display Masjid',
            'masjidList' => $masjidList,
            'display'    => null,
            'selectedMasjidId' => $selectedMasjidId,
        ];
        return view('backend/display_masjid/form', $data);
    }

    /**
     * Simpan display baru ke database
     */
    public function store()
    {
        // Ambil data dari form
        $dataInput = $this->_getFormData();

        // Upload logo jika ada
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $namaLogo = $logo->getRandomName();
            $logo->move(FCPATH . 'uploads/display', $namaLogo);
            $dataInput['logo'] = 'uploads/display/' . $namaLogo;
        }

        // Upload wallpaper jika ada
        $wallpaper = $this->request->getFile('wallpaper');
        if ($wallpaper && $wallpaper->isValid() && !$wallpaper->hasMoved()) {
            $namaWallpaper = $wallpaper->getRandomName();
            $wallpaper->move(FCPATH . 'uploads/display', $namaWallpaper);
            $dataInput['wallpaper'] = 'uploads/display/' . $namaWallpaper;
        }

        // Upload gambar overlay mode sholat
        $dataInput = $this->_handleModeImages($dataInput);

        // Simpan ke database
        if ($this->displayModel->save($dataInput)) {
            return redirect()->to(base_url('admin/display-masjid'))
                             ->with('success', 'Display masjid berhasil ditambahkan.');
        }

        return redirect()->back()->withInput()
                         ->with('errors', $this->displayModel->errors());
    }

    /**
     * Form edit display yang sudah ada
     */
    public function edit($id)
    {
        $display = $this->displayModel->find($id);
        if (!$display) {
            return redirect()->to(base_url('admin/display-masjid'))
                             ->with('error', 'Display tidak ditemukan.');
        }

        $masjidList = [];
        if (in_groups('OperatorMasjidMushola') && !in_groups('SuperAdmin') && !in_groups('Admin')) {
            $currentUser = user();
            if ($display['id_masjid_mushola'] != $currentUser->entitas_id) {
                return redirect()->to(base_url('admin/display-masjid'))
                                 ->with('error', 'Akses ditolak.');
            }
            $masjidList = $this->masjidModel->where('id_masjid_mushola', $currentUser->entitas_id)->findAll();
        } else {
            $masjidList = $this->masjidModel->orderBy('nama', 'ASC')->findAll();
        }

        $data = [
            'title'      => 'Edit Display Masjid',
            'masjidList' => $masjidList,
            'display'    => $display,
            'selectedMasjidId' => $display['id_masjid_mushola'],
        ];
        return view('backend/display_masjid/form', $data);
    }

    /**
     * Update data display yang sudah ada
     */
    public function update($id)
    {
        $display = $this->displayModel->find($id);
        if (!$display) {
            if ($this->_isAjaxRequest()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Display tidak ditemukan.']);
            }
            return redirect()->to(base_url('admin/display-masjid'))
                             ->with('error', 'Display tidak ditemukan.');
        }

        // Deteksi apakah request AJAX (auto-save)
        $isAjax = $this->_isAjaxRequest();

        $dataInput = $this->_getFormData($display);

        // Upload logo baru jika ada
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            if (!empty($display['logo']) && file_exists(FCPATH . $display['logo'])) {
                unlink(FCPATH . $display['logo']);
            }
            $namaLogo = $logo->getRandomName();
            $logo->move(FCPATH . 'uploads/display', $namaLogo);
            $dataInput['logo'] = 'uploads/display/' . $namaLogo;
        }

        // Upload wallpaper baru jika ada
        $wallpaper = $this->request->getFile('wallpaper');
        if ($wallpaper && $wallpaper->isValid() && !$wallpaper->hasMoved()) {
            if (!empty($display['wallpaper']) && file_exists(FCPATH . $display['wallpaper'])) {
                unlink(FCPATH . $display['wallpaper']);
            }
            $namaWallpaper = $wallpaper->getRandomName();
            $wallpaper->move(FCPATH . 'uploads/display', $namaWallpaper);
            $dataInput['wallpaper'] = 'uploads/display/' . $namaWallpaper;
        }

        // Merge existing gambar paths dari DB ke JSON sebelum upload baru
        // (mencegah gambar lama hilang saat form disave tanpa re-upload)
        $dataInput = $this->_mergeExistingImages($dataInput, $display);

        // Upload gambar overlay mode sholat (baru di-upload akan override)
        $dataInput = $this->_handleModeImages($dataInput, $display);

        // Update ke database
        if ($this->displayModel->update($id, $dataInput)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success'    => true,
                    'message'    => 'Pengaturan berhasil disimpan otomatis.',
                    'last_updated' => date('H:i:s'),
                    'csrf_token' => csrf_hash(),
                ]);
            }
            return redirect()->to(base_url('admin/display-masjid'))
                             ->with('success', 'Display masjid berhasil diperbarui.');
        }

        // Gagal update
        if ($isAjax) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Gagal menyimpan pengaturan.',
                'errors'     => $this->displayModel->errors(),
                'csrf_token' => csrf_hash(),
            ])->setStatusCode(422);
        }

        return redirect()->back()->withInput()
                         ->with('errors', $this->displayModel->errors());
    }

    /**
     * Cek apakah request adalah AJAX (dari auto-save atau header XHR)
     */
    private function _isAjaxRequest(): bool
    {
        return $this->request->isAJAX()
            || $this->request->getPost('is_ajax') == '1'
            || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * Hapus display beserta kontennya
     */
    public function delete($id)
    {
        $display = $this->displayModel->find($id);
        if (!$display) {
            return redirect()->to(base_url('admin/display-masjid'))
                             ->with('error', 'Display tidak ditemukan.');
        }

        if (in_groups('OperatorMasjidMushola') && !in_groups('SuperAdmin') && !in_groups('Admin')) {
            if ($display['id_masjid_mushola'] != user()->entitas_id) {
                return redirect()->to(base_url('admin/display-masjid'))
                                 ->with('error', 'Akses ditolak.');
            }
        }

        // Hapus file logo dan wallpaper jika ada
        if (!empty($display['logo']) && file_exists(FCPATH . $display['logo'])) {
            unlink(FCPATH . $display['logo']);
        }
        if (!empty($display['wallpaper']) && file_exists(FCPATH . $display['wallpaper'])) {
            unlink(FCPATH . $display['wallpaper']);
        }

        // Hapus semua konten terkait
        $kontens = $this->kontenModel->where('id_display_setting', $id)->findAll();
        foreach ($kontens as $k) {
            if (!empty($k['gambar']) && file_exists(FCPATH . $k['gambar'])) {
                unlink(FCPATH . $k['gambar']);
            }
        }
        $this->kontenModel->where('id_display_setting', $id)->delete();

        // Hapus display
        $this->displayModel->delete($id);

        return redirect()->to(base_url('admin/display-masjid'))
                         ->with('success', 'Display masjid berhasil dihapus.');
    }

    /**
     * Halaman kelola konten display
     */
    public function konten($id)
    {
        $display = $this->displayModel->getDisplayById($id);
        if (!$display) {
            return redirect()->to(base_url('admin/display-masjid'))
                             ->with('error', 'Display tidak ditemukan.');
        }

        if (in_groups('OperatorMasjidMushola') && !in_groups('SuperAdmin') && !in_groups('Admin')) {
            if ($display['id_masjid_mushola'] != user()->entitas_id) {
                return redirect()->to(base_url('admin/display-masjid'))
                                 ->with('error', 'Akses ditolak.');
            }
        }

        $data = [
            'title'   => 'Kelola Konten - ' . esc($display['nama_display']),
            'display' => $display,
            'kontens' => $this->kontenModel->getAllKonten($id),
            'tipeList' => [
                'info_kegiatan'     => 'Info Kegiatan',
                'gambar_slide'      => 'Gambar Slide',
                'laporan_keuangan'  => 'Laporan Keuangan',
                'jadwal_imsyakiyah' => 'Jadwal Imsyakiyah',
                'pengumuman'        => 'Pengumuman',
            ],
        ];
        return view('backend/display_masjid/konten', $data);
    }

    /**
     * Simpan konten baru
     */
    public function storeKonten($idDisplay)
    {
        $dataInput = [
            'id_display_setting' => $idDisplay,
            'tipe'               => $this->request->getPost('tipe'),
            'judul'              => $this->request->getPost('judul'),
            'konten'             => $this->request->getPost('konten'),
            'urutan'             => $this->request->getPost('urutan') ?? 0,
            'aktif'              => $this->request->getPost('aktif') ?? 1,
            'tanggal_mulai'      => $this->request->getPost('tanggal_mulai') ?: null,
            'tanggal_selesai'    => $this->request->getPost('tanggal_selesai') ?: null,
        ];

        // Upload gambar jika ada
        $gambar = $this->request->getFile('gambar');
        if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
            $namaGambar = $gambar->getRandomName();
            $gambar->move(FCPATH . 'uploads/display/konten', $namaGambar);
            $dataInput['gambar'] = 'uploads/display/konten/' . $namaGambar;
        }

        try {
            if ($this->kontenModel->save($dataInput)) {
                // Trigger update_at pada setting display
                $this->displayModel->update($idDisplay, ['updated_at' => date('Y-m-d H:i:s')]);
                
                return redirect()->to(base_url('admin/display-masjid/konten/' . $idDisplay))
                                 ->with('success', 'Konten berhasil ditambahkan.');
            }
        } catch (\CodeIgniter\Database\Exceptions\DataException $e) {
            // Menangkap error jika form disubmit tanpa data yang valid
            return redirect()->back()->withInput()
                             ->with('error', 'Gagal menambahkan konten. Pastikan data terisi dengan benar.');
        }

        return redirect()->back()->withInput()
                         ->with('errors', $this->kontenModel->errors());
    }

    /**
     * Update konten yang sudah ada
     */
    public function updateKonten($idDisplay, $idKonten)
    {
        $konten = $this->kontenModel->find($idKonten);
        if (!$konten) {
            return redirect()->to(base_url('admin/display-masjid/konten/' . $idDisplay))
                             ->with('error', 'Konten tidak ditemukan.');
        }

        $dataInput = [
            'tipe'            => $this->request->getPost('tipe'),
            'judul'           => $this->request->getPost('judul'),
            'konten'          => $this->request->getPost('konten'),
            'urutan'          => $this->request->getPost('urutan') ?? 0,
            'aktif'           => $this->request->getPost('aktif') ?? 1,
            'tanggal_mulai'   => $this->request->getPost('tanggal_mulai') ?: null,
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: null,
        ];

        $isImageUpdated = false;

        // Upload gambar baru jika ada
        $gambar = $this->request->getFile('gambar');
        if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
            // Hapus gambar lama
            if (!empty($konten['gambar']) && file_exists(FCPATH . $konten['gambar'])) {
                unlink(FCPATH . $konten['gambar']);
            }
            $namaGambar = $gambar->getRandomName();
            $gambar->move(FCPATH . 'uploads/display/konten', $namaGambar);
            $dataInput['gambar'] = 'uploads/display/konten/' . $namaGambar;
            $isImageUpdated = true;
        }

        try {
            // Check if there are actual data changes or if image was updated
            $hasDataChanges = false;
            foreach ($dataInput as $key => $value) {
                if (array_key_exists($key, $konten) && $konten[$key] != $value) {
                    $hasDataChanges = true;
                    break;
                }
            }
            
            if ($hasDataChanges || $isImageUpdated) {
                if ($this->kontenModel->update($idKonten, $dataInput)) {
                    // Trigger update_at pada setting display
                    $this->displayModel->update($idDisplay, ['updated_at' => date('Y-m-d H:i:s')]);
    
                    return redirect()->to(base_url('admin/display-masjid/konten/' . $idDisplay))
                                     ->with('success', 'Konten berhasil diperbarui.');
                }
            } else {
                return redirect()->to(base_url('admin/display-masjid/konten/' . $idDisplay))
                                 ->with('success', 'Konten berhasil diperbarui.');
            }
        } catch (\CodeIgniter\Database\Exceptions\DataException $e) {
            // Menangkap error jika tidak ada data yang berubah (data sama persis dengan DB)
            return redirect()->to(base_url('admin/display-masjid/konten/' . $idDisplay))
                             ->with('success', 'Konten berhasil diperbarui.');
        }

        return redirect()->back()->withInput()
                         ->with('errors', $this->kontenModel->errors());
    }

    /**
     * Hapus konten
     */
    public function deleteKonten($idDisplay, $idKonten)
    {
        $konten = $this->kontenModel->find($idKonten);
        if (!$konten) {
            return redirect()->to(base_url('admin/display-masjid/konten/' . $idDisplay))
                             ->with('error', 'Konten tidak ditemukan.');
        }

        // Hapus file gambar jika ada
        if (!empty($konten['gambar']) && file_exists(FCPATH . $konten['gambar'])) {
            unlink(FCPATH . $konten['gambar']);
        }

        try {
            $this->kontenModel->delete($idKonten);

            // Trigger update_at pada setting display
            $this->displayModel->update($idDisplay, ['updated_at' => date('Y-m-d H:i:s')]);

            return redirect()->to(base_url('admin/display-masjid/konten/' . $idDisplay))
                             ->with('success', 'Konten berhasil dihapus.');
        } catch (\CodeIgniter\Database\Exceptions\DataException $e) {
            return redirect()->to(base_url('admin/display-masjid/konten/' . $idDisplay))
                             ->with('error', 'Gagal menghapus konten. Terjadi kesalahan pada database.');
        }
    }

    /**
     * Helper: Ambil data form pengaturan display
     * Mengonsolidasikan form inputs menjadi JSON grouped columns
     */
    private function _getFormData(?array $existingDisplay = null)
    {
        $idMasjidMushola = $this->request->getPost('id_masjid_mushola');
        if (in_groups('OperatorMasjidMushola') && !in_groups('SuperAdmin') && !in_groups('Admin')) {
            $idMasjidMushola = user()->entitas_id;
        }

        $dataInput = [
            'id_masjid_mushola'    => $idMasjidMushola,
            'nama_display'         => $this->request->getPost('nama_display'),
            'template_aktif'       => $this->request->getPost('template_aktif'),
            'orientasi'            => $this->request->getPost('orientasi') ?? 'horizontal',
            'nama_masjid_display'  => $this->request->getPost('nama_masjid_display'),
            'alamat_display'       => $this->request->getPost('alamat_display'),
            'running_text'         => $this->request->getPost('running_text'),
            'metode_hitung'        => $this->request->getPost('metode_hitung') ?? 'Kemenag',
            'sholat_jumat'         => $this->request->getPost('sholat_jumat') ?? 1,
            'interval_sync'        => $this->request->getPost('interval_sync') ?? 60,
            'aktif'                => $this->request->getPost('aktif') ?? 1,

            // JSON: Koreksi waktu sholat
            'koreksi_waktu' => json_encode([
                'subuh'   => (int)($this->request->getPost('koreksi_subuh') ?? 0),
                'dzuhur'  => (int)($this->request->getPost('koreksi_dzuhur') ?? 0),
                'ashar'   => (int)($this->request->getPost('koreksi_ashar') ?? 0),
                'maghrib' => (int)($this->request->getPost('koreksi_maghrib') ?? 0),
                'isya'    => (int)($this->request->getPost('koreksi_isya') ?? 0),
                'hijriah' => (int)($this->request->getPost('koreksi_hijriah') ?? 0),
            ]),

            // JSON: Timer durasi iqomah per waktu
            'timer_iqomah' => json_encode([
                'subuh'   => (int)($this->request->getPost('durasi_iqomah_subuh') ?? 10),
                'dzuhur'  => (int)($this->request->getPost('durasi_iqomah_dzuhur') ?? 10),
                'ashar'   => (int)($this->request->getPost('durasi_iqomah_ashar') ?? 10),
                'maghrib' => (int)($this->request->getPost('durasi_iqomah_maghrib') ?? 5),
                'isya'    => (int)($this->request->getPost('durasi_iqomah_isya') ?? 10),
            ]),

            // JSON: Mode event sholat (6 fase: menjelang_adzan, adzan, qobliyah, iqomah, sholat, badiyah)
            'mode_sholat_event' => json_encode([
                'menjelang_adzan' => [
                    'aktif'  => (int)($this->request->getPost('mode_menjelang_adzan') ?? 0),
                    'durasi' => (int)($this->request->getPost('durasi_menjelang_adzan') ?? 10),
                ],
                'adzan' => [
                    'aktif'  => (int)($this->request->getPost('mode_adzan') ?? 0),
                    'durasi' => (int)($this->request->getPost('durasi_adzan') ?? 7),
                ],
                'qobliyah' => [
                    'aktif'  => (int)($this->request->getPost('mode_qobliyah') ?? 0),
                    'durasi' => (int)($this->request->getPost('durasi_qobliyah') ?? 5),
                ],
                'iqomah' => [
                    'aktif'  => (int)($this->request->getPost('mode_iqomah') ?? 1),
                ],
                'sholat' => [
                    'aktif'  => (int)($this->request->getPost('mode_sholat') ?? 0),
                    'durasi' => (int)($this->request->getPost('durasi_sholat') ?? 15),
                ],
                'badiyah' => [
                    'aktif'  => (int)($this->request->getPost('mode_badiyah') ?? 0),
                    'durasi' => (int)($this->request->getPost('durasi_badiyah') ?? 5),
                ],
            ]),

            // JSON: Mode tarawih
            'mode_tarawih_json' => json_encode([
                'aktif'  => (int)($this->request->getPost('mode_tarawih') ?? 0),
                'durasi' => (int)($this->request->getPost('durasi_tarawih') ?? 60),
            ]),

            // JSON: Mode hari raya (idul adha & idul fitri)
            'mode_hari_raya' => json_encode([
                'idul_adha' => [
                    'aktif'   => (int)($this->request->getPost('mode_idul_adha') ?? 0),
                    'tanggal' => $this->request->getPost('tanggal_idul_adha') ?: null,
                    'durasi'  => (int)($this->request->getPost('durasi_idul_adha') ?? 120),
                ],
                'idul_fitri' => [
                    'aktif'   => (int)($this->request->getPost('mode_idul_fitri') ?? 0),
                    'tanggal' => $this->request->getPost('tanggal_idul_fitri') ?: null,
                    'durasi'  => (int)($this->request->getPost('durasi_idul_fitri') ?? 120),
                ],
            ]),

            // JSON: Opsi waktu sholat spesifik untuk qobliyah & badiyah
            'opsi_waktu_sholat' => json_encode([
                'qobliyah' => $this->request->getPost('opsi_qobliyah') ?? ['subuh'=>1, 'dzuhur'=>1, 'ashar'=>0, 'maghrib'=>1, 'isya'=>1],
                'badiyah'  => $this->request->getPost('opsi_badiyah') ?? ['subuh'=>0, 'dzuhur'=>1, 'ashar'=>0, 'maghrib'=>1, 'isya'=>1],
                'koordinat' => [
                    'latitude'  => $this->request->getPost('latitude'),
                    'longitude' => $this->request->getPost('longitude')
                ]
            ]),
        ];

        // JSON: General Display Setting (merging old JSON configuration with new inputs for specific namespaces)
        $oldDisplaySetting = [];
        if ($existingDisplay && !empty($existingDisplay['display_setting'])) {
            $oldDisplaySetting = json_decode($existingDisplay['display_setting'], true) ?: [];
        }

        $oldDisplaySetting['modern1'] = [
            'event_countdown' => [
                'tampilkan' => (bool)$this->request->getPost('modern1_event_tampilkan'),
                'label' => $this->request->getPost('modern1_event_label') ?: 'Ramadhan',
                'tanggal_target' => $this->request->getPost('modern1_event_tanggal_target') ?: date('Y-m-d H:i:s')
            ],
            'kutipan' => [
                'tampilkan' => (bool)$this->request->getPost('modern1_kutipan_tampilkan'),
                'teks' => $this->request->getPost('modern1_kutipan_teks') ?: '"Barangsiapa yang menempuh jalan untuk mencari ilmu, maka Allah akan mudahkan baginya jalan menuju surga." (HR. Muslim)'
            ]
        ];

        $dataInput['display_setting'] = json_encode($oldDisplaySetting);

        return $dataInput;
    }

    /**
     * Helper: Merge existing gambar paths dari record DB ke dalam JSON baru
     * Ini diperlukan karena _getFormData() build JSON tanpa key gambar,
     * sehingga tanpa merge ini semua gambar lama akan hilang saat update.
     *
     * @param array $dataInput - data form yang akan disimpan (berisi JSON string baru dari form)
     * @param array $existingDisplay - data display yang sudah ada di DB
     * @return array - dataInput dengan gambar paths yang dipertahankan
     */
    private function _mergeExistingImages(array $dataInput, array $existingDisplay): array
    {
        // 1. Merge gambar di mode_sholat_event (nested: key.gambar)
        $newModeEvent = json_decode($dataInput['mode_sholat_event'] ?? '{}', true) ?: [];
        $oldModeEvent = json_decode($existingDisplay['mode_sholat_event'] ?? '{}', true) ?: [];
        $eventKeys = ['menjelang_adzan', 'adzan', 'qobliyah', 'iqomah', 'sholat', 'badiyah'];
        foreach ($eventKeys as $key) {
            if (!empty($oldModeEvent[$key]['gambar']) && empty($newModeEvent[$key]['gambar'])) {
                if (!isset($newModeEvent[$key])) $newModeEvent[$key] = [];
                $newModeEvent[$key]['gambar'] = $oldModeEvent[$key]['gambar'];
            }
        }
        $dataInput['mode_sholat_event'] = json_encode($newModeEvent);

        // 2. Merge gambar di mode_tarawih_json (flat: gambar)
        $newTarawih = json_decode($dataInput['mode_tarawih_json'] ?? '{}', true) ?: [];
        $oldTarawih = json_decode($existingDisplay['mode_tarawih_json'] ?? '{}', true) ?: [];
        if (!empty($oldTarawih['gambar']) && empty($newTarawih['gambar'])) {
            $newTarawih['gambar'] = $oldTarawih['gambar'];
        }
        $dataInput['mode_tarawih_json'] = json_encode($newTarawih);

        // 3. Merge gambar di mode_hari_raya (nested: key.gambar)
        $newHariRaya = json_decode($dataInput['mode_hari_raya'] ?? '{}', true) ?: [];
        $oldHariRaya = json_decode($existingDisplay['mode_hari_raya'] ?? '{}', true) ?: [];
        foreach (['idul_adha', 'idul_fitri'] as $key) {
            if (!empty($oldHariRaya[$key]['gambar']) && empty($newHariRaya[$key]['gambar'])) {
                if (!isset($newHariRaya[$key])) $newHariRaya[$key] = [];
                $newHariRaya[$key]['gambar'] = $oldHariRaya[$key]['gambar'];
            }
        }
        $dataInput['mode_hari_raya'] = json_encode($newHariRaya);

        return $dataInput;
    }

    /**
     * Helper: Upload gambar overlay untuk setiap mode event sholat
     * Gambar disimpan ke filesystem dan path-nya diinjeksi ke JSON columns
     *
     * @param array $dataInput - data form yang akan disimpan (sudah berisi JSON string)
     * @param array|null $existingDisplay - data display lama (untuk edit/update)
     * @return array - dataInput yang sudah diupdate path gambar di dalam JSON
     */
    private function _handleModeImages(array $dataInput, ?array $existingDisplay = null): array
    {
        // Mapping: form field name → [json_column, json_key_path]
        $imageMap = [
            'gambar_menjelang_adzan' => ['mode_sholat_event', 'menjelang_adzan'],
            'gambar_adzan'           => ['mode_sholat_event', 'adzan'],
            'gambar_qobliyah'        => ['mode_sholat_event', 'qobliyah'],
            'gambar_iqomah'          => ['mode_sholat_event', 'iqomah'],
            'gambar_sholat'          => ['mode_sholat_event', 'sholat'],
            'gambar_badiyah'         => ['mode_sholat_event', 'badiyah'],
            'gambar_tarawih'         => ['mode_tarawih_json', null],
            'gambar_idul_adha'       => ['mode_hari_raya', 'idul_adha'],
            'gambar_idul_fitri'      => ['mode_hari_raya', 'idul_fitri'],
        ];

        $uploadDir = FCPATH . 'uploads/display/mode';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        foreach ($imageMap as $field => [$jsonCol, $jsonKey]) {
            $newPath = null;

            // Prioritas 1: Cek base64 dari crop modal (hidden input: cropped_gambar_*)
            $croppedData = $this->request->getPost('cropped_' . $field);
            if (!empty($croppedData) && strpos($croppedData, 'data:image/') === 0) {
                // Decode base64
                $parts = explode(',', $croppedData, 2);
                $imageData = base64_decode($parts[1] ?? '');
                if ($imageData) {
                    // Hapus gambar lama
                    $oldPath = $this->_getOldImagePath($existingDisplay, $jsonCol, $jsonKey);
                    if ($oldPath && file_exists(FCPATH . $oldPath)) {
                        unlink(FCPATH . $oldPath);
                    }

                    $namaFile = time() . '_' . bin2hex(random_bytes(10)) . '.jpg';
                    file_put_contents($uploadDir . '/' . $namaFile, $imageData);
                    $newPath = 'uploads/display/mode/' . $namaFile;
                }
            }

            // Prioritas 2: Cek file upload biasa (fallback jika tidak ada crop)
            if (!$newPath) {
                $file = $this->request->getFile($field);
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $oldPath = $this->_getOldImagePath($existingDisplay, $jsonCol, $jsonKey);
                    if ($oldPath && file_exists(FCPATH . $oldPath)) {
                        unlink(FCPATH . $oldPath);
                    }

                    $namaFile = $file->getRandomName();
                    $file->move($uploadDir, $namaFile);
                    $newPath = 'uploads/display/mode/' . $namaFile;
                }
            }

            // Inject path ke JSON jika ada gambar baru
            if ($newPath) {
                $dataInput = $this->_injectImageToJson($dataInput, $jsonCol, $jsonKey, $newPath);
            }
        }

        return $dataInput;
    }

    /**
     * Helper: Ambil old image path dari existing display JSON data
     */
    private function _getOldImagePath(?array $display, string $jsonCol, ?string $jsonKey): ?string
    {
        if (!$display || empty($display[$jsonCol])) return null;
        $data = json_decode($display[$jsonCol], true);
        if (!$data) return null;

        if ($jsonKey === null) {
            return $data['gambar'] ?? null;
        }
        return $data[$jsonKey]['gambar'] ?? null;
    }

    /**
     * Helper: Inject image path ke dalam JSON string di dataInput
     */
    private function _injectImageToJson(array $dataInput, string $jsonCol, ?string $jsonKey, string $path): array
    {
        $data = json_decode($dataInput[$jsonCol] ?? '{}', true) ?: [];

        if ($jsonKey === null) {
            // Top-level (e.g. mode_tarawih_json)
            $data['gambar'] = $path;
        } else {
            // Nested (e.g. mode_sholat_event.menjelang_adzan.gambar)
            if (!isset($data[$jsonKey])) $data[$jsonKey] = [];
            $data[$jsonKey]['gambar'] = $path;
        }

        $dataInput[$jsonCol] = json_encode($data);
        return $dataInput;
    }
}

