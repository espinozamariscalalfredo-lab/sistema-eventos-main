{{-- Cambio al español: 12/10/2026. La vista usa nombres de reservas y campos en español. --}}
<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Mantiene el mismo estilo ligero de iconos en las tablas de formularios. */
        .tabla-icono-accion { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; margin-left: 4px; border: 0; border-radius: 7px; background: transparent; color: #3b82f6; font-size: 16px; text-decoration: none; cursor: pointer; transition: background-color .15s ease, color .15s ease; }
        .tabla-icono-accion:hover, .tabla-icono-accion:focus-visible { background: #eff6ff; color: #1d4ed8; outline: none; }
    </style>
    
    <div class="min-h-screen bg-white py-8 px-4 sm:px-8">
        <div style="max-width: 1200px; margin: 0 auto;" class="relative">
            
            <!-- Cabecera y Botones Usar/Supervisar -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                <h1 style="font-size: 26px; font-weight: bold; color: #475b75;">Opciones de supervisión para reservas de salon</h1>
                
                <div style="display: flex; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <a href="{{ route('salones.usar') }}" style="background-color: white; padding: 6px 16px; color: #3b82f6; border-right: 1px solid #e5e7eb; font-size: 14px; text-decoration: none;">
                        <i class="fa-solid fa-sliders" style="margin-right: 8px;"></i> Usar
                    </a>
                    <button style="background-color: #f9fafb; padding: 6px 16px; font-weight: bold; color: #374151; font-size: 14px; border: none;">
                        <i class="fa-solid fa-sliders" style="margin-right: 8px;"></i> Supervisar
                    </button>
                </div>
            </div>

            <!-- Contador y enlace a papelera -->
            <div style="display: flex; align-items: baseline; gap: 16px; margin-bottom: 16px;">
                <span style="font-size: 15px; color: #4b5563;">{{ $totalFormularios }} formularios</span>
                <a href="{{ route('salones.papelera') }}" style="color: #3b82f6; font-size: 13px; text-decoration: underline;">Mostrar papelera</a>
            </div>

            <!-- TABLA PRINCIPAL -->
            <div style="max-width: 600px; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden; margin-bottom: 24px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13.5px; text-align: left;">
                    <thead>
                        <tr style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <th style="padding: 10px 16px; font-weight: bold; color: #475b75; text-decoration: underline;">ID</th>
                            <th style="padding: 10px 16px; font-weight: bold; color: #475b75; text-decoration: underline; text-align: center;">Creado por</th>
                            <th style="padding: 10px 16px; font-weight: bold; color: #475b75; text-decoration: underline;">Creado el</th>
                            <th style="padding: 10px 16px; text-align: right; color: #2563eb; font-size: 16px;">
                                <i class="fa-solid fa-download"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservas as $reserva)
                        <tr style="border-bottom: 1px solid #e5e7eb; color: #4b5563; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='transparent'">
                            <td style="padding: 10px 16px;">{{ $reserva->id }}</td>
                            <td style="padding: 10px 16px; text-align: center;">{{ $reserva->nombre_usuario ?? 'administrador' }}</td>
                            <td style="padding: 10px 16px;">
                                {{ $reserva->fecha_creacion ? $reserva->fecha_creacion->format('Y-m-d H:i') : '-' }}
                            </td>
                            <td style="padding: 10px 16px; text-align: right; color: #2563eb; font-size: 16px;">
                                <!-- Botón Calendario (Redirige al calendario grande de salones) -->
                                <a class="tabla-icono-accion" href="{{ route('salones.usar') }}" title="Ver en calendario" aria-label="Ver en calendario">
                                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                </a>
                                <!-- Botón Documento (Abre el modal reutilizando el componente) -->
                                <button class="tabla-icono-accion" onclick="openFormModal({{ $reserva->id }})" title="Ver Formulario" aria-label="Ver formulario">
                                    <i class="fa-regular fa-file-lines" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        
                        @if($reservas->isEmpty())
                        <tr>
                            <td colspan="4" style="padding: 24px; text-align: center; color: #9ca3af;">No hay formularios registrados.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- PAGINACIÓN AUTOMÁTICA -->
            <div style="margin-bottom: 24px; max-width: 600px;">
                {{ $reservas->links() }}
            </div>

            <!-- ÚLTIMA ADICIÓN -->
            @if($ultimoEvento)
            <div style="display: flex; align-items: center; gap: 12px; color: #4b5563; font-size: 13.5px;">
                <i class="fa-solid fa-file-circle-plus" style="font-size: 24px; color: #3b82f6;"></i>
                <span>Última adición: {{ $ultimoEvento->fecha_creacion->format('Y-m-d H:i') }} por {{$ultimoEvento->nombre_usuario ?? 'administrador' }}</span>
            </div>
            @endif

        </div>
    </div>

    <!-- MODAL FLOTANTE PARA VER EL FORMULARIO REUTILIZANDO EL COMPONENTE -->
    <div id="form-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; overflow-y: auto; padding: 20px 0;">
        <div style="background: white; width: 700px; max-width: 90%; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow: hidden; max-height: 90vh; display: flex; flex-direction: column;">
            
            <div style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background: #f9fafb;">
                <h3 style="font-size: 16px; font-weight: bold; color: #475b75;">Detalle del Formulario #<span id="modal-event-id"></span></h3>
                <button onclick="document.getElementById('form-modal').style.display='none'" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #9ca3af;"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div style="padding: 24px; overflow-y: auto; flex: 1;" id="modal-form-content">
                <!-- Aquí cargaremos dinámicamente los datos mediante JavaScript o una vista parcial -->
                <p style="text-align: center; color: #9ca3af;">Cargando información...</p>
            </div>

            <div style="padding: 16px 20px; border-top: 1px solid #e5e7eb; background: #f9fafb; text-align: right;">
                <button onclick="document.getElementById('form-modal').style.display='none'" style="background: #2563eb; color: white; border: none; padding: 6px 16px; border-radius: 4px; font-size: 13px; font-weight: bold; cursor: pointer;">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        function openFormModal(id) {
            document.getElementById('modal-event-id').innerText = id;
            document.getElementById('form-modal').style.display = 'flex';
            document.getElementById('modal-form-content').innerHTML = '<p style="text-align: center; color: #9ca3af; padding: 20px;">Cargando datos del formulario...</p>';

            // Hacemos una petición para obtener los datos del evento y mostrarlos en modo lectura dentro del modal
            fetch('/api/salones/reservas/' + id)
                .then(res => res.json())
                .then(data => {
                    // Si creamos una ruta API que devuelva el HTML del formulario parcial o los datos, podemos renderizarlo.
                    // Para simplificar, mostramos un resumen rápido con los campos del componente:
                    let html = `
                        <div style="display: grid; grid-template-columns: 200px 1fr; gap: 12px; font-size: 13.5px; color: #374151;">
                            <strong>Nombre del evento:</strong> <span>${data.titulo || ''}</span>
                            <strong>Autoridad solicitante:</strong> <span>${data.nombre_autoridad || ''}</span>
                            <strong>Tipo de evento:</strong> <span>${data.tipo_evento || ''}</span>
                            <strong>Aforo:</strong> <span>${data.aforo || ''}</span>
                            <strong>Tipo de ingreso:</strong> <span>${data.tipo_ingreso || ''}</span>
                            <strong>Requerimiento especial:</strong> <span>${data.requerimientos_especiales || ''}</span>
                            <strong>Registrado por:</strong> <span>${data.nombre_usuario || ''}</span>
                            <strong>Salón:</strong> <span>${data.salon || ''}</span>
                        </div>
                    `;
                    document.getElementById('modal-form-content').innerHTML = html;
                })
                .catch(err => {
                    document.getElementById('modal-form-content').innerHTML = '<p style="text-align: center; color: #ef4444;">Error al cargar los datos del formulario.</p>';
                });
        }
    </script>
</x-app-layout>
