<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * KeuanganIuranAnggotaModel
 * Model untuk pencatatan pembayaran iuran per anggota (personil).
 * Mendukung status: lunas, sebagian, belum.
 */
class KeuanganIuranAnggotaModel extends Model
{
    protected $table            = 'tbl_keuangan_iuran_anggota';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_iuran_setting',
        'id_personil',
        'periode_bayar',
        'tanggal_bayar',
        'jumlah_bayar',
        'status',
        'keterangan',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil semua pembayaran iuran untuk setting tertentu,
     * dengan join ke tbl_personil untuk nama anggota
     */
    public function getBySettingWithPersonil(int $idIuranSetting): array
    {
        return $this->db->table('tbl_keuangan_iuran_anggota ia')
            ->select('ia.*, p.nama_lengkap, p.nia, p.nik, p.no_hp')
            ->join('tbl_personil p', 'p.id = ia.id_personil', 'left')
            ->where('ia.id_iuran_setting', $idIuranSetting)
            ->orderBy('ia.periode_bayar', 'DESC')
            ->orderBy('p.nama_lengkap', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Ambil pembayaran untuk satu anggota pada setting iuran tertentu
     */
    public function getByPersonilAndSetting(int $idPersonil, int $idIuranSetting): array
    {
        return $this->where('id_personil', $idPersonil)
                    ->where('id_iuran_setting', $idIuranSetting)
                    ->orderBy('periode_bayar', 'DESC')
                    ->findAll();
    }

    /**
     * Cek apakah satu personil sudah membayar iuran pada periode tertentu
     */
    public function sudahBayar(int $idPersonil, int $idIuranSetting, string $periodeBayar): ?array
    {
        return $this->where('id_personil', $idPersonil)
                    ->where('id_iuran_setting', $idIuranSetting)
                    ->where('periode_bayar', $periodeBayar)
                    ->first();
    }

    /**
     * Ambil laporan iuran lengkap per periode untuk satu setting iuran.
     * Menampilkan seluruh personil entitas + status bayar per periode.
     *
     * Return: ['personil' => [...], 'periodes' => [...], 'bayaranMap' => [...]]
     */
    public function getLaporanIuranPerPeriode(int $idIuranSetting, string $entitasType): array
    {
        // Ambil semua personil aktif dari entitas ini
        $personilList = $this->db->table('tbl_personil')
            ->where('entitas_type', $entitasType)
            ->where('status_aktif', 1)
            ->orderBy('nama_lengkap', 'ASC')
            ->get()->getResultArray();

        // Ambil semua data bayar untuk setting ini
        $bayaranRows = $this->where('id_iuran_setting', $idIuranSetting)->findAll();

        // Buat map: [id_personil][periode_bayar] => data bayar
        $bayaranMap = [];
        foreach ($bayaranRows as $bayar) {
            $bayaranMap[$bayar['id_personil']][$bayar['periode_bayar']] = $bayar;
        }

        return [
            'personil'   => $personilList,
            'bayaranMap' => $bayaranMap,
        ];
    }

    /**
     * Rekap iuran: jumlah sudah bayar vs belum bayar untuk satu setting + periode
     */
    public function getRekapPeriode(int $idIuranSetting, string $periodeBayar): array
    {
        $lunas    = $this->where('id_iuran_setting', $idIuranSetting)->where('periode_bayar', $periodeBayar)->where('status', 'lunas')->countAllResults();
        $sebagian = $this->where('id_iuran_setting', $idIuranSetting)->where('periode_bayar', $periodeBayar)->where('status', 'sebagian')->countAllResults();
        $total    = $this->where('id_iuran_setting', $idIuranSetting)->where('periode_bayar', $periodeBayar)->countAllResults();

        return [
            'lunas'    => $lunas,
            'sebagian' => $sebagian,
            'belum'    => $total - $lunas - $sebagian,
            'total'    => $total,
        ];
    }
}
