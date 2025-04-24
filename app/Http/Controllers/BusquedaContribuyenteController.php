<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

use App\Models\TipoPersonalidadJuridica;
use App\Models\MultinotaServicio;
use App\Models\ContribuyenteMultinota;
use App\DTOs\ContribuyenteMultinotaDTO;
use App\DTOs\PersonaFisicaDTO;
use App\DTOs\PersonaJuridicaDTO;
use App\DTOs\CuentaDTO;
use App\Transformers\PersonaFisicaTransformer;
use App\Transformers\PersonaJuridicaTransformer;

class BusquedaContribuyenteController extends Controller {
    public function buscar(Request $request) {
        try {
            $cuil = $request->post('cuil');
            $tipo = new TipoPersonalidadJuridica();

            $cuilFormateado = substr($cuil, 0, 2);;
            $cuilNumero = (int) $cuilFormateado;
            if ($cuilNumero >= 30){
                $tipo->setCodigo("2");
                $tipo->setDescripcion("PERSONA JURIDICA");
            } else {
                $tipo->setCodigo("1");
                $tipo->setDescripcion("PERSONA FISICA");
            }

            if ($tipo == null) {
                throw new \Exception("El usuario ingresado no existe");
            }

            // Se recupera el servicio asociado a la multinota
            $servicio = MultinotaServicio::join('tipo_tramite_multinota as ttm', 'ttm.id_multinota_servicio', '=', 'multinota_servicios.id_multinota_servicio')
            ->where('ttm.id_tipo_tramite_multinota', (int) $request->post('idMultinota'))
            ->select('multinota_servicios.url')
            ->first();

            // Se reemplaza el CUIT en el template de la URL
            if($servicio) {
                $url = str_replace('{cuit}', str_replace('-', '', $cuil), $servicio->url);
                $response = Http::get($url)->throw();;
                $data = $response->json();

                if($data['res'] === 'success') {
                    if(array_key_exists('status', $data)) {
                        $cuentaDTO = new CuentaDTO();
                    } else {
                        //200
                        $cuentasArray = $data['data'];
                        $cuentas = array_map(function ($item) {
                            return new CuentaDTO($item['codigo'], $item['descripcion']);
                        }, $cuentasArray);
                    }

                    //throw new \Exception("Error al obtener las cuentas: ");
                } else {
                    throw new \Exception("Error al obtener las cuentas");
                }
            }

            if ($tipo->isPersonaFisica()) {
                $model = ContribuyenteMultinota::where('cuit', str_replace('-', '', $cuil))->first();

                if($model == null) {
                    throw new \Exception("El usuario ingresado no existe");
                }

                $dto = new ContribuyenteMultinotaDTO(
                    $model->id_contribuyente_multinota,
                    $model->cuit,
                    $model->nombre,
                    $model->apellido,
                    $model->correo,
                    $model->telefono1,
                    $model->telefono2,
                    $model->clave,
                    $model->id_direccion,
                    $model->activo,
                    $model->cantidad_intentos,
                    $model->codigo_activacion,
                    new DateTime($model->fecha_activacion)
                );

                $personaFisica = new PersonaFisicaDTO();
                $personaFisica->setCuilCuit($dto->getCuit());
                $personaFisica->setNombre($dto->getNombre());
                $personaFisica->setApellido($dto->getApellido());

                $contribuyente = (new PersonaFisicaTransformer())
                ->personaFisica($personaFisica)
                ->cuentas($cuentas)
                ->transform();

                //this.navegador.navegar(contribuyente, this.claveCategoria, this.getUnmaskCuit());
            } else if ($tipo->isPersonaJuridica()) {
                $model = ContribuyenteMultinota::where('cuit', str_replace('-', '', $cuil))->first();

                if($model == null) {
                    throw new \Exception("El usuario ingresado no existe");
                }

                $dto = new ContribuyenteMultinotaDTO(
                    $model->id_contribuyente_multinota,
                    $model->cuit,
                    $model->nombre,
                    $model->apellido,
                    $model->correo,
                    $model->telefono1,
                    $model->telefono2,
                    $model->clave,
                    $model->id_direccion,
                    $model->activo,
                    $model->cantidad_intentos,
                    $model->codigo_activacion,
                    new DateTime($model->fecha_activacion)
                );

                $personaJuridica = new PersonaJuridicaDTO();
                $personaJuridica->setCuit($dto->getCuit());
                $personaJuridica->setRazonSocial($dto->getApellido());

                $contribuyente = (new PersonaJuridicaTransformer())
                ->personaJuridica($personaJuridica)
                ->cuentas($cuentas)
                ->transform();

                //this.navegador.navegar(contribuyente, this.claveCategoria, this.getUnmaskCuit());
            } else {
                /* return new SinPersonalidadJuridicaTransformer().cuentas( cuentas ).cuit( cuit ).transform(); */
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
