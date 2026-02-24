<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\TpqMdtaModel;
use App\Models\MasjidMusholaModel;

class TpqMdtaController extends BaseController
{
    protected $tpqModel;
    protected $masjidModel;

    public function __construct()
    {
        $this->tpqModel = new TpqMdtaModel();
        $this->masjidModel = new MasjidMusholaModel();
    }

    public function index()
    {
        $data = [
            'tpqList' => $this->tpqModel->getWithMasjid()->findAll(),
        ];

        return view('backend/tpq_mdta/index', $data);
    }

    public function create()
    {
        $data = [
            'masjidList' => $this->masjidModel->findAll(),
        ];
        return view('backend/tpq_mdta/form', $data);
    }

    public function store()
    {
        if (!$this->validate($this->tpqModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle Foto
        $foto = $this->request->getFile('foto');
        $namaFoto = null;
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $namaFoto = $foto->getRandomName();
            $foto->move('uploads/tpq_mdta', $namaFoto);

            // Resize compress
            \Config\Services::image()
                ->withFile('uploads/tpq_mdta/' . $namaFoto)
                ->resize(800, 800, true, 'height')
                ->save('uploads/tpq_mdta/' . $namaFoto, 70);
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

        $this->tpqModel->save([
            'id_masjid_mushola' => $this->request->getPost('id_masjid_mushola') ?: null,
            'nama'              => $this->request->getPost('nama'),
            'alamat'            => trim($this->request->getPost('alamat')),
            'provinsi'       => $provinsi,
            'kabupaten_kota' => $kabupaten,
            'kecamatan'      => $kecamatan,
            'kelurahan_desa' => $kelurahan,
            'rt'             => $this->request->getPost('rt'),
            'rw'             => $this->request->getPost('rw'),
            'hari'              => $this->request->getPost('hari'),
            'waktu'             => $this->request->getPost('waktu'),
            'pimpinan'          => $this->request->getPost('pimpinan'),
            'no_hp_pimpinan'    => $this->request->getPost('no_hp_pimpinan'),
            'jumlah_santri'     => $this->request->getPost('jumlah_santri'),
            'latitude'          => $this->request->getPost('latitude'),
            'longitude'         => $this->request->getPost('longitude'),
            'foto'              => $namaFoto,
        ]);

        return redirect()->to('admin/tpq-mdta')->with('success', 'Data TPQ/MDTA berhasil ditambahkan.');
    }

    public function show($id)
    {
        $data = [
            'tpq' => $this->tpqModel->getWithMasjid($id),
        ];

        if (empty($data['tpq'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data TPQ/MDTA tidak ditemukan: ' . $id);
        }

        return view('backend/tpq_mdta/show', $data);
    }

    public function edit($id)
    {
        $data = [
            'tpq'        => $this->tpqModel->find($id),
            'masjidList' => $this->masjidModel->findAll(),
        ];

        if (empty($data['tpq'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data TPQ/MDTA tidak ditemukan: ' . $id);
        }

        return view('backend/tpq_mdta/form', $data);
    }

    public function update($id)
    {
        if (!$this->validate($this->tpqModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tpqLama = $this->tpqModel->find($id);

        // Handle Foto
        $foto = $this->request->getFile('foto');
        $namaFoto = $tpqLama['foto'];
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            if ($namaFoto && file_exists('uploads/tpq_mdta/' . $namaFoto)) {
                @unlink('uploads/tpq_mdta/' . $namaFoto);
            }
            $namaFoto = $foto->getRandomName();
            $foto->move('uploads/tpq_mdta', $namaFoto);

            // Resize compress
            \Config\Services::image()
                ->withFile('uploads/tpq_mdta/' . $namaFoto)
                ->resize(800, 800, true, 'height')
                ->save('uploads/tpq_mdta/' . $namaFoto, 70);
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

        $this->tpqModel->update($id, [
            'id_masjid_mushola' => $this->request->getPost('id_masjid_mushola') ?: null,
            'nama'              => $this->request->getPost('nama'),
            'alamat'            => trim($this->request->getPost('alamat')),
            'provinsi'       => $provinsi,
            'kabupaten_kota' => $kabupaten,
            'kecamatan'      => $kecamatan,
            'kelurahan_desa' => $kelurahan,
            'rt'             => $this->request->getPost('rt'),
            'rw'             => $this->request->getPost('rw'),
            'hari'              => $this->request->getPost('hari'),
            'waktu'             => $this->request->getPost('waktu'),
            'pimpinan'          => $this->request->getPost('pimpinan'),
            'no_hp_pimpinan'    => $this->request->getPost('no_hp_pimpinan'),
            'jumlah_santri'     => $this->request->getPost('jumlah_santri'),
            'latitude'          => $this->request->getPost('latitude'),
            'longitude'         => $this->request->getPost('longitude'),
            'foto'              => $namaFoto,
        ]);

        return redirect()->to('admin/tpq-mdta')->with('success', 'Data TPQ/MDTA berhasil diperbarui.');
    }

    public function delete($id)
    {
        $tpq = $this->tpqModel->find($id);

        if ($tpq['foto'] && file_exists('uploads/tpq_mdta/' . $tpq['foto'])) {
            unlink('uploads/tpq_mdta/' . $tpq['foto']);
        }

        $this->tpqModel->delete($id);

        return redirect()->to('admin/tpq-mdta')->with('success', 'Data TPQ/MDTA berhasil dihapus.');
    }

    // ============================================================
    // API ENDPOINT: Update Foto Base64 via AJAX Cropper
    // ============================================================
    public function updateFotoBase64()
    {
        // Allowed roles
        $allowedRoles = ['SuperAdmin', 'Admin', 'Kasi Bimas', 'Kepala KUA', 'OperatorTpqMdta'];
        if (!in_array(session()->get('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $id = $this->request->getVar('id');
        $base64Image = $this->request->getVar('image_base64');

        if (empty($id) || empty($base64Image)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
        }

        $tpq = $this->tpqModel->find($id);
        if (!$tpq) {
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
            $newFileName = 'tpq_' . uniqid() . '.jpg';
            $uploadPath = FCPATH . 'uploads/tpq_mdta/';
            
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

            if (!empty($tpq['foto']) && file_exists($uploadPath . $tpq['foto'])) {
                @unlink($uploadPath . $tpq['foto']);
            }

            $this->tpqModel->update($id, ['foto' => $newFileName]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Foto berhasil diperbarui',
                'new_image_url' => base_url('uploads/tpq_mdta/' . $newFileName)
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
