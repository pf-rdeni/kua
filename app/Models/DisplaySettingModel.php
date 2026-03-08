<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel tbl_display_setting
 * Menyimpan pengaturan display masjid per masjid
 */
class DisplaySettingModel extends Model
{
    protected $table            = 'tbl_display_setting';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_masjid_mushola', 'nama_display', 'template_aktif', 'orientasi',
        'nama_masjid_display', 'alamat_display', 'running_text',
        'logo', 'wallpaper', 'metode_hitung',
        'sholat_jumat', 'interval_sync', 'aktif',
        // JSON grouped settings
        'koreksi_waktu',       // JSON: {subuh, dzuhur, ashar, maghrib, isya}
        'timer_iqomah',        // JSON: {subuh, dzuhur, ashar, maghrib, isya}
        'mode_sholat_event',   // JSON: {menjelang_adzan, adzan, qobliyah, iqomah, sholat, badiyah}
        'mode_tarawih_json',   // JSON: {aktif, durasi, gambar}
        'mode_hari_raya',      // JSON: {idul_adha:{...}, idul_fitri:{...}}
        'opsi_waktu_sholat',   // JSON: {qobliyah:{...}, badiyah:{...}}
        'display_setting',     // JSON: General settings per template namespace {modern1:{...}}
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validasi
    protected $validationRules = [
        'nama_display'      => 'required|min_length[3]|max_length[100]',
        'template_aktif'    => 'required|in_list[klasik,modern,modern1,modern2,keuangan]',
        'orientasi'         => 'required|in_list[horizontal,vertikal]',
        'interval_sync'     => 'permit_empty|integer|greater_than[9]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Ambil semua display beserta nama masjid dari tabel relasi
     */
    public function getDisplayDenganMasjid()
    {
        return $this->select('tbl_display_setting.*, tbl_masjid_mushola.nama as nama_masjid, tbl_masjid_mushola.latitude, tbl_masjid_mushola.longitude')
                    ->join('tbl_masjid_mushola', 'tbl_masjid_mushola.id_masjid_mushola = tbl_display_setting.id_masjid_mushola', 'left')
                    ->orderBy('tbl_display_setting.id', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil satu display beserta data masjid
     */
    public function getDisplayById($id)
    {
        return $this->select('tbl_display_setting.*, tbl_masjid_mushola.nama as nama_masjid, tbl_masjid_mushola.alamat as alamat_masjid, tbl_masjid_mushola.latitude, tbl_masjid_mushola.longitude, tbl_masjid_mushola.foto as foto_masjid')
                    ->join('tbl_masjid_mushola', 'tbl_masjid_mushola.id_masjid_mushola = tbl_display_setting.id_masjid_mushola', 'left')
                    ->where('tbl_display_setting.id', $id)
                    ->first();
    }
}
