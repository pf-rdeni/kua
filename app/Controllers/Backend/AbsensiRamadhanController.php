<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;

class AbsensiRamadhanController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // 1. Ambil Parameter Tanggal (Default: Hari Ini)
        $tanggalPilih = $this->request->getGet('tanggal') ?? date('Y-m-d');
        
        // 2. Query Jadwal (Masjid, Mubaligh, Kehadiran via tbl_absensi) pada tanggal tersebut
        $jadwalHarian = $this->db->table('tbl_jadwal_kegiatan j')
            ->select('
                j.id as id_jadwal, 
                j.hari_ke, 
                j.tahun_hijriah, 
                j.id_personil,
                j.jenis_kegiatan,
                a.status_kehadiran,
                a.keterangan as keterangan_absensi,
                a.id_personil_pengganti,
                m.nama as nama_masjid, 
                m.alamat as alamat_masjid, 
                p.nama_lengkap as nama_mubaligh, 
                p.no_hp as no_hp_mubaligh,
                p2.nama_lengkap as nama_pengganti
            ')
            ->join('tbl_masjid_mushola m', 'm.id_masjid_mushola = j.id_masjid_mushola', 'left')
            ->join('tbl_personil p', 'p.id = j.id_personil', 'left')
            ->join('tbl_tema_ceramah t', 't.hari_ke = j.hari_ke AND t.tahun_hijriah = j.tahun_hijriah', 'left')
            ->join('tbl_absensi a', 'a.id_jadwal = j.id', 'left')
            ->join('tbl_personil p2', 'p2.id = a.id_personil_pengganti', 'left')
            ->where('j.jenis_kegiatan', 'ramadhan')
            ->where('t.jenis_kegiatan', 'ramadhan')
            ->where('t.tanggal', $tanggalPilih)
            ->where('j.id_personil IS NOT NULL') // Hanya yg sudah diisi penceramah
            ->orderBy('m.nama', 'ASC')
            ->get()->getResultArray();

        $data = [
            'title'        => 'Absensi Jadwal Ramadhan',
            'pageTitle'    => 'Absensi Penceramah Ramadhan',
            'tanggalPilih' => $tanggalPilih,
            'jadwalHarian' => $jadwalHarian,
            'breadcrumb'   => [
                ['title' => 'Dashboard', 'url' => 'admin/dashboard'],
                ['title' => 'Jadwal Ramadhan', 'url' => 'admin/jadwal-ramadhan'],
                ['title' => 'Absensi', 'url' => '']
            ]
        ];

        return view('backend/jadwal_ramadhan/absensi', $data);
    }

    public function save_absensi_admin()
    {
        $absensiData = $this->request->getPost('absensi'); // array [id_jadwal => status_kehadiran]
        
        if (empty($absensiData) || !is_array($absensiData)) {
            return redirect()->back()->with('error', 'Tidak ada data absensi yang disimpan.');
        }

        $berhasilSimpan = 0;
        foreach ($absensiData as $idJadwal => $status) {
            // Kita skip jika status masih kosong (Belum diabsen)
            if (empty($status)) continue;

            // Cari detail jadwal untuk dimasukkan ke data absensi
            $jadwal = $this->db->table('tbl_jadwal_kegiatan j')
                               ->join('tbl_tema_ceramah t', 't.hari_ke = j.hari_ke AND t.tahun_hijriah = j.tahun_hijriah', 'left')
                               ->where('j.id', $idJadwal)
                               ->select('j.jenis_kegiatan, j.id_personil, t.tanggal')
                               ->get()->getRowArray();

            if (!$jadwal) continue;

            // Check if absensi already exists
            $existing = $this->db->table('tbl_absensi')->where('id_jadwal', $idJadwal)->get()->getRowArray();

            if ($existing) {
                // Update
                $this->db->table('tbl_absensi')
                    ->where('id_jadwal', $idJadwal)
                    ->update([
                        'status_kehadiran' => $status,
                        'waktu_absen' => date('Y-m-d H:i:s')
                    ]);
            } else {
                // Insert new record
                $this->db->table('tbl_absensi')->insert([
                    'id_jadwal' => $idJadwal,
                    'jenis_kegiatan' => $jadwal['jenis_kegiatan'],
                    'tanggal_kegiatan' => $jadwal['tanggal'],
                    'id_personil' => $jadwal['id_personil'],
                    'status_kehadiran' => $status,
                    'waktu_absen' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
                
            $berhasilSimpan++;
        }

        return redirect()->back()->with('success', "Berhasil menyimpan absensi untuk $berhasilSimpan penceramah.");
    }
}
