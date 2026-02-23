<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRekeningBankFields extends Migration
{
    public function up()
    {
        // 1. Rename no_rek_bpr to rekening_bank and change type to TEXT in tbl_personil
        $fieldsPersonil = [
            'no_rek_bpr' => [
                'name' => 'rekening_bank',
                'type' => 'TEXT',
                'null' => true,
            ],
        ];
        $this->forge->modifyColumn('tbl_personil', $fieldsPersonil);

        // 2. Add is_rekening and rekening_digit to tbl_setting_berkas
        $fieldsSettingBerkas = [
            'is_rekening' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
            ],
            'rekening_digit' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
        ];
        $this->forge->addColumn('tbl_setting_berkas', $fieldsSettingBerkas);
    }

    public function down()
    {
        // 1. Revert rekening_bank back to no_rek_bpr (VARCHAR)
        $fieldsPersonil = [
            'rekening_bank' => [
                'name'       => 'no_rek_bpr',
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
        ];
        $this->forge->modifyColumn('tbl_personil', $fieldsPersonil);

        // 2. Drop columns from tbl_setting_berkas
        $this->forge->dropColumn('tbl_setting_berkas', 'is_rekening');
        $this->forge->dropColumn('tbl_setting_berkas', 'rekening_digit');
    }
}
