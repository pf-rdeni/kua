<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\KeuanganTransaksiModel;
use App\Models\KeuanganKasModel;
use App\Models\EntitasTypeModel;

/**
 * KeuanganController
 * Menampilkan dashboard keuangan dan laporan umum pemasukan/pengeluaran.
 * Akses dikontrol per entitas: SuperAdmin/Admin bisa lihat semua,
 * Operator entitas hanya melihat entitasnya.
 */
class KeuanganController extends BaseController
{
    protected $transaksiModel;
    protected $kasModel;
    protected $entitasTypeModel;

    public function __construct()
    {
        $this->transaksiModel   = new KeuanganTransaksiModel();
        $this->kasModel         = new KeuanganKasModel();
        $this->entitasTypeModel = new EntitasTypeModel();
    }

    /**
     * Ambil grup user saat ini dari Myth/Auth
     */
    private function getUserGroups(): array
    {
        if (!function_exists('user')) {
            helper('auth');
        }
        $currentUser = (function_exists('user') && logged_in()) ? user() : null;
        if (!$currentUser) {
            return [];
        }
        $groupModel = new \Myth\Auth\Models\GroupModel();
        $groups     = $groupModel->getGroupsForUser($currentUser->id);
        return array_column($groups, 'name');
    }

    /**
     * Ambil ID user saat ini
     */
    private function getCurrentUserId(): ?int
    {
        if (!function_exists('user')) {
            helper('auth');
        }
        $currentUser = (function_exists('user') && logged_in()) ? user() : null;
        return $currentUser ? $currentUser->id : null;
    }

    /**
     * Ambil daftar entitas yang bisa diakses user berdasarkan grupnya
     */
    private function getAccessibleEntitas(): array
    {
        $userGroups = $this->getUserGroups();
        return $this->entitasTypeModel->getAccessibleForUser($userGroups);
    }

    /**
     * Cek apakah user bisa akses entitas tertentu
     */
    private function canAccessEntitas(string $entitasType): bool
    {
        $accessible = $this->getAccessibleEntitas();
        foreach ($accessible as $et) {
            if ($et['kode'] === $entitasType) {
                return true;
            }
        }
        return false;
    }

    /**
     * Dashboard Keuangan
     * Menampilkan ringkasan saldo, total pemasukan/pengeluaran, dan grafik tren
     * untuk semua entitas yang dapat diakses user.
     */
    public function index()
    {
        $accessibleEntitas = $this->getAccessibleEntitas();
        $tahunSekarang     = (int)date('Y');

        // Rekap per entitas yang dapat diakses
        $rekapEntitas = [];
        foreach ($accessibleEntitas as $et) {
            $rekap = $this->transaksiModel->getRekap([
                'entitas_type' => $et['kode'],
                'tahun'        => $tahunSekarang,
            ]);
            $rekapEntitas[] = [
                'entitas'          => $et,
                'total_pemasukan'  => $rekap['total_pemasukan'],
                'total_pengeluaran' => $rekap['total_pengeluaran'],
                'saldo'             => $rekap['saldo'],
            ];
        }

        // Rekap total keseluruhan semua entitas yang bisa diakses
        $totalPemasukan   = array_sum(array_column($rekapEntitas, 'total_pemasukan'));
        $totalPengeluaran = array_sum(array_column($rekapEntitas, 'total_pengeluaran'));

        // Ambil 10 transaksi terakhir dari semua entitas yang bisa diakses
        $transaksiTerakhir = [];
        if (!empty($accessibleEntitas)) {
            $kodeEntitas = array_column($accessibleEntitas, 'kode');
            $transaksiTerakhir = $this->transaksiModel->getWithDetail([]);;
            // Filter manual agar sesuai entitas yang bisa diakses
            $transaksiTerakhir = array_filter($transaksiTerakhir, function ($t) use ($kodeEntitas) {
                return in_array($t['entitas_type'], $kodeEntitas);
            });
            $transaksiTerakhir = array_slice(array_values($transaksiTerakhir), 0, 10);
        }

        $data = [
            'title'             => 'Dashboard Keuangan',
            'accessibleEntitas' => $accessibleEntitas,
            'rekapEntitas'      => $rekapEntitas,
            'totalPemasukan'    => $totalPemasukan,
            'totalPengeluaran'  => $totalPengeluaran,
            'totalSaldo'        => $totalPemasukan - $totalPengeluaran,
            'transaksiTerakhir' => $transaksiTerakhir,
            'tahun'             => $tahunSekarang,
        ];

        return view('backend/keuangan/dashboard', $data);
    }

    /**
     * Laporan Keuangan Umum
     * Menampilkan laporan pemasukan/pengeluaran dengan filter entitas,
     * rentang tanggal, dan kategori.
     */
    public function laporan()
    {
        $accessibleEntitas = $this->getAccessibleEntitas();

        // Ambil filter dari GET request
        $filters = [
            'entitas_type'    => $this->request->getGet('entitas_type') ?? '',
            'jenis'           => $this->request->getGet('jenis') ?? '',
            'tanggal_dari'    => $this->request->getGet('tanggal_dari') ?? date('Y-m-01'), // default: awal bulan ini
            'tanggal_sampai'  => $this->request->getGet('tanggal_sampai') ?? date('Y-m-d'),
        ];

        // Jika entitas_type di-filter, pastikan user punya akses
        if (!empty($filters['entitas_type']) && !$this->canAccessEntitas($filters['entitas_type'])) {
            return redirect()->back()->with('error', 'Akses ditolak untuk entitas tersebut.');
        }

        // Jika user bukan admin, batasi entitas_type ke entitas yang bisa diakses
        if (empty($filters['entitas_type']) && !empty($accessibleEntitas)) {
            // Ambil transaksi dari semua entitas yang bisa diakses
            $semuaTransaksi = [];
            foreach ($accessibleEntitas as $et) {
                $f = $filters;
                $f['entitas_type'] = $et['kode'];
                $rows = $this->transaksiModel->getWithDetail($f);
                $semuaTransaksi = array_merge($semuaTransaksi, $rows);
            }
            // Urutkan berdasarkan tanggal desc
            usort($semuaTransaksi, fn($a, $b) => strtotime($b['tanggal_transaksi']) - strtotime($a['tanggal_transaksi']));
            $transaksiList = $semuaTransaksi;
        } else {
            $transaksiList = $this->transaksiModel->getWithDetail($filters);
        }

        // Hitung rekap total
        $totalPemasukan   = array_sum(array_map(fn($t) => $t['jenis'] === 'pemasukan' ? (float)$t['jumlah'] : 0, $transaksiList));
        $totalPengeluaran = array_sum(array_map(fn($t) => $t['jenis'] === 'pengeluaran' ? (float)$t['jumlah'] : 0, $transaksiList));

        $data = [
            'title'             => 'Laporan Keuangan',
            'accessibleEntitas' => $accessibleEntitas,
            'transaksiList'     => $transaksiList,
            'filters'           => $filters,
            'totalPemasukan'    => $totalPemasukan,
            'totalPengeluaran'  => $totalPengeluaran,
            'totalSaldo'        => $totalPemasukan - $totalPengeluaran,
        ];

        return view('backend/keuangan/laporan/index', $data);
    }
}
