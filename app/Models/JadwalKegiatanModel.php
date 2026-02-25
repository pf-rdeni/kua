<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalKegiatanModel extends Model
{
    protected $table            = 'tbl_jadwal_kegiatan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'jenis_kegiatan',
        'tahun_hijriah',
        'tahun_masehi',
        'bulan',
        'id_masjid_mushola',
        'id_personil',
        'peran_petugas',
        'tanggal',
        'hari_ke',
        'id_tema'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getMatriksRamadhan($tahunHijriah)
    {
        // Akan mengembalikan jadwal per masjid per hari_ke
        // Struktur: [ id_masjid ][ hari_ke ] => { data_jadwal_dan_penceramah }
        $builder = $this->db->table($this->table)
            ->select('tbl_jadwal_kegiatan.*, tbl_personil.nama_lengkap as nama_mubaligh, tbl_personil.foto as foto_mubaligh, tbl_personil.nik as nik_mubaligh')
            ->join('tbl_personil', 'tbl_personil.id = tbl_jadwal_kegiatan.id_personil', 'left')
            ->where('tbl_jadwal_kegiatan.jenis_kegiatan', 'ramadhan')
            ->where('tbl_jadwal_kegiatan.tahun_hijriah', $tahunHijriah);
            
        $results = $builder->get()->getResultArray();
        
        $matriks = [];
        foreach ($results as $row) {
            if ($row['id_masjid_mushola'] && $row['hari_ke']) {
                $matriks[$row['id_masjid_mushola']][$row['hari_ke']] = $row;
            }
        }
        
        return $matriks;
    }
}
