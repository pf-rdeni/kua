<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * KeuanganKasModel
 * Model untuk kas/rekening per entitas.
 * Setiap masjid bisa memiliki kas sendiri menggunakan entitas_type + entitas_id.
 */
class KeuanganKasModel extends Model
{
    protected $table            = 'tbl_keuangan_kas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'entitas_type',
        'entitas_id',
        'nama_kas',
        'saldo_awal',
        'keterangan',
        'is_active',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil semua kas aktif berdasarkan entitas_type
     */
    public function getByEntitasType(string $entitasType): array
    {
        return $this->where('entitas_type', $entitasType)
                    ->where('is_active', 1)
                    ->orderBy('nama_kas', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil kas berdasarkan entitas_type dan entitas_id spesifik
     * Misal: kas Masjid Al-Falah (entitas_type=masjid_mushola, entitas_id=5)
     */
    public function getByEntitas(string $entitasType, ?int $entitasId = null): ?array
    {
        $builder = $this->where('entitas_type', $entitasType)->where('is_active', 1);

        if ($entitasId !== null) {
            $builder->where('entitas_id', $entitasId);
        } else {
            $builder->where('entitas_id IS NULL', null, false);
        }

        return $builder->first();
    }

    /**
     * Hitung saldo berjalan kas berdasarkan transaksi yang ada.
     * Saldo = saldo_awal + total_pemasukan - total_pengeluaran
     */
    public function hitungSaldo(int $idKas): float
    {
        $kas = $this->find($idKas);
        if (!$kas) {
            return 0.0;
        }

        $db = \Config\Database::connect();

        // Total pemasukan dari tabel transaksi
        $pemasukan = $db->table('tbl_keuangan_transaksi')
            ->selectSum('jumlah')
            ->where('id_kas', $idKas)
            ->where('jenis', 'pemasukan')
            ->get()->getRow()->jumlah ?? 0;

        // Total pengeluaran dari tabel transaksi
        $pengeluaran = $db->table('tbl_keuangan_transaksi')
            ->selectSum('jumlah')
            ->where('id_kas', $idKas)
            ->where('jenis', 'pengeluaran')
            ->get()->getRow()->jumlah ?? 0;

        return (float)$kas['saldo_awal'] + (float)$pemasukan - (float)$pengeluaran;
    }

    /**
     * Ambil semua kas dengan join ke masjid/mushola (untuk tampilan nama masjid)
     */
    public function getAllWithMasjid(): array
    {
        return $this->db->table('tbl_keuangan_kas k')
            ->select('k.*, m.nama as nama_masjid')
            ->join('tbl_masjid_mushola m', 'k.entitas_id = m.id_masjid_mushola AND k.entitas_type = \'masjid_mushola\'', 'left')
            ->where('k.is_active', 1)
            ->orderBy('k.entitas_type', 'ASC')
            ->orderBy('k.nama_kas', 'ASC')
            ->get()->getResultArray();
    }
}
