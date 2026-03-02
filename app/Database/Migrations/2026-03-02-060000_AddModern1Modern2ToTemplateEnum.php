<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddModern1Modern2ToTemplateEnum extends Migration
{
    public function up()
    {
        // Tambah modern1 dan modern2 ke ENUM template_aktif
        $this->db->query("ALTER TABLE tbl_display_setting MODIFY COLUMN template_aktif ENUM('klasik','modern','modern1','modern2','keuangan') NOT NULL DEFAULT 'klasik'");

        // Fix: set value yang kosong ke 'klasik'
        $this->db->query("UPDATE tbl_display_setting SET template_aktif = 'klasik' WHERE template_aktif = '' OR template_aktif IS NULL");
    }

    public function down()
    {
        // Kembalikan ke enum lama
        $this->db->query("UPDATE tbl_display_setting SET template_aktif = 'modern' WHERE template_aktif IN ('modern1','modern2')");
        $this->db->query("ALTER TABLE tbl_display_setting MODIFY COLUMN template_aktif ENUM('klasik','modern','keuangan') NOT NULL DEFAULT 'klasik'");
    }
}
