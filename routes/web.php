<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LicenciaController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SeccionesMultinotaController;


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

Route::get('/tramites', [TramiteController::class, 'index'])->name('tramites.index');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
Route::put('/categorias/{id}/desactivar', [CategoriaController::class, 'desactivar'])->name('categorias.desactivar');
Route::get('/categorias/{id}/edit', [CategoriaController::class, 'edit'])->name('categorias.edit');
Route::put('/categorias/{id}', [CategoriaController::class, 'update'])->name('categorias.update');
Route::get('categorias/create', [CategoriaController::class, 'create'])->name('categorias.create');
Route::post('categorias', [CategoriaController::class, 'store'])->name('categorias.store');
Route::get('/secciones-multinota', [SeccionesMultinotaController::class, 'index'])->name('secciones-multinota.index');
Route::get('/secciones-multinota/{id}/select', [SeccionesMultinotaController::class, 'selectCampo'])->name('secciones-multinota.selectCampo');
Route::get('/secciones-multinota/addOpcionCampo/{id_campo}/{nueva_opcion}', [SeccionesMultinotaController::class, 'addOpcionCampo'])->name('secciones-multinota.addOpcionCampo');
Route::get('/secciones-multinota/getOpcionesCampo/{id}/{tipo}', [SeccionesMultinotaController::class, 'getOpcionesCampo'])->name('secciones-multinota.getOpcionesCampo');
Route::get('/secciones-multinota/getOpcionesFormTipoCampo/{id}/{tipo}', [SeccionesMultinotaController::class, 'getOpcionesFormTipoCampo'])->name('secciones-multinota.getOpcionesFormTipoCampo');
Route::get('/secciones-multinota/{id}/deleteOpcionCampo', [SeccionesMultinotaController::class, 'deleteOpcionCampo'])->name('secciones-multinota.deleteOpcionCampo');
Route::get('/secciones-multinota/{id}/edit', [SeccionesMultinotaController::class, 'edit'])->name('secciones-multinota.edit');
Route::get('/secciones-multinota/{id}/deleteCampo', [SeccionesMultinotaController::class, 'deleteCampo'])->name('secciones-multinota.deleteCampo');
Route::post('/secciones-multinota/updateSeccion', [SeccionesMultinotaController::class, 'updateSeccion'])->name('secciones-multinota.updateSeccion');
Route::post('/secciones-multinota/updateSeccionOpcionesCampo', [SeccionesMultinotaController::class, 'updateSeccionOpcionesCampo'])->name('secciones-multinota.updateSeccionOpcionesCampo');
Route::get('/secciones-multinota/refresh', [SeccionesMultinotaController::class, 'refresh'])->name('secciones-multinota.refresh');
Route::get('/secciones-multinota/refreshOpcionesCampo', [SeccionesMultinotaController::class, 'refreshOpcionesCampo'])->name('secciones-multinota.refreshOpcionesCampo');
Route::put('/secciones-multinota/{id}', [SeccionesMultinotaController::class, 'update'])->name('secciones-multinota.update');
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

Route::get('/set-usuario-interno', [UsuarioController::class, 'setUsuarioInterno']);
Route::get('/clear-session', [UsuarioController::class, 'clearSession']); // Para limpiar la sesión

Route::view('/navbar','/navbar')->name('navbar');


//Route::get('/dashboard', 'DashboardController@index')->name('dashboard.index');
