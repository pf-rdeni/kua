<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblMasjidMushola extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_masjid_mushola' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'jenis' => [
                'type'       => 'ENUM',
                'constraint' => ['Masjid', 'Mushola'],
                'default'    => 'Masjid',
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tahun_berdiri' => [
                'type'       => 'YEAR',
                'null'       => true,
            ],
            'luas_bangunan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'status_tanah' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'nama_ketua_dkm' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'no_hp_ketua' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'jumlah_jamaah' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'latitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,8',
                'null'       => true,
            ],
            'longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
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

        $this->forge->addKey('id_masjid_mushola', true);
        $this->forge->createTable('tbl_masjid_mushola', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_masjid_mushola', true);
    }
}
