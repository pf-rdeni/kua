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
    <h3>BAPAK <?= strtoupper(esc($mubaligh['nama_lengkap'])) ?></h3>
    <hr>
    
    <p><strong>NIK / ID:</strong> <?= esc($mubaligh['nik']) ?></p>
    <p><strong>No HP:</strong> <?= esc($mubaligh['no_hp']) ?></p>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Hari/Tanggal</th>
                <th width="35%">Masjid/Mushola & Lokasi</th>
                <th width="40%">Tema Ceramah</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($jadwalList)): ?>
            <tr>
                <td colspan="4" style="text-align: center;">Belum ada jadwal</td>
            </tr>
            <?php else: ?>
                <?php $no = 1; foreach($jadwalList as $j): ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td><?= esc($j['hari_ke']) ?> Ramadhan <?= esc($tahunHijriah) ?> (Malam Ke-<?= intval($j['hari_ke']) + 1 ?>)<br><small><?= $j['tanggal'] ? tanggal_indo_panjang($j['tanggal']) : '-' ?></small></td>
                    <td><strong><?= esc($j['nama_masjid']) ?></strong><br><small><?= esc($j['alamat_masjid']) ?></small></td>
                    <td><?= esc($j['tema']) ?: '<i style="color:#777;">Belum ada tema (menyesuaikan)</i>' ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 50px; float: right; width: 300px; text-align: center;">
        <p>Pengurus / Panitia Ramadhan</p>
        <br><br><br>
        <p><strong>( ______________________ )</strong></p>
    </div>
</body>
</html>
