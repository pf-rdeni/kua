<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\AgendaKegiatanModel;

class DashboardController extends BaseController
{
    public function index()
    {
        // Routing dashboard berdasarkan role
        if (in_groups('OperatorMasjidMushola') && !in_groups('SuperAdmin') && !in_groups('Admin')) {
            return $this->dashboardMasjid();
        }
        if (in_groups('OperatorMajelisTaklim') && !in_groups('SuperAdmin') && !in_groups('Admin')) {
            return $this->dashboardMajelisTaklim();
        }

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

        $jadwalRamadhan = $db->table('tbl_jadwal_kegiatan j')
            ->select('j.hari_ke, j.tahun_hijriah, m.nama as nama_masjid, m.alamat as alamat_masjid, p.id as id_mubaligh, p.nama_lengkap as nama_mubaligh, p.no_hp, p.foto, p.token_jadwal, t.tema, t.tanggal')
            ->join('tbl_masjid_mushola m', 'm.id_masjid_mushola = j.id_masjid_mushola', 'left')
            ->join('tbl_personil p', 'p.id = j.id_personil', 'left')
            ->join('tbl_tema_ceramah t', 't.hari_ke = j.hari_ke AND t.tahun_hijriah = j.tahun_hijriah', 'left')
            ->where('j.jenis_kegiatan', 'ramadhan')
            ->where('j.id_personil IS NOT NULL')
            ->whereIn('t.tanggal', [date('Y-m-d'), date('Y-m-d', strtotime('+1 day'))])
            ->orderBy('t.tanggal', 'ASC')
            ->get()->getResultArray();

        $jadwalMaghribMengaji = $db->table('tbl_jadwal_kegiatan j')
            ->select('j.peran_petugas, j.tanggal, m.nama as nama_masjid, m.alamat as alamat_masjid, p.id as id_mubaligh, p.nama_lengkap as nama_mubaligh, p.no_hp, p.foto, p.token_jadwal')
            ->join('tbl_masjid_mushola m', 'm.id_masjid_mushola = j.id_masjid_mushola', 'left')
            ->join('tbl_personil p', 'p.id = j.id_personil', 'left')
            ->where('j.jenis_kegiatan', 'maghrib_mengaji')
            ->where('j.id_personil IS NOT NULL')
            ->whereIn('j.tanggal', [date('Y-m-d'), date('Y-m-d', strtotime('+1 day'))])
            ->get()->getResultArray();

        $jadwalKhotibJumat = $db->table('tbl_jadwal_kegiatan j')
            ->select('j.peran_petugas, j.tanggal, m.nama as nama_masjid, m.alamat as alamat_masjid, p.id as id_mubaligh, p.nama_lengkap as nama_mubaligh, p.no_hp, p.foto, p.token_jadwal')
            ->join('tbl_masjid_mushola m', 'm.id_masjid_mushola = j.id_masjid_mushola', 'left')
            ->join('tbl_personil p', 'p.id = j.id_personil', 'left')
            ->where('j.jenis_kegiatan', 'jumat')
            ->where('j.id_personil IS NOT NULL')
            ->where('j.tanggal >=', date('Y-m-d', strtotime('-7 days')))
            ->where('j.tanggal <=', date('Y-m-d', strtotime('+21 days')))
            ->orderBy('j.tanggal', 'ASC')
            ->get()->getResultArray();

        // Generate tokens for each list
        $this->ensureTokens($jadwalRamadhan, $db);
        $this->ensureTokens($jadwalMaghribMengaji, $db);
        $this->ensureTokens($jadwalKhotibJumat, $db);

        // Urutkan ulang berdasarkan tanggal ASC
        usort($jadwalMaghribMengaji, function($a, $b) {
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });
        usort($jadwalKhotibJumat, function($a, $b) {
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });

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
            'jadwalRamadhan'     => $jadwalRamadhan,
            'jadwalMaghribMengaji' => $jadwalMaghribMengaji,
            'jadwalKhotibJumat'  => $jadwalKhotibJumat,
        ];

        return view('backend/dashboard/index', $data);
    }

    private function ensureTokens(&$list, $db)
    {
        foreach ($list as &$row) {
            if (empty($row['token_jadwal']) && !empty($row['id_mubaligh'])) {
                $newToken = bin2hex(random_bytes(4)); 
                $db->table('tbl_personil')
                   ->where('id', $row['id_mubaligh'])
                   ->update(['token_jadwal' => $newToken]);
                
                $row['token_jadwal'] = $newToken;
            }
        }
    }

    /**
     * Dashboard khusus untuk OperatorMasjidMushola
     * Menampilkan data yang relevan: profil masjid, display aktif, saldo kas, jadwal
     */
    private function dashboardMasjid()
    {
        $currentUser = user();
        $db          = \Config\Database::connect();

        // Jika operator belum punya entitas terhubung, tampilkan pesan setup
        if (empty($currentUser->entitas_id) || $currentUser->entitas_type !== 'masjid_mushola') {
            return view('backend/dashboard/operator_masjid', [
                'title'    => 'Dashboard',
                'masjid'   => null,
                'displays' => [],
                'saldoKas' => 0,
                'jmlKonten'     => 0,
                'jadwalMaghrib' => [],
                'jadwalJumat'   => [],
                'belumSetup'    => true,
            ]);
        }

        $idMasjid = $currentUser->entitas_id;

        // 1. Profil Masjid
        $masjid = $db->table('tbl_masjid_mushola')
                     ->where('id_masjid_mushola', $idMasjid)
                     ->get()->getRowArray();

        // 2. Daftar Display aktif milik masjid ini
        $displays = $db->table('tbl_display_setting')
                       ->where('id_masjid_mushola', $idMasjid)
                       ->get()->getResultArray();

        // 3. Jumlah konten display aktif
        $displayIds = array_column($displays, 'id');
        $jmlKonten  = 0;
        if (!empty($displayIds)) {
            $jmlKonten = $db->table('tbl_display_konten')
                            ->whereIn('id_display_setting', $displayIds)
                            ->where('aktif', 1)
                            ->countAllResults();
        }

        // 4. Saldo kas bulan ini (pemasukan - pengeluaran)
        $bulanIni  = date('Y-m');
        $saldoKas  = 0;
        $kasExists = $db->tableExists('tbl_keuangan_transaksi');
        if ($kasExists) {
            $pemasukan = $db->table('tbl_keuangan_transaksi')
                            ->selectSum('jumlah')
                            ->where('entitas_type', 'masjid_mushola')
                            ->where('entitas_id', $idMasjid)
                            ->where('jenis', 'pemasukan')
                            ->like('tanggal_transaksi', $bulanIni, 'after')
                            ->get()->getRowArray()['jumlah'] ?? 0;

            $pengeluaran = $db->table('tbl_keuangan_transaksi')
                              ->selectSum('jumlah')
                              ->where('entitas_type', 'masjid_mushola')
                              ->where('entitas_id', $idMasjid)
                              ->where('jenis', 'pengeluaran')
                              ->like('tanggal_transaksi', $bulanIni, 'after')
                              ->get()->getRowArray()['jumlah'] ?? 0;

            $saldoKas = ((float)$pemasukan) - ((float)$pengeluaran);
        }

        // 5. Jadwal Maghrib Mengaji di masjid ini (hari ini & besok)
        $jadwalMaghrib = $db->table('tbl_jadwal_kegiatan j')
            ->select('j.tanggal, j.peran_petugas, p.nama_lengkap as nama_mubaligh, p.no_hp, p.foto')
            ->join('tbl_personil p', 'p.id = j.id_personil', 'left')
            ->where('j.jenis_kegiatan', 'maghrib_mengaji')
            ->where('j.id_masjid_mushola', $idMasjid)
            ->where('j.id_personil IS NOT NULL', null, false)
            ->whereIn('j.tanggal', [date('Y-m-d'), date('Y-m-d', strtotime('+1 day'))])
            ->orderBy('j.tanggal', 'ASC')
            ->get()->getResultArray();

        // 6. Jadwal Khotib Jumat di masjid ini (3 minggu ke depan)
        $jadwalJumat = $db->table('tbl_jadwal_kegiatan j')
            ->select('j.tanggal, j.peran_petugas, p.nama_lengkap as nama_mubaligh, p.no_hp, p.foto')
            ->join('tbl_personil p', 'p.id = j.id_personil', 'left')
            ->where('j.jenis_kegiatan', 'jumat')
            ->where('j.id_masjid_mushola', $idMasjid)
            ->where('j.id_personil IS NOT NULL', null, false)
            ->where('j.tanggal >=', date('Y-m-d'))
            ->where('j.tanggal <=', date('Y-m-d', strtotime('+21 days')))
            ->orderBy('j.tanggal', 'ASC')
            ->get()->getResultArray();

        // 7. Agenda mandiri masjid yang akan datang (7 hari ke depan)
        $agendaModel      = new AgendaKegiatanModel();
        $agendaMendatang  = $agendaModel->getAgendaMendatang('masjid_mushola', $idMasjid, 7);

        return view('backend/dashboard/operator_masjid', [
            'title'            => 'Dashboard ' . ($masjid['jenis'] ?? 'Masjid'),
            'masjid'           => $masjid,
            'displays'         => $displays,
            'saldoKas'         => $saldoKas,
            'jmlKonten'        => $jmlKonten,
            'jadwalMaghrib'    => $jadwalMaghrib,
            'jadwalJumat'      => $jadwalJumat,
            'agendaMendatang'  => $agendaMendatang,
            'belumSetup'       => false,
        ]);
    }

    /**
     * Dashboard khusus untuk OperatorMajelisTaklim
     */
    private function dashboardMajelisTaklim()
    {
        $currentUser = user();
        $db          = \Config\Database::connect();

        if (empty($currentUser->entitas_id) || $currentUser->entitas_type !== 'majelis_taklim') {
            return view('backend/dashboard/operator_majelis_taklim', [
                'title'          => 'Dashboard Majelis Taklim',
                'majelis'        => null,
                'saldoKas'       => 0,
                'agendaTerdekat' => [],
                'belumSetup'     => true,
            ]);
        }

        $idMajelis = $currentUser->entitas_id;

        // 1. Profil Majelis
        $majelis = $db->table('tbl_majelis_taklim')
            ->where('id_majelis_taklim', $idMajelis)
            ->get()->getRowArray();

        // 2. Total Saldo Kas (tbl_keuangan_kas)
        $kasModel = new \App\Models\KeuanganKasModel();
        $listKas  = $kasModel->where('entitas_type', 'majelis_taklim')
                             ->groupStart()
                                ->where('entitas_id', $idMajelis)
                                ->orWhere('entitas_id IS NULL')
                             ->groupEnd()
                             ->where('is_active', 1)
                             ->findAll();
        
        $saldoKas = 0;
        foreach ($listKas as $k) {
            $saldoKas += (float)$kasModel->hitungSaldo($k['id']);
        }

        // 3. Agenda Terdekat (30 hari ke depan)
        $agendaModel = new \App\Models\AgendaKegiatanModel();
        $agendaTerdekat = $agendaModel->getAgendaMendatang('majelis_taklim', $idMajelis, 30);

        return view('backend/dashboard/operator_majelis_taklim', [
            'title'          => 'Dashboard',
            'majelis'        => $majelis,
            'saldoKas'       => $saldoKas,
            'agendaTerdekat' => $agendaTerdekat,
            'belumSetup'     => false,
        ]);
    }
}
