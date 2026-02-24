<?php

namespace App\Models;

use CodeIgniter\Model;

class MajelisTaklimModel extends Model
{
    protected $table            = 'tbl_majelis_taklim';
    protected $primaryKey       = 'id_majelis_taklim';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_masjid_mushola', 'nama_majelis_taklim', 'alamat', 'provinsi', 'kabupaten_kota', 'kecamatan', 'kelurahan_desa', 'rt', 'rw', 'hari', 'waktu', 
        'pimpinan', 'no_hp_pimpinan', 'jumlah_jamaah', 'foto'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'id_masjid_mushola'   => 'permit_empty|numeric', // Optional link to masjid
        'nama_majelis_taklim' => 'required|min_length[3]|max_length[100]',
        'alamat'              => 'permit_empty|max_length[255]',
        'hari'                => 'permit_empty|max_length[20]',
        'waktu'               => 'permit_empty',
        'pimpinan'            => 'permit_empty|max_length[100]',
        'no_hp_pimpinan'      => 'permit_empty|max_length[20]',
        'jumlah_jamaah'       => 'permit_empty|numeric',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get Majelis Taklim with Masjid/Mushola name
     */
    public function getWithMasjid($id = null)
    {
        $this->select('tbl_majelis_taklim.*, tbl_masjid_mushola.nama as nama_masjid')
             ->join('tbl_masjid_mushola', 'tbl_masjid_mushola.id_masjid_mushola = tbl_majelis_taklim.id_masjid_mushola', 'left');

        if ($id) {
            return $this->find($id);
        }
        return $this;
    }
}
