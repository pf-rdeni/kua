<?php

namespace App\Models;

use CodeIgniter\Model;

class BerkasModel extends Model
{
    protected $table            = 'tbl_berkas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'entitas_type',
        'entitas_id',
        'nama_berkas',
        'nama_file',
        'status',
    ];

    // ============================================================
    // Generic Methods (menerima entitas_type sebagai parameter)
    // ============================================================

    /**
     * Ambil semua berkas untuk entitas tertentu
     */
    public function getBerkas(string $entitasType, int $entitasId): array
    {
        return $this->where('entitas_type', $entitasType)
            ->where('entitas_id', $entitasId)
            ->orderBy('nama_berkas', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Ambil hanya berkas aktif untuk entitas tertentu
     */
    public function getBerkasAktif(string $entitasType, int $entitasId): array
    {
        return $this->where('entitas_type', $entitasType)
            ->where('entitas_id', $entitasId)
            ->where('status', 1)
            ->orderBy('nama_berkas', 'ASC')
            ->findAll();
    }

    /**
     * Ambil berkas aktif berdasarkan entitas dan tipe berkas
     */
    public function getBerkasAktifByType(string $entitasType, int $entitasId, string $namaBerkas): ?array
    {
        return $this->where('entitas_type', $entitasType)
            ->where('entitas_id', $entitasId)
            ->where('nama_berkas', $namaBerkas)
            ->where('status', 1)
            ->first();
    }

    /**
     * Deactivate semua berkas dengan tipe tertentu
     */
    public function deactivateByType(string $entitasType, int $entitasId, string $namaBerkas): bool
    {
        return $this->where('entitas_type', $entitasType)
            ->where('entitas_id', $entitasId)
            ->where('nama_berkas', $namaBerkas)
            ->where('status', 1)
            ->set('status', 0)
            ->update();
    }
}
