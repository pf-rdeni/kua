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

    public function save_matrix()
    {
        $tahun = $this->request->getPost('tahun');
        $matrixForm = $this->request->getPost('matrix'); // array of bulans

        if (empty($tahun) || empty($matrixForm)) {
            return redirect()->back()->with('error', 'Data input tidak valid.');
        }

        $this->db->transStart();

        // Hapus konfigurasi lama untuk tahun ini
        $this->db->table('tbl_jadwal_kegiatan')
            ->where('jenis_kegiatan', 'maghrib_mengaji')
            ->where('tahun_masehi', $tahun)
            ->delete();

        // Insert konfigurasi baru
        $insertBatch = [];
        foreach ($matrixForm as $bulan => $item) {
            // Skip jika tanggal kosong atau masjid kosong
            if (empty($item['tanggal']) || empty($item['id_masjid'])) {
                continue;
            }

            // Peran MC
            if (!empty($item['mc'])) {
                $insertBatch[] = [
                    'jenis_kegiatan'    => 'maghrib_mengaji',
                    'tahun_masehi'      => $tahun,
                    'bulan'             => $bulan,
                    'tanggal'           => $item['tanggal'],
                    'id_masjid_mushola' => $item['id_masjid'],
                    'id_personil'       => $item['mc'],
                    'peran_petugas'     => 'mc',
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s')
                ];
            }
            
            // Peran Doa
            if (!empty($item['doa'])) {
                $insertBatch[] = [
                    'jenis_kegiatan'    => 'maghrib_mengaji',
                    'tahun_masehi'      => $tahun,
                    'bulan'             => $bulan,
                    'tanggal'           => $item['tanggal'],
                    'id_masjid_mushola' => $item['id_masjid'],
                    'id_personil'       => $item['doa'],
                    'peran_petugas'     => 'doa',
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s')
                ];
            }
            
            // Peran Kultum
            if (!empty($item['kultum'])) {
                $insertBatch[] = [
                    'jenis_kegiatan'    => 'maghrib_mengaji',
                    'tahun_masehi'      => $tahun,
                    'bulan'             => $bulan,
                    'tanggal'           => $item['tanggal'],
                    'id_masjid_mushola' => $item['id_masjid'],
                    'id_personil'       => $item['kultum'],
                    'peran_petugas'     => 'kultum',
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s')
                ];
            }
        }

        if (!empty($insertBatch)) {
            $this->db->table('tbl_jadwal_kegiatan')->insertBatch($insertBatch);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menyimpan tabel jadwal Maghrib Mengaji.');
        }

        return redirect()->back()->with('success', 'Berhasil menyimpan jadwal Maghrib Mengaji tahun ' . $tahun);
    }
}
