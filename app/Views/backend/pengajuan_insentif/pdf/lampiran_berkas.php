<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: serif; font-size: 12pt; line-height: 1.4; margin: 30px; }
        h2 { text-align: center; margin-bottom: 20px; font-size: 14pt; }
        .info { margin-bottom: 20px; }
        .info table td { padding: 2px 8px; vertical-align: top; }
        .foto-section { text-align: center; margin: 20px 0; }
        .foto-section img { max-width: 200px; max-height: 250px; border: 1px solid #ccc; }
        .berkas-section { margin-top: 20px; page-break-inside: avoid; }
        .berkas-section h4 { margin: 10px 0 5px; font-size: 12pt; border-bottom: 1px solid #999; padding-bottom: 3px; }
        .berkas-section img { max-width: 100%; max-height: 400px; border: 1px solid #ccc; display: block; margin: 5px auto; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <h2>LAMPIRAN BERKAS<br><?= esc(strtoupper($entitasConfig['nama_label'])) ?></h2>

    <div class="info">
        <table>
            <tr>
                <td style="width: 140px;">Nama Lengkap</td>
                <td style="width: 10px;">:</td>
                <td><strong><?= esc($personil['nama_lengkap']) ?></strong></td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td><?= esc($personil['nik'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td><?= esc($personil['alamat'] ?? '-') ?></td>
            </tr>
        </table>
    </div>

    <?php if ($fotoBase64): ?>
    <div class="foto-section">
        <h4>Pas Foto</h4>
        <img src="<?= $fotoBase64 ?>" alt="Foto">
    </div>
    <?php endif; ?>

    <?php if (!empty($berkasImages)): ?>
        <?php foreach ($berkasImages as $idx => $bi): ?>
        <div class="berkas-section<?= $idx > 0 ? ' page-break' : '' ?>">
            <h4><?= esc($bi['nama']) ?></h4>
            <img src="<?= $bi['base64'] ?>" alt="<?= esc($bi['nama']) ?>">
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align: center; color: #999;">Belum ada berkas lampiran.</p>
    <?php endif; ?>
</body>
</html>
