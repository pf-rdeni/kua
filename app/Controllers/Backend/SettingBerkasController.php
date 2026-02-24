<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\SettingBerkasModel;

class SettingBerkasController extends BaseController
{
    protected $settingBerkasModel;

    public function __construct()
    {
        $this->settingBerkasModel = new SettingBerkasModel();
    }

    public function index()
    {
        $data = [
            'page_title' => 'Setting Berkas Lampiran',
            'settings' => $this->settingBerkasModel->findAll()
        ];
        return view('backend/setting_berkas/index', $data);
    }

    public function create()
    {
        $data = [
            'page_title' => 'Tambah Setting Berkas'
        ];
        return view('backend/setting_berkas/create', $data);
    }

    public function store()
    {
        $rules = [
            'nama_berkas' => 'required|is_unique[tbl_setting_berkas.nama_berkas]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // entitas_type dari form adalah array multiple select/checkbox
        $entitasType = $this->request->getPost('entitas_type');
        $entitasTypeStr = !empty($entitasType) && is_array($entitasType) ? implode(',', $entitasType) : '';

        $data = [
            'nama_berkas'         => $this->request->getPost('nama_berkas'),
            'entitas_type'        => $entitasTypeStr,
            'aspect_ratio_width'  => $this->request->getPost('aspect_ratio_width') ?: null,
            'aspect_ratio_height' => $this->request->getPost('aspect_ratio_height') ?: null,
            'is_rekening'         => $this->request->getPost('is_rekening') ?? 0,
            'rekening_digit'      => $this->request->getPost('rekening_digit') ?: null,
            'cetak_tipe'          => $this->request->getPost('cetak_tipe') ?? 'gabung',
            'cetak_lebar'         => $this->request->getPost('cetak_lebar') ?? 100,
            'status_aktif'        => $this->request->getPost('status_aktif') ?? 1,
        ];

        $this->settingBerkasModel->insert($data);
        return redirect()->to(base_url('admin/setting-berkas'))->with('success', 'Setting berkas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $setting = $this->settingBerkasModel->find($id);
        if (!$setting) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'page_title' => 'Edit Setting Berkas',
            'setting' => $setting
        ];
        return view('backend/setting_berkas/edit', $data);
    }

    public function update($id)
    {
        $setting = $this->settingBerkasModel->find($id);
        if (!$setting) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'nama_berkas' => "required|is_unique[tbl_setting_berkas.nama_berkas,id,$id]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $entitasType = $this->request->getPost('entitas_type');
        $entitasTypeStr = !empty($entitasType) && is_array($entitasType) ? implode(',', $entitasType) : '';

        $data = [
            'nama_berkas'         => $this->request->getPost('nama_berkas'),
            'entitas_type'        => $entitasTypeStr,
            'aspect_ratio_width'  => $this->request->getPost('aspect_ratio_width') ?: null,
            'aspect_ratio_height' => $this->request->getPost('aspect_ratio_height') ?: null,
            'is_rekening'         => $this->request->getPost('is_rekening') ?? 0,
            'rekening_digit'      => $this->request->getPost('rekening_digit') ?: null,
            'cetak_tipe'          => $this->request->getPost('cetak_tipe') ?? 'gabung',
            'cetak_lebar'         => $this->request->getPost('cetak_lebar') ?? 100,
            'status_aktif'        => $this->request->getPost('status_aktif') ?? 1,
        ];

        // Jika Nama Berkas dirubah oleh Admin, kita harus mengupdate seluruh histori tabel berkas yang lama 
        // agar sinkron dengan template setting yang baru (sehingga PDF layout tidak rusak)
        if ($setting['nama_berkas'] !== $data['nama_berkas']) {
            $berkasModel = new \App\Models\BerkasModel();
            $berkasModel->where('nama_berkas', $setting['nama_berkas'])
                        ->set(['nama_berkas' => $data['nama_berkas']])
                        ->update();
        }

        $this->settingBerkasModel->update($id, $data);
        return redirect()->to(base_url('admin/setting-berkas'))->with('success', 'Setting berkas berhasil diperbarui.');
    }

    public function delete($id)
    {
        $setting = $this->settingBerkasModel->find($id);
        if (!$setting) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        if ($this->settingBerkasModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil dihapus']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus data']);
    }
}
