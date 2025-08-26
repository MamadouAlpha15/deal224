<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'superadmin@gmail.com';
        $password = '12345678';

        User::updateOrCreate(
            ['email' => $email],

            [
                'name' => 'superadmin',
                'password' => Hash::make($password),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]
            );
        
    }
}
