<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingBerkasModel extends Model
{
    protected $table            = 'tbl_setting_berkas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_berkas',
        'entitas_type',
        'aspect_ratio_width',
        'aspect_ratio_height',
        'is_rekening',
        'rekening_digit',
        'cetak_tipe',
        'cetak_lebar',
        'status_aktif'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get pengaturan berkas berdasarkan tipe entitas yang menggunakannya
     * Menggunakan FIND_IN_SET untuk mencari entitas dalam CSV
     */
    public function getSettingByEntitas($entitasType)
    {
        return $this->where("FIND_IN_SET('$entitasType', entitas_type) >", 0)
                    ->where('status_aktif', 1)
                    ->findAll();
    }
}
