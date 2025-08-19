<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        // historial_tramite
        Schema::table('historial_tramite', function (Blueprint $table) {
            // Añadir FK apuntando a la nueva tabla
            $table->foreign('id_tramite', 'historial_tramite_ibfk_4')->references('id_tramite')->on('multinota');
        });

        // tramite_archivo
        Schema::table('tramite_archivo', function (Blueprint $table) {
            // Añadir FK apuntando a la nueva tabla
            $table->foreign('id_tramite', 'tramite_archivo_ibfk_1')->references('id_tramite')->on('multinota');
        });

        // tramite_estado_tramite
        Schema::table('tramite_estado_tramite', function (Blueprint $table) {
            // Añadir FK apuntando a la nueva tabla
            $table->foreign('id_tramite', 'tramite_estado_tramite_ibfk_1')->references('id_tramite')->on('multinota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // Reverse all the changes
        Schema::table('historial_tramite', function (Blueprint $table) {
            $table->dropForeign('historial_tramite_ibfk_4');
        });

        Schema::table('tramite_archivo', function (Blueprint $table) {
            $table->dropForeign('tramite_archivo_ibfk_1');
        });

        Schema::table('tramite_estado_tramite', function (Blueprint $table) {
            $table->dropForeign('tramite_estado_tramite_ibfk_1');
        });
    }
};
