<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- Cambio: 15/10/2026. SweetAlert2 unifica los avisos de esta pantalla. --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Acciones de la tabla con el mismo trazo azul ligero del resto del sistema. */
        .tabla-icono-accion { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; margin-left: 4px; border: 0; border-radius: 7px; background: transparent; color: #3b82f6; font-size: 16px; text-decoration: none; cursor: pointer; transition: background-color .15s ease, color .15s ease; }
        .tabla-icono-accion:hover, .tabla-icono-accion:focus-visible { background: #eff6ff; color: #1d4ed8; outline: none; }
    </style>
    
    <div class="min-h-screen bg-white py-8 px-4 sm:px-8">
        <div style="max-width: 1100px; margin: 0 auto;">
            
            <h1 style="font-size: 26px; font-weight: bold; color: #475b75; margin-bottom: 12px;">Gestión de usuarios</h1>
            
            <p style="font-size: 13.5px; color: #4b5563; margin-bottom: 24px;">
                Los usuarios y superusuarios enumerados aquí pueden acceder a todas las aplicaciones de su cuenta.
            </p>

            @if(in_array(auth()->user()->rol, ['admin', 'superadmin'], true))
            <section style="border: 1px solid #cbd5e1; border-radius: 11px; padding: 16px; background: #fff; margin-bottom: 20px;">
                <h2 style="font-size: 19px; font-weight: bold; color: #475b75; margin-bottom: 6px;">Nombres de eventos del calendario</h2>
                <p style="font-size: 13px; color: #64748b; margin-bottom: 14px;">Consulta y administra los salones. Ahora puedes editar el nombre, el color y el orden guardando los cambios.</p>
                
                <form action="{{ route('salones.nombres.store') }}" method="POST" style="display: flex; flex-wrap: wrap; gap: 8px; align-items: end; margin-bottom: 16px;">
                    @csrf
                    <label style="font-size: 12px; color: #475569;">Nuevo nombre
                        <input name="nombre" maxlength="120" required placeholder="Nombre del evento" style="display:block; border:1px solid #cbd5e1; border-radius:6px; padding:7px;">
                    </label>
                    <label style="font-size: 12px; color: #475569;">Color
                        <input type="color" name="color" value="#2563eb" required style="display:block; width:54px; height:34px; border:1px solid #cbd5e1; border-radius:6px; padding:2px;">
                    </label>
                    <label style="font-size: 12px; color: #475569;">Orden
                        <input type="number" name="orden" min="0" max="9999" value="0" style="display:block; width:82px; border:1px solid #cbd5e1; border-radius:6px; padding:7px;">
                    </label>
                    <button type="submit" style="background:#2563eb; color:white; border:0; padding:8px 13px; border-radius:6px; cursor:pointer;">Agregar Salon</button>
                </form>

                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; font-size:13px; text-align:left;">
                        <thead><tr style="background:#f1f5f9; color:#475b75;">
                            <th style="padding:9px;">Nombre</th>
                            <th style="padding:9px;">Color</th>
                            <th style="padding:9px;">Orden</th>
                            <th style="padding:9px;">Estado</th>
                            <th style="padding:9px;">Guardar</th>
                            <th style="padding:9px;">Acciones</th>
                        </tr></thead>
                        <tbody>
                        @foreach($salones as $salon)
                            <tr style="border-top:1px solid #e2e8f0;">
                                {{-- FORMULARIO PRINCIPAL DE EDICIÓN (Abarca Nombre, Color y Orden) --}}
                                <td style="padding:8px;">
                                    <form id="editar-salon-{{ $salon->id }}" action="{{ route('salones.nombres.update', $salon) }}" method="POST" style="margin: 0;">
                                        @csrf @method('PUT')
                                        <input name="nombre" value="{{ $salon->nombre }}" maxlength="120" required aria-label="Nombre" style="width:100%; min-width:180px; border:1px solid #cbd5e1; border-radius:6px; padding:7px;">
                                    </form>
                                </td>
                                
                                {{-- SECCIÓN COLOR EDITABLE (Conectado al form principal por ID) --}}
                                <td style="padding:8px;">
                                    <input form="editar-salon-{{ $salon->id }}" type="color" name="color" value="{{ $salon->color }}" required title="Cambiar color" style="display:inline-block; width:36px; height:32px; border:1px solid #cbd5e1; border-radius:4px; padding:1px; cursor:pointer;">
                                </td>
                                
                                {{-- ORDEN EDITABLE --}}
                                <td style="padding:8px;">
                                    <input form="editar-salon-{{ $salon->id }}" type="number" name="orden" value="{{ $salon->orden }}" min="0" max="9999" required aria-label="Orden" style="width:74px; border:1px solid #cbd5e1; border-radius:6px; padding:7px;">
                                </td>
                                
                                {{-- ESTADO --}}
                                <td style="padding:8px; color:{{ $salon->activo ? '#15803d' : '#64748b' }}; font-weight: 500;">
                                    {{ $salon->activo ? 'Activo' : 'Inactivo' }}
                                </td>
                                
                                {{-- BOTÓN GUARDAR EDICIÓN --}}
                                <td style="padding:8px;">
                                    <button form="editar-salon-{{ $salon->id }}" type="submit" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; padding:7px 10px; border-radius:6px; cursor:pointer;">
                                        Guardar
                                    </button>
                                </td>
                                
                                {{-- ACCIONES (Dar de baja / Eliminar) --}}
                                <td style="padding:8px;">
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        
                                        {{-- Botón Dar de baja / Activar (Disponible para Admin y Superadmin) --}}
                                        <form action="{{ route('salones.nombres.estado', $salon) }}" method="POST" onsubmit="return confirm('¿{{ $salon->activo ? 'Dar de baja' : 'Activar' }} este nombre?');" style="margin: 0;">
                                            @csrf @method('PATCH')
                                            <button type="submit" style="background:{{ $salon->activo ? '#fff7ed' : '#f0fdf4' }}; color:{{ $salon->activo ? '#c2410c' : '#15803d' }}; border:1px solid {{ $salon->activo ? '#fed7aa' : '#bbf7d0' }}; padding:7px 10px; border-radius:6px; cursor:pointer; white-space:nowrap;">
                                                {{ $salon->activo ? 'Dar de baja' : 'Activar' }}
                                            </button>
                                        </form>

                                        {{-- Botón Eliminar DEFINITIVO (Solo Admin) --}}
                                        @if(auth()->user()->rol === 'admin')
                                        <form action="{{ route('salones.nombres.destroy', $salon->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este salón definitivamente? El orden de los demás salones se recalculará automáticamente.');" style="margin: 0;">
                                            @csrf @method('DELETE')
                                            <button type="submit" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca; padding:7px 10px; border-radius:6px; cursor:pointer;" title="Eliminar definitivamente">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                        @endif
                                        
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
            @endif

            <div style="border: 1px solid #cbd5e1; border-radius: 11px; padding: 16px; background: #fff;">
            <!-- TABLA PRINCIPAL DE USUARIOS -->
            <div style="overflow-x: auto; margin-bottom: 16px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13.5px; text-align: left;">
                    <thead>
                        <tr style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <th style="padding: 10px 16px; font-weight: bold; color: #475b75;">Rol</th>
                            <th style="padding: 10px 16px; font-weight: bold; color: #475b75;">Nombre de usuario / correo</th>
                            <th style="padding: 10px 16px; font-weight: bold; color: #475b75;">Nombre completo</th>
                            <th style="padding: 10px 16px; font-weight: bold; color: #475b75;">Creado el</th>
                            <th style="padding: 10px 16px; text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                        <tr style="border-bottom: 1px solid #e5e7eb; color: #4b5563; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='transparent'">
                            <td style="padding: 10px 16px;">{{ $usuario->rol ?? 'usuario' }}</td>
                            <td style="padding: 10px 16px;">{{ $usuario->correo }}</td>
                            <td style="padding: 10px 16px;">{{ $usuario->nombre }}</td>
                            <td style="padding: 10px 16px;">
                                {{ $usuario->fecha_creacion ? $usuario->fecha_creacion->format('Y-m-d H:i') : '-' }}
                            </td>
                            <td style="padding: 10px 16px; text-align: right; color: #2563eb; font-size: 15px;">
                                <!-- Botón Editar -->
                                <button class="tabla-icono-accion" onclick="openEditModal({{ $usuario->id }}, '{{ $usuario->nombre }}', '{{ $usuario->correo }}', '{{ $usuario->rol }}')" title="Editar" aria-label="Editar usuario">
                                    <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i>
                                </button>
                                
                                <!-- Botón Eliminar -->
                                <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Eliminar" style="color: #ef4444; background: none; border: none; cursor: pointer;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- BOTÓN NUEVO USUARIO -->
            @if(auth()->user()->rol === 'admin')
            <button onclick="document.getElementById('modal-create').style.display='flex'" style="background-color: #2563eb; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: bold; font-size: 13.5px; margin-bottom: 30px; cursor: pointer;">
                Nuevo usuario
            </button>
            @endif
            </div>

            <!-- ENLACE PARA VOLVER AL RESUMEN -->
            <div style="text-align: center; border-top: 1px solid #e5e7eb; padding-top: 24px; margin-top: 40px;">
                <a href="{{ route('salones.supervisar') }}" style="color: #2563eb; text-decoration: underline; font-size: 14px;">
                    Volver al Resumen
                </a>
            </div>
        </div>
    </div>

    <!-- MODAL CREAR USUARIO -->
    <div id="modal-create" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
        <div style="background: white; width: 400px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow: hidden;">
            <div style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 16px; font-weight: bold; color: #475b75;">Crear Nuevo Usuario</h3>
                <button onclick="document.getElementById('modal-create').style.display='none'" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #9ca3af;"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('usuarios.store') }}" method="POST" style="padding: 20px;">
                @csrf
                <div style="margin-bottom: 12px;">
                    <label style="font-size: 13px; color: #4b5563; font-weight: bold;">Nombre Completo</label>
                    <input type="text" name="nombre" style="width: 100%; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px; font-size: 13px;" required>
                </div>
                <div style="margin-bottom: 12px;">
                    <label style="font-size: 13px; color: #4b5563; font-weight: bold;">Correo Electrónico</label>
                    <input type="email" name="correo" style="width: 100%; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px; font-size: 13px;" required>
                </div>
                <div style="margin-bottom: 12px;">
                    <label style="font-size: 13px; color: #4b5563; font-weight: bold;">Contraseña</label>
                    <input type="password" name="password" style="width: 100%; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px; font-size: 13px;" required>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="font-size: 13px; color: #4b5563; font-weight: bold;">Rol</label>
                    <select name="rol" style="width: 100%; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px; font-size: 13px;" required>
                        <option value="usuario">Usuario (Solo ver)</option>
                        <option value="superadmin">Superadmin (Crear y editar)</option>
                        <option value="admin">Admin (Control total)</option>
                    </select>
                </div>
                <button type="submit" style="width: 100%; background: #2563eb; color: white; border: none; padding: 10px; border-radius: 4px; font-size: 14px; font-weight: bold; cursor: pointer;">Guardar Usuario</button>
            </form>
        </div>
    </div>

    <!-- MODAL EDITAR USUARIO -->
    <div id="modal-edit" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
        <div style="background: white; width: 400px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow: hidden;">
            <div style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 16px; font-weight: bold; color: #475b75;">Editar Usuario</h3>
                <button onclick="document.getElementById('modal-edit').style.display='none'" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #9ca3af;"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="form-edit" method="POST" style="padding: 20px;">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 12px;">
                    <label style="font-size: 13px; color: #4b5563; font-weight: bold;">Nombre Completo</label>
                    <input type="text" id="edit-nombre" name="nombre" style="width: 100%; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px; font-size: 13px;" required>
                </div>
                <div style="margin-bottom: 12px;">
                    <label style="font-size: 13px; color: #4b5563; font-weight: bold;">Correo Electrónico</label>
                    <input type="email" id="edit-correo" name="correo" style="width: 100%; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px; font-size: 13px;" required>
                </div>
                <div style="margin-bottom: 12px;">
                    <label style="font-size: 13px; color: #4b5563; font-weight: bold;">Contraseña <span style="font-weight: normal; color: #9ca3af;">(Dejar en blanco para no cambiar)</span></label>
                    <input type="password" name="password" style="width: 100%; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px; font-size: 13px;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="font-size: 13px; color: #4b5563; font-weight: bold;">Rol</label>
                    <select id="edit-rol" name="rol" style="width: 100%; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px; font-size: 13px;" required>
                        <option value="usuario">Usuario (Solo ver)</option>
                        <option value="superadmin">Superadmin (Crear y editar)</option>
                        <option value="admin">Admin (Control total)</option>
                    </select>
                </div>
                <button type="submit" style="width: 100%; background: #2563eb; color: white; border: none; padding: 10px; border-radius: 4px; font-size: 14px; font-weight: bold; cursor: pointer;">Actualizar Usuario</button>
            </form>
        </div>
    </div>

    <script>
        // Cambio: 15/10/2026. Muestra avisos flash de usuarios, salones y validación.
        function mostrarAvisoUsuarios(aviso) {
            Swal.fire({
                icon: aviso.tipo,
                title: aviso.titulo,
                text: aviso.mensaje,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        }

        @if(session('notificacion'))
            mostrarAvisoUsuarios(@json(session('notificacion')));
        @elseif(session('estado_catalogo'))
            mostrarAvisoUsuarios({ tipo: 'success', titulo: '¡Listo!', mensaje: @json(session('estado_catalogo')) });
        @elseif(session('error_catalogo'))
            mostrarAvisoUsuarios({ tipo: 'error', titulo: 'No se pudo completar', mensaje: @json(session('error_catalogo')) });
        @elseif($errors->any())
            mostrarAvisoUsuarios({ tipo: 'error', titulo: 'Revisa los datos', mensaje: @json($errors->first()) });
        @endif

        function openEditModal(id, name, email, role) {
            document.getElementById('edit-nombre').value = name;
            document.getElementById('edit-correo').value = email;
            
            // Si el rol viene vacío, lo marcamos como usuario por defecto
            document.getElementById('edit-rol').value = role ? role : 'usuario'; 
            
            // Configurar la ruta dinámicamente para que actualice al ID correcto
            document.getElementById('form-edit').action = '/salones/usuarios/' + id;
            
            document.getElementById('modal-edit').style.display = 'flex';
        }
    </script>
</x-app-layout>
