<div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Acciones
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="#" wire:click.prevent="edit({{ $categoryId }})">Editar</a>
        <a class="dropdown-item" href="#" wire:click.prevent="confirmDelete({{ $categoryId }})">Eliminar</a>
    </div>
</div>
