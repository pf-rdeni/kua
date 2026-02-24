<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // Mengambil data titik lokasi (yang memiliki koordinat)
        $masjidLocations = $db->table('tbl_masjid_mushola')
            ->select('nama, jenis as tipe, alamat, foto, latitude, longitude, no_hp_ketua as no_hp')
            ->where('latitude !=', '')
            ->where('latitude IS NOT NULL', null, false)
            ->get()->getResultArray();

        $tpqLocations = $db->table('tbl_tpq_mdta')
            ->select('nama, "TPQ / MDTA" as tipe, alamat, foto, latitude, longitude, no_hp_pimpinan as no_hp')
            ->where('latitude !=', '')
            ->where('latitude IS NOT NULL', null, false)
            ->get()->getResultArray();

        $personilLocations = $db->table('tbl_personil p')
            ->select('p.nama_lengkap as nama, e.nama_label as tipe, p.alamat as alamat, p.foto, p.latitude, p.longitude, p.no_hp as no_hp')
            ->join('tbl_entitas_type e', 'p.entitas_type = e.kode', 'left')
            ->where('p.latitude !=', '')
            ->where('p.latitude IS NOT NULL', null, false)
            ->where('p.status_aktif', 1)
            ->get()->getResultArray();

        $allLocations = array_merge($masjidLocations, $tpqLocations, $personilLocations);

        // Statistik Jenis Kelamin per Entitas (hanya personil aktif)
        $statsGenderRaw = $db->table('tbl_personil p')
            ->select('e.nama_label as entitas, p.jenis_kelamin, COUNT(p.id) as total')
            ->join('tbl_entitas_type e', 'p.entitas_type = e.kode', 'left')
            ->where('p.status_aktif', 1)
            ->groupBy('p.entitas_type, p.jenis_kelamin')
            ->get()->getResultArray();

        // Format data Gender: [ 'Nama Entitas' => ['L' => x, 'P' => y, 'Total' => z] ]
        $statsGender = [];
        foreach ($statsGenderRaw as $row) {
            $entitas = $row['entitas'] ?? 'Lainnya';
            $jk = $row['jenis_kelamin'] === 'P' ? 'P' : 'L'; // Defaultkan L jika kosong/tidak valid
            
            if (!isset($statsGender[$entitas])) {
                $statsGender[$entitas] = ['L' => 0, 'P' => 0, 'Total' => 0];
            }
            $statsGender[$entitas][$jk] += $row['total'];
            $statsGender[$entitas]['Total'] += $row['total'];
        }

        // Statistik Kelurahan/Desa per Entitas (hanya personil aktif)
        $statsKelurahanRaw = $db->table('tbl_personil p')
            ->select('p.kelurahan_desa, e.nama_label as entitas, COUNT(p.id) as total')
            ->join('tbl_entitas_type e', 'p.entitas_type = e.kode', 'left')
            ->where('p.status_aktif', 1)
            ->where('p.kelurahan_desa !=', '')
            ->where('p.kelurahan_desa IS NOT NULL', null, false)
            ->groupBy('p.kelurahan_desa, p.entitas_type')
            ->orderBy('p.kelurahan_desa', 'ASC')
            ->get()->getResultArray();
            
        // Format data Kelurahan: [ 'Nama Kelurahan' => ['Entitas A' => x, 'Entitas B' => y, 'Total' => z] ]
        $statsKelurahan = [];
        $headerEntitasKelurahan = []; // Untuk menyimpan semua jenis entitas unik sebagai header tabel dinamis
        
        foreach ($statsKelurahanRaw as $row) {
            $kelurahan = $row['kelurahan_desa'];
            $entitas = $row['entitas'] ?? 'Lainnya';
            
            if (!in_array($entitas, $headerEntitasKelurahan)) {
                $headerEntitasKelurahan[] = $entitas;
            }
            
            if (!isset($statsKelurahan[$kelurahan])) {
                $statsKelurahan[$kelurahan] = ['Total' => 0];
            }
            
            if (!isset($statsKelurahan[$kelurahan][$entitas])) {
                $statsKelurahan[$kelurahan][$entitas] = 0;
            }
            
            $statsKelurahan[$kelurahan][$entitas] += $row['total'];
            $statsKelurahan[$kelurahan]['Total'] += $row['total'];
        }

        // --- TAMBAHAN: Gabungkan TPQ/MDTA ke Statistik Kelurahan ---
        $tpqKelurahanStats = $db->table('tbl_tpq_mdta')
            ->select('kelurahan_desa, COUNT(id_tpq_mdta) as total')
            ->where('kelurahan_desa !=', '')
            ->where('kelurahan_desa IS NOT NULL', null, false)
            ->groupBy('kelurahan_desa')
            ->get()->getResultArray();

        $entitasTpq = 'TPQ / MDTA';
        if (!empty($tpqKelurahanStats) && !in_array($entitasTpq, $headerEntitasKelurahan)) {
            $headerEntitasKelurahan[] = $entitasTpq;
        }

        foreach ($tpqKelurahanStats as $row) {
            $kelurahan = $row['kelurahan_desa'];
            if (!isset($statsKelurahan[$kelurahan])) {
                $statsKelurahan[$kelurahan] = ['Total' => 0];
            }
            if (!isset($statsKelurahan[$kelurahan][$entitasTpq])) {
                $statsKelurahan[$kelurahan][$entitasTpq] = 0;
            }
            $statsKelurahan[$kelurahan][$entitasTpq] += $row['total'];
            $statsKelurahan[$kelurahan]['Total'] += $row['total'];
        }

        // --- TAMBAHAN: Gabungkan Masjid dan Mushola ke Statistik Kelurahan ---
        $masjidKelurahanStats = $db->table('tbl_masjid_mushola')
            ->select('kelurahan_desa, jenis, COUNT(id_masjid_mushola) as total')
            ->where('kelurahan_desa !=', '')
            ->where('kelurahan_desa IS NOT NULL', null, false)
            ->groupBy('kelurahan_desa, jenis')
            ->get()->getResultArray();

        foreach ($masjidKelurahanStats as $row) {
            $kelurahan = $row['kelurahan_desa'];
            $jenis = $row['jenis'] ?? 'Masjid'; // Default ke Masjid jika kosong
            
            // Format header (Pastikan kapitalisasi sesuai)
            $entitasName = ($jenis === 'Mushola') ? 'Mushola' : 'Masjid';

            if (!in_array($entitasName, $headerEntitasKelurahan)) {
                $headerEntitasKelurahan[] = $entitasName;
            }

            if (!isset($statsKelurahan[$kelurahan])) {
                $statsKelurahan[$kelurahan] = []; 
            }
            if (!isset($statsKelurahan[$kelurahan][$entitasName])) {
                $statsKelurahan[$kelurahan][$entitasName] = 0;
            }
            $statsKelurahan[$kelurahan][$entitasName] += $row['total'];
        }

        // --- TAMBAHAN: Gabungkan Majelis Taklim ke Statistik Kelurahan ---
        $mtKelurahanStats = $db->table('tbl_majelis_taklim')
            ->select('kelurahan_desa, COUNT(id_majelis_taklim) as total')
            ->where('kelurahan_desa !=', '')
            ->where('kelurahan_desa IS NOT NULL', null, false)
            ->groupBy('kelurahan_desa')
            ->get()->getResultArray();

        $entitasMt = 'Majelis Taklim';
        if (!empty($mtKelurahanStats) && !in_array($entitasMt, $headerEntitasKelurahan)) {
            $headerEntitasKelurahan[] = $entitasMt;
        }

        foreach ($mtKelurahanStats as $row) {
            $kelurahan = $row['kelurahan_desa'];
            if (!isset($statsKelurahan[$kelurahan])) {
                $statsKelurahan[$kelurahan] = [];
            }
            if (!isset($statsKelurahan[$kelurahan][$entitasMt])) {
                $statsKelurahan[$kelurahan][$entitasMt] = 0;
            }
            $statsKelurahan[$kelurahan][$entitasMt] += $row['total'];
        }

        // Sort Header agar rapi (Opsional)
        sort($headerEntitasKelurahan);
        // Sort keys (nama kelurahan) 
        ksort($statsKelurahan);

        $data = [
            'title'              => 'Dashboard',
            'mapLocationsJson'   => base64_encode(json_encode($allLocations)),
            'totalMubaligh'      => $db->table('tbl_personil')->where('entitas_type', 'mubaligh')->where('status_aktif', 1)->countAllResults(),
            'totalMasjidMushola' => $db->table('tbl_masjid_mushola')->countAllResults(),
            'totalImamMasjid'    => $db->table('tbl_personil')->where('entitas_type', 'imam_masjid')->where('status_aktif', 1)->countAllResults(),
            'totalFarduKifayah'  => $db->table('tbl_personil')->where('entitas_type', 'fardu_kifayah')->countAllResults(),
            'totalPenggaliKubur' => $db->table('tbl_personil')->where('entitas_type', 'penggali_kubur')->countAllResults(),
            'totalMajelisTaklim' => $db->table('tbl_majelis_taklim')->countAllResults(),
            'totalTpqMdta'       => $db->table('tbl_tpq_mdta')->countAllResults(),
            'statsGender'        => $statsGender,
            'statsKelurahan'     => $statsKelurahan,
            'headerEntitasKelurahan' => $headerEntitasKelurahan,
        ];

        return view('backend/dashboard/index', $data);
    }
}
