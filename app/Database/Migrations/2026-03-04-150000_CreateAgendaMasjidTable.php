<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Tabel agenda kegiatan Ramadhan mandiri per masjid
 * Terpisah dari tbl_jadwal_kegiatan (jadwal mubaligh oleh admin KUA)
 */
class CreateAgendaMasjidTable extends Migration
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
            'id_masjid_mushola' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'waktu_mulai' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'waktu_selesai' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'judul_kegiatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => false,
            ],
            'jenis' => [
                'type'       => 'ENUM',
                'constraint' => ['ceramah', 'ta_lim', 'sosial', 'buka_bersama', 'tadarus', 'sahur', 'lainnya'],
                'default'    => 'ceramah',
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'nama_penceramah' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'id_personil' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'Mubaligh dari database jika terpilih',
            ],
            'lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
            ],
            'is_published' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1=Aktif, 0=Draft',
            ],
            'created_by' => [
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
        $this->forge->addKey('id_masjid_mushola');
        $this->forge->addKey('tanggal');
        $this->forge->createTable('tbl_agenda_masjid');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_agenda_masjid');
    }
}
