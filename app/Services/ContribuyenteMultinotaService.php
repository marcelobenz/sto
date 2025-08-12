<?php

namespace App\Services;

use App\Repositories\ContribuyenteMultinotaRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\RestablecerClaveMailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ContribuyenteMultinotaService
{
    protected $repository;

    public function __construct(ContribuyenteMultinotaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buscarPorCuit(string $cuit): ?array
    {
        $contribuyente = $this->repository->findByCuit($cuit);
        return $contribuyente ? ['contribuyente' => $contribuyente] : null;
    }

    public function actualizarCorreo(int $id, string $email): bool
    {
        return $this->repository->updateEmail($id, $email);
    }

    public function restablecerClave(int $id): array
    {
        $newPassword = Str::random(12);
        $encryptedPassword = Hash::make($newPassword);

        $success = $this->repository->updatePassword($id, $encryptedPassword);
        
        if ($success) {
            $contribuyente = $this->repository->findById($id);
            Mail::to($contribuyente->correo)->send(new RestablecerClaveMailable($newPassword));
        }

        return [
            'success' => $success,
            'password' => $success ? $newPassword : null
        ];
    }

    public function cambiarClave($currentPassword, $newPassword, $contribuyente): bool
    {
        if (!Hash::check($currentPassword, $contribuyente->clave)) {
            return false;
        }

        return $this->repository->updatePassword(
            $contribuyente->id_contribuyente_multinota, 
            Hash::make($newPassword)
        );
    }

    public function actualizarPerfil(array $data): bool
    {
        return $this->repository->updateProfile($data);
    }

    public function getContribuyenteFromSession()
    {
        return Session::get('contribuyente_multinota');
    }

    public function storeContribuyenteInSession($contribuyente)
    {
        Session::put('contribuyente_multinota', $contribuyente);
    }
}