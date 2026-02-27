<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNiaToPersonil extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_personil', [
            'nia' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'nik',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_personil', 'nia');
    }
}
