<?php

namespace App\Models;

use CodeIgniter\Model;

class PersonilModel extends Model
{
    protected $table            = 'tbl_personil';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'entitas_type',
        'id_masjid_mushola',
        'nama_lengkap',
        'nik',
        'nia',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'provinsi',
        'kabupaten_kota',
        'kecamatan',
        'kelurahan_desa',
        'rt',
        'rw',
        'no_hp',
        'pendidikan_terakhir',
        'pekerjaan',
        'status_aktif',
        'status',
        'sk_pengangkatan',
        'rekening_bank',
        'jenis_penerima_insentif',
        'foto',
        'latitude',
        'longitude',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // ============================================================
    // Scoped Queries
    // ============================================================

    /**
     * Query builder yang sudah di-filter berdasarkan entitas_type.
     * Mengembalikan $this agar bisa di-chain.
     */
    public function ofType(string $entitasType)
    {
        return $this->where('entitas_type', $entitasType);
    }

    /**
     * Ambil semua personil berdasarkan tipe entitas
     */
    public function getByEntitas(string $entitasType)
    {
        return $this->ofType($entitasType)->findAll();
    }

    /**
     * Ambil personil dengan join masjid/mushola
     */
    public function getWithMasjid(string $entitasType, $id = null)
    {
        $this->select('tbl_personil.*, tbl_masjid_mushola.nama as nama_masjid')
             ->join('tbl_masjid_mushola', 'tbl_masjid_mushola.id_masjid_mushola = tbl_personil.id_masjid_mushola', 'left')
             ->where('tbl_personil.entitas_type', $entitasType);

        if ($id) {
            return $this->find($id);
        }
        return $this;
    }

    /**
     * Search berdasarkan keyword, di-scope ke entitas_type
     */
    public function searchByType(string $entitasType, string $keyword)
    {
        return $this->ofType($entitasType)
                    ->groupStart()
                        ->like('nama_lengkap', $keyword)
                        ->orLike('nik', $keyword)
                        ->orLike('alamat', $keyword)
                        ->orLike('kelurahan_desa', $keyword)
                    ->groupEnd();
    }

    /**
     * Ambil personil yang aktif berdasarkan tipe
     */
    public function getAktifByType(string $entitasType)
    {
        return $this->ofType($entitasType)
                    ->where('status_aktif', 1)
                    ->findAll();
    }

    /**
     * Ambil nilai NIA terbesar berdasarkan entitas_type (di-cast ke integer).
     */
    public function getMaxNiaByEntitas(string $entitasType)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        $builder->select('MAX(CAST(nia AS UNSIGNED)) as max_nia', false);
        $builder->where('entitas_type', $entitasType);
        
        $query = $builder->get();
        $row = $query->getRow();
        
        return $row ? (int)$row->max_nia : 0;
    }
}
