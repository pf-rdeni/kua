<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\PenggaliKuburModel;
use App\Models\MasjidMusholaModel;

class PenggaliKuburController extends BaseController
{
    protected $pkModel;
    protected $masjidModel;

    public function __construct()
    {
        $this->pkModel = new PenggaliKuburModel();
        $this->masjidModel = new MasjidMusholaModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        
        $dataPK = $this->pkModel->getWithMasjid();

        if ($keyword) {
            $dataPK->like('tbl_penggali_kubur.nama', $keyword)
                   ->orLike('tbl_masjid_mushola.nama', $keyword);
        }

        $data = [
            'pkList'  => $dataPK->paginate(10, 'penggali_kubur'),
            'pager'   => $this->pkModel->pager,
            'keyword' => $keyword,
        ];

        return view('backend/penggali_kubur/index', $data);
    }

    public function create()
    {
        $data = [
            'masjidList' => $this->masjidModel->findAll(),
        ];
        return view('backend/penggali_kubur/form', $data);
    }

    public function store()
    {
        if (!$this->validate($this->pkModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle Foto
        $foto = $this->request->getFile('foto');
        $namaFoto = null;
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $namaFoto = $foto->getRandomName();
            $foto->move('uploads/penggali_kubur', $namaFoto);
        }

        // Handle SK
        $sk = $this->request->getFile('sk_pengangkatan');
        $namaSk = null;
        if ($sk && $sk->isValid() && !$sk->hasMoved()) {
            $namaSk = $sk->getRandomName();
            $sk->move('uploads/penggali_kubur', $namaSk);
        }

        $this->pkModel->save([
            'id_masjid_mushola' => $this->request->getPost('id_masjid_mushola'),
            'nama'              => $this->request->getPost('nama'),
            'no_hp'             => $this->request->getPost('no_hp'),
            'alamat'            => $this->request->getPost('alamat'),
            'status'            => $this->request->getPost('status'),
            'foto'              => $namaFoto,
            'sk_pengangkatan'   => $namaSk,
        ]);

        return redirect()->to('admin/penggali-kubur')->with('success', 'Data Penggali Kubur berhasil ditambahkan.');
    }

    public function show($id)
    {
        $data = [
            'petugas' => $this->pkModel->getWithMasjid($id),
        ];

        if (empty($data['petugas'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Petugas tidak ditemukan: ' . $id);
        }

        return view('backend/penggali_kubur/show', $data);
    }

    public function edit($id)
    {
        $data = [
            'petugas'    => $this->pkModel->find($id),
            'masjidList' => $this->masjidModel->findAll(),
        ];

        if (empty($data['petugas'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Petugas tidak ditemukan: ' . $id);
        }

        return view('backend/penggali_kubur/form', $data);
    }

    public function update($id)
    {
        if (!$this->validate($this->pkModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $pkLama = $this->pkModel->find($id);

        // Handle Foto
        $foto = $this->request->getFile('foto');
        $namaFoto = $pkLama['foto'];
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            if ($namaFoto && file_exists('uploads/penggali_kubur/' . $namaFoto)) {
                unlink('uploads/penggali_kubur/' . $namaFoto);
            }
            $namaFoto = $foto->getRandomName();
            $foto->move('uploads/penggali_kubur', $namaFoto);
        }

        // Handle SK
        $sk = $this->request->getFile('sk_pengangkatan');
        $namaSk = $pkLama['sk_pengangkatan'];
        if ($sk && $sk->isValid() && !$sk->hasMoved()) {
            if ($namaSk && file_exists('uploads/penggali_kubur/' . $namaSk)) {
                unlink('uploads/penggali_kubur/' . $namaSk);
            }
            $namaSk = $sk->getRandomName();
            $sk->move('uploads/penggali_kubur', $namaSk);
        }

        $this->pkModel->update($id, [
            'id_masjid_mushola' => $this->request->getPost('id_masjid_mushola'),
            'nama'              => $this->request->getPost('nama'),
            'no_hp'             => $this->request->getPost('no_hp'),
            'alamat'            => $this->request->getPost('alamat'),
            'status'            => $this->request->getPost('status'),
            'foto'              => $namaFoto,
            'sk_pengangkatan'   => $namaSk,
        ]);

        return redirect()->to('admin/penggali-kubur')->with('success', 'Data Penggali Kubur berhasil diperbarui.');
    }

    public function delete($id)
    {
        $pk = $this->pkModel->find($id);

        if ($pk['foto'] && file_exists('uploads/penggali_kubur/' . $pk['foto'])) {
            unlink('uploads/penggali_kubur/' . $pk['foto']);
        }
        if ($pk['sk_pengangkatan'] && file_exists('uploads/penggali_kubur/' . $pk['sk_pengangkatan'])) {
            unlink('uploads/penggali_kubur/' . $pk['sk_pengangkatan']);
        }

        $this->pkModel->delete($id);

        return redirect()->to('admin/penggali-kubur')->with('success', 'Data Penggali Kubur berhasil dihapus.');
    }
}
