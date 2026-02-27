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
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil semua kategori aktif
     */
    public function getActive(): array
    {
        return $this->where('is_active', 1)
                    ->orderBy('nama_kategori', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil kategori berdasarkan jenis (pemasukan/pengeluaran/keduanya)
     */
    public function getByJenis(string $jenis): array
    {
        return $this->where('is_active', 1)
                    ->groupStart()
                        ->where('jenis', $jenis)
                        ->orWhere('jenis', 'keduanya')
                    ->groupEnd()
                    ->orderBy('nama_kategori', 'ASC')
                    ->findAll();
    }
}
