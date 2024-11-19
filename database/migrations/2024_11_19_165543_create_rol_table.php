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
        Schema::create('rol', function (Blueprint $table) {
            $table->id('id_rol'); // Campo de ID auto incrementable
            $table->string('nombre'); // Nombre del rol
            $table->string('clave'); // Clave del rol
            $table->timestamp('fecha_sistema')->default(DB::raw('CURRENT_TIMESTAMP')); // Fecha y hora del sistema por defecto
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rol');
    }
};
