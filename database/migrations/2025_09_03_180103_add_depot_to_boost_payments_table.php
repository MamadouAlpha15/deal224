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
    Schema::table('boost_payments', function (Blueprint $table) {
        $table->string('depot')->nullable()->after('payment_proof'); 
    });
}

public function down(): void
{
    Schema::table('boost_payments', function (Blueprint $table) {
        $table->dropColumn('depot');
    });
}


   
};
