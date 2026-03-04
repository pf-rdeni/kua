<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<?php
$bulanId  = ['','Januari','Februari','Maret','April','Mei','Juni',
              'Juli','Agustus','September','Oktober','November','Desember'];
$namaHari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

$jenisLabel = [
    'ceramah'      => ['label'=>'Ceramah','color'=>'primary','icon'=>'fas fa-microphone'],
    'ta_lim'       => ['label'=>"Ta'lim",'color'=>'success','icon'=>'fas fa-book-open'],
    'sosial'       => ['label'=>'Sosial','color'=>'warning','icon'=>'fas fa-hands-helping'],
    'buka_bersama' => ['label'=>'Buka Bersama','color'=>'danger','icon'=>'fas fa-utensils'],
    'tadarus'      => ['label'=>'Tadarus','color'=>'info','icon'=>'fas fa-quran'],
    'sahur'        => ['label'=>'Sahur','color'=>'secondary','icon'=>'fas fa-moon'],
    'lainnya'      => ['label'=>'Lainnya','color'=>'dark','icon'=>'fas fa-star'],
];

$namaEntitas = $entitas['_nama_entitas'] ?? 'Entitas';
$jenisEntitas = $entitas['_jenis_entitas'] ?? 'Entitas';

// Kelompokkan agenda berdasarkan tanggal
$agendaByTanggal = [];
foreach ($agendaList as $a) {
    $agendaByTanggal[$a['tanggal']][] = $a;
}

// Build URL base dengan entitas params
$baseParams = '';
if ($isAdmin && $entitasType === 'majelis_taklim') {
    $baseParams = '?entitas_type=majelis_taklim&entitas_id=' . $entitasId;
}
?>

<!-- Filter atas -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="form-inline flex-wrap" style="gap:8px;">
            <?php if ($isAdmin): ?>
                <select name="entitas_type" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="masjid_mushola" <?= $entitasType === 'masjid_mushola' ? 'selected' : '' ?>>Masjid/Mushola</option>
                    <option value="majelis_taklim" <?= $entitasType === 'majelis_taklim' ? 'selected' : '' ?>>Majelis Taklim</option>
                </select>
                <select name="entitas_id" class="form-control form-control-sm" style="min-width:200px;">
                    <option value="">— Pilih <?= $jenisEntitas ?> —</option>
                    <?php foreach ($entitasList as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= $e['id'] == $entitasId ? 'selected' : '' ?>>
                            <?= esc($e['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <select name="bulan" class="form-control form-control-sm">
                <?php for ($b = 1; $b <= 12; $b++): ?>
                    <option value="<?= $b ?>" <?= $b == $bulan ? 'selected' : '' ?>><?= $bulanId[$b] ?></option>
                <?php endfor; ?>
            </select>
            <select name="tahun" class="form-control form-control-sm">
                <?php foreach ($tahunList as $ty): ?>
                    <option value="<?= $ty ?>" <?= $ty == $tahun ? 'selected' : '' ?>><?= $ty ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter mr-1"></i>Filter</button>
            <a href="<?= base_url('admin/agenda-masjid/create') . $baseParams ?>" class="btn btn-sm btn-success ml-auto">
                <i class="fas fa-plus mr-1"></i>Tambah Agenda
            </a>
        </form>
    </div>
</div>

<!-- Flash messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible"><button class="close" data-dismiss="alert">&times;</button><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible"><button class="close" data-dismiss="alert">&times;</button><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<!-- Header Info Entitas -->
<div class="d-flex align-items-center mb-3">
    <i class="fas fa-<?= $entitasType === 'masjid_mushola' ? 'mosque' : 'chalkboard-teacher' ?> fa-2x text-primary mr-3"></i>
    <div>
        <h5 class="mb-0 font-weight-bold"><?= esc($namaEntitas) ?></h5>
        <small class="text-muted"><?= $bulanId[$bulan] ?> <?= $tahun ?> &mdash; <?= count($agendaList) ?> agenda</small>
    </div>
</div>

<div class="row">
    <!-- Kolom Kiri: Agenda Mandiri -->
    <div class="<?= $entitasType === 'masjid_mushola' ? 'col-lg-8' : 'col-12' ?>">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Agenda Kegiatan (Mandiri)</h3>
            </div>
            <div class="card-body p-0">
                <?php if (empty($agendaList)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                        Belum ada agenda untuk <strong><?= $bulanId[$bulan] ?> <?= $tahun ?></strong>.<br>
                        <a href="<?= base_url('admin/agenda-masjid/create') . $baseParams ?>" class="btn btn-sm btn-success mt-2">
                            <i class="fas fa-plus mr-1"></i>Tambah Agenda Pertama
                        </a>
                    </div>
                <?php else: ?>
                    <div class="timeline timeline-inverse px-3 pt-3">
                        <?php
                        $prevTanggal = null;
                        foreach ($agendaList as $a):
                            $tgl = $a['tanggal'];
                            $ts  = strtotime($tgl);
                            $jInfo = $jenisLabel[$a['jenis']] ?? $jenisLabel['lainnya'];
                            $namaPenceramah = $a['nama_mubaligh_db'] ?: $a['nama_penceramah'];
                        ?>
                        <?php if ($tgl !== $prevTanggal): ?>
                            <div class="time-label">
                                <span class="bg-<?= date('w',$ts)==5?'warning':'secondary' ?> text-<?= date('w',$ts)==5?'dark':'white' ?>">
                                    <?= $namaHari[date('w',$ts)] ?>, <?= date('d', $ts) ?> <?= $bulanId[(int)date('m',$ts)] ?> <?= date('Y',$ts) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <?php $prevTanggal = $tgl; ?>

                        <div>
                            <i class="<?= $jInfo['icon'] ?> bg-<?= $jInfo['color'] ?>"></i>
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="fas fa-clock mr-1"></i>
                                    <?= $a['waktu_mulai'] ? date('H:i', strtotime($a['waktu_mulai'])) : '--:--' ?>
                                    <?= $a['waktu_selesai'] ? '— ' . date('H:i', strtotime($a['waktu_selesai'])) : '' ?>
                                </span>
                                <h3 class="timeline-header">
                                    <span class="badge badge-<?= $jInfo['color'] ?> mr-1"><?= $jInfo['label'] ?></span>
                                    <?= esc($a['judul_kegiatan']) ?>
                                    <?php if (!$a['is_published']): ?>
                                        <span class="badge badge-secondary ml-1">Draft</span>
                                    <?php endif; ?>
                                </h3>
                                <div class="timeline-body">
                                    <?php if ($namaPenceramah): ?>
                                        <div class="mb-1"><i class="fas fa-user mr-1 text-muted"></i><strong>Penceramah:</strong> <?= esc($namaPenceramah) ?></div>
                                    <?php endif; ?>
                                    <?php if ($a['lokasi']): ?>
                                        <div class="mb-1"><i class="fas fa-map-marker-alt mr-1 text-muted"></i><?= esc($a['lokasi']) ?></div>
                                    <?php endif; ?>
                                    <?php if ($a['deskripsi']): ?>
                                        <div class="text-muted small"><?= nl2br(esc($a['deskripsi'])) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="timeline-footer">
                                    <a href="<?= base_url('admin/agenda-masjid/edit/' . $a['id']) ?>" class="btn btn-xs btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="<?= base_url('admin/agenda-masjid/delete/' . $a['id']) ?>"
                                       class="btn btn-xs btn-danger btn-hapus"
                                       data-nama="<?= esc($a['judul_kegiatan']) ?>">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div><i class="fas fa-clock bg-gray"></i></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($entitasType === 'masjid_mushola'): ?>
    <!-- Kolom Kanan: Jadwal Mubaligh KUA (hanya untuk masjid) -->
    <div class="col-lg-4">
        <div class="card card-outline card-success sticky-top" style="top:70px;">
            <div class="card-header">
                <h3 class="card-title text-success"><i class="fas fa-user-tie mr-2"></i>Jadwal Mubaligh (KUA)</h3>
            </div>
            <div class="card-body p-0" style="max-height:600px;overflow-y:auto;">
                <?php if (empty($jadwalKUAByTanggal)): ?>
                    <div class="text-center text-muted py-4 small">
                        Belum ada jadwal mubaligh dari KUA untuk masjid ini.
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($jadwalKUAByTanggal as $tgl => $jadwals): ?>
                            <?php $ts = strtotime($tgl); ?>
                            <li class="list-group-item py-1 px-2 bg-light">
                                <small class="font-weight-bold text-success">
                                    <?= $namaHari[date('w',$ts)] ?>, <?= date('d',$ts) ?> <?= $bulanId[(int)date('m',$ts)] ?>
                                </small>
                            </li>
                            <?php foreach ($jadwals as $jk): ?>
                            <li class="list-group-item py-2 px-3">
                                <div class="font-weight-bold small"><?= esc($jk['nama_mubaligh'] ?? '-') ?></div>
                                <?php if (!empty($jk['tema'])): ?>
                                    <div class="text-muted" style="font-size:0.75rem;"><?= esc($jk['tema']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($jk['no_hp'])): ?>
                                    <small class="text-muted"><i class="fas fa-phone mr-1"></i><?= esc($jk['no_hp']) ?></small>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    $('.btn-hapus').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const nama = $(this).data('nama');
        Swal.fire({
            title: 'Hapus Agenda?',
            text: '"' + nama + '" akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = url;
        });
    });
});
</script>
<?= $this->endSection(); ?>
