<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Tambah kolom entitas ke tabel users
 * Kolom ini digunakan untuk menghubungkan user ke entitas tertentu
 * (misal: masjid_mushola, mubaligh, majelis_taklim, dsb)
 */
class AddEntitasToUsers extends Migration
{
    public function up()
    {
        // Tambah kolom entitas_type dan entitas_id ke tabel users
        // Kolom ditambahkan di akhir tabel (tanpa AFTER untuk hindari error kolom tidak ada)
        $this->forge->addColumn('users', [
            // Tipe entitas (misal: masjid_mushola, mubaligh, dsb)
            'entitas_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
                'comment'    => 'Tipe entitas yang dimiliki user ini (misal: masjid_mushola)',
            ],
            // ID entitas yang terhubung
            'entitas_id' => [
                'type'    => 'INT',
                'null'    => true,
                'default' => null,
                'comment' => 'ID entitas yang terhubung dengan user ini',
            ],
        ]);

        // Buat index untuk mempercepat pencarian user berdasarkan entitas
        $this->db->query('ALTER TABLE `users` ADD INDEX `idx_users_entitas` (`entitas_type`, `entitas_id`)');
    }

    public function down()
    {
        // Hapus index terlebih dahulu sebelum menghapus kolom
        $this->db->query('ALTER TABLE `users` DROP INDEX IF EXISTS `idx_users_entitas`');

        // Hapus kolom entitas
        $this->forge->dropColumn('users', 'entitas_type');
        $this->forge->dropColumn('users', 'entitas_id');
    }
}
