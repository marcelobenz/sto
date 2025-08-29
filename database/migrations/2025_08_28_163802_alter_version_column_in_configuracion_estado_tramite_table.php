<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1: Convierte los valores existentes a string
        DB::statement("UPDATE configuracion_estado_tramite SET version = CAST(version AS CHAR)");

        // 2: Se cambia el tipo a VARCHAR(100)
        Schema::table('configuracion_estado_tramite', function (Blueprint $table) {
            $table->string('version', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1: Convierte los valores existentes nuevamente a int (fallara si hay registros con el nuevo tipo de dato string)
        DB::statement("UPDATE configuracion_estado_tramite SET version = CAST(version AS UNSIGNED)");

        // 2: Se cambia el tipo a INT
        Schema::table('configuracion_estado_tramite', function (Blueprint $table) {
            $table->integer('version')->change();
        });
    }
};
