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
            ->select('nama, jenis as tipe, alamat, foto, latitude, longitude')
            ->where('latitude !=', '')
            ->where('latitude IS NOT NULL', null, false)
            ->get()->getResultArray();

        $tpqLocations = $db->table('tbl_tpq_mdta')
            ->select('nama, "TPQ / MDTA" as tipe, alamat, foto, latitude, longitude')
            ->where('latitude !=', '')
            ->where('latitude IS NOT NULL', null, false)
            ->get()->getResultArray();

        $personilLocations = $db->table('tbl_personil p')
            ->select('p.nama_lengkap as nama, e.nama_label as tipe, p.alamat as alamat, p.foto, p.latitude, p.longitude')
            ->join('tbl_entitas_type e', 'p.entitas_type = e.kode', 'left')
            ->where('p.latitude !=', '')
            ->where('p.latitude IS NOT NULL', null, false)
            ->where('p.status_aktif', 1)
            ->get()->getResultArray();

        $allLocations = array_merge($masjidLocations, $tpqLocations, $personilLocations);

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
        ];

        return view('backend/dashboard/index', $data);
    }
}
