<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJadwalKegiatanTable extends Migration
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
            'id_masjid_mushola' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'id_personil' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'hari_ke' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'id_tema' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'status_kehadiran' => [
                'type'       => 'ENUM',
                'constraint' => ['hadir', 'digantikan', 'tidak_hadir'],
                'null'       => true,
            ],
            'id_personil_pengganti' => [
                'type'       => 'INT',
                'constraint' => 11,
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
        $this->forge->createTable('tbl_jadwal_kegiatan');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_jadwal_kegiatan');
    }
}
