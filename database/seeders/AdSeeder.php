<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ad;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash; // Important pour Hash::make

class AdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création d'un générateur Faker pour les données factices
        $faker = Faker::create();

        // 🔹 Vérifie que le dossier "ads" existe dans storage/app/public
        // Si le dossier n'existe pas, on le crée
        if (!Storage::disk('public')->exists('ads')) {
            Storage::disk('public')->makeDirectory('ads');
        }

        // 🔹 Crée 5 utilisateurs avec annonces
        for ($u = 1; $u <= 5; $u++) {

            // Création d'un utilisateur
            $user = User::factory()->create([
                'name' => $faker->name, // nom aléatoire
                'email' => $faker->unique()->safeEmail, // email unique
                'profile_photo' => null, // pas de photo pour l'instant
                'password' => Hash::make('password123'), // obligatoire sinon MySQL plante
            ]);

            // 🔹 Pour chaque utilisateur, on crée entre 3 et 6 annonces
            $adCount = rand(3, 6);
            for ($i = 1; $i <= $adCount; $i++) {

                // Création de l'annonce
                $ad = Ad::create([
                    'user_id' => $user->id, // associe l'annonce à l'utilisateur
                    'title' => $faker->sentence(5), // titre aléatoire
                    'description' => $faker->paragraph, // description aléatoire
                    'price' => $faker->numberBetween(1000, 50000), // prix aléatoire
                    'boosted_until' => null, // pas boosté automatiquement
                ]);

                // 🔹 Pour chaque annonce, on crée entre 2 et 4 images
                $imgCount = rand(2, 4);
                for ($j = 1; $j <= $imgCount; $j++) {

                    // Génère une image factice dans storage/app/public/ads
                    $imageFileName = $faker->image(
                        storage_path('app/public/ads'), // chemin du dossier
                        640, // largeur
                        480, // hauteur
                        null, // catégorie (aucune)
                        false // retourne seulement le nom du fichier
                    );

                    // Associe l'image à l'annonce
                    $ad->images()->create([
                        'path' => 'ads/' . $imageFileName,
                    ]);
                }
            }
        }

        // Affiche un message dans le terminal quand le Seeder est terminé
        $this->command->info('✅ Seed terminé : utilisateurs + annonces + images générés.');
    }
}
