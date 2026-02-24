<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\MajelisTaklimModel;
use App\Models\MasjidMusholaModel;

class MajelisTaklimController extends BaseController
{
    protected $mtModel;
    protected $masjidModel;

    public function __construct()
    {
        $this->mtModel = new MajelisTaklimModel();
        $this->masjidModel = new MasjidMusholaModel();
    }

    public function index()
    {
        $data = [
            'mtList'  => $this->mtModel->getWithMasjid()->findAll(),
        ];

        return view('backend/majelis_taklim/index', $data);
    }

    public function create()
    {
        $data = [
            'masjidList' => $this->masjidModel->findAll(),
        ];
        return view('backend/majelis_taklim/form', $data);
    }

    public function store()
    {
        if (!$this->validate($this->mtModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle Foto
        $foto = $this->request->getFile('foto');
        $namaFoto = null;
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $namaFoto = $foto->getRandomName();
            $foto->move('uploads/majelis_taklim', $namaFoto);

            // Resize compress
            \Config\Services::image()
                ->withFile('uploads/majelis_taklim/' . $namaFoto)
                ->resize(800, 800, true, 'height')
                ->save('uploads/majelis_taklim/' . $namaFoto, 70);
        }

        // Parsing JSON Wilayah dari Emsifa
        $provinsi = $this->request->getPost('provinsi');
        $kabupaten = $this->request->getPost('kabupaten_kota');
        $kecamatan = $this->request->getPost('kecamatan');
        $kelurahan = $this->request->getPost('kelurahan_desa');

        if ($provinsi && strpos($provinsi, '|') !== false) $provinsi = explode('|', $provinsi)[1];
        if ($kabupaten && strpos($kabupaten, '|') !== false) $kabupaten = explode('|', $kabupaten)[1];
        if ($kecamatan && strpos($kecamatan, '|') !== false) $kecamatan = explode('|', $kecamatan)[1];
        if ($kelurahan && strpos($kelurahan, '|') !== false) $kelurahan = explode('|', $kelurahan)[1];

        $this->mtModel->save([
            'id_masjid_mushola'     => $this->request->getPost('id_masjid_mushola') ?: null,
            'nama_majelis_taklim'   => $this->request->getPost('nama_majelis_taklim'),
            'alamat'                => trim($this->request->getPost('alamat')),
            'provinsi'              => $provinsi,
            'kabupaten_kota'        => $kabupaten,
            'kecamatan'             => $kecamatan,
            'kelurahan_desa'        => $kelurahan,
            'rt'                    => $this->request->getPost('rt'),
            'rw'                    => $this->request->getPost('rw'),
            'susunan_pengurus'      => $this->request->getPost('susunan_pengurus'),
            'hari'                  => $this->request->getPost('hari'),
            'waktu'                 => $this->request->getPost('waktu'),
            'pimpinan'              => $this->request->getPost('pimpinan'),
            'no_hp_pimpinan'        => $this->request->getPost('no_hp_pimpinan'),
            'jumlah_jamaah'         => $this->request->getPost('jumlah_jamaah'),
            'foto'                  => $namaFoto,
        ]);

        return redirect()->to('admin/majelis-taklim')->with('success', 'Data Majelis Taklim berhasil ditambahkan.');
    }

    public function show($id)
    {
        $data = [
            'majelis' => $this->mtModel->getWithMasjid($id),
        ];

        if (empty($data['majelis'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Majelis Taklim tidak ditemukan: ' . $id);
        }

        return view('backend/majelis_taklim/show', $data);
    }

    public function edit($id)
    {
        $data = [
            'majelis'    => $this->mtModel->find($id),
            'masjidList' => $this->masjidModel->findAll(),
        ];

        if (empty($data['majelis'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Majelis Taklim tidak ditemukan: ' . $id);
        }

        return view('backend/majelis_taklim/form', $data);
    }

    public function update($id)
    {
        if (!$this->validate($this->mtModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $mtLama = $this->mtModel->find($id);

        // Parsing JSON Wilayah dari Emsifa
        $provinsi = $this->request->getPost('provinsi');
        $kabupaten = $this->request->getPost('kabupaten_kota');
        $kecamatan = $this->request->getPost('kecamatan');
        $kelurahan = $this->request->getPost('kelurahan_desa');

        if ($provinsi && strpos($provinsi, '|') !== false) $provinsi = explode('|', $provinsi)[1];
        if ($kabupaten && strpos($kabupaten, '|') !== false) $kabupaten = explode('|', $kabupaten)[1];
        if ($kecamatan && strpos($kecamatan, '|') !== false) $kecamatan = explode('|', $kecamatan)[1];
        if ($kelurahan && strpos($kelurahan, '|') !== false) $kelurahan = explode('|', $kelurahan)[1];

        $this->mtModel->update($id, [
            'id_masjid_mushola'     => $this->request->getPost('id_masjid_mushola') ?: null,
            'nama_majelis_taklim'   => $this->request->getPost('nama_majelis_taklim'),
            'alamat'                => trim($this->request->getPost('alamat')),
            'provinsi'              => $provinsi,
            'kabupaten_kota'        => $kabupaten,
            'kecamatan'             => $kecamatan,
            'kelurahan_desa'        => $kelurahan,
            'rt'                    => $this->request->getPost('rt'),
            'rw'                    => $this->request->getPost('rw'),
            'susunan_pengurus'      => $this->request->getPost('susunan_pengurus'),
            'hari'                  => $this->request->getPost('hari'),
            'waktu'                 => $this->request->getPost('waktu'),
            'pimpinan'              => $this->request->getPost('pimpinan'),
            'no_hp_pimpinan'        => $this->request->getPost('no_hp_pimpinan'),
            'jumlah_jamaah'         => $this->request->getPost('jumlah_jamaah'),
            'foto'                  => $namaFoto,
        ]);

        return redirect()->to('admin/majelis-taklim')->with('success', 'Data Majelis Taklim berhasil diperbarui.');
    }

    public function delete($id)
    {
        $mt = $this->mtModel->find($id);

        if ($mt['foto'] && file_exists('uploads/majelis_taklim/' . $mt['foto'])) {
            unlink('uploads/majelis_taklim/' . $mt['foto']);
        }

        $this->mtModel->delete($id);

        return redirect()->to('admin/majelis-taklim')->with('success', 'Data Majelis Taklim berhasil dihapus.');
    }
}
