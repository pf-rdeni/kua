<?php $this->extend('backend/template/template'); ?>
<?php $this->section('content'); ?>

<!-- Action Header -->
<div class="row mb-3">
    <div class="col-12 text-right">
        <a href="<?= base_url('admin/keuangan/iuran/' . $entitasType) ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>Kembali
        </a>
    </div>
</div>

<!-- Info Iuran -->
<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <div class="row">
            <div class="col-auto"><small class="text-muted d-block">Nama Iuran</small><strong><?= esc($setting['nama_iuran']) ?></strong></div>
            <div class="col-auto">
                <small class="text-muted d-block">Periode</small>
                <span class="badge badge-info"><?= ucfirst($setting['periode']) ?></span>
            </div>
            <div class="col-auto"><small class="text-muted d-block">Nominal</small><strong class="text-success">Rp <?= number_format($setting['nominal'], 0, ',', '.') ?></strong></div>
            <div class="col-auto"><small class="text-muted d-block">Berlaku</small><strong><?= date('d M Y', strtotime($setting['tanggal_mulai'])) ?> — <?= $setting['tanggal_selesai'] ? date('d M Y', strtotime($setting['tanggal_selesai'])) : 'Tanpa batas' ?></strong></div>
            <div class="col-auto"><small class="text-muted d-block">Total Anggota</small><strong><?= count($personilList) ?> orang</strong></div>
        </div>
    </div>
</div>

<!-- Alert -->
<?php if (session()->getFlashdata('success')): ?><div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle mr-1"></i><?= session()->getFlashdata('success') ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div><?php endif; ?>
<?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle mr-1"></i><?= session()->getFlashdata('error') ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div><?php endif; ?>

<!-- Filter Periode -->
<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" action="" class="d-flex align-items-end gap-2 flex-wrap">
            <div style="min-width: 300px; max-width: 100%;">
                <label class="small font-weight-bold">Filter Periode</label>
                <select name="periode[]" class="form-control form-control-sm select2" multiple="multiple" data-placeholder="— Pilih Beberapa Periode —">
                    <?php 
                    $selectedFilter = $filterPeriode ? explode(',', $filterPeriode) : [];
                    foreach ($semuaPeriode as $p): 
                    ?>
                    <option value="<?= $p ?>" <?= in_array($p, $selectedFilter) ? 'selected' : '' ?>><?= $p ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-sm btn-primary mt-auto"><i class="fas fa-filter mr-1"></i>Tampilkan</button>
            <?php if ($filterPeriode): ?>
            <a href="?" class="btn btn-sm btn-secondary mt-auto"><i class="fas fa-redo mr-1"></i>Reset</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Rekap Per Periode yang Ditampilkan -->
<?php if (!empty($periodeAktif)): ?>
<div class="row mb-3">
    <?php foreach ($periodeAktif as $p): ?>
    <?php $rp = $rekapPeriode[$p] ?? ['lunas' => 0, 'sebagian' => 0, 'belum' => 0]; ?>
    <div class="col-auto mb-2">
        <div class="card shadow-sm border-left border-info" style="min-width:160px">
            <div class="card-body py-2 px-3">
                <div class="small font-weight-bold text-info mb-1"><?= $p ?></div>
                <span class="badge badge-success mr-1">✓ <?= $rp['lunas'] ?> lunas</span>
                <span class="badge badge-warning mr-1"><?= $rp['sebagian'] ?> sebagian</span>
                <span class="badge badge-danger"><?= $rp['belum'] ?> belum</span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Tabel Anggota + Status Bayar per Periode -->
<div class="card shadow-sm">
    <div class="card-header py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-table mr-1 text-secondary"></i>Status Pembayaran Anggota</h6>
        <small class="text-muted"><?= count($personilList) ?> anggota · <?= count($periodeAktif) ?> periode</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabel-anggota" class="table table-bordered table-sm mb-0" style="width:100%">
                <thead class="thead-light">
                    <tr>
                        <th width="40">#</th>
                        <th>Identitas</th>
                        <?php foreach ($periodeAktif as $p): ?>
                        <th class="text-center"><?= $p ?></th>
                        <?php endforeach; ?>
                        <th width="60">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($personilList)): ?>
                    <tr><td colspan="<?= 4 + count($periodeAktif) ?>" class="text-center text-muted py-4">Tidak ada anggota aktif untuk entitas ini.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($personilList as $idx => $p): ?>
                    <tr>
                        <td><?= $idx + 1 ?></td>
                        <?php
                            // Generate dinamis "NI" + huruf depan setiap kata (misal: "Mubaligh" -> NIM, "Imam Masjid" -> NII)
                            $words = explode(' ', $entitasConfig['nama_label'] ?? '');
                            $inisial = '';
                            foreach ($words as $w) {
                                if(!empty($w)) $inisial .= strtoupper(substr($w, 0, 1));
                            }
                            // Jika inisial cuma 1 karakter, jadikan NI+X, misal Mubaligh -> NIM. Jika lebih misal Fardu Kifayah -> NIFK
                            $niaLabel = "NI" . ($inisial ?: 'A');
                            
                            // Siapkan data rekap untuk JS
                            $rekapPerson = [];
                            foreach ($semuaPeriode as $per) {
                                $rekapPerson[$per] = $bayaranMap[$p['id']][$per]['status'] ?? 'belum';
                            }
                        ?>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <?php if (!empty($p['foto']) && file_exists(FCPATH . 'uploads/personil/' . $p['foto'])): ?>
                                        <img src="<?= base_url('uploads/personil/' . esc($p['foto'])) ?>" alt="Foto" style="width: 45px; height: 45px; object-fit: cover; border-radius: 50%;">
                                    <?php else: ?>
                                        <?php 
                                        $words = explode(' ', trim($p['nama_lengkap']));
                                        $initials = '';
                                        if (count($words) >= 2) {
                                            $initials = strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
                                        } elseif (count($words) == 1 && strlen($words[0]) >= 2) {
                                            $initials = strtoupper(substr($words[0], 0, 2));
                                        } else {
                                            $initials = strtoupper(substr($p['nama_lengkap'], 0, 1) ?: '?');
                                        }
                                        $colors = ['#f56954', '#f39c12', '#00c0ef', '#00a65a', '#3c8dbc', '#d81b60', '#605ca8', '#ff851b', '#39cccc', '#001f3f'];
                                        $colorIndex = ord($initials[0]) % count($colors);
                                        $bgColor = $colors[$colorIndex] ?? '#6c757d';
                                        ?>
                                        <div class="d-inline-flex align-items-center justify-content-center text-white" style="width: 45px; height: 45px; border-radius: 50%; background-color: <?= $bgColor ?>; font-weight: bold; font-size: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            <?= $initials ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <strong><?= esc($p['nama_lengkap']) ?></strong>
                                    <br><small class="text-muted"><?= $niaLabel ?>: <?= esc($p['nia'] ?? '-') ?></small>
                                    <?php if ($p['no_hp']): ?>
                                    <br>
                                    <a href="#" class="btn-wa-modal text-success small text-decoration-none" 
                                       data-hp="<?= esc($p['no_hp']) ?>" 
                                       data-nama="<?= esc($p['nama_lengkap']) ?>"
                                       data-rekap="<?= htmlspecialchars(json_encode($rekapPerson), ENT_QUOTES, 'UTF-8') ?>">
                                       <i class="fab fa-whatsapp"></i> <span class="wa-number"><?= esc($p['no_hp']) ?></span>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <?php foreach ($periodeAktif as $periode): ?>
                        <?php
                            $bayarData = $bayaranMap[$p['id']][$periode] ?? null;
                            $status    = $bayarData ? $bayarData['status'] : 'belum';
                            $badgeClass = match($status) {
                                'lunas'    => 'success',
                                'sebagian' => 'warning',
                                default    => 'danger',
                            };
                            $badgeIcon = match($status) {
                                'lunas'    => 'check',
                                'sebagian' => 'exclamation',
                                default    => 'times',
                            };
                        ?>
                        <td class="text-center">
                            <button class="btn btn-xs btn-<?= $badgeClass ?> btn-catat-bayar"
                                    title="<?= $status === 'belum' ? 'Catat Bayar' : 'Lihat / Update' ?>"
                                    data-pid="<?= $p['id'] ?>"
                                    data-pnama="<?= esc($p['nama_lengkap']) ?>"
                                    data-periode="<?= $periode ?>"
                                    data-status="<?= $status ?>"
                                    data-jumlah="<?= $bayarData ? $bayarData['jumlah_bayar'] : $setting['nominal'] ?>"
                                    data-tgl="<?= $bayarData ? $bayarData['tanggal_bayar'] : date('Y-m-d') ?>"
                                    data-ket="<?= esc($bayarData ? $bayarData['keterangan'] ?? '' : '') ?>"
                                    data-bayar-id="<?= $bayarData ? $bayarData['id'] : '' ?>">
                                <i class="fas fa-<?= $badgeIcon ?>"></i>
                                <br><small style="font-size:9px"><?= ucfirst($status) ?></small>
                            </button>
                        </td>
                        <?php endforeach; ?>
                        <td class="text-center">
                            <!-- Placeholder untuk aksi per baris jika diperlukan -->
                            <small class="text-muted">—</small>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Catat Bayar -->
<div class="modal fade" id="modalCatatBayar" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title"><i class="fas fa-money-bill-wave mr-1"></i>Catat Pembayaran Iuran</h6>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="<?= base_url('admin/keuangan/iuran/' . $entitasType . '/bayar') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id_iuran_setting" value="<?= $setting['id'] ?>">
                <input type="hidden" name="id_personil" id="modal_pid">
                <input type="hidden" name="periode_filter" value="<?= $filterPeriode ?>">
                <div class="modal-body">
                    <div class="alert alert-light border mb-3">
                        <strong id="modal_nama_anggota" class="d-block"></strong>
                        <small class="text-muted">Iuran: <strong><?= esc($setting['nama_iuran']) ?></strong> | Periode: <strong id="modal_periode_tampil"></strong></small>
                    </div>
                    <input type="hidden" name="periode_bayar" id="modal_periode">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_bayar" id="modal_tgl" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small">Jumlah Bayar (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                <input type="number" name="jumlah_bayar" id="modal_jumlah" class="form-control form-control-sm" step="any" required>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="font-weight-bold small">Status Pembayaran <span class="text-danger">*</span></label>
                            <select name="status" id="modal_status" class="form-control form-control-sm" required>
                                <option value="lunas">Lunas</option>
                                <option value="sebagian">Sebagian</option>
                                <option value="belum">Belum Bayar</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="font-weight-bold small">Keterangan</label>
                            <textarea name="keterangan" id="modal_ket" class="form-control form-control-sm" rows="2" placeholder="Opsional..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Batal</button>
                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save mr-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Kirim WhatsApp -->
<div class="modal fade" id="modalWA" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title"><i class="fab fa-whatsapp mr-1"></i>Kirim Informasi Iuran WhatsApp</h6>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="alert alert-light border mb-3">
                            <strong id="wa_nama" class="d-block text-success"></strong>
                            <small class="text-muted"><i class="fas fa-phone mr-1"></i><span id="wa_hp"></span></small>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small font-weight-bold">Informasi Status yang Disertakan:</label>
                            <div>
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input filter-wa" id="wa_lunas" value="lunas" checked>
                                    <label class="custom-control-label small" for="wa_lunas">Lunas</label>
                                </div>
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input filter-wa" id="wa_sebagian" value="sebagian" checked>
                                    <label class="custom-control-label small" for="wa_sebagian">Sebagian</label>
                                </div>
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input filter-wa" id="wa_belum" value="belum" checked>
                                    <label class="custom-control-label small" for="wa_belum">Belum Bayar</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small font-weight-bold">Rentang Periode:</label>
                            <select id="wa_periode_start" class="form-control form-control-sm mb-1 filter-wa">
                                <?php foreach ($semuaPeriode as $p): ?>
                                <option value="<?= $p ?>"><?= $p ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select id="wa_periode_end" class="form-control form-control-sm filter-wa">
                                <?php foreach ($semuaPeriode as $p): ?>
                                <option value="<?= $p ?>"><?= $p ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary w-100" id="btn-generate-wa">
                            <i class="fas fa-sync-alt mr-1"></i>Generate Template Pesan
                        </button>
                    </div>
                    <div class="col-md-7">
                        <label class="small font-weight-bold">Template Pesan WhatsApp:</label>
                        <textarea id="wa_message" class="form-control form-control-sm" rows="12"></textarea>
                        <small class="text-muted mt-1 d-block">Anda dapat mengedit template ini sebelum dikirim.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Tutup</button>
                <a href="#" target="_blank" id="btn-send-wa" class="btn btn-sm btn-success"><i class="fab fa-whatsapp mr-1"></i>Kirim Sekarang</a>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
$(function () {
    // Inisialisasi Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        allowClear: true
    });

    // Inisialisasi DataTables
    $('#tabel-anggota').DataTable({
        responsive: false,
        paging: true,
        pageLength: 50,
        searching: true,
        ordering: false,
        scrollX: true,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excelHtml5', text: '<i class="fas fa-file-excel mr-1"></i>Excel', className: 'btn btn-sm btn-success' },
            { extend: 'pdfHtml5',   text: '<i class="fas fa-file-pdf mr-1"></i>PDF',   className: 'btn btn-sm btn-danger', orientation: 'landscape' }
        ]
    });

    // Isi modal Catat Bayar ketika tombol diklik
    $(document).on('click', '.btn-catat-bayar', function () {
        $('#modal_pid').val($(this).data('pid'));
        $('#modal_nama_anggota').text($(this).data('pnama'));
        $('#modal_periode').val($(this).data('periode'));
        $('#modal_periode_tampil').text($(this).data('periode'));
        $('#modal_status').val($(this).data('status') !== 'belum' ? $(this).data('status') : 'lunas');
        $('#modal_jumlah').val(Math.round(parseFloat($(this).data('jumlah'))));
        $('#modal_tgl').val($(this).data('tgl'));
        $('#modal_ket').val($(this).data('ket'));
        $('#modalCatatBayar').modal('show');
    });

    // Fitur WhatsApp
    let currentPersonData = {};
    let settingNamaIuran = "<?= esc($setting['nama_iuran']) ?>";
    let settingNominal = "Rp <?= number_format($setting['nominal'], 0, ',', '.') ?>";

    // Bind event clik ke tbody agar DataTable tidak memblokir event delegatenya
    $('#tabel-anggota tbody').on('click', '.btn-wa-modal', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Mencegah DataTables row click (jika ada) menangkap klik ini
        
        currentPersonData = {
            hp: $(this).data('hp'),
            nama: $(this).data('nama'),
            rekap: $(this).data('rekap')
        };
        
        // Format HP agar berawalan 62
        let hpFormatted = currentPersonData.hp.toString().replace(/^0/, '62');
        if(!hpFormatted.startsWith('62')) hpFormatted = '62' + hpFormatted;
        currentPersonData.hpFormatted = hpFormatted;

        $('#wa_nama').text(currentPersonData.nama);
        $('#wa_hp').text(currentPersonData.hp);
        
        // Set dropdown periode otomatis dari awal s.d akhir yg ada di rekap
        let periods = Object.keys(currentPersonData.rekap).sort();
        if(periods.length > 0) {
            $('#wa_periode_start').val(periods[0]);
            $('#wa_periode_end').val(periods[periods.length - 1]);
        }
        
        generateWAMessage();
        $('#modalWA').modal('show');
    });

    $('#btn-generate-wa, .filter-wa').on('change click', function(e) {
        if(e.type === 'click' && !$(this).is('#btn-generate-wa')) return;
        generateWAMessage();
    });

    $('#wa_message').on('input', function() {
        updateWALink();
    });

    function generateWAMessage() {
        if (!currentPersonData.rekap) return;
        
        let start = $('#wa_periode_start').val();
        let end = $('#wa_periode_end').val();
        let showLunas = $('#wa_lunas').is(':checked');
        let showSebagian = $('#wa_sebagian').is(':checked');
        let showBelum = $('#wa_belum').is(':checked');

        let msg = `Assalamu'alaikum wr. wb.\n\nYth. Bapak/Ibu *${currentPersonData.nama}*,\n\n`;
        msg += `Berikut adalah infromasi status ${settingNamaIuran} (Nominal: ${settingNominal}) Bapak/Ibu:\n\n`;
        
        let periods = Object.keys(currentPersonData.rekap).sort();
        let adaData = false;
        
        periods.forEach(idx => {
            if (idx >= start && idx <= end) {
                let stat = currentPersonData.rekap[idx];
                if ((stat === 'lunas' && showLunas) || 
                    (stat === 'sebagian' && showSebagian) || 
                    (stat === 'belum' && showBelum)) {
                    
                    let statText = stat === 'belum' ? 'Belum Bayar' : (stat === 'sebagian' ? 'Dibayar Sebagian' : 'Lunas');
                    let icon = stat === 'belum' ? '❌' : (stat === 'sebagian' ? '⚠️' : '✅');
                    msg += `- Periode ${idx}: ${icon} ${statText}\n`;
                    adaData = true;
                }
            }
        });

        if(!adaData) {
            msg += `(Tidak ada data yang sesuai filter)\n`;
        }

        msg += `\nDemikian informasi ini disampaikan. Terima kasih.\n\nWassalamu'alaikum wr. wb.`;
        $('#wa_message').val(msg);
        updateWALink();
    }

    function updateWALink() {
        let text = encodeURIComponent($('#wa_message').val());
        $('#btn-send-wa').attr('href', `https://wa.me/${currentPersonData.hpFormatted}?text=${text}`);
    }
});
</script>
<?php $this->endSection(); ?>
