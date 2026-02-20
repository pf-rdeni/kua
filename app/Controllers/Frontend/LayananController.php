<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;

class LayananController extends BaseController
{
    public function persyaratan_nikah()
    {
        return view('frontend/layanan/persyaratan_nikah');
    }

    public function rujuk()
    {
        return view('frontend/layanan/rujuk');
    }
    
    public function legalisir()
    {
        return view('frontend/layanan/legalisir');
    }

    public function konsultasi()
    {
        return view('frontend/layanan/konsultasi');
    }
    
    public function wakaf()
    {
        return view('frontend/layanan/wakaf');
    }

    public function kemasjidan()
    {
        return view('frontend/layanan/kemasjidan');
    }
    
    public function haji()
    {
        return view('frontend/layanan/haji');
    }
}
