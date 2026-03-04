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
        $masjidList = [];
        $title      = 'Data Masjid dan Mushola';

        if (in_groups('OperatorMasjidMushola') && !in_groups('SuperAdmin') && !in_groups('Admin')) {
            // Operator hanya bisa melihat masjid/mushola tempat dia ditugaskan
            $currentUser = user();
            if ($currentUser && $currentUser->entitas_type === 'masjid_mushola' && !empty($currentUser->entitas_id)) {
                $masjidList = $this->masjidModel->where('id_masjid_mushola', $currentUser->entitas_id)->findAll();

                // Ubah title dinamis jika datanya ada
                if (!empty($masjidList)) {
                    $title = 'Data ' . esc($masjidList[0]['jenis']);
                }
            }
        } else {
            // Admin/SuperAdmin bisa melihat semua
            $masjidList = $this->masjidModel->findAll();
        }

        $data = [
            'title'      => $title,
            'breadcrumb' => [
                ['title' => 'Home', 'url' => 'admin/dashboard'],
                ['title' => $title, 'url' => ''],
            ],
            'masjidList' => $masjidList,
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

            // Resize & Kompres Gambar agar riangan dimuat di List View
            // Resize ke max lebar 800px, pertahankan rasio aspek
            \Config\Services::image()
                ->withFile('uploads/masjid_mushola/' . $namaFoto)
                ->resize(800, 800, true, 'height')
                ->save('uploads/masjid_mushola/' . $namaFoto, 70); // Kualitas 70%
        }

        // Parsing JSON Wilayah dari Emsifa
        $provinsi = $this->request->getPost('provinsi');
        $kabupaten = $this->request->getPost('kabupaten_kota');
        $kecamatan = $this->request->getPost('kecamatan');
        $kelurahan = $this->request->getPost('kelurahan_desa');

        if ($provinsi && strpos($provinsi, '|') !== false) $provinsi = ucwords(strtolower(explode('|', $provinsi)[1]));
        elseif ($provinsi) $provinsi = ucwords(strtolower($provinsi));
        if ($kabupaten && strpos($kabupaten, '|') !== false) $kabupaten = ucwords(strtolower(explode('|', $kabupaten)[1]));
        elseif ($kabupaten) $kabupaten = ucwords(strtolower($kabupaten));
        if ($kecamatan && strpos($kecamatan, '|') !== false) $kecamatan = ucwords(strtolower(explode('|', $kecamatan)[1]));
        elseif ($kecamatan) $kecamatan = ucwords(strtolower($kecamatan));
        if ($kelurahan && strpos($kelurahan, '|') !== false) $kelurahan = ucwords(strtolower(explode('|', $kelurahan)[1]));
        elseif ($kelurahan) $kelurahan = ucwords(strtolower($kelurahan));

        $this->masjidModel->save([
            'nama'           => $this->request->getPost('nama'),
            'jenis'          => $this->request->getPost('jenis'),
            // Alamat sekarang dibangun dinamis berdasarkan detail
            'alamat'         => trim($this->request->getPost('alamat')),
            'provinsi'       => $provinsi,
            'kabupaten_kota' => $kabupaten,
            'kecamatan'      => $kecamatan,
            'kelurahan_desa' => $kelurahan,
            'rt'             => $this->request->getPost('rt'),
            'rw'             => $this->request->getPost('rw'),
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
                @unlink('uploads/masjid_mushola/' . $namaFoto);
            }
            $namaFoto = $foto->getRandomName();
            $foto->move('uploads/masjid_mushola', $namaFoto);

            // Resize compress
            \Config\Services::image()
                ->withFile('uploads/masjid_mushola/' . $namaFoto)
                ->resize(800, 800, true, 'height')
                ->save('uploads/masjid_mushola/' . $namaFoto, 70);
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

        $this->masjidModel->update($id, [
            'nama'           => $this->request->getPost('nama'),
            'jenis'          => $this->request->getPost('jenis'),
            'alamat'         => trim($this->request->getPost('alamat')),
            'provinsi'       => $provinsi,
            'kabupaten_kota' => $kabupaten,
            'kecamatan'      => $kecamatan,
            'kelurahan_desa' => $kelurahan,
            'rt'             => $this->request->getPost('rt'),
            'rw'             => $this->request->getPost('rw'),
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
