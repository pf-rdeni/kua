<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Konsolidasi kolom individual tbl_display_setting ke format JSON grouped.
 * 
 * Perubahan:
 * - Tambah kolom JSON: koreksi_waktu, timer_iqomah, mode_sholat_event, mode_tarawih_json, mode_hari_raya
 * - Migrasi data dari kolom lama ke JSON
 * - Hapus 32 kolom individual yang sudah dipindah
 * - Pisahkan mode Iqomah dan Sholat dari yang sebelumnya gabung
 */
class ConsolidateDisplaySettingToJson extends Migration
{
    public function up()
    {
        // ============================================================
        // STEP 1: Tambah kolom JSON baru
        // ============================================================
        $fields = [
            'koreksi_waktu' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'JSON koreksi waktu sholat: {subuh, dzuhur, ashar, maghrib, isya}',
            ],
            'timer_iqomah' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'JSON durasi iqomah per waktu: {subuh, dzuhur, ashar, maghrib, isya}',
            ],
            'mode_sholat_event' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'JSON mode event sholat: {menjelang_adzan, adzan, qobliyah, iqomah, sholat, badiyah}',
            ],
            'mode_tarawih_json' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'JSON setting tarawih: {aktif, durasi, gambar}',
            ],
            'mode_hari_raya' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'JSON setting hari raya: {idul_fitri:{aktif,tanggal,durasi,gambar}, idul_adha:{...}}',
            ],
        ];

        $this->forge->addColumn('tbl_display_setting', $fields);

        // ============================================================
        // STEP 2: Migrasi data dari kolom lama ke kolom JSON baru
        // ============================================================
        $rows = $this->db->table('tbl_display_setting')->get()->getResultArray();

        foreach ($rows as $row) {
            $id = $row['id'];

            // koreksi_waktu
            $koreksiWaktu = json_encode([
                'subuh'   => (int)($row['koreksi_subuh'] ?? 0),
                'dzuhur'  => (int)($row['koreksi_dzuhur'] ?? 0),
                'ashar'   => (int)($row['koreksi_ashar'] ?? 0),
                'maghrib' => (int)($row['koreksi_maghrib'] ?? 0),
                'isya'    => (int)($row['koreksi_isya'] ?? 0),
            ]);

            // timer_iqomah
            $timerIqomah = json_encode([
                'subuh'   => (int)($row['durasi_iqomah_subuh'] ?? 10),
                'dzuhur'  => (int)($row['durasi_iqomah_dzuhur'] ?? 10),
                'ashar'   => (int)($row['durasi_iqomah_ashar'] ?? 10),
                'maghrib' => (int)($row['durasi_iqomah_maghrib'] ?? 5),
                'isya'    => (int)($row['durasi_iqomah_isya'] ?? 10),
            ]);

            // mode_sholat_event (pisah iqomah & sholat)
            $modeSholatEvent = json_encode([
                'menjelang_adzan' => [
                    'aktif'  => (int)($row['mode_menjelang_adzan'] ?? 1),
                    'durasi' => (int)($row['durasi_menjelang_adzan'] ?? 10),
                    'gambar' => $row['gambar_menjelang_adzan'] ?? null,
                ],
                'adzan' => [
                    'aktif'  => (int)($row['mode_adzan'] ?? 1),
                    'durasi' => (int)($row['durasi_adzan'] ?? 7),
                    'gambar' => $row['gambar_adzan'] ?? null,
                ],
                'qobliyah' => [
                    'aktif'  => (int)($row['mode_qobliyah'] ?? 0),
                    'durasi' => (int)($row['durasi_qobliyah'] ?? 5),
                    'gambar' => $row['gambar_qobliyah'] ?? null,
                ],
                'iqomah' => [
                    'aktif'  => (int)($row['mode_sholat'] ?? 1), // inherit dari mode_sholat lama
                    'gambar' => null, // baru, belum ada gambar sebelumnya
                ],
                'sholat' => [
                    'aktif'  => (int)($row['mode_sholat'] ?? 1),
                    'durasi' => (int)($row['durasi_sholat'] ?? 15),
                    'gambar' => $row['gambar_sholat'] ?? null,
                ],
                'badiyah' => [
                    'aktif'  => (int)($row['mode_badiyah'] ?? 0),
                    'durasi' => (int)($row['durasi_badiyah'] ?? 5),
                    'gambar' => $row['gambar_badiyah'] ?? null,
                ],
            ]);

            // mode_tarawih_json
            $modeTarawih = json_encode([
                'aktif'  => (int)($row['mode_tarawih'] ?? 0),
                'durasi' => (int)($row['durasi_tarawih'] ?? 60),
                'gambar' => $row['gambar_tarawih'] ?? null,
            ]);

            // mode_hari_raya
            $modeHariRaya = json_encode([
                'idul_adha' => [
                    'aktif'   => (int)($row['mode_idul_adha'] ?? 0),
                    'tanggal' => $row['tanggal_idul_adha'] ?? null,
                    'durasi'  => (int)($row['durasi_idul_adha'] ?? 60),
                    'gambar'  => $row['gambar_idul_adha'] ?? null,
                ],
                'idul_fitri' => [
                    'aktif'   => (int)($row['mode_idul_fitri'] ?? 0),
                    'tanggal' => $row['tanggal_idul_fitri'] ?? null,
                    'durasi'  => (int)($row['durasi_idul_fitri'] ?? 60),
                    'gambar'  => $row['gambar_idul_fitri'] ?? null,
                ],
            ]);

            $this->db->table('tbl_display_setting')
                ->where('id', $id)
                ->update([
                    'koreksi_waktu'     => $koreksiWaktu,
                    'timer_iqomah'      => $timerIqomah,
                    'mode_sholat_event' => $modeSholatEvent,
                    'mode_tarawih_json' => $modeTarawih,
                    'mode_hari_raya'    => $modeHariRaya,
                ]);
        }

        // ============================================================
        // STEP 3: Hapus kolom individual lama
        // ============================================================
        $dropColumns = [
            // Koreksi waktu (5)
            'koreksi_subuh', 'koreksi_dzuhur', 'koreksi_ashar', 'koreksi_maghrib', 'koreksi_isya',
            // Durasi iqomah (5)
            'durasi_iqomah_subuh', 'durasi_iqomah_dzuhur', 'durasi_iqomah_ashar',
            'durasi_iqomah_maghrib', 'durasi_iqomah_isya',
            // Mode event sholat (15)
            'mode_menjelang_adzan', 'durasi_menjelang_adzan', 'gambar_menjelang_adzan',
            'mode_adzan', 'durasi_adzan', 'gambar_adzan',
            'mode_qobliyah', 'durasi_qobliyah', 'gambar_qobliyah',
            'mode_sholat', 'durasi_sholat', 'gambar_sholat',
            'mode_badiyah', 'durasi_badiyah', 'gambar_badiyah',
            // Tarawih (3)
            'mode_tarawih', 'durasi_tarawih', 'gambar_tarawih',
            // Idul Adha (4)
            'mode_idul_adha', 'tanggal_idul_adha', 'durasi_idul_adha', 'gambar_idul_adha',
            // Idul Fitri (4)
            'mode_idul_fitri', 'tanggal_idul_fitri', 'durasi_idul_fitri', 'gambar_idul_fitri',
        ];

        foreach ($dropColumns as $col) {
            if ($this->db->fieldExists($col, 'tbl_display_setting')) {
                $this->forge->dropColumn('tbl_display_setting', $col);
            }
        }
    }

    public function down()
    {
        // Restore kolom individual lama
        $fields = [
            'koreksi_subuh'   => ['type' => 'TINYINT', 'constraint' => 4, 'default' => 0],
            'koreksi_dzuhur'  => ['type' => 'TINYINT', 'constraint' => 4, 'default' => 0],
            'koreksi_ashar'   => ['type' => 'TINYINT', 'constraint' => 4, 'default' => 0],
            'koreksi_maghrib' => ['type' => 'TINYINT', 'constraint' => 4, 'default' => 0],
            'koreksi_isya'    => ['type' => 'TINYINT', 'constraint' => 4, 'default' => 0],

            'durasi_iqomah_subuh'   => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 10],
            'durasi_iqomah_dzuhur'  => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 10],
            'durasi_iqomah_ashar'   => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 10],
            'durasi_iqomah_maghrib' => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 5],
            'durasi_iqomah_isya'    => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 10],

            'mode_menjelang_adzan'   => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true, 'default' => 1],
            'durasi_menjelang_adzan' => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 10],
            'gambar_menjelang_adzan' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'mode_adzan'             => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true, 'default' => 1],
            'durasi_adzan'           => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 7],
            'gambar_adzan'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'mode_qobliyah'          => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true, 'default' => 0],
            'durasi_qobliyah'        => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 5],
            'gambar_qobliyah'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'mode_sholat'            => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true, 'default' => 1],
            'durasi_sholat'          => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 15],
            'gambar_sholat'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'mode_badiyah'           => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true, 'default' => 0],
            'durasi_badiyah'         => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 5],
            'gambar_badiyah'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'mode_tarawih'           => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true, 'default' => 0],
            'durasi_tarawih'         => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 60],
            'gambar_tarawih'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'mode_idul_adha'         => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true, 'default' => 0],
            'tanggal_idul_adha'      => ['type' => 'DATE', 'null' => true],
            'durasi_idul_adha'       => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 60],
            'gambar_idul_adha'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'mode_idul_fitri'        => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true, 'default' => 0],
            'tanggal_idul_fitri'     => ['type' => 'DATE', 'null' => true],
            'durasi_idul_fitri'      => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 60],
            'gambar_idul_fitri'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ];

        $this->forge->addColumn('tbl_display_setting', $fields);

        // Drop kolom JSON
        $jsonColumns = ['koreksi_waktu', 'timer_iqomah', 'mode_sholat_event', 'mode_tarawih_json', 'mode_hari_raya'];
        foreach ($jsonColumns as $col) {
            if ($this->db->fieldExists($col, 'tbl_display_setting')) {
                $this->forge->dropColumn('tbl_display_setting', $col);
            }
        }
    }
}
