<?php

namespace App\Repositories;

use App\Models\ContribuyenteMultinota;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContribuyenteMultinotaRepository
{
    public function findByCuit(string $cuit): ?ContribuyenteMultinota
    {
        return ContribuyenteMultinota::where('cuit', $cuit)->first();
    }

    public function findById(int $id): ?ContribuyenteMultinota
    {
        return ContribuyenteMultinota::find($id);
    }

    public function updateEmail(int $id, string $email): bool
    {
        $contribuyente = $this->findById($id);
        if (!$contribuyente) {
            return false;
        }

        $contribuyente->correo = $email;
        return $contribuyente->save();
    }

    public function updatePassword(int $id, string $password): bool
    {
        $contribuyente = $this->findById($id);
        if (!$contribuyente) {
            return false;
        }

        $contribuyente->clave = $password;
        return $contribuyente->save();
    }

    public function updateProfile(array $data): bool
    {
        $contribuyente = $this->findById($data['id_contribuyente_multinota']);
        if (!$contribuyente) {
            return false;
        }

        $contribuyente->fill($data);
        return $contribuyente->save();
    }
}