<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasjidMusholaSeeder extends Seeder
{
    public function run()
    {
        $data = [];
        $kelurahan = ['Tanjung Permai', 'Teluk Lobam', 'Teluk Sasah', 'Kuala Sempang', 'Busung'];
        
        $masjidNames = [
            'Baitul Hikmah', 'Nurul Islam', 'Al-Falah', 'Jami Al-Hidayah', 
            'Baiturrahman', 'Ar-Raudhah', 'At-Taqwa', 'Al-Ikhlas'
        ];

        $musholaNames = [
            'Al-Husna', 'Nur Hidayah', 'At-Tawakal', 'Al-Istiqomah', 'Baitul Ma\'mur'
        ];

        // Generate 8 Masjids
        foreach ($masjidNames as $name) {
            $data[] = [
                'nama'           => 'Masjid ' . $name,
                'jenis'          => 'Masjid',
                'tahun_berdiri'  => mt_rand(1980, 2020),
                'alamat'         => 'Jl. Raya Seri Kuala Lobam No. ' . mt_rand(1, 50),
                'provinsi'       => 'Kepulauan Riau',
                'kabupaten_kota' => 'Kabupaten Bintan',
                'kecamatan'      => 'Seri Kuala Lobam',
                'kelurahan_desa' => $kelurahan[array_rand($kelurahan)],
                'rt'             => sprintf('%02d', mt_rand(1, 10)),
                'rw'             => sprintf('%02d', mt_rand(1, 5)),
                'nama_ketua_dkm' => 'H. ' . ['Ahmad', 'Mansur', 'Umar', 'Saleh', 'Abdullah'][mt_rand(0, 4)],
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ];
        }

        // Generate 5 Musholas
        foreach ($musholaNames as $name) {
            $data[] = [
                'nama'           => 'Mushola ' . $name,
                'jenis'          => 'Mushola',
                'tahun_berdiri'  => mt_rand(1990, 2022),
                'alamat'         => 'Gg. Mushola No. ' . mt_rand(1, 20),
                'provinsi'       => 'Kepulauan Riau',
                'kabupaten_kota' => 'Kabupaten Bintan',
                'kecamatan'      => 'Seri Kuala Lobam',
                'kelurahan_desa' => $kelurahan[array_rand($kelurahan)],
                'rt'             => sprintf('%02d', mt_rand(1, 10)),
                'rw'             => sprintf('%02d', mt_rand(1, 5)),
                'nama_ketua_dkm' => ['Budi', 'Joko', 'Rahman', 'Syarif', 'Anton'][mt_rand(0, 4)],
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ];
        }

        // Using Query Builder
        $this->db->table('tbl_masjid_mushola')->insertBatch($data);
    }
}
