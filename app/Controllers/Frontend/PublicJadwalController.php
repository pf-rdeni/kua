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

        // Ambil jadwal sebulan penuh untuk Mubaligh ini (sebagai utama maupun pengganti)
        $jadwalPribadi = $this->db->table('tbl_jadwal_kegiatan j')
            ->select('
                j.id as id_jadwal, 
                j.hari_ke, 
                j.tahun_hijriah, 
                j.tanggal,
                j.jenis_kegiatan,
                j.peran_petugas,
                j.id_personil as id_personil_asli,
                p_asli.nama_lengkap as nama_asli,
                p_asli.nia as nia_asli,
                a.status_kehadiran,
                a.keterangan as keterangan_absensi,
                a.id_personil_pengganti,
                p_pengganti.nama_lengkap as nama_pengganti,
                p_pengganti.nia as nia_pengganti,
                m.nama as nama_masjid, 
                m.alamat as alamat_masjid, 
                t.tema
            ')
            ->join('tbl_masjid_mushola m', 'm.id_masjid_mushola = j.id_masjid_mushola', 'left')
            ->join('tbl_tema_ceramah t', 't.hari_ke = j.hari_ke AND t.tahun_hijriah = j.tahun_hijriah', 'left')
            ->join('tbl_personil p_asli', 'p_asli.id = j.id_personil', 'left')
            ->join('tbl_absensi a', 'a.id_jadwal = j.id', 'left')
            ->join('tbl_personil p_pengganti', 'p_pengganti.id = a.id_personil_pengganti', 'left')
            ->whereIn('j.jenis_kegiatan', ['ramadhan', 'maghrib_mengaji', 'jumat'])
            ->groupStart()
                ->where('j.id_personil', $mubaligh['id'])
                ->orWhere('a.id_personil_pengganti', $mubaligh['id'])
            ->groupEnd()
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

        $jadwal = $this->db->table('tbl_jadwal_kegiatan j')
                           ->join('tbl_absensi a', 'a.id_jadwal = j.id', 'left')
                           ->where('j.id', $idJadwal)
                           ->groupStart()
                               ->where('j.id_personil', $mubaligh['id'])
                               ->orWhere('a.id_personil_pengganti', $mubaligh['id'])
                           ->groupEnd()
                           ->get()->getRowArray();
                           
        if (!$jadwal) {
             return $this->response->setJSON(['status' => 'error', 'message' => 'Jadwal tidak ditemukan.']);
        }

        $this->absensi_upsert($jadwal, 'hadir', null, null);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Terima kasih, konfirmasi kehadiran berhasil dicatat.']);
    }

    public function search_pengganti()
    {
        $token = $this->request->getGet('token');
        $keyword = $this->request->getGet('q');

        $mubaligh = $this->db->table('tbl_personil')
            ->where('token_jadwal', $token)
            ->where('status_aktif', 1)
            ->get()->getRowArray();

        $builder = $this->db->table('tbl_personil')
            ->where('status_aktif', 1)
            ->where('entitas_type', 'mubaligh');

        if ($mubaligh) {
            $builder->where('id !=', $mubaligh['id']);
        }

        if ($keyword) {
            $builder->groupStart()
                    ->like('nama_lengkap', $keyword)
                    ->orLike('nia', $keyword)
                    ->groupEnd();
        }

        $mubalighs = $builder->get()->getResultArray();

        $results = [];
        foreach ($mubalighs as $m) {
            $displayName = (!empty($m['nia']) ? $m['nia'] . ' - ' : '') . $m['nama_lengkap'];
            $results[] = [
                'id'   => $m['id'],
                'text' => $displayName,
                'nama' => $displayName,
                'foto' => $m['foto'] ? base_url('uploads/personil/' . $m['foto']) : base_url('template/backend/dist/img/default-150x150.png')
            ];
        }

        return $this->response->setJSON([
            'results' => $results
        ]);
    }

    public function ajukan_pengganti()
    {
        $idJadwal = $this->request->getPost('id_jadwal');
        $token = $this->request->getPost('token');
        $idPengganti = $this->request->getPost('id_pengganti');
        $alasan = $this->request->getPost('alasan');

        if (empty($idPengganti)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Pilih mubaligh/khotib pengganti terlebih dahulu.']);
        }

        // Validasi kepemilikan
        $mubaligh = $this->db->table('tbl_personil')->where('token_jadwal', $token)->get()->getRowArray();
        if (!$mubaligh) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Token tidak valid.']);
        }

        // Ambil data jadwal saat ini untuk validasi bentrok
        $jadwalAsli = $this->db->table('tbl_jadwal_kegiatan')
                           ->where('id', $idJadwal)
                           ->where('id_personil', $mubaligh['id'])
                           ->get()->getRowArray();
                           
        if (!$jadwalAsli) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Jadwal tidak ditemukan.']);
        }
        
        // Pengecekan Bentrok Pengganti secara fleksibel per jenis kegiatan & tanggal/hari
        $builderBentrok = $this->db->table('tbl_jadwal_kegiatan')
            ->where('id_personil', $idPengganti)
            ->where('jenis_kegiatan', $jadwalAsli['jenis_kegiatan']);

        if (!empty($jadwalAsli['tanggal'])) {
            $builderBentrok->where('tanggal', $jadwalAsli['tanggal']);
        } else if (!empty($jadwalAsli['hari_ke'])) {
            $builderBentrok->where('hari_ke', $jadwalAsli['hari_ke'])
                           ->where('tahun_hijriah', $jadwalAsli['tahun_hijriah']);
        }

        $cekBentrok = $builderBentrok->countAllResults();

        if ($cekBentrok > 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Mubaligh/Khotib pengganti yang dipilih sudah memiliki jadwal pada waktu tersebut. Silakan cari yang lain.']);
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
