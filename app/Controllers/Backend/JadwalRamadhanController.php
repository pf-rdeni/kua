<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\TemaCeramahModel;
use App\Models\JadwalKegiatanModel;
use App\Models\MasjidMusholaModel;
use App\Models\PersonilModel;

class JadwalRamadhanController extends BaseController
{
    protected $temaModel;
    protected $jadwalModel;
    protected $masjidModel;
    protected $personilModel;

    public function __construct()
    {
        $this->temaModel = new TemaCeramahModel();
        $this->jadwalModel = new JadwalKegiatanModel();
        $this->masjidModel = new MasjidMusholaModel();
        $this->personilModel = new PersonilModel();
    }

    public function index()
    {
        $tahunHijriah = $this->request->getGet('tahun') ?? '1446 H'; // Default
        
        $temaList = $this->temaModel->where('tahun_hijriah', $tahunHijriah)->orderBy('hari_ke', 'ASC')->findAll();
        $tanggals = array_column($temaList, 'tanggal', 'hari_ke');

        $data = [
            'title'        => 'Jadwal Penceramah Ramadhan',
            'masjidList'   => $this->masjidModel->orderBy('nama', 'ASC')->findAll(),
            'matriks'      => $this->jadwalModel->getMatriksRamadhan($tahunHijriah),
            'tanggals'     => $tanggals,
            'tahunHijriah' => $tahunHijriah,
        ];

        return view('backend/jadwal_ramadhan/index', $data);
    }
    
    public function tema()
    {
        $tahunHijriah = $this->request->getGet('tahun') ?? '1446 H'; // Default
        
        // Inisialisasi tema 1-30 jika belum ada
        $existingTema = $this->temaModel->where('tahun_hijriah', $tahunHijriah)
                                        ->where('jenis_kegiatan', 'ramadhan')
                                        ->orderBy('hari_ke', 'ASC')
                                        ->findAll();
                                        
        if (count($existingTema) == 0) {
            // Generate 30 days
            $insertData = [];
            for ($i = 1; $i <= 30; $i++) {
                $insertData[] = [
                    'jenis_kegiatan' => 'ramadhan',
                    'tahun_hijriah'  => $tahunHijriah,
                    'hari_ke'        => $i,
                    'tema'           => ''
                ];
            }
            $this->temaModel->insertBatch($insertData);
            
            // Reload
            $existingTema = $this->temaModel->where('tahun_hijriah', $tahunHijriah)
                                        ->where('jenis_kegiatan', 'ramadhan')
                                        ->orderBy('hari_ke', 'ASC')
                                        ->findAll();
        }
        
        $data = [
            'title'        => 'Tema Ceramah Ramadhan',
            'temaList'     => $existingTema,
            'tahunHijriah' => $tahunHijriah,
        ];
        
        return view('backend/jadwal_ramadhan/tema', $data);
    }

    public function save_tema()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getPost('id');
            $tema = $this->request->getPost('tema');
            $tanggal = $this->request->getPost('tanggal');
            
            $updateData = ['tema' => $tema];
            if ($tanggal !== null) {
                $updateData['tanggal'] = empty($tanggal) ? null : $tanggal;
            }

            if ($this->temaModel->update($id, $updateData)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Tersimpan']);
            }
            
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan tema']);
        }
    }

    public function duplicate_tema()
    {
        if ($this->request->isAJAX()) {
            $from_year = $this->request->getPost('from_year');
            $to_year = $this->request->getPost('to_year');

            if (empty($from_year) || empty($to_year) || $from_year == $to_year) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Tahun asal dan tujuan tidak valid']);
            }

            // Hapus target existing jika ada
            $this->temaModel->where('tahun_hijriah', $to_year)->delete();

            // Ambil data dari source
            $sourceThemes = $this->temaModel->where('tahun_hijriah', $from_year)->findAll();
            
            if (empty($sourceThemes)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada tema di tahun sumber (' . $from_year . ')']);
            }

            $insertData = [];
            foreach ($sourceThemes as $t) {
                $insertData[] = [
                    'jenis_kegiatan' => $t['jenis_kegiatan'],
                    'tahun_hijriah' => $to_year,
                    'hari_ke' => $t['hari_ke'],
                    'tema' => $t['tema'],
                    'tanggal' => null // Tanggal Masehi harus digenerate ulang
                ];
            }

            if (!empty($insertData)) {
                $this->temaModel->insertBatch($insertData);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Berhasil menduplikasi tema ke tahun ' . esc($to_year)
            ]);
        }
    }

    public function duplicate_jadwal()
    {
        if ($this->request->isAJAX()) {
            $from_year = $this->request->getPost('from_year');
            $to_year = $this->request->getPost('to_year');

            if (empty($from_year) || empty($to_year) || $from_year == $to_year) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Tahun asal dan tujuan tidak valid']);
            }

            // Hapus target existing jika ada
            $this->jadwalModel->where('jenis_kegiatan', 'ramadhan')->where('tahun_hijriah', $to_year)->delete();

            // Ambil data dari source
            $sourceSchedules = $this->jadwalModel
                                    ->where('jenis_kegiatan', 'ramadhan')
                                    ->where('tahun_hijriah', $from_year)
                                    ->findAll();
            
            if (empty($sourceSchedules)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada jadwal di tahun sumber (' . $from_year . ')']);
            }

            // Ambil data tanggal dari tema target untuk dipetakan
            $targetThemes = $this->temaModel->where('tahun_hijriah', $to_year)->findAll();
            $targetDateMap = [];
            foreach ($targetThemes as $tt) {
                $targetDateMap[$tt['hari_ke']] = $tt['tanggal'];
            }

            $insertData = [];
            foreach ($sourceSchedules as $s) {
                // Cari tanggal masehi pada target year
                $tanggalTarget = $targetDateMap[$s['hari_ke']] ?? null;

                $insertData[] = [
                    'jenis_kegiatan' => $s['jenis_kegiatan'],
                    'tahun_hijriah' => $to_year,
                    'id_masjid_mushola' => $s['id_masjid_mushola'],
                    'id_personil' => $s['id_personil'],
                    'hari_ke' => $s['hari_ke'],
                    'id_tema' => null, // opsional
                    'tanggal' => $tanggalTarget // Sinkronisasi Tanggal Masehi (Phase 6)
                ];
            }

            if (!empty($insertData)) {
                $this->jadwalModel->insertBatch($insertData);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Berhasil menduplikasi matriks jadwal ke tahun ' . esc($to_year)
            ]);
        }
    }

    public function search_mubaligh()
    {
        if ($this->request->isAJAX()) {
            $keyword = $this->request->getGet('q');
            $hari_ke = $this->request->getGet('hari_ke');
            $tahun_hijriah = $this->request->getGet('tahun_hijriah');
            
            // Find IDs of mubalighs already scheduled on this day and year
            $scheduledIds = [];
            if ($hari_ke && $tahun_hijriah) {
                $scheduled = $this->jadwalModel
                    ->select('id_personil')
                    ->where('jenis_kegiatan', 'ramadhan')
                    ->where('tahun_hijriah', $tahun_hijriah)
                    ->where('hari_ke', $hari_ke)
                    ->where('id_personil IS NOT NULL')
                    ->findAll();
                $scheduledIds = array_column($scheduled, 'id_personil');
            }

            $builder = $this->personilModel->ofType('mubaligh')->where('status_aktif', 1);
            
            // Exclude those already scheduled
            if (!empty($scheduledIds)) {
                $builder->whereNotIn('id', $scheduledIds);
            }

            if ($keyword) {
                $builder->groupStart()
                        ->like('nama_lengkap', $keyword)
                        ->orLike('nia', $keyword)
                        ->groupEnd();
            }
            
            $mubalighs = $builder->findAll();
            
            $results = [];
            foreach ($mubalighs as $m) {
                $results[] = [
                    'id'   => $m['id'],
                    'text' => $m['nia'] . ' - ' . $m['nama_lengkap'],
                    'foto' => $m['foto'] ? base_url('uploads/personil/' . $m['foto']) : base_url('template/backend/dist/img/default-150x150.png')
                ];
            }
            
            return $this->response->setJSON([
                'results' => $results
            ]);
        }
    }

    public function save_cell()
    {
        if ($this->request->isAJAX()) {
            $id_masjid = $this->request->getPost('id_masjid');
            $hari_ke = $this->request->getPost('hari_ke');
            $tahun_hijriah = $this->request->getPost('tahun_hijriah');
            $id_personil = $this->request->getPost('id_personil');
            
            // Cek apakah jadwal sudah ada
            $existing = $this->jadwalModel
                            ->where('jenis_kegiatan', 'ramadhan')
                            ->where('tahun_hijriah', $tahun_hijriah)
                            ->where('id_masjid_mushola', $id_masjid)
                            ->where('hari_ke', $hari_ke)
                            ->first();

            // AMBIL TANGGAL DARI TEMA
            $tema = $this->temaModel
                        ->where('jenis_kegiatan', 'ramadhan')
                        ->where('tahun_hijriah', $tahun_hijriah)
                        ->where('hari_ke', $hari_ke)
                        ->first();
            $tanggalMasehi = $tema ? $tema['tanggal'] : null;

            $data = [
                'jenis_kegiatan'    => 'ramadhan',
                'tahun_hijriah'     => $tahun_hijriah,
                'id_masjid_mushola' => $id_masjid,
                'hari_ke'           => $hari_ke,
                'tanggal'           => $tanggalMasehi,
                'id_personil'       => empty($id_personil) ? null : $id_personil
            ];

            if ($existing) {
                if (empty($id_personil)) {
                    // hapus jika kosong
                    $this->jadwalModel->delete($existing['id']);
                } else {
                    $this->jadwalModel->update($existing['id'], $data);
                }
            } else {
                if (!empty($id_personil)) {
                    $this->jadwalModel->insert($data);
                }
            }

            // Get personil info for response
            $personil_info = null;
            if (!empty($id_personil)) {
                $p = $this->personilModel->find($id_personil);
                $personil_info = [
                    'nik' => $p['nik'],
                    'nia' => $p['nia'],
                    'nama' => $p['nama_lengkap'],
                    'foto' => $p['foto'] ? base_url('uploads/personil/' . $p['foto']) : base_url('dist/img/default-150x150.png')
                ];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Berhasil disimpan',
                'personil' => $personil_info
            ]);
        }
    }

    public function reset_jadwal()
    {
        if ($this->request->isAJAX()) {
            $tahun_hijriah = $this->request->getPost('tahun_hijriah');

            if (empty($tahun_hijriah)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Tahun tidak ditemukan']);
            }

            $this->jadwalModel->where('tahun_hijriah', $tahun_hijriah)->where('jenis_kegiatan', 'ramadhan')->delete();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Berhasil mereset seluruh jadwal matriks Ramadhan ' . esc($tahun_hijriah)
            ]);
        }
    }

    public function export_excel()
    {
        $tahunHijriah = $this->request->getGet('tahun') ?? '1446 H';
        
        $temaList = $this->temaModel->where('tahun_hijriah', $tahunHijriah)->orderBy('hari_ke', 'ASC')->findAll();
        $tanggals = array_column($temaList, 'tanggal', 'hari_ke');

        $data = [
            'masjidList'   => $this->masjidModel->orderBy('nama', 'ASC')->findAll(),
            'matriks'      => $this->jadwalModel->getMatriksRamadhan($tahunHijriah),
            'tanggals'     => $tanggals,
            'tahunHijriah' => $tahunHijriah,
        ];

        return view('backend/jadwal_ramadhan/export_excel', $data);
    }

    public function cetak_mubaligh($id_personil)
    {
        $tahunHijriah = $this->request->getGet('tahun') ?? '1446 H';
        
        // Ambil Data Mubaligh
        $mubaligh = $this->personilModel->find($id_personil);
        if (!$mubaligh) {
            return redirect()->back()->with('error', 'Mubaligh tidak ditemukan');
        }

        // Ambil Jadwal spesifik Mubaligh
        $db = \Config\Database::connect();
        $jadwalList = $db->table('tbl_jadwal_kegiatan j')
            ->select('j.hari_ke, m.nama as nama_masjid, m.alamat as alamat_masjid, t.tema, t.tanggal')
            ->join('tbl_masjid_mushola m', 'm.id_masjid_mushola = j.id_masjid_mushola', 'left')
            ->join('tbl_tema_ceramah t', 't.hari_ke = j.hari_ke AND t.tahun_hijriah = j.tahun_hijriah', 'left')
            ->where('j.jenis_kegiatan', 'ramadhan')
            ->where('j.tahun_hijriah', $tahunHijriah)
            ->where('j.id_personil', $id_personil)
            ->orderBy('j.hari_ke', 'ASC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Cetak Jadwal Mubaligh - ' . $mubaligh['nama_lengkap'],
            'mubaligh' => $mubaligh,
            'jadwalList' => $jadwalList,
            'tahunHijriah' => $tahunHijriah
        ];

        return view('backend/jadwal_ramadhan/print_mubaligh', $data);
    }

    public function cetak_masjid($id_masjid)
    {
        $tahunHijriah = $this->request->getGet('tahun') ?? '1446 H';
        
        // Ambil Data Masjid
        $masjid = $this->masjidModel->find($id_masjid);
        if (!$masjid) {
            return redirect()->back()->with('error', 'Masjid tidak ditemukan');
        }

        // Ambil Jadwal spesifik Masjid
        $db = \Config\Database::connect();
        $jadwalList = $db->table('tbl_tema_ceramah t')
            ->select('t.hari_ke, t.tanggal, t.tema, p.nama_lengkap as nama_mubaligh, p.no_hp')
            ->join('tbl_jadwal_kegiatan j', 'j.hari_ke = t.hari_ke AND j.tahun_hijriah = t.tahun_hijriah AND j.id_masjid_mushola = ' . $id_masjid, 'left')
            ->join('tbl_personil p', 'p.id = j.id_personil', 'left')
            ->where('t.tahun_hijriah', $tahunHijriah)
            ->orderBy('t.hari_ke', 'ASC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Cetak Jadwal Masjid - ' . $masjid['nama'],
            'masjid' => $masjid,
            'jadwalList' => $jadwalList,
            'tahunHijriah' => $tahunHijriah
        ];

        return view('backend/jadwal_ramadhan/print_masjid', $data);
    }

    public function generate_tanggal_masehi()
    {
        if ($this->request->isAJAX()) {
            $tahun_hijriah = $this->request->getPost('tahun_hijriah');
            $tanggal_mulai = $this->request->getPost('tanggal_mulai');

            if (empty($tanggal_mulai)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Tanggal awal tidak boleh kosong']);
            }

            try {
                $baseDate = new \DateTime($tanggal_mulai);
                
                for ($i = 1; $i <= 30; $i++) {
                    $currentDate = clone $baseDate;
                    if ($i > 1) {
                        $currentDate->modify('+' . ($i - 1) . ' days');
                    }
                    
                    $formattedDate = $currentDate->format('Y-m-d');
                    
                    $existingTema = $this->temaModel->where('tahun_hijriah', $tahun_hijriah)
                                                    ->where('hari_ke', $i)
                                                    ->first();

                    if ($existingTema) {
                        $this->temaModel->update($existingTema['id'], ['tanggal' => $formattedDate]);
                    } else {
                        $this->temaModel->insert([
                            'jenis_kegiatan' => 'ramadhan',
                            'tahun_hijriah'  => $tahun_hijriah,
                            'hari_ke'        => $i,
                            'tanggal'        => $formattedDate,
                            'tema'           => ''
                        ]);
                    }
                }

                return $this->response->setJSON(['status' => 'success', 'message' => 'Tanggal 1-30 Ramadhan berhasil di-generate otomatis.']);
            } catch (\Exception $e) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memproses tanggal: ' . $e->getMessage()]);
            }
        }
    }
}
