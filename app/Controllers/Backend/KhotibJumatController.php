<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;

class KhotibJumatController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    private function getFridays($tahun, $kuartal)
    {
        $startMonth = ($kuartal - 1) * 3 + 1;
        $endMonth = $startMonth + 2;
        
        $startDate = new \DateTime("$tahun-" . sprintf('%02d', $startMonth) . "-01");
        $endDate = clone $startDate;
        $endDate->modify("+3 months");
        
        $fridays = [];
        $current = clone $startDate;
        
        // Find first Friday (N = 5 in PHP DateTime)
        while ($current->format('N') != 5) {
            $current->modify('+1 day');
        }
        
        while ($current < $endDate) {
            $fridays[] = $current->format('Y-m-d');
            $current->modify('+7 days');
        }
        
        return $fridays;
    }

    public function index()
    {
        $tahunPilih = $this->request->getGet('tahun') ?? date('Y');
        $kuartalPilih = $this->request->getGet('kuartal') ?? ceil(date('n') / 3);

        $fridays = $this->getFridays($tahunPilih, $kuartalPilih);

        // Ambil Data Master Masjid (Hanya jenis Masjid saja untuk Sholat Jumat)
        $masjidList = $this->db->table('tbl_masjid_mushola')
            ->where('jenis', 'Masjid')
            ->orderBy('nama', 'ASC')->get()->getResultArray();
            
        // Ambil Data Jadwal Jumat untuk kuartal ini
        $jadwalData = $this->db->table('tbl_jadwal_kegiatan')
            ->where('jenis_kegiatan', 'jumat')
            ->where('tahun_masehi', $tahunPilih)
            ->whereIn('tanggal', $fridays)
            ->get()->getResultArray();

        // Siapkan struktur mapping [id_masjid][tanggal] = id_personil
        $matrixIds = [];
        $personilIds = [];
        foreach ($jadwalData as $row) {
            $matrixIds[$row['id_masjid_mushola']][$row['tanggal']] = $row['id_personil'];
            if (!in_array($row['id_personil'], $personilIds)) {
                $personilIds[] = $row['id_personil'];
            }
        }

        // Ambil nama personil untuk inisialisasi awal dropdown Select2
        $personilNames = [];
        if (!empty($personilIds)) {
            $personils = $this->db->table('tbl_personil')->whereIn('id', $personilIds)->get()->getResultArray();
            foreach ($personils as $p) {
                $personilNames[$p['id']] = [
                    'nama' => $p['nama_lengkap'],
                    'foto' => $p['foto'] ? base_url('uploads/personil/' . $p['foto']) : base_url('template/backend/dist/img/default-150x150.png')
                ];
            }
        }

        $data = [
            'title'         => 'Matriks Khotib Jumat',
            'pageTitle'     => 'Jadwal Khotib Jumat',
            'tahunPilih'    => $tahunPilih,
            'kuartalPilih'  => $kuartalPilih,
            'fridays'       => $fridays,
            'masjidList'    => $masjidList,
            'matrixIds'     => $matrixIds,
            'personilNames' => $personilNames,
            'breadcrumb'    => [
                ['title' => 'Dashboard', 'url' => 'admin/dashboard'],
                ['title' => 'Khotib Jumat', 'url' => '']
            ]
        ];

        return view('backend/khotib_jumat/index', $data);
    }

    public function save_cell()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        $idMasjid = $this->request->getPost('id_masjid');
        $tanggal  = $this->request->getPost('tanggal');
        $idMubaligh = $this->request->getPost('id_mubaligh');
        $tahun    = date('Y', strtotime($tanggal));

        $db = \Config\Database::connect();
        
        // Cek apakah cell sudah ada isinya
        $existing = $db->table('tbl_jadwal_kegiatan')
            ->where('jenis_kegiatan', 'jumat')
            ->where('id_masjid_mushola', $idMasjid)
            ->where('tanggal', $tanggal)
            ->get()->getRowArray();

        // Cek clash (Mubaligh sudah tugas di masjid lain pada jumat yg sama)
        if (!empty($idMubaligh)) {
            $clash = $db->table('tbl_jadwal_kegiatan')
                ->where('jenis_kegiatan', 'jumat')
                ->where('tanggal', $tanggal)
                ->where('id_personil', $idMubaligh)
                ->where('id_masjid_mushola !=', $idMasjid)
                ->get()->getRowArray();

            if ($clash) {
                $masjidClash = $db->table('tbl_masjid_mushola')
                    ->where('id_masjid_mushola', $clash['id_masjid_mushola'])
                    ->get()->getRowArray();
                $namaMasjid = $masjidClash ? $masjidClash['nama'] : 'Masjid Lain';
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Khotib ini sudah dijadwalkan di ' . $namaMasjid . ' pada tanggal tersebut.'
                ]);
            }
        }

        try {
            if ($existing) {
                if (empty($idMubaligh)) {
                    // Delete if cleared
                    $db->table('tbl_jadwal_kegiatan')->where('id', $existing['id'])->delete();
                } else {
                    // Update
                    $db->table('tbl_jadwal_kegiatan')->where('id', $existing['id'])->update([
                        'id_personil' => $idMubaligh,
                        'updated_at'  => date('Y-m-d H:i:s')
                    ]);
                }
            } else {
                if (!empty($idMubaligh)) {
                    // Insert new
                    $db->table('tbl_jadwal_kegiatan')->insert([
                        'jenis_kegiatan'    => 'jumat',
                        'tahun_masehi'      => $tahun,
                        'id_masjid_mushola' => $idMasjid,
                        'tanggal'           => $tanggal,
                        'id_personil'       => $idMubaligh,
                        'peran_petugas'     => 'khotib',
                        'created_at'        => date('Y-m-d H:i:s'),
                        'updated_at'        => date('Y-m-d H:i:s')
                    ]);
                }
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Data berhasil disimpan',
            'debug_query' => $db->getLastQuery()->getQuery()
        ]);
    }

    public function cetak_mubaligh($id_personil)
    {
        $tahunPilih = $this->request->getGet('tahun') ?? date('Y');
        
        $db = \Config\Database::connect();
        $mubaligh = $db->table('tbl_personil')->where('id', $id_personil)->get()->getRowArray();
        
        if (!$mubaligh) {
            return redirect()->back()->with('error', 'Khotib tidak ditemukan');
        }

        $jadwalList = $db->table('tbl_jadwal_kegiatan j')
            ->select('j.tanggal, m.nama as nama_masjid, m.alamat as alamat_masjid')
            ->join('tbl_masjid_mushola m', 'm.id_masjid_mushola = j.id_masjid_mushola', 'left')
            ->where('j.jenis_kegiatan', 'jumat')
            ->where('j.tahun_masehi', $tahunPilih)
            ->where('j.id_personil', $id_personil)
            ->orderBy('j.tanggal', 'ASC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Cetak Jadwal Khotib - ' . $mubaligh['nama_lengkap'],
            'mubaligh' => $mubaligh,
            'jadwalList' => $jadwalList,
            'tahunPilih' => $tahunPilih
        ];

        return view('backend/khotib_jumat/print_mubaligh', $data);
    }

    public function cetak_masjid($id_masjid)
    {
        $tahunPilih = $this->request->getGet('tahun') ?? date('Y');
        
        $db = \Config\Database::connect();
        $masjid = $db->table('tbl_masjid_mushola')->where('id_masjid_mushola', $id_masjid)->get()->getRowArray();
        
        if (!$masjid) {
            return redirect()->back()->with('error', 'Masjid tidak ditemukan');
        }

        $jadwalList = $db->table('tbl_jadwal_kegiatan j')
            ->select('j.tanggal, p.nama_lengkap as nama_mubaligh, p.no_hp')
            ->join('tbl_personil p', 'p.id = j.id_personil', 'left')
            ->where('j.jenis_kegiatan', 'jumat')
            ->where('j.tahun_masehi', $tahunPilih)
            ->where('j.id_masjid_mushola', $id_masjid)
            ->orderBy('j.tanggal', 'ASC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Cetak Jadwal Masjid - ' . $masjid['nama'],
            'masjid' => $masjid,
            'jadwalList' => $jadwalList,
            'tahunPilih' => $tahunPilih
        ];

        return view('backend/khotib_jumat/print_masjid', $data);
    }
}
