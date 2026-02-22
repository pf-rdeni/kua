<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\BerkasModel;
use App\Models\PersonilModel;
use App\Models\MajelisTaklimModel;
use App\Models\TpqMdtaModel;
use App\Models\SettingBerkasModel;

/**
 * BerkasController — Shared AJAX controller untuk upload/edit/delete berkas lampiran.
 * Mendukung semua modul entitas melalui pendekatan polymorphic (entitas_type + entitas_id).
 */
class BerkasController extends BaseController
{
    protected $berkasModel;

    /**
     * Whitelist entitas yang diizinkan.
     * 'model'     => Class model entitas
     * 'pk'        => Primary key kolom
     * 'fotoField' => Kolom foto di tabel entitas (untuk upload profil)
     * 'fotoDir'   => Direktori upload foto profil
     */
    protected $allowedEntitas = [
        // Unified person-entity types (semua pakai PersonilModel)
        'mubaligh' => [
            'model'     => PersonilModel::class,
            'pk'        => 'id',
            'fotoField' => 'foto',
            'fotoDir'   => 'uploads/personil',
        ],
        'imam_masjid' => [
            'model'     => PersonilModel::class,
            'pk'        => 'id',
            'fotoField' => 'foto',
            'fotoDir'   => 'uploads/personil',
        ],
        'fardu_kifayah' => [
            'model'     => PersonilModel::class,
            'pk'        => 'id',
            'fotoField' => 'foto',
            'fotoDir'   => 'uploads/personil',
        ],
        'penggali_kubur' => [
            'model'     => PersonilModel::class,
            'pk'        => 'id',
            'fotoField' => 'foto',
            'fotoDir'   => 'uploads/personil',
        ],
        // Lembaga entities (tetap model terpisah)
        'majelis_taklim' => [
            'model'     => MajelisTaklimModel::class,
            'pk'        => 'id_majelis_taklim',
            'fotoField' => 'foto',
            'fotoDir'   => 'uploads/majelis_taklim',
        ],
        'tpq_mdta' => [
            'model'     => TpqMdtaModel::class,
            'pk'        => 'id_tpq_mdta',
            'fotoField' => 'foto',
            'fotoDir'   => 'uploads/tpq_mdta',
        ],
    ];

    public function __construct()
    {
        $this->berkasModel = new BerkasModel();
    }

    // ============================================================
    // Helper: Resolve entitas model instance
    // ============================================================

    /**
     * Resolve entitas config & model instance berdasarkan entitas_type.
     * @return array|null ['config' => [...], 'modelInstance' => Model]
     */
    private function resolveEntitas(string $entitasType): ?array
    {
        if (!isset($this->allowedEntitas[$entitasType])) {
            return null;
        }

        $config = $this->allowedEntitas[$entitasType];
        $modelClass = $config['model'];
        return [
            'config'        => $config,
            'modelInstance' => new $modelClass(),
        ];
    }

    // ============================================================
    // Upload Berkas (KTP, KK, dll)
    // ============================================================

    public function upload()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Request harus menggunakan AJAX']);
            }

            $entitasType = $this->request->getPost('entitas_type');
            $entitasId   = (int) $this->request->getPost('entitas_id');
            $namaBerkas  = $this->request->getPost('nama_berkas');
            $berkasCropped = $this->request->getPost('berkas_cropped');
            $editBerkasId  = $this->request->getPost('edit_berkas_id');
            $syncAll       = $this->request->getPost('sync_all'); // <-- Diterima parameter setuju Sinkron dari Dialog

            // Validasi entitas_type
            $entitas = $this->resolveEntitas($entitasType);
            if (!$entitas) {
                return $this->response->setJSON(['success' => false, 'message' => 'Tipe entitas tidak valid']);
            }

            // Validasi entitas_id
            if (empty($entitasId)) {
                return $this->response->setJSON(['success' => false, 'message' => 'ID entitas tidak tersedia']);
            }

            // Validasi nama_berkas
            $settingBerkasModel = new SettingBerkasModel();
            $allowedSettings = $settingBerkasModel->getSettingByEntitas($entitasType);
            $allowedTipeBerkas = array_column($allowedSettings, 'nama_berkas');

            if (empty($namaBerkas) || !in_array($namaBerkas, $allowedTipeBerkas)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Tipe berkas tidak valid']);
            }

            // Cek apakah entitas ada
            $entitasData = $entitas['modelInstance']->find($entitasId);
            if (!$entitasData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data entitas tidak ditemukan']);
            }

            // Buat direktori jika belum ada
            $uploadPath = FCPATH . 'uploads/berkas/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $thumbPath = $uploadPath . 'thumbs/';
            if (!is_dir($thumbPath)) {
                mkdir($thumbPath, 0755, true);
            }

            // Tentukan mode: edit atau upload baru
            $isEditMode = !empty($editBerkasId);
            $berkasToUpdate = null;

            if ($isEditMode) {
                $berkasToUpdate = $this->berkasModel->find($editBerkasId);
                if (!$berkasToUpdate || $berkasToUpdate['entitas_type'] != $entitasType || $berkasToUpdate['entitas_id'] != $entitasId) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Data berkas tidak ditemukan atau tidak memiliki akses']);
                }
            } else {
                // Cek apakah sudah ada file aktif dengan tipe yang sama
                $existingBerkas = $this->berkasModel->getBerkasAktifByType($entitasType, $entitasId, $namaBerkas);
                if ($existingBerkas) {
                    $isEditMode = true;
                    $berkasToUpdate = $existingBerkas;
                    $editBerkasId = $existingBerkas['id'];
                }
            }

            // Handle base64 image dari crop
            if (!empty($berkasCropped)) {
                if (preg_match('/^data:image\/(\w+);base64,/', $berkasCropped, $type)) {
                    $berkasCropped = substr($berkasCropped, strpos($berkasCropped, ',') + 1);
                    $type = strtolower($type[1]);

                    if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Format gambar tidak didukung']);
                    }

                    $berkasCropped = base64_decode($berkasCropped);

                    if ($berkasCropped === false) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses gambar']);
                    }

                    // Generate nama file unik
                    $fileName = $entitasType . '_' . $entitasId . '_' . strtolower($namaBerkas) . '_' . time() . '.' . $type;
                    $filePath = $uploadPath . $fileName;

                    // Simpan file
                    if (!file_put_contents($filePath, $berkasCropped)) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan file']);
                    }

                    // Generate Thumbnail
                    try {
                        $image = \Config\Services::image();
                        $image->withFile($filePath)
                              ->resize(300, 300, true, 'auto')
                              ->save($thumbPath . $fileName, 75);
                    } catch (\Exception $e) {
                        log_message('error', 'Gagal generate thumb berkas: ' . $e->getMessage());
                    }

                    if ($isEditMode && $berkasToUpdate) {
                        // --- [NEW] REFERENCE COUNTING SEBELUM REPLACE FILE LAMA ---
                        $usageCountOld = $this->berkasModel->where('nama_file', $berkasToUpdate['nama_file'])->countAllResults();
                        
                        // Hapus file fisik lama HANYA JIKA cuma dipakai oleh 1 baris
                        if ($usageCountOld <= 1) {
                            $oldFilePath = $uploadPath . $berkasToUpdate['nama_file'];
                            if (file_exists($oldFilePath)) {
                                @unlink($oldFilePath);
                            }
                            $oldThumbPath = $thumbPath . $berkasToUpdate['nama_file'];
                            if (file_exists($oldThumbPath)) {
                                @unlink($oldThumbPath);
                            }
                        }

                        // Update record
                        $this->berkasModel->update($editBerkasId, [
                            'nama_file' => $fileName,
                            'status'    => 1,
                        ]);

                        // --- [NEW] GLOBAL FILE SHARING UPDATE ---
                        if ($syncAll === 'true' && in_array($entitasType, ['mubaligh', 'imam_masjid', 'fardu_kifayah', 'penggali_kubur']) && !empty($entitasData['nik'])) {
                            $personilModel = new PersonilModel();
                            $sameNikEntities = $personilModel->where('nik', $entitasData['nik'])
                                                             ->where('id !=', $entitasData['id'])
                                                             ->findAll();
                            
                            foreach ($sameNikEntities as $sibling) {
                                $siblingBerkas = $this->berkasModel->where('entitas_type', $sibling['entitas_type'])
                                                                   ->where('entitas_id', $sibling['id'])
                                                                   ->where('nama_berkas', $namaBerkas)
                                                                   ->first();
                                if ($siblingBerkas) {
                                    $this->berkasModel->update($siblingBerkas['id'], ['nama_file' => $fileName]);
                                } else {
                                    $this->berkasModel->insert([
                                        'entitas_type' => $sibling['entitas_type'],
                                        'entitas_id'   => $sibling['id'],
                                        'nama_berkas'  => $namaBerkas,
                                        'nama_file'    => $fileName,
                                    ]);
                                }
                            }
                        }

                        return $this->response->setJSON([
                            'success' => true,
                            'message' => 'Berkas berhasil diperbarui',
                            'data'    => [
                                'id'        => $editBerkasId,
                                'nama_file' => $fileName,
                                'url'       => base_url('uploads/berkas/' . $fileName),
                            ]
                        ]);
                    }

                    // Insert record baru
                    $this->berkasModel->insert([
                            'entitas_type' => $entitasType,
                            'entitas_id'   => $entitasId,
                            'nama_berkas'  => $namaBerkas,
                            'nama_file'    => $fileName,
                            'status'       => 1,
                        ]);

                        $newId = $this->berkasModel->getInsertID();

                        // --- [NEW] GLOBAL FILE SHARING (KTP, KK, dll) ---
                        // Khusus untuk personil, sinkronkan file ini ke entitas lain dengan NIK yang sama
                        if ($syncAll === 'true' && in_array($entitasType, ['mubaligh', 'imam_masjid', 'fardu_kifayah', 'penggali_kubur']) && !empty($entitasData['nik'])) {
                            $personilModel = new PersonilModel();
                            $sameNikEntities = $personilModel->where('nik', $entitasData['nik'])
                                                             ->where('id !=', $entitasData['id'])
                                                             ->findAll();
                            
                            foreach ($sameNikEntities as $sibling) {
                                // Cek apakah sibling ini sudah punya entri berkas untuk tipe ini ($namaBerkas)
                                $siblingBerkas = $this->berkasModel->where('entitas_type', $sibling['entitas_type'])
                                                                   ->where('entitas_id', $sibling['id'])
                                                                   ->where('nama_berkas', $namaBerkas)
                                                                   ->first();
                                if ($siblingBerkas) {
                                    // Update nama_file agar menunjuk ke file yang baru
                                    $this->berkasModel->update($siblingBerkas['id'], ['nama_file' => $fileName]);
                                } else {
                                    // Insert cross-reference pointer baru
                                    $this->berkasModel->insert([
                                        'entitas_type' => $sibling['entitas_type'],
                                        'entitas_id'   => $sibling['id'],
                                        'nama_berkas'  => $namaBerkas,
                                        'nama_file'    => $fileName,
                                        'status'       => 1,
                                    ]);
                                }
                            }
                        }

                        return $this->response->setJSON([
                            'success' => true,
                            'message' => 'Berkas berhasil diupload',
                            'data'    => [
                                'id'        => $newId,
                                'nama_file' => $fileName,
                                'url'       => base_url('uploads/berkas/' . $fileName),
                            ]
                        ]);
                } else {
                    return $this->response->setJSON(['success' => false, 'message' => 'Format data gambar tidak valid']);
                }
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada data gambar yang dikirim']);
            }
        } catch (\Exception $e) {
            log_message('error', 'BerkasController::upload - Error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // ============================================================
    // Delete Berkas
    // ============================================================

    public function delete($id)
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Request harus menggunakan AJAX']);
            }

            $berkas = $this->berkasModel->find($id);
            if (!$berkas) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data berkas tidak ditemukan']);
            }

            // --- [NEW] REFERENCE COUNTING / SAFE UNLINK ---
            // Cek berapa entitas yang "memakai" file fisik ini
            $usageCount = $this->berkasModel->where('nama_file', $berkas['nama_file'])->countAllResults();

            if ($usageCount <= 1) {
                // Hapus file fisik HANYA JIKA ini adalah satu-satunya entri terakhir yang memakai file tersebut
                $filePath = FCPATH . 'uploads/berkas/' . $berkas['nama_file'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                $thumbPath = FCPATH . 'uploads/berkas/thumbs/' . $berkas['nama_file'];
                if (file_exists($thumbPath)) {
                    @unlink($thumbPath);
                }
            }

            // Selalu hapus record di DB untuk entitas peminta ini
            $this->berkasModel->delete($id);

            return $this->response->setJSON(['success' => true, 'message' => 'Berkas berhasil dihapus']);
        } catch (\Exception $e) {
            log_message('error', 'BerkasController::delete - Error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // ============================================================
    // Get Berkas By ID (for AJAX — modal edit)
    // ============================================================

    public function getById($id)
    {
        try {
            $berkas = $this->berkasModel->find($id);
            if (!$berkas) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data berkas tidak ditemukan']);
            }

            $berkas['url'] = base_url('uploads/berkas/' . $berkas['nama_file']);

            return $this->response->setJSON(['success' => true, 'data' => $berkas]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // ============================================================
    // Upload Foto Profil (update kolom 'foto' di tabel entitas)
    // ============================================================

    public function uploadProfil()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Request harus menggunakan AJAX']);
            }

            $entitasType   = $this->request->getPost('entitas_type');
            $entitasId     = (int) $this->request->getPost('entitas_id');
            $profilCropped = $this->request->getPost('profil_cropped');
            $syncAll       = $this->request->getPost('sync_all');

            // Validasi entitas_type
            $entitas = $this->resolveEntitas($entitasType);
            if (!$entitas) {
                return $this->response->setJSON(['success' => false, 'message' => 'Tipe entitas tidak valid']);
            }

            if (empty($entitasId)) {
                return $this->response->setJSON(['success' => false, 'message' => 'ID entitas tidak tersedia']);
            }

            $config = $entitas['config'];
            $model  = $entitas['modelInstance'];

            // Cek entitas ada
            $entitasData = $model->find($entitasId);
            if (!$entitasData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data entitas tidak ditemukan']);
            }

            // Buat direktori jika belum ada
            $uploadDir = FCPATH . $config['fotoDir'] . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $thumbDir = $uploadDir . 'thumbs/';
            if (!is_dir($thumbDir)) {
                mkdir($thumbDir, 0755, true);
            }

            if (!empty($profilCropped)) {
                if (preg_match('/^data:image\/(\w+);base64,/', $profilCropped, $type)) {
                    $profilCropped = substr($profilCropped, strpos($profilCropped, ',') + 1);
                    $type = strtolower($type[1]);

                    if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Format gambar tidak didukung']);
                    }

                    $profilCropped = base64_decode($profilCropped);
                    if ($profilCropped === false) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses gambar']);
                    }

                    // Generate nama file
                    $fileName = $entitasType . '_profil_' . $entitasId . '_' . time() . '.' . $type;
                    $filePath = $uploadDir . $fileName;

                    // --- [NEW] REFERENCE COUNTING SEBELUM REPLACE FOTO PROFIL LAMA ---
                    $fotoField = $config['fotoField'];
                    $oldFoto = $entitasData[$fotoField] ?? null;
                    
                    if ($oldFoto) {
                        $usageCountOld = $model->where($fotoField, $oldFoto)->countAllResults();
                        
                        if ($usageCountOld <= 1) {
                            if (file_exists($uploadDir . $oldFoto)) {
                                @unlink($uploadDir . $oldFoto);
                            }
                            if (file_exists($thumbDir . $oldFoto)) {
                                @unlink($thumbDir . $oldFoto);
                            }
                        }
                    }

                    // Simpan file baru
                    if (!file_put_contents($filePath, $profilCropped)) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan file']);
                    }

                    // Generate Thumbnail
                    try {
                        $image = \Config\Services::image();
                        $image->withFile($filePath)
                              ->resize(300, 300, true, 'auto')
                              ->save($thumbDir . $fileName, 75);
                    } catch (\Exception $e) {
                        log_message('error', 'Gagal generate thumb profil: ' . $e->getMessage());
                    }

                    // Update kolom foto di tabel entitas peminta
                    $model->update($entitasId, [$fotoField => $fileName]);

                    // --- [NEW] GLOBAL FILE SHARING UPDATE (FOTO PROFIL) ---
                    if ($syncAll === 'true' && in_array($entitasType, ['mubaligh', 'imam_masjid', 'fardu_kifayah', 'penggali_kubur']) && !empty($entitasData['nik'])) {
                        $personilModel = new PersonilModel();
                        // Broadcast update ke seluruh entitas yang se-NIK
                        $personilModel->where('nik', $entitasData['nik'])
                                      ->where('id !=', $entitasData['id'])
                                      ->set([$fotoField => $fileName])
                                      ->update();
                    }

                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Foto profil berhasil diupload',
                        'data'    => [
                            'nama_file' => $fileName,
                            'url'       => base_url($config['fotoDir'] . '/' . $fileName),
                        ]
                    ]);
                } else {
                    return $this->response->setJSON(['success' => false, 'message' => 'Format data gambar tidak valid']);
                }
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada data gambar yang dikirim']);
            }
        } catch (\Exception $e) {
            log_message('error', 'BerkasController::uploadProfil - Error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // ============================================================
    // Delete Foto Profil
    // ============================================================

    public function deleteProfil()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Request harus menggunakan AJAX']);
            }

            $entitasType = $this->request->getPost('entitas_type');
            $entitasId   = (int) $this->request->getPost('entitas_id');

            $entitas = $this->resolveEntitas($entitasType);
            if (!$entitas) {
                return $this->response->setJSON(['success' => false, 'message' => 'Tipe entitas tidak valid']);
            }

            $config = $entitas['config'];
            $model  = $entitas['modelInstance'];

            $entitasData = $model->find($entitasId);
            if (!$entitasData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data entitas tidak ditemukan']);
            }

            $fotoField = $config['fotoField'];
            $oldFoto = $entitasData[$fotoField] ?? null;

            if ($oldFoto) {
                // --- [NEW] REFERENCE COUNTING / SAFE UNLINK (FOTO PROFIL) ---
                $usageCount = $model->where($fotoField, $oldFoto)->countAllResults();
                
                if ($usageCount <= 1) {
                    $filePath = FCPATH . $config['fotoDir'] . '/' . $oldFoto;
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                    $thumbPath = FCPATH . $config['fotoDir'] . '/thumbs/' . $oldFoto;
                    if (file_exists($thumbPath)) {
                        @unlink($thumbPath);
                    }
                }
            }

            // Set foto to null
            $model->update($entitasId, [$fotoField => null]);

            return $this->response->setJSON(['success' => true, 'message' => 'Foto profil berhasil dihapus']);
        } catch (\Exception $e) {
            log_message('error', 'BerkasController::deleteProfil - Error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
