<?php

namespace App\Models;

use CodeIgniter\Model;

class KeuanganKategoriHiddenModel extends Model
{
    protected $table            = 'tbl_keuangan_kategori_hidden';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_kategori',
        'entitas_type',
        'entitas_id',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Ambil array ID kategori yang disembunyikan oleh entitas tertentu
     */
    public function getHiddenCategoryIds(string $entitasType, ?int $entitasId = null): array
    {
        $this->select('id_kategori')
             ->where('entitas_type', $entitasType);
             
        if ($entitasId !== null) {
            $this->where('entitas_id', $entitasId);
        } else {
            $this->where('entitas_id IS NULL');
        }

        $results = $this->findAll();
        return array_column($results, 'id_kategori');
    }
}
