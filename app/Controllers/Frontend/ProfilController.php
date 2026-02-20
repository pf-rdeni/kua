<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;

class ProfilController extends BaseController
{
    public function sejarah()
    {
        return view('frontend/profil/sejarah');
    }

    public function visi_misi()
    {
        return view('frontend/profil/visi_misi');
    }

    public function struktur_organisasi()
    {
        return view('frontend/profil/struktur_organisasi');
    }

    public function tupoksi()
    {
        return view('frontend/profil/tupoksi');
    }
}
