<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;

class PublicJadwalController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function view($token)
    {
        if (empty($token)) {
            return view('errors/html/error_404', ['message' => 'Token tidak valid.']);
        }

        // Cari Mubaligh berdasarkan Token
        $mubaligh = $this->db->table('tbl_personil')
            ->where('token_jadwal', $token)
            ->where('status_aktif', 1)
            ->get()->getRowArray();

        if (!$mubaligh) {
            return view('errors/html/error_404', ['message' => 'Data Mubaligh tidak ditemukan atau token kadaluarsa.']);
        }

        // Ambil jadwal sebulan penuh untuk Mubaligh ini
        $jadwalPribadi = $this->db->table('tbl_jadwal_kegiatan j')
            ->select('
                j.id as id_jadwal, 
                j.hari_ke, 
                j.tahun_hijriah, 
                j.tanggal,
                j.jenis_kegiatan,
                j.peran_petugas,
                a.status_kehadiran,
                a.keterangan as keterangan_absensi,
                m.nama as nama_masjid, 
                m.alamat as alamat_masjid, 
                t.tema
            ')
            ->join('tbl_masjid_mushola m', 'm.id_masjid_mushola = j.id_masjid_mushola', 'left')
            ->join('tbl_tema_ceramah t', 't.hari_ke = j.hari_ke AND t.tahun_hijriah = j.tahun_hijriah', 'left')
            ->join('tbl_absensi a', 'a.id_jadwal = j.id', 'left')
            ->whereIn('j.jenis_kegiatan', ['ramadhan', 'maghrib_mengaji', 'jumat'])
            ->where('j.id_personil', $mubaligh['id'])
            ->orderBy('j.tanggal', 'ASC')
            ->get()->getResultArray();

        $data = [
            'mubaligh' => $mubaligh,
            'jadwal' => $jadwalPribadi,
            'token' => $token
        ];

        return view('frontend/public_jadwal/index', $data);
    }

    public function konfirmasi_hadir()
    {
        $idJadwal = $this->request->getPost('id_jadwal');
        $token = $this->request->getPost('token');

        // Validasi kepemilikan
        $mubaligh = $this->db->table('tbl_personil')->where('token_jadwal', $token)->get()->getRowArray();
        if (!$mubaligh) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Token tidak valid.']);
        }

        $jadwal = $this->db->table('tbl_jadwal_kegiatan')
                           ->where('id', $idJadwal)
                           ->where('id_personil', $mubaligh['id'])
                           ->get()->getRowArray();
                           
        if (!$jadwal) {
             return $this->response->setJSON(['status' => 'error', 'message' => 'Jadwal tidak ditemukan.']);
        }

        $this->absensi_upsert($jadwal, 'hadir', null, null);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Terima kasih, konfirmasi kehadiran berhasil dicatat.']);
    }

    public function ajukan_pengganti()
    {
        $idJadwal = $this->request->getPost('id_jadwal');
        $token = $this->request->getPost('token');
        $idPengganti = $this->request->getPost('id_pengganti');
        $alasan = $this->request->getPost('alasan');

        if (empty($idPengganti)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Pilih mubaligh pengganti terlebih dahulu.']);
        }

        // Validasi kepemilikan
        $mubaligh = $this->db->table('tbl_personil')->where('token_jadwal', $token)->get()->getRowArray();
        if (!$mubaligh) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Token tidak valid.']);
        }

        // Ambil data jadwal saat ini untuk validasi bentrok (anti double jadwal si pengganti)
        $jadwalAsli = $this->db->table('tbl_jadwal_kegiatan')
                           ->where('id', $idJadwal)
                           ->where('id_personil', $mubaligh['id'])
                           ->get()->getRowArray();
                           
        if (!$jadwalAsli) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Jadwal tidak ditemukan.']);
        }
        
        // Pengecekan Bentrok Pengganti
        $cekBentrok = $this->db->table('tbl_jadwal_kegiatan')
            ->where('hari_ke', $jadwalAsli['hari_ke'])
            ->where('tahun_hijriah', $jadwalAsli['tahun_hijriah'])
            ->where('id_personil', $idPengganti)
            ->where('jenis_kegiatan', 'ramadhan')
            ->countAllResults();

        if ($cekBentrok > 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Mubaligh pengganti yang dipilih sudah memiliki jadwal di hari yang sama. Silakan cari yang lain.']);
        }

        $this->absensi_upsert($jadwalAsli, 'diganti', $idPengganti, $alasan);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Delegasi jadwal berhasil diajukan.']);
    }
    
    private function absensi_upsert($jadwal, $status, $idPengganti, $alasan)
    {
        $existing = $this->db->table('tbl_absensi')->where('id_jadwal', $jadwal['id'])->get()->getRowArray();
        
        $data = [
            'status_kehadiran' => $status,
            'id_personil_pengganti' => $idPengganti,
            'keterangan' => $alasan,
            'waktu_absen' => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            $this->db->table('tbl_absensi')->where('id', $existing['id'])->update($data);
        } else {
            $data['id_jadwal'] = $jadwal['id'];
            $data['jenis_kegiatan'] = $jadwal['jenis_kegiatan'];
            $data['tanggal_kegiatan'] = $jadwal['tanggal'];
            $data['id_personil'] = $jadwal['id_personil'];
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('tbl_absensi')->insert($data);
        }
    }
}
