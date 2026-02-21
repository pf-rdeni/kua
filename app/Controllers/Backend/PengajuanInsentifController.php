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

        $berkasImages = [];
        foreach ($berkasAktif as $berkas) {
            $filePath = FCPATH . 'uploads/berkas/' . $berkas['nama_file'];
            if (file_exists($filePath)) {
                $berkasImages[] = [
                    'nama'   => $berkas['nama_berkas'],
                    'base64' => 'data:image/' . pathinfo($filePath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($filePath)),
                ];
            }
        }

        $data = [
            'personil'      => $personil,
            'entitasConfig' => $config,
            'fotoBase64'    => $fotoBase64,
            'berkasImages'  => $berkasImages,
            'settingBerkas' => $settingBerkas,
            'tanggalCetak'  => tanggal_indo(date('Y-m-d')),
        ];

        $html = view('backend/pengajuan_insentif/pdf/lampiran_berkas', $data);
        return $this->generatePdf($html, 'Lampiran_Berkas_' . $personil['nama_lengkap']);
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
