<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\ImamMasjidModel;
use App\Models\MasjidMusholaModel;

class ImamMasjidController extends BaseController
{
    protected $imamModel;
    protected $masjidModel;

    public function __construct()
    {
        $this->imamModel = new ImamMasjidModel();
        $this->masjidModel = new MasjidMusholaModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        
        $dataImam = $this->imamModel->getWithMasjid();

        if ($keyword) {
            $dataImam->like('tbl_imam_masjid.nama', $keyword)
                     ->orLike('tbl_masjid_mushola.nama', $keyword);
        }

        $data = [
            'imamList' => $dataImam->paginate(10, 'imam'),
            'pager'    => $this->imamModel->pager,
            'keyword'  => $keyword,
        ];

        return view('backend/imam_masjid/index', $data);
    }

    public function create()
    {
        $data = [
            'masjidList' => $this->masjidModel->findAll(),
        ];
        return view('backend/imam_masjid/form', $data);
    }

    public function store()
    {
        if (!$this->validate($this->imamModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle File Uploads
        $foto = $this->request->getFile('foto');
        $namaFoto = null;
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $namaFoto = $foto->getRandomName();
            $foto->move('uploads/imam_masjid', $namaFoto);
        }

        $sk = $this->request->getFile('sk_pengangkatan');
        $namaSk = null;
        if ($sk && $sk->isValid() && !$sk->hasMoved()) {
            $namaSk = $sk->getRandomName();
            $sk->move('uploads/imam_masjid', $namaSk);
        }

        $this->imamModel->save([
            'id_masjid_mushola' => $this->request->getPost('id_masjid_mushola'),
            'nama'              => $this->request->getPost('nama'),
            'no_hp'             => $this->request->getPost('no_hp'),
            'alamat'            => $this->request->getPost('alamat'),
            'status'            => $this->request->getPost('status'),
            'foto'              => $namaFoto,
            'sk_pengangkatan'   => $namaSk,
        ]);

        return redirect()->to('admin/imam-masjid')->with('success', 'Data Imam Masjid berhasil ditambahkan.');
    }

    public function show($id)
    {
        $data = [
            'imam' => $this->imamModel->getWithMasjid($id),
        ];

        if (empty($data['imam'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Imam Masjid tidak ditemukan: ' . $id);
        }

        return view('backend/imam_masjid/show', $data);
    }

    public function edit($id)
    {
        $data = [
            'imam'       => $this->imamModel->find($id),
            'masjidList' => $this->masjidModel->findAll(),
        ];

        if (empty($data['imam'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Imam Masjid tidak ditemukan: ' . $id);
        }

        return view('backend/imam_masjid/form', $data);
    }

    public function update($id)
    {
        if (!$this->validate($this->imamModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $imamLama = $this->imamModel->find($id);

        // Handle Foto
        $foto = $this->request->getFile('foto');
        $namaFoto = $imamLama['foto'];
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            if ($namaFoto && file_exists('uploads/imam_masjid/' . $namaFoto)) {
                unlink('uploads/imam_masjid/' . $namaFoto);
            }
            $namaFoto = $foto->getRandomName();
            $foto->move('uploads/imam_masjid', $namaFoto);
        }

        // Handle SK
        $sk = $this->request->getFile('sk_pengangkatan');
        $namaSk = $imamLama['sk_pengangkatan'];
        if ($sk && $sk->isValid() && !$sk->hasMoved()) {
            if ($namaSk && file_exists('uploads/imam_masjid/' . $namaSk)) {
                unlink('uploads/imam_masjid/' . $namaSk);
            }
            $namaSk = $sk->getRandomName();
            $sk->move('uploads/imam_masjid', $namaSk);
        }

        $this->imamModel->update($id, [
            'id_masjid_mushola' => $this->request->getPost('id_masjid_mushola'),
            'nama'              => $this->request->getPost('nama'),
            'no_hp'             => $this->request->getPost('no_hp'),
            'alamat'            => $this->request->getPost('alamat'),
            'status'            => $this->request->getPost('status'),
            'foto'              => $namaFoto,
            'sk_pengangkatan'   => $namaSk,
        ]);

        return redirect()->to('admin/imam-masjid')->with('success', 'Data Imam Masjid berhasil diperbarui.');
    }

    public function delete($id)
    {
        $imam = $this->imamModel->find($id);

        if ($imam['foto'] && file_exists('uploads/imam_masjid/' . $imam['foto'])) {
            unlink('uploads/imam_masjid/' . $imam['foto']);
        }
        if ($imam['sk_pengangkatan'] && file_exists('uploads/imam_masjid/' . $imam['sk_pengangkatan'])) {
            unlink('uploads/imam_masjid/' . $imam['sk_pengangkatan']);
        }

        $this->imamModel->delete($id);

        return redirect()->to('admin/imam-masjid')->with('success', 'Data Imam Masjid berhasil dihapus.');
    }
}
