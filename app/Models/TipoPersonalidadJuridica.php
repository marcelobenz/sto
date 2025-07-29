<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPersonalidadJuridica extends Model
{
    private static string $PERSONA_FISICA = '1';

    private static string $PERSONA_JURIDICA = '2';

    private string $codigo;

    private string $descripcion;

    public function isPersonaFisica()
    {
        return $this->codigo != null && $this->codigo === self::$PERSONA_FISICA;
    }

    public function isPersonaJuridica()
    {
        return $this->codigo != null && $this->codigo === self::$PERSONA_JURIDICA;
    }

    public function __construct()
    {
        $arguments = func_get_args();
        $numberOfArguments = func_num_args();

        if (method_exists($this, $function =
                'ConstructorWithArgument'.$numberOfArguments)) {
            call_user_func_array(
                [$this, $function],
                $arguments
            );
        }
    }

    public function getCodigo()
    {
        return $this->codigo;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
}
