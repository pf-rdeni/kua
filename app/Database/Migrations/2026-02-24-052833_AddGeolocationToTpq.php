<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGeolocationToTpq extends Migration
{
    public function up()
    {
        $fields = [
            'latitude' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'longitude' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
        ];

        $this->forge->addColumn('tbl_tpq_mdta', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_tpq_mdta', 'latitude');
        $this->forge->dropColumn('tbl_tpq_mdta', 'longitude');
    }
}
