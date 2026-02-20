<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\MubalighModel;

class MubalighController extends BaseController
{
    protected $mubalighModel;

    public function __construct()
    {
        $this->mubalighModel = new MubalighModel();
    }

    /**
     * Tampilkan daftar semua mubaligh
     */
    public function index()
    {
        $keyword = $this->request->getGet('keyword');

        if ($keyword) {
            $mubalighList = $this->mubalighModel->search($keyword)
                                                ->orderBy('nama_lengkap', 'ASC')
                                                ->paginate(10, 'mubaligh');
        } else {
            $mubalighList = $this->mubalighModel->orderBy('nama_lengkap', 'ASC')
                                                ->paginate(10, 'mubaligh');
        }

        $data = [
            'title'        => 'Data Mubaligh',
            'mubalighList' => $mubalighList,
            'pager'        => $this->mubalighModel->pager,
            'keyword'      => $keyword,
            'currentPage'  => $this->request->getGet('page_mubaligh') ?? 1,
        ];

        return view('backend/mubaligh/index', $data);
    }

    /**
     * Tampilkan form tambah mubaligh
     */
    public function create()
    {
        $data = [
            'title'      => 'Tambah Mubaligh',
            'validation' => \Config\Services::validation(),
        ];

        return view('backend/mubaligh/form', $data);
    }

    /**
     * Simpan data mubaligh baru
     */
    public function store()
    {
        // Validasi input
        $rules = [
            'nama_lengkap'  => 'required|min_length[3]|max_length[255]',
            'nik'           => 'permit_empty|exact_length[16]|numeric',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'no_hp'         => 'permit_empty|max_length[20]',
            'foto'          => 'permit_empty|uploaded[foto]|max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle upload foto
        $fotoName = null;
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && ! $foto->hasMoved()) {
            $fotoName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/mubaligh', $fotoName);
        }

        // Simpan data
        $this->mubalighModel->save([
            'nama_lengkap'       => $this->request->getPost('nama_lengkap'),
            'nik'                => $this->request->getPost('nik'),
            'tempat_lahir'       => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir'      => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin'      => $this->request->getPost('jenis_kelamin'),
            'alamat'             => $this->request->getPost('alamat'),
            'kelurahan_desa'     => $this->request->getPost('kelurahan_desa'),
            'no_hp'              => $this->request->getPost('no_hp'),
            'pendidikan_terakhir' => $this->request->getPost('pendidikan_terakhir'),
            'pekerjaan'          => $this->request->getPost('pekerjaan'),
            'status_aktif'       => $this->request->getPost('status_aktif') ?? 1,
            'foto'               => $fotoName,
            'latitude'           => $this->request->getPost('latitude'),
            'longitude'          => $this->request->getPost('longitude'),
        ]);

        return redirect()->to('/admin/mubaligh')->with('success', 'Data mubaligh berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit mubaligh
     */
    public function edit($id)
    {
        $mubaligh = $this->mubalighModel->find($id);

        if (! $mubaligh) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data mubaligh tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Mubaligh',
            'mubaligh'   => $mubaligh,
            'validation' => \Config\Services::validation(),
        ];

        return view('backend/mubaligh/form', $data);
    }

    /**
     * Update data mubaligh
     */
    public function update($id)
    {
        $mubaligh = $this->mubalighModel->find($id);

        if (! $mubaligh) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data mubaligh tidak ditemukan.');
        }

        // Validasi input
        $rules = [
            'nama_lengkap'  => 'required|min_length[3]|max_length[255]',
            'nik'           => 'permit_empty|exact_length[16]|numeric',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'no_hp'         => 'permit_empty|max_length[20]',
            'foto'          => 'permit_empty|uploaded[foto]|max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle upload foto baru
        $fotoName = $mubaligh['foto']; // Pertahankan foto lama
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && ! $foto->hasMoved()) {
            // Hapus foto lama jika ada
            if ($mubaligh['foto'] && file_exists(FCPATH . 'uploads/mubaligh/' . $mubaligh['foto'])) {
                unlink(FCPATH . 'uploads/mubaligh/' . $mubaligh['foto']);
            }
            $fotoName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/mubaligh', $fotoName);
        }

        // Update data
        $this->mubalighModel->update($id, [
            'nama_lengkap'       => $this->request->getPost('nama_lengkap'),
            'nik'                => $this->request->getPost('nik'),
            'tempat_lahir'       => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir'      => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin'      => $this->request->getPost('jenis_kelamin'),
            'alamat'             => $this->request->getPost('alamat'),
            'kelurahan_desa'     => $this->request->getPost('kelurahan_desa'),
            'no_hp'              => $this->request->getPost('no_hp'),
            'pendidikan_terakhir' => $this->request->getPost('pendidikan_terakhir'),
            'pekerjaan'          => $this->request->getPost('pekerjaan'),
            'status_aktif'       => $this->request->getPost('status_aktif') ?? 1,
            'foto'               => $fotoName,
            'latitude'           => $this->request->getPost('latitude'),
            'longitude'          => $this->request->getPost('longitude'),
        ]);

        return redirect()->to('/admin/mubaligh')->with('success', 'Data mubaligh berhasil diperbarui.');
    }

    /**
     * Hapus data mubaligh
     */
    public function delete($id)
    {
        $mubaligh = $this->mubalighModel->find($id);

        if (! $mubaligh) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data mubaligh tidak ditemukan.');
        }

        // Hapus foto jika ada
        if ($mubaligh['foto'] && file_exists(FCPATH . 'uploads/mubaligh/' . $mubaligh['foto'])) {
            unlink(FCPATH . 'uploads/mubaligh/' . $mubaligh['foto']);
        }

        $this->mubalighModel->delete($id);

        return redirect()->to('/admin/mubaligh')->with('success', 'Data mubaligh berhasil dihapus.');
    }

    /**
     * Tampilkan detail mubaligh
     */
    public function show($id)
    {
        $mubaligh = $this->mubalighModel->find($id);

        if (! $mubaligh) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data mubaligh tidak ditemukan.');
        }

        $data = [
            'title'    => 'Detail Mubaligh',
            'mubaligh' => $mubaligh,
        ];

        return view('backend/mubaligh/show', $data);
    }

    /**
     * Tampilkan halaman berkas lampiran mubaligh
     */
    public function showBerkasLampiran()
    {
        $berkasModel = new \App\Models\BerkasModel();

        // Query semua mubaligh
        $mubalighList = $this->mubalighModel->findAll();

        // Ambil data berkas untuk setiap mubaligh
        $mubalighWithBerkas = [];
        foreach ($mubalighList as $m) {
            $berkasAktif = $berkasModel->getBerkasAktif('mubaligh', $m['id_mubaligh']);

            // Organize berkas by type
            $berkasByType = [];
            foreach ($berkasAktif as $berkas) {
                $berkasByType[$berkas['nama_berkas']] = $berkas;
            }

            $mubalighWithBerkas[] = [
                'mubaligh' => $m,
                'berkas'   => $berkasByType,
            ];
        }

        $data = [
            'page_title'        => 'Berkas Lampiran Mubaligh',
            'mubalighWithBerkas' => $mubalighWithBerkas,
        ];

        return view('backend/mubaligh/berkasLampiran', $data);
    }
}
