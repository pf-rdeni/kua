<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEntitasToKeuanganKategori extends Migration
{
    public function up()
    {
        // 1. Tambah kolom entitas_type & entitas_id ke tbl_keuangan_kategori
        $fields = [
            'entitas_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Null = milik global Admin',
                'after'      => 'deskripsi'
            ],
            'entitas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'entitas_type'
            ]
        ];
        // Tambahkan ke tabel eksisting
        $this->forge->addColumn('tbl_keuangan_kategori', $fields);

        // 2. Buat tabel tbl_keuangan_kategori_hidden
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_kategori' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'entitas_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'entitas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['id_kategori', 'entitas_type', 'entitas_id']);
        $this->forge->createTable('tbl_keuangan_kategori_hidden', true);
    }

    public function down()
    {
        // Rollback tabel & kolom
        $this->forge->dropTable('tbl_keuangan_kategori_hidden', true);
        $this->forge->dropColumn('tbl_keuangan_kategori', 'entitas_type');
        $this->forge->dropColumn('tbl_keuangan_kategori', 'entitas_id');
    }
}
