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
    Schema::table('boost_messages', function (Blueprint $table) {
        $table->boolean('read')->default(false);
    });
}

public function down(): void
{
    Schema::table('boost_messages', function (Blueprint $table) {
        $table->dropColumn('read');
    });
}

};
