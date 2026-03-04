<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMajelisTaklimToEntitasType extends Migration
{
    public function up()
    {
        $data = [
            'kode'            => 'majelis_taklim',
            'nama_label'      => 'Keuangan Majelis Taklim',
            'icon'            => 'fas fa-chalkboard-teacher',
            'deskripsi'       => 'Pengelolaan Dana dan Kas Majelis Taklim',
            'operator_group'  => 'OperatorMajelisTaklim',
            'has_masjid_link' => 0,
            'has_sk'          => 0,
            'urutan'          => 99,
            'is_active'       => 1,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        // Cek apakah sudah ada untuk menghindari duplikat
        $exists = $this->db->table('tbl_entitas_type')->where('kode', 'majelis_taklim')->countAllResults();
        
        if ($exists == 0) {
            $this->db->table('tbl_entitas_type')->insert($data);
        }
    }

    public function down()
    {
        $this->db->table('tbl_entitas_type')->where('kode', 'majelis_taklim')->delete();
    }
}
