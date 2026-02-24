<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAddressFieldsToEntities extends Migration
{
    public function up()
    {
        $fields = [
            'provinsi'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'kabupaten_kota' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'kecamatan'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'kelurahan_desa' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'rt'             => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
            'rw'             => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
        ];

        // Add to tbl_masjid_mushola
        $this->forge->addColumn('tbl_masjid_mushola', $fields);

        // Add to tbl_tpq_mdta
        $this->forge->addColumn('tbl_tpq_mdta', $fields);

        // Add to tbl_majelis_taklim
        $this->forge->addColumn('tbl_majelis_taklim', $fields);
    }

    public function down()
    {
        $fieldsToDrop = ['provinsi', 'kabupaten_kota', 'kecamatan', 'kelurahan_desa', 'rt', 'rw'];
        
        $this->forge->dropColumn('tbl_masjid_mushola', $fieldsToDrop);
        $this->forge->dropColumn('tbl_tpq_mdta', $fieldsToDrop);
        $this->forge->dropColumn('tbl_majelis_taklim', $fieldsToDrop);
    }
}
