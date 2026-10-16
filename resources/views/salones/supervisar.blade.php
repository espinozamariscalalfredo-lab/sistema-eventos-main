{{-- Cambio al español: 12/10/2026. La vista usa nombres de reservas y campos en español. --}}
<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Usamos la versión core normal de FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/es.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .tabla-icono-accion { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; margin-left: 4px; border: 0; border-radius: 7px; background: transparent; color: #3b82f6; font-size: 16px; text-decoration: none; cursor: pointer; transition: background-color .15s ease, color .15s ease; }
        .tabla-icono-accion:hover, .tabla-icono-accion:focus-visible { background: #eff6ff; color: #1d4ed8; outline: none; }
        /* Estilo para cuando agarras una columna para moverla */
        th.arrastrable { cursor: grab; user-select: none; transition: background-color 0.2s; }
        th.arrastrable:active { cursor: grabbing; background-color: #e2e8f0; }
        .drag-over { border-left: 3px solid #2563eb !important; }
        
        .fc .fc-toolbar { display: none !important; }
        .fc-theme-standard th, .fc-theme-standard td { border-color: #e2e8f0; }
        .fc-theme-standard th { border-top: none !important; padding-bottom: 8px; border-left: none !important; border-right: none !important; }
        .fc-scrollgrid { border: none !important; }
        
        .fc .fc-day-today { background-color: #fafafa !important; }
        .fc-dayGridMonth-view .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 26px;
            height: 26px;
            margin: 3px;
            padding: 0 6px;
            border-radius: 9999px;
            background-color: #2563eb;
            color: #fff !important;
            font-weight: 700;
        }
        
        .fc-timegrid-axis-cushion { display: none !important; }
        .fc-timegrid-slot-label-cushion { font-size: 0.8rem; color: #475b75; }
        .fc-reserva-contenido { width: 100%; overflow: hidden; white-space: normal; overflow-wrap: anywhere; line-height: 1.12; }
        .fc-reserva-hora { margin-bottom: 3px; font-weight: 800; }
        .fc-reserva-titulo { font-weight: 600; }
        .fc-reserva-detalle { font-size: 0.92em; }
        
        .fc-dayGridMonth-view .fc-event {
            border-radius: 99px !important; 
            border: none !important;
            margin-bottom: 2px !important; 
            overflow: hidden;
            box-shadow: none !important;
        }
        
        .fc-dayGridMonth-view .fc-event-main {
            padding: 2px 6px !important; 
            display: flex;
            align-items: center;
        }

        .fc-dayGridMonth-view .fc-reserva-contenido {
            display: flex;
            flex-direction: row; 
            align-items: center;
            width: 100%;
            gap: 4px; 
            line-height: 1;
        }

        .fc-dayGridMonth-view .fc-reserva-hora {
            font-weight: 800;
            font-size: 10px; 
            margin-bottom: 0 !important; 
            color: white;
            white-space: nowrap;
        }

        .fc-dayGridMonth-view .fc-reserva-titulo {
            font-weight: 500;
            font-size: 10px;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis; 
        }
        
        .fc-dayGridMonth-view .fc-daygrid-day-frame {
            height: 140px !important; 
            display: flex;
            flex-direction: column;
        }

        .fc-dayGridMonth-view .fc-daygrid-day-events {
            flex-grow: 1;
            max-height: 105px !important; 
            overflow-y: auto !important; 
            margin-bottom: 2px;
        }

        .fc-dayGridMonth-view .fc-daygrid-day-events::-webkit-scrollbar {
            width: 4px; 
        }
        .fc-dayGridMonth-view .fc-daygrid-day-events::-webkit-scrollbar-track {
            background: transparent; 
        }
        .fc-dayGridMonth-view .fc-daygrid-day-events::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 10px;
        }
        .fc-dayGridMonth-view .fc-daygrid-day-events::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; 
        }
        .fc .fc-event.reserva-finalizada { opacity: 0.55; }

        .mini-cal { width: 100%; text-align: center; font-size: 13px; border-collapse: separate; border-spacing: 2px; }
        .mini-cal th { color: #9ca3af; font-weight: normal; font-size: 12px; padding-bottom: 4px; }
        .mini-cal td { padding: 5px 0; border-radius: 4px; cursor: pointer; color: #4b5563; font-weight: 500; transition: background 0.2s; background-color: #ffffff; }
        
        .mc-has-event { background-color: #f59e0b !important; color: white !important; font-weight: bold; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .mc-selected-date { position: relative; z-index: 2; outline: 2px solid #2563eb; outline-offset: 2px; border-radius: 5px !important; color: white !important; font-weight: bold !important; }
        .mc-selected-date:not(.mc-has-event) { background-color: #eab308 !important; }
        .mc-hover:hover { background-color: #f3f4f6 !important; }

        .salon-filter { border: 0; font: inherit; color: white; font-weight: bold; cursor: pointer; transition: box-shadow 0.15s, transform 0.15s; }
        .salon-filter:hover { transform: translateY(-1px); }
        .salon-filter:not(.active) .salon-filter-check { display: none; }
        .salon-filter.active { box-shadow: 0 0 0 2px white, 0 0 0 4px #334155; }
        
        .mc-today-real {
            border: 2px solid #2563eb !important;
            color: #2563eb !important;
            font-weight: bold;
        }

        .mc-row-selected td {
            background-color: #e2e8f0 !important;
        }
        .mc-row-selected td.mc-today-real {
            background-color: #ffffff !important;
            border: 2px solid #2563eb !important;
        }
        .mc-row-selected td.mc-has-event {
            background-color: #f59e0b !important;
        }

        @media (max-width: 900px) {
            .salones-cal-layout { flex-direction: column !important; gap: 16px !important; }
            .salones-cal-main, .salones-cal-side { width: 100% !important; min-width: 0 !important; }
            .salones-cal-side { padding: 12px !important; }
        }
        @media (max-width: 640px) {
            .salones-page-header, .salones-cal-toolbar { flex-wrap: wrap; gap: 12px; }
            .salones-page-header { align-items: center !important; }
            .salones-cal-toolbar { justify-content: flex-start !important; }
            .salones-calendar-scroll { overflow-x: auto; }
            .salones-calendar-scroll #calendar { min-width: 560px; }
            .edit-modal-panel { max-height: calc(100dvh - 24px) !important; }
        }
        .edit-modal-drag-handle { cursor: move; touch-action: none; user-select: none; }
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
            </div>

            <div style="border: 1px solid #cbd5e1; border-radius: 11px; padding: 16px; background: #fff;">
                <h2 style="font-size: 22px; font-weight: bold; color: #374151; margin-bottom: 16px;">Resumen</h2>

                <p style="font-size: 14px; color: #4b5563; margin-bottom: 8px;">Este horario se encuentra en esta dirección web:</p>
                <a href="{{ route('salones.usar') }}" style="font-size: 15px; color: #2563eb; font-weight: bold; text-decoration: none; display: flex; align-items: center; gap: 6px; margin-bottom: 24px;">
                    {{ url('/salones/usar') }} <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 12px;"></i>
                </a>

                <!-- Panel de Herramientas Dinámicas -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
                    
                    <div style="display: flex; align-items: center; gap: 20px; font-size: 14px;">
                        
                        <!-- Filtro por salón -->
                        <form method="GET"
                            action="{{ route('salones.supervisar') }}"
                            style="display: flex; align-items: center; gap: 6px;">

                            <label for="filtro-salon-supervisar"
                                style="color: #4b5563;">
                                Salón:
                            </label>

                            <select id="filtro-salon-supervisar"
                                    name="salon"
                                    onchange="this.form.submit()"
                                    style="border: 1px solid #d1d5db;
                                        border-radius: 4px;
                                        padding: 4px 8px;
                                        font-size: 13px;
                                        color: #374151;">

                                <option value=""
                                    {{ empty($salonSeleccionado) ? 'selected' : '' }}>
                                    Todos
                                </option>

                                @foreach($salones as $salon)
                                    <option value="{{ $salon->nombre }}"
                                        {{ $salonSeleccionado === $salon->nombre ? 'selected' : '' }}>
                                        {{ $salon->nombre }}
                                    </option>
                                @endforeach
                            </select>

                            <noscript>
                                <button type="submit">Filtrar</button>
                            </noscript>
                        </form>

                        <!-- NUEVO: Filtro independiente por responsable -->
                        <form method="GET"
                            action="{{ route('salones.supervisar') }}"
                            style="display: flex; align-items: center; gap: 6px;">

                            <label for="filtro-responsable-supervisar"
                                style="color: #4b5563; white-space: nowrap;">
                                Responsable:
                            </label>

                            <select id="filtro-responsable-supervisar"
                                    name="responsable"
                                    onchange="this.form.submit()"
                                    style="border: 1px solid #d1d5db;
                                        border-radius: 4px;
                                        padding: 4px 8px;
                                        font-size: 13px;
                                        color: #374151;
                                        max-width: 230px;">

                                <option value=""
                                    {{ empty($responsableSeleccionado) ? 'selected' : '' }}>
                                    Todos
                                </option>

                                @foreach($responsables as $responsable)
                                    <option value="{{ $responsable }}"
                                        {{ $responsableSeleccionado === $responsable ? 'selected' : '' }}>
                                        {{ $responsable }}
                                    </option>
                                @endforeach
                            </select>

                            <noscript>
                                <button type="submit">Filtrar</button>
                            </noscript>
                        </form>
                        <a href="{{ route('salones.papelera') }}" style="color: #3b82f6; text-decoration: underline;">Mostrar papelera</a>
                    </div>

                    <!-- Nuevos Botones de Control (Columnas y Exportar) -->
                    <div style="display: flex; gap: 12px; align-items: center;">
                        
                        <!-- Botón Desplegable de Columnas -->
                        <div style="position: relative;" id="contenedor-columnas">
                            <button type="button" id="btn-columnas" style="background-color: white; border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #374151; display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                <i class="fa-solid fa-table-columns"></i> Columnas <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
                            </button>
                            <!-- Menú flotante de checkboxes -->
                            <div id="menu-columnas" style="display: none; position: absolute; top: 100%; right: 0; margin-top: 4px; background: white; border: 1px solid #e5e7eb; border-radius: 6px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); z-index: 50; padding: 12px; width: 220px; max-height: 300px; overflow-y: auto;">
                                <div style="font-size: 11px; font-weight: bold; color: #9ca3af; margin-bottom: 8px; text-transform: uppercase;">Mostrar / Ocultar</div>
                                <!-- Los checkboxes se generan aquí por JavaScript -->
                            </div>
                        </div>

                        <!-- Botón Exportar Vista Actual -->
                        <button type="button" id="btn-exportar-js" style="background-color: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 4px; border: 1px solid #bbf7d0; font-size: 13px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-download"></i> Descargar Vista
                        </button>
                    </div>

                </div>

                <!-- TABLA PRINCIPAL DE RESERVAS -->
                <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; margin-bottom: 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="overflow-x: auto;">
                        <table id="tabla-dinamica" style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                            <thead>
                                <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                    <th class="arrastrable" style="padding: 12px 16px; font-weight: 600; color: #475b75; white-space: nowrap;" title="Arrastra para mover">Salón</th>
                                    <th class="arrastrable" style="padding: 12px 16px; font-weight: 600; color: #475b75; white-space: nowrap;" title="Arrastra para mover">Fecha y Hora</th>
                                    <th class="arrastrable" style="padding: 12px 16px; font-weight: 600; color: #475b75; white-space: nowrap;" title="Arrastra para mover">Descripción del Evento</th>
                                    <th class="arrastrable" style="padding: 12px 16px; font-weight: 600; color: #475b75; white-space: nowrap;" title="Arrastra para mover">Autoridad</th>
                                    <th class="arrastrable" style="padding: 12px 16px; font-weight: 600; color: #475b75; white-space: nowrap;" title="Arrastra para mover">Responsable</th>
                                    <th class="arrastrable" style="padding: 12px 16px; font-weight: 600; color: #475b75; white-space: nowrap;" title="Arrastra para mover">Tipo / Aforo</th>
                                    <th class="arrastrable" style="padding: 12px 16px; font-weight: 600; color: #475b75; white-space: nowrap;" title="Arrastra para mover">Ingreso</th>
                                    <th class="arrastrable" style="padding: 12px 16px; font-weight: 600; color: #475b75; white-space: nowrap;" title="Arrastra para mover">Registrado por</th>
                                    <th data-exclude="true" style="padding: 12px 16px; font-weight: 600; color: #475b75; text-align: center; white-space: nowrap;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reservas as $reserva)
                                <tr style="border-bottom: 1px solid #e5e7eb; color: #334155; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f1f5f9'" onmouseout="this.style.backgroundColor='transparent'">
                                    
                                    <td style="padding: 12px 16px; font-weight: 500; white-space: nowrap;">
                                        <span style="background-color: #e2e8f0; padding: 4px 8px; border-radius: 4px; font-size: 12px;">{{ $reserva->salon }}</span>
                                    </td>
                                    
                                    <td style="padding: 12px 16px; white-space: nowrap;">
                                        <div style="font-weight: 500; color: #1e293b;">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d-m-Y') }}</div>
                                        <div style="font-size: 12px; color: #64748b;">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('H:i') }} a {{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('H:i') }}</div>
                                    </td>
                                    
                                    <td style="padding: 12px 16px; max-width: 250px;">
                                        <div style="font-weight: 500; overflow: hidden; text-overflow: ellipsis;" title="{{ $reserva->titulo }}">{{ $reserva->titulo }}</div>
                                        @if($reserva->requerimientos_especiales)
                                            <div style="font-size: 11px; color: #64748b; margin-top: 2px; overflow: hidden; text-overflow: ellipsis;" title="{{ $reserva->requerimientos_especiales }}">
                                                <i class="fa-solid fa-circle-info" style="font-size: 10px; margin-right: 3px;"></i> {{ $reserva->requerimientos_especiales }}
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <td style="padding: 12px 16px; white-space: normal;">
                                        <div title="{{ $reserva->nombre_autoridad }}">{{ $reserva->nombre_autoridad ?: '-' }}</div>
                                    </td>

                                    <td style="padding: 12px 16px; white-space: normal;">
                                        <div style="font-weight: 500;">{{ $reserva->responsable ?: '-' }}</div>
                                        @if($reserva->nombre_coordinador_externo)
                                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;" title="Coord. Externo">
                                                <i class="fa-solid fa-user-tie" style="font-size: 10px;"></i> {{ $reserva->nombre_coordinador_externo }}
                                                @if($reserva->telefono_coordinador_externo) ({{ $reserva->telefono_coordinador_externo }}) @endif
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <td style="padding: 12px 16px; white-space: nowrap;">
                                        <div>{{ $reserva->tipo_evento ?: '-' }}</div>
                                        <div style="font-size: 12px; color: #64748b;"><i class="fa-solid fa-users" style="font-size: 10px;"></i> {{ $reserva->aforo ? $reserva->aforo . ' personas' : '-' }}</div>
                                    </td>

                                    <td style="padding: 12px 16px; white-space: nowrap;">
                                        @if($reserva->tipo_ingreso == 'Bajo lista')
                                            <span style="color: #b45309; background-color: #fef3c7; padding: 2px 6px; border-radius: 4px; font-size: 12px;"><i class="fa-solid fa-clipboard-list"></i> Lista</span>
                                        @elseif($reserva->tipo_ingreso == 'Con invitación')
                                            <span style="color: #1d4ed8; background-color: #dbeafe; padding: 2px 6px; border-radius: 4px; font-size: 12px;"><i class="fa-solid fa-envelope-open-text"></i> Invitación</span>
                                        @else
                                            <span style="color: #15803d; background-color: #dcfce7; padding: 2px 6px; border-radius: 4px; font-size: 12px;"><i class="fa-solid fa-door-open"></i> Libre</span>
                                        @endif
                                    </td>

                                    <td style="padding: 12px 16px; white-space: nowrap;">
                                        <div>{{ $reserva->registrado_por ?: ($reserva->nombre_usuario ?: '-') }}</div>
                                        <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">{{ $reserva->fecha_creacion->format('d/m/y H:i') }}</div>
                                    </td>
                                    
                                    <td style="padding: 12px 16px; text-align: center; color: #3b82f6; font-size: 15px; white-space: nowrap;">
                                        <a class="tabla-icono-accion" href="{{ route('salones.protocolo', $reserva->id) }}" title="Documento Protocolo" aria-label="Ver documento protocolo"><i class="fa-regular fa-file-lines" aria-hidden="true"></i></a>
                                        <a class="tabla-icono-accion" href="{{ route('salones.usar') }}" title="Ver en Calendario" aria-label="Ver en calendario"><i class="fa-regular fa-calendar" aria-hidden="true"></i></a>
                                        <a class="tabla-icono-accion" href="{{ route('salones.usar', ['editar' => $reserva->id, 'fecha' => $reserva->fecha_inicio->format('Y-m-d')]) }}" title="Editar en el calendario" aria-label="Editar reserva"><i class="fa-regular fa-pen-to-square" aria-hidden="true"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                                
                                @if($reservas->isEmpty())
                                <tr>
                                    <td colspan="9" style="padding: 32px; text-align: center; color: #9ca3af; font-size: 14px;">
                                        <i class="fa-regular fa-folder-open" style="font-size: 24px; margin-bottom: 8px; display: block;"></i>
                                        No hay reservas registradas en el sistema.
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Paginación y Resto del código... --}}
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
            
            <div style="max-width: 1400px; margin: 0 auto; padding: 0 16px; margin-top: 30px;">
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
                            <tr>
                                <td style="padding: 12px 16px;">
                                    <a href="{{ route('salones.papelera') }}" style="color: #2563eb; text-decoration: none;">Mostrar papelera</a>
                                </td>
                                <td style="padding: 12px 16px; color: #4b5563;">Mostrar reservas recién borradas</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALES Y SCRIPTS -->
    <div id="booking-modal" style="display: none; position: fixed; inset: 0; background: transparent; z-index: 100;">
        <div id="booking-modal-panel" class="edit-modal-panel" style="position: fixed; top: 10vh; left: 5vw; width: min(650px, calc(100vw - 32px)); max-height: 86vh; display: flex; flex-direction: column; background: rgba(255,255,255,0.94); backdrop-filter: blur(14px); border: 1px solid rgba(203,213,225,0.8); border-radius: 10px; box-shadow: 0 18px 50px rgba(15,23,42,0.18); font-family: sans-serif;">
            
            <div id="booking-modal-drag-handle" class="edit-modal-drag-handle" style="position: sticky; top: 0; z-index: 1; padding: 16px 24px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background: rgba(248,250,252,0.92); backdrop-filter: blur(14px); flex-shrink: 0;">
                <h3 style="font-size: 16px; font-weight: bold; color: #475b75; margin: 0;">Nueva reserva y protocolo</h3>
                <button id="close-modal" type="button" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #64748b; line-height: 1; padding: 0;">&times;</button>
            </div>

            <form id="booking-form" style="display: flex; flex-direction: column; overflow: hidden; flex-grow: 1;">
                @csrf
                <div style="padding: 24px; overflow-y: auto; flex-grow: 1;">
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
                        <div style="width: 24px; text-align: center; color: #64748b; font-size: 18px;">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                            <input type="text" id="modal-start" name="fecha_inicio" style="flex: 1; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 8px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                            <span style="font-size: 13px; color: #475b75;">hasta</span>
                            <input type="text" id="modal-end" name="fecha_fin" style="flex: 1; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 8px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <div style="width: 24px; text-align: center; color: #64748b; font-size: 18px; padding-top: 10px;">
                            <i class="fa-solid fa-align-left"></i>
                        </div>
                        <div style="flex: 1;">
                            <textarea id="modal-title" name="titulo" maxlength="150" placeholder="Descripción *" rows="2" style="width: 100%; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 8px 12px; font-size: 13px; color: #1e293b; outline: none; resize: none;" required></textarea>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
                        <div style="width: 24px; text-align: center; color: #64748b; font-size: 18px;">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div style="flex: 1;">
                            <input type="text" maxlength="70" id="modal-username" name="nombre_usuario" value="{{ Auth::user()->nombre }}" placeholder="Nombre de usuario" style="width: 100%; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 8px 12px; font-size: 13px; color: #1e293b; outline: none;">
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 8px;">
                        <div style="width: 24px; text-align: center; color: #64748b; font-size: 18px;">
                            <i class="fa-solid fa-mobile-screen"></i>
                        </div>
                        <div style="flex: 1;">
                            <input type="tel" id="modal-phone" name="telefono_usuario" placeholder="Teléfono" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" style="width: 100%; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 8px 12px; font-size: 13px; color: #1e293b; outline: none;">
                        </div>
                    </div>
                    <div style="height: 1px; background-color: #e5e7eb; margin: 20px -24px;"></div>
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Nombre de autoridad *</label>
                        <div style="flex: 1;">
                            <input type="text" maxlength="50" name="nombre_autoridad" style="width: 100%; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563; padding-top: 10px;">Responsable del evento *</label>
                        <div style="flex: 1;">
                            <textarea name="responsable" maxlength="40" rows="2" style="width: 100%; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none; resize: none;" required></textarea>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Salones *</label>
                        <div style="flex: 1;">
                            <select name="salon" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                                <option value="" disabled selected>Seleccione un salón...</option>
                                @foreach($salones as $salon)
                                    <option value="{{ $salon->nombre }}" {{ isset($salon->activo) && !$salon->activo ? 'disabled' : '' }}>
                                        {{ $salon->nombre }}{{ isset($salon->activo) && !$salon->activo ? ' (inactivo)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Tipo de evento *</label>
                        <div style="flex: 1;">
                            <select name="tipo_evento" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                                <option value="Visita guiada">Visita guiada</option>
                                <option value="Seminario">Seminario</option>
                                <option value="Taller">Taller</option>
                                <option value="Reunión">Reunión</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Requerimientos *<br><small style="color:#9ca3af; font-weight:normal;">Recomiende según tipo de evento</small></label>
                        <div style="flex: 1;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Sonido"> Sonido</label>
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Maestro de ceremonias"> Maestro de ceremonias</label>
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Tiene refrigerio"> Tiene refrigerio</label>
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Prensa"> Prensa</label>
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Testera"> Testera</label>
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Atril"> Atril</label>
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Protocolo sala"> Protocolo sala</label>
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Protocolo antesala"> Protocolo antesala</label>
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Guía"> Guía</label>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Aforo *<br><small style="color:#9ca3af; font-weight:normal;">Cantidad de personas en el evento</small></label>
                        <div style="flex: 1;">
                            <input type="number" name="aforo" placeholder="Ej: 77" min="1" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Tipo de ingreso *<br><small style="color:#9ca3af; font-weight:normal;">Como ingresaran al edificio</small></label>
                        <div style="flex: 1;">
                            <select name="tipo_ingreso" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                                <option value="Bajo lista">Bajo lista</option>
                                <option value="Libre">Libre</option>
                                <option value="Con invitación">Con invitación</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Nombre del coordinador externo *<br><small style="color:#9ca3af; font-weight:normal;">Nombre completo</small></label>
                        <div style="flex: 1;">
                            <input type="text" name="nombre_coordinador_externo" placeholder="" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Celular del coordinador externo *</label>
                        <div style="flex: 1;">
                            <input type="tel" name="telefono_coordinador_externo" placeholder="" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Requerimiento especial<br><small style="color:#9ca3af; font-weight:normal;">Personal o característica</small></label>
                        <div style="flex: 1;">
                            <input type="text" name="requerimientos_especiales" placeholder="" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;">
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Nombre de quien registra *</label>
                        <div style="flex: 1;">
                            <input type="text" name="registrado_por" value="{{ Auth::user()->nombre }}" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Nombre coordinador interno</label>
                        <div style="flex: 1;">
                            <input type="text" name="coordinador_interno" placeholder="" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;">
                        </div>
                    </div>
                </div>
                <div style="padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 12px; flex-shrink: 0; background: rgba(248,250,252,0.92); backdrop-filter: blur(14px);">
                    <button type="button" id="btn-cancel" style="background: white; border: 1px solid #d1d5db; color: #374151; padding: 6px 16px; border-radius: 4px; font-size: 13px; cursor: pointer;">Cancelar</button>
                    <button type="submit" style="background: #2563eb; color: white; border: none; padding: 6px 16px; border-radius: 4px; font-size: 13px; font-weight: bold; cursor: pointer;">Crear reserva</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL FLOTANTE UNIFICADO "EDITAR RESERVA" -->
    <div id="edit-booking-modal" style="display: none; position: fixed; inset: 0; background: transparent; z-index: 100;">
        <div id="edit-modal-panel" class="edit-modal-panel" style="position: fixed; top: 10vh; left: 5vw; width: min(650px, calc(100vw - 32px)); max-height: 86vh; display: flex; flex-direction: column; background: rgba(255,255,255,0.94); backdrop-filter: blur(14px); border: 1px solid rgba(203,213,225,0.8); border-radius: 10px; box-shadow: 0 18px 50px rgba(15,23,42,0.18); font-family: sans-serif;">
            
            <div id="edit-modal-drag-handle" class="edit-modal-drag-handle" style="position: sticky; top: 0; z-index: 1; padding: 16px 24px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background: rgba(248,250,252,0.92); backdrop-filter: blur(14px); flex-shrink: 0;">
                <h3 style="font-size: 16px; font-weight: bold; color: #475b75; margin: 0;">Editar reserva y protocolo</h3>
                <button id="close-edit-modal" type="button" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #64748b; line-height: 1; padding: 0;">&times;</button>
            </div>

            <form id="edit-booking-form" style="display: flex; flex-direction: column; overflow: hidden; flex-grow: 1;">
                @csrf
                <input type="hidden" id="edit-event-id" name="id">
                
                <div style="padding: 24px; overflow-y: auto; flex-grow: 1;">
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
                        <div style="width: 24px; text-align: center; color: #64748b; font-size: 18px;">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                            <input type="text" id="edit-start" name="fecha_inicio" style="flex: 1; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 8px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                            <span style="font-size: 13px; color: #475b75;">hasta</span>
                            <input type="text" id="edit-end" name="fecha_fin" style="flex: 1; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 8px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <div style="width: 24px; text-align: center; color: #64748b; font-size: 18px; padding-top: 10px;">
                            <i class="fa-solid fa-align-left"></i>
                        </div>
                        <div style="flex: 1;">
                            <textarea id="edit-title" name="titulo" maxlength="150" placeholder="Descripción *" rows="2" style="width: 100%; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 8px 12px; font-size: 13px; color: #1e293b; outline: none; resize: none;" required></textarea>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
                        <div style="width: 24px; text-align: center; color: #64748b; font-size: 18px;">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div style="flex: 1;">
                            <input type="text" maxlength="70" id="edit-username" name="nombre_usuario" placeholder="Nombre de usuario" style="width: 100%; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 8px 12px; font-size: 13px; color: #1e293b; outline: none;">
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 8px;">
                        <div style="width: 24px; text-align: center; color: #64748b; font-size: 18px;">
                            <i class="fa-solid fa-mobile-screen"></i>
                        </div>
                        <div style="flex: 1;">
                            <input type="tel" id="edit-phone" name="telefono_usuario" placeholder="Teléfono" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" style="width: 100%; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 8px 12px; font-size: 13px; color: #1e293b; outline: none;">
                        </div>
                    </div>
                    <div style="height: 1px; background-color: #e5e7eb; margin: 20px -24px;"></div>
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Nombre de autoridad *</label>
                        <div style="flex: 1;">
                            <input type="text" id="edit-authority" maxlength="50" name="nombre_autoridad" style="width: 100%; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563; padding-top: 10px;">Responsable del evento *</label>
                        <div style="flex: 1;">
                            <textarea id="edit-responsable" name="responsable" maxlength="40" rows="2" style="width: 100%; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none; resize: none;" required></textarea>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Salones *</label>
                        <div style="flex: 1;">
                            <select id="edit-salon" name="salon" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                                @foreach($salones as $salon)
                                    <option value="{{ $salon->nombre }}" {{ !$salon->activo ? 'disabled' : '' }}>{{ $salon->nombre }}{{ !$salon->activo ? ' (inactivo)' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Tipo de evento *</label>
                        <div style="flex: 1;">
                            <select id="edit-tipo-evento" name="tipo_evento" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                                <option value="Visita guiada">Visita guiada</option>
                                <option value="Seminario">Seminario</option>
                                <option value="Taller">Taller</option>
                                <option value="Reunión">Reunión</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Requerimientos *<br><small style="color:#9ca3af; font-weight:normal;">Recomiende según tipo</small></label>
                        <div style="flex: 1;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Sonido"> Sonido</label>
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Maestro de ceremonias"> Maestro de ceremonias</label>
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Tiene refrigerio"> Tiene refrigerio</label>
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Prensa"> Prensa</label>
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Testera"> Testera</label>
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Atril"> Atril</label>
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Protocolo sala"> Protocolo sala</label>
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Protocolo antesala"> Protocolo antesala</label>
                                <label style="font-size: 13px; color: #4b5563; display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="requerimientos[]" value="Guía"> Guía</label>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Aforo *</label>
                        <div style="flex: 1;">
                            <input type="number" id="edit-aforo" name="aforo" min="1" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Tipo de ingreso *</label>
                        <div style="flex: 1;">
                            <select id="edit-tipo-ingreso" name="tipo_ingreso" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                                <option value="Bajo lista">Bajo lista</option>
                                <option value="Libre">Libre</option>
                                <option value="Con invitación">Con invitación</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Coord. externo *</label>
                        <div style="flex: 1;">
                            <input type="text" id="edit-coord-ext" name="nombre_coordinador_externo" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Teléfono externo *</label>
                        <div style="flex: 1;">
                            <input type="tel" id="edit-tel-coord-ext" name="telefono_coordinador_externo" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Requerimiento especial</label>
                        <div style="flex: 1;">
                            <input type="text" id="edit-req-esp" name="requerimientos_especiales" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;">
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Nombre quien registra *</label>
                        <div style="flex: 1;">
                            <input type="text" id="edit-registrado-por" name="registrado_por" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Coordinador interno</label>
                        <div style="flex: 1;">
                            <input type="text" id="edit-coord-int" name="coordinador_interno" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;">
                        </div>
                    </div>
                </div>
                <div style="padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; background: rgba(248,250,252,0.92); backdrop-filter: blur(14px);">
                    <div>
                        <span id="edit-footer-text" style="font-size: 11px; color: #9ca3af;"></span>
                    </div>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <button type="button" id="btn-edit-cancel" style="background: white; border: 1px solid #d1d5db; color: #374151; padding: 6px 16px; border-radius: 4px; font-size: 13px; cursor: pointer;">Cerrar</button>
                        
                        @if(auth()->user()->rol === 'admin' || auth()->user()->rol === 'superadmin')
                            <button type="submit" id="btn-edit-update" style="background: #2563eb; color: white; border: none; padding: 6px 16px; border-radius: 4px; font-size: 13px; font-weight: bold; cursor: pointer;">Actualizar reserva</button>
                        @endif

                        @if(auth()->user()->rol === 'admin')
                            <button type="button" id="btn-edit-delete" style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;" title="Eliminar reserva">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT MÁGICO DE TABLA DINÁMICA Y EXPORTACIÓN -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabla = document.getElementById('tabla-dinamica');
            const menuColumnas = document.getElementById('menu-columnas');
            const btnColumnas = document.getElementById('btn-columnas');
            const cabeceras = tabla.querySelectorAll('thead th');

            // 1. Mostrar/Ocultar Menú
            btnColumnas.addEventListener('click', (e) => {
                e.stopPropagation();
                menuColumnas.style.display = menuColumnas.style.display === 'none' ? 'block' : 'none';
            });
            document.addEventListener('click', (e) => {
                if (!btnColumnas.contains(e.target) && !menuColumnas.contains(e.target)) {
                    menuColumnas.style.display = 'none';
                }
            });

            // 2. Generar Checkboxes y Drag & Drop
            cabeceras.forEach((th, index) => {
                if (th.dataset.exclude === "true") return;

                const label = document.createElement('label');
                label.style.cssText = 'display: flex; align-items: center; gap: 8px; font-size: 13px; color: #4b5563; margin-bottom: 8px; cursor: pointer;';
                
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.checked = true;
                checkbox.addEventListener('change', (e) => toggleColumna(index, e.target.checked));

                label.appendChild(checkbox);
                label.appendChild(document.createTextNode(th.innerText.trim()));
                menuColumnas.appendChild(label);

                th.draggable = true;
                th.addEventListener('dragstart', handleDragStart);
                th.addEventListener('dragover', handleDragOver);
                th.addEventListener('dragleave', handleDragLeave);
                th.addEventListener('drop', handleDrop);
            });

            function toggleColumna(indiceAfectado, esVisible) {
                const filas = tabla.querySelectorAll('tr');
                filas.forEach(fila => {
                    const celdas = fila.children;
                    if(celdas[indiceAfectado]) {
                        celdas[indiceAfectado].style.display = esVisible ? '' : 'none';
                    }
                });
            }

            let columnaArrastradaIndex;

            function handleDragStart(e) {
                columnaArrastradaIndex = Array.from(this.parentNode.children).indexOf(this);
                e.dataTransfer.effectAllowed = 'move';
            }

            function handleDragOver(e) {
                e.preventDefault();
                this.classList.add('drag-over');
            }

            function handleDragLeave(e) {
                this.classList.remove('drag-over');
            }

            function handleDrop(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                const columnaDestinoIndex = Array.from(this.parentNode.children).indexOf(this);
                
                if (columnaArrastradaIndex === columnaDestinoIndex) return;

                const filas = tabla.querySelectorAll('tr');
                filas.forEach(fila => {
                    const celdas = Array.from(fila.children);
                    if(celdas.length === 0) return;
                    
                    const celdaMover = celdas[columnaArrastradaIndex];
                    const celdaDestino = celdas[columnaDestinoIndex];
                    
                    if (columnaArrastradaIndex < columnaDestinoIndex) {
                        fila.insertBefore(celdaMover, celdaDestino.nextSibling);
                    } else {
                        fila.insertBefore(celdaMover, celdaDestino);
                    }
                });
            }

            // 3. Exportador Inteligente a CSV (Basado en la Vista Actual)
            document.getElementById('btn-exportar-js').addEventListener('click', () => {
                let csv = [];
                const filas = tabla.querySelectorAll('tr');

                filas.forEach(fila => {
                    let datosFila = [];
                    const celdas = fila.querySelectorAll('th, td');
                    
                    celdas.forEach(celda => {
                        if (celda.style.display !== 'none' && !celda.querySelector('.tabla-icono-accion')) {
                            let texto = celda.innerText.replace(/(\r\n|\n|\r)/gm, " - ").trim();
                            datosFila.push('"' + texto.replace(/"/g, '""') + '"');
                        }
                    });
                    
                    if(datosFila.length > 0) csv.push(datosFila.join(';'));
                });
                
                const contenidoCsv = "\uFEFF" + csv.join("\n");
                const blob = new Blob([contenidoCsv], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const enlace = document.createElement('a');
                
                const filtro = document.getElementById('filtro-salon-supervisar').value;
                enlace.download = filtro ? `Reservas_${filtro}.csv` : 'Reservas_Completas.csv';
                
                enlace.href = url;
                enlace.click();
                URL.revokeObjectURL(url);
            });
        });
    </script>
</x-app-layout>