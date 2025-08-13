<?php

namespace App\Repositories;

use App\Models\Categoria;

class CategoriaRepository
{
    public function getAllWithParent()
    {
        return Categoria::leftJoin('categoria as parent', 'categoria.id_padre', '=', 'parent.id_categoria')
            ->select('categoria.*', 'parent.nombre as parent_nombre')
            ->where('categoria.flag_activo', 1)
            ->get();
    }

    public function findById($id)
    {
        return Categoria::find($id);
    }

    public function findOrFail($id)
    {
        return Categoria::findOrFail($id);
    }

    public function getActiveCategories()
    {
        return Categoria::where('flag_activo', 1)->get();
    }

    public function create(array $data)
    {
        return Categoria::create([
            'nombre' => $data['nombre'],
            'id_padre' => $data['id_padre'] ?? null,
            'flag_activo' => $data['flag_activo'],
            'fecha_alta' => now()
        ]);
    }

    public function update($id, array $data)
    {
        $categoria = $this->findById($id);
        
        if ($categoria) {
            $categoria->update([
                'nombre' => $data['nombre'],
                'id_padre' => $data['id_padre'] ?? null,
                'flag_activo' => $data['flag_activo']
            ]);
            return $categoria;
        }
        
        return null;
    }

    public function deactivate($id)
    {
        $categoria = $this->findById($id);
        
        if ($categoria) {
            $categoria->flag_activo = 0;
            $categoria->save();
            return true;
        }
        
        return false;
    }
}