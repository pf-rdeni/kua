<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\MasjidMusholaModel;

class MasjidMusholaController extends BaseController
{
    protected $masjidModel;

    public function __construct()
    {
        $this->masjidModel = new MasjidMusholaModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        
        if ($keyword) {
            $dataMasjid = $this->masjidModel->search($keyword);
        } else {
            $dataMasjid = $this->masjidModel;
        }

        $data = [
            'masjidList' => $dataMasjid->paginate(10, 'masjid'),
            'pager'      => $this->masjidModel->pager,
            'keyword'    => $keyword,
        ];

        return view('backend/masjid_mushola/index', $data);
    }

    public function create()
    {
        return view('backend/masjid_mushola/form');
    }

    public function store()
    {
        if (!$this->validate($this->masjidModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $foto = $this->request->getFile('foto');
        $namaFoto = null;

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $namaFoto = $foto->getRandomName();
            $foto->move('uploads/masjid_mushola', $namaFoto);
        }

        $this->masjidModel->save([
            'nama'           => $this->request->getPost('nama'),
            'jenis'          => $this->request->getPost('jenis'),
            'alamat'         => $this->request->getPost('alamat'),
            'tahun_berdiri'  => $this->request->getPost('tahun_berdiri'),
            'luas_bangunan'  => $this->request->getPost('luas_bangunan'),
            'status_tanah'   => $this->request->getPost('status_tanah'),
            'nama_ketua_dkm' => $this->request->getPost('nama_ketua_dkm'),
            'no_hp_ketua'    => $this->request->getPost('no_hp_ketua'),
            'jumlah_jamaah'  => $this->request->getPost('jumlah_jamaah'),
            'latitude'       => $this->request->getPost('latitude'),
            'longitude'      => $this->request->getPost('longitude'),
            'foto'           => $namaFoto,
        ]);

        return redirect()->to('admin/masjid-mushola')->with('success', 'Data Masjid/Mushola berhasil ditambahkan.');
    }

    public function show($id)
    {
        $data = [
            'masjid' => $this->masjidModel->find($id),
        ];

        if (empty($data['masjid'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Masjid/Mushola tidak ditemukan: ' . $id);
        }

        return view('backend/masjid_mushola/show', $data);
    }

    public function edit($id)
    {
        $data = [
            'masjid' => $this->masjidModel->find($id),
        ];

        if (empty($data['masjid'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Masjid/Mushola tidak ditemukan: ' . $id);
        }

        return view('backend/masjid_mushola/form', $data);
    }

    public function update($id)
    {
        $rules = $this->masjidModel->getValidationRules();
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $foto = $this->request->getFile('foto');
        $masjidLama = $this->masjidModel->find($id);
        $namaFoto = $masjidLama['foto'];

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            if ($namaFoto && file_exists('uploads/masjid_mushola/' . $namaFoto)) {
                unlink('uploads/masjid_mushola/' . $namaFoto);
            }
            $namaFoto = $foto->getRandomName();
            $foto->move('uploads/masjid_mushola', $namaFoto);
        }

        $this->masjidModel->update($id, [
            'nama'           => $this->request->getPost('nama'),
            'jenis'          => $this->request->getPost('jenis'),
            'alamat'         => $this->request->getPost('alamat'),
            'tahun_berdiri'  => $this->request->getPost('tahun_berdiri'),
            'luas_bangunan'  => $this->request->getPost('luas_bangunan'),
            'status_tanah'   => $this->request->getPost('status_tanah'),
            'nama_ketua_dkm' => $this->request->getPost('nama_ketua_dkm'),
            'no_hp_ketua'    => $this->request->getPost('no_hp_ketua'),
            'jumlah_jamaah'  => $this->request->getPost('jumlah_jamaah'),
            'latitude'       => $this->request->getPost('latitude'),
            'longitude'      => $this->request->getPost('longitude'),
            'foto'           => $namaFoto,
        ]);

        return redirect()->to('admin/masjid-mushola')->with('success', 'Data Masjid/Mushola berhasil diperbarui.');
    }

    public function delete($id)
    {
        $masjid = $this->masjidModel->find($id);

        if ($masjid['foto'] && file_exists('uploads/masjid_mushola/' . $masjid['foto'])) {
            unlink('uploads/masjid_mushola/' . $masjid['foto']);
        }

        $this->masjidModel->delete($id);

        return redirect()->to('admin/masjid-mushola')->with('success', 'Data Masjid/Mushola berhasil dihapus.');
    }
}
