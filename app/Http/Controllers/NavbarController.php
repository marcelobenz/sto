<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\TipoTramiteMultinota;

class NavbarController extends Controller
{
    public function cargarItemsFormularios()
    {
        $categoriasSubcategoriasMap = collect();
        $subcategoriasMultinotasMap = collect();
        $categoriaPadreTieneCategoriasConMultinotasActivas = collect();
        $categorias = new Categoria;

        // Obtengo los IDs de las distintas categorias padre
        $distinctIdPadres = Categoria::whereNotNull('id_padre')
            ->distinct()
            ->pluck('id_padre');

        // Obtengo las subcategorias de cada categoria padre y las guardo en un HashMap
        foreach ($distinctIdPadres as $d) {
            $categorias = Categoria::where('flag_activo', 1)
                ->where('id_padre', $d)
                ->orderBy('nombre', 'asc')
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
                    ->orderBy('nombre', 'asc')
                    ->get();

                $subcategoriasMultinotasMap->put($c->id_categoria, $multinotasActivas);

                // Se revisa si cada categoria tiene multinotas activas
                // Verifico que el ID de categoria no sea igual al ID padre debido a que seria conceptualmente incorrecto
                if ($key != $c->id_categoria) {
                    $count = TipoTramiteMultinota::where('id_categoria', $c->id_categoria)
                        ->where('baja_logica', 0)
                        ->count();

                    array_push($arrayCounts, $count);
                }
            }

            $categoriaPadreTieneCategoriasConMultinotasActivas->put($key, false);

            // Se verifica si hay al menos 1 categoria con multinotas activas
            foreach ($arrayCounts as $a) {
                if ($a > 0) {
                    $categoriaPadreTieneCategoriasConMultinotasActivas->put($key, true);
                }
            }
        }

        return [
            'categoriasSubcategoriasMap' => $categoriasSubcategoriasMap,
            'subcategoriasMultinotasMap' => $subcategoriasMultinotasMap,
            'categoriaPadreTieneCategoriasConMultinotasActivas' => $categoriaPadreTieneCategoriasConMultinotasActivas,
        ];
    }
}
