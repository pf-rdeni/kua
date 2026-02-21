<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\MasjidMusholaModel;
use App\Models\PersonilModel;
use App\Models\MajelisTaklimModel;
use App\Models\TpqMdtaModel;

class DataController extends BaseController
{
    protected $masjidModel;
    protected $personilModel;
    protected $mtModel;
    protected $tpqModel;

    public function __construct()
    {
        $this->masjidModel   = new MasjidMusholaModel();
        $this->personilModel = new PersonilModel();
        $this->mtModel       = new MajelisTaklimModel();
        $this->tpqModel      = new TpqMdtaModel();
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
        $model = $this->personilModel->ofType('mubaligh');
        
        if ($keyword) {
            $model->groupStart()
                  ->like('nama_lengkap', $keyword)
                  ->orLike('alamat', $keyword)
                  ->groupEnd();
        }

        $data = [
            'mubalighList' => $model->paginate(12, 'data'),
            'pager' => $this->personilModel->pager,
            'keyword' => $keyword
        ];
        return view('frontend/data/mubaligh', $data);
    }

    public function imam_masjid()
    {
        $keyword = $this->request->getGet('keyword');
        $model = $this->personilModel->getWithMasjid('imam_masjid');
        
        if ($keyword) {
             $model->groupStart()
                   ->like('tbl_personil.nama_lengkap', $keyword)
                   ->orLike('tbl_masjid_mushola.nama', $keyword)
                   ->groupEnd();
        }

        $data = [
            'imamList' => $model->paginate(12, 'data'),
            'pager' => $this->personilModel->pager,
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
