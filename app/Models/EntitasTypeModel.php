<?php

namespace App\Models;

use CodeIgniter\Model;

class EntitasTypeModel extends Model
{
    protected $table            = 'tbl_entitas_type';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode', 'nama_label', 'icon', 'deskripsi', 'operator_group',
        'has_masjid_link', 'has_sk', 'urutan', 'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil semua entitas aktif, diurutkan
     */
    public function getActive()
    {
        return $this->where('is_active', 1)
                    ->orderBy('urutan', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil config berdasarkan kode
     */
    public function getByKode(string $kode)
    {
        return $this->where('kode', $kode)->first();
    }

    /**
     * Cek apakah user punya akses ke entitas tertentu berdasarkan grupnya
     */
    public function getAccessibleForUser(array $userGroups): array
    {
        $allEntitas = $this->getActive();
        $accessible = [];

        foreach ($allEntitas as $entitas) {
            // SuperAdmin dan Admin bisa akses semua
            if (in_array('SuperAdmin', $userGroups) || in_array('Admin', $userGroups)) {
                $accessible[] = $entitas;
                continue;
            }
            // Operator hanya bisa akses entitas sesuai grupnya
            if (!empty($entitas['operator_group']) && in_array($entitas['operator_group'], $userGroups)) {
                $accessible[] = $entitas;
            }
        }

        return $accessible;
    }
}
