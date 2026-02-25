<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; margin: 0; padding: 20px; color: #333; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-weight-bold { font-weight: bold; }
        .mb-4 { margin-bottom: 1.5rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 12px; }
        .header-title { font-size: 20px; text-transform: uppercase; margin-bottom: 5px; }
        .header-subtitle { font-size: 14px; margin-bottom: 20px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            table { border-color: #000; }
            th, td { border-color: #000; }
        }
        .btn-print {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .btn-print:hover { background-color: #218838; }
    </style>
</head>
<body>
    <div class="text-center">
        <div class="header-title font-weight-bold">MATRIKS JADWAL MAGHRIB MENGAJI</div>
        <div class="header-subtitle font-weight-bold">KANTOR URUSAN AGAMA (KUA) KECAMATAN RENGASDENGKLOK</div>
        <div class="header-subtitle">TAHUN MASEHI: <strong><?= esc($tahunPilih) ?></strong></div>
    </div>

    <button class="no-print btn-print" onclick="window.print()">
        <i class="fas fa-print"></i> Cetak Jadwal
    </button>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 40px;">NO</th>
                <th rowspan="2" style="width: 100px;">BULAN</th>
                <th rowspan="2" style="width: 130px;">TANGGAL</th>
                <th rowspan="2">TEMPAT MASJID/MUSHOLA</th>
                <th colspan="3">PETUGAS (USTADZ)</th>
            </tr>
            <tr>
                <th>MC</th>
                <th>DO'A</th>
                <th>KULTUM</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $namaBulan = [
                1 => 'JANUARI', 2 => 'PEBRUARI', 3 => 'MARET', 4 => 'APRIL',
                5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
                9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'
            ];
            ?>
            <?php for ($b = 1; $b <= 12; $b++): ?>
                <tr>
                    <td><?= $b ?></td>
                    <td class="text-left font-weight-bold"><?= $namaBulan[$b] ?></td>
                    <td><?= !empty($matrix[$b]['tanggal']) ? tanggal_indo_panjang($matrix[$b]['tanggal']) : '-' ?></td>
                    <td class="text-left"><?= esc($matrix[$b]['nama_masjid'] ?: '-') ?></td>
                    <td class="text-left"><?= esc($matrix[$b]['mc'] ?: '-') ?></td>
                    <td class="text-left"><?= esc($matrix[$b]['doa'] ?: '-') ?></td>
                    <td class="text-left"><?= esc($matrix[$b]['kultum'] ?: '-') ?></td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <div style="margin-top: 50px;" class="no-print">
        <p><small>* Dokumen ini dicetak otomatis dari sistem informasi KUA.</small></p>
    </div>
</body>
</html>
