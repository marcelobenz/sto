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
        Schema::create('permiso', function (Blueprint $table) {
            $table->id('id_permiso'); // Campo de ID auto incrementable
            $table->timestamp('fecha_sistema')->default(DB::raw('CURRENT_TIMESTAMP')); // Fecha y hora del sistema
            $table->string('permiso'); // Nombre del permiso
            $table->string('permiso_clave'); // Clave única del permiso
            $table->text('descripcion')->nullable(); // Descripción del permiso, puede ser nula
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permiso');
    }
};
