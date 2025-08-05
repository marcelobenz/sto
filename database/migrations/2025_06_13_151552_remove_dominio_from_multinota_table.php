<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('multinota', function (Blueprint $table) {
            $table->dropColumn('dominio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('multinota', function (Blueprint $table) {
            $table->string('dominio', 20)->nullable(); // varchar(20)
        });
    }
};
