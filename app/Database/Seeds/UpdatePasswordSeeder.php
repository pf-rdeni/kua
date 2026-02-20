<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\UserModel;

class UpdatePasswordSeeder extends Seeder
{
    public function run()
    {
        $userModel = new UserModel();
        $users = $userModel->findAll();

        foreach ($users as $user) {
            // Force password reset to trigger hashing
            $user->password = 'password123'; 
            $userModel->save($user);
            echo "Updated password for: " . $user->email . "\n";
        }
    }
}
