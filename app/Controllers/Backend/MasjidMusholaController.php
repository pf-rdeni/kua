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
        $data = [
            'masjidList' => $this->masjidModel->findAll(),
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

        if ($provinsi && strpos($provinsi, '|') !== false) $provinsi = explode('|', $provinsi)[1];
        if ($kabupaten && strpos($kabupaten, '|') !== false) $kabupaten = explode('|', $kabupaten)[1];
        if ($kecamatan && strpos($kecamatan, '|') !== false) $kecamatan = explode('|', $kecamatan)[1];
        if ($kelurahan && strpos($kelurahan, '|') !== false) $kelurahan = explode('|', $kelurahan)[1];

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

    // ============================================================
    // API ENDPOINT: Update Foto Base64 via AJAX Cropper
    // ============================================================
    public function updateFotoBase64()
    {
        // Allowed roles
        $allowedRoles = ['SuperAdmin', 'Admin', 'Kasi Bimas', 'Kepala KUA', 'OperatorMasjidMushola'];
        if (!in_array(session()->get('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $id = $this->request->getVar('id');
        $base64Image = $this->request->getVar('image_base64');

        if (empty($id) || empty($base64Image)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
        }

        // Check entitas exist
        $masjid = $this->masjidModel->find($id);
        if (!$masjid) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        try {
            // Strip out data header if exists (data:image/jpeg;base64,)
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
            $newFileName = 'masjid_' . uniqid() . '.jpg';
            $uploadPath = FCPATH . 'uploads/masjid_mushola/';
            
            // Ensure folder exists
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $filePath = $uploadPath . $newFileName;

            // Save raw image first
            if (!file_put_contents($filePath, $imageData)) {
                throw new \Exception('Failed to save file');
            }

            // Manipulate/Compress Image (800x800 max for landscape)
            \Config\Services::image()
                ->withFile($filePath)
                ->resize(800, 800, true, 'height')
                ->save($filePath, 80); // 80% quality

            // Delete old photo if it exists and different
            if (!empty($masjid['foto']) && file_exists($uploadPath . $masjid['foto'])) {
                @unlink($uploadPath . $masjid['foto']);
            }

            // Update database
            $this->masjidModel->update($id, ['foto' => $newFileName]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Foto berhasil diperbarui',
                'new_image_url' => base_url('uploads/masjid_mushola/' . $newFileName)
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
