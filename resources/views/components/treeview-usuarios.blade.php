<!-- resources/views/components/treeview-usuarios.blade.php -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="border rounded bg-light p-3 h-100">
    <h1 class="h5">Usuarios por Grupo</h1>

    <!-- Campo de búsqueda -->
    <input type="text" id="user-search" class="form-control mb-3" placeholder="Buscar usuario...">

    <!-- Treeview de grupos y usuarios -->
    <ul id="treeview" class="list-unstyled">
        @foreach ($grupos as $grupo)
            <li class="mb-2">
                <span class="toggle-icon font-weight-bold">+</span>
                <span class="grupo-label" data-id="{{ $grupo->id_grupo_interno }}">{{ $grupo->descripcion }}</span>
                <ul class="usuarios-list pl-4" style="display: none;">
                    @foreach ($grupo->usuarios as $usuario)
                        <li class="usuario-item">
                           <input type="checkbox" class="usuario-checkbox"
                                  id="usuario-{{ $usuario->id_usuario_interno }}"
                                  data-usuario-id="{{ $usuario->id_usuario_interno }}"
                                  data-grupo-id="{{ $grupo->id_grupo_interno }}"
                                  data-nombre="{{ $usuario->nombre }}"
                                  data-apellido="{{ $usuario->apellido }}"
                                  data-legajo="{{ $usuario->legajo }}">

                            <label for="usuario-{{ $usuario->id_usuario_interno }}">{{ $usuario->nombre }} {{ $usuario->apellido }} (Legajo: {{ $usuario->legajo }})</label>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</div>


<script>
   $(document).ready(function() {
    console.log("DOM completamente cargado con jQuery"); 

    $('#treeview').on('click', '.grupo-label', function() {
        const icon = $(this).prev('.toggle-icon'); 
        const usuariosList = $(this).next('.usuarios-list'); 

        if (usuariosList.is(':visible')) {
            usuariosList.hide();
            icon.text('+'); 
        } else {
            usuariosList.show(); 
            icon.text('-'); 
        }
    });

    $('#treeview').on('change', '.usuario-checkbox', function() {
        const checkbox = $(this);
        console.log("Usuario seleccionado:", checkbox.val(), "Nombre:", checkbox.data('nombre'), "Apellido:", checkbox.data('apellido'), "Estado:", checkbox.is(':checked')); 

        const id = checkbox.val();
        const nombre = checkbox.data('nombre');
        const apellido = checkbox.data('apellido');

        if (checkbox.is(':checked')) {
            addItemToTable(id, nombre, apellido);
        } else {
            removeItemFromTable(id);
        }
    });

    function addItemToTable(id, nombre, apellido) {
        if ($('#selected-item-' + id).length) {
            return; 
        }
        const newRow = `<tr id="selected-item-${id}">
            <td>${id}</td>
            <td>${nombre} ${apellido}</td>
            <td>
                <input type="number" min="0" style="width: 100%;" placeholder="Ingrese límite" />
            </td>
        </tr>`;
        $('#selected-items-table tbody').append(newRow);
    }

    window.removeItemFromTable = function(id) {
        $('#selected-item-' + id).remove(); 
    };

    $('#user-search').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();

        $('.usuarios-list').hide(); 
        $('.toggle-icon').text('+'); 

        $('.usuario-item').each(function() {
            const usuarioCheckbox = $(this).find('.usuario-checkbox');
            const usuarioNombre = usuarioCheckbox.data('nombre').toLowerCase();
            const usuarioApellido = usuarioCheckbox.data('apellido').toLowerCase();
            const usuarioLegajo = usuarioCheckbox.data('legajo').toString();
            const grupo = $(this).closest('ul.usuarios-list');
            const grupoIcon = grupo.prev('.toggle-icon');

            if (usuarioNombre.includes(searchTerm) || usuarioApellido.includes(searchTerm) || usuarioLegajo.includes(searchTerm)) {
                $(this).show(); 
                grupo.show(); 
                grupoIcon.text('-'); 
            } else {
                $(this).hide(); 
            }
        });
    });
});
</script>