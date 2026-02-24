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

        // Handle Foto
        $foto = $this->request->getFile('foto');
        $namaFoto = $mtLama['foto'];
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            if ($namaFoto && file_exists('uploads/majelis_taklim/' . $namaFoto)) {
                @unlink('uploads/majelis_taklim/' . $namaFoto);
            }
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

    // ============================================================
    // API ENDPOINT: Update Foto Base64 via AJAX Cropper
    // ============================================================
    public function updateFotoBase64()
    {
        // Allowed roles
        $allowedRoles = ['SuperAdmin', 'Admin', 'Kasi Bimas', 'Kepala KUA', 'OperatorMajelisTaklim'];
        if (!in_array(session()->get('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $id = $this->request->getVar('id');
        $base64Image = $this->request->getVar('image_base64');

        if (empty($id) || empty($base64Image)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
        }

        $mt = $this->mtModel->find($id);
        if (!$mt) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                $type = strtolower($type[1]);
                if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                    throw new \Exception('Invalid image type');
                }
            } else {
                throw new \Exception('Did not match data URI with image data');
            }

            $base64Image = str_replace(' ', '+', $base64Image);
            $imageData = base64_decode($base64Image);

            if ($imageData === false) {
                throw new \Exception('Base64 decode failed');
            }

            // Generate filename
            $newFileName = 'mt_' . uniqid() . '.jpg';
            $uploadPath = FCPATH . 'uploads/majelis_taklim/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $filePath = $uploadPath . $newFileName;

            if (!file_put_contents($filePath, $imageData)) {
                throw new \Exception('Failed to save file');
            }

            \Config\Services::image()
                ->withFile($filePath)
                ->resize(800, 800, true, 'height')
                ->save($filePath, 80);

            if (!empty($mt['foto']) && file_exists($uploadPath . $mt['foto'])) {
                @unlink($uploadPath . $mt['foto']);
            }

            $this->mtModel->update($id, ['foto' => $newFileName]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Foto berhasil diperbarui',
                'new_image_url' => base_url('uploads/majelis_taklim/' . $newFileName)
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
