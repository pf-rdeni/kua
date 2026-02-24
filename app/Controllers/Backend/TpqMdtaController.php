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
        $keyword = $this->request->getGet('keyword');
        
        $dataTPQ = $this->tpqModel->getWithMasjid();

        if ($keyword) {
            $dataTPQ->like('tbl_tpq_mdta.nama', $keyword)
                    ->orLike('tbl_tpq_mdta.pimpinan', $keyword)
                    ->orLike('tbl_masjid_mushola.nama', $keyword);
        }

        $data = [
            'tpqList' => $dataTPQ->paginate(10, 'tpq_mdta'),
            'pager'   => $this->tpqModel->pager,
            'keyword' => $keyword,
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
        }

        $this->tpqModel->save([
            'id_masjid_mushola' => $this->request->getPost('id_masjid_mushola'),
            'nama'              => $this->request->getPost('nama'),
            'alamat'            => $this->request->getPost('alamat'),
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
                unlink('uploads/tpq_mdta/' . $namaFoto);
            }
            $namaFoto = $foto->getRandomName();
            $foto->move('uploads/tpq_mdta', $namaFoto);
        }

        $this->tpqModel->update($id, [
            'id_masjid_mushola' => $this->request->getPost('id_masjid_mushola'),
            'nama'              => $this->request->getPost('nama'),
            'alamat'            => $this->request->getPost('alamat'),
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
}
