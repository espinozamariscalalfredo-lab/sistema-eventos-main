{{-- Cambio al español: 12/10/2026. La vista usa nombres de reservas y campos en español. --}}
<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Iconos de acción discretos para tablas: documento, calendario y edición. */
        .tabla-icono-accion { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; margin-left: 4px; border: 0; border-radius: 7px; background: transparent; color: #3b82f6; font-size: 16px; text-decoration: none; cursor: pointer; transition: background-color .15s ease, color .15s ease; }
        .tabla-icono-accion:hover, .tabla-icono-accion:focus-visible { background: #eff6ff; color: #1d4ed8; outline: none; }
    </style>
    
    <div class="min-h-screen bg-white py-6 px-4 sm:px-8">
        <div style="max-width: 1400px; margin: 0 auto;">
            
            <!-- Cabecera y Botones Usar/Supervisar -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                <h1 style="font-size: 28px; font-weight: bold; color: #475b75;">Opciones de supervisión para salones</h1>
                
                <div style="display: flex; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <a href="{{ route('salones.usar') }}" style="background-color: white; padding: 6px 16px; color: #3b82f6; border-right: 1px solid #e5e7eb; font-size: 14px; text-decoration: none;">
                        <i class="fa-regular fa-calendar" style="margin-right: 8px;"></i> Usar
                    </a>
                    <button style="background-color: #f9fafb; padding: 6px 16px; font-weight: bold; color: #374151; font-size: 14px; border: none;">
                        <i class="fa-solid fa-sliders" style="margin-right: 8px;"></i> Supervisar
                    </button>
                </div>
            </div>

            <!-- Pestañas (Tabs) -->
            <div style="display: flex; gap: 24px; border-bottom: 2px solid #f3f4f6; margin-bottom: 24px;">
                <div style="padding: 8px 12px; border-bottom: 3px solid #e0e7ff; color: #4f46e5; font-weight: bold; font-size: 14px; cursor: pointer; background-color: #f5f3ff;">RESUMEN</div>
                <div style="padding: 8px 12px; color: #6b7280; font-size: 14px; cursor: pointer;">DESCARGAR</div>
            </div>

            <div style="border: 1px solid #cbd5e1; border-radius: 11px; padding: 16px; background: #fff;">
            <h2 style="font-size: 22px; font-weight: bold; color: #374151; margin-bottom: 16px;">Resumen</h2>

            <p style="font-size: 14px; color: #4b5563; margin-bottom: 8px;">Este horario se encuentra en esta dirección web:</p>
            <a href="{{ route('salones.usar') }}" style="font-size: 15px; color: #2563eb; font-weight: bold; text-decoration: none; display: flex; align-items: center; gap: 6px; margin-bottom: 24px;">
                {{ url('/salones/usar') }} <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 12px;"></i>
            </a>

            <!-- Filtros y Enlaces -->
            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 12px; font-size: 14px;">
                <form method="GET" action="{{ route('salones.supervisar') }}" style="display: flex; align-items: center; gap: 6px;">
                    <label for="filtro-salon-supervisar" style="color: #4b5563;">Filtrar:</label>
                    <select id="filtro-salon-supervisar" name="salon" onchange="this.form.submit()" style="border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 8px; font-size: 13px; color: #374151;">
                        <option value="" {{ empty($salonSeleccionado) ? 'selected' : '' }}>Todos</option>
                        @foreach($salones as $salon)
                            <option value="{{ $salon->nombre }}" {{ $salonSeleccionado === $salon->nombre ? 'selected' : '' }}>{{ $salon->nombre }}</option>
                        @endforeach
                    </select>
                    <noscript><button type="submit">Filtrar</button></noscript>
                </form>
                <a href="#" style="color: #3b82f6; text-decoration: underline;">Mostrar historial</a>
                <a href="{{ route('salones.papelera') }}" style="color: #3b82f6; text-decoration: underline;">Mostrar papelera</a>
            </div>

            <!-- TABLA PRINCIPAL DE RESERVAS -->
            <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; margin-bottom: 40px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                    <thead>
                        <tr style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <th style="padding: 12px 16px; font-weight: bold; color: #475b75; text-decoration: underline;">salones</th>
                            <th style="padding: 12px 16px; font-weight: bold; color: #475b75; text-decoration: underline;">Cuándo</th>
                            <th style="padding: 12px 16px; font-weight: bold; color: #475b75; text-decoration: underline;">Creado por</th>
                            <th style="padding: 12px 16px; font-weight: bold; color: #475b75; text-decoration: underline;">Creado el</th>
                            <th style="padding: 12px 16px; font-weight: bold; color: #475b75; text-decoration: underline;">Descripción</th>
                            <th style="padding: 12px 16px; font-weight: bold; color: #475b75; text-decoration: underline;">Nombre de autoridad</th>
                            <th style="padding: 12px 16px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservas as $reserva)
                        <tr style="border-bottom: 1px solid #e5e7eb; color: #4b5563; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='transparent'">
                            <td style="padding: 12px 16px;">{{ $reserva->salon }}</td>
                            <td style="padding: 12px 16px; white-space: nowrap;">
                                {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d-m-Y H:i') }}
                            </td>
                            <td style="padding: 12px 16px;">{{ $reserva->nombre_usuario ?? 'N/A' }}</td>
                            <td style="padding: 12px 16px; white-space: nowrap;">
                                {{ $reserva->fecha_creacion->format('Y-m-d H:i') }}
                            </td>
                            <td style="padding: 12px 16px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $reserva->titulo }}">
                                {{ $reserva->titulo }}
                            </td>
                            <td style="padding: 12px 16px;">{{ $reserva->nombre_autoridad }}</td>
                            <td style="padding: 12px 16px; text-align: right; color: #3b82f6; font-size: 15px; white-space: nowrap;">
                                <a class="tabla-icono-accion" href="{{ route('salones.protocolo', $reserva->id) }}" title="Documento Protocolo" aria-label="Ver documento protocolo"><i class="fa-regular fa-file-lines" aria-hidden="true"></i></a>
                                <a class="tabla-icono-accion" href="{{ route('salones.usar') }}" title="Ver en Calendario" aria-label="Ver en calendario"><i class="fa-regular fa-calendar" aria-hidden="true"></i></a>
                                <a class="tabla-icono-accion" href="{{ route('salones.usar', ['editar' => $reserva->id, 'fecha' => $reserva->fecha_inicio->format('Y-m-d')]) }}" title="Editar en el calendario" aria-label="Editar reserva"><i class="fa-regular fa-pen-to-square" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                        @endforeach
                        
                        @if($reservas->isEmpty())
                        <tr>
                            <td colspan="7" style="padding: 24px; text-align: center; color: #9ca3af;">No hay reservas registradas en el sistema.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Paginación numérica compacta, manteniendo el filtro seleccionado. --}}
            @if($reservas->lastPage() > 1)
                @php
                    if ($reservas->currentPage() <= 5) {
                        $paginaInicial = 1;
                        $paginaFinal = min(9, $reservas->lastPage());
                    } elseif ($reservas->currentPage() >= $reservas->lastPage() - 4) {
                        $paginaInicial = max(1, $reservas->lastPage() - 8);
                        $paginaFinal = $reservas->lastPage();
                    } else {
                        $paginaInicial = $reservas->currentPage() - 4;
                        $paginaFinal = $reservas->currentPage() + 4;
                    }
                @endphp
                <nav aria-label="Paginación de reservas" style="display: flex; align-items: center; gap: 6px; margin: -24px 0 40px;">
                    @if($reservas->onFirstPage())
                        <span aria-disabled="true" style="padding: 5px 8px; border: 1px solid #d1d5db; border-radius: 6px; color: #cbd5e1;">&#171;</span>
                    @else
                        <a href="{{ $reservas->previousPageUrl() }}" aria-label="Página anterior" style="padding: 5px 8px; border: 1px solid #cbd5e1; border-radius: 6px; color: #2563eb; text-decoration: none;">&#171;</a>
                    @endif

                    @if($paginaInicial > 1)
                        <a href="{{ $reservas->url(1) }}" style="padding: 5px 9px; border: 1px solid #cbd5e1; border-radius: 6px; color: #2563eb; text-decoration: none;">1</a>
                        @if($paginaInicial > 2)<span style="padding: 5px 3px; color: #475569;">…</span>@endif
                    @endif

                    @for($pagina = $paginaInicial; $pagina <= $paginaFinal; $pagina++)
                        @if($pagina === $reservas->currentPage())
                            <span aria-current="page" style="padding: 5px 9px; border: 1px solid #cbd5e1; border-radius: 6px; background: #cbd5e1; color: #334155; font-weight: bold;">{{ $pagina }}</span>
                        @else
                            <a href="{{ $reservas->url($pagina) }}" style="padding: 5px 9px; border: 1px solid #cbd5e1; border-radius: 6px; color: #2563eb; text-decoration: none;">{{ $pagina }}</a>
                        @endif
                    @endfor

                    @if($paginaFinal < $reservas->lastPage())
                        @if($paginaFinal < $reservas->lastPage() - 1)<span style="padding: 5px 3px; color: #475569;">…</span>@endif
                        <a href="{{ $reservas->url($reservas->lastPage()) }}" style="padding: 5px 9px; border: 1px solid #cbd5e1; border-radius: 6px; color: #2563eb; text-decoration: none;">{{ $reservas->lastPage() }}</a>
                    @endif

                    @if($reservas->hasMorePages())
                        <a href="{{ $reservas->nextPageUrl() }}" aria-label="Página siguiente" style="padding: 5px 8px; border: 1px solid #cbd5e1; border-radius: 6px; color: #2563eb; text-decoration: none;">&#187;</a>
                    @else
                        <span aria-disabled="true" style="padding: 5px 8px; border: 1px solid #d1d5db; border-radius: 6px; color: #cbd5e1;">&#187;</span>
                    @endif
                </nav>
            @endif

            @if($ultimoCambio)
                <p style="margin: -24px 0 32px; color: #64748b; font-size: 14px;">
                    Último cambio:
                    {{ ($ultimoCambio->fecha_actualizacion ?? $ultimoCambio->fecha_creacion)->format('Y-m-d H:i') }}
                    por {{ $ultimoCambio->modificado_por ?? $ultimoCambio->nombre_usuario ?? 'Administrador' }}
                    para {{ $ultimoCambio->salon }}
                </p>
            @endif

            </div>
            <div style="max-width: 1400px; margin: 0 auto; padding: 0 16px;">
            <!-- TABLA INFERIOR DE FUNCIONES -->
            <div style="max-width: 550px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px; text-align: left; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden;">
                    <thead>
                        <tr style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <th style="padding: 12px 16px; font-weight: bold; color: #475b75;">Función</th>
                            <th style="padding: 12px 16px; font-weight: bold; color: #475b75;">Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px 16px;">
                                <a href="#" style="background-color: #c7d2fe; color: #4338ca; padding: 4px 10px; border-radius: 4px; text-decoration: none; font-size: 13px;">Bloquear horario</a>
                            </td>
                            <td style="padding: 12px 16px; color: #4b5563;">Bloquear horario para usuarios</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px 16px;">
                                <a href="{{ route('salones.usuarios') }}" style="color: #2563eb; text-decoration: none;">Gestión de usuarios</a>
                            </td>
                            <td style="padding: 12px 16px; color: #4b5563;">Añadir o borrar usuarios y superusuarios</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px 16px;">
                                <a href="{{ route('salones.papelera') }}" style="color: #2563eb; text-decoration: none;">Mostrar papelera</a>
                            </td>
                            <td style="padding: 12px 16px; color: #4b5563;">Mostrar reservas recién borradas</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 16px;">
                                <a href="#" style="color: #2563eb; text-decoration: none;">Descargar horario</a>
                            </td>
                            <td style="padding: 12px 16px; color: #4b5563;">Descargar horario en formato Excel o CSV</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
        </div>
    </div>
</x-app-layout>
