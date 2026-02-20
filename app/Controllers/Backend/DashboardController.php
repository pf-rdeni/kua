<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $data = [
            'title'              => 'Dashboard',
            'totalMubaligh'      => $db->table('tbl_mubaligh')->where('status_aktif', 1)->countAllResults(),
            'totalMasjidMushola' => $db->table('tbl_masjid_mushola')->countAllResults(),
            'totalImamMasjid'    => $db->table('tbl_imam_masjid')->where('status_aktif', 1)->countAllResults(),
            'totalFarduKifayah'  => $db->table('tbl_pengurus_fardu_kifayah')->countAllResults(),
            'totalPenggaliKubur' => $db->table('tbl_petugas_penggali_kubur')->countAllResults(),
            'totalMajelisTaklim' => $db->table('tbl_majelis_taklim')->countAllResults(),
            'totalTpqMdta'       => $db->table('tbl_lembaga_tpq_mdta')->countAllResults(),
        ];

        return view('backend/dashboard/index', $data);
    }
}
