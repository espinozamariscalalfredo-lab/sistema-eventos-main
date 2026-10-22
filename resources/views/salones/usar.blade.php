{{-- FRONTEND. 20/10/2026: pantalla de calendario, vista Disponible, filtros y formularios. --}}
{{-- Cambio al español: 12/10/2026. La vista usa nombres de reservas y campos en español. --}}
<x-app-layout>
    {{-- LIBRERÍAS. 20/10/2026: FullCalendar, idioma español, Font Awesome y SweetAlert. --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/es.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ESTILOS. 20/10/2026: diseño del calendario, eventos, formularios y vista Disponible. */
        /* Calendario: encabezados, carriles, eventos y modo oscuro. */
        .fc .fc-toolbar { display: none !important; }
        .fc-theme-standard th, .fc-theme-standard td { border-color: #e2e8f0; }
        .fc-theme-standard th { border-top: none !important; padding-bottom: 8px; border-left: none !important; border-right: none !important; }
        .fc-scrollgrid { border: none !important; }
        
        .fc .fc-day-today { background-color: #fafafa !important; }
        .fc-dayGridMonth-view .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
            display: inline-flex; align-items: center; justify-content: center; min-width: 26px; height: 26px; margin: 3px; padding: 0 6px; border-radius: 9999px; background-color: #2563eb; color: #fff !important; font-weight: 700;
        }
        
        /* Vista día/semana: líneas de horario y contenido de las reservas. */
        .fc-timegrid-axis-cushion { display: none !important; }
        .fc-timegrid-slot-label-cushion { font-size: 0.8rem; color: #475b75; }
        .fc-reserva-contenido { width: 100%; overflow: hidden; white-space: normal; overflow-wrap: anywhere; line-height: 1.12; }
        .fc-reserva-hora { margin-bottom: 3px; font-weight: 800; }
        .fc-timeGridDay-view .fc-reserva-hora { display: block; margin-bottom: 3px; font-weight: 1000 !important; -webkit-text-stroke: 0.35px currentColor; white-space: normal; overflow-wrap: anywhere; }
        .fc-timeGridDay-view .fc-timegrid-event, .fc-timeGridDay-view .fc-timegrid-event .fc-event-main { overflow: hidden !important; }
        .fc-timeGridDay-view .fc-reserva-contenido { box-sizing: border-box; max-width: 100%; height: 100%; font-size: calc(1em - 2pt); overflow-x: hidden; overflow-y: auto; scrollbar-width: none; overscroll-behavior: contain; }
        .fc-timeGridDay-view .fc-reserva-contenido::-webkit-scrollbar { display: none; }
        .fc-timeGridDay-view .fc-reserva-titulo, .fc-timeGridDay-view .fc-reserva-detalle { font-weight: 400 !important; }
        .fc-reserva-titulo { font-weight: 600; }
        .fc-reserva-detalle { font-size: 0.92em; }
        
        /* Vista mes: tarjetas de eventos dentro de cada día. */
        .fc-dayGridMonth-view .fc-event { border-radius: 99px !important; border: none !important; margin-bottom: 2px !important; overflow: hidden; box-shadow: none !important; }
        .fc-dayGridMonth-view .fc-event-main { padding: 2px 6px !important; display: flex; align-items: center; }
        .fc-dayGridMonth-view .fc-reserva-contenido { display: flex; flex-direction: row; align-items: center; width: 100%; gap: 4px; line-height: 1; }
        .fc-dayGridMonth-view .fc-reserva-hora { font-weight: 800; font-size: 10px; margin-bottom: 0 !important; color: white; white-space: nowrap; }
        .fc-dayGridMonth-view .fc-reserva-titulo { font-weight: 500; font-size: 10px; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .fc-reserva-indicador-cancelado { display: inline-block; width: 6px; height: 6px; flex: 0 0 6px; border-radius: 50%; background: #dc2626; box-shadow: 0 0 0 1px rgba(255, 255, 255, .9); vertical-align: middle; }
        .fc-dayGridMonth-view .fc-reserva-titulo-cancelado { display: inline-flex; align-items: center; gap: 3px; min-width: 0; }
        .fc-dayGridMonth-view .fc-daygrid-day-frame { height: 140px !important; display: flex; flex-direction: column; }
        .fc-dayGridMonth-view .fc-daygrid-day-events { flex-grow: 1; max-height: 105px !important; overflow-y: auto !important; margin-bottom: 2px; }
        .fc-dayGridMonth-view .fc-daygrid-day-events::-webkit-scrollbar { width: 4px; }
        .fc-dayGridMonth-view .fc-daygrid-day-events::-webkit-scrollbar-track { background: transparent; }
        .fc-dayGridMonth-view .fc-daygrid-day-events::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .fc-dayGridMonth-view .fc-daygrid-day-events::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .fc .fc-event.reserva-finalizada { opacity: 0.55; }

        .salones-calendar-scroll { overflow-x: auto; min-height: 600px; }

        /* Mini calendario y leyenda de salones. */
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
        
        .mc-today-real { border: 2px solid #2563eb !important; color: #2563eb !important; font-weight: bold; }
        .mc-row-selected td { background-color: #e2e8f0 !important; }
        .mc-row-selected td.mc-today-real { background-color: #ffffff !important; border: 2px solid #2563eb !important; }
        .mc-row-selected td.mc-has-event { background-color: #f59e0b !important; }

        @media (max-width: 900px) {
            .salones-cal-layout { flex-direction: column !important; gap: 16px !important; }
            .salones-cal-main, .salones-cal-side { width: 100% !important; min-width: 0 !important; }
            .salones-cal-side { padding: 12px !important; }
        }
        .edit-modal-drag-handle { cursor: move; touch-action: none; user-select: none; }
        .fc-reserva-contenido-semana { position: relative; height: 100%; overflow: visible; text-align: center; }
        .fc-reserva-contenido-semana .fc-reserva-indicador-cancelado { position: absolute; top: 1px; left: 1px; z-index: 2; }
        .fc-reserva-contenido-semana .fc-reserva-hora { display: flex; flex-direction: column; align-items: center; white-space: nowrap; overflow: visible; text-align: center; font-size: 10px; line-height: 1.1; }
        .fc-reserva-minutos-semana { font-size: 9px; }
        .fc-reserva-periodo-semana { font-size: 8px; text-transform: uppercase; }
        .fc-reserva-estado-cancelado { display: inline-flex; align-items: center; flex: 0 0 auto; padding: 2px 7px; border: 1px solid #fecaca; border-radius: 999px; background: #dc2626; color: #fff; font-size: 9px; font-weight: 700; line-height: 1.2; vertical-align: middle; box-shadow: 0 1px 2px rgba(127,29,29,.25); }
        .fc-timeGridDay-view .fc-reserva-titulo { display: flex; flex-direction: column; align-items: flex-start; gap: 3px; }
        .fc-timegrid-event, .fc-timegrid-event .fc-event-main { overflow: visible !important; }
        .fecha-hora-control { position: relative; display: flex; flex: 1; min-width: 0; }
        .fecha-hora-texto { box-sizing: border-box; width: 100%; min-width: 0; flex: 1; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 8px 12px; font: inherit; font-size: 13px; color: #1e293b; outline: none; }
        .fecha-hora-control:focus-within .fecha-hora-texto { border-color: #2563eb; box-shadow: 0 0 0 2px rgba(37, 99, 235, .12); }
        .fecha-hora-valor { position: absolute; left: 0; bottom: 0; width: 1px; height: 1px; opacity: 0; pointer-events: none; }
        /* Minicalendario propio: evita el selector grande que dibuja cada navegador. */
        .fecha-mini-calendario { display: none; position: fixed; z-index: 10000; box-sizing: border-box; width: 194px; padding: 6px; border: 1px solid #cbd5e1; border-radius: 5px; background: #fff; color: #334155; box-shadow: 0 5px 14px rgba(15, 23, 42, .2); font: 12px Arial, sans-serif; }
        .fecha-mini-calendario.is-open { display: block; }
        .fecha-mini-cal-header { display: flex; align-items: center; justify-content: space-between; height: 24px; margin-bottom: 4px; border-bottom: 1px solid #e2e8f0; }
        .fecha-mini-mes { flex: 1; text-align: center; font-weight: 700; text-transform: lowercase; }
        .fecha-mini-nav { width: 24px; height: 22px; padding: 0; border: 0; background: transparent; color: #2563eb; font-size: 19px; line-height: 20px; cursor: pointer; }
        .fecha-mini-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; text-align: center; }
        .fecha-mini-semana { padding: 3px 0; color: #334155; font-weight: 700; }
        .fecha-mini-dia { height: 22px; padding: 0; border: 1px solid transparent; border-radius: 2px; background: transparent; color: #1e293b; font: inherit; cursor: pointer; }
        .fecha-mini-dia:hover { background: #dbeafe; }
        .fecha-mini-dia.otro-mes { color: #94a3b8; }
        .fecha-mini-dia.hoy { border-color: #2563eb; }
        .fecha-mini-dia.seleccionado { background: #2563eb; color: #fff; }

        /* 2026-10-20: compacta día, semana y mes; conserva las marcas de hora y media hora. */
        .salones-calendar-scroll { width: 100%; overscroll-behavior-x: contain; }
        .fc-timegrid-slot { height: 1.5em !important; }
        .fc-timegrid-slot-label { padding: 1px 3px !important; }
        .fc-timegrid-slot-label-cushion { font-size: 0.72rem; }
        .fc-timegrid-event { font-size: 10px; }
        .fc-timegrid-event .fc-event-main { padding: 1px 2px !important; }
        .fc-dayGridMonth-view .fc-event-main { padding: 1px 4px !important; }
        .fc-dayGridMonth-view .fc-event { margin-bottom: 1px !important; }
        .fc-dayGridMonth-view .fc-reserva-contenido { gap: 2px; }
        .fc-dayGridMonth-view .fc-reserva-hora, .fc-dayGridMonth-view .fc-reserva-titulo { font-size: 9px; }
        .fc-timegrid .fc-col-header-cell-cushion > div:nth-child(2) { font-size: 1.3rem !important; }

        /* Oculta el riel de desplazamiento del calendario sin bloquear el scroll. */
        #calendar-container,
        #calendar .fc-scroller,
        #calendar .fc-daygrid-day-events {
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        #calendar-container::-webkit-scrollbar,
        #calendar .fc-scroller::-webkit-scrollbar,
        #calendar .fc-daygrid-day-events::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        /* Vista Disponible: tabla compacta para mostrar más salones en pantalla. */
        /* 2026-10-20: se redujeron fuente, anchos y espacios interiores. */
        .tabla-disponible { width: 100%; min-width: 0; table-layout: fixed; border-collapse: collapse; }
        .tabla-disponible th { padding: 6px 3px !important; font-weight: bold; color: #475b75; font-size: 10px; text-align: center; line-height: 1.15; white-space: normal; overflow-wrap: anywhere; }
        .tabla-disponible td { padding: 5px 3px !important; text-align: center; border-bottom: 1px solid #e2e8f0; font-size: 11px; color: #64748b; }
        .tabla-disponible th:nth-child(1), .tabla-disponible td:nth-child(1) { width: 120px !important; }
        .tabla-disponible th:nth-child(2), .tabla-disponible td:nth-child(2) { width: 16px !important; }
        .tabla-disponible th:nth-child(3), .tabla-disponible td:nth-child(3) { width: 65px !important; }
        .tabla-disponible th:nth-child(4), .tabla-disponible td:nth-child(4) { width: 26px !important; }
        .tabla-disponible td:nth-child(n+5) { overflow-wrap: anywhere; }
        @media (max-width: 900px) {
            .tabla-disponible { min-width: 760px; }
            .tabla-disponible th { font-size: 9px; }
            .tabla-disponible td { font-size: 10px; }
        }
        .tabla-disponible tr:hover { background-color: #f8fafc; }
        @media (max-width: 640px) {
            .salones-calendar-scroll { overflow-x: auto; }
            .fc-dayGridMonth-view .fc-reserva-hora, .fc-dayGridMonth-view .fc-reserva-titulo { font-size: 8px; }
            .fc-timegrid-event { font-size: 9px; }
        }
        .celda-vacia:hover { background-color: #e2e8f0; border-radius: 4px; cursor: pointer; color: #3b82f6; font-weight: bold; }
    </style>

    <!-- HTML/BLADE del frontend. Las rutas y reglas del servidor están en routes/ y app/. -->
    <div class="min-h-screen bg-white py-6 px-4 sm:px-8">
        <div style="max-width: 1400px; margin: 0 auto;">
            
            <div class="salones-page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                <div>
                    <h1 style="font-size: 28px; font-weight: bold; color: #475b75; margin-bottom: 4px;">Horario para salones</h1>
                    @if(auth()->user()->rol === 'superadmin')
                        <p style="font-size: 14px; color: #9ca3af;">Selecciona un espacio libre para crear una reserva o un evento para editarlo y administrarlo.</p>
                    @elseif(auth()->user()->rol === 'admin')
                        <p style="font-size: 14px; color: #9ca3af;">Selecciona un espacio libre para crear una reserva. Haz clic en un evento para cancelarlo.</p>
                    @else
                        <p style="font-size: 14px; color: #9ca3af;">Calendario de consulta. Puedes cambiar entre las vistas de mes, semana y día.</p>
                    @endif
                </div>
                
                <div style="display: flex; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <button style="background-color: #f9fafb; padding: 6px 16px; font-weight: bold; color: #374151; border-right: 1px solid #e5e7eb; font-size: 14px;">
                        <i class="fa-regular fa-calendar text-blue-500" style="margin-right: 8px;"></i> Usar
                    </button>
                    @if(auth()->user()->rol === 'superadmin')
                        <a href="{{ route('salones.supervisar') }}" style="background-color: white; padding: 6px 16px; color: #3b82f6; font-size: 14px; text-decoration: none;">
                            <i class="fa-solid fa-sliders" style="margin-right: 8px;"></i> Supervisar
                        </a>
                    @endif
                </div>
            </div>

            <div class="salones-cal-layout" style="display: flex; gap: 30px;">
                
                <div class="salones-cal-main" style="flex: 1; min-width: 0;">
                    <div class="salones-cal-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        
                        <div style="position: relative;" id="view-dropdown-container">
                            <button id="btn-view-dropdown" style="border: 1px solid #d1d5db; border-radius: 4px; padding: 5px 12px; font-size: 14px; background: white; color: #374151; cursor: pointer; display: flex; align-items: center;">
                                <span id="current-view-text">Semana</span> 
                                <i class="fa-solid fa-chevron-down" style="color: #3b82f6; font-size: 12px; margin-left: 10px;"></i>
                            </button>
                            
                            <div id="view-menu" style="display: none; position: absolute; left: 0; top: 100%; margin-top: 4px; width: 140px; background: white; border: 1px solid #e5e7eb; border-radius: 4px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); z-index: 50; padding: 6px 0; font-size: 14px; color: #4b5563;">
                                <a href="#" class="view-option" data-view="dayGridMonth" style="display: block; padding: 6px 16px; text-decoration: none; color: inherit;">Mes</a>
                                <a href="#" class="view-option" data-view="timeGridWeek" style="display: block; padding: 6px 16px; text-decoration: none; color: #1f2937; font-weight: bold; background-color: #f9fafb;">Semana</a>
                                <a href="#" class="view-option" data-view="timeGridDay" style="display: block; padding: 6px 16px; text-decoration: none; color: inherit;">Día</a>
                                @if(auth()->user()->rol !== 'usuario')
                                    <a href="#" class="view-option" data-view="disponible" style="display: block; padding: 6px 16px; text-decoration: none; color: inherit; border-top: 1px solid #e5e7eb;">Disponible</a>
                                @endif
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 24px;" id="toolbar-botones-navegacion">
                            <button id="btn-hoy" style="background: #94a3b8; color: white; padding: 4px 12px; border-radius: 4px; font-size: 14px; border: none; cursor: pointer;">
                                Hoy
                            </button>
                            <div style="display: flex; align-items: center; font-size: 18px; color: #3b82f6; font-weight: bold;">
                                <i class="fa-solid fa-chevron-left" id="btn-prev" style="cursor: pointer; padding: 0 8px;"></i>
                                <span id="calendar-title" style="color: #475b75; font-size: 15px; margin: 0 16px; font-weight: normal; text-transform: capitalize;">Semana 42</span>
                                <i class="fa-solid fa-chevron-right" id="btn-next" style="cursor: pointer; padding: 0 8px;"></i>
                            </div>
                        </div>
                    </div>

                    <!-- CONTENEDOR DEL CALENDARIO NORMAL -->
                    <div class="salones-calendar-scroll" id="calendar-container">
                        <div id="calendar" style="background: white;"></div>
                    </div>

                    <!-- CONTENEDOR DE LA NUEVA TABLA "DISPONIBLE" CON EL BUSCADOR (Oculta por defecto) -->
                    <div id="vista-disponible-container" style="display: none; background: white; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="padding: 16px 24px; border-bottom: 1px solid #e2e8f0;">
                            <h2 style="font-size: 18px; font-weight: bold; color: #334155; margin: 0;" id="titulo-disponible">Disponibilidad para salones</h2>
                        </div>
                        <div style="overflow-x: auto; min-height: 300px;">
                            <table class="tabla-disponible" style="width: 100%; border-collapse: collapse;">
                                <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                    <tr id="cabecera-disponible">
                                        <!-- Generado por JS -->
                                    </tr>
                                </thead>
                                <tbody id="cuerpo-disponible">
                                    <!-- Generado por JS -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- FILTRO BUSCADOR DE TIEMPO LIBRE -->
                        <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0; font-size: 13px; color: #64748b;">
                            Haga clic en un espacio libre para crear una nueva reserva.
                        </div>
                        <div style="padding: 16px 24px; display: flex; align-items: center; gap: 12px; font-size: 14px; color: #475b75; flex-wrap: wrap; background: #f8fafc; border-top: 1px solid #e2e8f0;">
                            <span>Encontrar espacio disponible después de</span>
                            <input type="datetime-local" id="filtro-gap-fecha" style="border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 8px; outline: none; font-size: 13px; font-family: inherit;">
                            <span>de</span>
                            <input type="number" id="filtro-gap-minutos" value="60" min="15" style="border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 8px; width: 70px; outline: none; font-size: 13px;">
                            <span>minutos para</span>
                            <select id="filtro-gap-salon" style="border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 8px; outline: none; font-size: 13px;">
                                <option value="any">cualquier recurso</option>
                                @foreach($salones as $salon)
                                    <option value="{{ $salon->nombre }}">{{ $salon->nombre }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="btn-buscar-gap" style="background: #2563eb; color: white; border: none; border-radius: 4px; padding: 6px 16px; font-weight: bold; font-size: 13px; cursor: pointer;">Buscar</button>
                            <a href="#" id="btn-vaciar-gap" style="color: #3b82f6; text-decoration: underline; font-size: 12px;">Vaciar búsqueda</a>
                        </div>
                    </div>

                </div>

                <!-- MINI CALENDARIO DINÁMICO -->
                <div class="salones-cal-side" style="width: 260px; flex-shrink: 0; padding: 50px 12px 12px; border: 1px solid #cbd5e1; border-radius: 11px; background: #fff;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; color: #3b82f6; font-size: 14px;">
                        <i class="fa-solid fa-chevron-left" id="mini-prev" style="cursor: pointer;"></i>
                        <span style="font-size: 13px; text-transform: capitalize;"><span id="mini-cal-month-label" style="color: #000; font-weight: 700;">octubre</span> <span id="mini-cal-year-label" style="color: #475b75; font-weight: 400;">2026</span></span>
                        <i class="fa-solid fa-chevron-right" id="mini-next" style="cursor: pointer;"></i>
                    </div>

                    <div style="margin-bottom: 24px;">
                        <table class="mini-cal" id="mini-calendar-table">
                            <thead>
                                <tr>
                                    <th>DO</th><th>LU</th><th>MA</th><th>MI</th><th>JU</th><th>VI</th><th>SA</th>
                                </tr>
                            </thead>
                            <tbody id="mini-cal-body">
                                <!-- Generado dinámicamente -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Leyendas de Salones -->
                    <div id="salon-filter-buttons" style="display: flex; flex-wrap: wrap; gap: 6px; font-size: 11px; font-weight: bold; color: white;">
                        <button type="button" class="salon-filter active" data-salon="all" aria-pressed="true" style="background-color: #9ca3af; padding: 4px 8px; border-radius: 4px;">
                            <i class="fa-solid fa-check salon-filter-check" aria-hidden="true" style="margin-right: 4px;"></i>Todos
                        </button>
                        @foreach($salones as $salon)
                            <button type="button" class="salon-filter" data-salon="{{ $salon->nombre }}" aria-pressed="false" style="background-color: {{ $salon->color }}; padding: 4px 8px; border-radius: 4px;">
                                <i class="fa-solid fa-check salon-filter-check" aria-hidden="true" style="margin-right: 4px;"></i>{{ $salon->nombre }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <!-- MODAL FLOTANTE UNIFICADO "NUEVA RESERVA" -->
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
                                <input type="datetime-local" id="modal-start" name="fecha_inicio" step="60" style="flex: 1; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 8px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                                <span style="font-size: 13px; color: #475b75;">hasta</span>
                                <input type="datetime-local" id="modal-end" name="fecha_fin" step="60" style="flex: 1; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 8px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
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
                                    @foreach($salonesActivos as $salon)
                                        <option value="{{ $salon->nombre }}">{{ $salon->nombre }}</option>
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
                            <label style="width: 160px; font-size: 13px; color: #4b5563;">Aforo *<br><small style="color:#9ca3af; font-weight:normal;">Cantidad de personas</small></label>
                            <div style="flex: 1;">
                                <input type="number" name="aforo" placeholder="Ej: 77" min="1" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                            </div>
                        </div>

                        <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                            <label style="width: 160px; font-size: 13px; color: #4b5563;">Tipo de ingreso *</label>
                            <div style="flex: 1;">
                                <select name="tipo_ingreso" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                                    <option value="Bajo lista">Bajo lista</option>
                                    <option value="Libre">Libre</option>
                                    <option value="Con invitación">Con invitación</option>
                                </select>
                            </div>
                        </div>

                        <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                            <label style="width: 160px; font-size: 13px; color: #4b5563;">Requerimiento especial</label>
                            <div style="flex: 1;">
                                <input type="text" name="requerimientos_especiales" placeholder="" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;">
                            </div>
                        </div>

                        <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                            <label style="width: 160px; font-size: 13px; color: #4b5563;">Nombre quien registra *</label>
                            <div style="flex: 1;">
                                <input type="text" name="registrado_por" value="{{ Auth::user()->nombre }}" style="width: 100%; border: 1px solid #d1d5db; background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                            </div>
                        </div>

                        <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                            <label style="width: 160px; font-size: 13px; color: #4b5563;">Coordinador interno</label>
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
                                <input type="datetime-local" id="edit-start" name="fecha_inicio" step="60" style="flex: 1; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 8px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
                                <span style="font-size: 13px; color: #475b75;">hasta</span>
                                <input type="datetime-local" id="edit-end" name="fecha_fin" step="60" style="flex: 1; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 4px; padding: 8px 12px; font-size: 13px; color: #1e293b; outline: none;" required>
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

                        @if(auth()->user()->rol === 'superadmin')
                            <div style="display: flex; align-items: center; gap: 16px; margin: 0 0 16px 40px; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc;">
                                <div style="flex: 1;">
                                    <label for="edit-event-state" style="display: block; margin-bottom: 4px; color: #334155; font-size: 13px; font-weight: 600;">Estado del evento</label>
                                    <span style="color: #64748b; font-size: 11px;">Se muestra junto al título; se guarda quién y cuándo lo cambió.</span>
                                </div>
                                <select id="edit-event-state" name="estado" style="min-width: 145px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 10px; background: #fff; color: #1e293b; font-size: 13px;">
                                    <option value="programado">Programado</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                        @endif

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
                            
                            @if(auth()->user()->rol === 'superadmin')
                                <button type="submit" id="btn-edit-update" style="background: #2563eb; color: white; border: none; padding: 6px 16px; border-radius: 4px; font-size: 13px; font-weight: bold; cursor: pointer;">Actualizar reserva</button>
                            @endif

                            @if(auth()->user()->rol === 'superadmin')
                                <button type="button" id="btn-edit-delete" style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;" title="Eliminar reserva">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript frontend. 20/10/2026: conecta calendario, filtros y formularios con rutas Laravel. -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var rolActual = @json(auth()->user()->rol ?? 'usuario');

            // Muestra fecha local legible y conserva el selector nativo del navegador (20/10/2026).
            function formatoFechaHora(valor) {
                var coincidencia = String(valor || '').match(/^(\d{4})-(\d{2})-(\d{2})T(\d{2}:\d{2})/);
                return coincidencia ? `${coincidencia[1]}/${coincidencia[2]}/${coincidencia[3]} ${coincidencia[4]}` : '';
            }

            function valorFechaHora(texto) {
                var coincidencia = String(texto || '').trim().match(/^(\d{4})\/(\d{2})\/(\d{2})\s+(\d{1,2}):(\d{2})$/);
                if (!coincidencia) return '';

                var hora = Number(coincidencia[4]);
                var minuto = Number(coincidencia[5]);
                var fecha = new Date(Number(coincidencia[1]), Number(coincidencia[2]) - 1, Number(coincidencia[3]), hora, minuto);
                if (fecha.getFullYear() !== Number(coincidencia[1]) || fecha.getMonth() !== Number(coincidencia[2]) - 1 || fecha.getDate() !== Number(coincidencia[3]) || fecha.getHours() !== hora || fecha.getMinutes() !== minuto || hora > 23 || minuto > 59) return '';

                return `${coincidencia[1]}-${coincidencia[2]}-${coincidencia[3]}T${String(hora).padStart(2, '0')}:${coincidencia[5]}`;
            }

            // 2026-10-20: calendario compacto de 194 px, con semana de domingo a sábado.
            function crearMiniCalendarioFecha(contenedor, texto, nativo) {
                var mesVisible = new Date();
                var popover = document.createElement('div');
                popover.className = 'fecha-mini-calendario';
                popover.setAttribute('role', 'dialog');
                popover.setAttribute('aria-label', 'Seleccionar fecha');
                document.body.appendChild(popover);

                function claveFecha(fecha) {
                    return `${fecha.getFullYear()}-${String(fecha.getMonth() + 1).padStart(2, '0')}-${String(fecha.getDate()).padStart(2, '0')}`;
                }
                function fechaSeleccionada() {
                    var valor = nativo.value || valorFechaHora(texto.value);
                    return valor ? valor.slice(0, 10) : '';
                }
                function dibujar() {
                    var anio = mesVisible.getFullYear();
                    var mes = mesVisible.getMonth();
                    var tituloMes = new Intl.DateTimeFormat('es-BO', { month: 'long', year: 'numeric' }).format(new Date(anio, mes, 1));
                    var seleccion = fechaSeleccionada();
                    var hoy = claveFecha(new Date());
                    var inicio = new Date(anio, mes, 1).getDay();
                    var diasMes = new Date(anio, mes + 1, 0).getDate();
                    var cantidad = Math.ceil((inicio + diasMes) / 7) * 7;

                    popover.innerHTML = `<div class="fecha-mini-cal-header"><button type="button" class="fecha-mini-nav" data-mes="-1" aria-label="Mes anterior">‹</button><span class="fecha-mini-mes">${tituloMes}</span><button type="button" class="fecha-mini-nav" data-mes="1" aria-label="Mes siguiente">›</button></div><div class="fecha-mini-grid">${['DO','LU','MA','MI','JU','VI','SA'].map(d => `<span class="fecha-mini-semana">${d}</span>`).join('')}</div><div class="fecha-mini-grid fecha-mini-dias"></div>`;
                    var dias = popover.querySelector('.fecha-mini-dias');
                    for (var indice = 0; indice < cantidad; indice++) {
                        var fecha = new Date(anio, mes, indice - inicio + 1);
                        var boton = document.createElement('button');
                        boton.type = 'button';
                        boton.className = 'fecha-mini-dia';
                        boton.textContent = fecha.getDate();
                        boton.dataset.fecha = claveFecha(fecha);
                        if (fecha.getMonth() !== mes) boton.classList.add('otro-mes');
                        if (boton.dataset.fecha === hoy) boton.classList.add('hoy');
                        if (boton.dataset.fecha === seleccion) boton.classList.add('seleccionado');
                        dias.appendChild(boton);
                    }
                }
                function posicionar() {
                    var rect = texto.getBoundingClientRect();
                    popover.style.visibility = 'hidden';
                    popover.classList.add('is-open');
                    var ancho = popover.offsetWidth;
                    var alto = popover.offsetHeight;
                    var izquierda = Math.max(8, Math.min(rect.left, window.innerWidth - ancho - 8));
                    var arriba = rect.top - alto - 4;
                    if (arriba < 8) arriba = Math.min(window.innerHeight - alto - 8, rect.bottom + 4);
                    popover.style.left = izquierda + 'px';
                    popover.style.top = Math.max(8, arriba) + 'px';
                    popover.style.visibility = '';
                }
                function abrir() {
                    var valor = fechaSeleccionada();
                    var base = valor ? new Date(valor + 'T12:00:00') : new Date();
                    mesVisible = new Date(base.getFullYear(), base.getMonth(), 1);
                    dibujar();
                    posicionar();
                }
                function cerrar() { popover.classList.remove('is-open'); }
                function actualizar() { if (popover.classList.contains('is-open')) { dibujar(); posicionar(); } }

                popover.addEventListener('click', function(evento) {
                    evento.stopPropagation();
                    var navegacion = evento.target.closest('[data-mes]');
                    if (navegacion) {
                        mesVisible.setMonth(mesVisible.getMonth() + Number(navegacion.dataset.mes));
                        dibujar();
                        posicionar();
                        return;
                    }
                    var dia = evento.target.closest('[data-fecha]');
                    if (!dia) return;
                    var horaActual = nativo.value ? nativo.value.slice(11, 16) : '08:30';
                    nativo.value = dia.dataset.fecha + 'T' + horaActual;
                    texto.value = formatoFechaHora(nativo.value);
                    texto.setCustomValidity('');
                    cerrar();
                    texto.focus();
                });
                document.addEventListener('click', function(evento) {
                    if (evento.target !== texto && !popover.contains(evento.target)) cerrar();
                });
                window.addEventListener('scroll', cerrar, true);
                window.addEventListener('resize', cerrar);
                return { abrir: abrir, cerrar: cerrar, actualizar: actualizar };
            }

            function prepararCamposFechaHora() {
                document.querySelectorAll('input[type="datetime-local"]').forEach(function(nativo) {
                    if (nativo.dataset.formatoPreparado) return;
                    nativo.dataset.formatoPreparado = 'true';
                    var eraRequerido = nativo.required;
                    var contenedor = document.createElement('div');
                    contenedor.className = 'fecha-hora-control';
                    contenedor.style.flex = nativo.id === 'filtro-gap-fecha' ? '0 0 160px' : '0 1 190px';
                    nativo.parentNode.insertBefore(contenedor, nativo);
                    contenedor.appendChild(nativo);

                    var texto = document.createElement('input');
                    texto.type = 'text';
                    texto.className = 'fecha-hora-texto';
                    texto.placeholder = 'AAAA/MM/DD HH:mm';
                    texto.autocomplete = 'off';
                    texto.inputMode = 'numeric';
                    texto.setAttribute('aria-label', 'Fecha y hora, formato año/mes/día hora:minutos');
                    texto.required = eraRequerido;
                    texto.value = formatoFechaHora(nativo.value);
                    texto.dataset.fechaHoraPara = nativo.id;
                    contenedor.insertBefore(texto, nativo);

                    nativo.required = false;
                    nativo.classList.add('fecha-hora-valor');

                    var calendarioFecha = crearMiniCalendarioFecha(contenedor, texto, nativo);
                    nativo._calendarioFecha = calendarioFecha;

                    function actualizarDesdeTexto() {
                        var valor = valorFechaHora(texto.value);
                        nativo.value = valor;
                        texto.setCustomValidity(texto.value && !valor
                            ? 'Use AAAA/MM/DD H:mm o HH:mm, por ejemplo 2026/10/22 8:30.'
                            : (texto.required && !valor ? 'Ingrese una fecha y hora.' : ''));
                        calendarioFecha.actualizar();
                    }

                    texto.addEventListener('input', actualizarDesdeTexto);
                    texto.addEventListener('click', calendarioFecha.abrir);
                    texto.addEventListener('blur', function() {
                        var valor = valorFechaHora(texto.value);
                        if (valor) texto.value = formatoFechaHora(valor);
                    });
                });
            }

            function establecerFechaHora(id, valor) {
                var nativo = document.getElementById(id);
                if (!nativo) return;
                nativo.value = String(valor || '').slice(0, 16);
                var texto = document.querySelector('[data-fecha-hora-para="' + id + '"]');
                if (texto) {
                    texto.value = formatoFechaHora(nativo.value);
                    texto.setCustomValidity('');
                }
                if (nativo._calendarioFecha) nativo._calendarioFecha.actualizar();
            }

            function leerFechaHora(id) {
                var nativo = document.getElementById(id);
                var texto = document.querySelector('[data-fecha-hora-para="' + id + '"]');
                if (!nativo) return '';
                if (texto) {
                    nativo.value = valorFechaHora(texto.value);
                    texto.setCustomValidity(texto.value && !nativo.value ? 'Use el formato AAAA/MM/DD HH:mm.' : '');
                }
                if (nativo._calendarioFecha) nativo._calendarioFecha.actualizar();
                return nativo.value;
            }

            prepararCamposFechaHora();
            var selectedSalonFilter = null;
            var miniEventDatesCache = {};
            var salonColors = @json($coloresSalones);
            var urlFechasReservas = @json(route('api.reservas.fechas'));
            var parametrosInicio = new URLSearchParams(window.location.search);
            var reservaParaEditar = parametrosInicio.get('editar');
            var salonOrder = Object.keys(salonColors);
            
            // VARIABLES GLOBALES PARA EL BUSCADOR DE ESPACIOS (Gaps)
            var gapFilterActive = false;
            var gapStartDate = null;
            var gapDurationMins = 60;
            var gapSalon = 'any';
            // 2026-10-20: evita que una respuesta vieja de la API agregue filas tras cambiar la fecha.
            var renderDisponibleId = 0;

            var fechaDisponibleInicial = new Date();
            fechaDisponibleInicial.setHours(8, 30, 0, 0);
            var fechaDisponibleTexto = `${fechaDisponibleInicial.getFullYear()}-${String(fechaDisponibleInicial.getMonth() + 1).padStart(2, '0')}-${String(fechaDisponibleInicial.getDate()).padStart(2, '0')}T08:30`;
            establecerFechaHora('filtro-gap-fecha', fechaDisponibleTexto);
            document.getElementById('filtro-gap-minutos').value = '60';
            gapFilterActive = true;
            gapStartDate = fechaDisponibleInicial;

            function ajustarAnchoCarriles(viewType) {
                if (viewType === 'dayGridMonth' || viewType === 'timeGridWeek') {
                    calendarEl.style.minWidth = '100%';
                    return;
                }
                calendarEl.style.minWidth = Math.max(560, salonOrder.length * 80 + 55) + 'px';
            }

            function posicionarEventoEnCarril(info) {
                var salonIndex = salonOrder.indexOf(info.event.extendedProps.salon);
                if (salonIndex < 0) return;

                var count = Math.max(salonOrder.length, 1);
                var width = 100 / count;
                var harness = info.el.parentElement;

                if (info.view.type === 'dayGridMonth') return;

                if (info.view.type === 'timeGridDay' || info.view.type === 'timeGridWeek') {
                    harness.style.setProperty('left', (salonIndex * width) + '%', 'important');
                    harness.style.setProperty('right', (100 - ((salonIndex + 1) * width)) + '%', 'important');
                    harness.style.setProperty('width', 'auto', 'important');
                }
            }

            function reaplicarCarrilesEnVista(viewType) {
                if (viewType !== 'timeGridDay' && viewType !== 'timeGridWeek') return;

                calendarEl.querySelectorAll('.fc-timegrid-event[data-reserva-id]').forEach(function(elemento) {
                    var evento = calendar.getEventById(elemento.dataset.reservaId);
                    if (evento) {
                        posicionarEventoEnCarril({
                            el: elemento,
                            event: evento,
                            view: { type: viewType }
                        });
                    }
                });
            }

            function salonSeleccionadoEnCarril(info) {
                if (selectedSalonFilter) return selectedSalonFilter;

                var fecha = info.startStr.slice(0, 10);
                var columnas = calendarEl.querySelectorAll('.fc-timegrid-col[data-date]');
                var x = info.jsEvent && info.jsEvent.clientX;
                if (typeof x !== 'number') return null;

                for (var i = 0; i < columnas.length; i++) {
                    var columna = columnas[i];
                    if (columna.tagName !== 'TD' || columna.getAttribute('data-date') !== fecha) continue;

                    var rect = columna.getBoundingClientRect();
                    if (x < rect.left || x > rect.right || !rect.width) continue;

                    var laneIndex = Math.min(
                        salonOrder.length - 1,
                        Math.floor(((x - rect.left) / rect.width) * salonOrder.length)
                    );
                    return salonOrder[laneIndex] || null;
                }
                return null;
            }

            function compareEventsBySalon(a, b) {
                var salonA = a.extendedProps.salon;
                var salonB = b.extendedProps.salon;
                var positionA = salonOrder.indexOf(salonA);
                var positionB = salonOrder.indexOf(salonB);

                if (positionA === -1) positionA = salonOrder.length;
                if (positionB === -1) positionB = salonOrder.length;

                return positionA - positionB || a.start - b.start || a.title.localeCompare(b.title);
            }

            // ======= FUNCIONALIDAD DE LA VISTA PERSONALIZADA "DISPONIBLE" =======
            function renderTablaDisponible() {
                var renderId = ++renderDisponibleId;
                var tbody = document.getElementById('cuerpo-disponible');
                var thead = document.getElementById('cabecera-disponible');
                var tituloVista = document.querySelector('#titulo-disponible');
                
                // 1. DETERMINAR QUÉ SALONES MOSTRAR (Todos o solo el filtrado en la leyenda)
                // Si el Buscador de Huecos está activo y tiene un salón específico, forzamos ese salón
                var currentVisualFilter = (gapFilterActive && gapSalon !== 'any') ? gapSalon : selectedSalonFilter;
                var salonesAMostrar = currentVisualFilter ? [currentVisualFilter] : salonOrder;

                // Actualizar el título dinámicamente
                if (tituloVista) {
                    let tituloBase = currentVisualFilter ? `Disponibilidad para ${currentVisualFilter}` : 'Disponibilidad para salones';
                    if (gapFilterActive && gapStartDate) {
                        let dateString = `${gapStartDate.getFullYear()}-${String(gapStartDate.getMonth()+1).padStart(2,'0')}-${String(gapStartDate.getDate()).padStart(2,'0')}`;
                        tituloVista.innerText = `${tituloBase} después de ${dateString}`;
                    } else {
                        tituloVista.innerText = tituloBase;
                    }
                }

                // 2. CONSTRUIR CABECERAS
                tbody.innerHTML = '';
                thead.innerHTML = `
                    <th style="padding: 12px 16px; font-weight: 600; color: #475b75; width: 80px;">De</th>
                    <th style="padding: 12px 8px; width: 20px;"></th>
                    <th style="padding: 12px 16px; font-weight: 600; color: #475b75; width: 80px;">Hasta</th>
                    <th style="padding: 12px 16px; width: 40px;"></th>
                `;

                salonesAMostrar.forEach(salon => {
                    thead.innerHTML += `<th style="padding: 12px 16px; font-weight: 600; color: #475b75; text-align: center; max-width: 120px; white-space: normal; line-height: 1.2;">${salon}</th>`;
                });

                // Pedimos TODAS las reservas a la API para hacer los cálculos
                fetch('/api/salones/reservas', { headers: { 'Accept': 'application/json' } })
                    .then(response => response.json())
                    .then(allEvents => {
                        // Si hubo otra búsqueda mientras cargaban los datos, ignoramos esta respuesta anterior.
                        if (renderId !== renderDisponibleId) return;

                        let sortedKeys = [];
                        let timeBlocks = {};
                        
                        // Si el Buscador de Huecos NO está activo, generamos la tabla normal (basada en reservas de la semana)
                        if (!gapFilterActive) {
                            let viewStart = calendar.view.activeStart;
                            let viewEnd = calendar.view.activeEnd;
                            
                            let eventosEnEstaSemana = allEvents.filter(ev => {
                                let evStart = new Date(ev.start);
                                return (evStart >= viewStart && evStart < viewEnd);
                            });

                            eventosEnEstaSemana.forEach(ev => {
                                let start = new Date(ev.start);
                                let end = ev.end ? new Date(ev.end) : new Date(ev.start);
                                
                                let dayNames = ['do', 'lu', 'ma', 'mi', 'ju', 'vi', 'sa'];
                                let startDay = dayNames[start.getDay()];
                                let startMonth = String(start.getMonth() + 1).padStart(2, '0');
                                let startDateStr = String(start.getDate()).padStart(2, '0');
                                let startHour = start.getHours(); // Formato 8:00
                                let startMin = String(start.getMinutes()).padStart(2, '0');
                                
                                let endHour = end.getHours();
                                let endMin = String(end.getMinutes()).padStart(2, '0');

                                let key = `${start.getTime()}-${end.getTime()}`;
                                let displayStart = `${startDay} ${startMonth}-${startDateStr} ${startHour}:${startMin}`;
                                let displayEnd = `${endHour}:${endMin}`;

                                if (!timeBlocks[key]) {
                                    timeBlocks[key] = { startObj: start, endObj: end, displayStart: displayStart, displayEnd: displayEnd, salonesOcupados: {} };
                                }
                                timeBlocks[key].salonesOcupados[ev.salon] = ev;
                            });

                            sortedKeys = Object.keys(timeBlocks).sort((a, b) => timeBlocks[a].startObj.getTime() - timeBlocks[b].startObj.getTime());
                            
                        } 
                        // MODO BUSCADOR: Calculamos los ESPACIOS LIBRES desde la fecha indicada (Buscador Inteligente)
                        else {
                            let targetDate = new Date(gapStartDate);
                            targetDate.setSeconds(0, 0);
                            
                            // Buscamos hasta 14 días adelante
                            let endDateScope = new Date(targetDate);
                            endDateScope.setDate(endDateScope.getDate() + 14);

                            let salonesAChecar = gapSalon === 'any' ? salonOrder : [gapSalon];
                            let gapsEncontrados = [];

                            for (let d = new Date(targetDate); d < endDateScope; d.setDate(d.getDate() + 1)) {
                                // Horario de oficina: de 08:00 a 21:00
                                let dayStart = new Date(d); dayStart.setHours(8, 0, 0, 0);
                                let dayEnd = new Date(d); dayEnd.setHours(21, 0, 0, 0);

                                if (d.toDateString() === targetDate.toDateString()) {
                                    if (targetDate > dayStart) dayStart = new Date(targetDate);
                                }
                                if (dayStart >= dayEnd) continue;

                                salonesAChecar.forEach(salon => {
                                    let evsDelDia = allEvents.filter(e => {
                                        let eStart = new Date(e.start);
                                        let eEnd = new Date(e.end || e.start);
                                        return e.salon === salon && eEnd > dayStart && eStart < dayEnd;
                                    });
                                    evsDelDia.sort((a, b) => new Date(a.start) - new Date(b.start));

                                    let pointer = new Date(dayStart);
                                    evsDelDia.forEach(ev => {
                                        let evStart = new Date(ev.start);
                                        let evEnd = new Date(ev.end || ev.start);

                                        if (evStart > pointer) {
                                            let diffMins = (evStart - pointer) / 60000;
                                            if (diffMins >= gapDurationMins) {
                                                gapsEncontrados.push({ salon: salon, start: new Date(pointer), end: new Date(evStart) });
                                            }
                                        }
                                        if (evEnd > pointer) {
                                            pointer = new Date(evEnd);
                                        }
                                    });

                                    // Chequear espacio desde el último evento del día hasta las 21:00
                                    if (dayEnd > pointer) {
                                        let diffMins = (dayEnd - pointer) / 60000;
                                        if (diffMins >= gapDurationMins) {
                                            gapsEncontrados.push({ salon: salon, start: new Date(pointer), end: new Date(dayEnd) });
                                        }
                                    }
                                });
                            }

                            // Agrupar los gaps por franja horaria para generar las filas de la tabla
                            gapsEncontrados.forEach(gap => {
                                let dayNames = ['do', 'lu', 'ma', 'mi', 'ju', 'vi', 'sa'];
                                let startDay = dayNames[gap.start.getDay()];
                                let startMonth = String(gap.start.getMonth() + 1).padStart(2, '0');
                                let startDateStr = String(gap.start.getDate()).padStart(2, '0');
                                let startHour = gap.start.getHours(); // 8:00 sin cero inicial
                                let startMin = String(gap.start.getMinutes()).padStart(2, '0');
                                
                                let endHour = gap.end.getHours();
                                let endMin = String(gap.end.getMinutes()).padStart(2, '0');

                                let key = `${gap.start.getTime()}-${gap.end.getTime()}`;
                                let displayStart = `${startDay} ${startMonth}-${startDateStr} ${startHour}:${startMin}`;
                                let displayEnd = `${endHour}:${endMin}`;

                                if (!timeBlocks[key]) {
                                    timeBlocks[key] = { startObj: gap.start, endObj: gap.end, displayStart: displayStart, displayEnd: displayEnd, salonesOcupados: {} };
                                    // En modo gap, asumimos todo ocupado (.) y luego "desocupamos" (+) el salón libre encontrado
                                    salonesAMostrar.forEach(s => timeBlocks[key].salonesOcupados[s] = true);
                                }
                                // Como encontramos este hueco libre, lo borramos de ocupados
                                delete timeBlocks[key].salonesOcupados[gap.salon];
                            });
                            
                            sortedKeys = Object.keys(timeBlocks).sort((a, b) => timeBlocks[a].startObj.getTime() - timeBlocks[b].startObj.getTime());
                        }

                        // VALIDAR SI HAY RESULTADOS
                        if (sortedKeys.length === 0) {
                            let msg = gapFilterActive ? `No se encontraron espacios de ${gapDurationMins} minutos disponibles después de la fecha indicada.` : `No hay actividad programada para mostrar en esta vista.`;
                            tbody.innerHTML = `<tr><td colspan="${4 + salonesAMostrar.length}" style="padding: 32px; text-align: center; color: #9ca3af;">${msg}</td></tr>`;
                            return;
                        }

                        // 4. GENERAR FILAS Y CELDAS
                        sortedKeys.slice(0, 10).forEach(key => {
                            let block = timeBlocks[key];
                            let tr = document.createElement('tr');

                            tr.innerHTML += `<td style="padding: 12px 16px; color: #475b75; white-space: nowrap;">${block.displayStart}</td>`;
                            tr.innerHTML += `<td style="padding: 12px 8px; color: #94a3b8;">-</td>`;
                            tr.innerHTML += `<td style="padding: 12px 16px; color: #475b75;">${block.displayEnd}</td>`;
                            tr.innerHTML += `<td style="padding: 12px 16px; color: #3b82f6;"><i class="fa-solid fa-calendar-days"></i></td>`;

                            salonesAMostrar.forEach(salonName => {
                                let td = document.createElement('td');
                                td.style.padding = '12px 16px';
                                td.style.textAlign = 'center';

                                if (block.salonesOcupados[salonName]) {
                                    // OCUPADO (.)
                                    td.innerText = '.';
                                    if(!gapFilterActive) {
                                        let ev = block.salonesOcupados[salonName];
                                        td.title = `Ocupado: ${ev.title}`; 
                                    }
                                    td.style.color = '#94a3b8'; 
                                    td.style.fontWeight = 'bold';
                                } else {
                                    // LIBRE (+)
                                    let color = salonColors[salonName] || '#3b82f6';
                                    td.innerHTML = `<span style="display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:50%; background-color:${color}; color:white; font-size:14px; cursor:pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.1);" title="Disponible - Clic para reservar">+</span>`;
                                    
                                    td.querySelector('span').addEventListener('click', () => {
                                        let s = block.startObj;
                                        let e = block.endObj;
                                        
                                        let startFormatted = `${s.getFullYear()}-${String(s.getMonth()+1).padStart(2,'0')}-${String(s.getDate()).padStart(2,'0')}T${String(s.getHours()).padStart(2,'0')}:${String(s.getMinutes()).padStart(2,'0')}`;
                                        let endFormatted = `${e.getFullYear()}-${String(e.getMonth()+1).padStart(2,'0')}-${String(e.getDate()).padStart(2,'0')}T${String(e.getHours()).padStart(2,'0')}:${String(e.getMinutes()).padStart(2,'0')}`;
                                        
                                        establecerFechaHora('modal-start', startFormatted);
                                        establecerFechaHora('modal-end', endFormatted);
                                        
                                        let salonSelect = document.querySelector('#booking-modal select[name="salon"]');
                                        if (salonSelect) salonSelect.value = salonName;
                                        
                                        document.getElementById('booking-modal').style.display = 'flex';
                                    });
                                }
                                tr.appendChild(td);
                            });
                            tbody.appendChild(tr);
                        });
                    });
            }

            function cancelarEventoDesdeCalendario(evento) {
                var props = evento.extendedProps || {};
                if (props.estado === 'cancelado') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Evento ya cancelado',
                        text: 'Este evento ya se encuentra cancelado.',
                        confirmButtonColor: '#2563eb'
                    });
                    return;
                }

                Swal.fire({
                    icon: 'warning',
                    title: '¿Cancelar este evento?',
                    text: evento.title + (props.salon ? ' · ' + props.salon : ''),
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cancelar',
                    cancelButtonText: 'Volver',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b'
                }).then(async function(resultado) {
                    if (!resultado.isConfirmed) return;

                    try {
                        var respuesta = await fetch('/api/salones/reservas/' + encodeURIComponent(evento.id) + '/cancelar', {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                'Accept': 'application/json'
                            }
                        });
                        var datos = await respuesta.json();
                        if (!respuesta.ok || !datos.success) {
                            throw new Error(datos.message || 'No se pudo cancelar el evento.');
                        }

                        calendar.refetchEvents();
                        miniEventDatesCache = {};
                        updateMiniCalendar(currentMiniYear, currentMiniMonth);
                        Swal.fire({
                            icon: 'success',
                            title: 'Evento cancelado',
                            text: 'El evento se canceló correctamente.',
                            confirmButtonColor: '#2563eb'
                        });
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'No se pudo cancelar',
                            text: error.message || 'Intenta nuevamente.',
                            confirmButtonColor: '#2563eb'
                        });
                    }
                });
            }

            function abrirEdicionReserva(evento) {
                var props = evento.extendedProps || {};

                if (rolActual === 'admin') {
                    cancelarEventoDesdeCalendario(evento);
                    return;
                }

                if (rolActual !== 'superadmin') {
                    var formatoEvento = new Intl.DateTimeFormat('es-BO', { dateStyle: 'short', timeStyle: 'short' });
                    var detalleEvento = (props.salon ? 'Salón: ' + props.salon + '\n' : '')
                        + (evento.start ? 'Inicio: ' + formatoEvento.format(evento.start) : '');
                    Swal.fire({
                        icon: 'info',
                        title: evento.title || 'Evento del calendario',
                        text: detalleEvento || 'Puedes consultar este evento en el calendario.',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#2563eb'
                    });
                    return;
                }

                var modal = document.getElementById('edit-booking-modal');

                document.getElementById('edit-event-id').value = evento.id;
                establecerFechaHora('edit-start', evento.startStr);
                establecerFechaHora('edit-end', evento.endStr || '');
                document.getElementById('edit-title').value = evento.title;
                var selectorEstado = document.getElementById('edit-event-state');
                if (selectorEstado) selectorEstado.value = props.estado || 'programado';
                document.getElementById('edit-username').value = props.nombre_usuario || '';
                document.getElementById('edit-phone').value = props.telefono_usuario || '';
                
                document.getElementById('edit-authority').value = props.nombre_autoridad || '';
                document.getElementById('edit-responsable').value = props.responsable || '';
                document.getElementById('edit-salon').value = props.salon || '';

                document.getElementById('edit-tipo-evento').value = props.tipo_evento || 'Visita guiada';
                document.getElementById('edit-aforo').value = props.aforo || '';
                document.getElementById('edit-tipo-ingreso').value = props.tipo_ingreso || 'Bajo lista';
                document.getElementById('edit-req-esp').value = props.requerimientos_especiales || '';
                document.getElementById('edit-registrado-por').value = props.registrado_por || '';
                document.getElementById('edit-coord-int').value = props.coordinador_interno || '';

                let reqs = props.requerimientos;
                if (typeof reqs === 'string') {
                    try { reqs = JSON.parse(reqs); } catch(e) { reqs = reqs.split(','); }
                }
                if (!Array.isArray(reqs)) reqs = [];
                
                let checkboxes = document.querySelectorAll('#edit-booking-form input[name="requerimientos[]"]');
                checkboxes.forEach(cb => {
                    cb.checked = reqs.some(r => r.trim() === cb.value.trim());
                });

                var fecha = props.fecha_creacion ? props.fecha_creacion.slice(0, 10) : '';
                document.getElementById('edit-footer-text').innerText = 'Creado el ' + fecha + ' por ' + (props.nombre_usuario || 'usuario');
                
                modal.style.display = 'flex';
            }
            
            function getWeekNumber(d) {
                d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
                d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay()||7));
                var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
                return Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7);
            }

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: reservaParaEditar ? 'timeGridDay' : 'timeGridWeek',
                initialDate: parametrosInicio.get('fecha') || undefined,
                locale: 'es',
                firstDay: 0,
                views: { dayGridMonth: { firstDay: 0 } },
                height: 800,
                allDaySlot: false, 
                slotEventOverlap: false,
                eventOrder: compareEventsBySalon,
                eventOrderStrict: true,
                
                eventClassNames: function(arg) {
                    var finEvento = arg.event.end || arg.event.start;
                    return finEvento && finEvento <= new Date() ? ['reserva-finalizada'] : [];
                },
                slotMinTime: '08:00:00',
                slotMaxTime: '22:00:00', 
                slotLabelFormat: { hour: 'numeric', minute: '2-digit', omitZeroMinute: false, meridiem: false }, 
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
                expandRows: true,
                selectable: true,

                events: function(fetchInfo, successCallback, failureCallback) {
                    fetch('/api/salones/reservas', { headers: { 'Accept': 'application/json' } })
                        .then(function(response) {
                            if (!response.ok) throw new Error('No se pudieron cargar las reservas.');
                            return response.json();
                        })
                        .then(function(events) {
                            var filteredEvents = selectedSalonFilter
                                ? events.filter(function(event) { return event.salon === selectedSalonFilter; })
                                : events;
                            
                            successCallback(filteredEvents);

                            if (reservaParaEditar) {
                                window.setTimeout(function() {
                                    var evento = calendar.getEventById(reservaParaEditar);
                                    if (evento) abrirEdicionReserva(evento);
                                    reservaParaEditar = null;
                                }, 0);
                            }
                        })
                        .catch(failureCallback);
                },

                eventContent: function(arg) {
                    var props = arg.event.extendedProps;
                    var contenido = document.createElement('div');
                    contenido.className = 'fc-reserva-contenido';

                    if (arg.view.type === 'dayGridMonth') {
                        var horaMes = document.createElement('span'); 
                        horaMes.className = 'fc-reserva-hora';
                        horaMes.textContent = arg.timeText.split(' - ')[0] || arg.timeText; 
                        contenido.appendChild(horaMes);
                        
                        var tituloMes = document.createElement('span');
                        tituloMes.className = 'fc-reserva-titulo';
                        if (props.estado === 'cancelado') {
                            tituloMes.classList.add('fc-reserva-titulo-cancelado');
                            var indicadorMes = document.createElement('span');
                            indicadorMes.className = 'fc-reserva-indicador-cancelado';
                            indicadorMes.setAttribute('aria-label', 'Evento cancelado');
                            indicadorMes.title = 'Cancelado';
                            tituloMes.appendChild(indicadorMes);
                        }
                        var nombreMes = document.createElement('span');
                        nombreMes.textContent = arg.event.title || '';
                        tituloMes.appendChild(nombreMes);
                        contenido.appendChild(tituloMes);
                        
                        return { domNodes: [contenido] };
                    }

                    var hora = document.createElement('div');
                    hora.className = 'fc-reserva-hora';
                    hora.textContent = arg.timeText || '';
                    contenido.appendChild(hora);

                    if (arg.view.type === 'timeGridWeek') {
                        contenido.classList.add('fc-reserva-contenido-semana');
                        if (props.estado === 'cancelado') {
                            var indicadorSemana = document.createElement('span');
                            indicadorSemana.className = 'fc-reserva-indicador-cancelado';
                            indicadorSemana.setAttribute('aria-label', 'Evento cancelado');
                            indicadorSemana.title = 'Cancelado';
                            contenido.appendChild(indicadorSemana);
                        }
                        var horaInicio = arg.event.start;
                        hora.textContent = '';
                        var hora24 = horaInicio.getHours();
                        var hora12 = hora24 % 12 || 12;

                        var horaNumero = document.createElement('span');
                        horaNumero.textContent = String(hora12).padStart(2, '0');

                        var minutos = document.createElement('span');
                        minutos.className = 'fc-reserva-minutos-semana';
                        minutos.textContent = String(horaInicio.getMinutes()).padStart(2, '0');

                        var periodo = document.createElement('span');
                        periodo.className = 'fc-reserva-periodo-semana';
                        periodo.textContent = hora24 < 12 ? 'AM' : 'PM';

                        hora.appendChild(horaNumero);
                        hora.appendChild(minutos);
                        hora.appendChild(periodo);

                        return { domNodes: [contenido] };
                    }

                    var detalles = [
                        { valor: arg.event.title, clase: 'fc-reserva-titulo' },
                        { valor: props.nombre_autoridad, clase: 'fc-reserva-detalle' },
                        { valor: props.responsable, clase: 'fc-reserva-detalle' },
                        { valor: props.nombre_usuario, clase: 'fc-reserva-detalle' }
                    ];

                    detalles.forEach(function(detalle) {
                        if (detalle.valor === null || detalle.valor === undefined || String(detalle.valor).trim() === '') return;
                        var linea = document.createElement('div');
                        linea.className = detalle.clase;
                        if (detalle.clase === 'fc-reserva-titulo') {
                            if (props.estado === 'cancelado') {
                                var estadoDia = document.createElement('span');
                                estadoDia.className = 'fc-reserva-estado-cancelado';
                                estadoDia.textContent = 'CANCELADO';
                                linea.appendChild(estadoDia);
                            }

                            var tituloTexto = document.createElement('span');
                            tituloTexto.textContent = detalle.valor;
                            linea.appendChild(tituloTexto);
                        } else {
                            linea.textContent = detalle.valor;
                        }
                        contenido.appendChild(linea);
                    });

                    return { domNodes: [contenido] };
                },

                eventDidMount: function(info) {
                    info.el.dataset.reservaId = info.event.id;
                    posicionarEventoEnCarril(info);

                    if (info.view.type === 'timeGridDay') {
                        var contenidoDia = info.el.querySelector('.fc-reserva-contenido');
                        if (contenidoDia) {
                            contenidoDia.addEventListener('wheel', function(eventoRueda) {
                                if (contenidoDia.scrollHeight > contenidoDia.clientHeight) {
                                    eventoRueda.stopPropagation();
                                }
                            }, { passive: true });
                        }
                    }

                    var props = info.event.extendedProps;
                    var detalles = [
                        info.event.title,
                        props.nombre_autoridad,
                        props.responsable,
                        props.tipo_evento,
                        props.salon,
                        props.nombre_usuario,
                        props.modificado_por
                    ].filter(function(valor) {
                        return valor !== null && valor !== undefined && String(valor).trim() !== '';
                    });
                    if (props.estado === 'cancelado') detalles.push('Estado: Cancelado');

                    info.el.setAttribute('title', detalles.join('\n'));
                    info.el.setAttribute('aria-label', detalles.join(', '));
                },

                eventsSet: function(info) {
                    var viewType = calendar.view.type;
                    
                    if (document.getElementById('vista-disponible-container').style.display === 'block') {
                        renderTablaDisponible();
                    }

                    if (viewType !== 'timeGridDay' && viewType !== 'timeGridWeek') return;

                    requestAnimationFrame(function() {
                        calendar.updateSize();
                        requestAnimationFrame(function() {
                            reaplicarCarrilesEnVista(viewType);
                        });
                    });
                },

                select: function(info) {
                    if (rolActual === 'usuario') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Calendario de consulta',
                            text: 'Tu cuenta puede consultar el calendario, pero no crear ni modificar reservas.',
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#2563eb'
                        });
                        return;
                    }

                    var modal = document.getElementById('booking-modal');
                    establecerFechaHora('modal-start', info.startStr);
                    establecerFechaHora('modal-end', info.endStr);
                    var salon = salonSeleccionadoEnCarril(info);
                    var salonSelect = modal.querySelector('select[name="salon"]');
                    if (salon && salonSelect && Array.from(salonSelect.options).some(function(option) { return option.value === salon; })) {
                        salonSelect.value = salon;
                    }
                    modal.style.display = 'flex';
                },

                eventClick: function(info) {
                    abrirEdicionReserva(info.event);
                },

                dayHeaderContent: function(arg) {
                    if (arg.view.type === 'timeGridDay') {
                        var nombresDiasCompletos = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
                        var nombresMeses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                        var fechaCompleta = nombresDiasCompletos[arg.date.getDay()] + ' '
                            + arg.date.getDate() + ' '
                            + nombresMeses[arg.date.getMonth()] + ' '
                            + arg.date.getFullYear();
                        var hoy = new Date();
                        var esHoy = arg.date.getFullYear() === hoy.getFullYear()
                            && arg.date.getMonth() === hoy.getMonth()
                            && arg.date.getDate() === hoy.getDate();
                        var estiloFechaDia = esHoy
                            ? 'display: table; margin: 4px auto; padding: 8px 16px; border-radius: 999px; background: #eff6ff; color: #2563eb; box-shadow: 0 0 0 1px #bfdbfe; text-align: center; font-size: 14px; font-weight: 600;'
                            : 'text-align: center; color: #475b75; font-size: 14px; font-weight: 400; padding: 10px 0;';

                        return { html: '<div style="' + estiloFechaDia + '">' + fechaCompleta + '</div>' };
                    }

                    var dayNames = ['DO', 'LU', 'MA', 'MI', 'JU', 'VI', 'SA'];
                    var dayStr = dayNames[arg.date.getDay()];
                    if (arg.view.type === 'dayGridMonth') {
                        return { html: '<div style="text-align: center; text-transform: uppercase; font-size: 0.8rem; color: #64748b; font-weight: 400;">' + dayStr + '</div>' };
                    }
                    var dateNum = arg.date.getDate();
                    var hoy = new Date();
                    var esHoy = arg.view.type === 'timeGridWeek'
                        && arg.date.getFullYear() === hoy.getFullYear()
                        && arg.date.getMonth() === hoy.getMonth()
                        && arg.date.getDate() === hoy.getDate();
                    var dateStyle = esHoy
                        ? 'display: flex; align-items: center; justify-content: center; width: 58px; height: 42px; margin: 2px auto 0; border-radius: 999px; background: #2563eb; color: #fff; font-size: 1.6rem; font-weight: 700;'
                        : 'text-align: center; font-size: 1.6rem; color: #475b75; font-weight: 400; margin-top: 2px;';
                    return { 
                        html: '<div style="text-align: center; text-transform: uppercase; font-size: 0.8rem; color: #64748b; font-weight: 400;">' + dayStr + '</div>' +
                              '<div style="' + dateStyle + '">' + dateNum + '</div>'
                    };
                },
                datesSet: function(info) {
                    ajustarAnchoCarriles(info.view.type);

                    if (info.view.type === 'timeGridDay' || info.view.type === 'timeGridWeek') {
                        requestAnimationFrame(function() {
                            calendar.updateSize();
                            requestAnimationFrame(function() {
                                reaplicarCarrilesEnVista(info.view.type);
                            });
                        });
                    }

                    var titleEl = document.getElementById('calendar-title');
                    if (info.view.type === 'timeGridWeek') {
                        var weekNumber = getWeekNumber(info.view.currentStart);
                        titleEl.innerText = 'Semana ' + weekNumber;
                    } else {
                        titleEl.innerText = info.view.title;
                    }

                    if (typeof updateMiniCalendar === 'function') {
                        var focusedDate = info.view.calendar.getDate();
                        currentMiniYear = focusedDate.getFullYear();
                        currentMiniMonth = focusedDate.getMonth() + 1;
                        updateMiniCalendar(currentMiniYear, currentMiniMonth);
                    }
                }
            });
            
            calendar.render();
            window.setInterval(function() { calendar.rerenderEvents(); }, 60000);

            // CONTROL DEL MENÚ DE VISTAS Y LA VISTA "DISPONIBLE"
            var btnDropdown = document.getElementById('btn-view-dropdown');
            var viewMenu = document.getElementById('view-menu');
            var currentViewText = document.getElementById('current-view-text');
            var viewOptions = document.querySelectorAll('.view-option');
            var containerCalendar = document.getElementById('calendar-container');
            var containerDisponible = document.getElementById('vista-disponible-container');
            var toolBotonesNav = document.getElementById('toolbar-botones-navegacion');

            btnDropdown.addEventListener('click', function(e) {
                e.preventDefault(); e.stopPropagation();
                viewMenu.style.display = (viewMenu.style.display === 'none' || viewMenu.style.display === '') ? 'block' : 'none';
            });

            document.addEventListener('click', function(e) {
                if (!btnDropdown.contains(e.target) && !viewMenu.contains(e.target)) viewMenu.style.display = 'none';
            });

            viewOptions.forEach(function(option) {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    var viewName = this.getAttribute('data-view');

                    if (viewName === 'disponible') {
                        containerCalendar.style.display = 'none';
                        toolBotonesNav.style.display = 'none'; // Ocultamos los botones de mes/semana/día cuando vemos esta tabla
                        containerDisponible.style.display = 'block';
                        renderTablaDisponible();
                    } else {
                        containerCalendar.style.display = 'block';
                        toolBotonesNav.style.display = 'flex';
                        containerDisponible.style.display = 'none';
                        calendar.changeView(viewName);
                    }
                    
                    currentViewText.innerText = this.innerText;
                    viewOptions.forEach(opt => {
                        opt.style.fontWeight = 'normal';
                        opt.style.backgroundColor = 'transparent';
                        opt.style.color = 'inherit';
                    });
                    this.style.fontWeight = 'bold';
                    this.style.backgroundColor = '#f9fafb';
                    this.style.color = '#1f2937';
                    viewMenu.style.display = 'none';
                });
            });

            // ACCIONES DEL BUSCADOR DE ESPACIOS
            document.getElementById('btn-buscar-gap').addEventListener('click', function() {
                let fechaInput = leerFechaHora('filtro-gap-fecha');
                if (!fechaInput) {
                    alert('Por favor, seleccione una fecha base para buscar.');
                    return;
                }
                gapFilterActive = true;
                gapStartDate = new Date(Number(fechaInput.slice(0, 4)), Number(fechaInput.slice(5, 7)) - 1, Number(fechaInput.slice(8, 10)), Number(fechaInput.slice(11, 13)), Number(fechaInput.slice(14, 16)));
                gapDurationMins = parseInt(document.getElementById('filtro-gap-minutos').value) || 60;
                gapSalon = document.getElementById('filtro-gap-salon').value;
                
                renderTablaDisponible();
            });

            document.getElementById('btn-vaciar-gap').addEventListener('click', function(e) {
                e.preventDefault();
                gapFilterActive = false;
                establecerFechaHora('filtro-gap-fecha', '');
                document.getElementById('filtro-gap-salon').value = 'any';
                renderTablaDisponible();
            });

            document.getElementById('btn-prev').addEventListener('click', function() { calendar.prev(); });
            document.getElementById('btn-next').addEventListener('click', function() { calendar.next(); });
            document.getElementById('btn-hoy').addEventListener('click', function() { calendar.today(); });

            // Modales
            document.getElementById('close-modal').addEventListener('click', () => { document.getElementById('booking-modal').style.display = 'none'; });
            document.getElementById('btn-cancel').addEventListener('click', () => { document.getElementById('booking-modal').style.display = 'none'; });
            document.getElementById('close-edit-modal').addEventListener('click', () => { document.getElementById('edit-booking-modal').style.display = 'none'; });
            document.getElementById('btn-edit-cancel').addEventListener('click', () => { document.getElementById('edit-booking-modal').style.display = 'none'; });

            var editModalPanel = document.getElementById('edit-modal-panel');
            var editModalHandle = document.getElementById('edit-modal-drag-handle');
            setupDraggableModal(editModalPanel, editModalHandle);

            var bookingModalPanel = document.getElementById('booking-modal-panel');
            var bookingModalHandle = document.getElementById('booking-modal-drag-handle');
            setupDraggableModal(bookingModalPanel, bookingModalHandle);

            function setupDraggableModal(panel, handle) {
                if (!panel || !handle) return;
                var movimientoModal = null;

                handle.addEventListener('pointerdown', function(event) {
                    if (event.target.closest('button')) return;
                    var bounds = panel.getBoundingClientRect();
                    movimientoModal = { x: event.clientX, y: event.clientY, left: bounds.left, top: bounds.top };
                    panel.style.left = bounds.left + 'px';
                    panel.style.top = bounds.top + 'px';
                    panel.style.right = 'auto';
                    handle.setPointerCapture(event.pointerId);
                });

                handle.addEventListener('pointermove', function(event) {
                    if (!movimientoModal) return;
                    var left = movimientoModal.left + event.clientX - movimientoModal.x;
                    var top = movimientoModal.top + event.clientY - movimientoModal.y;
                    var maxLeft = window.innerWidth - panel.offsetWidth;
                    var maxTop = window.innerHeight - panel.offsetHeight;
                    panel.style.left = Math.max(0, Math.min(left, maxLeft)) + 'px';
                    panel.style.top = Math.max(0, Math.min(top, maxTop)) + 'px';
                });

                function terminarMovimiento() { movimientoModal = null; }
                handle.addEventListener('pointerup', terminarMovimiento);
                handle.addEventListener('pointercancel', terminarMovimiento);
            }

            document.getElementById('edit-booking-form').addEventListener('submit', function(e) {
                e.preventDefault();
                var eventId = document.getElementById('edit-event-id').value;
                var formData = new FormData(this);
                formData.append('_method', 'PUT');

                fetch('/api/salones/reservas/' + eventId, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'Accept': 'application/json' },
                    body: formData
                }).then(res => {
                    if (res.status === 422) {
                        return res.json().then(data => {
                            Swal.fire({ icon: 'error', title: 'No se pudo actualizar', text: data.message || 'El salón está ocupado.', confirmButtonColor: '#2563eb' });
                            throw new Error('Conflicto');
                        });
                    }
                    return res.json();
                }).then(data => {
                    if(data.success) {
                        document.getElementById('edit-booking-modal').style.display = 'none';
                        calendar.refetchEvents();
                        miniEventDatesCache = {};
                        updateMiniCalendar(currentMiniYear, currentMiniMonth);
                        Swal.fire({ icon: 'success', title: '¡Actualizado!', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                    }
                }).catch(err => console.error(err));
            });

            var deleteButton = document.getElementById('btn-edit-delete');
            if (deleteButton) deleteButton.addEventListener('click', function() {
                if(confirm('¿Estás seguro de que deseas eliminar esta reserva?')) {
                    fetch('/api/salones/reservas/' + document.getElementById('edit-event-id').value, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'Accept': 'application/json' }
                    }).then(res => res.json()).then(data => {
                        if(data.success) {
                            document.getElementById('edit-booking-modal').style.display = 'none';
                            calendar.refetchEvents();
                            miniEventDatesCache = {};
                            updateMiniCalendar(currentMiniYear, currentMiniMonth);
                        }
                    });
                }
            });

            document.getElementById('booking-form').addEventListener('submit', function(e) {
                e.preventDefault();
                fetch('/api/salones/reservas', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'Accept': 'application/json' },
                    body: new FormData(this)
                }).then(res => {
                    if (res.status === 422) {
                        return res.json().then(data => {
                            Swal.fire({ icon: 'error', title: '¡Conflicto!', text: data.message || 'El salón ya está ocupado.', confirmButtonColor: '#2563eb' });
                            throw new Error('Conflicto');
                        });
                    }
                    return res.json();
                }).then(data => {
                    if(data.success) {
                        document.getElementById('booking-modal').style.display = 'none';
                        calendar.refetchEvents();
                        miniEventDatesCache = {};
                        updateMiniCalendar(currentMiniYear, currentMiniMonth);
                        Swal.fire({ icon: 'success', title: '¡Completada!', toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 });
                    }
                }).catch(err => console.error(err));
            });

            var currentMiniYear = 2026;
            var currentMiniMonth = 10;

            function updateMiniCalendar(year, month) {
                var monthNames = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
                document.getElementById('mini-cal-month-label').innerText = monthNames[month - 1];
                document.getElementById('mini-cal-year-label').innerText = year;

                var salonQuery = selectedSalonFilter ? `&salon=${encodeURIComponent(selectedSalonFilter)}` : '';
                var cacheKey = `${year}-${month}-${selectedSalonFilter || 'all'}`;
                if (!miniEventDatesCache[cacheKey]) {
                    miniEventDatesCache[cacheKey] = fetch(`${urlFechasReservas}?year=${year}&month=${month}${salonQuery}`).then(response => response.json());
                }

                miniEventDatesCache[cacheKey].then(eventDates => {
                    var firstDay = new Date(year, month - 1, 1);
                    var lastDay = new Date(year, month, 0);
                    var startingDay = firstDay.getDay();
                    var totalDays = lastDay.getDate();

                    var tbody = document.getElementById('mini-cal-body');
                    tbody.innerHTML = '';
                    var row = document.createElement('tr');
                    var realToday = new Date(); realToday.setHours(0,0,0,0);
                    var viewStart = calendar.view.currentStart;
                    var viewEnd = calendar.view.currentEnd;

                    var prevMonthLastDay = new Date(year, month - 1, 0).getDate();
                    for (var i = startingDay - 1; i >= 0; i--) {
                        var td = document.createElement('td'); td.innerText = prevMonthLastDay - i; td.style.color = '#d1d5db'; row.appendChild(td);
                    }

                    for (var day = 1; day <= totalDays; day++) {
                        if (row.children.length === 7) { tbody.appendChild(row); row = document.createElement('tr'); }

                        var td = document.createElement('td'); td.innerText = day; td.className = 'mc-hover';
                        var fullDateStr = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                        td.setAttribute('data-date', fullDateStr);

                        var cellDate = new Date(year, month - 1, day); cellDate.setHours(0,0,0,0);
                        var calendarDate = new Date(calendar.getDate()); calendarDate.setHours(0,0,0,0);
                        
                        if (cellDate.getTime() === calendarDate.getTime()) td.classList.add('mc-selected-date');
                        if (cellDate.getTime() === realToday.getTime()) td.classList.add('mc-today-real');

                        if (Array.isArray(eventDates) && eventDates.includes(fullDateStr)) {
                            td.classList.add('mc-has-event');
                            td.style.setProperty('background-color', (selectedSalonFilter ? salonColors[selectedSalonFilter] : '#f59e0b') || '#f59e0b', 'important');
                        }

                        if (viewStart && viewEnd && cellDate.getTime() >= viewStart.getTime() && cellDate.getTime() < viewEnd.getTime()) {
                            row.classList.add('mc-row-selected');
                        }

                        td.addEventListener('click', function() {
                            var fechaElegida = this.getAttribute('data-date');
                            calendar.gotoDate(fechaElegida);

                            if (document.getElementById('vista-disponible-container').style.display === 'block') {
                                var fechaActual = leerFechaHora('filtro-gap-fecha');
                                var horaElegida = fechaActual ? fechaActual.slice(11, 16) : '08:30';
                                establecerFechaHora('filtro-gap-fecha', fechaElegida + 'T' + horaElegida);
                                gapStartDate = new Date(
                                    Number(fechaElegida.slice(0, 4)),
                                    Number(fechaElegida.slice(5, 7)) - 1,
                                    Number(fechaElegida.slice(8, 10)),
                                    Number(horaElegida.slice(0, 2)),
                                    Number(horaElegida.slice(3, 5))
                                );
                                gapFilterActive = true;
                                gapDurationMins = parseInt(document.getElementById('filtro-gap-minutos').value, 10) || 60;
                                gapSalon = document.getElementById('filtro-gap-salon').value;
                                renderTablaDisponible();
                            }
                        });
                        row.appendChild(td);
                    }

                    var nextMonthDay = 1;
                    while (row.children.length < 7) {
                        var td = document.createElement('td'); td.innerText = nextMonthDay++; td.style.color = '#d1d5db'; row.appendChild(td);
                    }
                    tbody.appendChild(row);
                }).catch(err => { delete miniEventDatesCache[cacheKey]; console.error(err); });
            }

            document.getElementById('mini-prev').addEventListener('click', function() {
                currentMiniMonth--; if (currentMiniMonth < 1) { currentMiniMonth = 12; currentMiniYear--; }
                updateMiniCalendar(currentMiniYear, currentMiniMonth);
            });
            document.getElementById('mini-next').addEventListener('click', function() {
                currentMiniMonth++; if (currentMiniMonth > 12) { currentMiniMonth = 1; currentMiniYear++; }
                updateMiniCalendar(currentMiniYear, currentMiniMonth);
            });

            updateMiniCalendar(currentMiniYear, currentMiniMonth);

            document.querySelectorAll('.salon-filter').forEach(function(button) {
                button.addEventListener('click', function() {
                    selectedSalonFilter = this.dataset.salon === 'all' ? null : this.dataset.salon;
                    calendar.setOption('slotEventOverlap', selectedSalonFilter !== null);
                    document.querySelectorAll('.salon-filter').forEach(b => {
                        var isAct = b === button;
                        b.classList.toggle('active', isAct);
                        b.setAttribute('aria-pressed', isAct ? 'true' : 'false');
                    });
                    calendar.refetchEvents();
                    updateMiniCalendar(currentMiniYear, currentMiniMonth);
                });
            });
        });
    </script>
</x-app-layout>
