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
       Schema::create('boost_messages', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('boost_payment_id');
    $table->unsignedBigInteger('user_id'); // qui envoie le message
    $table->text('message');
    $table->timestamps();

    $table->foreign('boost_payment_id')->references('id')->on('boost_payments')->onDelete('cascade');
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boost_messages');
    }
};
