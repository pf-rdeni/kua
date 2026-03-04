<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Rename tbl_agenda_masjid → tbl_agenda_kegiatan
 * Tambah kolom entitas_type agar bisa dipakai multi-entitas (masjid, majelis taklim, dll)
 * Rename kolom id_masjid_mushola → entitas_id
 */
class RenameAgendaMasjidToAgendaKegiatan extends Migration
{
    public function up()
    {
        // 1. Tambah kolom entitas_type sebelum rename tabel
        $this->forge->addColumn('tbl_agenda_masjid', [
            'entitas_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'masjid_mushola',
                'after'      => 'id',
            ],
        ]);

        // 2. Update semua data existing → set entitas_type = 'masjid_mushola'
        $this->db->query("UPDATE tbl_agenda_masjid SET entitas_type = 'masjid_mushola' WHERE entitas_type IS NULL OR entitas_type = ''");

        // 3. Rename kolom id_masjid_mushola → entitas_id
        $this->forge->modifyColumn('tbl_agenda_masjid', [
            'id_masjid_mushola' => [
                'name'       => 'entitas_id',
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);

        // 4. Rename tabel
        $this->forge->renameTable('tbl_agenda_masjid', 'tbl_agenda_kegiatan');
    }

    public function down()
    {
        // Reverse: rename back
        $this->forge->renameTable('tbl_agenda_kegiatan', 'tbl_agenda_masjid');

        // Rename kolom back
        $this->forge->modifyColumn('tbl_agenda_masjid', [
            'entitas_id' => [
                'name'       => 'id_masjid_mushola',
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);

        // Drop entitas_type
        $this->forge->dropColumn('tbl_agenda_masjid', 'entitas_type');
    }
}
