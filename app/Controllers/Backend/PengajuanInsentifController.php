<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\PersonilModel;
use App\Models\EntitasTypeModel;
use App\Models\BerkasModel;
use App\Models\SettingBerkasModel;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * PengajuanInsentifController — Halaman cetak berkas dan surat pengajuan insentif.
 * Mendukung semua entitas tipe orang melalui parameter entitasType.
 */
class PengajuanInsentifController extends BaseController
{
    protected $personilModel;
    protected $entitasTypeModel;

    public function __construct()
    {
        helper('tanggal');
        $this->personilModel    = new PersonilModel();
        $this->entitasTypeModel = new EntitasTypeModel();
    }

    /**
     * Resolve entitas config atau 404
     */
    private function getEntitasConfig(string $entitasType): array
    {
        $config = $this->entitasTypeModel->getByKode($entitasType);
        if (!$config) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Tipe entitas '{$entitasType}' tidak ditemukan.");
        }

        // --- DYNAMIC ROUTE AUTHORIZATION ---
        $allowedGroups = ['SuperAdmin', 'Admin'];
        if (!empty($config['operator_group'])) {
            $allowedGroups[] = $config['operator_group'];
        }

        if (! function_exists('in_groups')) {
            helper('auth');
        }

        if (! \in_groups($allowedGroups)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Akses Ditolak. Anda tidak memiliki izin operasi untuk data " . $config['nama_label']);
        }

        return $config;
    }

    /**
     * Halaman utama Pengajuan Insentif per entitas
     */
    public function index(string $entitasType)
    {
        $config = $this->getEntitasConfig($entitasType);
        $berkasModel        = new BerkasModel();
        $settingBerkasModel = new SettingBerkasModel();

        $personilList = $this->personilModel->getByEntitas($entitasType);
        $settingBerkas = $settingBerkasModel->getSettingByEntitas($entitasType);

        // Build data with berkas info per personil
        $personilWithBerkas = [];
        foreach ($personilList as $p) {
            $berkasAktif = $berkasModel->getBerkasAktif($entitasType, $p['id']);
            $berkasByType = [];
            foreach ($berkasAktif as $berkas) {
                $berkasByType[$berkas['nama_berkas']] = $berkas;
            }

            $personilWithBerkas[] = [
                'personil' => $p,
                'berkas'   => $berkasByType,
            ];
        }

        $data = [
            'title'              => 'Pengajuan Insentif - ' . $config['nama_label'],
            'breadcrumb'         => [
                ['title' => 'Home', 'url' => 'admin/dashboard'],
                ['title' => $config['nama_label'], 'url' => 'admin/personil/' . $entitasType],
                ['title' => 'Pengajuan Insentif', 'url' => ''],
            ],
            'entitasType'        => $entitasType,
            'entitasConfig'      => $config,
            'personilWithBerkas' => $personilWithBerkas,
            'settingBerkas'      => $settingBerkas,
        ];

        return view('backend/pengajuan_insentif/index', $data);
    }

    // ================================================================
    // PDF Generation Methods
    // ================================================================

    /**
     * Generate Surat Pernyataan ASN
     */
    public function cetakSuratAsn(string $entitasType, $id)
    {
        $config   = $this->getEntitasConfig($entitasType);
        $personil = $this->personilModel->find($id);

        if (!$personil || $personil['entitas_type'] !== $entitasType) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data tidak ditemukan.');
        }

        $data = [
            'personil'      => $personil,
            'entitasConfig' => $config,
            'tanggalCetak'  => tanggal_indo(date('Y-m-d')),
        ];

        $html = view('backend/pengajuan_insentif/pdf/surat_pernyataan_asn', $data);
        return $this->generatePdf($html, 'Surat_Pernyataan_ASN_' . $personil['nama_lengkap']);
    }

    /**
     * Generate Surat Pernyataan Insentif
     */
    public function cetakSuratInsentif(string $entitasType, $id)
    {
        $config   = $this->getEntitasConfig($entitasType);
        $personil = $this->personilModel->find($id);

        if (!$personil || $personil['entitas_type'] !== $entitasType) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data tidak ditemukan.');
        }

        $data = [
            'personil'      => $personil,
            'entitasConfig' => $config,
            'tanggalCetak'  => tanggal_indo(date('Y-m-d')),
        ];

        $html = view('backend/pengajuan_insentif/pdf/surat_pernyataan_insentif', $data);
        return $this->generatePdf($html, 'Surat_Pernyataan_Insentif_' . $personil['nama_lengkap']);
    }

    /**
     * Generate Surat Rekomendasi
     */
    public function cetakSuratRekomendasi(string $entitasType, $id)
    {
        $config   = $this->getEntitasConfig($entitasType);
        $personil = $this->personilModel->find($id);

        if (!$personil || $personil['entitas_type'] !== $entitasType) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data tidak ditemukan.');
        }

        $data = [
            'personil'      => $personil,
            'entitasConfig' => $config,
            'tanggalCetak'  => tanggal_indo(date('Y-m-d')),
        ];

        $html = view('backend/pengajuan_insentif/pdf/surat_rekomendasi', $data);
        return $this->generatePdf($html, 'Surat_Rekomendasi_' . $personil['nama_lengkap']);
    }

    /**
     * Generate Lampiran Berkas (foto + berkas lampiran sebagai gambar)
     */
    public function cetakLampiran(string $entitasType, $id)
    {
        $config   = $this->getEntitasConfig($entitasType);
        $personil = $this->personilModel->find($id);

        if (!$personil || $personil['entitas_type'] !== $entitasType) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data tidak ditemukan.');
        }

        $berkasModel        = new BerkasModel();
        $settingBerkasModel = new SettingBerkasModel();

        $berkasAktif   = $berkasModel->getBerkasAktif($entitasType, $id);
        $settingBerkas = $settingBerkasModel->getSettingByEntitas($entitasType);

        // Convert images to base64 for PDF embedding
        $fotoBase64 = null;
        if (!empty($personil['foto'])) {
            $fotoPath = FCPATH . 'uploads/personil/' . $personil['foto'];
            if (file_exists($fotoPath)) {
                $fotoBase64 = 'data:image/' . pathinfo($fotoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($fotoPath));
            }
        }

        $largeBerkas = [];
        $smallBerkas = [];
        
        foreach ($berkasAktif as $berkas) {
            $filePath = FCPATH . 'uploads/berkas/' . $berkas['nama_file'];
            if (file_exists($filePath)) {
                // Get physical dimensions
                list($width, $height) = getimagesize($filePath);
                
                // Tentukan layout dari setting database
                $isLarge = false;
                $cetakLebar = 100; // default width %
                
                foreach ($settingBerkas as $sb) {
                    if ($sb['nama_berkas'] === $berkas['nama_berkas']) {
                        if (isset($sb['cetak_tipe']) && $sb['cetak_tipe'] === 'full_page') {
                            $isLarge = true;
                        }
                        if (isset($sb['cetak_lebar'])) {
                            $cetakLebar = (int) $sb['cetak_lebar'];
                        }
                        break;
                    }
                }
                
                $imgData = null;
                // If Large and Landscape, physically rotate it for the PDF so it fits the A4 Portrait nicely
                if ($isLarge && $width > $height) {
                    try {
                        $imageService = \Config\Services::image();
                        $tempPath = WRITEPATH . 'uploads/tmp_rotate_' . time() . '_' . rand(100, 999) . '_' . $berkas['nama_file'];
                        
                        $imageService->withFile($filePath)
                                     ->rotate(270) // Rotate -90 degrees
                                     ->save($tempPath);
                        
                        $imgData = base64_encode(file_get_contents($tempPath));
                        @unlink($tempPath); // Cleanup temp file
                    } catch (\Exception $e) {
                        // Fallback safely if library fails
                        $imgData = base64_encode(file_get_contents($filePath));
                    }
                } else {
                    $imgData = base64_encode(file_get_contents($filePath));
                }

                $item = [
                    'nama'   => $berkas['nama_berkas'],
                    'base64' => 'data:image/' . pathinfo($filePath, PATHINFO_EXTENSION) . ';base64,' . $imgData,
                    'lebar'  => $cetakLebar
                ];
                
                if ($isLarge) {
                    $largeBerkas[] = $item;
                } else {
                    $smallBerkas[] = $item;
                }
            }
        }

        $data = [
            'personil'      => $personil,
            'entitasConfig' => $config,
            'fotoBase64'    => $fotoBase64,
            'largeBerkas'   => $largeBerkas,
            'smallBerkas'   => $smallBerkas,
            'settingBerkas' => $settingBerkas,
            'tanggalCetak'  => tanggal_indo(date('Y-m-d')),
        ];

        $html = view('backend/pengajuan_insentif/pdf/lampiran_berkas', $data);
        return $this->generatePdf($html, 'Lampiran_Berkas_' . $personil['nama_lengkap']);
    }

    /**
     * Generate Cetak Gabungan (Merge beberapa PDF jadi satu)
     */
    public function cetakGabungan(string $entitasType, $id)
    {
        $config   = $this->getEntitasConfig($entitasType);
        $personil = $this->personilModel->find($id);

        if (!$personil || $personil['entitas_type'] !== $entitasType) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data tidak ditemukan.');
        }

        $berkasReq = $this->request->getGet('berkas');
        if (empty($berkasReq)) {
             throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Tidak ada dokumen yang dipilih untuk dicetak.');
        }
        $selectedBerkas = explode(',', $berkasReq);

        $htmlList = [];
        $dataUmum = [
            'personil'      => $personil,
            'entitasConfig' => $config,
            'tanggalCetak'  => tanggal_indo(date('Y-m-d')),
        ];

        // 1. Surat ASN
        if (in_array('asn', $selectedBerkas)) {
            $htmlList[] = view('backend/pengajuan_insentif/pdf/surat_pernyataan_asn', $dataUmum);
        }

        // 2. Surat Insentif
        if (in_array('insentif', $selectedBerkas)) {
            $htmlList[] = view('backend/pengajuan_insentif/pdf/surat_pernyataan_insentif', $dataUmum);
        }

        // 3. Surat Rekomendasi
        if (in_array('rekomendasi', $selectedBerkas)) {
            $htmlList[] = view('backend/pengajuan_insentif/pdf/surat_rekomendasi', $dataUmum);
        }

        // 4. Lampiran Berkas
        if (in_array('lampiran', $selectedBerkas)) {
            $berkasModel        = new BerkasModel();
            $settingBerkasModel = new SettingBerkasModel();

            $berkasAktif   = $berkasModel->getBerkasAktif($entitasType, $id);
            $settingBerkas = $settingBerkasModel->getSettingByEntitas($entitasType);

            // Convert images to base64
            $fotoBase64 = null;
            if (!empty($personil['foto'])) {
                $fotoPath = FCPATH . 'uploads/personil/' . $personil['foto'];
                if (file_exists($fotoPath)) {
                    $fotoBase64 = 'data:image/' . pathinfo($fotoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($fotoPath));
                }
            }

            $largeBerkas = [];
            $smallBerkas = [];
            foreach ($berkasAktif as $berkas) {
                $filePath = FCPATH . 'uploads/berkas/' . $berkas['nama_file'];
                if (file_exists($filePath)) {
                    // Cek dimensi fisik untuk landscape/portrait rotation logic
                    list($width, $height) = getimagesize($filePath);
                    $isLarge = false;
                    $cetakLebar = 100;
                    foreach ($settingBerkas as $sb) {
                        if ($sb['nama_berkas'] === $berkas['nama_berkas']) {
                            if (isset($sb['cetak_tipe']) && $sb['cetak_tipe'] === 'full_page') {
                                $isLarge = true;
                            }
                            if (isset($sb['cetak_lebar'])) {
                                $cetakLebar = (int) $sb['cetak_lebar'];
                            }
                            break;
                        }
                    }
                    
                    $imgData = null;
                    if ($isLarge && $width > $height) {
                        try {
                            $imageService = \Config\Services::image();
                            $tempPath = WRITEPATH . 'uploads/tmp_rotate_' . time() . '_' . rand(100, 999) . '_' . $berkas['nama_file'];
                            $imageService->withFile($filePath)->rotate(270)->save($tempPath);
                            $imgData = base64_encode(file_get_contents($tempPath));
                            @unlink($tempPath);
                        } catch (\Exception $e) {
                            $imgData = base64_encode(file_get_contents($filePath));
                        }
                    } else {
                        $imgData = base64_encode(file_get_contents($filePath));
                    }

                    $item = [
                        'nama'   => $berkas['nama_berkas'],
                        'base64' => 'data:image/' . pathinfo($filePath, PATHINFO_EXTENSION) . ';base64,' . $imgData,
                        'lebar'  => $cetakLebar
                    ];
                    
                    if ($isLarge) {
                        $largeBerkas[] = $item;
                    } else {
                        $smallBerkas[] = $item;
                    }
                }
            }

            $dataLampiran = array_merge($dataUmum, [
                'fotoBase64'    => $fotoBase64,
                'largeBerkas'   => $largeBerkas,
                'smallBerkas'   => $smallBerkas,
                'settingBerkas' => $settingBerkas,
            ]);

            $htmlList[] = view('backend/pengajuan_insentif/pdf/lampiran_berkas', $dataLampiran);
        }

        // Combine HTML
        $combinedStyles = "";
        $combinedBody = "";

        foreach ($htmlList as $index => $htmlItem) {
            // Extract styles
            if (preg_match('/<style.*?>(.*?)<\/style>/s', $htmlItem, $styleMatches)) {
                $combinedStyles .= "\n/* Styles part " . ($index + 1) . " */\n" . $styleMatches[1];
            }
            // Extract body
            if (preg_match('/<body.*?>(.*?)<\/body>/s', $htmlItem, $bodyMatches)) {
                if ($index > 0) {
                    $combinedBody .= "\n<div style=\"page-break-before: always;\"></div>\n";
                }
                $combinedBody .= $bodyMatches[1];
            } else {
                if ($index > 0) {
                    $combinedBody .= "\n<div style=\"page-break-before: always;\"></div>\n";
                }
                $combinedBody .= $htmlItem;
            }
        }

        $finalHtml = "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <style>
        " . $combinedStyles . "
    </style>
</head>
<body>
    " . $combinedBody . "
</body>
</html>";

        return $this->generatePdf($finalHtml, 'Dokumen_Gabungan_' . $personil['nama_lengkap']);
    }

    /**
     * Generate Bulk ZIP of PDFs (Mencetak Semua Personil ke dalam satu ZIP)
     */
    public function cetakBulkZip(string $entitasType)
    {
        $config = $this->getEntitasConfig($entitasType);
        $berkasReq = $this->request->getGet('berkas');

        if (empty($berkasReq)) {
            return redirect()->back()->with('error', 'Tidak ada dokumen yang dipilih untuk dicetak bulk.');
        }
        $selectedBerkas = explode(',', $berkasReq);

        // Ambil semua personil untuk entitas ini
        $semuaPersonil = $this->personilModel->getByEntitas($entitasType);
        
        if (empty($semuaPersonil)) {
            return redirect()->back()->with('error', 'Data ' . $config['nama_label'] . ' masih kosong, tidak ada yang bisa dicetak.');
        }

        // Setup ZipArchive
        $zip = new \ZipArchive();
        $zipFileName = 'Bulk_Cetak_' . $config['nama_label'] . '_' . date('Ymd_His') . '.zip';
        $zipFilePath = WRITEPATH . 'uploads/' . $zipFileName;

        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            return redirect()->back()->with('error', 'Gagal membuat file ZIP di server.');
        }

        // Setup Models for lampiran
        $berkasModel        = new BerkasModel();
        $settingBerkasModel = new SettingBerkasModel();
        $settingBerkas      = $settingBerkasModel->getSettingByEntitas($entitasType);

        // Render each personil
        foreach ($semuaPersonil as $personil) {
            $htmlList = [];
            $dataUmum = [
                'personil'      => $personil,
                'entitasConfig' => $config,
                'tanggalCetak'  => tanggal_indo(date('Y-m-d')),
            ];

            // 1. Surat ASN
            if (in_array('asn', $selectedBerkas)) {
                $htmlList[] = view('backend/pengajuan_insentif/pdf/surat_pernyataan_asn', $dataUmum);
            }

            // 2. Surat Insentif
            if (in_array('insentif', $selectedBerkas)) {
                $htmlList[] = view('backend/pengajuan_insentif/pdf/surat_pernyataan_insentif', $dataUmum);
            }

            // 3. Surat Rekomendasi
            if (in_array('rekomendasi', $selectedBerkas)) {
                $htmlList[] = view('backend/pengajuan_insentif/pdf/surat_rekomendasi', $dataUmum);
            }

            // 4. Lampiran Berkas
            if (in_array('lampiran', $selectedBerkas)) {
                $berkasAktif = $berkasModel->getBerkasAktif($entitasType, $personil['id']);

                $fotoBase64 = null;
                if (!empty($personil['foto'])) {
                    $fotoPath = FCPATH . 'uploads/personil/' . $personil['foto'];
                    if (file_exists($fotoPath)) {
                        $fotoBase64 = 'data:image/' . pathinfo($fotoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($fotoPath));
                    }
                }

                $largeBerkas = [];
                $smallBerkas = [];
                foreach ($berkasAktif as $berkas) {
                    $filePath = FCPATH . 'uploads/berkas/' . $berkas['nama_file'];
                    if (file_exists($filePath)) {
                        list($width, $height) = getimagesize($filePath);
                        $isLarge = false;
                        $cetakLebar = 100;
                        foreach ($settingBerkas as $sb) {
                            if ($sb['nama_berkas'] === $berkas['nama_berkas']) {
                                if (isset($sb['cetak_tipe']) && $sb['cetak_tipe'] === 'full_page') {
                                    $isLarge = true;
                                }
                                if (isset($sb['cetak_lebar'])) {
                                    $cetakLebar = (int) $sb['cetak_lebar'];
                                }
                                break;
                            }
                        }
                        
                        $imgData = null;
                        if ($isLarge && $width > $height) {
                            try {
                                $imageService = \Config\Services::image();
                                $tempPath = WRITEPATH . 'uploads/tmp_rotate_' . time() . '_' . rand(100, 999) . '_' . $berkas['nama_file'];
                                $imageService->withFile($filePath)->rotate(270)->save($tempPath);
                                $imgData = base64_encode(file_get_contents($tempPath));
                                @unlink($tempPath);
                            } catch (\Exception $e) {
                                $imgData = base64_encode(file_get_contents($filePath));
                            }
                        } else {
                            $imgData = base64_encode(file_get_contents($filePath));
                        }

                        $item = [
                            'nama'   => $berkas['nama_berkas'],
                            'base64' => 'data:image/' . pathinfo($filePath, PATHINFO_EXTENSION) . ';base64,' . $imgData,
                            'lebar'  => $cetakLebar
                        ];
                        
                        if ($isLarge) {
                            $largeBerkas[] = $item;
                        } else {
                            $smallBerkas[] = $item;
                        }
                    }
                }

                $dataLampiran = array_merge($dataUmum, [
                    'fotoBase64'    => $fotoBase64,
                    'largeBerkas'   => $largeBerkas,
                    'smallBerkas'   => $smallBerkas,
                    'settingBerkas' => $settingBerkas,
                ]);

                $htmlList[] = view('backend/pengajuan_insentif/pdf/lampiran_berkas', $dataLampiran);
            }

            // Combine HTML untuk personil ini
            $combinedStyles = "";
            $combinedBody = "";

            foreach ($htmlList as $index => $htmlItem) {
                if (preg_match('/<style.*?>(.*?)<\/style>/s', $htmlItem, $styleMatches)) {
                    $combinedStyles .= "\n/* Styles part " . ($index + 1) . " */\n" . $styleMatches[1];
                }
                if (preg_match('/<body.*?>(.*?)<\/body>/s', $htmlItem, $bodyMatches)) {
                    if ($index > 0) {
                        $combinedBody .= "\n<div style=\"page-break-before: always;\"></div>\n";
                    }
                    $combinedBody .= $bodyMatches[1];
                } else {
                    if ($index > 0) {
                        $combinedBody .= "\n<div style=\"page-break-before: always;\"></div>\n";
                    }
                    $combinedBody .= $htmlItem;
                }
            }

            $finalHtml = "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <style>
        " . $combinedStyles . "
    </style>
</head>
<body>
    " . $combinedBody . "
</body>
</html>";

            // Render PDF via Dompdf but return string
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('defaultFont', 'serif');

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($finalHtml);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $pdfOutput = $dompdf->output();

            // Construct valid filename (bersihkan NIK dan Nama dari karakter tidak valid)
            $cleanNik = preg_replace('/[^0-9]/', '', $personil['nik'] ?? 'NONIK');
            $cleanNama = preg_replace('/[^a-zA-Z0-9_\- ]/', '_', $personil['nama_lengkap']);
            $pdfFileName = $cleanNik . '_' . $cleanNama . '.pdf';

            // Masukkan PDF Byte Stream ke dalam ZIP archive
            if (!empty($pdfOutput)) {
                $zip->addFromString($pdfFileName, $pdfOutput);
            }
        }

        $zip->close();

        // Download zip file lalu hapus setelah dikirim
        if (file_exists($zipFilePath)) {
            $fileData = file_get_contents($zipFilePath);
            @unlink($zipFilePath); // Clean up server storage

            return $this->response->download($zipFileName, $fileData)->setFileName($zipFileName);
        }

        return redirect()->back()->with('error', 'Gagal memproses file ZIP.');
    }

    // ================================================================
    // Dompdf shared method
    // ================================================================

    /**
     * Render HTML ke PDF dan stream ke browser
     */
    private function generatePdf(string $html, string $filename): void
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'serif');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Clean filename
        $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $filename);
        $dompdf->stream($filename . '.pdf', ['Attachment' => false]);
        exit;
    }
}
