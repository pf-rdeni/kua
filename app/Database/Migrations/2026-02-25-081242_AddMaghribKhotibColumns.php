<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMaghribKhotibColumns extends Migration
{
    public function up()
    {
        $fields = [
            'tahun_masehi' => [
                'type'       => 'INT',
                'constraint' => 4,
                'null'       => true,
                'after'      => 'tahun_hijriah',
            ],
            'bulan' => [
                'type'       => 'INT',
                'constraint' => 2,
                'null'       => true,
                'after'      => 'tahun_masehi',
            ],
            'peran_petugas' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'penceramah',
                'after'      => 'id_personil',
            ],
        ];
        
        $this->forge->addColumn('tbl_jadwal_kegiatan', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_jadwal_kegiatan', ['tahun_masehi', 'bulan', 'peran_petugas']);
    }
}
