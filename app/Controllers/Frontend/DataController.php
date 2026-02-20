<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\MasjidMusholaModel;
use App\Models\MubalighModel;
use App\Models\ImamMasjidModel;
use App\Models\MajelisTaklimModel;
use App\Models\TpqMdtaModel;

class DataController extends BaseController
{
    protected $masjidModel;
    protected $mubalighModel;
    protected $imamModel;
    protected $mtModel;
    protected $tpqModel;

    public function __construct()
    {
        $this->masjidModel = new MasjidMusholaModel();
        // Check if models exist, fallback or handle error if not yet created (Mubaligh was step 4)
        $this->mubalighModel = new MubalighModel();
        $this->imamModel = new ImamMasjidModel();
        $this->mtModel = new MajelisTaklimModel();
        $this->tpqModel = new TpqMdtaModel();
    }

    public function masjid_mushola()
    {
        $keyword = $this->request->getGet('keyword');
        $model = $this->masjidModel;
        
        if ($keyword) {
            $model->like('nama', $keyword)->orLike('alamat', $keyword);
        }
        
        $data = [
            'masjidList' => $model->paginate(12, 'data'),
            'pager' => $model->pager,
            'keyword' => $keyword
        ];
        return view('frontend/data/masjid_mushola', $data);
    }

    public function detail_masjid($id)
    {
        $masjid = $this->masjidModel->find($id);
        if (!$masjid) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view('frontend/data/detail_masjid', ['masjid' => $masjid]);
    }

    public function mubaligh()
    {
        $keyword = $this->request->getGet('keyword');
        $model = $this->mubalighModel;
        
        if ($keyword) {
            $model->like('nama', $keyword)->orLike('alamat', $keyword);
        }

        $data = [
            'mubalighList' => $model->paginate(12, 'data'),
            'pager' => $model->pager,
            'keyword' => $keyword
        ];
        return view('frontend/data/mubaligh', $data);
    }

    public function imam_masjid()
    {
        $keyword = $this->request->getGet('keyword');
        $model = $this->imamModel->getWithMasjid(); // Uses Join
        
        if ($keyword) {
             $model->like('tbl_imam_masjid.nama', $keyword)
                   ->orLike('tbl_masjid_mushola.nama', $keyword);
        }

        $data = [
            'imamList' => $model->paginate(12, 'data'),
            'pager' => $this->imamModel->pager,
            'keyword' => $keyword
        ];
        return view('frontend/data/imam_masjid', $data);
    }

    public function majelis_taklim()
    {
        $keyword = $this->request->getGet('keyword');
        $model = $this->mtModel->getWithMasjid(); // Uses Join
        
        if ($keyword) {
             $model->like('tbl_majelis_taklim.nama_majelis_taklim', $keyword)
                   ->orLike('tbl_majelis_taklim.pimpinan', $keyword);
        }

        $data = [
            'mtList' => $model->paginate(12, 'data'),
            'pager' => $this->mtModel->pager,
            'keyword' => $keyword
        ];
        return view('frontend/data/majelis_taklim', $data);
    }

    public function tpq_mdta()
    {
        $keyword = $this->request->getGet('keyword');
        $model = $this->tpqModel->getWithMasjid(); // Uses Join
        
        if ($keyword) {
             $model->like('tbl_tpq_mdta.nama', $keyword)
                   ->orLike('tbl_tpq_mdta.pimpinan', $keyword);
        }

        $data = [
            'tpqList' => $model->paginate(12, 'data'),
            'pager' => $this->tpqModel->pager,
            'keyword' => $keyword
        ];
        return view('frontend/data/tpq_mdta', $data);
    }
}
