<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; margin: 20px; }
        h2, h3 { text-align: center; margin: 5px 0; }
        hr { border-top: 2px solid #000; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #17a2b8; color: white; border: none; cursor: pointer;">Cetak/Print</button>
        <button onclick="window.close()" style="padding: 10px 20px; background-color: #6c757d; color: white; border: none; cursor: pointer; margin-left:10px;">Tutup</button>
    </div>

    <h2>JADWAL CERAMAH TARAWIH RAMADHAN <?= esc($tahunHijriah) ?></h2>
    <h3><?= strtoupper(esc($masjid['nama'])) ?></h3>
    <p style="text-align: center; margin-top:5px;"><?= esc($masjid['alamat']) ?></p>
    <hr>

    <table>
        <thead>
            <tr>
                <th width="15%">Malam / Hari Ke</th>
                <th width="20%">Tanggal</th>
                <th width="35%">Nama Penceramah / Mubaligh</th>
                <th width="30%">Tema Ceramah</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($jadwalList)): ?>
            <tr>
                <td colspan="4" style="text-align: center;">Jadwal belum tersedia</td>
            </tr>
            <?php else: ?>
                <?php 
                // Buffer untuk Malam Ke-1 (Pengumuman)
                $tanggalMalam1 = '';
                foreach ($jadwalList as $j_check) {
                    if ($j_check['hari_ke'] == 1 && $j_check['tanggal']) {
                        $tanggalMalam1 = date('Y-m-d', strtotime('-1 day', strtotime($j_check['tanggal'])));
                        break;
                    }
                }
                ?>
                <?php if ($tanggalMalam1): ?>
                <tr>
                    <td style="text-align: center;">Malam Ke-1<br><small>(H-1 Puasa)</small></td>
                    <td><?= tanggal_indo_panjang($tanggalMalam1) ?></td>
                    <td colspan="2">
                        <i style="color:#555;">(Umumnya diisi dengan sambutan/pengumuman Panitia Ramadhan)</i>
                    </td>
                </tr>
                <?php endif; ?>

                <?php foreach($jadwalList as $j): ?>
                <tr>
                    <td style="text-align: center;">Malam Ke-<?= intval($j['hari_ke']) + 1 ?><br><small>(<?= esc($j['hari_ke']) ?> Ramadhan <?= esc($tahunHijriah) ?>)</small></td>
                    <td><?= $j['tanggal'] ? tanggal_indo_panjang($j['tanggal']) : '-' ?></td>
                    <td>
                        <?php if($j['nama_mubaligh']): ?>
                            <strong><?= esc($j['nama_mubaligh']) ?></strong>
                            <br><small><?= esc($j['no_hp']) ?></small>
                        <?php else: ?>
                            <i style="color:red;">Kosong / Belum Diisi</i>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($j['tema']) ?: '-' ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 50px; float: right; width: 300px; text-align: center;">
        <p>Ketua DKM / Pengurus</p>
        <br><br><br>
        <p><strong>( ______________________ )</strong></p>
    </div>
</body>
</html>
