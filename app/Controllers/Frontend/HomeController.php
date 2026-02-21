<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        // Data statistik untuk halaman publik
        $db = \Config\Database::connect();

        $data = [
            'totalMubaligh'      => $db->table('tbl_personil')->where('entitas_type', 'mubaligh')->where('status_aktif', 1)->countAllResults(),
            'totalMasjidMushola' => $db->table('tbl_masjid_mushola')->countAllResults(),
            'totalImamMasjid'    => $db->table('tbl_personil')->where('entitas_type', 'imam_masjid')->where('status_aktif', 1)->countAllResults(),
            'totalFarduKifayah'  => $db->table('tbl_personil')->where('entitas_type', 'fardu_kifayah')->countAllResults(),
            'totalPenggaliKubur' => $db->table('tbl_personil')->where('entitas_type', 'penggali_kubur')->countAllResults(),
            'totalMajelisTaklim' => $db->table('tbl_majelis_taklim')->countAllResults(),
            'totalTpqMdta'       => $db->table('tbl_tpq_mdta')->countAllResults(),
        ];

        return view('frontend/home/index', $data);
    }
}
