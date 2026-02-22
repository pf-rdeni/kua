<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;

class DokumentasiController extends BaseController
{
    public function arsitektur()
    {
        return view('backend/dokumentasi/arsitektur', [
            'pageTitle' => 'General System (Arsitektur Data)',
            'breadcrumb' => [
                ['title' => 'Dokumentasi', 'url' => 'admin/dokumentasi/arsitektur'],
                ['title' => 'General System', 'url' => '']
            ]
        ]);
    }

    public function auth()
    {
        return view('backend/dokumentasi/auth', [
            'pageTitle' => 'Proses Input Data Personil & Otentikasi',
            'breadcrumb' => [
                ['title' => 'Dokumentasi', 'url' => 'admin/dokumentasi/arsitektur'],
                ['title' => 'Input Data & Auth', 'url' => '']
            ]
        ]);
    }

    public function komponen()
    {
        return view('backend/dokumentasi/komponen', [
            'pageTitle' => 'Proses Berkas Lampiran',
            'breadcrumb' => [
                ['title' => 'Dokumentasi', 'url' => 'admin/dokumentasi/arsitektur'],
                ['title' => 'Berkas Lampiran', 'url' => '']
            ]
        ]);
    }

    public function alur_insentif()
    {
        return view('backend/dokumentasi/alur_insentif', [
            'pageTitle' => 'Proses Pengajuan Insentif',
            'breadcrumb' => [
                ['title' => 'Dokumentasi', 'url' => 'admin/dokumentasi/arsitektur'],
                ['title' => 'Pengajuan Insentif', 'url' => '']
            ]
        ]);
    }
}
