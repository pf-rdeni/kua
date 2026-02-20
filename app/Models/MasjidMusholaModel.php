<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidMusholaModel extends Model
{
    protected $table            = 'tbl_masjid_mushola';
    protected $primaryKey       = 'id_masjid_mushola';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama', 'jenis', 'alamat', 'tahun_berdiri', 'luas_bangunan', 
        'status_tanah', 'nama_ketua_dkm', 'no_hp_ketua', 'jumlah_jamaah', 
        'foto', 'latitude', 'longitude'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'nama'              => 'required|min_length[3]|max_length[100]',
        'jenis'             => 'required|in_list[Masjid,Mushola]',
        'alamat'            => 'permit_empty|max_length[255]',
        'tahun_berdiri'     => 'permit_empty|numeric|exact_length[4]',
        'luas_bangunan'     => 'permit_empty|numeric',
        'nama_ketua_dkm'    => 'permit_empty|max_length[100]',
        'foto'              => 'permit_empty|max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Search data based on keyword
     */
    public function search($keyword)
    {
        return $this->like('nama', $keyword)
                    ->orLike('alamat', $keyword)
                    ->orLike('nama_ketua_dkm', $keyword);
    }
}
