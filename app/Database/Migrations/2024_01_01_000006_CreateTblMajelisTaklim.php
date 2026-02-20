<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblMajelisTaklim extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_majelis_taklim' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_majelis' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'nama_pimpinan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
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
            'no_hp_pimpinan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'jumlah_anggota' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'jadwal_kegiatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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

        $this->forge->addKey('id_majelis_taklim', true);
        $this->forge->createTable('tbl_majelis_taklim', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_majelis_taklim', true);
    }
}
