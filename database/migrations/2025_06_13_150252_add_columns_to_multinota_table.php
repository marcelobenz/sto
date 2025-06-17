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
        Schema::table('multinota', function (Blueprint $table) {
            if (!Schema::hasColumn('multinota', 'id_prioridad')) {
                $table->unsignedMediumInteger('id_prioridad')->nullable();
            }

            if (!Schema::hasColumn('multinota', 'id_solicitante')) {
                $table->unsignedMediumInteger('id_solicitante')->nullable();
            }

            if (!Schema::hasColumn('multinota', 'id_usuario_interno')) {
                $table->integer('id_usuario_interno')->nullable();
            }

            if (!Schema::hasColumn('multinota', 'r_caracter')) {
                $table->unsignedMediumInteger('r_caracter')->nullable();
            }

            if (!Schema::hasColumn('multinota', 'correo')) {
                $table->string('correo')->nullable();
            }

            if (!Schema::hasColumn('multinota', 'cuit_contribuyente')) {
                $table->string('cuit_contribuyente')->nullable();
            }

            if (!Schema::hasColumn('multinota', 'flag_rechazado')) {
                $table->boolean('flag_rechazado')->default(false);
            }

            if (!Schema::hasColumn('multinota', 'flag_cancelado')) {
                $table->boolean('flag_cancelado')->default(false);
            }
        });

        Schema::table('multinota', function (Blueprint $table) {
            // Add foreign keys if they do not exist yet
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $foreignKeys = collect($sm->listTableForeignKeys('multinota'))->map->getName();

            if (!$foreignKeys->contains('multinota_id_prioridad_foreign')) {
                $table->foreign('id_prioridad')->references('id_prioridad')->on('prioridad');
            }

            if (!$foreignKeys->contains('multinota_id_solicitante_foreign')) {
                $table->foreign('id_solicitante')->references('id_solicitante')->on('solicitante');
            }

            if (!$foreignKeys->contains('multinota_id_usuario_interno_foreign')) {
                $table->foreign('id_usuario_interno')->references('id_usuario_interno')->on('usuario_interno');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('multinota', function (Blueprint $table) {
            $table->dropColumn([
                'id_prioridad',
                'id_solicitante',
                'id_usuario_interno',
                'r_caracter',
                'correo',
                'cuit_contribuyente',
                'flag_rechazado',
                'flag_cancelado',
            ]);
        });
    }
};
