<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametroMailTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parametro_mail', function (Blueprint $table) {
            $table->id('id_parametro_mail'); // Campo de ID (auto incrementable)
            $table->timestamp('fecha_sistema')->default(DB::raw('CURRENT_TIMESTAMP')); // Fecha y hora del sistema
            $table->string('host'); // Host del servidor de correo
            $table->integer('puerto'); // Puerto del servidor de correo
            $table->string('usuario'); // Usuario para la autenticación del correo
            $table->string('clave'); // Clave de la cuenta de correo
            $table->boolean('activo')->default(true); // Indica si el parámetro está activo
        });
    }

    /**
     * Revierte las migraciones.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parametro_mail'); // Eliminar la tabla
    }
}
