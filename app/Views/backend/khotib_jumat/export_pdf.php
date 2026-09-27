<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 15px; color: #000; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-weight-bold { font-weight: bold; }
        .header-title { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
        .header-subtitle { font-size: 12px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: center; font-size: 10px; }
        th { background-color: #e9ecef; }
        .bg-header { background-color: #f8f9fa; }
        .no-print { margin-bottom: 15px; }
        .page-break {
            page-break-after: always;
            break-after: page;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding: 8px 18px; font-weight: bold; cursor: pointer; background-color: #007bff; color: white; border: none; border-radius: 4px;">
            <i class="fas fa-print"></i> Cetak / Simpan PDF
        </button>
    </div>

    <!-- HALAMAN 1: JADWAL KODE NIS -->
    <div class="text-center">
        <div class="header-title">MATRIKS JADWAL KHOTIB JUMAT (KODE NIS)</div>
        <div class="header-subtitle">KUARTAL <?= esc($kuartalPilih) ?> TAHUN <?= esc($tahunPilih) ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 35px;">NO</th>
                <th rowspan="2" style="width: 220px;" class="text-left">NAMA MASJID</th>
                <?php
                    $months = [];
                    foreach ($fridays as $f) {
                        $m = date('F', strtotime($f));
                        if(!isset($months[$m])) $months[$m] = 0;
                        $months[$m]++;
                    }
                ?>
                <?php foreach ($months as $monthName => $colspan): ?>
                    <th colspan="<?= $colspan ?>"><?= strtoupper($monthName) ?></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <?php foreach($fridays as $tgl): ?>
                    <th><?= date('d', strtotime($tgl)) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($masjidList)): ?>
                <tr>
                    <td colspan="<?= count($fridays) + 2 ?>" class="text-center">Belum ada penugasan Khotib Jumat pada Kuartal ini.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($masjidList as $m): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td style="text-align: left; font-weight: bold;"><?= esc($m['nama']) ?></td>
                        <?php foreach ($fridays as $tgl): ?>
                            <?php 
                                $personilId = $matrixIds[$m['id_masjid_mushola']][$tgl] ?? null;
                                $nisDisplay = '-';
                                if ($personilId && isset($personilMap[$personilId])) {
                                    $nisDisplay = $personilMap[$personilId]['nia'] ?: $personilMap[$personilId]['nama_lengkap'];
                                }
                            ?>
                            <td><?= esc($nisDisplay) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- HALAMAN 2: JADWAL NAMA & NO HP -->
    <div class="text-center">
        <div class="header-title">MATRIKS JADWAL KHOTIB JUMAT (NAMA & NO. HP)</div>
        <div class="header-subtitle">KUARTAL <?= esc($kuartalPilih) ?> TAHUN <?= esc($tahunPilih) ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 35px;">NO</th>
                <th rowspan="2" style="width: 180px;" class="text-left">NAMA MASJID</th>
                <?php foreach ($months as $monthName => $colspan): ?>
                    <th colspan="<?= $colspan ?>"><?= strtoupper($monthName) ?></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <?php foreach($fridays as $tgl): ?>
                    <th><?= date('d', strtotime($tgl)) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($masjidList)): ?>
                <tr>
                    <td colspan="<?= count($fridays) + 2 ?>" class="text-center">Belum ada penugasan Khotib Jumat pada Kuartal ini.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($masjidList as $m): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td style="text-align: left; font-weight: bold;"><?= esc($m['nama']) ?></td>
                        <?php foreach ($fridays as $tgl): ?>
                            <?php 
                                $personilId = $matrixIds[$m['id_masjid_mushola']][$tgl] ?? null;
                                if ($personilId && isset($personilMap[$personilId])) {
                                    $pNia = esc($personilMap[$personilId]['nia'] ?: '');
                                    $pName = esc($personilMap[$personilId]['nama_lengkap']);
                                    $pHp = esc($personilMap[$personilId]['no_hp'] ?: '');
                                    $headerText = ($pNia ? $pNia . ' - ' : '') . $pName;
                                }
                            ?>
                            <td style="padding: 4px 2px; font-size: 9.5px;">
                                <?php if ($personilId && isset($personilMap[$personilId])): ?>
                                    <strong><?= $headerText ?></strong><br>
                                    <span style="color: #444; font-size: 9px;"><?= $pHp ?: '-' ?></span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- HALAMAN 3: DAFTAR KHOTIB -->
    <div class="text-center">
        <div class="header-title">DAFTAR KHOTIB / MUBALIGH</div>
        <div class="header-subtitle">KUARTAL <?= esc($kuartalPilih) ?> TAHUN <?= esc($tahunPilih) ?></div>
    </div>

    <table style="width: 750px; margin: 0 auto;">
        <thead>
            <tr class="bg-header">
                <th style="width: 40px;">NO</th>
                <th style="width: 140px;">NO. NIS / NIA</th>
                <th style="text-align: left;">NAMA KHOTIB / MUBALIGH</th>
                <th style="width: 170px;">NO. HP / WHATSAPP</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($personils)): ?>
                <tr><td colspan="4" class="text-center">Belum ada data Khotib pada Kuartal ini.</td></tr>
            <?php else: ?>
                <?php $noP = 1; foreach ($personils as $p): ?>
                    <tr>
                        <td><?= $noP++ ?></td>
                        <td class="font-weight-bold"><?= esc($p['nia'] ?: '-') ?></td>
                        <td style="text-align: left;"><?= esc($p['nama_lengkap']) ?></td>
                        <td><?= esc($p['no_hp'] ?: '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
