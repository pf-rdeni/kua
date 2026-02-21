<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\BerkasModel;
use App\Models\MubalighModel;
use App\Models\ImamMasjidModel;
use App\Models\FarduKifayahModel;
use App\Models\PenggaliKuburModel;
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
        'mubaligh' => [
            'model'     => MubalighModel::class,
            'pk'        => 'id_mubaligh',
            'fotoField' => 'foto',
            'fotoDir'   => 'uploads/mubaligh',
        ],
        'imam_masjid' => [
            'model'     => ImamMasjidModel::class,
            'pk'        => 'id_imam_masjid',
            'fotoField' => 'foto',
            'fotoDir'   => 'uploads/imam_masjid',
        ],
        'fardu_kifayah' => [
            'model'     => FarduKifayahModel::class,
            'pk'        => 'id_fardu_kifayah',
            'fotoField' => 'foto',
            'fotoDir'   => 'uploads/fardu_kifayah',
        ],
        'penggali_kubur' => [
            'model'     => PenggaliKuburModel::class,
            'pk'        => 'id_penggali_kubur',
            'fotoField' => 'foto',
            'fotoDir'   => 'uploads/penggali_kubur',
        ],
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
                        // Hapus file lama
                        $oldFilePath = $uploadPath . $berkasToUpdate['nama_file'];
                        if (file_exists($oldFilePath)) {
                            @unlink($oldFilePath);
                        }
                        $oldThumbPath = $thumbPath . $berkasToUpdate['nama_file'];
                        if (file_exists($oldThumbPath)) {
                            @unlink($oldThumbPath);
                        }

                        // Update record
                        $this->berkasModel->update($editBerkasId, [
                            'nama_file' => $fileName,
                            'status'    => 1,
                        ]);

                        return $this->response->setJSON([
                            'success' => true,
                            'message' => 'Berkas berhasil diperbarui',
                            'data'    => [
                                'id'        => $editBerkasId,
                                'nama_file' => $fileName,
                                'url'       => base_url('uploads/berkas/' . $fileName),
                            ]
                        ]);
                    } else {
                        // Insert record baru
                        $this->berkasModel->insert([
                            'entitas_type' => $entitasType,
                            'entitas_id'   => $entitasId,
                            'nama_berkas'  => $namaBerkas,
                            'nama_file'    => $fileName,
                            'status'       => 1,
                        ]);

                        $newId = $this->berkasModel->getInsertID();

                        return $this->response->setJSON([
                            'success' => true,
                            'message' => 'Berkas berhasil diupload',
                            'data'    => [
                                'id'        => $newId,
                                'nama_file' => $fileName,
                                'url'       => base_url('uploads/berkas/' . $fileName),
                            ]
                        ]);
                    }
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

            // Hapus file fisik
            $filePath = FCPATH . 'uploads/berkas/' . $berkas['nama_file'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            $thumbPath = FCPATH . 'uploads/berkas/thumbs/' . $berkas['nama_file'];
            if (file_exists($thumbPath)) {
                @unlink($thumbPath);
            }

            // Delete record
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

            $entitasType = $this->request->getPost('entitas_type');
            $entitasId   = (int) $this->request->getPost('entitas_id');
            $profilCropped = $this->request->getPost('profil_cropped');

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

                    // Hapus foto lama
                    $fotoField = $config['fotoField'];
                    $oldFoto = $entitasData[$fotoField] ?? null;
                    if ($oldFoto && file_exists($uploadDir . $oldFoto)) {
                        @unlink($uploadDir . $oldFoto);
                    }
                    if ($oldFoto && file_exists($thumbDir . $oldFoto)) {
                        @unlink($thumbDir . $oldFoto);
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

                    // Update kolom foto di tabel entitas
                    $model->update($entitasId, [$fotoField => $fileName]);

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
                $filePath = FCPATH . $config['fotoDir'] . '/' . $oldFoto;
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                $thumbPath = FCPATH . $config['fotoDir'] . '/thumbs/' . $oldFoto;
                if (file_exists($thumbPath)) {
                    @unlink($thumbPath);
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
