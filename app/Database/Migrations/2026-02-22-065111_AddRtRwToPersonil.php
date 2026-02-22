<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRtRwToPersonil extends Migration
{
    public function up()
    {
        $fields = [
            'provinsi' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'alamat',
            ],
            'kabupaten_kota' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'provinsi',
            ],
            'kecamatan' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'kabupaten_kota',
            ],
            'rt' => [
                'type'       => 'VARCHAR',
                'constraint' => '5',
                'null'       => true,
                'after'      => 'kelurahan_desa',
            ],
            'rw' => [
                'type'       => 'VARCHAR',
                'constraint' => '5',
                'null'       => true,
                'after'      => 'rt',
            ],
        ];

        $this->forge->addColumn('tbl_personil', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_personil', ['provinsi', 'kabupaten_kota', 'kecamatan', 'rt', 'rw']);
    }
}
