<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutorizacionController;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\EstadoTramiteController;
use App\Http\Controllers\InstanciaMultinotaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LicenciaController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\LimiteController;
use App\Http\Controllers\ContribuyenteMultinotaController;
use App\Http\Controllers\CuestionarioController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ConfiguracionMailController;
use App\Http\Controllers\IngresoExternoController;
use App\Http\Controllers\AdministracionWorkflowController;
use App\Http\Controllers\BandejaPersonalController;
use App\Http\Controllers\SeccionesMultinotaController;
use App\Http\Controllers\MultinotaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Login
Route::get('/success', function (Request $request) {
    $code = $request->code;

    $response = Http::asForm()->post(env('SERVICIO_TOKEN_CVSA'), [
        'grant_type' => 'authorization_code',
        'client_id' => env('OAUTH_ID'),
        'client_secret' => env('OAUTH_SECRET'),
        'redirect_uri' => env('URL_CALLBACK_CVSA'),
        'code' => $request->code,
    ]);

    $res = $response->json();

    Session::put('TOKEN_TYPE', $res['token_type']);
    Session::put('ACCESS_TOKEN', $res['access_token']);
    Session::put('REFRESH_TOKEN', $res['refresh_token']);    
    Session::put('EXPIRES_IN', $res['expires_in']);

    return redirect('/ingreso');
});

Route::get('/', function () {
    return redirect(env('URL_REDIRECT_INICIO'));
});

//Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

//Reportes
Route::get('/reporte/constancia/{idTramite}', [ReporteController::class, 'generarPDF'])->name('reporte.constancia');
Route::get('/reporte/constancia/modal/{idTramite}', function ($idTramite) {
    return view('reportes.modal', compact('idTramite'));
})->name('reporte.constancia.modal');

//Archivos
Route::post('/archivo/subir', [ArchivoController::class, 'subirArchivo'])->name('archivo.subir');
Route::post('/archivo/subirTemporal', [ArchivoController::class, 'subirArchivoTemporal'])->name('archivo.subirTemporal');
Route::post('/archivo/cargarComentario', [ArchivoController::class, 'cargarComentario'])->name('archivo.cargarComentario');
Route::post('/archivo/eliminarTemporal', [ArchivoController::class, 'eliminarArchivoTemporal'])->name('archivo.eliminarTemporal');
Route::get('/archivo/descargar/{id}', [ArchivoController::class, 'descargar'])->name('archivo.descargar');

//Comtentarios
Route::post('/comentario/guardar', [ComentarioController::class, 'guardarComentario'])->name('comentario.guardar');

//Trámites
Route::get('/tramites/{id}/detalle', [TramiteController::class, 'show'])->name('tramites.detalle');
Route::get('/tramites', [TramiteController::class, 'index'])->name('tramites.index');
Route::get('/tramites/data', [TramiteController::class, 'getTramitesData'])->name('tramites.data');
Route::get('/tramites/en-curso', [TramiteController::class, 'enCurso'])->name('tramites.enCurso');
Route::post('/tramites/tomarTramite', [TramiteController::class, 'tomarTramite'])->name('tramites.tomarTramite');
Route::post('/tramites/cambiar-prioridad', [TramiteController::class, 'cambiarPrioridad'])->name('tramites.cambiarPrioridad');
Route::post('/tramites/darDeBaja', [TramiteController::class, 'darDeBaja'])->name('tramites.darDeBaja');
Route::post('/tramites/avanzarEstado', [TramiteController::class, 'avanzarEstado'])->name('tramites.avanzarEstado');
Route::post('/tramites/posibles-estados', [TramiteController::class, 'getPosiblesEstados'])->name('tramites.getPosiblesEstados');
Route::post('/tramites/guardar-cuestionario', [TramiteController::class, 'guardarCuestionario'])->name('cuestionarios.guardar');
Route::post('/tramites/pedir-documentacion', [TramiteController::class, 'pedirDocumentacion'])->name('tramites.pedirDocumentacion');

//Estado Trámite
Route::get('/estadoTramite/tienePermiso/{multinota}', [EstadoTramiteController::class, 'tienePermiso'])->name('estadoTramite.tienePermiso');

// Instancia Tramite
Route::get('/instanciaTramite/buscar', [InstanciaMultinotaController::class, 'buscar'])->name('instanciaTramite.buscar');
Route::get('/instanciaTramite/avanzarPaso', [InstanciaMultinotaController::class, 'avanzarPaso'])->name('instanciaTramite.avanzarPaso');
Route::get('/instanciaTramite/retrocederPaso', [InstanciaMultinotaController::class, 'retrocederPaso'])->name('instanciaTramite.retrocederPaso');
/* Etapa: Datos del Solicitante */
Route::post('/instanciaTramite/guardarDatosDelSolicitante', [InstanciaMultinotaController::class, 'guardarDatosDelSolicitante'])->name('instanciaTramite.guardarDatosDelSolicitante');
Route::get('/instanciaTramite/session-data', function () {
    return response()->json([
        'cuenta' => session('SOLICITANTE')->cuenta,
        'correo' => session('SOLICITANTE')->correo,
    ]);
});
/* Etapa: Datos del Representante */
Route::get('/instanciaTramite/buscarContribuyente/{cuit}', [InstanciaMultinotaController::class, 'buscarContribuyente'])->name('instanciaTramite.buscarContribuyente');
Route::post('/instanciaTramite/guardarDatosSeccionSolicitante', [InstanciaMultinotaController::class, 'guardarDatosSeccionSolicitante'])->name('instanciaTramite.guardarDatosSeccionSolicitante');

/* Etapa: Datos a Completar */
Route::post('/instanciaTramite/guardarDatosSeccionDatosACompletar', [InstanciaMultinotaController::class, 'guardarDatosSeccionDatosACompletar'])->name('instanciaTramite.guardarDatosSeccionDatosACompletar');

/* Etapa: Información Adicional */
Route::post('/instanciaTramite/guardarDatosSeccionInformacionAdicional', [InstanciaMultinotaController::class, 'guardarDatosSeccionInformacionAdicional'])->name('instanciaTramite.guardarDatosSeccionInformacionAdicional');

/* Etapa: Resumen */
Route::post('/instanciaTramite/handleCheckConfirmarDeclaracion', [InstanciaMultinotaController::class, 'handleCheckConfirmarDeclaracion'])->name('instanciaTramite.handleCheckConfirmarDeclaracion');
Route::get('/instanciaTramite/registrarTramite', [InstanciaMultinotaController::class, 'registrarTramite'])->name('instanciaTramite.registrarTramite');

//Categorías
Route::get('/perfil', [UsuarioController::class, 'perfil'])->name('perfil');
Route::post('/perfil/actualizar', [UsuarioController::class, 'actualizarPerfil'])->name('perfil.actualizar');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
Route::put('/categorias/{id}/desactivar', [CategoriaController::class, 'desactivar'])->name('categorias.desactivar');
Route::get('/categorias/{id}/edit', [CategoriaController::class, 'edit'])->name('categorias.edit');
Route::put('/categorias/{id}', [CategoriaController::class, 'update'])->name('categorias.update');
Route::get('categorias/create', [CategoriaController::class, 'create'])->name('categorias.create');
Route::post('categorias', [CategoriaController::class, 'store'])->name('categorias.store');

//Secciones Multinota
Route::get('/secciones-multinota', [SeccionesMultinotaController::class, 'index'])->name('secciones-multinota.index');
Route::get('/secciones-multinota/{id}/select', [SeccionesMultinotaController::class, 'selectCampo'])->name('secciones-multinota.selectCampo');
Route::get('/secciones-multinota/nuevoCampo', [SeccionesMultinotaController::class, 'nuevoCampo'])->name('secciones-multinota.nuevoCampo');
Route::post('/secciones-multinota/actualizarDatosCampo', [SeccionesMultinotaController::class, 'actualizarDatosCampo'])->name('secciones-multinota.actualizarDatosCampo');
Route::get('/secciones-multinota/{id}/deleteCampo', [SeccionesMultinotaController::class, 'deleteCampo'])->name('secciones-multinota.deleteCampo');
Route::get('/secciones-multinota/setearNuevoOrdenCampos/{array}', [SeccionesMultinotaController::class, 'setearNuevoOrdenCampos'])->name('secciones-multinota.setearNuevoOrdenCampos');
Route::get('/secciones-multinota/setearNuevoOrdenOpcionesCampo/{array}', [SeccionesMultinotaController::class, 'setearNuevoOrdenOpcionesCampo'])->name('secciones-multinota.setearNuevoOrdenOpcionesCampo');
Route::get('/secciones-multinota/addOpcionCampo/{nueva_opcion}', [SeccionesMultinotaController::class, 'addOpcionCampo'])->name('secciones-multinota.addOpcionCampo');
Route::get('/secciones-multinota/getOpcionesCampo', [SeccionesMultinotaController::class, 'getOpcionesCampo'])->name('secciones-multinota.getOpcionesCampo');
Route::get('/secciones-multinota/getOpcionesCampoAlfabeticamente', [SeccionesMultinotaController::class, 'getOpcionesCampoAlfabeticamente'])->name('secciones-multinota.getOpcionesCampoAlfabeticamente');
Route::get('/secciones-multinota/getOpcionesFormTipoCampo/{tipo}', [SeccionesMultinotaController::class, 'getOpcionesFormTipoCampo'])->name('secciones-multinota.getOpcionesFormTipoCampo');
Route::get('/secciones-multinota/deleteOpcionCampo/{id}', [SeccionesMultinotaController::class, 'deleteOpcionCampo'])->name('secciones-multinota.deleteOpcionCampo');
Route::get('/secciones-multinota/{id}/edit', [SeccionesMultinotaController::class, 'edit'])->name('secciones-multinota.edit');
Route::get('/secciones-multinota/crearNuevaSeccion', [SeccionesMultinotaController::class, 'crearNuevaSeccion'])->name('secciones-multinota.crearNuevaSeccion');
Route::post('/secciones-multinota/editarSeccion', [SeccionesMultinotaController::class, 'editarSeccion'])->name('secciones-multinota.editarSeccion');
Route::put('/secciones-multinota/{id}/desactivar', [SeccionesMultinotaController::class, 'desactivarSeccion'])->name('secciones-multinota.desactivarSeccion');

//Multinotas
Route::get('/multinotas', [MultinotaController::class, 'index'])->name('multinotas.index');
Route::get('/multinotas/{id}/view', [MultinotaController::class, 'view'])->name('multinotas.view');
Route::get('/multinotas/{id}/edit', [MultinotaController::class, 'edit'])->name('multinotas.edit');
Route::get('/multinotas/refresh', [MultinotaController::class, 'refresh'])->name('multinotas.refresh');
Route::get('/multinotas/recargarSubcategorias/{id}', [MultinotaController::class, 'recargarSubcategorias'])->name('multinotas.recargarSubcategorias');
Route::post('/multinotas/agregarSeccion', [MultinotaController::class, 'agregarSeccion'])->name('multinotas.agregarSeccion');
Route::post('/multinotas/quitarSeccion', [MultinotaController::class, 'quitarSeccion'])->name('multinotas.quitarSeccion');
Route::get('/multinotas/setearNuevoOrdenSeccion/{array}', [MultinotaController::class, 'setearNuevoOrdenSeccion'])->name('multinotas.setearNuevoOrdenSeccion');
Route::post('/multinotas/previsualizarCambiosMultinota', [MultinotaController::class, 'previsualizarCambiosMultinota'])->name('multinotas.previsualizarCambiosMultinota');
Route::get('/multinotas/guardarMultinota/{id}', [MultinotaController::class, 'guardarMultinota'])->name('multinotas.guardarMultinota');
Route::put('/multinotas/{id}/desactivar', [MultinotaController::class, 'desactivarMultinota'])->name('multinotas.desactivarMultinota');
Route::get('/multinotas/crearNuevaMultinota', [MultinotaController::class, 'crearNuevaMultinota'])->name('multinotas.crearNuevaMultinota');

//Usuarios
Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
Route::get('usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
Route::post('usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
Route::get('/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
Route::get('/usuarios/{id}/licencias', [LicenciaController::class, 'crearLicencia'])->name('licencias.crear');
Route::post('/usuarios/{id}/licencias', [LicenciaController::class, 'guardarLicencia'])->name('licencias.store');
Route::get('usuario', [ContribuyenteMultinotaController::class, 'index'])->name('contribuyente.index');
Route::post('/usuario', [ContribuyenteMultinotaController::class, 'buscar'])->name('contribuyente.buscar');
Route::put('/usuario/{id}/actualizar-correo', [ContribuyenteMultinotaController::class, 'actualizarCorreo'])->name('contribuyente.actualizarCorreo');
Route::post('/usuario/{id}/restablecer-clave', [ContribuyenteMultinotaController::class, 'restablecerClave'])->name('contribuyente.restablecerClave');

//Roles
Route::get('/roles/{id_rol}/permisos', [UsuarioController::class, 'obtenerPermisosPorRol'])->name('roles.permisos');
Route::get('roles', [RolController::class, 'index'])->name('roles.index');
Route::get('roles/create', [RolController::class, 'create'])->name('roles.create');
Route::post('roles', [RolController::class, 'store'])->name('roles.store');
Route::get('/roles/{id}/edit', [RolController::class, 'edit'])->name('roles.edit');
Route::put('/roles/{id}', [RolController::class, 'update'])->name('roles.update');

//Limites
Route::get('limite', [LimiteController::class, 'index'])->name('limites.index');
Route::post('/guardar-limite', [LimiteController::class, 'guardarLimite'])->name('guardar.limite');

//Cuestionarios
Route::get('cuestionarios', [CuestionarioController::class, 'index'])->name('cuestionarios.index');
Route::get('/cuestionarios/crear', [CuestionarioController::class, 'create'])->name('cuestionarios.create');
Route::post('/cuestionarios', [CuestionarioController::class, 'store'])->name('cuestionarios.store');
Route::get('/cuestionarios/{id}/editar', [CuestionarioController::class, 'edit'])->name('cuestionarios.edit');
Route::put('/cuestionarios/{id}', [CuestionarioController::class, 'update'])->name('cuestionarios.update');
Route::put('/cuestionarios/{id}/activar', [CuestionarioController::class, 'activar'])->name('cuestionarios.activar');
Route::put('/cuestionarios/{id}/desactivar', [CuestionarioController::class, 'desactivar'])->name('cuestionarios.desactivar');

//Mail
Route::get('/sistema', [ConfiguracionMailController::class, 'edit'])->name('configuracion.edit');
Route::post('/sistema', [ConfiguracionMailController::class, 'update'])->name('configuracion.update');

//Ingreso Externo
Route::get('/ingreso-externo', [IngresoExternoController::class, 'showLoginForm'])->name('ingreso-externo');
Route::post('/ingreso-externo', [IngresoExternoController::class, 'login'])->name('ingreso-externo.login');

//Contribuyente Multinota
Route::get('/ingreso-externo/registro', [IngresoExternoController::class, 'registro'])->name('ingreso-externo.registro');
Route::post('/ingreso-externo/registrar', [IngresoExternoController::class, 'registrar'])->name('registro-externo.registrar');
Route::get('/bandeja-usuario-externo', [IngresoExternoController::class, 'bandejaExterna'])->name('bandeja-usuario-externo');
Route::get('/cambiar-clave', [ContribuyenteMultinotaController::class, 'showChangePasswordForm'])->name('cambiar-clave');
Route::post('/cambiar-clave', [ContribuyenteMultinotaController::class, 'changePassword'])->name('cambiar-clave.submit');
Route::get('/externo/{id}/detalle', [TramiteController::class, 'detalleExterno'])->name('externo.detalle');

//Administración Workflow
Route::get('/perfil-externo', [ContribuyenteMultinotaController::class, 'perfil'])->name('perfil-externo');
Route::post('/perfil-externo', [ContribuyenteMultinotaController::class, 'actualizarPerfil'])->name('perfil-externo.actualizarPerfil');
Route::get('/estados', [AdministracionWorkflowController::class, 'index'])->name('estados.index');
Route::get('/workflow/{id}', [AdministracionWorkflowController::class, 'crear'])->name('workflow.crear');
Route::post('/workflow/guardar/{id}', [AdministracionWorkflowController::class, 'guardar'])->name('workflow.guardar');
Route::get('/workflow/editar/{id}', [AdministracionWorkflowController::class, 'editar'])->name('workflow.editar');
Route::get('/workflow/borrador/{id}', [AdministracionWorkflowController::class, 'borrador'])->name('workflow.borrador');
Route::post('/workflow/editar/guardarEdicion/{id}', [AdministracionWorkflowController::class, 'guardarEdicion'])->name('workflow.guardarEdicion');
Route::post('/workflow/editar/guardarBorrador/{id}', [AdministracionWorkflowController::class, 'guardarBorrador'])->name('workflow.guardarBorrador');
Route::post('/workflow/editar/publicarBorrador/{id}', [AdministracionWorkflowController::class, 'publicarBorrador'])->name('workflow.publicarBorrador');

//Navbar
//Route::view('/navbar', [NavbarController::class, 'cargarElementos'])->name('navbar');

//Bandeja Personal
Route::get('/bandeja-personal', [BandejaPersonalController::class, 'index'])->name('bandeja-personal.index');

// Otros
Route::get('/set-usuario-interno', [UsuarioController::class, 'setUsuarioInterno']);
//Route::get('/clear-session', [UsuarioController::class, 'clearSession']); // Para limpiar la sesión
Route::get('/clear-session', function() {
    session()->forget('contribuyente_multinota');
    return redirect()->route('ingreso-externo');
});