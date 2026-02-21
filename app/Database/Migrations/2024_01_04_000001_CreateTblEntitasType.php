<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblEntitasType extends Migration
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
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => 'Identifier unik: mubaligh, imam_masjid, dll',
            ],
            'nama_label' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'Label tampilan: Mubaligh, Imam Masjid, dll',
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'FontAwesome icon class',
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'operator_group' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Nama grup Myth/Auth: OperatorMubaligh, dll',
            ],
            'has_masjid_link' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => 'Apakah entitas terkait masjid/mushola',
            ],
            'has_sk' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => 'Apakah entitas punya SK pengangkatan',
            ],
            'urutan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Urutan tampilan di sidebar',
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
        $this->forge->addUniqueKey('kode');
        $this->forge->createTable('tbl_entitas_type', true);

        // Seed data awal
        $this->db->table('tbl_entitas_type')->insertBatch([
            [
                'kode'            => 'mubaligh',
                'nama_label'      => 'Mubaligh',
                'icon'            => 'fas fa-user-tie',
                'deskripsi'       => 'Data Mubaligh',
                'operator_group'  => 'OperatorMubaligh',
                'has_masjid_link' => 0,
                'has_sk'          => 0,
                'urutan'          => 1,
                'is_active'       => 1,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'kode'            => 'imam_masjid',
                'nama_label'      => 'Imam Masjid',
                'icon'            => 'fas fa-user-check',
                'deskripsi'       => 'Data Imam Masjid',
                'operator_group'  => 'OperatorMasjidMushola',
                'has_masjid_link' => 1,
                'has_sk'          => 1,
                'urutan'          => 2,
                'is_active'       => 1,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'kode'            => 'fardu_kifayah',
                'nama_label'      => 'Fardu Kifayah',
                'icon'            => 'fas fa-hands-helping',
                'deskripsi'       => 'Data Pengurus Fardu Kifayah',
                'operator_group'  => 'OperatorFarduKifayah',
                'has_masjid_link' => 1,
                'has_sk'          => 1,
                'urutan'          => 3,
                'is_active'       => 1,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'kode'            => 'penggali_kubur',
                'nama_label'      => 'Penggali Kubur',
                'icon'            => 'fas fa-hard-hat',
                'deskripsi'       => 'Data Petugas Penggali Kubur',
                'operator_group'  => 'OperatorPenggaliKubur',
                'has_masjid_link' => 1,
                'has_sk'          => 1,
                'urutan'          => 4,
                'is_active'       => 1,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_entitas_type', true);
    }
}
