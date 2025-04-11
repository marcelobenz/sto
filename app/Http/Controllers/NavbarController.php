<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\TipoTramiteMultinota;
use App\Models\Categoria;
use App\Models\MensajeInicial;
use App\Models\TipoTramiteMensajeInicial;
use App\Models\MultinotaTipoCuenta;
use App\Models\MultinotaSeccion;
use App\Models\SeccionMultinota;
use App\Models\Campo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use DB;

class NavbarController extends Controller
{
    public function cargarItemsFormularios() {
        $categoriasSubcategoriasMap = collect();
        $subcategoriasMultinotasMap = collect();
        $categoriaPadreTieneCategoriasConMulinotasActivas = collect();
        $categorias = new Categoria();

        // Obtengo los IDs de las distintas categorias padre
        $distinctIdPadres = Categoria::whereNotNull('id_padre')
        ->distinct()
        ->pluck('id_padre');

        // Obtengo las subcategorias de cada categoria padre y las guardo en un HashMap
        foreach ($distinctIdPadres as $d) {
            $categorias = Categoria::where('flag_activo', 1)
            ->where('id_padre', $d)
            ->orderBy('nombre')
            ->get();

            // Busco la categoria padre para tener el nombre
            $categoriaPadre = Categoria::where('id_categoria', $d)->get();

            $categorias->prepend($categoriaPadre[0]);

            $categoriasSubcategoriasMap->put($d, $categorias);
        }

        // Obtengo las multinotas activas de cada subcategoria y las guardo en un HashMap
        foreach ($categoriasSubcategoriasMap as $key => $categorias) {
            $arrayCounts = [];
            
            foreach ($categorias as $c) {
                $multinotasActivas = TipoTramiteMultinota::where('id_categoria', $c->id_categoria)
                ->where('baja_logica', 0)
                ->where('publico', 1)
                ->get();

                $subcategoriasMultinotasMap->put($c->id_categoria, $multinotasActivas);

                // Se revisa si cada categoria tiene multinotas activas
                // Verifico que el ID de categoria no sea igual al ID padre debido a que seria conceptualmente incorrecto
                if($key != $c->id_categoria) {
                    $count = TipoTramiteMultinota::where('id_categoria', $c->id_categoria)
                    ->where('baja_logica', 0)
                    ->count();

                    array_push($arrayCounts, $count);
                }
            }

            $categoriaPadreTieneCategoriasConMulinotasActivas->put($key, false);

            // Se verifica si hay al menos 1 categoria con multinotas activas
            foreach ($arrayCounts as $a) {
                if($a > 0) {
                    $categoriaPadreTieneCategoriasConMulinotasActivas->put($key, true);
                }
            }
        }
        
        return [
            'categoriasSubcategoriasMap' => $categoriasSubcategoriasMap,
            'subcategoriasMultinotasMap' => $subcategoriasMultinotasMap,
            'categoriaPadreTieneCategoriasConMulinotasActivas' => $categoriaPadreTieneCategoriasConMulinotasActivas
        ];
    }
}