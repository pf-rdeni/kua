<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblAbsensi extends Migration
{
    public function up()
    {
        // 1. Buat Tabel Absensi Baru
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_jadwal' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'jenis_kegiatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'tanggal_kegiatan' => [
                'type' => 'DATE',
            ],
            'id_personil' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status_kehadiran' => [
                'type'       => 'ENUM',
                'constraint' => ['hadir', 'tidak_hadir', 'diganti', 'menunggu_konfirmasi'],
                'default'    => 'hadir',
            ],
            'id_personil_pengganti' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'waktu_absen' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->createTable('tbl_absensi', true);

        // 2. Drop Kolom Usang dari tbl_jadwal_kegiatan (agar bersih)
        // Check first to prevent errors if they don't exist
        $fields = $this->db->getFieldNames('tbl_jadwal_kegiatan');
        $columnsToDrop = [];
        if (in_array('status_kehadiran', $fields)) $columnsToDrop[] = 'status_kehadiran';
        if (in_array('id_personil_pengganti', $fields)) $columnsToDrop[] = 'id_personil_pengganti';
        if (in_array('keterangan_absensi', $fields)) $columnsToDrop[] = 'keterangan_absensi';
        
        if (!empty($columnsToDrop)) {
            $this->forge->dropColumn('tbl_jadwal_kegiatan', $columnsToDrop);
        }
    }

    public function down()
    {
        // 1. Drop tabel absensi
        $this->forge->dropTable('tbl_absensi', true);

        // 2. Kembalikan kolom-kolom ke tbl_jadwal_kegiatan
        $fields = $this->db->getFieldNames('tbl_jadwal_kegiatan');
        $columnsToAdd = [];
        
        if (!in_array('status_kehadiran', $fields)) {
            $columnsToAdd['status_kehadiran'] = [
                'type'       => 'ENUM',
                'constraint' => ['hadir', 'tidak_hadir', 'diganti'],
                'null'       => true,
            ];
        }
        if (!in_array('id_personil_pengganti', $fields)) {
            $columnsToAdd['id_personil_pengganti'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ];
        }
        if (!in_array('keterangan_absensi', $fields)) {
            $columnsToAdd['keterangan_absensi'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ];
        }

        if (!empty($columnsToAdd)) {
            $this->forge->addColumn('tbl_jadwal_kegiatan', $columnsToAdd);
        }
    }
}
