<?php

use Illuminate\Database\Migrations\Migration; // 📦 Classe de base pour toutes les migrations Laravel
use Illuminate\Database\Schema\Blueprint;      // 🧱 Permet de définir la structure d’une table (colonnes, types…)
use Illuminate\Support\Facades\Schema;         // 🛠️ Fournit les méthodes pour manipuler les tables (create, drop, etc.)

// 🔄 Retourne une classe anonyme qui contient les méthodes `up()` et `down()`
return new class extends Migration
{
    /**
     * 🟢 Cette méthode est appelée lorsque tu exécutes `php artisan migrate`
     * Elle sert à créer la table `ad_images`
     */
    public function up(): void
    {
        Schema::create('ad_images', function (Blueprint $table) {
            $table->id(); // 🔹 Clé primaire auto-incrémentée (id)

            $table->unsignedBigInteger('ad_id'); 
            // 🔗 Colonne pour stocker l’ID de l’annonce liée (clé étrangère vers la table `ads`)

            $table->string('path'); 
            // 🖼️ Stocke le chemin de l’image dans le dossier de stockage (storage/app/public/...)

            $table->timestamps(); 
            // 🕒 Deux colonnes automatiques : created_at et updated_at

            // 🔐 Déclaration de la clé étrangère
            $table->foreign('ad_id')           // La colonne ad_id...
                  ->references('id')->on('ads') // ...fait référence à l’id de la table `ads`
                  ->onDelete('cascade');       // 🗑️ Si une annonce est supprimée, ses images le seront aussi
        });
    }

    /**
     * 🔴 Cette méthode est appelée si tu fais un rollback : `php artisan migrate:rollback`
     * Elle supprime simplement la table `ad_images`
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_images'); // ❌ Supprime la table si elle existe
    }
};
