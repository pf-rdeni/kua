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

    /**
     * Endpoint untuk mengecek apakah entitas dengan ID terkait memiliki entitas kembar
     * berdasarkan NIK. (Dipakai untuk memicu SweetAlert sinkronisasi Berkas Lampiran).
     */
    public function checkNikSharing()
    {
        $entitasType = $this->request->getGet('entitas_type');
        $entitasId   = $this->request->getGet('entitas_id');

        if (empty($entitasType) || empty($entitasId)) {
             return $this->response->setJSON(['status' => 'error', 'message' => 'Parameter tidak lengkap']);
        }

        // Cari tahu model apa yang dipakai (menggunakan EntitasTypeModel atau manual array)
        $personilModel = new PersonilModel();
        
        // Cari data aslinya dulu untuk menemukan NIK-nya
        $currentEntity = $personilModel->where('entitas_type', $entitasType)
                                       ->where('id', $entitasId)
                                       ->first();

        if (!$currentEntity || empty($currentEntity['nik'])) {
            return $this->response->setJSON([
                'status' => 'success',
                'has_siblings' => false,
                'siblingRoles' => []
            ]);
        }

        // Cari kembaran berdasarkan NIK
        $siblings = $personilModel->where('nik', $currentEntity['nik'])
                                  ->where('id !=', $currentEntity['id'])
                                  ->findAll();

        if (empty($siblings)) {
             return $this->response->setJSON([
                 'status' => 'success',
                 'has_siblings' => false,
                 'siblingRoles' => []
             ]);
        }

        // Mapping role name
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_entitas_type');
        $types = $builder->get()->getResultArray();
        $typeNames = [];
        foreach ($types as $t) {
             $typeNames[$t['kode']] = $t['nama_label'];
        }

        $rolesArray = [];
        foreach ($siblings as $s) {
            $rolesArray[] = $typeNames[$s['entitas_type']] ?? $s['entitas_type'];
        }

        return $this->response->setJSON([
             'status' => 'success',
             'has_siblings' => true,
             'siblingRoles' => array_unique($rolesArray)
        ]);
    }
}
