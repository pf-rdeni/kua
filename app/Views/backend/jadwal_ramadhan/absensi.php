<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-md-12">
        
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Rekap Absensi Penceramah Harian</h3>
                <div class="card-tools">
                    <form action="" method="get" class="form-inline">
                        <label class="mr-2">Pilih Tanggal:</label>
                        <input type="date" name="tanggal" class="form-control form-control-sm mr-2" value="<?= esc($tanggalPilih) ?>" onchange="this.form.submit()">
                    </form>
                </div>
            </div>
            
            <form action="<?= base_url('admin/jadwal-ramadhan/save-absensi') ?>" method="post">
                <?= csrf_field() ?>
                <div class="card-body">
                    
                    <?php if (empty($jadwalHarian)): ?>
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Info!</h5>
                            Belum ada jadwal penceramah yang diplot untuk tanggal <strong><?= esc(tanggal_indo_panjang($tanggalPilih)) ?></strong>.
                        </div>
                    <?php else: ?>
                        <div class="alert bg-light border">
                            <i class="fas fa-calendar-alt text-primary mr-2"></i> 
                            Daftar Jadwal Penceramah untuk Tanggal: <strong><?= esc(tanggal_indo_panjang($tanggalPilih)) ?></strong>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">No</th>
                                        <th>Nama Masjid / Tempat</th>
                                        <th>Hari / Tanggal</th>
                                        <th>Mubaligh Terjadwal</th>
                                        <th style="width: 300px;" class="text-center">Status Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($jadwalHarian as $jadwal): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td>
                                            <h6 class="mb-0 font-weight-bold text-primary"><?= esc($jadwal['nama_masjid']) ?></h6>
                                            <small class="text-muted"><i class="fas fa-map-marker-alt"></i> <?= esc($jadwal['alamat_masjid']) ?></small>
                                        </td>
                                        <td>
                                            Ramadhan Ke-<?= esc($jadwal['hari_ke']) ?><br>
                                            <small class="text-muted"><?= esc($jadwal['tahun_hijriah']) ?></small>
                                        </td>
                                        <td>
                                            <h6 class="mb-0 font-weight-bold text-dark"><?= esc($jadwal['nama_mubaligh']) ?></h6>
                                            <?php if($jadwal['status_kehadiran'] == 'diganti'): ?>
                                                <small class="text-warning font-weight-bold d-block mt-1">
                                                    <i class="fas fa-exchange-alt"></i> Digantikan Oleh: <?= esc($jadwal['nama_pengganti'] ?? 'Belum ditentukan') ?>
                                                </small>
                                                <?php if(!empty($jadwal['keterangan_absensi'])): ?>
                                                    <small class="text-muted d-block mt-1">Ket: <?= esc($jadwal['keterangan_absensi']) ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <small class="text-muted"><i class="fas fa-phone-alt"></i> <?= esc($jadwal['no_hp_mubaligh']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                              <label class="btn btn-sm btn-outline-success <?= ($jadwal['status_kehadiran'] == 'hadir') ? 'active' : '' ?>">
                                                <input type="radio" name="absensi[<?= $jadwal['id_jadwal'] ?>]" value="hadir" <?= ($jadwal['status_kehadiran'] == 'hadir') ? 'checked' : '' ?>> 
                                                <i class="fas fa-check"></i> Hadir
                                              </label>
                                              
                                              <label class="btn btn-sm btn-outline-danger <?= ($jadwal['status_kehadiran'] == 'tidak_hadir') ? 'active' : '' ?>">
                                                <input type="radio" name="absensi[<?= $jadwal['id_jadwal'] ?>]" value="tidak_hadir" <?= ($jadwal['status_kehadiran'] == 'tidak_hadir') ? 'checked' : '' ?>> 
                                                <i class="fas fa-times"></i> Alpha
                                              </label>
                                            </div>
                                            
                                            <!-- Info badge jika delegasi sudah diajukan via HP -->
                                            <?php if($jadwal['status_kehadiran'] == 'diganti'): ?>
                                                <div class="mt-2 badge badge-warning w-100 p-2"><i class="fas fa-info-circle"></i> Diajukan Ganti (Delegasi)</div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    
                </div>
                
                <?php if (!empty($jadwalHarian)): ?>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Rekap Absensi
                    </button>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- You can add specific absensi list scripts here if needed -->
<?= $this->endSection(); ?>
