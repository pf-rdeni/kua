<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel tbl_display_konten
 * Menyimpan konten dinamis display masjid (slide, info, laporan)
 */
class DisplayKontenModel extends Model
{
    protected $table            = 'tbl_display_konten';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_display_setting', 'tipe', 'judul', 'konten', 'gambar',
        'urutan', 'aktif', 'tanggal_mulai', 'tanggal_selesai',
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validasi
    protected $validationRules = [
        'id_display_setting' => 'required|integer',
        'tipe'               => 'required|in_list[info_kegiatan,gambar_slide,laporan_keuangan,jadwal_imsyakiyah,pengumuman]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Ambil konten aktif berdasarkan display dan tanggal saat ini
     * Hanya menampilkan konten yang aktif dan dalam rentang tanggal
     */
    public function getKontenAktif($idDisplaySetting)
    {
        $today = date('Y-m-d');

        return $this->where('id_display_setting', $idDisplaySetting)
                    ->where('aktif', 1)
                    ->groupStart()
                        ->where('tanggal_mulai IS NULL')
                        ->orWhere('tanggal_mulai <=', $today)
                    ->groupEnd()
                    ->groupStart()
                        ->where('tanggal_selesai IS NULL')
                        ->orWhere('tanggal_selesai >=', $today)
                    ->groupEnd()
                    ->orderBy('urutan', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil konten berdasarkan tipe tertentu
     */
    public function getKontenByTipe($idDisplaySetting, $tipe)
    {
        return $this->where('id_display_setting', $idDisplaySetting)
                    ->where('tipe', $tipe)
                    ->where('aktif', 1)
                    ->orderBy('urutan', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil semua konten untuk halaman admin (termasuk yang tidak aktif)
     */
    public function getAllKonten($idDisplaySetting)
    {
        return $this->where('id_display_setting', $idDisplaySetting)
                    ->orderBy('tipe', 'ASC')
                    ->orderBy('urutan', 'ASC')
                    ->findAll();
    }
}
