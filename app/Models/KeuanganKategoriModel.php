<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * KeuanganKategoriModel
 * Model untuk kategori transaksi keuangan (Infaq, Sedekah, Operasional, dll.)
 */
class KeuanganKategoriModel extends Model
{
    protected $table            = 'tbl_keuangan_kategori';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_kategori',
        'jenis',
        'warna_badge',
        'deskripsi',
        'entitas_type',
        'entitas_id',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil semua kategori aktif untuk Admin (Tanpa peduli entitas)
     */
    public function getActive(): array
    {
        return $this->where('is_active', 1)
                    ->orderBy('nama_kategori', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil kategori khusus untuk suatu entitas
     * Menggabungkan Kategori Global (yang tidak di-hidden) + Kategori Milik Entitas
     */
    public function getActiveForEntitas(string $entitasType, ?int $entitasId = null): array
    {
        $hiddenModel = new KeuanganKategoriHiddenModel();
        $hiddenIds = $hiddenModel->getHiddenCategoryIds($entitasType, $entitasId);

        $builder = $this->where('is_active', 1)
                        ->groupStart()
                            // 1. Kategori Global (entitas_type IS NULL)
                            ->groupStart()
                                ->where('entitas_type IS NULL')
                            ->groupEnd()
                            // 2. ATAU Kategori Milik Entitas Ini
                            ->orGroupStart()
                                ->where('entitas_type', $entitasType);
                                if ($entitasId !== null) {
                                    $builder = $builder->where('entitas_id', $entitasId);
                                } else {
                                    $builder = $builder->where('entitas_id IS NULL');
                                }
                            $builder = $builder->groupEnd()
                        ->groupEnd();

        // Kecualikan yang di-hidden (berlaku untuk global)
        if (!empty($hiddenIds)) {
            $builder = $builder->whereNotIn('id', $hiddenIds);
        }

        return $builder->orderBy('nama_kategori', 'ASC')->findAll();
    }

    /**
     * Ambil kategori berdasarkan jenis (pemasukan/pengeluaran/keduanya)
     */
    public function getByJenis(string $jenis, ?string $entitasType = null, ?int $entitasId = null): array
    {
        // ... (can be reused if needed, but getActiveForEntitas will cover form mapping)
        return $this->where('is_active', 1)
                    ->groupStart()
                        ->where('jenis', $jenis)
                        ->orWhere('jenis', 'keduanya')
                    ->groupEnd()
                    ->orderBy('nama_kategori', 'ASC')
                    ->findAll();
    }
}
