<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>




<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>Pengajuan Insentif — <?= esc($entitasConfig['nama_label']) ?>
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-info" id="btn-cetak-bulk">
                        <i class="fas fa-file-archive"></i> Cetak Bulk (ZIP)
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm" id="tabelInsentif">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th style="width: 100px; text-align: center;">Aksi</th>
                                <th>NIK / Nama</th>
                                <th style="width: 130px; text-align: center;">Profil</th>
                                <?php foreach ($settingBerkas as $sb): ?>
                                    <th style="width: 100px; text-align: center;"><?= esc($sb['nama_berkas']) ?></th>
                                <?php endforeach; ?>
                                <th style="width: 280px; text-align: center;">Cetak Surat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($personilWithBerkas)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($personilWithBerkas as $item): ?>
                                    <?php
                                    $p = $item['personil'];
                                    $berkas = $item['berkas'];
                                    $fotoUrl = !empty($p['foto']) ? base_url('uploads/personil/' . $p['foto']) : null;
                                    $fotoThumbUrl = $fotoUrl;
                                    if (!empty($p['foto']) && file_exists(FCPATH . 'uploads/personil/thumbs/' . $p['foto'])) {
                                        $fotoThumbUrl = base_url('uploads/personil/thumbs/' . $p['foto']);
                                    }
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <button type="button" class="btn btn-sm btn-primary btn-cetak-gabungan" data-id="<?= $p['id'] ?>" data-nama="<?= esc($p['nama_lengkap']) ?>" title="Cetak Gabungan">
                                                <i class="fas fa-print"></i> Cetak Gabungan
                                            </button>
                                        </td>
                                        <td>
                                            <div><strong><?= esc($p['nama_lengkap']) ?></strong></div>
                                            <small class="text-muted"><?= esc($p['nik'] ?? '-') ?></small>
                                        </td>
                                        <!-- Foto Profil -->
                                        <td class="text-center" style="vertical-align: middle;">
                                            <?php if ($fotoUrl): ?>
                                                <img src="<?= $fotoThumbUrl ?>" alt="Profil" class="img-thumbnail"
                                                     style="max-width: 60px; max-height: 80px; object-fit: cover;">
                                            <?php else: ?>
                                                <span class="text-muted"><i class="fas fa-user fa-2x"></i></span>
                                            <?php endif; ?>
                                        </td>
                                        <!-- Status Berkas (ikon centang/silang) -->
                                        <?php foreach ($settingBerkas as $sb): ?>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <?php if (isset($berkas[$sb['nama_berkas']])): ?>
                                                    <span class="text-success"><i class="fas fa-check-circle fa-lg"></i></span>
                                                <?php else: ?>
                                                    <span class="text-danger"><i class="fas fa-times-circle fa-lg"></i></span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <!-- Tombol Cetak -->
                                        <td class="text-center" style="vertical-align: middle;">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?= base_url('admin/pengajuan-insentif/' . $entitasType . '/cetak-asn/' . $p['id']) ?>"
                                                   target="_blank" class="btn btn-outline-primary" title="Surat Pernyataan ASN">
                                                    <i class="fas fa-file-pdf"></i> ASN
                                                </a>
                                                <a href="<?= base_url('admin/pengajuan-insentif/' . $entitasType . '/cetak-insentif/' . $p['id']) ?>"
                                                   target="_blank" class="btn btn-outline-success" title="Surat Pernyataan Insentif">
                                                    <i class="fas fa-file-pdf"></i> Insentif
                                                </a>
                                                <a href="<?= base_url('admin/pengajuan-insentif/' . $entitasType . '/cetak-rekomendasi/' . $p['id']) ?>"
                                                   target="_blank" class="btn btn-outline-warning" title="Surat Rekomendasi">
                                                    <i class="fas fa-file-pdf"></i> Rekom
                                                </a>
                                                <a href="<?= base_url('admin/pengajuan-insentif/' . $entitasType . '/cetak-lampiran/' . $p['id']) ?>"
                                                   target="_blank" class="btn btn-outline-info" title="Lampiran Berkas">
                                                    <i class="fas fa-file-image"></i> Lamp
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <?php $colspan = 5 + count($settingBerkas); ?>
                                    <td colspan="<?= $colspan ?>" class="text-center text-muted">Belum ada data <?= esc($entitasConfig['nama_label']) ?>.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cetak Gabungan -->
<div class="modal fade" id="modalCetakGabungan" tabindex="-1" role="dialog" aria-labelledby="modalCetakGabunganLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCetakGabunganLabel"><i class="fas fa-print mr-2"></i><span id="cgModalTitleText">Cetak Gabungan Dokumen</span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="cgModalDescriptionText">Pilih dokumen yang ingin digabungkan untuk <strong id="cgNamaPersonil"></strong>:</p>
                <form id="formCetakGabungan">
                    <input type="hidden" id="cgIdPersonil">
                    <input type="hidden" id="cgActionType" value="individual">
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input cg-checkbox" id="chkAsn" value="asn">
                        <label class="custom-control-label" for="chkAsn">Surat Pernyataan ASN</label>
                    </div>
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input cg-checkbox" id="chkInsentif" value="insentif">
                        <label class="custom-control-label" for="chkInsentif">Surat Pernyataan Insentif</label>
                    </div>
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input cg-checkbox" id="chkRekomendasi" value="rekomendasi">
                        <label class="custom-control-label" for="chkRekomendasi">Surat Rekomendasi</label>
                    </div>
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input cg-checkbox" id="chkLampiran" value="lampiran">
                        <label class="custom-control-label" for="chkLampiran">Lampiran Berkas</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnProsesCetakGabungan"><i class="fas fa-print mr-1"></i> Proses Cetak</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    $('#tabelInsentif').DataTable({
        pageLength: 25,
        lengthChange: true,
        ordering: true,
        searching: true,
    });

    // Helper load preference
    function loadCetakPrefs() {
        var savedPrefs = localStorage.getItem('cetakGabunganPrefs');
        if (savedPrefs) {
            var prefs = JSON.parse(savedPrefs);
            $('.cg-checkbox').each(function() {
                $(this).prop('checked', prefs.includes($(this).val()));
            });
        } else {
            $('.cg-checkbox').prop('checked', true);
        }
    }

    // Handle Cetak Gabungan Modal (Individual)
    $('#tabelInsentif').on('click', '.btn-cetak-gabungan', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        
        $('#cgIdPersonil').val(id);
        $('#cgActionType').val('individual');
        $('#cgModalTitleText').text('Cetak Gabungan Dokumen');
        $('#cgModalDescriptionText').html('Pilih dokumen yang ingin digabungkan untuk <strong id="cgNamaPersonil">' + nama + '</strong>:');
        
        loadCetakPrefs();
        $('#modalCetakGabungan').modal('show');
    });

    // Handle Cetak Bulk (ZIP) Modal
    $('#btn-cetak-bulk').on('click', function() {
        $('#cgIdPersonil').val('');
        $('#cgActionType').val('bulk');
        $('#cgModalTitleText').text('Cetak Bulk Dokumen (ZIP)');
        $('#cgModalDescriptionText').html('Pilih dokumen yang ingin digabungkan untuk <strong>Semua Personil</strong>. Proses ini akan mengunduh sebuah file `.zip`.');
        
        loadCetakPrefs();
        $('#modalCetakGabungan').modal('show');
    });

    // Save preferences and process print
    $('#btnProsesCetakGabungan').on('click', function() {
        var actionType = $('#cgActionType').val();
        var id = $('#cgIdPersonil').val();
        var selectedBerkas = [];
        
        $('.cg-checkbox:checked').each(function() {
            selectedBerkas.push($(this).val());
        });
        
        if (selectedBerkas.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih minimal satu dokumen untuk dicetak.'
            });
            return;
        }
        
        // Save to localStorage
        localStorage.setItem('cetakGabunganPrefs', JSON.stringify(selectedBerkas));
        
        // Build URL
        var baseUrl = '<?= base_url('admin/pengajuan-insentif/' . $entitasType) ?>';
        var activeBtn = $(this);
        var originalBtnHtml = activeBtn.html();
        
        if (actionType === 'individual') {
            var printUrl = baseUrl + '/cetak-gabungan/' + id + '?berkas=' + selectedBerkas.join(',');
            window.open(printUrl, '_blank');
            $('#modalCetakGabungan').modal('hide');
        } else if (actionType === 'bulk') {
            var bulkUrl = baseUrl + '/cetak-bulk-zip?berkas=' + selectedBerkas.join(',');
            
            // Show loading UI on button
            activeBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Sedang Memproses...');
            activeBtn.prop('disabled', true);
            
            // Open download directly
            window.location.href = bulkUrl;
            
            // Modal stays open a bit then closes
            setTimeout(function() {
                activeBtn.html(originalBtnHtml);
                activeBtn.prop('disabled', false);
                $('#modalCetakGabungan').modal('hide');
            }, 3000);
        }
    });
});
</script>
<?= $this->endSection(); ?>
