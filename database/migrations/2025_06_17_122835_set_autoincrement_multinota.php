<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Drop foreign keys that reference id_tramite
        Schema::table('multinota_campo_valor', function ($table) {
            $table->dropForeign('multinota_campo_valor_ibfk_1');
        });

        Schema::table('multinota_seccion_valor', function ($table) {
            $table->dropForeign('multinota_seccion_valor_ibfk_1');
        });

        // Step 2: Alter id_tramite to AUTO_INCREMENT
        DB::statement('ALTER TABLE multinota MODIFY id_tramite MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT');

        // Step 3: Re-add the foreign keys
        Schema::table('multinota_campo_valor', function ($table) {
            $table->foreign('id_tramite', 'multinota_campo_valor_ibfk_1')->references('id_tramite')->on('multinota');
        });

        Schema::table('multinota_seccion_valor', function ($table) {
            $table->foreign('id_tramite', 'multinota_seccion_valor_ibfk_1')->references('id_tramite')->on('multinota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse: drop FK, remove auto_increment, re-add FK

        Schema::table('multinota_campo_valor', function ($table) {
            $table->dropForeign('multinota_campo_valor_ibfk_1');
        });

        Schema::table('multinota_seccion_valor', function ($table) {
            $table->dropForeign('multinota_seccion_valor_ibfk_1');
        });

        DB::statement('ALTER TABLE multinota MODIFY id_tramite MEDIUMINT UNSIGNED NOT NULL');

        Schema::table('multinota_campo_valor', function ($table) {
            $table->foreign('id_tramite', 'multinota_campo_valor_ibfk_1')->references('id_tramite')->on('multinota');
        });

        Schema::table('multinota_seccion_valor', function ($table) {
            $table->foreign('id_tramite', 'multinota_seccion_valor_ibfk_1')->references('id_tramite')->on('multinota');
        });
    }
};
