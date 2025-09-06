<?php

namespace Database\Seeders;

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ad;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;
class AdSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Assure que le dossier existe
        if (!Storage::disk('public')->exists('ads')) {
            Storage::disk('public')->makeDirectory('ads');
        }

        // 1️⃣ Crée 5 utilisateurs
        for ($u = 1; $u <= 5; $u++) {
            $user = User::factory()->create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'profile_photo' => null, // Optionnel, tu peux ajouter une photo ici
            ]);

            // 2️⃣ Pour chaque utilisateur, crée 3 à 6 annonces
            $adCount = rand(3, 6);
            for ($i = 1; $i <= $adCount; $i++) {
                $ad = Ad::create([
                    'user_id' => $user->id,
                    'title' => $faker->sentence(5),
                    'description' => $faker->paragraph,
                    'price' => $faker->numberBetween(1000, 50000),
                    'boosted_until' => null,
                ]);

                // 3️⃣ Pour chaque annonce, crée 2 à 4 images
                $imgCount = rand(2, 4);
                for ($j = 1; $j <= $imgCount; $j++) {
                    $imageFileName = $faker->image(storage_path('app/public/ads'), 640, 480, null, false);
                    $ad->images()->create([
                        'path' => 'ads/' . $imageFileName,
                    ]);
                }
            }
        }

        $this->command->info('✅ Seed terminé : utilisateurs + annonces + images générés.');
    }
}
