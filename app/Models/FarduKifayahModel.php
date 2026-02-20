<?php

namespace App\Models;

use CodeIgniter\Model;

class FarduKifayahModel extends Model
{
    protected $table            = 'tbl_fardu_kifayah';
    protected $primaryKey       = 'id_fardu_kifayah';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_masjid_mushola', 'nama', 'no_hp', 'alamat', 'status', 'sk_pengangkatan', 'foto'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'id_masjid_mushola' => 'required|numeric',
        'nama'              => 'required|min_length[3]|max_length[100]',
        'status'            => 'required|max_length[50]',
        'no_hp'             => 'permit_empty|max_length[20]',
        'alamat'            => 'permit_empty|max_length[255]',
        'foto'              => 'permit_empty|max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
        'sk_pengangkatan'   => 'permit_empty|max_size[sk_pengangkatan,2048]|ext_in[sk_pengangkatan,pdf,jpg,jpeg,png]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get Petugas Fardu Kifayah with Masjid/Mushola name
     */
    public function getWithMasjid($id = null)
    {
        $this->select('tbl_fardu_kifayah.*, tbl_masjid_mushola.nama as nama_masjid')
             ->join('tbl_masjid_mushola', 'tbl_masjid_mushola.id_masjid_mushola = tbl_fardu_kifayah.id_masjid_mushola', 'left');

        if ($id) {
            return $this->find($id);
        }
        return $this;
    }
}
