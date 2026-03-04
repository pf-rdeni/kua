<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * KeuanganTransaksiModel
 * Model untuk semua transaksi pemasukan dan pengeluaran.
 * Mendukung multi-entitas: masjid_mushola, mubaligh, majelis_taklim, dll.
 */
class KeuanganTransaksiModel extends Model
{
    protected $table            = 'tbl_keuangan_transaksi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_kas',
        'entitas_type',
        'entitas_id',
        'id_kategori',
        'jenis',
        'jumlah',
        'keterangan',
        'tanggal_transaksi',
        'bukti',
        'no_referensi',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil transaksi dengan join kategori dan kas (dan masjid jika ada).
     * Mendukung filter: entitas_type, entitas_id, jenis, bulan, tahun
     */
    public function getWithDetail(array $filters = []): array
    {
        $builder = $this->db->table('tbl_keuangan_transaksi t')
            ->select('t.*, k.nama_kategori, k.warna_badge, ks.nama_kas, u.username as nama_input, m.nama as nama_masjid, mt.nama_majelis_taklim')
            ->join('tbl_keuangan_kategori k', 'k.id = t.id_kategori', 'left')
            ->join('tbl_keuangan_kas ks', 'ks.id = t.id_kas', 'left')
            ->join('users u', 'u.id = t.created_by', 'left')
            ->join('tbl_masjid_mushola m', 'm.id_masjid_mushola = t.entitas_id AND t.entitas_type = "masjid_mushola"', 'left')
            ->join('tbl_majelis_taklim mt', 'mt.id_majelis_taklim = t.entitas_id AND t.entitas_type = "majelis_taklim"', 'left');

        // Filter berdasarkan entitas_type
        if (!empty($filters['entitas_type'])) {
            $builder->where('t.entitas_type', $filters['entitas_type']);
        }
        // Filter berdasarkan entitas_id (misal ID masjid)
        if (isset($filters['entitas_id']) && $filters['entitas_id'] !== '') {
            $builder->where('t.entitas_id', $filters['entitas_id']);
        }
        // Filter berdasarkan jenis (pemasukan/pengeluaran)
        if (!empty($filters['jenis'])) {
            $builder->where('t.jenis', $filters['jenis']);
        }
        // Filter berdasarkan bulan
        if (!empty($filters['bulan'])) {
            $builder->where('MONTH(t.tanggal_transaksi)', $filters['bulan']);
        }
        // Filter berdasarkan tahun
        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(t.tanggal_transaksi)', $filters['tahun']);
        }
        // Filter berdasarkan kategori
        if (!empty($filters['id_kategori'])) {
            $builder->where('t.id_kategori', $filters['id_kategori']);
        }
        // Filter rentang tanggal
        if (!empty($filters['tanggal_dari'])) {
            $builder->where('t.tanggal_transaksi >=', $filters['tanggal_dari']);
        }
        if (!empty($filters['tanggal_sampai'])) {
            $builder->where('t.tanggal_transaksi <=', $filters['tanggal_sampai']);
        }

        return $builder->orderBy('t.tanggal_transaksi', 'DESC')
                       ->orderBy('t.id', 'DESC')
                       ->get()->getResultArray();
    }

    /**
     * Hitung total pemasukan dan pengeluaran berdasarkan filter entitas
     */
    public function getRekap(array $filters = []): array
    {
        $builder = $this->db->table('tbl_keuangan_transaksi t');

        if (!empty($filters['entitas_type'])) {
            $builder->where('t.entitas_type', $filters['entitas_type']);
        }
        if (isset($filters['entitas_id']) && $filters['entitas_id'] !== '') {
            $builder->where('t.entitas_id', $filters['entitas_id']);
        }
        if (!empty($filters['bulan'])) {
            $builder->where('MONTH(t.tanggal_transaksi)', $filters['bulan']);
        }
        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(t.tanggal_transaksi)', $filters['tahun']);
        }
        if (!empty($filters['tanggal_dari'])) {
            $builder->where('t.tanggal_transaksi >=', $filters['tanggal_dari']);
        }
        if (!empty($filters['tanggal_sampai'])) {
            $builder->where('t.tanggal_transaksi <=', $filters['tanggal_sampai']);
        }

        // Hitung total pemasukan
        $totalPemasukan  = (clone $builder)->where('t.jenis', 'pemasukan')->selectSum('jumlah')->get()->getRow()->jumlah ?? 0;
        // Hitung total pengeluaran
        $totalPengeluaran = (clone $builder)->where('t.jenis', 'pengeluaran')->selectSum('jumlah')->get()->getRow()->jumlah ?? 0;

        return [
            'total_pemasukan'  => (float)$totalPemasukan,
            'total_pengeluaran' => (float)$totalPengeluaran,
            'saldo'             => (float)$totalPemasukan - (float)$totalPengeluaran,
        ];
    }

    /**
     * Ambil data tren bulanan (untuk grafik Chart.js)
     * Return: array dengan label bulan dan data pemasukan/pengeluaran
     */
    public function getTrenBulanan(string $entitasType, ?int $entitasId = null, int $tahun = 0): array
    {
        if ($tahun === 0) {
            $tahun = (int)date('Y');
        }

        $builder = $this->db->table('tbl_keuangan_transaksi')
            ->select('MONTH(tanggal_transaksi) as bulan, jenis, SUM(jumlah) as total')
            ->where('entitas_type', $entitasType)
            ->where('YEAR(tanggal_transaksi)', $tahun)
            ->groupBy(['MONTH(tanggal_transaksi)', 'jenis'])
            ->orderBy('bulan', 'ASC');

        if ($entitasId !== null) {
            $builder->where('entitas_id', $entitasId);
        }

        $rows = $builder->get()->getResultArray();

        // Inisialisasi array 12 bulan dengan nilai 0
        $pemasukan   = array_fill(1, 12, 0);
        $pengeluaran = array_fill(1, 12, 0);

        foreach ($rows as $row) {
            if ($row['jenis'] === 'pemasukan') {
                $pemasukan[(int)$row['bulan']] = (float)$row['total'];
            } else {
                $pengeluaran[(int)$row['bulan']] = (float)$row['total'];
            }
        }

        return [
            'pemasukan'   => array_values($pemasukan),
            'pengeluaran' => array_values($pengeluaran),
        ];
    }

    /**
     * Hitung rekap berdasarkan kategori untuk chart (contoh Bar Chart)
     */
    public function getRekapKategori(string $entitasType, ?int $entitasId = null, int $tahun = 0, int $bulan = 0): array
    {
        $builder = $this->db->table('tbl_keuangan_transaksi t')
            ->select('k.nama_kategori, t.jenis, SUM(t.jumlah) as total')
            ->join('tbl_keuangan_kategori k', 'k.id = t.id_kategori', 'left')
            ->where('t.entitas_type', $entitasType);

        if ($entitasId !== null) {
            $builder->where('t.entitas_id', $entitasId);
        }

        if ($tahun > 0) {
            $builder->where('YEAR(t.tanggal_transaksi)', $tahun);
        }
        if ($bulan > 0) {
            $builder->where('MONTH(t.tanggal_transaksi)', $bulan);
        }

        // Only include those that have category
        $builder->where('t.id_kategori IS NOT NULL')
                ->groupBy(['t.id_kategori', 'k.nama_kategori', 't.jenis']);

        return $builder->get()->getResultArray();
    }
}
