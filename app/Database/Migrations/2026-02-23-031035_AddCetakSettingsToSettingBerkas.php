<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCetakSettingsToSettingBerkas extends Migration
{
    public function up()
    {
        $fields = [
            'cetak_tipe' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'gabung', // 'gabung' or 'full_page'
                'null'       => false,
                'after'      => 'rekening_digit',
            ],
            'cetak_lebar' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 100, // percentage 10-100
                'null'       => false,
                'after'      => 'cetak_tipe',
            ],
        ];
        
        $this->forge->addColumn('tbl_setting_berkas', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_setting_berkas', 'cetak_tipe');
        $this->forge->dropColumn('tbl_setting_berkas', 'cetak_lebar');
    }
}
