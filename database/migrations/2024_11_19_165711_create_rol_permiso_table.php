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
        Schema::create('rol_permiso', function (Blueprint $table) {
            $table->id('id_rol_permiso'); // Campo de ID auto incrementable
            $table->unsignedBigInteger('id_rol'); // ID del rol, clave foránea
            $table->unsignedBigInteger('id_permiso'); // ID del permiso, clave foránea
            $table->timestamp('fecha_sistema')->default(DB::raw('CURRENT_TIMESTAMP')); // Fecha y hora del sistema

            // Definición de las claves foráneas
            $table->foreign('id_rol')->references('id_rol')->on('rol')->onDelete('cascade');
            $table->foreign('id_permiso')->references('id_permiso')->on('permiso')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rol_permiso');
    }
};
