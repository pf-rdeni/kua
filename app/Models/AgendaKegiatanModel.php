<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel tbl_agenda_kegiatan (sebelumnya tbl_agenda_masjid)
 * Mendukung multi-entitas: masjid_mushola, majelis_taklim, dll
 */
class AgendaKegiatanModel extends Model
{
    protected $table            = 'tbl_agenda_kegiatan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'entitas_type', 'entitas_id', 'tanggal', 'waktu_mulai', 'waktu_selesai',
        'judul_kegiatan', 'jenis', 'deskripsi', 'nama_penceramah',
        'id_personil', 'lokasi', 'is_published', 'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'judul_kegiatan' => 'required|min_length[3]|max_length[200]',
        'tanggal'        => 'required|valid_date[Y-m-d]',
        'jenis'          => 'required|in_list[ceramah,ta_lim,sosial,buka_bersama,tadarus,sahur,lainnya]',
    ];

    /**
     * Ambil agenda dengan join data personil
     */
    public function getAgendaDenganDetail(string $entitasType, int $entitasId, array $filters = []): array
    {
        $builder = $this->db->table('tbl_agenda_kegiatan a')
            ->select('a.*, p.nama_lengkap as nama_mubaligh_db, p.foto as foto_personil, p.no_hp as hp_personil')
            ->join('tbl_personil p', 'p.id = a.id_personil', 'left')
            ->where('a.entitas_type', $entitasType)
            ->where('a.entitas_id', $entitasId);

        if (!empty($filters['bulan'])) {
            $builder->where('MONTH(a.tanggal)', $filters['bulan']);
        }
        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(a.tanggal)', $filters['tahun']);
        }
        if (!empty($filters['jenis'])) {
            $builder->where('a.jenis', $filters['jenis']);
        }

        return $builder->orderBy('a.tanggal', 'ASC')
                       ->orderBy('a.waktu_mulai', 'ASC')
                       ->get()->getResultArray();
    }

    /**
     * Ambil jadwal mubaligh (dari admin KUA) untuk masjid ini
     */
    public function getJadwalMubalighUntukMasjid(int $idMasjid, ?string $tahunHijriah = null): array
    {
        $builder = $this->db->table('tbl_jadwal_kegiatan j')
            ->select('j.hari_ke, j.tanggal, t.tema, p.nama_lengkap as nama_mubaligh, p.no_hp, p.foto,
                      j.jenis_kegiatan, j.peran_petugas, j.tahun_hijriah')
            ->join('tbl_personil p', 'p.id = j.id_personil', 'left')
            ->join('tbl_tema_ceramah t', 't.hari_ke = j.hari_ke AND t.tahun_hijriah = j.tahun_hijriah', 'left')
            ->where('j.id_masjid_mushola', $idMasjid)
            ->where('j.id_personil IS NOT NULL', null, false);

        if ($tahunHijriah) {
            $builder->where('j.tahun_hijriah', $tahunHijriah);
        }

        return $builder->orderBy('j.tanggal', 'ASC')
                       ->orderBy('j.hari_ke', 'ASC')
                       ->get()->getResultArray();
    }

    /**
     * Ambil agenda mendatang dalam N hari ke depan (untuk reminder dashboard)
     */
    public function getAgendaMendatang(string $entitasType, int $entitasId, int $hariKedepan = 7): array
    {
        $tglAwal  = date('Y-m-d');
        $tglAkhir = date('Y-m-d', strtotime("+{$hariKedepan} days"));

        return $this->db->table('tbl_agenda_kegiatan a')
            ->select('a.*, p.nama_lengkap as nama_mubaligh_db')
            ->join('tbl_personil p', 'p.id = a.id_personil', 'left')
            ->where('a.entitas_type', $entitasType)
            ->where('a.entitas_id', $entitasId)
            ->where('a.tanggal >=', $tglAwal)
            ->where('a.tanggal <=', $tglAkhir)
            ->where('a.is_published', 1)
            ->orderBy('a.tanggal', 'ASC')
            ->orderBy('a.waktu_mulai', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Ambil range tahun yang tersedia (untuk filter dropdown)
     */
    public function getTahunTersedia(string $entitasType, int $entitasId): array
    {
        $rows = $this->db->table('tbl_agenda_kegiatan')
            ->select('YEAR(tanggal) as tahun')
            ->where('entitas_type', $entitasType)
            ->where('entitas_id', $entitasId)
            ->groupBy('YEAR(tanggal)')
            ->orderBy('tahun', 'DESC')
            ->get()->getResultArray();

        $tahunList = array_column($rows, 'tahun');
        if (!in_array(date('Y'), $tahunList)) $tahunList[] = date('Y');
        if (!in_array(date('Y') + 1, $tahunList)) $tahunList[] = date('Y') + 1;
        rsort($tahunList);
        return $tahunList;
    }
}
