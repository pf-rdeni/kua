<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblBerkas extends Migration
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
            'entitas_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => 'Tipe modul: mubaligh, imam_masjid, fardu_kifayah, penggali_kubur, majelis_taklim, tpq_mdta',
            ],
            'entitas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'ID dari entitas terkait',
            ],
            'nama_berkas' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => 'Tipe berkas: KTP, KK, dll',
            ],
            'nama_file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'comment'    => 'Nama file yang disimpan di server',
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1=aktif, 0=nonaktif',
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
        $this->forge->addKey(['entitas_type', 'entitas_id'], false, false, 'idx_entitas');
        $this->forge->createTable('tbl_berkas', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_berkas', true);
    }
}
