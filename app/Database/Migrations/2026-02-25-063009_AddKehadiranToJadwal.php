<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKehadiranToJadwal extends Migration
{
    public function up()
    {
        // 1. Tambah kolom token_jadwal ke tbl_personil
        if (!$this->db->fieldExists('token_jadwal', 'tbl_personil')) {
            $fieldsPersonil = [
                'token_jadwal' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 16,
                    'null'       => true,
                    'after'      => 'status_aktif', // Atur posisi sesuai keinginan
                ],
            ];
            $this->forge->addColumn('tbl_personil', $fieldsPersonil);
        }

        // 2. Tambah kolom absensi ke tbl_jadwal_kegiatan
        if (!$this->db->fieldExists('keterangan_absensi', 'tbl_jadwal_kegiatan')) {
            $fieldsJadwal = [
                'keterangan_absensi' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'id_personil_pengganti',
                ],
            ];
            $this->forge->addColumn('tbl_jadwal_kegiatan', $fieldsJadwal);
        }
    }

    public function down()
    {
        // Rollback: Hapus kolom-kolom yang telah ditambahkan
        if ($this->db->fieldExists('token_jadwal', 'tbl_personil')) {
            $this->forge->dropColumn('tbl_personil', 'token_jadwal');
        }

        if ($this->db->fieldExists('keterangan_absensi', 'tbl_jadwal_kegiatan')) {
            $this->forge->dropColumn('tbl_jadwal_kegiatan', 'keterangan_absensi');
        }
    }
}
