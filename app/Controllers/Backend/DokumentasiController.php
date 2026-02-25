<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;

class DokumentasiController extends BaseController
{
    protected $entitasTypeModel;

    public function __construct()
    {
        $this->entitasTypeModel = new \App\Models\EntitasTypeModel();
    }

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

    public function alurInsentif()
    {
        return view('backend/dokumentasi/alur_insentif', [
            'pageTitle' => 'Proses Pengajuan Insentif',
            'breadcrumb' => [
                ['title' => 'Dokumentasi', 'url' => 'admin/dokumentasi/arsitektur'],
                ['title' => 'Pengajuan Insentif', 'url' => '']
            ]
        ]);
    }

    public function uploadBerkas()
    {
        return view('backend/dokumentasi/upload_berkas', [
            'pageTitle' => 'Panduan Upload Berkas',
            'breadcrumb' => [
                ['title' => 'Dokumentasi', 'url' => 'admin/dokumentasi/arsitektur'],
                ['title' => 'Panduan Upload Berkas', 'url' => '']
            ]
        ]);
    }

    public function settingBerkas()
    {
        $data = [
            'title' => 'Panduan Setting Berkas',
            'entitas_type' => $this->entitasTypeModel->where('is_active', 1)->findAll()
        ];
        
        return view('backend/dokumentasi/setting_berkas', $data);
    }

    public function settingEntitas()
    {
        $data = [
            'title' => 'Panduan Setting Entitas',
            'entitas_type' => $this->entitasTypeModel->where('is_active', 1)->findAll()
        ];
        
        return view('backend/dokumentasi/setting_entitas', $data);
    }

    public function jadwalRamadhan()
    {
        return view('backend/dokumentasi/jadwal_ramadhan', [
            'pageTitle' => 'Panduan Jadwal Ramadhan',
            'breadcrumb' => [
                ['title' => 'Dokumentasi', 'url' => 'admin/dokumentasi/arsitektur'],
                ['title' => 'Jadwal Ramadhan', 'url' => '']
            ]
        ]);
    }
}
