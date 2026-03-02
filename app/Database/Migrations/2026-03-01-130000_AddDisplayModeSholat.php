<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration untuk menambahkan kolom mode display event sholat
 * ke tabel tbl_display_setting
 * 
 * 8 mode: menjelang adzan, adzan, qobliyah, sholat/iqomah,
 * badiyah, tarawih, idul adha, idul fitri
 * 
 * Setiap mode memiliki: toggle aktif, durasi (menit), dan gambar overlay
 */
class AddDisplayModeSholat extends Migration
{
    public function up()
    {
        $fields = [
            // === Mode 1: Menjelang Adzan ===
            'mode_menjelang_adzan' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 1,
                'comment'    => 'Aktifkan mode menjelang adzan',
            ],
            'durasi_menjelang_adzan' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 10,
                'comment'    => 'Menit sebelum adzan mulai ditampilkan',
            ],
            'gambar_menjelang_adzan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path gambar overlay menjelang adzan',
            ],

            // === Mode 2: Saat Adzan ===
            'mode_adzan' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 1,
                'comment'    => 'Aktifkan mode saat adzan',
            ],
            'durasi_adzan' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 7,
                'comment'    => 'Durasi tampilan adzan (menit)',
            ],
            'gambar_adzan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path gambar overlay saat adzan',
            ],

            // === Mode 3: Sholat Qobliyah ===
            'mode_qobliyah' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => 'Aktifkan mode sholat qobliyah',
            ],
            'durasi_qobliyah' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 5,
                'comment'    => 'Durasi tampilan qobliyah (menit)',
            ],
            'gambar_qobliyah' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path gambar overlay sholat qobliyah',
            ],

            // === Mode 4: Iqomah & Sholat Berlangsung ===
            'mode_sholat' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 1,
                'comment'    => 'Aktifkan mode iqomah + sholat berlangsung',
            ],
            'durasi_sholat' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 15,
                'comment'    => 'Estimasi durasi sholat berlangsung (menit)',
            ],
            'gambar_sholat' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path gambar overlay sholat berlangsung',
            ],

            // === Mode 5: Sholat Ba\'diyah ===
            'mode_badiyah' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => 'Aktifkan mode sholat ba\'diyah',
            ],
            'durasi_badiyah' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 5,
                'comment'    => 'Durasi tampilan ba\'diyah (menit)',
            ],
            'gambar_badiyah' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path gambar overlay sholat ba\'diyah',
            ],

            // === Mode 6: Tarawih ===
            'mode_tarawih' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => 'Aktifkan mode tarawih (bulan Ramadhan)',
            ],
            'durasi_tarawih' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 60,
                'comment'    => 'Durasi tampilan tarawih (menit)',
            ],
            'gambar_tarawih' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path gambar overlay tarawih',
            ],

            // === Mode 7: Idul Adha ===
            'mode_idul_adha' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => 'Aktifkan mode Idul Adha',
            ],
            'tanggal_idul_adha' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Tanggal Idul Adha',
            ],
            'durasi_idul_adha' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 60,
                'comment'    => 'Durasi tampilan Idul Adha (menit)',
            ],
            'gambar_idul_adha' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path gambar overlay Idul Adha',
            ],

            // === Mode 8: Idul Fitri ===
            'mode_idul_fitri' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => 'Aktifkan mode Idul Fitri',
            ],
            'tanggal_idul_fitri' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Tanggal Idul Fitri',
            ],
            'durasi_idul_fitri' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 60,
                'comment'    => 'Durasi tampilan Idul Fitri (menit)',
            ],
            'gambar_idul_fitri' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path gambar overlay Idul Fitri',
            ],
        ];

        $this->forge->addColumn('tbl_display_setting', $fields);
    }

    public function down()
    {
        $columns = [
            'mode_menjelang_adzan', 'durasi_menjelang_adzan', 'gambar_menjelang_adzan',
            'mode_adzan', 'durasi_adzan', 'gambar_adzan',
            'mode_qobliyah', 'durasi_qobliyah', 'gambar_qobliyah',
            'mode_sholat', 'durasi_sholat', 'gambar_sholat',
            'mode_badiyah', 'durasi_badiyah', 'gambar_badiyah',
            'mode_tarawih', 'durasi_tarawih', 'gambar_tarawih',
            'mode_idul_adha', 'tanggal_idul_adha', 'durasi_idul_adha', 'gambar_idul_adha',
            'mode_idul_fitri', 'tanggal_idul_fitri', 'durasi_idul_fitri', 'gambar_idul_fitri',
        ];

        foreach ($columns as $col) {
            $this->forge->dropColumn('tbl_display_setting', $col);
        }
    }
}
