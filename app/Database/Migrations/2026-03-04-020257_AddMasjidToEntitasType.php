<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMasjidToEntitasType extends Migration
{
    public function up()
    {
        $this->db->table('tbl_entitas_type')->ignore(true)->insert([
            'kode'            => 'masjid_mushola',
            'nama_label'      => 'Masjid & Mushola',
            'icon'            => 'fas fa-mosque',
            'deskripsi'       => 'Entitas untuk transaksi keuangan dan data Masjid/Mushola',
            'operator_group'  => 'OperatorMasjidMushola',
            'has_masjid_link' => 0,
            'has_sk'          => 0,
            'urutan'          => 0, // Tempatkan paling atas atau biarkan 0
            'is_active'       => 1,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        
        // Pindahkan imam masjid ke grup lain atau kosongkan jika tidak ingin imam masjid dikelola oleh operator masjid 
        // Namun, user mungkin masih butuh Imam dikelola oleh operator. Kita biarkan saja karena nanti difilter di sidebar
    }

    public function down()
    {
        $this->db->table('tbl_entitas_type')->where('kode', 'masjid_mushola')->delete();
    }
}
