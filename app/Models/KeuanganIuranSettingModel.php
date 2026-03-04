<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * KeuanganIuranSettingModel
 * Model untuk konfigurasi jenis iuran per entitas.
 * Iuran dapat bersifat: harian, mingguan, bulanan, tahunan, atau sekali.
 */
class KeuanganIuranSettingModel extends Model
{
    protected $table            = 'tbl_keuangan_iuran_setting';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'entitas_type',
        'entitas_id',
        'nama_iuran',
        'periode',
        'nominal',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'is_active',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil semua setting iuran aktif berdasarkan entitas_type
     */
    public function getByEntitasType(string $entitasType, ?int $entitasId = null): array
    {
        $builder = $this->where('entitas_type', $entitasType)
                        ->where('is_active', 1)
                        ->orderBy('nama_iuran', 'ASC');
        
        if ($entitasId !== null) {
            $builder->groupStart()
                    ->where('entitas_id', $entitasId)
                    ->orWhere('entitas_id IS NULL')
                    ->groupEnd();
        } else {
            // Jika admin global (null), tetap lihat semua
        }

        return $builder->findAll();
    }

    /**
     * Ambil semua setting iuran (aktif maupun tidak) untuk halaman management
     */
    public function getAllByEntitasType(string $entitasType, ?int $entitasId = null): array
    {
        $builder = $this->where('entitas_type', $entitasType)
                        ->orderBy('is_active', 'DESC')
                        ->orderBy('nama_iuran', 'ASC');

        if ($entitasId !== null) {
            $builder->groupStart()
                    ->where('entitas_id', $entitasId)
                    ->orWhere('entitas_id IS NULL')
                    ->groupEnd();
        }

        return $builder->findAll();
    }

    /**
     * Hasilkan daftar periode yang perlu dibayar berdasarkan setting iuran.
     * Digunakan untuk menampilkan kolom periode pada tabel iuran anggota.
     */
    public function generatePeriode(array $setting): array
    {
        $periodes = [];
        $mulai    = new \DateTime($setting['tanggal_mulai']);
        $selesai  = $setting['tanggal_selesai']
                    ? new \DateTime($setting['tanggal_selesai'])
                    : new \DateTime(); // default: sampai hari ini

        switch ($setting['periode']) {
            case 'harian':
                $interval = new \DateInterval('P1D');
                break;
            case 'mingguan':
                $interval = new \DateInterval('P1W');
                break;
            case 'bulanan':
                $interval = new \DateInterval('P1M');
                break;
            case 'tahunan':
                $interval = new \DateInterval('P1Y');
                break;
            case 'sekali':
                // Iuran sekali hanya menghasilkan satu periode
                return [date('Y-m-d', strtotime($setting['tanggal_mulai']))];
            default:
                return [];
        }

        $current = clone $mulai;
        while ($current <= $selesai) {
            switch ($setting['periode']) {
                case 'bulanan':
                    $periodes[] = $current->format('Y-m');
                    break;
                case 'tahunan':
                    $periodes[] = $current->format('Y');
                    break;
                default:
                    $periodes[] = $current->format('Y-m-d');
            }
            $current->add($interval);
        }

        return $periodes;
    }
}
