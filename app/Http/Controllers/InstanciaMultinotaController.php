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
use App\Models\Multinota;
use App\Models\TipoDocumento;
use App\Models\Direccion;
use App\Models\CodigoArea;
use App\Models\Archivo;
use App\Models\TramiteArchivo;
use App\Models\ConfiguracionEstadoTramite;
use App\Models\EstadoTramite;
use App\Models\EstadoTramiteAsignable;
use App\Models\UsuarioInterno;
use App\Models\GrupoInterno;
use App\DTOs\UsuarioInternoDTO;
use App\DTOs\GrupoInternoDTO;
use App\DTOs\EstadoTramiteDTO;
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
use App\Builders\EstadoBuilder;
use App\Enums\TipoCaracterEnum;
use App\Enums\TipoEstadoEnum;
use App\Transformers\PersonaFisicaTransformer;
use App\Transformers\PersonaJuridicaTransformer;
use App\Http\Controllers\ArchivoController;
use App\Interfaces\AsignableATramite;
use App\Factories\FactoryEstados;
use App\Factories\FactoryEstadoBuilder;
use App\Helpers\EstadoHelper;

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
                
                // Se guarda ContribuyenteDTO
                Session::put('CONTRIBUYENTE', $contribuyente);

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

                // Se guarda ContribuyenteDTO
                Session::put('CONTRIBUYENTE', $contribuyente);

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

        Session::put('MENSAJE_INICIAL', $mensajeInicial);

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
            'archivos' => $archivos,
            'confirmarDeclaracionChecked' => false
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
        $confirmarDeclaracionChecked = false;
        
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

        $htmlBotones = view('partials.botones-avance-tramite', compact('formulario', 'confirmarDeclaracionChecked'))->render();

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
        $confirmarDeclaracionChecked = false;
        
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
        

        $htmlBotones = view('partials.botones-avance-tramite', compact('formulario', 'confirmarDeclaracionChecked'))->render();

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
            $cuentaCaracter = Multinota::where('id_solicitante', $solicitante->id_solicitante)
            ->orderBy('id_tramite', 'desc')
            ->first();

            // Obtener tipo caracter
            if (!$cuentaCaracter) {
                $cuentaCaracter = new \stdClass();
            }
            $cuentaCaracter->r_caracter = 33;
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

    public function handleCheckConfirmarDeclaracion(Request $request) {
        $formulario = Session::get('FORMULARIO');

        $data = $request->all();
        $confirmarDeclaracionChecked = false;

        if($data['checked']) {
            $confirmarDeclaracionChecked = true;
        }

        $htmlVista = view('partials.botones-avance-tramite', compact('formulario', 'confirmarDeclaracionChecked'))->render();

        return response()->json([
            'htmlVista' => $htmlVista,
        ]);
    }

    public function cargarEstados($idTipoTramiteMultinota) {
        // Step 1: Get EstadoBuilder collection/array
        $estadoBuilders = $this->obtenerEstados($idTipoTramiteMultinota);

        // Step 2: Build EstadoTramite objects from EstadoBuilders
        $factory = new FactoryEstados();
        $estadosAux = $factory->construirEstados($estadoBuilders);

        // Step 3: Collect initial states linked to those "EN_CREACION"
        $estadosIniciales = collect();

        foreach ($estadosAux as $estado) {
            // Assuming getTipoEstado() returns string, compare with your constant
            if ($estado->getTipoEstado() === TipoEstadoEnum::EN_CREACION) {
                foreach ($estado->getEstadosPosteriores() as $estadoPosterior) {
                    // Use Laravel Collection's contains with a callback to compare IDs
                    if (!$estadosIniciales->contains(function ($e) use ($estadoPosterior) {
                        return $e->getId() === $estadoPosterior->getId();
                    })) {
                        $estadosIniciales->push($estadoPosterior);
                    }
                }
            }
        }

        return $estadosIniciales;
    }

    public function obtenerEstados($idTipoTramiteMultinota) {
        // Se obtienen estados asociados a la multinota
        $estadosTramite = EstadoTramite::select('estado_tramite.*')
        ->join('configuracion_estado_tramite', 'estado_tramite.id_estado_tramite', '=', 'configuracion_estado_tramite.id_estado_tramite')
        ->where('configuracion_estado_tramite.id_tipo_tramite_multinota', $idTipoTramiteMultinota)
        ->where('configuracion_estado_tramite.activo', 1)
        ->where('configuracion_estado_tramite.publico', 1)
        ->groupBy([
            'estado_tramite.id_estado_tramite',
            'estado_tramite.fecha_sistema',
            'estado_tramite.nombre',
            'estado_tramite.tipo',
            'estado_tramite.puede_rechazar',
            'estado_tramite.puede_pedir_documentacion'
        ])
        ->get();

        // Se guardan estados en un array plano
        $estadoTramiteDTOS = $estadosTramite->map(function ($et) {
            return new EstadoTramiteDTO(
                $et->id_estado_tramite,
                $et->nombre,
                TipoEstadoEnum::fromName($et->tipo),
                $et->puede_rechazar,
                $et->puede_pedir_documentacion,
                $et->puede_elegir_camino,
                $et->estado_tiene_expediente,
                collect(),
                collect(),
                collect(),
                collect(),
                null
            );
        })->all();

        $estadosBuilder = collect();

        foreach($estadoTramiteDTOS as $etdto) {
            $asignablesDB = EstadoTramiteAsignable::where('id_estado_tramite', $etdto->getId())->get();
            $asignables = collect();
            
            foreach ($asignablesDB as $a) {
                if($a->id_usuario_interno != null) {
                    $modelo = UsuarioInterno::with([
                        'rol.permisos',
                        'categoria',
                        'grupoInterno.oficina',
                    ])->find($a->id_usuario_interno);

                    if ($modelo) {
                        $usuario = UsuarioInternoDTO::desdeModelo($modelo);
                        $asignables->push($usuario);
                    }
                } else {
                    $modelo = GrupoInterno::with('usuarios')->find($a->id_grupo_interno);

                    if ($modelo) {
                        $grupo = GrupoInternoDTO::desdeModelo($modelo);
                        $asignables->push($grupo);
                    }
                }
            }

            // Construir EstadoBuilder
            $builder = new EstadoBuilder($etdto->getNombre());
            $builder->id = $etdto->getId();
            $builder->tipoEstado = $etdto->getTipoEstado();
            $builder->puedePedirDocumentacion = $etdto->getPuedePedirDocumentacion() === 1;
            $builder->tieneExpediente = $etdto->getTieneExpediente() === 1;
            $builder->puedeRechazar = $etdto->getPuedeRechazar() === 1;
            $builder->puedeElegirCamino = $etdto->getPuedeElegirCamino() === 1;

            // Initialize empty collections
            $builder->estadosAnteriores = collect();
            $builder->estadosPosteriores = collect();
            $builder->nodosAnteriores = collect(); // if needed

            // Agregar asignables
            foreach ($asignables as $asignable) {
                $builder->agregarAsignable($asignable);
            }

            // Agregar validadores según tipo de estado
            $validadores = FactoryEstadoBuilder::obtenerValidadoresPorTipo($etdto->getTipoEstado());
            foreach ($validadores as $validador) {
                $builder->agregarValidador($validador);
            }

            // Guardar en colección final
            $estadosBuilder->push($builder);
        }

        foreach ($estadosBuilder as $eb) {
            $configs = ConfiguracionEstadoTramite::where('id_estado_tramite', $eb->id)
                ->where('activo', 1)
                ->where('publico', 1)
                ->get();

            foreach($estadoTramiteDTOS as $etdto) {
                if ($eb->id === $etdto->getId()) {
                    foreach($configs as $c) {
                        $aAgregar = EstadoHelper::buscarEstado($c, $estadosBuilder);

                        if ($aAgregar !== null) {
                            $eb->agregarEstadoPosterior($aAgregar);
                            $aAgregar->agregarEstadoAnterior($eb);
                        }
                    }
                }
            }
        } 
        return $estadosBuilder;
    }

    public function registrarTramite() {
        try {
            /* registrarTramite(t, t2);
            t.registrarParticularidades(t2);

            registrarInicio(t, t2);

            new Thread(new NotificadorRegistro(t)).start(); */

            // Se cargan datos
            $idContribuyenteMultinota = Session::get('CONTRIBUYENTE')->getIdContribuyenteMultinota();
            $idUsuarioInterno = null; // TO-DO
            $representante = Session::get('REPRESENTANTE');
            $solicitante = Session::get('SOLICITANTE');
            $informacionAdicional = Session::get('INFORMACION_ADICIONAL');
            $idTipoTramiteMultinota = Session::get('MULTINOTA')->id_tipo_tramite_multinota;
            $idMensajeInicial = Session::get('MENSAJE_INICIAL')->id_mensaje_inicial;
            $archivos = Session::get('ARCHIVOS');

            // Cargar estados
            $this->cargarEstados($idTipoTramiteMultinota);

            /* for( EstadoTramite estadoTramite : t.getEstadosActuales() )
                estadoTramite.setUsuarioAsignado( new AsignableATramiteController().recomendado(estadoTramite) ); */

            // Se inserta dirección del representante
            $direccion = Direccion::create([
                'calle' => $representante->getDomicilio()->getCalle(),
                'numero' => $representante->getDomicilio()->getNumero(),
                'codigo_postal' => $representante->getDomicilio()->getCodigoPostal(),
                'provincia' => $representante->getDomicilio()->getProvincia(),
                'localidad' => $representante->getDomicilio()->getLocalidad(),
                'pais' => $representante->getDomicilio()->getPais(),
                'latitud' => $representante->getDomicilio()->getLatitud(),
                'longitud' => $representante->getDomicilio()->getLongitud(),
                'piso' => $representante->getDomicilio()->getPiso(),
                'departamento' => $representante->getDomicilio()->getDepartamento(),
            ]);

            $id_direccion = $direccion->id_direccion;

            // Se obtiene el ID de tipo_documento
            $id_tipo_documento = TipoDocumento::where('nombre', $representante->getDocumento()->getTipo())->value('id_tipo_documento');

            // Se inserta representante
            $solicitanteDB = Solicitante::create([
                'nombre' => $representante->getNombre(),
                'documento' => $representante->getDocumento()->getNumero(),
                'telefono' => $representante->getTelefono(),
                'correo' => $representante->getCorreo(),
                'id_tipo_documento' => $id_tipo_documento,
                'apellido' => $representante->getApellido(),
                'id_direccion' => $id_direccion
            ]);

            // Se inserta trámite
            $multinota = Multinota::create([
                'cuenta' => $solicitante->cuenta,
                'id_tipo_tramite_multinota' => $idTipoTramiteMultinota,
                'id_mensaje_inicial' => $idMensajeInicial,
                'id_contribuyente_multinota' => $idContribuyenteMultinota ?? $idContribuyenteMultinota,
                'informacion_adicional' => $informacionAdicional,
                'id_prioridad' => 1,
                'id_solicitante' => $solicitanteDB->id_solicitante,
                'id_usuario_interno' => $idUsuarioInterno ?? $idUsuarioInterno,
                'r_caracter' => $representante->getTipoCaracter()->getCodigo(),
                'correo' => $solicitante->correo,
                'cuit_contribuyente' => $solicitante->cuit
            ]);

            // Se guardan los archivos en el directorio final
            (new ArchivoController)->moverArchivo($archivos, $multinota->id_tramite);

            // Se recuperan archivos con path actualizado
            $archivos = Session::get('ARCHIVOS');

            // Se insertan archivos asociados al trámite
            foreach ($archivos as $a) {
                $archivo = Archivo::create([
                    'nombre' => $a['nombre'],
                    'tipo_contenido' => $a['tipoContenido'],
                    'path_archivo' => $a['pathArchivo'],
                    'descripcion' => $a['comentario']
                ]);

                TramiteArchivo::create([
                    'id_archivo' => $archivo->id_archivo,
                    'id_tramite' => $multinota->id_tramite
                ]);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al registrar el tramite: ' . $e->getMessage());
        }
    }
}
