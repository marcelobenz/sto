<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

use App\Models\TipoPersonalidadJuridica;
use App\Models\MultinotaServicio;
use App\Models\ContribuyenteMultinota;
use App\Models\TipoTramiteMultinota;
use App\Models\SeccionMultinota;
use App\Models\Campo;
use App\Models\OpcionCampo;
use App\Models\MensajeInicial;
use App\Models\Solicitante;
use App\Models\SolicitanteCuentaCaracter;
use App\Models\Direccion;
use App\Models\CodigoArea;
use App\DTOs\ContribuyenteMultinotaDTO;
use App\DTOs\PersonaFisicaDTO;
use App\DTOs\PersonaJuridicaDTO;
use App\DTOs\TramiteMultinotaDTO;
use App\DTOs\CuentaDTO;
use App\DTOs\FormularioMultinotaDTO;
use App\DTOs\SolicitanteDTO;
use App\DTOs\RepresentanteDTO;
use App\DTOs\CodigoAreaDTO;
use App\DTOs\DocumentoDTO;
use App\DTOs\DomicilioDTO;
use App\DTOs\TipoCaracterDTO;
use App\Enums\TipoCaracterEnum;
use App\Transformers\PersonaFisicaTransformer;
use App\Transformers\PersonaJuridicaTransformer;

class InstanciaMultinotaController extends Controller {
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

            // Se recupera objeto de la multinota          
            $multinota = TipoTramiteMultinota::where('id_tipo_tramite_multinota', (int) $request->post('idMultinota'))->first();

            // Se reemplaza el CUIT en el template de la URL
            if($servicio) {
                $url = str_replace('{cuit}', str_replace('-', '', $cuil), $servicio->url);
                $response = Http::get($url)->throw();;
                $data = $response->json();

                if($data['res'] === 'success') {
                    if(array_key_exists('status', $data['data'])) {
                        $cuentaDTO = new CuentaDTO();
                    } else {
                        //200
                        $cuentasArray = $data['data'];
                        $cuentas = array_map(function ($item) {
                            return new CuentaDTO(['codigo' => $item['codigo'], 'descripcion' => $item['descripcion']]);
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

                $contribuyente = new ContribuyenteMultinotaDTO(
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

                // Obtengo dirección del solicitante por ID
                $direccion = Direccion::select('calle', 'numero')->where('id_direccion', $model->id_direccion)->first();
                $direccionCompleta = $direccion->calle . ' ' . $direccion->numero;

                $personaFisica = new PersonaFisicaDTO();
                $personaFisica->setCuit($contribuyente->getCuit());
                $personaFisica->setNombre($contribuyente->getNombre());
                $personaFisica->setApellido($contribuyente->getApellido());
                $personaFisica->setDireccion($direccionCompleta);

                $contribuyenteTransformed = (new PersonaFisicaTransformer())
                ->personaFisica($personaFisica)
                ->cuentas($cuentas ?? [])
                ->transform();
                
                return InstanciaMultinotaController::showMultinotaInterna($multinota, $contribuyenteTransformed, 'Fisica');
            } else if ($tipo->isPersonaJuridica()) {
                $model = ContribuyenteMultinota::where('cuit', str_replace('-', '', $cuil))->first();

                if($model == null) {
                    throw new \Exception("El usuario ingresado no existe");
                }

                $contribuyente = new ContribuyenteMultinotaDTO(
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

                // Obtengo dirección del solicitante por ID
                $direccion = Direccion::select('calle', 'numero')->where('id_direccion', $model->id_direccion)->first();
                $direccionCompleta = $direccion->calle . ' ' . $direccion->numero;

                $personaJuridica = new PersonaJuridicaDTO();
                $personaJuridica->setCuit($contribuyente->getCuit());
                $personaJuridica->setRazonSocial($contribuyente->getApellido());
                $personaJuridica->setDireccion($direccionCompleta);

                $contribuyenteTransformed = (new PersonaJuridicaTransformer())
                ->personaJuridica($personaJuridica)
                ->cuentas($cuentas)
                ->transform();

                return InstanciaMultinotaController::showMultinotaInterna($multinota, $contribuyenteTransformed, 'Juridica');
            } else {
                /* return new SinPersonalidadJuridicaTransformer().cuentas( cuentas ).cuit( cuit ).transform(); */
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function showMultinotaInterna($multinota, $contribuyente, $persona) {
        $pasos = [];

        // Get secciones multinota
        $secciones = SeccionMultinota::join('multinota_seccion', 'seccion.id_seccion', '=', 'multinota_seccion.id_seccion')
        ->where('multinota_seccion.id_tipo_tramite_multinota', $multinota->id_tipo_tramite_multinota)
        ->select('seccion.*', 'multinota_seccion.orden')
        ->orderBy('multinota_seccion.orden')
        ->get();

        // Get campos de cada seccion
        foreach ($secciones as $s) {
            $campos = Campo::where('id_seccion', $s->id_seccion)
            ->orderBy('orden')
            ->get();

            $campos = collect($campos)
            ->map(function ($c) {
                $c->gridSpan = $c->dimension;
                $c->isString = in_array($c->tipo, ['STRING']);
                $c->isInteger = in_array($c->tipo, ['INTEGER']);
                $c->isSelect = in_array($c->tipo, ['LISTA']);
                $c->isCajasSeleccion = in_array($c->tipo, ['CAJAS_SELECCION']);
                $c->isTextarea = in_array($c->tipo, ['TEXTAREA_FIJO']);
                $c->isDate = in_array($c->tipo, ['DATE']);

                //Si el campo es LISTA o CAJAS_SELECCION, se le cargan las opciones
                $c->opciones = OpcionCampo::where('id_campo', $c->id_campo)
                ->orderBy('orden', 'asc')
                ->get();

                return $c;
            });

            $s->campos = $campos;
        }

        // Get mensaje inicial
        $mensajeInicial = MensajeInicial::join('tipo_tramite_mensaje_inicial', 'mensaje_inicial.id_mensaje_inicial', '=', 'tipo_tramite_mensaje_inicial.id_mensaje_inicial')
        ->where('tipo_tramite_mensaje_inicial.id_tipo_tramite_multinota', $multinota->id_tipo_tramite_multinota)
        ->select('mensaje_inicial.*')
        ->orderBy('mensaje_inicial.id_mensaje_inicial', 'desc')
        ->first(); 

        $multinota->mensaje_inicial = $mensajeInicial->mensaje_inicial;

        $llevaMensaje = !empty($mensajeInicial->mensaje_inicial);

        // Get pasos
        if($persona === 'Fisica') {
            $pasos = [
                ['orden' => 1, 'titulo' => 'Datos del Solicitante', 'iconoPaso' => 'fa-plus', 'ruta' => 'partials.etapas-tramite.inicio-tramite-general', 'completado' => false],
                ['orden' => 2, 'titulo' => 'Datos a Completar', 'iconoPaso' => 'fa-pen-to-square', 'ruta' => 'partials.etapas-tramite.seccion-valor-multinota', 'completado' => false],
                ['orden' => 3, 'titulo' => 'Información Adicional', 'iconoPaso' => 'fa-info', 'ruta' => 'partials.etapas-tramite.informacion-adicional', 'completado' => false],
                ['orden' => 4, 'titulo' => 'Adjuntar Documentación', 'iconoPaso' => 'fa-upload', 'ruta' => 'partials.etapas-tramite.adjuntar-documentacion', 'completado' => false],
                ['orden' => 5, 'titulo' => 'Resumen', 'iconoPaso' => 'fa-file-lines', 'ruta' => 'partials.etapas-tramite.resumen-multinota', 'completado' => false],
            ];
        } else {
            $pasos = [
                ['orden' => 1, 'titulo' => 'Datos del Solicitante', 'iconoPaso' => 'fa-plus', 'ruta' => 'partials.etapas-tramite.inicio-tramite-general', 'completado' => false],
                ['orden' => 2, 'titulo' => 'Datos del Representante', 'iconoPaso' => 'fa-street-view', 'ruta' => 'partials.etapas-tramite.solicitante', 'completado' => false],
                ['orden' => 3, 'titulo' => 'Datos a Completar', 'iconoPaso' => 'fa-pen-to-square', 'ruta' => 'partials.etapas-tramite.seccion-valor-multinota', 'completado' => false],
                ['orden' => 4, 'titulo' => 'Información Adicional', 'iconoPaso' => 'fa-info', 'ruta' => 'partials.etapas-tramite.informacion-adicional', 'completado' => false],
                ['orden' => 5, 'titulo' => 'Adjuntar Documentación', 'iconoPaso' => 'fa-upload', 'ruta' => 'partials.etapas-tramite.adjuntar-documentacion', 'completado' => false],
                ['orden' => 6, 'titulo' => 'Resumen', 'iconoPaso' => 'fa-file-lines', 'ruta' => 'partials.etapas-tramite.resumen-multinota', 'completado' => false],
            ]; 
        }

        $formulario = new FormularioMultinotaDTO($multinota, $secciones, $contribuyente['cuentas'], $pasos, $llevaMensaje);

        /* $solicitante = new SolicitanteDTO(str_replace(' ', '', $contribuyente['cuentas'][0]->getCodigo()), ''); */
        $solicitante = new SolicitanteDTO();
        
        // Se guardan datos que se mostrarán en el resumen final del trámite
        $solicitante->setCuit($contribuyente['persona']->getCuit());
        $solicitante->setDireccion($contribuyente['persona']->getDireccion());
        if($persona === 'Fisica') {
            $solicitante->setApellido($contribuyente['persona']->getApellido());
        } else {
            $solicitante->setApellido($contribuyente['persona']->getRazonSocial());
        }

        Session::put('FORMULARIO', $formulario);
        Session::put('MULTINOTA', $multinota);
        Session::put('SOLICITANTE', $solicitante);
        Session::put('PERSONA', $persona);

        // Se eliminan variables en sesión que deben estar nulas al inicio de un tramite
        session()->forget([
            'REPRESENTANTE', 
            'CODIGOS_AREA', 
            'CARACTERES', 
            'CAMPOS_SECCIONES',
            'INFORMACION_ADICIONAL',
            'ARCHIVOS',
        ]);

        // Se insertan objetos/arrays vacíos que posteriormente guardarán datos de las distintas etapas
        $archivos = [];

        return view('multinota-interno', [
            'formulario' => $formulario,
            'getOrdenActual' => $formulario->getOrdenActual(),
            'persona' => $persona,
            'multinota' => $multinota,
            'solicitante' => $solicitante,
            'archivos' => $archivos
        ]);
    }
    
    public function avanzarPaso() {
        $formulario = Session::get('FORMULARIO');
        $multinota = Session::get('MULTINOTA');
        $solicitante = Session::get('SOLICITANTE');
        $representante = Session::get('REPRESENTANTE');
        $codigosArea = Session::get('CODIGOS_AREA');
        $caracteres = Session::get('CARACTERES');
        $camposSecciones = Session::get('CAMPOS_SECCIONES');
        $informacionAdicional = Session::get('INFORMACION_ADICIONAL');
        $archivos = Session::get('ARCHIVOS');
        $persona = Session::get('PERSONA');
        
        foreach ($formulario->pasosFormulario as &$paso) {
            if ($paso['completado'] === false) {
                $paso['completado'] = true;
                break;
            }
        }

        unset($paso);
        Session::put('FORMULARIO', $formulario);
        $htmlPasos = view('partials.pasos-container', compact('formulario'))->render();
    
        $htmlRuta = view('partials.ruta-paso-tramite', compact(
            'formulario',
            'representante',
            'codigosArea',
            'caracteres',
            'camposSecciones',
            'informacionAdicional',
            'archivos',
            'solicitante',
            'persona'))->render();

        $htmlBotones = view('partials.botones-avance-tramite', compact('formulario'))->render();

        return response()->json([
            'htmlPasos' => $htmlPasos,
            'htmlRuta' => $htmlRuta,
            'htmlBotones' => $htmlBotones
        ]);
    }

    public function retrocederPaso() {
        $formulario = Session::get('FORMULARIO');
        $multinota = Session::get('MULTINOTA');
        $solicitante = Session::get('SOLICITANTE');
        $representante = Session::get('REPRESENTANTE');
        $codigosArea = Session::get('CODIGOS_AREA');
        $caracteres = Session::get('CARACTERES');
        $camposSecciones = Session::get('CAMPOS_SECCIONES');
        $informacionAdicional = Session::get('INFORMACION_ADICIONAL');
        $archivos = Session::get('ARCHIVOS');
        $persona = Session::get('PERSONA');
        
        foreach ($formulario->pasosFormulario as $i => &$paso) {
            if ($paso['completado'] === false) {
                $formulario->pasosFormulario[$i-1]['completado'] = false;
                break;
            }
        }

        unset($paso);
        Session::put('FORMULARIO', $formulario);
        $htmlPasos = view('partials.pasos-container', compact('formulario'))->render();
        
        $htmlRuta = view('partials.ruta-paso-tramite', compact(
            'formulario',
            'representante',
            'codigosArea',
            'caracteres',
            'camposSecciones',
            'informacionAdicional',
            'archivos',
            'solicitante',
            'persona'))->render();
        

        $htmlBotones = view('partials.botones-avance-tramite', compact('formulario'))->render();

        return response()->json([
            'htmlPasos' => $htmlPasos,
            'htmlRuta' => $htmlRuta,
            'htmlBotones' => $htmlBotones
        ]);
    }

    // Funciones específicas a las distintas etapas de instanciación de un trámite

    // Etapa "Datos del Solicitante"
    public function guardarDatosDelSolicitante(Request $request) {
        $solicitante = Session::get('SOLICITANTE');
        $solicitante->setCuenta($request->post('cuenta'));
        $solicitante->setCorreo($request->post('correo'));
        Session::put('SOLICITANTE', $solicitante);
    }

    // Etapa "Datos del Representante" (solo personas jurídicas)
    public function buscarContribuyente($cuit) {
        $cuit = str_replace('-', '', $cuit);

        // Validar CUIT

        // Buscar Representante (sin máscara)
        $solicitante = Solicitante::where('documento', $cuit)
        ->orderByRaw('1 desc')
        ->first();

        // Obtener todos los códigos de área y todos los caractéres
        $codigosArea = CodigoArea::select('codigo')
        ->distinct()
        ->orderBy('codigo', 'asc')
        ->get();

        $caracteres = array_map(
            fn($case) => $case->descripcion(),
            TipoCaracterEnum::cases()
        );

        if(!is_null($solicitante)) {
            // Buscar cuenta caracter del representante
            $cuentaCaracter = SolicitanteCuentaCaracter::where('id_solicitante', $solicitante->id_solicitante)
            ->orderBy('id_solicitante_cuenta_caracter', 'desc')
            ->first();

            // Obtener tipo caracter
            $caracterEnum = TipoCaracterEnum::from($cuentaCaracter->r_caracter);

            // Instanciar RepresentanteDTO
            $tipoCaracterDTO = new TipoCaracterDTO($caracterEnum->value, $caracterEnum->name);
            $documentoDTO = new DocumentoDTO(numero: $solicitante->documento);
            $domicilio = Direccion::where('id_direccion', $solicitante->id_direccion)->first();

            $domicilioDTO = new DomicilioDTO(
                $domicilio->calle, 
                $domicilio->numero,
                $domicilio->localidad,
                $domicilio->provincia,
                $domicilio->codigo_postal,
                $domicilio->pais,
                $domicilio->latitud ?? '',
                $domicilio->longitud ?? '',
                $domicilio->piso ?? '',
                $domicilio->departamento ?? '');

            $representante = new RepresentanteDTO(
                $tipoCaracterDTO,
                $solicitante->nombre,
                $solicitante->apellido,
                $documentoDTO,
                $solicitante->telefono,
                $solicitante->correo,
                '',
                $domicilioDTO,
                true
            );

            $codigoArea = $representante->getAreaTelefono();
            $codigoAreaObject = CodigoArea::where('codigo', $codigoArea)->first();
            $codigoAreaDTO = new CodigoAreaDTO(
                $codigoAreaObject->id_codigo_area,
                $codigoAreaObject->provincia,
                $codigoAreaObject->localidad,
                $codigoAreaObject->codigo);

            $telefonoSinMascara = $representante->getTelefonoSinMascara();

            // Asignar código de área y teléfono sin máscara a RepresentanteDTO
            $representante->setCodigoArea($codigoAreaDTO);
            $representante->setTelefono($telefonoSinMascara);

            // Se setean objetos representante, códigos de área y caractéres en sesión
            Session::put('REPRESENTANTE', $representante);
            Session::put('CODIGOS_AREA', $codigosArea);
            Session::put('CARACTERES', $caracteres);

            $htmlVista = view('partials.etapas-tramite.solicitante', compact('representante', 'codigosArea', 'caracteres'))->render();

            return response()->json([
                'mensaje' => null,
                'htmlVista' => $htmlVista,
            ]);
        } else {
            // Instanciar RepresentanteDTO sin el caracter
            $reflection = new \ReflectionClass(RepresentanteDTO::class);
            $representante = $reflection->newInstanceWithoutConstructor();
            $representante->setTipoCaracter(new TipoCaracterDTO(0, ''));
            $representante->setNombre('');
            $representante->setApellido('');
            $representante->setDocumento(new DocumentoDTO(numero: $cuit));
            $representante->setCodigoArea(new CodigoAreaDTO(0, '', '', ''));
            $representante->setTelefono('');
            $representante->setCorreo('');
            $representante->setCorreoRepetido('');
            $representante->setDomicilio(new DomicilioDTO(
                '', '', '', '', '', '', '', '', '', ''
            ));
            $representante->setEsCuitRegistrado(true);

            // Se setean objetos representante, códigos de área y caractéres en sesión
            Session::put('REPRESENTANTE', $representante);
            Session::put('CODIGOS_AREA', $codigosArea);
            Session::put('CARACTERES', $caracteres);

            $htmlVista = view('partials.etapas-tramite.solicitante', compact('representante', 'codigosArea', 'caracteres'))->render();

            // Mostrar toast alert de "No se encontraron resultados"
            return response()->json([
                'mensaje' => 'No se encontraron resultados.',
                'htmlVista' => $htmlVista
            ]);
        }
            
        // Setear codigo de area
        // Setear telefono
        // Setear direccion
        // Cargar geolocalizacion
        // Setear esCuitRegistrado = true
    }

    public function guardarDatosSeccionSolicitante(Request $request) {
        $data = $request->all();

        // Se obtiene ID del tipo caracter
        $caracterEnum = TipoCaracterEnum::fromDescripcion($data['tipoCaracter']);
        $data['tipoCaracterID'] = $caracterEnum->value;

        // Se obtienen datos del código de área
        $codigoAreaObject = CodigoArea::where('codigo', $data['codArea'])->first();
        $codigoAreaDTO = new CodigoAreaDTO(
        $codigoAreaObject->id_codigo_area,
        $codigoAreaObject->provincia,
        $codigoAreaObject->localidad,
        $codigoAreaObject->codigo);
        
        $representanteNew = RepresentanteDTO::fromRequest($data);

        // Se setea código de área
        $representanteNew->setCodigoArea($codigoAreaDTO);

        Session::put('REPRESENTANTE', $representanteNew);
    }

    public function guardarDatosSeccionDatosACompletar(Request $request) {
        $data = $request->all();

        Session::put('CAMPOS_SECCIONES', $data);
    }

    public function guardarDatosSeccionInformacionAdicional(Request $request) {
        $data = $request->all();

        Session::put('INFORMACION_ADICIONAL', $data['informacionAdicional']);
    }

    public function guardarDatosSeccionAdjuntarDocumentacion(Request $request) {
        $data = $request->all();

        Session::put('ARCHIVOS', $data['archivos']);
    }
}
