<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\FarduKifayahModel;
use App\Models\MasjidMusholaModel;

class FarduKifayahController extends BaseController
{
    protected $fkModel;
    protected $masjidModel;

    public function __construct()
    {
        $this->fkModel = new FarduKifayahModel();
        $this->masjidModel = new MasjidMusholaModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        
        $dataFK = $this->fkModel->getWithMasjid();

        if ($keyword) {
            $dataFK->like('tbl_fardu_kifayah.nama', $keyword)
                   ->orLike('tbl_masjid_mushola.nama', $keyword);
        }

        $data = [
            'fkList'  => $dataFK->paginate(10, 'fardu_kifayah'),
            'pager'   => $this->fkModel->pager,
            'keyword' => $keyword,
        ];

        return view('backend/fardu_kifayah/index', $data);
    }

    public function create()
    {
        $data = [
            'masjidList' => $this->masjidModel->findAll(),
        ];
        return view('backend/fardu_kifayah/form', $data);
    }

    public function store()
    {
        if (!$this->validate($this->fkModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle Foto
        $foto = $this->request->getFile('foto');
        $namaFoto = null;
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $namaFoto = $foto->getRandomName();
            $foto->move('uploads/fardu_kifayah', $namaFoto);
        }

        // Handle SK
        $sk = $this->request->getFile('sk_pengangkatan');
        $namaSk = null;
        if ($sk && $sk->isValid() && !$sk->hasMoved()) {
            $namaSk = $sk->getRandomName();
            $sk->move('uploads/fardu_kifayah', $namaSk);
        }

        $this->fkModel->save([
            'id_masjid_mushola' => $this->request->getPost('id_masjid_mushola'),
            'nama'              => $this->request->getPost('nama'),
            'no_hp'             => $this->request->getPost('no_hp'),
            'alamat'            => $this->request->getPost('alamat'),
            'status'            => $this->request->getPost('status'),
            'foto'              => $namaFoto,
            'sk_pengangkatan'   => $namaSk,
        ]);

        return redirect()->to('admin/fardu-kifayah')->with('success', 'Data Petugas Fardu Kifayah berhasil ditambahkan.');
    }

    public function show($id)
    {
        $data = [
            'petugas' => $this->fkModel->getWithMasjid($id),
        ];

        if (empty($data['petugas'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Petugas tidak ditemukan: ' . $id);
        }

        return view('backend/fardu_kifayah/show', $data);
    }

    public function edit($id)
    {
        $data = [
            'petugas'    => $this->fkModel->find($id),
            'masjidList' => $this->masjidModel->findAll(),
        ];

        if (empty($data['petugas'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Petugas tidak ditemukan: ' . $id);
        }

        return view('backend/fardu_kifayah/form', $data);
    }

    public function update($id)
    {
        if (!$this->validate($this->fkModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fkLama = $this->fkModel->find($id);

        // Handle Foto
        $foto = $this->request->getFile('foto');
        $namaFoto = $fkLama['foto'];
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            if ($namaFoto && file_exists('uploads/fardu_kifayah/' . $namaFoto)) {
                unlink('uploads/fardu_kifayah/' . $namaFoto);
            }
            $namaFoto = $foto->getRandomName();
            $foto->move('uploads/fardu_kifayah', $namaFoto);
        }

        // Handle SK
        $sk = $this->request->getFile('sk_pengangkatan');
        $namaSk = $fkLama['sk_pengangkatan'];
        if ($sk && $sk->isValid() && !$sk->hasMoved()) {
            if ($namaSk && file_exists('uploads/fardu_kifayah/' . $namaSk)) {
                unlink('uploads/fardu_kifayah/' . $namaSk);
            }
            $namaSk = $sk->getRandomName();
            $sk->move('uploads/fardu_kifayah', $namaSk);
        }

        $this->fkModel->update($id, [
            'id_masjid_mushola' => $this->request->getPost('id_masjid_mushola'),
            'nama'              => $this->request->getPost('nama'),
            'no_hp'             => $this->request->getPost('no_hp'),
            'alamat'            => $this->request->getPost('alamat'),
            'status'            => $this->request->getPost('status'),
            'foto'              => $namaFoto,
            'sk_pengangkatan'   => $namaSk,
        ]);

        return redirect()->to('admin/fardu-kifayah')->with('success', 'Data Petugas Fardu Kifayah berhasil diperbarui.');
    }

    public function delete($id)
    {
        $fk = $this->fkModel->find($id);

        if ($fk['foto'] && file_exists('uploads/fardu_kifayah/' . $fk['foto'])) {
            unlink('uploads/fardu_kifayah/' . $fk['foto']);
        }
        if ($fk['sk_pengangkatan'] && file_exists('uploads/fardu_kifayah/' . $fk['sk_pengangkatan'])) {
            unlink('uploads/fardu_kifayah/' . $fk['sk_pengangkatan']);
        }

        $this->fkModel->delete($id);

        return redirect()->to('admin/fardu-kifayah')->with('success', 'Data Petugas Fardu Kifayah berhasil dihapus.');
    }
}
