<?php
/**
 * _berkas_helper.php — Reusable PHP partial for berkas lampiran modals.
 * 
 * Include this partial in any berkasLampiran view.
 * Required: set $berkasConfig array before including, containing:
 *   - entitasType  : string (e.g., 'mubaligh')
 *   - tipeBerkas   : array  (e.g., ['KTP', 'KK'])
 *   - labelEntitas : string (e.g., 'Mubaligh')
 * 
 * Usage:
 *   <?php $berkasConfig = ['entitasType' => 'mubaligh', 'tipeBerkas' => ['KTP', 'KK'], 'labelEntitas' => 'Mubaligh']; ?>
 *   <?= $this->include('backend/partials/_berkas_helper') ?>
 */

$entitasType  = $berkasConfig['entitasType'] ?? 'unknown';
$tipeBerkas   = $berkasConfig['tipeBerkas'] ?? ['KTP', 'KK'];
$labelEntitas = $berkasConfig['labelEntitas'] ?? 'Data';
?>

<!-- Cropper.js CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>

<!-- ====================================================================== -->
<!-- Modal Upload Berkas -->
<!-- ====================================================================== -->
<div class="modal fade" id="modalBerkasUpload" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 900px;">
        <div class="modal-content" style="max-height: 90vh; display: flex; flex-direction: column;">
            <div class="modal-header" style="flex-shrink: 0;">
                <h5 class="modal-title">Upload Berkas Lampiran</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="flex: 1; overflow-y: auto; min-height: 0;">
                <form id="formBerkasUpload">
                    <input type="hidden" id="berkasUploadEntitasId" name="entitas_id">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Nama <?= esc($labelEntitas) ?></label>
                                <input type="text" id="berkasUploadNamaEntitas" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="berkasUploadNamaBerkas">Tipe Berkas <span class="text-danger">*</span></label>
                                <select class="form-control" id="berkasUploadNamaBerkas" name="nama_berkas" required>
                                    <option value="">Pilih Tipe Berkas</option>
                                    <?php foreach ($tipeBerkas as $tipe): ?>
                                        <option value="<?= esc($tipe) ?>"><?= esc($tipe) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="berkasUploadRekeningGroup" style="display: none;">
                        <label for="berkasUploadNoRekening">Nomor Rekening Bank <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="berkasUploadNoRekening" name="no_rekening" placeholder="Misal: 1234567890">
                        <small class="form-text text-muted" id="berkasUploadRekeningHelp">Dokumen ini mensyaratkan Nomor Rekening Bank. Silakan masukkan nomor rekening yang tertera.</small>
                    </div>
                    <div class="form-group">
                        <label for="berkasFileBerkas">File Berkas <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file" id="berkasFileBerkas" accept="image/jpeg,image/jpg,image/png">
                        <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maksimal 15MB.</small>
                        <small class="form-text text-info" id="berkasEditModeInfo" style="display: none;">
                            <i class="fas fa-info-circle"></i> Kosongkan jika tidak ingin mengganti gambar
                        </small>
                    </div>
                    <div id="berkasExistingImageContainer" style="display: none;">
                        <label>Gambar Saat Ini</label>
                        <div class="text-center mb-2" style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; min-height: 200px; max-height: 250px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <img id="berkasExistingImage" src="" alt="Gambar Saat Ini" style="max-width: 100%; max-height: 230px; object-fit: contain;">
                        </div>
                    </div>
                    <div id="berkasPreviewContainer" style="display: none;">
                        <label>Preview Hasil Crop</label>
                        <div class="text-center mb-2" style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; min-height: 200px; max-height: 250px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <img id="berkasPreviewImage" src="" alt="Preview" style="max-width: 100%; max-height: 230px; object-fit: contain;">
                        </div>
                    </div>
                    <input type="hidden" id="berkasCroppedImageData" name="berkas_cropped">
                </form>
            </div>
            <div class="modal-footer" style="flex-shrink: 0;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-info" id="berkasBtnUseExisting" style="display: none;">Gunakan Gambar Saat Ini</button>
                <button type="button" class="btn btn-primary" id="berkasBtnUploadFromForm" style="display: none;" onclick="berkasHelper.uploadBerkasFromForm()">Upload</button>
            </div>
        </div>
    </div>
</div>

<!-- ====================================================================== -->
<!-- Modal Crop Berkas -->
<!-- ====================================================================== -->
<div class="modal fade" id="modalBerkasCrop" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 95%; margin: 10px auto;">
        <div class="modal-content" style="height: calc(100vh - 20px); display: flex; flex-direction: column;">
            <div class="modal-header" style="flex-shrink: 0;">
                <h5 class="modal-title">Crop Berkas</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="alert alert-warning m-0" style="flex-shrink: 0; border-radius: 0; padding: 10px 15px; margin-bottom: 0 !important;">
                <small>
                    <i class="fas fa-info-circle"></i> <strong>Panduan:</strong>
                    Geser (drag) untuk memindahkan area crop • Resize untuk mengubah ukuran •
                    Gunakan tombol Putar Kiri/Kanan untuk memutar gambar •
                    Aspect ratio sudah fixed sesuai jenis berkas •
                    Klik <strong>Selesai</strong> untuk menyimpan
                </small>
            </div>
            <div class="modal-body" style="flex: 1; overflow: hidden; padding: 15px; display: flex; align-items: center; justify-content: center;">
                <div id="berkasCropContainer" style="width: 100%; height: 100%; max-height: calc(100vh - 200px); overflow: hidden; display: flex; align-items: center; justify-content: center; position: relative;">
                    <img id="berkasImageToCrop" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
            </div>
            <div class="modal-footer" style="flex-shrink: 0;">
                <div class="mr-auto">
                    <button type="button" class="btn btn-info btn-sm" id="berkasBtnRotateLeft" title="Putar 90° ke kiri">
                        <i class="fas fa-undo"></i> Putar Kiri
                    </button>
                    <button type="button" class="btn btn-info btn-sm" id="berkasBtnRotateRight" title="Putar 90° ke kanan">
                        <i class="fas fa-redo"></i> Putar Kanan
                    </button>
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="berkasBtnCropDone">Selesai</button>
            </div>
        </div>
    </div>
</div>

<!-- ====================================================================== -->
<!-- Modal Edit Berkas -->
<!-- ====================================================================== -->
<div class="modal fade" id="modalBerkasEdit" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 900px;">
        <div class="modal-content" style="max-height: 90vh; display: flex; flex-direction: column;">
            <div class="modal-header" style="flex-shrink: 0;">
                <h5 class="modal-title">Edit Berkas Lampiran</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="flex: 1; overflow-y: auto; min-height: 0;">
                <form id="formBerkasEdit">
                    <input type="hidden" id="berkasEditBerkasId" name="edit_berkas_id">
                    <input type="hidden" id="berkasEditEntitasId" name="entitas_id">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Nama <?= esc($labelEntitas) ?></label>
                                <input type="text" id="berkasEditNamaEntitas" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label>Tipe Berkas</label>
                                <input type="text" id="berkasEditNamaBerkas" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="berkasEditRekeningGroup" style="display: none;">
                        <label for="berkasEditNoRekening">Nomor Rekening Bank <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="berkasEditNoRekening" name="no_rekening" placeholder="Misal: 1234567890">
                        <small class="form-text text-muted" id="berkasEditRekeningHelp">Dokumen ini mensyaratkan Nomor Rekening Bank. Silakan masukkan nomor rekening yang tertera.</small>
                    </div>
                    <div class="form-group">
                        <label for="berkasEditFileBerkas">Ganti dengan File Baru</label>
                        <div class="custom-file-wrapper" style="position: relative;">
                            <input type="file" class="form-control-file" id="berkasEditFileBerkas" accept="image/jpeg,image/jpg,image/png" style="position: absolute; opacity: 0; width: 0; height: 0; overflow: hidden;">
                            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('berkasEditFileBerkas').click()">
                                <i class="fas fa-upload"></i> Pilih File
                            </button>
                            <span id="berkasEditFileNameDisplay" class="ml-2" style="font-size: 14px; color: #666;"></span>
                        </div>
                        <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maksimal 15MB. Kosongkan jika tidak ingin mengganti.</small>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Gambar Saat Ini</label>
                                <div class="text-center mb-2" style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; min-height: 200px; max-height: 250px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <img id="berkasEditExistingImage" src="" alt="Gambar Saat Ini" style="max-width: 100%; max-height: 230px; object-fit: contain;">
                                </div>
                                <div class="text-center">
                                    <button type="button" class="btn btn-sm btn-warning" id="berkasBtnCropExistingImage" onclick="berkasHelper.cropExistingImageInEdit()">
                                        <i class="fas fa-edit"></i> Edit Gambar saat ini
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Preview Hasil Edit</label>
                                <div id="berkasEditPreviewContainer" style="display: none;">
                                    <div class="text-center mb-2" style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; min-height: 200px; max-height: 250px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        <img id="berkasEditPreviewImage" src="" alt="Preview" style="max-width: 100%; max-height: 230px; object-fit: contain;">
                                    </div>
                                </div>
                                <div id="berkasEditPreviewPlaceholder" class="text-center" style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; min-height: 200px; max-height: 250px; display: flex; align-items: center; justify-content: center; color: #999;">
                                    <div>
                                        <i class="fas fa-image" style="font-size: 48px; margin-bottom: 10px;"></i>
                                        <p class="mb-0">Belum ada preview</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="berkasEditCroppedImageData" name="berkas_cropped">
                </form>
            </div>
            <div class="modal-footer" style="flex-shrink: 0;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="berkasBtnUpdateBerkas" style="display: none;" onclick="berkasHelper.updateBerkasFromForm()">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- ====================================================================== -->
<!-- Modal Image Preview -->
<!-- ====================================================================== -->
<div class="modal fade" id="modalBerkasImagePreview" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Gambar</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div class="text-center mb-3">
                    <img id="berkasPreviewEnlargedImage" src="" alt="Preview" style="max-width: 100%; max-height: 70vh; height: auto; border: 1px solid #ddd; border-radius: 4px;">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ====================================================================== -->
<!-- Modal Upload Profil -->
<!-- ====================================================================== -->
<div class="modal fade" id="modalBerkasUploadProfil" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 900px;">
        <div class="modal-content" style="max-height: 90vh; display: flex; flex-direction: column;">
            <div class="modal-header" style="flex-shrink: 0;">
                <h5 class="modal-title">Upload Foto Profil <?= esc($labelEntitas) ?></h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="flex: 1; overflow-y: auto; min-height: 0;">
                <form id="formBerkasUploadProfil">
                    <input type="hidden" id="berkasProfilEntitasId" name="entitas_id">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Nama <?= esc($labelEntitas) ?></label>
                                <input type="text" id="berkasProfilNamaEntitas" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="berkasFileProfil">File Foto Profil <span class="text-danger">*</span></label>
                                <input type="file" class="form-control-file" id="berkasFileProfil" accept="image/jpeg,image/jpg,image/png">
                                <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maksimal 10MB. Rasio 3:4</small>
                            </div>
                        </div>
                    </div>
                    <div id="berkasProfilExistingImageContainer" style="display: none;">
                        <label>Foto Saat Ini</label>
                        <div class="text-center mb-2" style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; min-height: 200px; max-height: 350px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <img id="berkasProfilExistingImage" src="" alt="Foto Saat Ini" style="max-width: 100%; max-height: 330px; object-fit: contain;">
                        </div>
                    </div>
                    <div id="berkasProfilPreviewContainer" style="display: none;">
                        <label>Preview Hasil Crop</label>
                        <div class="text-center mb-2" style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; min-height: 200px; max-height: 350px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <img id="berkasProfilPreviewImage" src="" alt="Preview" style="max-width: 100%; max-height: 330px; object-fit: contain;">
                        </div>
                    </div>
                    <input type="hidden" id="berkasProfilCroppedImageData" name="profil_cropped">
                </form>
            </div>
            <div class="modal-footer" style="flex-shrink: 0;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="berkasBtnUploadProfilFromForm" style="display: none;" onclick="berkasHelper.uploadProfilFromForm()">Upload</button>
            </div>
        </div>
    </div>
</div>

<!-- ====================================================================== -->
<!-- Modal Crop Profil -->
<!-- ====================================================================== -->
<div class="modal fade" id="modalBerkasCropProfil" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 95%; margin: 10px auto;">
        <div class="modal-content" style="height: calc(100vh - 20px); display: flex; flex-direction: column;">
            <div class="modal-header" style="flex-shrink: 0;">
                <h5 class="modal-title">Crop Foto Profil (Rasio 3:4)</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="alert alert-info m-0" style="flex-shrink: 0; border-radius: 0; padding: 10px 15px; margin-bottom: 0 !important;">
                <small>
                    <i class="fas fa-info-circle"></i> <strong>Panduan:</strong>
                    Geser (drag) untuk memindahkan area crop • Resize untuk mengubah ukuran •
                    Gunakan tombol Putar Kiri/Kanan untuk memutar gambar •
                    Aspect ratio fixed 3:4 •
                    Klik <strong>Selesai</strong> untuk menyimpan
                </small>
            </div>
            <div class="modal-body" style="flex: 1; overflow: hidden; padding: 15px; display: flex; align-items: center; justify-content: center;">
                <div id="berkasCropProfilContainer" style="width: 100%; height: 100%; max-height: calc(100vh - 200px); overflow: hidden; display: flex; align-items: center; justify-content: center; position: relative;">
                    <img id="berkasImageToCropProfil" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
            </div>
            <div class="modal-footer" style="flex-shrink: 0;">
                <div class="mr-auto">
                    <button type="button" class="btn btn-info btn-sm" id="berkasBtnProfilRotateLeft" title="Putar 90° ke kiri">
                        <i class="fas fa-undo"></i> Putar Kiri
                    </button>
                    <button type="button" class="btn btn-info btn-sm" id="berkasBtnProfilRotateRight" title="Putar 90° ke kanan">
                        <i class="fas fa-redo"></i> Putar Kanan
                    </button>
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="berkasBtnCropProfilDone">Selesai</button>
            </div>
        </div>
    </div>
</div>

<!-- ====================================================================== -->
<!-- CSS Styles for Berkas Modals -->
<!-- ====================================================================== -->
<style>
    /* Crop modal styles */
    #modalBerkasCrop .modal-body { min-height: 0; }
    #berkasCropContainer { position: relative; }
    #berkasImageToCrop { max-width: 100%; max-height: 100%; object-fit: contain; }

    /* Responsive */
    @media (max-height: 600px) {
        #modalBerkasCrop .modal-content { height: calc(100vh - 10px); }
        #berkasCropContainer { max-height: calc(100vh - 150px); }
    }
</style>
