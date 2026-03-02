<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration menambahkan kolom opsi_waktu_sholat (JSON)
 * Untuk menyimpan pengaturan spesifik waktu sholat yang mengaktifkan mode sunnah
 * (qobliyah dan ba'diyah), misal: hanya qobliyah dzuhur dan isya saja.
 */
class AddOpsiWaktuSholat extends Migration
{
    public function up()
    {
        $fields = [
            'opsi_waktu_sholat' => [
                'type' => 'TEXT', // Menggunakan TEXT agar kompatibel dengan MariaDB/MySQL versi lama, isinya JSON string
                'null' => true,
                'comment' => 'JSON konfigurasi spesifik qobliyah dan badiyah per waktu sholat',
            ],
        ];

        $this->forge->addColumn('tbl_display_setting', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_display_setting', 'opsi_waktu_sholat');
    }
}
