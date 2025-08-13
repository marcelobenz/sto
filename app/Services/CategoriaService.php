<?php

namespace App\Services;

use App\Repositories\CategoriaRepository;

class CategoriaService
{
    protected $categoriaRepository;

    public function __construct(CategoriaRepository $categoriaRepository)
    {
        $this->categoriaRepository = $categoriaRepository;
    }

    public function getAllCategoriesForDataTables()
    {
        return $this->categoriaRepository->getAllWithParent();
    }

    public function getActiveCategories()
    {
        return $this->categoriaRepository->getActiveCategories();
    }

    public function findCategory($id)
    {
        return $this->categoriaRepository->findById($id);
    }

    public function findOrFailCategory($id)
    {
        return $this->categoriaRepository->findOrFail($id);
    }

    public function createCategory(array $data)
    {
        return $this->categoriaRepository->create($data);
    }

    public function updateCategory($id, array $data)
    {
        return $this->categoriaRepository->update($id, $data);
    }

    public function deactivateCategory($id)
    {
        return $this->categoriaRepository->deactivate($id);
    }
}