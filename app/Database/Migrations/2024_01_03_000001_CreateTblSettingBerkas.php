<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblSettingBerkas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_berkas' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
                'unique'     => true, // Nama berkas unik
            ],
            'entitas_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true, // Comma-separated list misal: mubaligh,imam_masjid
            ],
            'aspect_ratio_width' => [
                'type'       => 'FLOAT',
                'null'       => true,
            ],
            'aspect_ratio_height' => [
                'type'       => 'FLOAT',
                'null'       => true,
            ],
            'status_aktif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1, // 1: aktif, 0: nonaktif
                'null'       => false,
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
        $this->forge->createTable('tbl_setting_berkas');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_setting_berkas');
    }
}
