/**
 * BerkasHelper — Reusable JavaScript helper untuk upload, crop, edit, dan delete berkas lampiran.
 * Menggunakan Cropper.js, SweetAlert2, dan jQuery AJAX.
 * 
 * Usage:
 *   const helper = new BerkasHelper({
 *       entitasType: 'mubaligh',
 *       tipeBerkas: ['KTP', 'KK'],
 *       uploadUrl: baseUrl + '/admin/berkas/upload',
 *       deleteUrl: baseUrl + '/admin/berkas/delete',
 *       getUrl: baseUrl + '/admin/berkas/get',
 *       profilUrl: baseUrl + '/admin/berkas/upload-profil',
 *       deleteProfilUrl: baseUrl + '/admin/berkas/delete-profil',
 *       berkasFileUrl: baseUrl + '/uploads/berkas/',
 *       profilDir: 'uploads/mubaligh',
 *   });
 */

class BerkasHelper {
    constructor(config) {
        this.config = config;
        this.cropperBerkas = null;
        this.cropperProfil = null;
        this.currentEntitasId = null;
        this.currentEditBerkasData = null;
        this.savedCropNamaBerkas = null;

        this._bindEvents();
    }

    // ============================================================
    // Event Bindings
    // ============================================================
    _bindEvents() {
        const self = this;

        // Image preview: click to enlarge, double-click open new tab
        $(document).on('click', '.preview-image', function (e) {
            const imageUrl = $(this).data('image-url');
            if (!imageUrl) return;

            const $img = $(this);
            if ($img.data('clickTimer')) {
                clearTimeout($img.data('clickTimer'));
                $img.removeData('clickTimer');
                window.open(imageUrl, '_blank');
            } else {
                const timer = setTimeout(function () {
                    $('#berkasPreviewEnlargedImage').attr('src', imageUrl);
                    $('#modalBerkasImagePreview').modal('show');
                    $img.removeData('clickTimer');
                }, 250);
                $img.data('clickTimer', timer);
            }
        });

        // File input change — berkas
        $('#berkasFileBerkas').on('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file size (max 15MB)
            if (file.size > 15728640) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Ukuran file terlalu besar. Maksimal 15MB.' });
                $(this).val('');
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tipe file tidak diizinkan. Hanya JPG, JPEG, PNG.' });
                $(this).val('');
                return;
            }

            // Validate tipe berkas selected
            const namaBerkas = $('#berkasUploadNamaBerkas').val();
            if (!namaBerkas) {
                Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Silakan pilih Tipe Berkas terlebih dahulu' });
                $(this).val('');
                return;
            }

            self.savedCropNamaBerkas = namaBerkas;

            // Hide existing image
            $('#berkasExistingImageContainer').hide();
            $('#berkasBtnUseExisting').hide();
            $('#berkasPreviewContainer').hide();
            $('#berkasBtnUploadFromForm').hide();
            $('#berkasCroppedImageData').val('');

            const reader = new FileReader();
            reader.onload = function (ev) {
                self._showCropModal(ev.target.result, namaBerkas);
            };
            reader.readAsDataURL(file);
        });

        // File input change — edit berkas
        $('#berkasEditFileBerkas').on('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            if (file.size > 15728640) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Ukuran file terlalu besar. Maksimal 15MB.' });
                $(this).val('');
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tipe file tidak diizinkan. Hanya JPG, JPEG, PNG.' });
                $(this).val('');
                return;
            }

            $('#berkasEditFileNameDisplay').text(file.name);

            const namaBerkas = self.savedCropNamaBerkas || $('#berkasEditNamaBerkas').val();

            const reader = new FileReader();
            reader.onload = function (ev) {
                self._showCropModal(ev.target.result, namaBerkas);
            };
            reader.readAsDataURL(file);
        });

        // File input change — profil
        $('#berkasFileProfil').on('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            if (file.size > 10485760) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Ukuran file terlalu besar. Maksimal 10MB.' });
                $(this).val('');
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tipe file tidak diizinkan. Hanya JPG, JPEG, PNG.' });
                $(this).val('');
                return;
            }

            $('#berkasProfilExistingImageContainer').hide();
            $('#berkasProfilPreviewContainer').hide();

            const reader = new FileReader();
            reader.onload = function (ev) {
                self._showCropProfilModal(ev.target.result);
            };
            reader.readAsDataURL(file);
        });

        // Rotate buttons — berkas
        $('#berkasBtnRotateLeft').on('click', function () {
            if (self.cropperBerkas) {
                self.cropperBerkas.rotate(-90);
                self._adjustCropBoxAfterRotate();
            }
        });

        $('#berkasBtnRotateRight').on('click', function () {
            if (self.cropperBerkas) {
                self.cropperBerkas.rotate(90);
                self._adjustCropBoxAfterRotate();
            }
        });

        // Rotate buttons — profil
        $('#berkasBtnProfilRotateLeft').on('click', function () {
            if (self.cropperProfil) self.cropperProfil.rotate(-90);
        });

        $('#berkasBtnProfilRotateRight').on('click', function () {
            if (self.cropperProfil) self.cropperProfil.rotate(90);
        });

        // Crop done — berkas
        $('#berkasBtnCropDone').on('click', function () {
            self._onCropDone();
        });

        // Crop done — profil
        $('#berkasBtnCropProfilDone').on('click', function () {
            self._onCropProfilDone();
        });
    }

    // ============================================================
    // Public Methods — Called from view via onclick
    // ============================================================

    /**
     * Open upload modal with a specific berkas type pre-selected
     */
    openUploadModal(entitasId, namaEntitas, namaBerkas, argAspectRatioWidth = null, argAspectRatioHeight = null) {
        this.currentEntitasId = entitasId;
        this.currentEditBerkasData = null;
        this.savedCropNamaBerkas = namaBerkas;
        this.currentAspectRatio = (argAspectRatioWidth && argAspectRatioHeight) ? argAspectRatioWidth / argAspectRatioHeight : NaN;

        $('#berkasUploadEntitasId').val(entitasId);
        $('#berkasUploadNamaEntitas').val(namaEntitas);

        // Dynamically add the option if it doesn't exist
        if ($('#berkasUploadNamaBerkas option').filter(function () { return $(this).val() == namaBerkas; }).length === 0) {
            $('#berkasUploadNamaBerkas').append(new Option(namaBerkas, namaBerkas));
        }

        $('#berkasUploadNamaBerkas').val(namaBerkas);
        $('#berkasUploadNamaBerkas').prop('disabled', true);

        // Reset
        $('#berkasFileBerkas').val('');
        $('#berkasPreviewContainer').hide();
        $('#berkasBtnUploadFromForm').hide();
        $('#berkasExistingImageContainer').hide();
        $('#berkasBtnUseExisting').hide();
        $('#berkasEditModeInfo').hide();
        $('#berkasCroppedImageData').val('');

        if (this.cropperBerkas) {
            this.cropperBerkas.destroy();
            this.cropperBerkas = null;
        }

        $('#modalBerkasUpload').modal('show');
    }

    /**
     * Open edit modal for existing berkas
     */
    editBerkas(berkasId, entitasId, namaEntitas, namaBerkas, argAspectRatioWidth = null, argAspectRatioHeight = null) {
        const self = this;
        this.currentEntitasId = entitasId;
        this.savedCropNamaBerkas = namaBerkas;
        this.currentAspectRatio = (argAspectRatioWidth && argAspectRatioHeight) ? argAspectRatioWidth / argAspectRatioHeight : NaN;

        // Fetch berkas data
        $.ajax({
            url: this.config.getUrl + '/' + berkasId,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    self.currentEditBerkasData = response.data;

                    $('#berkasEditBerkasId').val(berkasId);
                    $('#berkasEditEntitasId').val(entitasId);
                    $('#berkasEditNamaEntitas').val(namaEntitas);
                    $('#berkasEditNamaBerkas').val(namaBerkas);

                    // Show existing image
                    $('#berkasEditExistingImage').attr('src', response.data.url);
                    $('#berkasEditPreviewContainer').hide();
                    $('#berkasEditPreviewPlaceholder').show();
                    $('#berkasBtnUpdateBerkas').hide();
                    $('#berkasBtnCropExistingImage').show();
                    $('#berkasEditFileBerkas').val('');
                    $('#berkasEditFileNameDisplay').text('');
                    $('#berkasEditCroppedImageData').val('');

                    $('#modalBerkasEdit').modal('show');
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message });
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal mengambil data berkas' });
            }
        });
    }

    /**
     * Delete berkas with SweetAlert confirmation
     */
    deleteBerkas(berkasId) {
        const self = this;
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Berkas akan dihapus secara permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: self.config.deleteUrl + '/' + berkasId,
                    type: 'POST',
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message, timer: 1500, showConfirmButton: false })
                                .then(() => location.reload());
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                        }
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat menghapus berkas' });
                    }
                });
            }
        });
    }

    /**
     * Open upload profil modal
     */
    openUploadProfilModal(entitasId, namaEntitas, existingFotoUrl = null) {
        this.currentEntitasId = entitasId;

        $('#berkasProfilEntitasId').val(entitasId);
        $('#berkasProfilNamaEntitas').val(namaEntitas);
        $('#berkasFileProfil').val('');
        $('#berkasProfilPreviewContainer').hide();
        $('#berkasBtnUploadProfilFromForm').hide();
        $('#berkasProfilCroppedImageData').val('');

        if (existingFotoUrl) {
            $('#berkasProfilExistingImage').attr('src', existingFotoUrl);
            $('#berkasProfilExistingImageContainer').show();
        } else {
            $('#berkasProfilExistingImageContainer').hide();
        }

        if (this.cropperProfil) {
            this.cropperProfil.destroy();
            this.cropperProfil = null;
        }

        $('#modalBerkasUploadProfil').modal('show');
    }

    /**
     * Edit existing profil (open crop modal with existing image)
     */
    editProfil(entitasId, namaEntitas, existingFotoUrl) {
        this.openUploadProfilModal(entitasId, namaEntitas, existingFotoUrl);
    }

    /**
     * Delete profil foto
     */
    deleteProfil(entitasId) {
        const self = this;
        Swal.fire({
            title: 'Yakin ingin menghapus foto profil?',
            text: 'Foto profil akan dihapus!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: self.config.deleteProfilUrl,
                    type: 'POST',
                    data: {
                        entitas_type: self.config.entitasType,
                        entitas_id: entitasId
                    },
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message, timer: 1500, showConfirmButton: false })
                                .then(() => location.reload());
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                        }
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat menghapus foto profil' });
                    }
                });
            }
        });
    }

    /**
     * Reusable Method: Cek apakah NIK punya entitas kembar sebelum Upload.
     * Mengembalikan Promise(boolean) -> true jika minta Sync, false jika tidak.
     */
    async _checkNikSharingWithPrompt(entitasId) {
        return new Promise((resolve) => {
            // Tampilkan loading sebentar selagi checking
            Swal.fire({
                title: 'Mengecek Data...',
                allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: baseUrl + '/admin/api/personil/check-nik-sharing',
                type: 'GET',
                data: {
                    entitas_type: this.config.entitasType,
                    entitas_id: entitasId
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success' && response.has_siblings) {
                        // Tutup loading, tampilkan pertanyaan
                        Swal.fire({
                            title: 'Entitas Ganda Terdeteksi!',
                            html: `NIK ini juga terdaftar sebagai: <b>${response.siblingRoles.join(', ')}</b>.<br><br>Apakah Anda ingin file ini ikut dipasang ke entitas tersebut (Sinkronisasi), atau hanya untuk data saat ini saja?`,
                            icon: 'question',
                            showCancelButton: true,
                            showDenyButton: true,
                            showConfirmButton: true,
                            confirmButtonText: '<i class="fas fa-sync"></i> Ya, Sinkronkan Semua',
                            denyButtonText: '<i class="fas fa-file"></i> Hanya Data Ini Saja',
                            cancelButtonText: 'Batal Upload',
                            confirmButtonColor: '#28a745',
                            denyButtonColor: '#17a2b8',
                            cancelButtonColor: '#6c757d',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                resolve(true); // User wants to sync_all
                            } else if (result.isDenied) {
                                resolve(false); // User only wants this entity
                            } else {
                                resolve(null); // User cancelled the upload
                            }
                        });
                    } else {
                        // Jika tidak ada kembaran, langsung return false (tidak perlu sync_all) tanpa nanya
                        resolve(false);
                    }
                },
                error: function () {
                    // Jika API Error, anggap tidak usah sync dan jalan biasa tutup mata
                    resolve(false);
                }
            });
        });
    }

    /**
     * Upload berkas from form (after crop)
     */
    async uploadBerkasFromForm() {
        const self = this;
        const croppedImageData = $('#berkasCroppedImageData').val();

        if (!croppedImageData) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Tidak ada gambar. Silakan pilih file dan crop terlebih dahulu.' });
            return;
        }

        const namaBerkas = $('#berkasUploadNamaBerkas').val();
        if (!namaBerkas) {
            Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Silakan pilih Tipe Berkas' });
            return;
        }

        const entitasIdField = $('#berkasUploadEntitasId').val();
        const doSyncAll = await this._checkNikSharingWithPrompt(entitasIdField);

        if (doSyncAll === null) {
            // User aborted during confirmation
            return;
        }

        Swal.fire({
            title: 'Mengupload berkas...',
            text: 'Sedang memproses dan mengupload berkas...',
            allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: self.config.uploadUrl,
            type: 'POST',
            data: {
                entitas_type: self.config.entitasType,
                entitas_id: entitasIdField,
                nama_berkas: namaBerkas,
                berkas_cropped: croppedImageData,
                sync_all: doSyncAll ? 'true' : 'false'
            },
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (response) {
                Swal.close();
                if (response.success) {
                    $('#modalBerkasUpload').modal('hide');
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message, timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                }
            },
            error: function () {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat mengupload berkas' });
            }
        });
    }

    /**
     * Update berkas from edit form
     */
    async updateBerkasFromForm() {
        const self = this;
        const croppedImageData = $('#berkasEditCroppedImageData').val();

        if (!croppedImageData) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Tidak ada perubahan gambar. Silakan pilih file baru atau crop gambar saat ini.' });
            return;
        }

        const entitasIdField = $('#berkasEditEntitasId').val();
        const doSyncAll = await this._checkNikSharingWithPrompt(entitasIdField);

        if (doSyncAll === null) return;

        Swal.fire({
            title: 'Memperbarui berkas...',
            text: 'Sedang memproses dan memperbarui berkas...',
            allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: self.config.uploadUrl,
            type: 'POST',
            data: {
                entitas_type: self.config.entitasType,
                entitas_id: entitasIdField,
                nama_berkas: $('#berkasEditNamaBerkas').val(),
                berkas_cropped: croppedImageData,
                edit_berkas_id: $('#berkasEditBerkasId').val(),
                sync_all: doSyncAll ? 'true' : 'false'
            },
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (response) {
                Swal.close();
                if (response.success) {
                    $('#modalBerkasEdit').modal('hide');
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message, timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                }
            },
            error: function () {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat memperbarui berkas' });
            }
        });
    }

    /**
     * Upload profil from form
     */
    async uploadProfilFromForm() {
        const self = this;
        const croppedImageData = $('#berkasProfilCroppedImageData').val();

        if (!croppedImageData) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Tidak ada gambar. Silakan pilih file dan crop terlebih dahulu.' });
            return;
        }

        const entitasIdField = $('#berkasProfilEntitasId').val();
        const doSyncAll = await this._checkNikSharingWithPrompt(entitasIdField);

        if (doSyncAll === null) return;

        Swal.fire({
            title: 'Mengupload foto profil...',
            allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: self.config.profilUrl,
            type: 'POST',
            data: {
                entitas_type: self.config.entitasType,
                entitas_id: entitasIdField,
                profil_cropped: croppedImageData,
                sync_all: doSyncAll ? 'true' : 'false'
            },
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (response) {
                Swal.close();
                if (response.success) {
                    $('#modalBerkasUploadProfil').modal('hide');
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message, timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                }
            },
            error: function () {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat mengupload foto profil' });
            }
        });
    }

    /**
     * Crop existing image in edit modal
     */
    cropExistingImageInEdit() {
        const existingImageSrc = $('#berkasEditExistingImage').attr('src');
        if (!existingImageSrc) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Gambar tidak ditemukan' });
            return;
        }
        const namaBerkas = this.savedCropNamaBerkas || $('#berkasEditNamaBerkas').val();
        this._showCropModal(existingImageSrc, namaBerkas);
    }

    // ============================================================
    // Private Methods — Crop modals
    // ============================================================

    _showCropModal(imageUrl, namaBerkas) {
        const self = this;
        const imageElement = document.getElementById('berkasImageToCrop');

        if (this.cropperBerkas) {
            this.cropperBerkas.destroy();
            this.cropperBerkas = null;
        }

        window._berkasCurrentCropNamaBerkas = namaBerkas;

        const isFromEditModal = $('#modalBerkasEdit').is(':visible') || this.currentEditBerkasData;

        imageElement.src = imageUrl;

        if (isFromEditModal) {
            $('#modalBerkasEdit').modal('hide');
            $('#modalBerkasEdit').one('hidden.bs.modal', function () {
                $('#modalBerkasCrop').modal('show');
            });
        } else {
            $('#modalBerkasUpload').modal('hide');
            $('#modalBerkasUpload').one('hidden.bs.modal', function () {
                $('#modalBerkasCrop').modal('show');
            });
        }

        $('#modalBerkasCrop').off('shown.bs.modal').on('shown.bs.modal', function () {
            $('#berkasBtnRotateLeft').prop('disabled', true);
            $('#berkasBtnRotateRight').prop('disabled', true);

            if (self.cropperBerkas) {
                self.cropperBerkas.destroy();
                self.cropperBerkas = null;
            }

            const currentSrc = imageElement.src;
            imageElement.src = '';
            imageElement.src = currentSrc;

            imageElement.onload = function () {
                setTimeout(function () {
                    if (typeof Cropper === 'undefined') {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Library Cropper.js belum dimuat. Silakan refresh halaman.' });
                        return;
                    }

                    if (!imageElement.src || imageElement.offsetWidth === 0) return;

                    if (self.cropperBerkas) {
                        self.cropperBerkas.destroy();
                        self.cropperBerkas = null;
                    }

                    try {
                        const cropContainer = document.getElementById('berkasCropContainer');
                        if (cropContainer) {
                            const maxHeight = window.innerHeight - 200;
                            cropContainer.style.maxHeight = maxHeight + 'px';
                            cropContainer.style.height = maxHeight + 'px';
                        }

                        const aspectRatio = self.currentAspectRatio !== undefined ? self.currentAspectRatio : NaN;
                        window._berkasCurrentAspectRatio = aspectRatio;

                        self.cropperBerkas = new Cropper(imageElement, {
                            aspectRatio: aspectRatio,
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 0.8,
                            restore: false,
                            guides: true,
                            center: true,
                            highlight: false,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                            toggleDragModeOnDblclick: false,
                            responsive: true,
                            checkCrossOrigin: false,
                            minContainerWidth: 200,
                            minContainerHeight: 200,
                            ready: function () {
                                $('#berkasBtnRotateLeft').prop('disabled', false);
                                $('#berkasBtnRotateRight').prop('disabled', false);
                            }
                        });
                    } catch (error) {
                        console.error('Error initializing cropper:', error);
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menginisialisasi cropper: ' + error.message });
                    }
                }, 500);
            };

            if (imageElement.complete) {
                imageElement.onload();
            }
        });
    }

    _showCropProfilModal(imageUrl) {
        const self = this;
        const imageElement = document.getElementById('berkasImageToCropProfil');

        if (this.cropperProfil) {
            this.cropperProfil.destroy();
            this.cropperProfil = null;
        }

        imageElement.src = imageUrl;

        $('#modalBerkasUploadProfil').modal('hide');
        $('#modalBerkasUploadProfil').one('hidden.bs.modal', function () {
            $('#modalBerkasCropProfil').modal('show');
        });

        $('#modalBerkasCropProfil').off('shown.bs.modal').on('shown.bs.modal', function () {
            if (self.cropperProfil) {
                self.cropperProfil.destroy();
                self.cropperProfil = null;
            }

            const currentSrc = imageElement.src;
            imageElement.src = '';
            imageElement.src = currentSrc;

            imageElement.onload = function () {
                setTimeout(function () {
                    if (typeof Cropper === 'undefined') {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Library Cropper.js belum dimuat. Silakan refresh halaman.' });
                        return;
                    }
                    if (!imageElement.src || imageElement.offsetWidth === 0) return;

                    if (self.cropperProfil) {
                        self.cropperProfil.destroy();
                        self.cropperProfil = null;
                    }

                    try {
                        // Set container height explicitly (critical for getCroppedCanvas)
                        const cropContainer = document.getElementById('berkasCropProfilContainer');
                        if (cropContainer) {
                            const maxHeight = window.innerHeight - 200;
                            cropContainer.style.maxHeight = maxHeight + 'px';
                            cropContainer.style.height = maxHeight + 'px';
                        }

                        self.cropperProfil = new Cropper(imageElement, {
                            aspectRatio: 3 / 4, // Profil photo ratio
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 0.8,
                            restore: false,
                            guides: true,
                            center: true,
                            highlight: false,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                            toggleDragModeOnDblclick: false,
                            responsive: true,
                            checkCrossOrigin: false,
                            minContainerWidth: 200,
                            minContainerHeight: 200,
                            ready: function () {
                                console.log('Profil cropper initialized successfully');
                                $('#berkasBtnProfilRotateLeft').prop('disabled', false);
                                $('#berkasBtnProfilRotateRight').prop('disabled', false);
                            }
                        });
                    } catch (error) {
                        console.error('Error initializing profil cropper:', error);
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menginisialisasi cropper: ' + error.message });
                    }
                }, 500);
            };

            if (imageElement.complete) {
                imageElement.onload();
            }
        });
    }

    _onCropDone() {
        if (!this.cropperBerkas) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Cropper belum diinisialisasi' });
            return;
        }

        const canvas = this.cropperBerkas.getCroppedCanvas({
            maxWidth: 2000,
            maxHeight: 2000,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });

        if (!canvas) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal membuat canvas' });
            return;
        }

        const base64Image = canvas.toDataURL('image/jpeg', 0.85);
        const isFromEditModal = this.currentEditBerkasData || ($('#berkasEditNamaBerkas').val() && $('#berkasEditNamaBerkas').val() !== '');

        if (isFromEditModal) {
            $('#berkasEditCroppedImageData').val(base64Image);
            $('#berkasEditPreviewImage').attr('src', base64Image);
            $('#berkasEditPreviewContainer').show();
            $('#berkasEditPreviewPlaceholder').hide();
        } else {
            $('#berkasCroppedImageData').val(base64Image);
            $('#berkasPreviewImage').attr('src', base64Image);
        }

        // Hindari aria-hidden error di console Bootstrap
        $(':focus').blur();
        $('#modalBerkasCrop').modal('hide');

        const self = this;
        $('#modalBerkasCrop').one('hidden.bs.modal', function () {
            if (isFromEditModal) {
                $('#berkasEditPreviewContainer').show();
                $('#berkasEditPreviewPlaceholder').hide();
                $('#berkasBtnUpdateBerkas').show();
                $('#berkasBtnCropExistingImage').hide();

                setTimeout(function () {
                    $('#modalBerkasEdit').modal('show');
                }, 100);
            } else {
                const savedNamaBerkas = self.savedCropNamaBerkas;
                $('#berkasExistingImageContainer').hide();
                $('#berkasBtnUseExisting').hide();
                $('#berkasPreviewContainer').show();
                $('#berkasBtnUploadFromForm').show();

                setTimeout(function () {
                    if (savedNamaBerkas) {
                        $('#berkasUploadNamaBerkas').val(savedNamaBerkas);
                        $('#berkasUploadNamaBerkas').prop('disabled', true);
                    }
                    $('#modalBerkasUpload').modal('show');
                }, 100);
            }
        });
    }

    _onCropProfilDone() {
        if (!this.cropperProfil) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Cropper belum diinisialisasi' });
            return;
        }

        const canvas = this.cropperProfil.getCroppedCanvas({
            maxWidth: 800,
            maxHeight: 1067,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });

        if (!canvas) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal membuat canvas' });
            return;
        }

        const base64Image = canvas.toDataURL('image/jpeg', 0.85);

        $('#berkasProfilCroppedImageData').val(base64Image);
        $('#berkasProfilPreviewImage').attr('src', base64Image);

        $('#modalBerkasCropProfil').modal('hide');

        const self = this;
        $('#modalBerkasCropProfil').one('hidden.bs.modal', function () {
            $('#berkasProfilPreviewContainer').show();
            $('#berkasBtnUploadProfilFromForm').show();
            $('#berkasProfilExistingImageContainer').hide();

            setTimeout(function () {
                $('#modalBerkasUploadProfil').modal('show');
            }, 100);
        });
    }

    _adjustCropBoxAfterRotate() {
        const self = this;
        setTimeout(function () {
            if (!self.cropperBerkas) return;
            try {
                const aspectRatio = window._berkasCurrentAspectRatio;
                if (!isNaN(aspectRatio)) {
                    self.cropperBerkas.setAspectRatio(aspectRatio);
                }
            } catch (e) {
                console.log('Error adjusting crop box after rotate:', e);
            }
        }, 100);
    }
}
