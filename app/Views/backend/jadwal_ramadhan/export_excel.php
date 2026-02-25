<?php
// Mencegah output HTML lain yang tidak diinginkan
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Jadwal_Ramadhan_" . str_replace(' ', '_', $tahunHijriah) . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Ramadhan</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Matriks Jadwal Penceramah Ramadhan <?= esc($tahunHijriah) ?></h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Masjid/Mushola</th>
                <th>Desa/Kelurahan</th>
                <th>Alamat</th>
                <?php for($i=1; $i<=30; $i++): ?>
                <th><?= $i ?> Ramadhan <?= esc($tahunHijriah) ?><br><?= isset($tanggals[$i]) && $tanggals[$i] ? date('d-M-Y', strtotime($tanggals[$i])) : '-' ?></th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach($masjidList as $m): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= esc($m['nama']) ?></td>
                <td><?= esc($m['kelurahan_desa']) ?></td>
                <td><?= esc($m['alamat']) ?></td>
                <?php for($i=1; $i<=30; $i++): ?>
                    <?php 
                        $cellData = $matriks[$m['id_masjid_mushola']][$i] ?? null; 
                        $penceramah = $cellData ? $cellData['nama_mubaligh'] : '-';
                    ?>
                <td><?= esc($penceramah) ?></td>
                <?php endfor; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
