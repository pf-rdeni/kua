<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\AuthGroupModel;

class GroupController extends BaseController
{
    protected $groupModel;

    public function __construct()
    {
        $this->groupModel = new AuthGroupModel();
    }

    public function index()
    {
        $data = [
            'title'  => 'Manajemen Grup Akun',
            'groups' => $this->groupModel->findAll(),
        ];

        return view('backend/group/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Grup Akun'
        ];

        return view('backend/group/form', $data);
    }

    public function store()
    {
        $rules = [
            'name'        => 'required|alpha_numeric_space|is_unique[auth_groups.name]',
            'description' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->groupModel->save([
            'name'        => str_replace(' ', '', ucwords($this->request->getPost('name'))), // e.g. "Operator Penyuluh" -> "OperatorPenyuluh"
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to('admin/groups')->with('success', 'Grup akun berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $group = $this->groupModel->find($id);
        if (! $group) {
            return redirect()->to('admin/groups')->with('error', 'Grup tidak ditemukan.');
        }

        // Proteksi SuperAdmin dan Admin
        if (in_array($group['name'], ['SuperAdmin', 'Admin'])) {
            return redirect()->to('admin/groups')->with('error', 'Grup sistem inti (SuperAdmin/Admin) tidak boleh diubah secara manual.');
        }

        $data = [
            'title' => 'Edit Grup Akun',
            'group' => $group
        ];

        return view('backend/group/form', $data);
    }

    public function update($id)
    {
        $group = $this->groupModel->find($id);
        if (! $group) {
            return redirect()->to('admin/groups')->with('error', 'Grup tidak ditemukan.');
        }

        if (in_array($group['name'], ['SuperAdmin', 'Admin'])) {
            return redirect()->to('admin/groups')->with('error', 'Grup sistem inti (SuperAdmin/Admin) tidak boleh diubah secara manual.');
        }

        $rules = [
            'name'        => "required|alpha_numeric_space|is_unique[auth_groups.name,id,{$id}]",
            'description' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->groupModel->update($id, [
            'name'        => str_replace(' ', '', ucwords($this->request->getPost('name'))),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to('admin/groups')->with('success', 'Grup akun berhasil diubah.');
    }

    public function delete($id)
    {
        $group = $this->groupModel->find($id);
        if ($group) {
            if (in_array($group['name'], ['SuperAdmin', 'Admin'])) {
                return redirect()->to('admin/groups')->with('error', 'Grup sistem inti (SuperAdmin/Admin) tidak boleh dihapus.');
            }

            // Optional: Cek apakah grup sedang digunakan oleh entitas type
            $db = \Config\Database::connect();
            $inUseCount = $db->table('tbl_entitas_type')->where('operator_group', $group['name'])->countAllResults();
            if ($inUseCount > 0) {
                 return redirect()->to('admin/groups')->with('error', "Gagal dihapus! Grup ini sedang ditugaskan pada $inUseCount entitas di Pengaturan Entitas.");
            }
            
            // Optional: Cek apakah grup sedang ada penggunanya
            $userCount = $db->table('auth_groups_users')->where('group_id', $id)->countAllResults();
            if ($userCount > 0) {
                 return redirect()->to('admin/groups')->with('error', "Gagal dihapus! Terdapat $userCount pengguna/user yang memiliki peran/grup ini.");
            }

            $this->groupModel->delete($id);
            return redirect()->to('admin/groups')->with('success', 'Grup akun berhasil dihapus.');
        }

        return redirect()->to('admin/groups')->with('error', 'Grup tidak ditemukan.');
    }
}
