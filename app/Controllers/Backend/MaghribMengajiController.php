<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;

class MaghribMengajiController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $tahunPilih = $this->request->getGet('tahun') ?? date('Y');
        $currentYear = (int)date('Y');
        $years = [$currentYear, $currentYear + 1];

        // Ambil Data Master
        $masjidList = $this->db->table('tbl_masjid_mushola')->orderBy('nama', 'ASC')->get()->getResultArray();
        
        // Ambil data matrix dari database untuk tahun terpilih
        $existingData = $this->db->table('tbl_jadwal_kegiatan')
            ->where('jenis_kegiatan', 'maghrib_mengaji')
            ->where('tahun_masehi', $tahunPilih)
            ->get()->getResultArray();

        // Susun data agar mudah dirender di View
        // Struktur: $matrix[bulan] = ['tanggal' => '', 'id_masjid' => '', 'mc' => '', 'doa' => '', 'kultum' => '']
        $matrix = [];
        for ($b = 1; $b <= 12; $b++) {
            $matrix[$b] = [
                'tanggal' => '',
                'id_masjid' => '',
                'mc' => '',
                'doa' => '',
                'kultum' => '',
                'nama_mc' => '',
                'nama_doa' => '',
                'nama_kultum' => ''
            ];
        }

        // Mapping row database ke array matrix
        if (!empty($existingData)) {
            // Ambil nama personil untuk inisialisasi awal dropdown Select2
            $personilIds = array_column($existingData, 'id_personil');
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

            foreach ($existingData as $row) {
                $b = $row['bulan'];
                $matrix[$b]['tanggal'] = $row['tanggal'];
                $matrix[$b]['id_masjid'] = $row['id_masjid_mushola'];
                
                $peran = $row['peran_petugas']; // mc, doa, kultum
                if (in_array($peran, ['mc', 'doa', 'kultum'])) {
                    $matrix[$b][$peran] = $row['id_personil'];
                    $matrix[$b]['nama_'.$peran] = $personilNames[$row['id_personil']] ?? '';
                }
            }
        }

        $data = [
            'title'      => 'Matriks Maghrib Mengaji',
            'pageTitle'  => 'Jadwal Maghrib Mengaji',
            'tahunPilih' => $tahunPilih,
            'years'      => $years,
            'masjidList' => $masjidList,
            'matrix'     => $matrix,
            'personilNames' => $personilNames ?? [],
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => 'admin/dashboard'],
                ['title' => 'Maghrib Mengaji', 'url' => '']
            ]
        ];

        return view('backend/maghrib_mengaji/index', $data);
    }

    public function save_cell()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        $tahun = $this->request->getPost('tahun');
        $bulan = $this->request->getPost('bulan');
        $field = $this->request->getPost('field');     // tanggal, id_masjid, mc, doa, kultum
        $value = $this->request->getPost('value');

        try {
            if ($field === 'tanggal' || $field === 'id_masjid') {
                // Update ke SEMUA peran di bulan & tahun tersebut agar sinkron
                $dbField = ($field === 'id_masjid') ? 'id_masjid_mushola' : $field;
                $this->db->table('tbl_jadwal_kegiatan')
                    ->where('jenis_kegiatan', 'maghrib_mengaji')
                    ->where('tahun_masehi', $tahun)
                    ->where('bulan', $bulan)
                    ->update([$dbField => $value, 'updated_at' => date('Y-m-d H:i:s')]);
            } else {
                // Update SPECIFIC peran (mc, doa, kultum)
                // Cari data existing untuk peran ini
                $existing = $this->db->table('tbl_jadwal_kegiatan')
                    ->where('jenis_kegiatan', 'maghrib_mengaji')
                    ->where('tahun_masehi', $tahun)
                    ->where('bulan', $bulan)
                    ->where('peran_petugas', $field)
                    ->get()->getRowArray();

                if (empty($value)) {
                    // Hapus jika value dikosongkan
                    if ($existing) {
                        $this->db->table('tbl_jadwal_kegiatan')->where('id', $existing['id'])->delete();
                    }
                } else {
                    // Ambil info master (tanggal & masjid) dari peran lain di bulan tersebut agar sinkron
                    $otherData = $this->db->table('tbl_jadwal_kegiatan')
                        ->where('jenis_kegiatan', 'maghrib_mengaji')
                        ->where('tahun_masehi', $tahun)
                        ->where('bulan', $bulan)
                        ->get()->getRowArray();

                    $data = [
                        'id_personil'       => $value,
                        'updated_at'        => date('Y-m-d H:i:s')
                    ];

                    if ($existing) {
                        $this->db->table('tbl_jadwal_kegiatan')->where('id', $existing['id'])->update($data);
                    } else {
                        $data['jenis_kegiatan']    = 'maghrib_mengaji';
                        $data['tahun_masehi']      = $tahun;
                        $data['bulan']             = $bulan;
                        $data['peran_petugas']     = $field;
                        $data['tanggal']           = $otherData ? $otherData['tanggal'] : null;
                        $data['id_masjid_mushola'] = $otherData ? $otherData['id_masjid_mushola'] : null;
                        $data['created_at']        = date('Y-m-d H:i:s');
                        $this->db->table('tbl_jadwal_kegiatan')->insert($data);
                    }
                }
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Tersimpan']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }



    public function search_mubaligh()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        $keyword = $this->request->getGet('q');
        $excludeIds = $this->request->getGet('exclude_ids'); // Array of IDs to exclude (same row)

        $builder = $this->db->table('tbl_personil')
            ->where('status_aktif', 1)
            ->where('entitas_type', 'mubaligh');
        
        if (!empty($excludeIds) && is_array($excludeIds)) {
            $builder->whereNotIn('id', $excludeIds);
        }

        if ($keyword) {
            $builder->groupStart()
                    ->like('nama_lengkap', $keyword)
                    ->orLike('nik', $keyword)
                    ->groupEnd();
        }
        
        $mubalighs = $builder->get()->getResultArray();
        
        $results = [];
        foreach ($mubalighs as $m) {
            $results[] = [
                'id'   => $m['id'],
                'text' => $m['nama_lengkap'],
                'nama' => $m['nama_lengkap'],
                'foto' => $m['foto'] ? base_url('uploads/personil/' . $m['foto']) : base_url('template/backend/dist/img/default-150x150.png')
            ];
        }
        
        return $this->response->setJSON([
            'results' => $results
        ]);
    }

    public function get_cetak()
    {
        $tahunPilih = $this->request->getGet('tahun') ?? date('Y');
        
        $jadwalRaw = $this->db->table('tbl_jadwal_kegiatan j')
            ->select('j.bulan, j.tanggal, j.peran_petugas, j.id_personil, j.id_masjid_mushola, m.nama as nama_masjid, p.nama_lengkap as nama_personil')
            ->join('tbl_masjid_mushola m', 'm.id_masjid_mushola = j.id_masjid_mushola', 'left')
            ->join('tbl_personil p', 'p.id = j.id_personil', 'left')
            ->where('j.jenis_kegiatan', 'maghrib_mengaji')
            ->where('j.tahun_masehi', $tahunPilih)
            ->get()->getResultArray();

        $matrix = [];
        for ($i = 1; $i <= 12; $i++) {
            $matrix[$i] = [
                'tanggal' => '',
                'id_masjid' => '',
                'nama_masjid' => '',
                'mc' => '',
                'doa' => '',
                'kultum' => ''
            ];
        }

        foreach ($jadwalRaw as $row) {
            $bulan = $row['bulan'];
            $matrix[$bulan]['tanggal'] = $row['tanggal'];
            $matrix[$bulan]['id_masjid'] = $row['id_masjid_mushola'];
            $matrix[$bulan]['nama_masjid'] = $row['nama_masjid'];
            
            if ($row['peran_petugas'] == 'mc') $matrix[$bulan]['mc'] = $row['nama_personil'];
            if ($row['peran_petugas'] == 'doa') $matrix[$bulan]['doa'] = $row['nama_personil'];
            if ($row['peran_petugas'] == 'kultum') $matrix[$bulan]['kultum'] = $row['nama_personil'];
        }

        $data = [
            'title' => 'Cetak Jadwal Maghrib Mengaji Tahun ' . $tahunPilih,
            'tahunPilih' => $tahunPilih,
            'matrix' => $matrix
        ];

        return view('backend/maghrib_mengaji/print', $data);
    }
}
