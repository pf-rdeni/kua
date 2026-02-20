<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblLembagaTpqMdta extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_lembaga' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_lembaga' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'jenis' => [
                'type'       => 'ENUM',
                'constraint' => ['TPQ', 'MDTA'],
                'default'    => 'TPQ',
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'kelurahan_desa' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'nama_pimpinan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'no_hp_pimpinan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'jumlah_santri' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'jumlah_pengajar' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'tahun_berdiri' => [
                'type' => 'YEAR',
                'null' => true,
            ],
            'status_aktif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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

        $this->forge->addKey('id_lembaga', true);
        $this->forge->createTable('tbl_lembaga_tpq_mdta', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_lembaga_tpq_mdta', true);
    }
}
