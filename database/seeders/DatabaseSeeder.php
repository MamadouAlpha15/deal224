<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crée un utilisateur unique avec mot de passe
        User::firstOrCreate(
            ['email' => 'idiatou23@gmail.com'], // critère unique
            [
                'name' => 'sow',
                'password' => Hash::make('password123'), // mot de passe sécurisé
                'profile_photo' => null, // optionnel
            ]
        );

        // Appelle le seeder des annonces
        $this->call(AdSeeder::class);
    }
}
