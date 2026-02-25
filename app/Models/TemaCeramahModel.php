<?php

namespace App\Models;

use CodeIgniter\Model;

class TemaCeramahModel extends Model
{
    protected $table            = 'tbl_tema_ceramah';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'jenis_kegiatan',
        'tahun_hijriah',
        'hari_ke',
        'tanggal',
        'tema'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
