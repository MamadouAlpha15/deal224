<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('ads', function (Blueprint $table) {
        // Ce champ servira à savoir quand l’annonce a été affichée pour la dernière fois
        $table->timestamp('last_shown_at')->nullable()->after('boosted_until');
    });
}

public function down()
{
    Schema::table('ads', function (Blueprint $table) {
        $table->dropColumn('last_shown_at');
    });
}

};
