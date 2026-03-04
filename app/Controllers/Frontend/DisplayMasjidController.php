<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\DisplaySettingModel;
use App\Models\DisplayKontenModel;
use App\Models\KeuanganTransaksiModel;

/**
 * Controller publik untuk Display Masjid (fullscreen TV)
 * Tidak memerlukan autentikasi - bisa diakses langsung oleh TV/mini-PC
 */
class DisplayMasjidController extends BaseController
{
    protected $displayModel;
    protected $kontenModel;

    public function __construct()
    {
        $this->displayModel = new DisplaySettingModel();
        $this->kontenModel  = new DisplayKontenModel();
    }

    /**
     * Tampilkan display masjid fullscreen sesuai template
     */
    public function show($id)
    {
        $display = $this->displayModel->getDisplayById($id);

        // Jika display tidak ditemukan atau tidak aktif
        if (!$display || !$display['aktif']) {
            return view('frontend/display/not_found');
        }

        // Ambil konten aktif untuk display ini
        $kontens = $this->kontenModel->getKontenAktif($id);

        // Kelompokkan konten berdasarkan tipe
        $kontenByTipe = [];
        foreach ($kontens as $k) {
            $kontenByTipe[$k['tipe']][] = $k;
        }

        // Tentukan nama masjid yang tampil
        $namaMasjid = !empty($display['nama_masjid_display'])
            ? $display['nama_masjid_display']
            : ($display['nama_masjid'] ?? 'Masjid');

        $alamatDisplay = !empty($display['alamat_display'])
            ? $display['alamat_display']
            : ($display['alamat_masjid'] ?? '');

        $opsiWaktu = !empty($display['opsi_waktu_sholat']) ? json_decode($display['opsi_waktu_sholat'], true) : [];
        $manualLat = $opsiWaktu['koordinat']['latitude'] ?? null;
        $manualLon = $opsiWaktu['koordinat']['longitude'] ?? null;

        $finalLat = ($manualLat !== null && $manualLat !== '') ? $manualLat : ($display['latitude'] ?? 1.0408);
        $finalLon = ($manualLon !== null && $manualLon !== '') ? $manualLon : ($display['longitude'] ?? 104.2417);

        // Parse koreksi_waktu JSON untuk mendapatkan koreksi_hijriah
        $koreksiWaktu = !empty($display['koreksi_waktu']) ? json_decode($display['koreksi_waktu'], true) : [];
        $koreksiHijriah = (int)($koreksiWaktu['hijriah'] ?? 0);

        // Data untuk view
        $data = [
            'display'          => $display,
            'kontens'          => $kontens,
            'kontenByTipe'     => $kontenByTipe,
            'namaMasjid'       => $namaMasjid,
            'alamatDisplay'    => $alamatDisplay,
            'latitude'         => (float)$finalLat,
            'longitude'        => (float)$finalLon,
            'koreksiHijriah'   => $koreksiHijriah,
        ];

        // Pilih template sesuai pengaturan
        $template = !empty($display['template_aktif']) ? $display['template_aktif'] : 'klasik';
        $viewFile = 'frontend/display/template_' . $template;

        return view($viewFile, $data);
    }

    /**
     * API endpoint: Data display untuk AJAX sync
     * Dikonsumsi oleh display-engine.js untuk sinkronisasi background
     */
    public function apiData($id)
    {
        $display = $this->displayModel->getDisplayById($id);

        if (!$display) {
            return $this->response->setJSON(['error' => 'Display tidak ditemukan'])->setStatusCode(404);
        }

        // Ambil konten aktif
        $kontens = $this->kontenModel->getKontenAktif($id);

        // Kelompokkan konten berdasarkan tipe
        $kontenByTipe = [];
        foreach ($kontens as $k) {
            $kontenByTipe[$k['tipe']][] = $k;
        }

        // Nama dan alamat
        $namaMasjid = !empty($display['nama_masjid_display'])
            ? $display['nama_masjid_display']
            : ($display['nama_masjid'] ?? 'Masjid');

        $alamatDisplay = !empty($display['alamat_display'])
            ? $display['alamat_display']
            : ($display['alamat_masjid'] ?? '');

        // Decode JSON columns dengan default values
        $koreksi  = !empty($display['koreksi_waktu']) ? json_decode($display['koreksi_waktu'], true) : [];
        $iqomah   = !empty($display['timer_iqomah']) ? json_decode($display['timer_iqomah'], true) : [];
        $modeEvent = !empty($display['mode_sholat_event']) ? json_decode($display['mode_sholat_event'], true) : [];
        $modeTarawih = !empty($display['mode_tarawih_json']) ? json_decode($display['mode_tarawih_json'], true) : [];
        $modeHariRaya = !empty($display['mode_hari_raya']) ? json_decode($display['mode_hari_raya'], true) : [];
        $opsiWaktu = !empty($display['opsi_waktu_sholat']) ? json_decode($display['opsi_waktu_sholat'], true) : [];

        $opsiQobliyah = $opsiWaktu['qobliyah'] ?? ['subuh'=>1, 'dzuhur'=>1, 'ashar'=>0, 'maghrib'=>1, 'isya'=>1];
        $opsiBadiyah  = $opsiWaktu['badiyah']  ?? ['subuh'=>0, 'dzuhur'=>1, 'ashar'=>0, 'maghrib'=>1, 'isya'=>1];

        // Helper: resolve gambar path ke base_url
        $resolveGambar = function($path) {
            return !empty($path) ? base_url($path) : null;
        };

        $responseData = [
            'success'       => true,
            'timestamp'     => date('Y-m-d H:i:s'),
            'display'       => [
                'id'              => (int)$display['id'],
                'nama_display'    => $display['nama_display'],
                'template_aktif'  => $display['template_aktif'],
                'orientasi'       => $display['orientasi'],
                'nama_masjid'     => $namaMasjid,
                'alamat'          => $alamatDisplay,
                'running_text'    => $display['running_text'] ?? '',
                'logo'            => $display['logo'] ? base_url($display['logo']) : null,
                'wallpaper'       => $display['wallpaper'] ? base_url($display['wallpaper']) : null,
                'metode_hitung'   => $display['metode_hitung'],
                'latitude'        => (float)(($opsiWaktu['koordinat']['latitude'] ?? null) !== null && ($opsiWaktu['koordinat']['latitude'] ?? null) !== '' ? $opsiWaktu['koordinat']['latitude'] : ($display['latitude'] ?? 1.0408)),
                'longitude'       => (float)(($opsiWaktu['koordinat']['longitude'] ?? null) !== null && ($opsiWaktu['koordinat']['longitude'] ?? null) !== '' ? $opsiWaktu['koordinat']['longitude'] : ($display['longitude'] ?? 104.2417)),
                'interval_sync'   => (int)$display['interval_sync'],
                'sholat_jumat'    => (int)$display['sholat_jumat'],
                // Koreksi jadwal (dari JSON)
                'koreksi' => [
                    'subuh'   => (int)($koreksi['subuh'] ?? 0),
                    'dzuhur'  => (int)($koreksi['dzuhur'] ?? 0),
                    'ashar'   => (int)($koreksi['ashar'] ?? 0),
                    'maghrib' => (int)($koreksi['maghrib'] ?? 0),
                    'isya'    => (int)($koreksi['isya'] ?? 0),
                    'hijriah' => (int)($koreksi['hijriah'] ?? 0),
                ],
                // Durasi iqomah (dari JSON)
                'iqomah' => [
                    'subuh'   => (int)($iqomah['subuh'] ?? 10),
                    'dzuhur'  => (int)($iqomah['dzuhur'] ?? 10),
                    'ashar'   => (int)($iqomah['ashar'] ?? 10),
                    'maghrib' => (int)($iqomah['maghrib'] ?? 5),
                    'isya'    => (int)($iqomah['isya'] ?? 10),
                ],
                // Mode event sholat (dari JSON, pisah iqomah & sholat)
                'modeSholat' => [
                    'menjelangAdzan' => [
                        'aktif'  => (int)($modeEvent['menjelang_adzan']['aktif'] ?? 1),
                        'durasi' => (int)($modeEvent['menjelang_adzan']['durasi'] ?? 10),
                        'gambar' => $resolveGambar($modeEvent['menjelang_adzan']['gambar'] ?? null),
                    ],
                    'adzan' => [
                        'aktif'  => (int)($modeEvent['adzan']['aktif'] ?? 1),
                        'durasi' => (int)($modeEvent['adzan']['durasi'] ?? 7),
                        'gambar' => $resolveGambar($modeEvent['adzan']['gambar'] ?? null),
                    ],
                    'qobliyah' => [
                        'aktif'  => (int)($modeEvent['qobliyah']['aktif'] ?? 0),
                        'durasi' => (int)($modeEvent['qobliyah']['durasi'] ?? 5),
                        'gambar' => $resolveGambar($modeEvent['qobliyah']['gambar'] ?? null),
                        'opsi'   => $opsiQobliyah,
                    ],
                    'iqomah' => [
                        'aktif'  => (int)($modeEvent['iqomah']['aktif'] ?? 1),
                        'gambar' => $resolveGambar($modeEvent['iqomah']['gambar'] ?? null),
                    ],
                    'sholat' => [
                        'aktif'  => (int)($modeEvent['sholat']['aktif'] ?? 1),
                        'durasi' => (int)($modeEvent['sholat']['durasi'] ?? 15),
                        'gambar' => $resolveGambar($modeEvent['sholat']['gambar'] ?? null),
                    ],
                    'badiyah' => [
                        'aktif'  => (int)($modeEvent['badiyah']['aktif'] ?? 0),
                        'durasi' => (int)($modeEvent['badiyah']['durasi'] ?? 5),
                        'gambar' => $resolveGambar($modeEvent['badiyah']['gambar'] ?? null),
                        'opsi'   => $opsiBadiyah,
                    ],
                    'tarawih' => [
                        'aktif'  => (int)($modeTarawih['aktif'] ?? 0),
                        'durasi' => (int)($modeTarawih['durasi'] ?? 60),
                        'gambar' => $resolveGambar($modeTarawih['gambar'] ?? null),
                    ],
                    'idulAdha' => [
                        'aktif'   => (int)($modeHariRaya['idul_adha']['aktif'] ?? 0),
                        'durasi'  => (int)($modeHariRaya['idul_adha']['durasi'] ?? 60),
                        'tanggal' => $modeHariRaya['idul_adha']['tanggal'] ?? null,
                        'gambar'  => $resolveGambar($modeHariRaya['idul_adha']['gambar'] ?? null),
                    ],
                    'idulFitri' => [
                        'aktif'   => (int)($modeHariRaya['idul_fitri']['aktif'] ?? 0),
                        'durasi'  => (int)($modeHariRaya['idul_fitri']['durasi'] ?? 60),
                        'tanggal' => $modeHariRaya['idul_fitri']['tanggal'] ?? null,
                        'gambar'  => $resolveGambar($modeHariRaya['idul_fitri']['gambar'] ?? null),
                    ],
                ],
            ],
            'konten' => $kontenByTipe,
        ];

        return $this->response->setJSON($responseData);
    }

    /**
     * API endpoint: Cek update tercepat (lightweight polling)
     * Hanya mengembalikan timestamp kapan display/konten terakhir diubah
     */
    public function checkUpdate($id)
    {
        $display = $this->displayModel->getDisplayById($id);
        if (!$display) {
            return $this->response->setJSON(['error' => 'Not found'])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'updated_at' => $display['updated_at'] ?? '2000-01-01 00:00:00'
        ]);
    }

    /**
     * API endpoint: Data ringkasan keuangan masjid
     * Untuk template keuangan
     */
    public function apiKeuangan($id)
    {
        $display = $this->displayModel->find($id);
        if (!$display) {
            return $this->response->setJSON(['error' => 'Display tidak ditemukan'])->setStatusCode(404);
        }

        $idMasjid = $display['id_masjid_mushola'];

        // Coba ambil data keuangan jika model tersedia
        $keuanganData = [
            'pemasukan'      => 0,
            'pengeluaran'    => 0,
            'saldo'          => 0,
            'transaksi'      => [],
            'rekap_kategori' => [],
        ];

        try {
            $transaksiModel = new KeuanganTransaksiModel();
            $bulanIni = date('Y-m');

            // Ambil ringkasan keuangan bulan ini untuk masjid terkait
            $transaksis = $transaksiModel
                ->select('tbl_keuangan_transaksi.*, tbl_keuangan_kategori.nama_kategori')
                ->join('tbl_keuangan_kategori', 'tbl_keuangan_kategori.id = tbl_keuangan_transaksi.id_kategori', 'left')
                ->where('tbl_keuangan_transaksi.entitas_type', 'masjid_mushola')
                ->where('tbl_keuangan_transaksi.entitas_id', $idMasjid)
                ->where("DATE_FORMAT(tbl_keuangan_transaksi.tanggal_transaksi, '%Y-%m')", $bulanIni)
                ->orderBy('tbl_keuangan_transaksi.tanggal_transaksi', 'DESC')
                ->findAll(10); // 10 transaksi terakhir

            $pemasukan = 0;
            $pengeluaran = 0;
            foreach ($transaksis as $t) {
                if ($t['jenis'] === 'pemasukan') {
                    $pemasukan += (float)$t['jumlah'];
                } else {
                    $pengeluaran += (float)$t['jumlah'];
                }
            }
            
            // Ambil data rekap kategori untuk Chart
            $rekapKategori = $transaksiModel->getRekapKategori('masjid_mushola', $idMasjid, (int)date('Y'), (int)date('m'));

            $keuanganData = [
                'pemasukan'      => $pemasukan,
                'pengeluaran'    => $pengeluaran,
                'saldo'          => $pemasukan - $pengeluaran,
                'periode'        => date('F Y'),
                'rekap_kategori' => $rekapKategori,
                'transaksi'      => array_map(function($t) {
                    return [
                        'tanggal'    => $t['tanggal_transaksi'],
                        'keterangan' => $t['keterangan'] ?? '',
                        'kategori'   => $t['nama_kategori'] ?? '',
                        'jenis'      => $t['jenis'],
                        'jumlah'     => (float)$t['jumlah'],
                    ];
                }, $transaksis),
            ];
        } catch (\Exception $e) {
            // Jika model keuangan tidak tersedia, kembalikan data kosong
            log_message('warning', 'Display Keuangan: ' . $e->getMessage());
        }

        return $this->response->setJSON([
            'success'  => true,
            'keuangan' => $keuanganData,
        ]);
    }
}
