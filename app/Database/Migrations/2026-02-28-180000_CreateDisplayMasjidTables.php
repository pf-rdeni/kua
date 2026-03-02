<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration untuk membuat tabel display masjid
 * - tbl_display_setting: pengaturan display per masjid
 * - tbl_display_konten: konten dinamis (slide, info, dsb)
 */
class CreateDisplayMasjidTables extends Migration
{
    public function up()
    {
        // ============================================================
        // Tabel Pengaturan Display Masjid
        // ============================================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_masjid_mushola' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'FK ke tbl_masjid_mushola',
            ],
            'nama_display' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Display Utama',
                'comment'    => 'Nama identitas display',
            ],
            'template_aktif' => [
                'type'       => 'ENUM',
                'constraint' => ['klasik', 'modern', 'keuangan'],
                'default'    => 'klasik',
                'comment'    => 'Template tampilan yang digunakan',
            ],
            'orientasi' => [
                'type'       => 'ENUM',
                'constraint' => ['horizontal', 'vertikal'],
                'default'    => 'horizontal',
                'comment'    => 'Orientasi display: horizontal (landscape) atau vertikal (portrait)',
            ],
            'nama_masjid_display' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
                'comment'    => 'Nama masjid yang ditampilkan (override dari data masjid)',
            ],
            'alamat_display' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Alamat yang ditampilkan di display',
            ],
            'running_text' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Teks berjalan di bagian bawah display',
            ],
            'logo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path file logo masjid',
            ],
            'wallpaper' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path file background/wallpaper',
            ],
            // Pengaturan jadwal sholat
            'metode_hitung' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Kemenag',
                'comment'    => 'Metode perhitungan: MWL, ISNA, Egypt, Makkah, Karachi, Tehran, Jafari, Kemenag',
            ],
            'koreksi_subuh' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
                'default'    => 0,
                'comment'    => 'Koreksi waktu Subuh (menit)',
            ],
            'koreksi_dzuhur' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
                'default'    => 0,
            ],
            'koreksi_ashar' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
                'default'    => 0,
            ],
            'koreksi_maghrib' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
                'default'    => 0,
            ],
            'koreksi_isya' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
                'default'    => 0,
            ],
            // Durasi iqomah per waktu (menit)
            'durasi_iqomah_subuh' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 10,
            ],
            'durasi_iqomah_dzuhur' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 10,
            ],
            'durasi_iqomah_ashar' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 10,
            ],
            'durasi_iqomah_maghrib' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 5,
            ],
            'durasi_iqomah_isya' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 10,
            ],
            'sholat_jumat' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 1,
                'comment'    => '1=Aktifkan mode Jumat, 0=Nonaktif',
            ],
            'interval_sync' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 60,
                'comment'    => 'Interval sinkronisasi data dari server (detik)',
            ],
            'aktif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 1,
                'comment'    => '1=Display aktif, 0=Nonaktif',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('id_masjid_mushola');
        $this->forge->createTable('tbl_display_setting', true);

        // ============================================================
        // Tabel Konten Display Masjid
        // ============================================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_display_setting' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'FK ke tbl_display_setting',
            ],
            'tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['info_kegiatan', 'gambar_slide', 'laporan_keuangan', 'jadwal_imsyakiyah', 'pengumuman'],
                'default'    => 'info_kegiatan',
                'comment'    => 'Jenis konten',
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
                'comment'    => 'Judul konten',
            ],
            'konten' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Isi konten (HTML/teks)',
            ],
            'gambar' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path file gambar',
            ],
            'urutan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => 'Urutan tampil konten',
            ],
            'aktif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 1,
            ],
            'tanggal_mulai' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Tanggal mulai tayang',
            ],
            'tanggal_selesai' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Tanggal selesai tayang',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('id_display_setting');
        $this->forge->addKey('tipe');
        $this->forge->createTable('tbl_display_konten', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_display_konten', true);
        $this->forge->dropTable('tbl_display_setting', true);
    }
}
