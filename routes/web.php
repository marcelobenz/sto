<?php

use Illuminate\Support\Facades\Route;
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

Route::get('/', function () {
    return view('welcome');
});

/*Route::get('/navbar', function () {
    return view('navbar');
});*/

Route::get('/archivo/{id}/descargar', [ArchivoController::class, 'descargar'])->name('archivo.descargar');
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
Route::get('cuestionarios', [CuestionarioController::class, 'index'])->name('cuestionarios.index');
Route::get('/cuestionarios/crear', [CuestionarioController::class, 'create'])->name('cuestionarios.create');
Route::post('/cuestionarios', [CuestionarioController::class, 'store'])->name('cuestionarios.store');
Route::get('/cuestionarios/{id}/editar', [CuestionarioController::class, 'edit'])->name('cuestionarios.edit');
Route::put('/cuestionarios/{id}', [CuestionarioController::class, 'update'])->name('cuestionarios.update');
Route::put('/cuestionarios/{id}/activar', [CuestionarioController::class, 'activar'])->name('cuestionarios.activar');
Route::put('/cuestionarios/{id}/desactivar', [CuestionarioController::class, 'desactivar'])->name('cuestionarios.desactivar');

Route::get('/set-usuario-interno', [UsuarioController::class, 'setUsuarioInterno']);
Route::get('/clear-session', [UsuarioController::class, 'clearSession']); // Para limpiar la sesión

Route::view('/navbar','/navbar')->name('navbar');

//Route::get('/dashboard', 'DashboardController@index')->name('dashboard.index');
