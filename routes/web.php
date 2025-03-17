<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutorizacionController;
use App\Http\Controllers\TramiteController;
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

/*Route::get('/navbar', function () {
    return view('navbar');
});*/

Route::get('/reporte/constancia/{idTramite}', [ReporteController::class, 'generarPDF'])->name('reporte.constancia');
Route::post('/archivo/subir', [ArchivoController::class, 'subirArchivo'])->name('archivo.subir');
Route::get('/archivo/descargar/{id}', [ArchivoController::class, 'descargar'])->name('archivo.descargar');
Route::post('/comentario/guardar', [ComentarioController::class, 'guardarComentario'])->name('comentario.guardar');
Route::get('/tramites/{id}/detalle', [TramiteController::class, 'show'])->name('tramites.detalle');
Route::get('/tramites', [TramiteController::class, 'index'])->name('tramites.index');
Route::get('/tramites/data', [TramiteController::class, 'getTramitesData'])->name('tramites.data');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
Route::put('/categorias/{id}/desactivar', [CategoriaController::class, 'desactivar'])->name('categorias.desactivar');
Route::get('/categorias/{id}/edit', [CategoriaController::class, 'edit'])->name('categorias.edit');
Route::put('/categorias/{id}', [CategoriaController::class, 'update'])->name('categorias.update');
Route::get('categorias/create', [CategoriaController::class, 'create'])->name('categorias.create');
Route::post('categorias', [CategoriaController::class, 'store'])->name('categorias.store');
Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
Route::get('usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
Route::post('usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
Route::get('/roles/{id_rol}/permisos', [UsuarioController::class, 'obtenerPermisosPorRol'])->name('roles.permisos');
Route::get('/usuarios/{id}/licencias', [LicenciaController::class, 'crearLicencia'])->name('licencias.crear');
Route::post('/usuarios/{id}/licencias', [LicenciaController::class, 'guardarLicencia'])->name('licencias.store');
Route::get('/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
Route::get('roles', [RolController::class, 'index'])->name('roles.index');
Route::get('roles/create', [RolController::class, 'create'])->name('roles.create');
Route::post('roles', [RolController::class, 'store'])->name('roles.store');
Route::get('/roles/{id}/edit', [RolController::class, 'edit'])->name('roles.edit');
Route::put('/roles/{id}', [RolController::class, 'update'])->name('roles.update');
Route::get('limite', [LimiteController::class, 'index'])->name('limites.index');
Route::post('/guardar-limite', [LimiteController::class, 'guardarLimite'])->name('guardar.limite');
Route::get('usuario', [ContribuyenteMultinotaController::class, 'index'])->name('contribuyente.index');
Route::post('/usuario', [ContribuyenteMultinotaController::class, 'buscar'])->name('contribuyente.buscar');
Route::put('/usuario/{id}/actualizar-correo', [ContribuyenteMultinotaController::class, 'actualizarCorreo'])->name('contribuyente.actualizarCorreo');
Route::post('/usuario/{id}/restablecer-clave', [ContribuyenteMultinotaController::class, 'restablecerClave'])->name('contribuyente.restablecerClave');
Route::get('cuestionarios', [CuestionarioController::class, 'index'])->name('cuestionarios.index');
Route::get('/cuestionarios/crear', [CuestionarioController::class, 'create'])->name('cuestionarios.create');
Route::post('/cuestionarios', [CuestionarioController::class, 'store'])->name('cuestionarios.store');
Route::get('/cuestionarios/{id}/editar', [CuestionarioController::class, 'edit'])->name('cuestionarios.edit');
Route::put('/cuestionarios/{id}', [CuestionarioController::class, 'update'])->name('cuestionarios.update');
Route::put('/cuestionarios/{id}/activar', [CuestionarioController::class, 'activar'])->name('cuestionarios.activar');
Route::put('/cuestionarios/{id}/desactivar', [CuestionarioController::class, 'desactivar'])->name('cuestionarios.desactivar');
Route::get('/sistema', [ConfiguracionMailController::class, 'edit'])->name('configuracion.edit');
Route::post('/sistema', [ConfiguracionMailController::class, 'update'])->name('configuracion.update');
Route::get('/ingreso-externo', [IngresoExternoController::class, 'showLoginForm'])->name('ingreso-externo');
Route::post('/ingreso-externo', [IngresoExternoController::class, 'login'])->name('ingreso-externo.login');
Route::get('/bandeja-usuario-externo', [IngresoExternoController::class, 'showBandeja'])->name('bandeja-usuario-externo');
Route::get('/cambiar-clave', [ContribuyenteMultinotaController::class, 'showChangePasswordForm'])->name('cambiar-clave');
Route::post('/cambiar-clave', [ContribuyenteMultinotaController::class, 'changePassword'])->name('cambiar-clave.submit');
Route::get('/estados', [AdministracionWorkflowController::class, 'index'])->name('estados.index');
Route::get('/workflow/{id}', [AdministracionWorkflowController::class, 'crear'])->name('workflow.crear');


Route::get('/set-usuario-interno', [UsuarioController::class, 'setUsuarioInterno']);
Route::get('/clear-session', [UsuarioController::class, 'clearSession']); // Para limpiar la sesión


Route::get('/clear-session', function() {
    session()->forget('contribuyente_multinota');
    return redirect()->route('ingreso-externo');
});

Route::view('/navbar','/navbar')->name('navbar');


//Route::get('/dashboard', 'DashboardController@index')->name('dashboard.index');
Route::get('/bandeja-personal', [BandejaPersonalController::class, 'index'])->name('bandeja-personal.index');