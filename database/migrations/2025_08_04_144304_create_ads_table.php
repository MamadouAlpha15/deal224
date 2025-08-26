<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('ads', function (Blueprint $table) {
        $table->id();
        $table->string('title'); // Titre de l’annonce
        $table->text('description'); // Description détaillée
        $table->integer('price'); // Prix de l’article
        $table->string('image')->nullable(); // Image de l’article
        $table->unsignedBigInteger('user_id'); // L’utilisateur qui a posté l’annonce



        $table->timestamps();

        // Clé étrangère vers la table users
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}
};
