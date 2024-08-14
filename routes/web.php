<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\DashboardController;
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

Route::get('/navbar', function () {
    return view('navbar');
});

Route::get('/tramites', [TramiteController::class, 'index'])->name('tramites.index');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
//Route::get('/dashboard', 'DashboardController@index')->name('dashboard.index');
