<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTemaCeramahTable extends Migration
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
            'jenis_kegiatan' => [
                'type'       => 'ENUM',
                'constraint' => ['ramadhan', 'jumat', 'maghrib_mengaji', 'majelis_taklim'],
                'default'    => 'ramadhan',
            ],
            'tahun_hijriah' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'hari_ke' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'tema' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
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
        $this->forge->createTable('tbl_tema_ceramah');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_tema_ceramah');
    }
}
