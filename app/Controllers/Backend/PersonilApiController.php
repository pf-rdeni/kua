<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\PersonilModel;

class PersonilApiController extends BaseController
{
    /**
     * Endpoint untuk mencari NIK via dropdown Select2
     * Mengembalikan data array: id (nik), text (nik - nama)
     */
    public function searchNik()
    {
        $term = $this->request->getGet('q');
        $personilModel = new PersonilModel();
        
        $personilModel->select('nik, nama_lengkap');
        $personilModel->distinct();
        
        if (!empty($term)) {
            $personilModel->like('nik', $term);
        }
        
        // Hindari duplikasi referensi jika suatu NIK terdaftar di banyak entitas
        $personilModel->orderBy('nik', 'ASC');
        $results = $personilModel->findAll(10);
        
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'id'   => $row['nik'],
                'text' => $row['nik'] . ' - ' . $row['nama_lengkap']
            ];
        }
        
        return $this->response->setJSON($data);
    }

    /**
     * Endpoint untuk mengambil seluruh detail data Personil berdasarkan NIK
     * Berguna untuk melakukan Auto-Fill form pendaftaran Personil baru jika data sudah ada
     */
    public function getByNik()
    {
        $nik = $this->request->getGet('nik');
        
        if (empty($nik)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'NIK tidak valid']);
        }

        $personilModel = new PersonilModel();
        
        // Ambil data personil terbaru yang menggunakan NIK tersebut (jebakan auto-fill paling update)
        $personil = $personilModel->where('nik', $nik)->orderBy('id', 'DESC')->first();
        
        if ($personil) {
             // Cari tahu rentetan entitas_type di mana NIK ini terdaftar (Syarat Identifikasi Multi-Entitas)
             $allRecords = $personilModel->where('nik', $nik)->find();
             $registeredEntities = array_unique(array_column($allRecords, 'entitas_type'));
             
             return $this->response->setJSON([
                 'status' => 'success',
                 'data'   => $personil,
                 'registered_entities' => array_values($registeredEntities)
             ]);
        }
        
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Data tidak ditemukan'
        ]);
    }
}
