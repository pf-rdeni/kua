<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: serif; font-size: 12pt; line-height: 1.6; margin: 40px; }
        .kop { text-align: center; margin-bottom: 30px; border-bottom: 3px double #000; padding-bottom: 10px; }
        .kop h3 { margin: 0; font-size: 14pt; }
        .kop h2 { margin: 5px 0; font-size: 16pt; text-transform: uppercase; }
        .kop p { margin: 2px 0; font-size: 10pt; }
        .judul { text-align: center; margin: 20px 0; }
        .judul h3 { text-decoration: underline; margin-bottom: 5px; }
        .isi { text-align: justify; margin: 0 20px; }
        .isi p { margin: 8px 0; text-indent: 40px; }
        table.identitas { margin: 10px 20px; }
        table.identitas td { padding: 3px 8px; vertical-align: top; }
        .ttd { margin-top: 40px; text-align: right; padding-right: 40px; }
        .ttd .nama { margin-top: 80px; font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="kop">
        <h3>KEMENTERIAN AGAMA REPUBLIK INDONESIA</h3>
        <h2>KANTOR URUSAN AGAMA</h2>
        <p>Alamat KUA</p>
    </div>

    <div class="judul">
        <h3>SURAT PERNYATAAN</h3>
        <p>Tentang Bukan Aparatur Sipil Negara (ASN)</p>
    </div>

    <div class="isi">
        <p>Yang bertanda tangan di bawah ini:</p>

        <table class="identitas">
            <tr>
                <td style="width: 160px;">Nama Lengkap</td>
                <td style="width: 10px;">:</td>
                <td><strong><?= esc($personil['nama_lengkap']) ?></strong></td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td><?= esc($personil['nik'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Tempat, Tgl Lahir</td>
                <td>:</td>
                <td><?= esc($personil['tempat_lahir'] ?? '-') ?>, <?= !empty($personil['tanggal_lahir']) ? tanggal_indo($personil['tanggal_lahir']) : '-' ?></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td><?= esc($personil['alamat'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td><?= esc($entitasConfig['nama_label']) ?></td>
            </tr>
        </table>

        <p>Dengan ini menyatakan dengan sesungguhnya bahwa saya <strong>BUKAN</strong> merupakan Aparatur Sipil Negara (ASN) baik Pegawai Negeri Sipil (PNS) maupun Pegawai Pemerintah dengan Perjanjian Kerja (PPPK) pada instansi pemerintah manapun.</p>

        <p>Demikian surat pernyataan ini saya buat dengan sebenar-benarnya. Apabila dikemudian hari ternyata pernyataan ini tidak benar, saya bersedia menerima sanksi sesuai dengan ketentuan peraturan perundang-undangan yang berlaku.</p>
    </div>

    <div class="ttd">
        <p>................, <?= $tanggalCetak ?></p>
        <p>Yang membuat pernyataan,</p>
        <p class="nama"><?= esc($personil['nama_lengkap']) ?></p>
    </div>
</body>
</html>
