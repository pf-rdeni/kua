<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Refaktor: Rename tbl_mubaligh → tbl_personil
 * Tambah kolom baru untuk mendukung semua entitas tipe orang.
 * Migrate data dari tbl_imam_masjid, tbl_fardu_kifayah, tbl_penggali_kubur.
 * Drop tabel lama.
 */
class RefactorUnifiedPersonil extends Migration
{
    public function up()
    {
        // ======================================================
        // STEP 1: Rename tbl_mubaligh → tbl_personil
        // ======================================================
        $this->forge->renameTable('tbl_mubaligh', 'tbl_personil');

        // ======================================================
        // STEP 2: Rename primary key column
        // ======================================================
        $this->forge->modifyColumn('tbl_personil', [
            'id_mubaligh' => [
                'name'           => 'id',
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
        ]);

        // ======================================================
        // STEP 3: Tambah kolom baru
        // ======================================================
        $fields = [
            'entitas_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'mubaligh',
                'after'      => 'id',
                'comment'    => 'Tipe entitas: mubaligh, imam_masjid, fardu_kifayah, penggali_kubur',
            ],
            'id_masjid_mushola' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'entitas_type',
                'comment'    => 'FK ke tbl_masjid_mushola (untuk imam, fardu, penggali)',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'status_aktif',
                'comment'    => 'Status jabatan: Aktif, Non-aktif, dll',
            ],
            'sk_pengangkatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'status',
                'comment'    => 'Path file SK pengangkatan',
            ],
            'no_rek_bpr' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'sk_pengangkatan',
                'comment'    => 'Nomor rekening BPR',
            ],
            'jenis_penerima_insentif' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'no_rek_bpr',
                'comment'    => 'Kategori penerima insentif',
            ],
        ];
        $this->forge->addColumn('tbl_personil', $fields);

        // Tambah index entitas_type
        $this->db->query('CREATE INDEX idx_personil_entitas_type ON tbl_personil (entitas_type)');

        // ======================================================
        // STEP 4: Set entitas_type untuk data mubaligh yang sudah ada
        // ======================================================
        $this->db->query("UPDATE tbl_personil SET entitas_type = 'mubaligh' WHERE entitas_type = 'mubaligh' OR entitas_type IS NULL");

        // ======================================================
        // STEP 5: Migrate data dari tabel lama
        // ======================================================

        // Cek dan migrate tbl_imam_masjid
        if ($this->db->tableExists('tbl_imam_masjid')) {
            $imamData = $this->db->query("SELECT * FROM tbl_imam_masjid")->getResultArray();
            foreach ($imamData as $row) {
                $this->db->table('tbl_personil')->insert([
                    'entitas_type'      => 'imam_masjid',
                    'id_masjid_mushola' => $row['id_masjid_mushola'] ?? null,
                    'nama_lengkap'      => $row['nama'],
                    'no_hp'             => $row['no_hp'] ?? null,
                    'alamat'            => $row['alamat'] ?? null,
                    'status'            => $row['status'] ?? null,
                    'sk_pengangkatan'   => $row['sk_pengangkatan'] ?? null,
                    'foto'              => $row['foto'] ?? null,
                    'status_aktif'      => 1,
                    'jenis_kelamin'     => 'L',
                    'created_at'        => $row['created_at'] ?? date('Y-m-d H:i:s'),
                    'updated_at'        => $row['updated_at'] ?? date('Y-m-d H:i:s'),
                ]);

                // Update tbl_berkas references
                $newId = $this->db->insertID();
                $oldId = $row['id_imam_masjid'];
                $this->db->query(
                    "UPDATE tbl_berkas SET entitas_id = ? WHERE entitas_type = 'imam_masjid' AND entitas_id = ?",
                    [$newId, $oldId]
                );
            }
        }

        // Cek dan migrate tbl_fardu_kifayah
        if ($this->db->tableExists('tbl_fardu_kifayah')) {
            $fkData = $this->db->query("SELECT * FROM tbl_fardu_kifayah")->getResultArray();
            foreach ($fkData as $row) {
                $this->db->table('tbl_personil')->insert([
                    'entitas_type'      => 'fardu_kifayah',
                    'id_masjid_mushola' => $row['id_masjid_mushola'] ?? null,
                    'nama_lengkap'      => $row['nama'],
                    'no_hp'             => $row['no_hp'] ?? null,
                    'alamat'            => $row['alamat'] ?? null,
                    'status'            => $row['status'] ?? null,
                    'sk_pengangkatan'   => $row['sk_pengangkatan'] ?? null,
                    'foto'              => $row['foto'] ?? null,
                    'status_aktif'      => 1,
                    'jenis_kelamin'     => 'L',
                    'created_at'        => $row['created_at'] ?? date('Y-m-d H:i:s'),
                    'updated_at'        => $row['updated_at'] ?? date('Y-m-d H:i:s'),
                ]);

                $newId = $this->db->insertID();
                $oldId = $row['id_fardu_kifayah'];
                $this->db->query(
                    "UPDATE tbl_berkas SET entitas_id = ? WHERE entitas_type = 'fardu_kifayah' AND entitas_id = ?",
                    [$newId, $oldId]
                );
            }
        }

        // Cek dan migrate tbl_penggali_kubur
        if ($this->db->tableExists('tbl_penggali_kubur')) {
            $pkData = $this->db->query("SELECT * FROM tbl_penggali_kubur")->getResultArray();
            foreach ($pkData as $row) {
                $this->db->table('tbl_personil')->insert([
                    'entitas_type'      => 'penggali_kubur',
                    'id_masjid_mushola' => $row['id_masjid_mushola'] ?? null,
                    'nama_lengkap'      => $row['nama'],
                    'no_hp'             => $row['no_hp'] ?? null,
                    'alamat'            => $row['alamat'] ?? null,
                    'status'            => $row['status'] ?? null,
                    'sk_pengangkatan'   => $row['sk_pengangkatan'] ?? null,
                    'foto'              => $row['foto'] ?? null,
                    'status_aktif'      => 1,
                    'jenis_kelamin'     => 'L',
                    'created_at'        => $row['created_at'] ?? date('Y-m-d H:i:s'),
                    'updated_at'        => $row['updated_at'] ?? date('Y-m-d H:i:s'),
                ]);

                $newId = $this->db->insertID();
                $oldId = $row['id_penggali_kubur'];
                $this->db->query(
                    "UPDATE tbl_berkas SET entitas_id = ? WHERE entitas_type = 'penggali_kubur' AND entitas_id = ?",
                    [$newId, $oldId]
                );
            }
        }

        // ======================================================
        // STEP 6: Update tbl_berkas — entitas_type 'mubaligh' tetap valid
        // (tidak perlu update karena menggunakan tabel yang sama, hanya rename)
        // ======================================================

        // ======================================================
        // STEP 7: Update tbl_setting_berkas — entitas_type references
        // (setting berkas tetap kompatibel, kode entitas tidak berubah)
        // ======================================================

        // ======================================================
        // STEP 8: Drop tabel lama
        // ======================================================
        $this->forge->dropTable('tbl_imam_masjid', true);
        $this->forge->dropTable('tbl_fardu_kifayah', true);
        $this->forge->dropTable('tbl_penggali_kubur', true);
    }

    public function down()
    {
        // Recreate old tables (simplified — data tidak bisa di-restore sempurna)
        // tbl_imam_masjid
        $this->forge->addField([
            'id_imam_masjid'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_masjid_mushola' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama'              => ['type' => 'VARCHAR', 'constraint' => 100],
            'no_hp'             => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'alamat'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'            => ['type' => 'VARCHAR', 'constraint' => 50],
            'sk_pengangkatan'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'foto'              => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_imam_masjid', true);
        $this->forge->createTable('tbl_imam_masjid', true);

        // tbl_fardu_kifayah
        $this->forge->addField([
            'id_fardu_kifayah'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_masjid_mushola' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama'              => ['type' => 'VARCHAR', 'constraint' => 100],
            'no_hp'             => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'alamat'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'            => ['type' => 'VARCHAR', 'constraint' => 50],
            'sk_pengangkatan'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'foto'              => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_fardu_kifayah', true);
        $this->forge->createTable('tbl_fardu_kifayah', true);

        // tbl_penggali_kubur
        $this->forge->addField([
            'id_penggali_kubur' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_masjid_mushola' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama'              => ['type' => 'VARCHAR', 'constraint' => 100],
            'no_hp'             => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'alamat'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'            => ['type' => 'VARCHAR', 'constraint' => 50],
            'sk_pengangkatan'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'foto'              => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_penggali_kubur', true);
        $this->forge->createTable('tbl_penggali_kubur', true);

        // Remove new columns from tbl_personil
        $this->forge->dropColumn('tbl_personil', ['entitas_type', 'id_masjid_mushola', 'status', 'sk_pengangkatan', 'no_rek_bpr', 'jenis_penerima_insentif']);

        // Rename PK back
        $this->forge->modifyColumn('tbl_personil', [
            'id' => [
                'name'           => 'id_mubaligh',
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
        ]);

        // Rename table back
        $this->forge->renameTable('tbl_personil', 'tbl_mubaligh');
    }
}
