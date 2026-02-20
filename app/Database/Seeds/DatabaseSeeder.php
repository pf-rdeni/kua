<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // ============================================================
        // 1. Insert Groups (Roles) ke auth_groups
        // ============================================================
        $groups = [
            [
                'name'        => 'SuperAdmin',
                'description' => 'Akses penuh ke semua modul dan user management',
            ],
            [
                'name'        => 'Admin',
                'description' => 'Akses ke semua modul pendataan, tanpa user management',
            ],
            [
                'name'        => 'OperatorMubaligh',
                'description' => 'Operator khusus modul Mubaligh',
            ],
            [
                'name'        => 'OperatorMasjidMushola',
                'description' => 'Operator khusus modul Masjid & Mushola',
            ],
            [
                'name'        => 'OperatorFarduKifayah',
                'description' => 'Operator khusus modul Pengurus Fardu Kifayah',
            ],
            [
                'name'        => 'OperatorPenggaliKubur',
                'description' => 'Operator khusus modul Petugas Penggali Kubur',
            ],
            [
                'name'        => 'OperatorTpqMdta',
                'description' => 'Operator khusus modul Lembaga TPQ & MDTA',
            ],
            [
                'name'        => 'OperatorMajelisTaklim',
                'description' => 'Operator khusus modul Majelis Taklim',
            ],
        ];

        $db = \Config\Database::connect();

        // Cek apakah groups sudah ada, jika belum insert
        foreach ($groups as $group) {
            $exists = $db->table('auth_groups')
                         ->where('name', $group['name'])
                         ->countAllResults();

            if ($exists === 0) {
                $db->table('auth_groups')->insert($group);
            }
        }

        echo "✔ 8 Groups (Roles) berhasil di-seed.\n";

        // ============================================================
        // 2. Buat User SuperAdmin
        // ============================================================
        $existingUser = $db->table('users')
                           ->where('email', 'superadmin@kua.test')
                           ->countAllResults();

        if ($existingUser === 0) {
            // Insert user
            $db->table('users')->insert([
                'email'            => 'superadmin@kua.test',
                'username'         => 'superadmin',
                'password_hash'    => password_hash('password123', PASSWORD_DEFAULT),
                'active'           => 1,
                'force_pass_reset' => 0,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);

            // Dapatkan ID user yang baru dibuat
            $userId = $db->insertID();

            // Dapatkan ID group SuperAdmin
            $superAdminGroup = $db->table('auth_groups')
                                  ->where('name', 'SuperAdmin')
                                  ->get()
                                  ->getRow();

            if ($superAdminGroup) {
                // Assign user ke group SuperAdmin
                $db->table('auth_groups_users')->insert([
                    'group_id' => $superAdminGroup->id,
                    'user_id'  => $userId,
                ]);
            }

            echo "✔ User SuperAdmin (superadmin@kua.test / password123) berhasil dibuat.\n";
        } else {
            echo "⚠ User SuperAdmin sudah ada, dilewati.\n";
        }

        echo "\n========================================\n";
        echo "  DATABASE SEEDING SELESAI!\n";
        echo "========================================\n";
        echo "Login credentials:\n";
        echo "  Email    : superadmin@kua.test\n";
        echo "  Username : superadmin\n";
        echo "  Password : password123\n";
        echo "========================================\n";
    }
}
