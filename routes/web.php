<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


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
    /* $state = $request->session()->pull('state');
 
    throw_unless(
        strlen($state) > 0 && $state === $request->state,
        InvalidArgumentException::class,
        'Invalid state value.'
    ); */

    $code = $request->code;

    $response = Http::asForm()->post('http://localhost:8000/oauth/token', [
        'grant_type' => 'authorization_code',
        'client_id' => '1',
        'client_secret' => 'GToG3ZMH8toPdQsJ3Jq0YSnBBZp4xRUbqcBl855Z',
        'redirect_uri' => 'http://127.0.0.1:8001/success',
        'code' => $request->code,
    ]);

    $res = $response->json();

    return $response->json();
});

Route::get('/', function () {
    return redirect('http://localhost:8000/oauth/authorize?client_id=1&redirect_uri=http://127.0.0.1:8001/success&response_type=code&scope=&state=asdfasdfasdfasdfasdfasdfasdfasdfasdfasdf');
});

Route::get('/navbar', function () {
    return view('navbar');
});

Route::get('/tramites', [TramiteController::class, 'index'])->name('tramites.index');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
//Route::get('/dashboard', 'DashboardController@index')->name('dashboard.index');
