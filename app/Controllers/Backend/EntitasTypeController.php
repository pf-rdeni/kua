<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\EntitasTypeModel;

class EntitasTypeController extends BaseController
{
    protected $entitasModel;

    public function __construct()
    {
        $this->entitasModel = new EntitasTypeModel();
    }

    public function index()
    {
        $data = [
            'title'        => 'Pengaturan Entitas',
            'entitasTypes' => $this->entitasModel->orderBy('urutan', 'ASC')->findAll(),
        ];

        return view('backend/entitas_type/index', $data);
    }

    public function create()
    {
        // For Myth/Auth group selection dropdown (optional but helpful)
        $db = \Config\Database::connect();
        $authGroups = $db->table('auth_groups')->get()->getResultArray();

        $data = [
            'title'      => 'Tambah Entitas',
            'authGroups' => $authGroups
        ];

        return view('backend/entitas_type/form', $data);
    }

    public function store()
    {
        $rules = [
            'kode'           => 'required|is_unique[tbl_entitas_type.kode]',
            'nama_label'     => 'required',
            'operator_group' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->entitasModel->save([
            'kode'            => strtolower(url_title($this->request->getPost('kode'), '_')),
            'nama_label'      => $this->request->getPost('nama_label'),
            'icon'            => $this->request->getPost('icon') ?? 'fas fa-users',
            'deskripsi'       => $this->request->getPost('deskripsi'),
            'operator_group'  => $this->request->getPost('operator_group'),
            'has_masjid_link' => $this->request->getPost('has_masjid_link') ? 1 : 0,
            'has_sk'          => $this->request->getPost('has_sk') ? 1 : 0,
            'urutan'          => $this->request->getPost('urutan') ?: 0,
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('admin/entitas-type')->with('success', 'Entitas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $entitas = $this->entitasModel->find($id);
        if (! $entitas) {
            return redirect()->to('admin/entitas-type')->with('error', 'Data tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $authGroups = $db->table('auth_groups')->get()->getResultArray();

        $data = [
            'title'      => 'Edit Entitas',
            'entitas'    => $entitas,
            'authGroups' => $authGroups
        ];

        return view('backend/entitas_type/form', $data);
    }

    public function update($id)
    {
        $entitas = $this->entitasModel->find($id);
        if (! $entitas) {
            return redirect()->to('admin/entitas-type')->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'kode'           => "required|is_unique[tbl_entitas_type.kode,id,{$id}]",
            'nama_label'     => 'required',
            'operator_group' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->entitasModel->update($id, [
            'kode'            => strtolower(url_title($this->request->getPost('kode'), '_')),
            'nama_label'      => $this->request->getPost('nama_label'),
            'icon'            => $this->request->getPost('icon') ?? 'fas fa-users',
            'deskripsi'       => $this->request->getPost('deskripsi'),
            'operator_group'  => $this->request->getPost('operator_group'),
            'has_masjid_link' => $this->request->getPost('has_masjid_link') ? 1 : 0,
            'has_sk'          => $this->request->getPost('has_sk') ? 1 : 0,
            'urutan'          => $this->request->getPost('urutan') ?: 0,
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('admin/entitas-type')->with('success', 'Entitas berhasil diupdate.');
    }

    public function delete($id)
    {
        // Optional: you might want to prevent deletion if there are personil records linked
        $personilModel = new \App\Models\PersonilModel();
        
        $entitas = $this->entitasModel->find($id);
        if ($entitas) {
            $count = $personilModel->where('entitas_type', $entitas['kode'])->countAllResults();
            if ($count > 0) {
                return redirect()->to('admin/entitas-type')->with('error', "Gagal dihapus! Terdapat $count data personil yang menggunakan entitas ini.");
            }
            $this->entitasModel->delete($id);
            return redirect()->to('admin/entitas-type')->with('success', 'Entitas berhasil dihapus.');
        }

        return redirect()->to('admin/entitas-type')->with('error', 'Data tidak ditemukan.');
    }
}
