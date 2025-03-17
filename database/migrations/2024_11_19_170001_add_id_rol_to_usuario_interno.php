<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdRolToUsuarioInterno extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usuario_interno', function (Blueprint $table) {
            $table->unsignedBigInteger('id_rol')->nullable(); // Agregar el campo id_rol como clave foránea y permitir valores nulos

            // Opcional: Define una clave foránea si existe la tabla 'rol' y deseas establecer la relación
            $table->foreign('id_rol')->references('id_rol')->on('rol')->onDelete('set null');
        });
    }

    /**
     * Revierte las migraciones.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usuario_interno', function (Blueprint $table) {
            $table->dropForeign(['id_rol']); // Elimina la clave foránea si se revierte la migración
            $table->dropColumn('id_rol'); // Elimina el campo id_rol
        });
    }
}
