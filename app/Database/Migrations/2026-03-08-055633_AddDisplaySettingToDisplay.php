<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDisplaySettingToDisplay extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_display_setting', [
            'display_setting' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_display_setting', 'display_setting');
    }
}
