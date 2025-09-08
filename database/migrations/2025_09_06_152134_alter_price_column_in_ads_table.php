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
        $table->decimal('price', 15, 2)->change();
    });
}

public function down()
{
    Schema::table('ads', function (Blueprint $table) {
        $table->integer('price')->change();
    });
}

};
