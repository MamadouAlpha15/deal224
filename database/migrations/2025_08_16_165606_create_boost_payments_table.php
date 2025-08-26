<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boost_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // utilisateur qui paye
            $table->integer('ads_count');          // nombre d’annonces à booster
            $table->integer('amount');             // montant payé
            $table->dateTime('start_date')->nullable(); // début boost
            $table->dateTime('end_date')->nullable();   // fin boost
            $table->string('status')->default('pending'); // statut du paiement
            $table->string('payment_proof')->nullable(); // chemin de la capture Orange Money
            $table->timestamps();

            // Relation avec la table users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boost_payments');
    }
};

