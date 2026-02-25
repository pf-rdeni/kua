<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; margin: 0; padding: 20px; }
        .text-center { text-align: center; }
        .font-weight-bold { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        .header-title { font-size: 18px; margin-bottom: 5px; }
        .header-subtitle { font-size: 14px; margin-bottom: 20px; }
        @media print {
            body { padding: 0; }
            button.no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="text-center">
        <div class="header-title font-weight-bold">JADWAL KHOTIB JUMAT TAHUN <?= esc($tahunPilih) ?></div>
        <div class="header-subtitle">
            Khotib: <strong><?= esc($mubaligh['nama_lengkap']) ?></strong><br>
            No. HP: <?= esc($mubaligh['no_hp'] ?: '-') ?>
        </div>
    </div>

    <button class="no-print" onclick="window.print()" style="margin-bottom: 15px; padding: 8px 15px; cursor: pointer;">
        Cetak Jadwal
    </button>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Tanggal (Jumat)</th>
                <th style="width: 30%;">Tugas di Masjid/Mushola</th>
                <th style="width: 40%;">Alamat</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($jadwalList)): ?>
            <tr>
                <td colspan="4" class="text-center">Belum ada jadwal Khotib Jumat untuk tahun <?= esc($tahunPilih) ?>.</td>
            </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($jadwalList as $row): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-center"><?= tanggal_indo_panjang($row['tanggal']) ?></td>
                    <td><?= esc($row['nama_masjid']) ?></td>
                    <td><?= esc($row['alamat_masjid']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
