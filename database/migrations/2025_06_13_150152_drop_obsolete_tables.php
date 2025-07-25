<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 🔓 Step 1: Drop foreign keys (examples, you must adapt constraint names)
        Schema::table('tramite_cuestionario', function (Blueprint $table) {
            $table->dropForeign('tramite_cuestionario_ibfk_2');
        });

        Schema::table('historial_tramite', function (Blueprint $table) {
            $table->dropForeign('historial_tramite_ibfk_1');
            $table->dropForeign('historial_tramite_ibfk_2');
        });

        Schema::table('tipo_tramite_perfil_estado', function (Blueprint $table) {
            $table->dropForeign('tipo_tramite_perfil_estado_ibfk_1');
        });

        Schema::table('tramite', function (Blueprint $table) {
            $table->dropForeign('tramite_ibfk_1');
        });

        Schema::table('tramite_pago_pago_doble', function (Blueprint $table) {
            $table->dropForeign('tramite_pago_pago_doble_ibfk_1');
        });

        Schema::table('tipo_tramite_perfil_estado', function (Blueprint $table) {
            $table->dropForeign('tipo_tramite_perfil_estado_ibfk_2');
        });

        Schema::table('usuario', function (Blueprint $table) {
            $table->dropForeign('usuario_ibfk_1');
        });

        Schema::table('configuracion_estado_tramite', function (Blueprint $table) {
            $table->dropForeign('configuracion_estado_tramite_ibfk_3');
        });

        Schema::table('tipo_tramite_cuestionario', function (Blueprint $table) {
            $table->dropForeign('tipo_tramite_cuestionario_ibfk_1');
        });

        Schema::table('tipo_tramite_perfil_estado', function (Blueprint $table) {
            $table->dropForeign('tipo_tramite_perfil_estado_ibfk_3');
        });

        Schema::table('tramite', function (Blueprint $table) {
            $table->dropForeign('tramite_ibfk_3');
        });

        Schema::table('historial_tramite', function (Blueprint $table) {
            $table->dropForeign('historial_tramite_ibfk_4');
        });

        Schema::table('tramite_archivo', function (Blueprint $table) {
            $table->dropForeign('tramite_archivo_ibfk_1');
        });

        Schema::table('tramite_cuestionario', function (Blueprint $table) {
            $table->dropForeign('tramite_cuestionario_ibfk_1');
        });

        Schema::table('tramite_estado_tramite', function (Blueprint $table) {
            $table->dropForeign('tramite_estado_tramite_ibfk_1');
        });

        Schema::table('tramite_pago_pago_doble', function (Blueprint $table) {
            $table->dropForeign('tramite_pago_pago_doble_ibfk_2');
        });

        Schema::table('historial_asignacion', function (Blueprint $table) {
            $table->dropForeign('historial_asignacion_ibfk_3');
        });

        Schema::table('historial_asignacion', function (Blueprint $table) {
            $table->dropForeign('historial_asignacion_ibfk_4');
        });

        Schema::table('historial_tramite', function (Blueprint $table) {
            $table->dropForeign('historial_tramite_ibfk_5');
        });

        Schema::table('historial_tramite', function (Blueprint $table) {
            $table->dropForeign('historial_tramite_ibfk_6');
        });

        Schema::table('tramite', function (Blueprint $table) {
            $table->dropForeign('tramite_ibfk_4');
        });

        // 🗑 Step 2: Drop tables
        $tables = [
            'abl_alta_debito',
            'abl_baja_debito',
            'abl_pago_doble',
            'descargo_web',
            'reimputacion',
            'auto_baja_impositiva',
            'cambios_estadio_estado',
            'cuenta_descargo',
            'cuestionario_completo',
            'contribuyente_externo',
            'contribuyente_externo_cuenta',
            'contribuyente_externo_domicilio',
            'estadio_estado',
            'estado',
            'estado_correo',
            'expediente_multinota',
            'expediente_resolucion',
            'finalizacion_tramite',
            'intervencion',
            'jerarquia_perfil',
            'motivo_descargo',
            'pago',
            'pago_reimputacion',
            'perfil',
            'pregunta_cuestionario',
            'regla_prioridad',
            'regla_prioridad_log',
            'requisito',
            'resolucion',
            'revision_rechazo',
            'solicitante_cuenta_caracter',
            'tarjeta',
            'tipo_tramite',
            'tipo_tramite_campo',
            'tipo_tramite_cuestionario',
            'tipo_tramite_perfil_estado',
            'tramite',
            'tramite_cuestionario',
            'tramite_pago_pago_doble',
            'usuario_categoria',
            'usuario',
            'usuario_permiso'
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally, you could recreate the tables if needed.
        // But this would be a huge reverse migration.
    }
};
