<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: serif; font-size: 12pt; line-height: 1.4; margin: 30px; }
        h2 { text-align: center; margin-bottom: 20px; font-size: 14pt; }
        .info { margin-bottom: 20px; }
        .info table td { padding: 2px 8px; vertical-align: top; }
        .berkas-small-group { text-align: center; margin-top: 20px; font-size: 0; }
        .berkas-item-small { display: inline-block; margin: 10px 1%; vertical-align: top; text-align: center; page-break-inside: avoid; font-size: 12pt; }
        .berkas-item-small h4 { margin: 0 0 5px; font-size: 11pt; border-bottom: 1px solid #999; padding-bottom: 3px; }
        .berkas-item-small img { width: 100%; border: 1px solid #ccc; }
        
        .berkas-section { text-align: center; margin-top: 10px; page-break-inside: avoid; width: 100%; clear: both; }
        .berkas-title { margin: 0 auto 10px auto; font-size: 14pt; border-bottom: 2px solid #666; padding-bottom: 5px; display: inline-block; font-weight: bold; }
        .berkas-img-large { border: 1px solid #ccc; display: inline-block; max-height: 980px; }
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

    <div class="berkas-small-group">
        <?php if ($fotoBase64): ?>
        <div class="berkas-item-small" style="width: 30%;">
            <h4>Pas Foto</h4>
            <img src="<?= $fotoBase64 ?>" alt="Foto">
        </div>
        <?php endif; ?>

        <?php if (!empty($smallBerkas)): ?>
            <?php foreach ($smallBerkas as $bi): ?>
            <div class="berkas-item-small" style="width: <?= esc($bi['lebar'] - 2) ?>%;">
                <h4><?= esc($bi['nama']) ?></h4>
                <img src="<?= $bi['base64'] ?>" alt="<?= esc($bi['nama']) ?>">
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (!empty($largeBerkas)): ?>
        <?php foreach ($largeBerkas as $idx => $bi): ?>
        <div class="berkas-section <?= (!empty($smallBerkas) || $fotoBase64 || $idx > 0) ? 'page-break' : '' ?>">
            <div class="berkas-title"><?= esc($bi['nama']) ?></div><br>
            <img src="<?= $bi['base64'] ?>" alt="<?= esc($bi['nama']) ?>" class="berkas-img-large" style="width: <?= esc($bi['lebar']) ?>%; max-width: <?= esc($bi['lebar']) ?>%;">
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (empty($smallBerkas) && empty($largeBerkas) && !$fotoBase64): ?>
        <p style="text-align: center; color: #999;">Belum ada lampiran.</p>
    <?php endif; ?>
</body>
</html>
