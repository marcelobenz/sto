<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\Models\UsuarioSession;

class BandejaPersonalController extends Controller
{
    public static function index() {
        Session::get('USUARIO');
        return view('bandeja-personal.index');
    }
}
