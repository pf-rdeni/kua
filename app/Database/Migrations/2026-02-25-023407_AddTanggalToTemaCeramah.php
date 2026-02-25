<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTanggalToTemaCeramah extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_tema_ceramah', [
            'tanggal' => [
                'type' => 'DATE',
                'null' => true,
                'after'=> 'hari_ke'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_tema_ceramah', 'tanggal');
    }
}
