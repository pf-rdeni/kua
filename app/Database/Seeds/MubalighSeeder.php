<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MubalighSeeder extends Seeder
{
    public function run()
    {
        $data = [];
        $firstNames = ['Ahmad', 'Budi', 'Cecep', 'Deni', 'Eko', 'Rizal', 'Syamsul', 'Hasan', 'Umar', 'Ali', 'Zain', 'Ilham', 'Fajar', 'Taufik', 'Mahmoud', 'Habib'];
        $lastNames = ['Kurniawan', 'Pratama', 'Santoso', 'Hidayat', 'Maulana', 'Abdullah', 'Rahman', 'Siddiq', 'Anwar', 'Hafiz'];
        $places = ['Tanjung Pinang', 'Batam', 'Tanjung Uban', 'Kijang', 'Pekanbaru'];
        $addresses = ['Jl. Indrapura', 'Jl. Merdeka', 'Jl. Bhakti', 'Jl. Pendidikan', 'Jl. Nusantara'];
        $kelurahan = ['Tanjung Permai', 'Teluk Lobam', 'Teluk Sasah', 'Kuala Sempang', 'Busung'];

        for ($i = 0; $i < 20; $i++) {
            $name = $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
            
            // Generate random 16 digit NIK
            $nik = '2101'; // Kep Riau (21), Bintan (01)
            for ($j = 0; $j < 12; $j++) {
                $nik .= mt_rand(0, 9);
            }

            // Generate random phone number
            $hp = '08';
            for ($k = 0; $k < 10; $k++) {
                $hp .= mt_rand(0, 9);
            }

            $date_stamp = time() - mt_rand(30 * 365 * 24 * 60 * 60, 50 * 365 * 24 * 60 * 60);

            $data[] = [
                'entitas_type'   => 'mubaligh',
                'nama_lengkap'   => $name,
                'nik'            => $nik,
                'tempat_lahir'   => $places[array_rand($places)],
                'tanggal_lahir'  => date('Y-m-d', $date_stamp),
                'jenis_kelamin'  => 'L',
                'alamat'         => $addresses[array_rand($addresses)] . ' No. ' . mt_rand(1, 100),
                'provinsi'       => 'Kepulauan Riau',
                'kabupaten_kota' => 'Kabupaten Bintan',
                'kecamatan'      => 'Seri Kuala Lobam',
                'kelurahan_desa' => $kelurahan[array_rand($kelurahan)],
                'rt'             => sprintf('%02d', mt_rand(1, 50)),
                'rw'             => sprintf('%02d', mt_rand(1, 10)),
                'no_hp'          => $hp,
                'status_aktif'   => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ];
        }

        // Using Query Builder
        $this->db->table('tbl_personil')->insertBatch($data);
    }
}
