<?php

namespace App\Models;

use CodeIgniter\Model;

class TpqMdtaModel extends Model
{
    protected $table            = 'tbl_tpq_mdta';
    protected $primaryKey       = 'id_tpq_mdta';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_masjid_mushola',
        'nama',
        'alamat',
        'provinsi', 
        'kabupaten_kota', 
        'kecamatan', 
        'kelurahan_desa', 
        'rt', 
        'rw',
        'hari',
        'waktu',
        'pimpinan',
        'no_hp_pimpinan',
        'jumlah_santri',
        'foto',
        'latitude',
        'longitude'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'id_masjid_mushola' => 'permit_empty|numeric',
        'nama'              => 'required|min_length[3]|max_length[100]',
        'alamat'            => 'permit_empty|max_length[255]',
        'hari'              => 'permit_empty|max_length[20]',
        'waktu'             => 'permit_empty',
        'pimpinan'          => 'permit_empty|max_length[100]',
        'no_hp_pimpinan'    => 'permit_empty|max_length[20]',
        'jumlah_santri'     => 'permit_empty|numeric',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get TPQ/MDTA with Masjid/Mushola name
     */
    public function getWithMasjid($id = null)
    {
        $this->select('tbl_tpq_mdta.*, tbl_masjid_mushola.nama as nama_masjid')
             ->join('tbl_masjid_mushola', 'tbl_masjid_mushola.id_masjid_mushola = tbl_tpq_mdta.id_masjid_mushola', 'left');

        if ($id) {
            return $this->find($id);
        }
        return $this;
    }
}
