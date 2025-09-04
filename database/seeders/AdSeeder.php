<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ad;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;

class AdSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Assurez-vous que le dossier "public/ads" existe
        if (!Storage::disk('public')->exists('ads')) {
            Storage::disk('public')->makeDirectory('ads');
        }

        for ($i = 1; $i <= 30; $i++) {
            // Crée une annonce
            $ad = Ad::create([
                'user_id' => 1, // Remplace par un user_id existant
                'title' => $faker->sentence(5),
                'description' => $faker->paragraph,
                'price' => $faker->numberBetween(1000, 50000),
                'boosted_until' => null,
            ]);

            // Crée 3 images pour chaque annonce
            for ($j = 1; $j <= 3; $j++) {
                $imageName = 'ad_' . $i . '_' . $j . '.jpg';

                // Génère l'image factice
                $faker->image(storage_path('app/public/ads'), 640, 480, null, false);

                // Sauvegarde dans la relation images
                $ad->images()->create([
                    'path' => 'ads/' . $imageName,
                ]);
            }
        }
    }
}
