{{-- Cambio al español: 12/10/2026. La vista usa nombres de reservas y campos en español. --}}
<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/es.global.min.js'></script>

    <style>
        .fc .fc-toolbar { display: none !important; }
        .fc-theme-standard th, .fc-theme-standard td { border-color: #e2e8f0; }
        .fc-theme-standard th { border-top: none !important; padding-bottom: 8px; border-left: none !important; border-right: none !important; }
        .fc-scrollgrid { border: none !important; }
        
        .fc .fc-day-today { background-color: #fafafa !important; }
        /* Resalta solo el número de hoy en el mes, igual que el distintivo semanal. Cambio: 13/10/2026. */
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
        /* La hora queda en negrilla y los datos registrados aparecen debajo del título. */
        .fc-reserva-contenido { width: 100%; overflow: hidden; white-space: normal; overflow-wrap: anywhere; line-height: 1.12; }
        .fc-reserva-hora { margin-bottom: 3px; font-weight: 800; }
        .fc-reserva-titulo { font-weight: 600; }
        .fc-reserva-detalle { font-size: 0.92em; }
        /* ESTILOS PARA LA VISTA MENSUAL (Efecto pastilla delgada) */
        .fc-dayGridMonth-view .fc-event {
            border-radius: 99px !important; /* Bordes completamente redondeados */
            border: none !important;
            margin-bottom: 2px !important; /* Espaciado entre pastillas */
            overflow: hidden;
            box-shadow: none !important;
        }
        
        .fc-dayGridMonth-view .fc-event-main {
            padding: 2px 6px !important; /* Define la altura delgada de la pastilla */
            display: flex;
            align-items: center;
        }

        .fc-dayGridMonth-view .fc-reserva-contenido {
            display: flex;
            flex-direction: row; /* Alinea hora y título lado a lado */
            align-items: center;
            width: 100%;
            gap: 4px; /* Separación entre hora y título */
            line-height: 1;
        }

        .fc-dayGridMonth-view .fc-reserva-hora {
            font-weight: 800;
            font-size: 10px; /* Letra muy pequeña como en la referencia */
            margin-bottom: 0 !important; /* Sobreescribe el margen de las otras vistas */
            color: white;
            white-space: nowrap;
        }

        .fc-dayGridMonth-view .fc-reserva-titulo {
            font-weight: 500;
            font-size: 10px;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis; /* Corta el texto largo con "..." */
        }
        /* 1. Fijar altura uniforme para todos los cuadros del mes */
        .fc-dayGridMonth-view .fc-daygrid-day-frame {
            height: 140px !important; 
            display: flex;
            flex-direction: column;
        }

        /* 2. Habilitar scroll interno con la rueda del ratón para los eventos */
        .fc-dayGridMonth-view .fc-daygrid-day-events {
            flex-grow: 1;
            max-height: 105px !important; /* Limita el crecimiento */
            overflow-y: auto !important; /* Activa el desplazamiento interno */
            margin-bottom: 2px;
        }

        /* 3. Diseño elegante para la barra de scroll (Oculta la barra fea por defecto) */
        .fc-dayGridMonth-view .fc-daygrid-day-events::-webkit-scrollbar {
            width: 4px; /* Barra muy delgada */
        }
        .fc-dayGridMonth-view .fc-daygrid-day-events::-webkit-scrollbar-track {
            background: transparent; 
        }
        .fc-dayGridMonth-view .fc-daygrid-day-events::-webkit-scrollbar-thumb {
            background: #cbd5e1; /* Color plomo suave */
            border-radius: 10px;
        }
        .fc-dayGridMonth-view .fc-daygrid-day-events::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; /* Oscurece un poco al pasar el cursor */
        }
        /* Se atenúan únicamente eventos terminados, manteniendo el color del salón en todas las vistas. */
        .fc .fc-event.reserva-finalizada { opacity: 0.55; }

        /* Estilos del Mini Calendario */
        .mini-cal { width: 100%; text-align: center; font-size: 13px; border-collapse: separate; border-spacing: 2px; }
        .mini-cal th { color: #9ca3af; font-weight: normal; font-size: 12px; padding-bottom: 4px; }
        .mini-cal td { padding: 5px 0; border-radius: 4px; cursor: pointer; color: #4b5563; font-weight: 500; transition: background 0.2s; background-color: #ffffff; }
        
        .mc-has-event { background-color: #f59e0b !important; color: white !important; font-weight: bold; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        /* Selección sencilla: borde azul alrededor del día, conservando el color de evento. */
        .mc-selected-date { position: relative; z-index: 2; outline: 2px solid #2563eb; outline-offset: 2px; border-radius: 5px !important; color: white !important; font-weight: bold !important; }
        .mc-selected-date:not(.mc-has-event) { background-color: #eab308 !important; }
        .mc-hover:hover { background-color: #f3f4f6 !important; }

        /* Las etiquetas son filtros; el salón activo muestra una marca de selección. */
        .salon-filter { border: 0; font: inherit; color: white; font-weight: bold; cursor: pointer; transition: box-shadow 0.15s, transform 0.15s; }
        .salon-filter:hover { transform: translateY(-1px); }
        .salon-filter:not(.active) .salon-filter-check { display: none; }
        .salon-filter.active { box-shadow: 0 0 0 2px white, 0 0 0 4px #334155; }
        
        /* Contorno azul para el día actual real (nunca cambia) */
        .mc-today-real {
            border: 2px solid #2563eb !important;
            color: #2563eb !important;
            font-weight: bold;
        }

        /* Fila completa en tono plomo cuando la semana está seleccionada */
        .mc-row-selected td {
            background-color: #e2e8f0 !important;
        }
        /* Mantener color en el día de hoy o eventos aunque la fila esté seleccionada */
        .mc-row-selected td.mc-today-real {
            background-color: #ffffff !important;
            border: 2px solid #2563eb !important;
        }
        .mc-row-selected td.mc-has-event {
            background-color: #f59e0b !important;
        }

        /* En móviles, el calendario conserva su legibilidad y se desplaza horizontalmente. */
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
            
            <div class="salones-page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                <div>
                    <h1 style="font-size: 28px; font-weight: bold; color: #475b75; margin-bottom: 4px;">Horario para salones</h1>
                    <p style="font-size: 14px; color: #9ca3af;">Haga clic en un espacio libre para crear una nueva reserva. Haga clic en cualquier reserva para editarla.</p>
                </div>
                
                <div style="display: flex; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <button style="background-color: #f9fafb; padding: 6px 16px; font-weight: bold; color: #374151; border-right: 1px solid #e5e7eb; font-size: 14px;">
                        <i class="fa-regular fa-calendar text-blue-500" style="margin-right: 8px;"></i> Usar
                    </button>
                    <a href="{{ route('salones.supervisar') }}" style="background-color: white; padding: 6px 16px; color: #3b82f6; font-size: 14px; text-decoration: none;">
                        <i class="fa-solid fa-sliders" style="margin-right: 8px;"></i> Supervisar
                    </a>
                </div>
            </div>

            <div class="salones-cal-layout" style="display: flex; gap: 30px;">
                
                <!-- CALENDARIO GRANDE -->
                <div class="salones-cal-main" style="flex: 1; min-width: 0;">
                    <div class="salones-cal-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        
                        <div style="position: relative;" id="view-dropdown-container">
                            <button id="btn-view-dropdown" style="border: 1px solid #d1d5db; border-radius: 4px; padding: 5px 12px; font-size: 14px; background: white; color: #374151; cursor: pointer; display: flex; align-items: center;">
                                <span id="current-view-text">Semana</span> 
                                <i class="fa-solid fa-chevron-down" style="color: #3b82f6; font-size: 12px; margin-left: 10px;"></i>
                            </button>
                            
                            <div id="view-menu" style="display: none; position: absolute; left: 0; top: 100%; margin-top: 4px; width: 140px; background: white; border: 1px solid #e5e7eb; border-radius: 4px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); z-index: 50; padding: 6px 0; font-size: 14px; color: #4b5563;">
                                {{-- Cada opción cambia la vista del calendario grande. --}}
                                <a href="#" class="view-option" data-view="dayGridMonth" style="display: block; padding: 6px 16px; text-decoration: none; color: inherit;">Mes</a>
                                <a href="#" class="view-option" data-view="timeGridWeek" style="display: block; padding: 6px 16px; text-decoration: none; color: #1f2937; font-weight: bold; background-color: #f9fafb;">Semana</a>
                                <a href="#" class="view-option" data-view="timeGridDay" style="display: block; padding: 6px 16px; text-decoration: none; color: inherit;">Día</a>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 24px;">
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

                    <div class="salones-calendar-scroll"><div id="calendar" style="background: white;"></div></div>
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
                        {{-- Todos inicia activo y vuelve a mostrar reservas de cualquier salón. --}}
                        <button type="button" class="salon-filter active" data-salon="all" aria-pressed="true" style="background-color: #9ca3af; padding: 4px 8px; border-radius: 4px;">
                            <i class="fa-solid fa-check salon-filter-check" aria-hidden="true" style="margin-right: 4px;"></i>Todos
                        </button>
                        {{-- Genera un botón por salón con el mismo color de sus eventos. --}}
                        @foreach($salones as $salon)
                            <button type="button" class="salon-filter" data-salon="{{ $salon->nombre }}" aria-pressed="false" style="background-color: {{ $salon->color }}; padding: 4px 8px; border-radius: 4px;">
                                <i class="fa-solid fa-check salon-filter-check" aria-hidden="true" style="margin-right: 4px;"></i>{{ $salon->nombre }}
                            </button>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
        <!-- MODAL FLOTANTE "NUEVA RESERVA" -->
        <div id="booking-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
            <div style="background: white; width: 600px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow: hidden;">
                
                <!-- Cabecera del Modal -->
                <div style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-size: 16px; font-weight: bold; color: #475b75;">Nueva reserva</h3>
                    <button id="close-modal" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #9ca3af;"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <!-- Cuerpo del Formulario -->
                <form id="booking-form" style="padding: 20px;">
                    @csrf
                    <div style="display: flex; gap: 10px; margin-bottom: 16px; align-items: center;">
                        <i class="fa-regular fa-clock" style="color: #6b7280;"></i>
                        <input type="text" id="modal-start" name="fecha_inicio" style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px; font-size: 13px; width: 180px;" required>
                        <span>hasta</span>
                        <input type="text" id="modal-end" name="fecha_fin" style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px; font-size: 13px; width: 180px;" required>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <textarea id="modal-title" name="titulo" placeholder="Descripción *" rows="2" style="width: 100%; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px; font-size: 13px;" required></textarea>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <input type="text" id="modal-username" name="nombre_usuario" value="{{ Auth::user()->nombre }}" placeholder="Nombre de usuario" style="width: 100%; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px; font-size: 13px; background: #f9fafb;">
                    </div>

                    <div style="margin-bottom: 16px;">
                        <input type="text" id="modal-phone" name="telefono_usuario" placeholder="Teléfono" style="width: 100%; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px; font-size: 13px;">
                    </div>

                    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;">

                    <div style="margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Nombre de autoridad *</label>
                        <input type="text" name="nombre_autoridad" style="flex: 1; border: 1px solid #d1d5db; border-radius: 4px; padding: 6px; font-size: 13px;">
                    </div>

                    <div style="margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Responsable del evento *</label>
                        <textarea name="responsable" rows="2" style="flex: 1; border: 1px solid #d1d5db; border-radius: 4px; padding: 6px; font-size: 13px;"></textarea>
                    </div>

                    <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Salones</label>
                        <select name="salon" style="flex: 1; border: 1px solid #d1d5db; border-radius: 4px; padding: 6px; font-size: 13px;" required>
                            {{-- Ofrece al crear la misma lista de salones que aparece en la leyenda. --}}
                            @foreach($salonesActivos as $salon)
                                <option value="{{ $salon->nombre }}">{{ $salon->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Botones de Acción -->
                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                        <button type="button" id="btn-cancel" style="background: white; border: 1px solid #d1d5db; padding: 6px 16px; border-radius: 4px; font-size: 13px; cursor: pointer;">Cancelar</button>
                        <button type="submit" style="background: #2563eb; color: white; border: none; padding: 6px 16px; border-radius: 4px; font-size: 13px; font-weight: bold; cursor: pointer;">Crear reserva</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL FLOTANTE "EDITAR RESERVA" -->
        <div id="edit-booking-modal" style="display: none; position: fixed; inset: 0; background: transparent; z-index: 100;">
            <div id="edit-modal-panel" class="edit-modal-panel" style="position: fixed; top: 12vh; right: 5vw; width: min(600px, calc(100vw - 32px)); max-height: 84vh; overflow-y: auto; background: rgba(255,255,255,0.94); backdrop-filter: blur(14px); border: 1px solid rgba(203,213,225,0.8); border-radius: 10px; box-shadow: 0 18px 50px rgba(15,23,42,0.18);">
                
                <!-- Cabecera del Modal -->
                <div id="edit-modal-drag-handle" class="edit-modal-drag-handle" style="position: sticky; top: 0; z-index: 1; padding: 16px 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background: rgba(248,250,252,0.92); backdrop-filter: blur(14px);">
                    <h3 style="font-size: 16px; font-weight: bold; color: #475b75;">Editar reserva</h3>
                    <button id="close-edit-modal" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #9ca3af;"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <!-- Cuerpo del Formulario -->
                <form id="edit-booking-form" style="padding: 20px;">
                    @csrf
                    <input type="hidden" id="edit-event-id" name="id">
                    
                    <div style="display: flex; gap: 10px; margin-bottom: 16px; align-items: center;">
                        <i class="fa-regular fa-clock" style="color: #6b7280; width: 20px; text-align: center;"></i>
                        <input type="text" id="edit-start" name="fecha_inicio" style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px; font-size: 13px; width: 160px;" required>
                        <span style="font-size: 13px; color: #4b5563;">hasta</span>
                        <input type="text" id="edit-end" name="fecha_fin" style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px; font-size: 13px; width: 160px;" required>
                    </div>

                    <div style="margin-bottom: 16px; display: flex; gap: 10px; align-items: flex-start;">
                        <i class="fa-solid fa-align-left" style="color: #6b7280; width: 20px; text-align: center; margin-top: 10px;"></i>
                        <textarea id="edit-title" name="titulo" rows="2" style="flex: 1; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px; font-size: 13px; background: #f9fafb;" required></textarea>
                    </div>

                    <div style="margin-bottom: 16px; display: flex; gap: 10px; align-items: center;">
                        <i class="fa-solid fa-user" style="color: #6b7280; width: 20px; text-align: center;"></i>
                        <input type="text" id="edit-username" name="nombre_usuario" style="width: 250px; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px; font-size: 13px; background: #f9fafb;">
                    </div>

                    <div style="margin-bottom: 16px; display: flex; gap: 10px; align-items: center;">
                        <i class="fa-solid fa-mobile-screen" style="color: #6b7280; width: 20px; text-align: center;"></i>
                        <input type="text" id="edit-phone" name="telefono_usuario" style="width: 250px; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px; font-size: 13px; background: #f9fafb;">
                    </div>

                    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;">

                    <div style="margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Nombre de autoridad *</label>
                        <input type="text" id="edit-authority" name="nombre_autoridad" style="flex: 1; border: 1px solid #d1d5db; border-radius: 4px; padding: 6px; font-size: 13px; background: #f9fafb;">
                    </div>

                    <div style="margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Responsable del evento *</label>
                        <textarea id="edit-responsable" name="responsable" rows="2" style="flex: 1; border: 1px solid #d1d5db; border-radius: 4px; padding: 6px; font-size: 13px; background: #f9fafb;"></textarea>
                    </div>

                    <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <label style="width: 160px; font-size: 13px; color: #4b5563;">Salones</label>
                        <select id="edit-salon" name="salon" style="width: 200px; border: 1px solid #d1d5db; border-radius: 4px; padding: 6px; font-size: 13px;" required>
                            {{-- Reutiliza las mismas opciones cuando se edita una reserva. --}}
                            @foreach($salones as $salon)
                                <option value="{{ $salon->nombre }}" {{ !$salon->activo ? 'disabled' : '' }}>{{ $salon->nombre }}{{ !$salon->activo ? ' (inactivo)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Footer y Botones de Acción -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <div>
                            <span id="edit-footer-text" style="font-size: 11px; color: #9ca3af;"></span>
                        </div>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            
                            <button type="button" id="btn-edit-cancel" style="background: white; border: 1px solid #d1d5db; padding: 6px 16px; border-radius: 4px; font-size: 13px; cursor: pointer;">Cerrar</button>
                            
                            {{-- Admin y superadmin pueden editar reservas desde el calendario. --}}
                            @if(auth()->user()->rol === 'admin' || auth()->user()->rol === 'superadmin')
                                <button type="button" id="btn-edit-protocol" style="background: white; color: #3b82f6; border: 1px solid #3b82f6; padding: 6px 10px; border-radius: 4px; cursor: pointer;" title="Formulario de protocolo">
                                    <i class="fa-solid fa-file-lines"></i>
                                </button>
                                
                                <button type="submit" id="btn-edit-update" style="background: #2563eb; color: white; border: none; padding: 6px 16px; border-radius: 4px; font-size: 13px; font-weight: bold; cursor: pointer;">Actualizar reserva</button>
                            @endif

                            {{-- Solo Admin puede eliminar reservas desde el calendario grande. --}}
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
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            // null representa el filtro "Todos"; cualquier otro valor es el salón seleccionado.
            var selectedSalonFilter = null;
            // Guarda los días ocupados por mes para evitar repetir la misma consulta al navegar.
            var miniEventDatesCache = {};
            // Se usa el mismo mapa de colores de la leyenda para colorear los días del mini calendario.
            var salonColors = @json($coloresSalones);
            var urlFechasReservas = @json(route('api.reservas.fechas'));
            var parametrosInicio = new URLSearchParams(window.location.search);
            var reservaParaEditar = parametrosInicio.get('editar');
            // El orden de la leyenda define la posición de los salones en las tres vistas.
            var salonOrder = Object.keys(salonColors);

            function compareEventsBySalon(a, b) {
                var salonA = a.extendedProps.salon;
                var salonB = b.extendedProps.salon;
                var positionA = salonOrder.indexOf(salonA);
                var positionB = salonOrder.indexOf(salonB);

                // Los salones antiguos o fuera de catálogo se muestran al final.
                if (positionA === -1) positionA = salonOrder.length;
                if (positionB === -1) positionB = salonOrder.length;

                // Si son del mismo salón, conserva un orden cronológico dentro del día.
                return positionA - positionB || a.start - b.start || a.title.localeCompare(b.title);
            }

            function abrirEdicionReserva(evento) {
                var modal = document.getElementById('edit-booking-modal');
                var props = evento.extendedProps;

                document.getElementById('edit-event-id').value = evento.id;
                document.getElementById('edit-start').value = evento.startStr.slice(0, 16).replace('T', ' ');
                document.getElementById('edit-end').value = evento.endStr ? evento.endStr.slice(0, 16).replace('T', ' ') : '';
                document.getElementById('edit-title').value = evento.title;
                document.getElementById('edit-username').value = props.nombre_usuario || '';
                document.getElementById('edit-phone').value = props.telefono_usuario || '';
                document.getElementById('edit-authority').value = props.nombre_autoridad || '';
                document.getElementById('edit-responsable').value = props.responsable || '';
                document.getElementById('edit-salon').value = props.salon || '';

                var fecha = props.fecha_creacion ? props.fecha_creacion.slice(0, 10) : '';
                document.getElementById('edit-footer-text').innerText = 'Creado el ' + fecha + ' por ' + (props.nombre_usuario || 'usuario');
                document.getElementById('btn-edit-protocol').onclick = function() {
                    window.location.href = '/salones/reservas/' + evento.id + '/protocolo';
                };
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
                height: 800, 
                allDaySlot: false, 
                // "Todos" está seleccionado al iniciar: separa visualmente reservas simultáneas.
                slotEventOverlap: false,
                // Mantiene el orden de la lista de salones en mes, semana y día.
                eventOrder: compareEventsBySalon,
                eventOrderStrict: true,
                // La opacidad depende de la hora de fin y aplica a las vistas de mes, semana y día.
                eventClassNames: function(arg) {
                    var finEvento = arg.event.end || arg.event.start;
                    return finEvento && finEvento <= new Date()
                        ? ['reserva-finalizada']
                        : [];
                },
                slotMinTime: '08:00:00',
                slotMaxTime: '18:00:00', 
                slotLabelFormat: { hour: 'numeric', minute: '2-digit', omitZeroMinute: false, meridiem: false }, 
                // En la vista mensual también se muestran hora y minutos en formato de 24 horas.
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
                expandRows: true,
                selectable: true, // Permitir seleccionar celdas
                // Pide los eventos a Laravel y conserva solo el salón seleccionado, si hay filtro.
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

                // En el bloque del evento la hora va en negrilla y debajo se listan los datos registrados.
                eventContent: function(arg) {
                    var props = arg.event.extendedProps;
                    var contenido = document.createElement('div');
                    contenido.className = 'fc-reserva-contenido';

                    // El mes adopta forma de pastilla delgada con elementos en línea
                    if (arg.view.type === 'dayGridMonth') {
                        var horaMes = document.createElement('span'); // Cambiado a span
                        horaMes.className = 'fc-reserva-hora';
                        // Cortamos el string para que solo muestre la hora de inicio (ej. "09:00") y ahorre espacio
                        horaMes.textContent = arg.timeText.split(' - ')[0] || arg.timeText; 
                        contenido.appendChild(horaMes);
                        
                        var tituloMes = document.createElement('span'); // Cambiado a span
                        tituloMes.className = 'fc-reserva-titulo';
                        tituloMes.textContent = arg.event.title || '';
                        contenido.appendChild(tituloMes);
                        
                        return { domNodes: [contenido] };
                    }

                    var hora = document.createElement('div');
                    hora.className = 'fc-reserva-hora';
                    hora.textContent = arg.timeText || '';
                    contenido.appendChild(hora);

                    // En la vista semanal basta la hora; el resto de datos queda en el tooltip al pasar el cursor.
                    if (arg.view.type === 'timeGridWeek') {
                        return { domNodes: [contenido] };
                    }

                    var detalles = [
                        { valor: arg.event.title, clase: 'fc-reserva-titulo' },
                        { valor: props.nombre_coordinador_externo, clase: 'fc-reserva-detalle' },
                        { valor: props.telefono_coordinador_externo || props.telefono_usuario, clase: 'fc-reserva-detalle' },
                        { valor: props.nombre_autoridad, clase: 'fc-reserva-detalle' },
                        { valor: props.responsable, clase: 'fc-reserva-detalle' },
                        { valor: props.tipo_evento, clase: 'fc-reserva-detalle' },
                        { valor: props.requerimientos_especiales, clase: 'fc-reserva-detalle' },
                        { valor: Array.isArray(props.requerimientos) ? props.requerimientos.join(', ') : props.requerimientos, clase: 'fc-reserva-detalle' },
                        { valor: props.aforo ? 'Aforo: ' + props.aforo : '', clase: 'fc-reserva-detalle' },
                        { valor: props.nombre_usuario, clase: 'fc-reserva-detalle' },
                        { valor: props.registrado_por, clase: 'fc-reserva-detalle' },
                        { valor: props.coordinador_interno, clase: 'fc-reserva-detalle' }
                    ];

                    detalles.forEach(function(detalle) {
                        if (detalle.valor === null || detalle.valor === undefined || String(detalle.valor).trim() === '') return;
                        var linea = document.createElement('div');
                        linea.className = detalle.clase;
                        linea.textContent = detalle.valor;
                        contenido.appendChild(linea);
                    });

                    return { domNodes: [contenido] };
                },

                // Al pasar el cursor por un evento, muestra sus datos completos en la agenda grande.
                eventDidMount: function(info) {
                    var props = info.event.extendedProps;
                    var detalles = [
                        info.event.title,
                        props.nombre_coordinador_externo || props.nombre_usuario,
                        props.telefono_coordinador_externo || props.telefono_usuario,
                        props.nombre_autoridad,
                        props.responsable,
                        props.tipo_evento,
                        props.salon,
                        props.nombre_usuario,
                        props.modificado_por
                    ].filter(function(valor) {
                        return valor !== null && valor !== undefined && String(valor).trim() !== '';
                    });

                    // El atributo title muestra cada detalle en una línea como en la referencia.
                    info.el.setAttribute('title', detalles.join('\n'));
                    info.el.setAttribute('aria-label', detalles.join(', '));
                },

                // Al hacer clic o seleccionar un bloque de tiempo
                select: function(info) {
                    // Admin y superadmin pueden crear reservas desde el calendario grande.
                    let userRole = "{{ auth()->user()->rol ?? 'usuario' }}";
                    if(userRole !== 'admin' && userRole !== 'superadmin') {
                        alert('Modo de solo lectura: No tienes permisos para crear reservas.');
                        return; // Corta la ejecución aquí, el modal no se abre
                    }

                    var modal = document.getElementById('booking-modal');
                    document.getElementById('modal-start').value = info.startStr.slice(0, 19).replace('T', ' ');
                    document.getElementById('modal-end').value = info.endStr.slice(0, 19).replace('T', ' ');
                    modal.style.display = 'flex';
                },

                // Al hacer clic en un evento YA CREADO
                eventClick: function(info) {
                    abrirEdicionReserva(info.event);
                },

                dayHeaderContent: function(arg) {
                    // En la vista Día mostramos la fecha completa en español, en lugar de la abreviatura LU 12.
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

                        return {
                            html: '<div style="' + estiloFechaDia + '">'
                                + fechaCompleta + '</div>'
                        };
                    }

                    var dayNames = ['DO', 'LU', 'MA', 'MI', 'JU', 'VI', 'SA'];
                    var dayStr = dayNames[arg.date.getDay()];
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
                    var titleEl = document.getElementById('calendar-title');
                    // Muestra el número de semana en la vista semanal; las otras muestran su título normal.
                    if (info.view.type === 'timeGridWeek') {
                        var weekNumber = getWeekNumber(info.view.currentStart);
                        titleEl.innerText = 'Semana ' + weekNumber;
                    } else {
                        titleEl.innerText = info.view.title;
                    }

                    // Sincroniza el contorno del mini calendario con la fecha enfocada en la vista grande.
                    if (typeof updateMiniCalendar === 'function') {
                        var focusedDate = info.view.calendar.getDate();
                        currentMiniYear = focusedDate.getFullYear();
                        currentMiniMonth = focusedDate.getMonth() + 1;
                        updateMiniCalendar(currentMiniYear, currentMiniMonth);
                    }
                }
            });
            
            calendar.render();
            // Actualiza el distintivo en todas las vistas mientras la página sigue abierta.
            window.setInterval(function() { calendar.rerenderEvents(); }, 60000);

            // Controladores del Modal
            document.getElementById('close-modal').addEventListener('click', () => { document.getElementById('booking-modal').style.display = 'none'; });
            document.getElementById('btn-cancel').addEventListener('click', () => { document.getElementById('booking-modal').style.display = 'none'; });


            // Cerrar el modal de edición
            document.getElementById('close-edit-modal').addEventListener('click', () => { document.getElementById('edit-booking-modal').style.display = 'none'; });
            document.getElementById('btn-edit-cancel').addEventListener('click', () => { document.getElementById('edit-booking-modal').style.display = 'none'; });

            // Arrastra el modal desde su encabezado y mantenlo dentro de la pantalla.
            var modalPanel = document.getElementById('edit-modal-panel');
            var modalHandle = document.getElementById('edit-modal-drag-handle');
            var movimientoModal = null;

            modalHandle.addEventListener('pointerdown', function(event) {
                if (event.target.closest('button')) return;

                var bounds = modalPanel.getBoundingClientRect();
                movimientoModal = {
                    x: event.clientX,
                    y: event.clientY,
                    left: bounds.left,
                    top: bounds.top
                };
                modalPanel.style.left = bounds.left + 'px';
                modalPanel.style.top = bounds.top + 'px';
                modalPanel.style.right = 'auto';
                modalHandle.setPointerCapture(event.pointerId);
            });

            modalHandle.addEventListener('pointermove', function(event) {
                if (!movimientoModal) return;

                var left = movimientoModal.left + event.clientX - movimientoModal.x;
                var top = movimientoModal.top + event.clientY - movimientoModal.y;
                var maxLeft = window.innerWidth - modalPanel.offsetWidth;
                var maxTop = window.innerHeight - modalPanel.offsetHeight;
                modalPanel.style.left = Math.max(0, Math.min(left, maxLeft)) + 'px';
                modalPanel.style.top = Math.max(0, Math.min(top, maxTop)) + 'px';
            });

            function terminarMovimientoModal() {
                movimientoModal = null;
            }
            modalHandle.addEventListener('pointerup', terminarMovimientoModal);
            modalHandle.addEventListener('pointercancel', terminarMovimientoModal);

            // Enviar formulario de ACTUALIZAR por AJAX
            document.getElementById('edit-booking-form').addEventListener('submit', function(e) {
                e.preventDefault();
                var eventId = document.getElementById('edit-event-id').value;
                var formData = new FormData(this);
                formData.append('_method', 'PUT'); // Laravel necesita esto para saber que es un UPDATE

                fetch('/api/salones/reservas/' + eventId, {
                    method: 'POST', // Mandamos POST pero con _method PUT adentro
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('edit-booking-modal').style.display = 'none';
                        calendar.refetchEvents(); // Recarga los cuadritos mágicamente xd
                        miniEventDatesCache = {};
                        updateMiniCalendar(currentMiniYear, currentMiniMonth);
                    }
                });
            });

            // Botón ELIMINAR por AJAX
            var deleteButton = document.getElementById('btn-edit-delete');
            if (deleteButton) deleteButton.addEventListener('click', function() {
                if(confirm('¿Estás seguro de que deseas eliminar esta reserva?')) {
                    var eventId = document.getElementById('edit-event-id').value;
                    
                    fetch('/api/salones/reservas/' + eventId, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            document.getElementById('edit-booking-modal').style.display = 'none';
                            calendar.refetchEvents();
                            miniEventDatesCache = {};
                            updateMiniCalendar(currentMiniYear, currentMiniMonth);
                        }
                    });
                }
            });

            // Enviar formulario por AJAX
            document.getElementById('booking-form').addEventListener('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                fetch('/api/salones/reservas', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success && data.redirect_url) {
                        // Redirigir automáticamente al segundo formulario grande de protocolo
                        window.location.href = data.redirect_url;
                    }
                });
            });

            var currentMiniYear = 2026;
            var currentMiniMonth = 10;

            function updateMiniCalendar(year, month) {
                var monthNames = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
                document.getElementById('mini-cal-month-label').innerText = monthNames[month - 1];
                document.getElementById('mini-cal-year-label').innerText = year;

                // El mini calendario solicita fechas solo del salón activo; sin filtro recibe todas.
                var salonQuery = selectedSalonFilter ? `&salon=${encodeURIComponent(selectedSalonFilter)}` : '';
                var cacheKey = `${year}-${month}-${selectedSalonFilter || 'all'}`;
                if (!miniEventDatesCache[cacheKey]) {
                    miniEventDatesCache[cacheKey] = fetch(`${urlFechasReservas}?year=${year}&month=${month}${salonQuery}`)
                        .then(response => {
                            if (!response.ok) throw new Error('No se pudieron cargar las fechas ocupadas.');
                            return response.json();
                        });
                }

                miniEventDatesCache[cacheKey]
                    .then(eventDates => {
                        var firstDay = new Date(year, month - 1, 1);
                        var lastDay = new Date(year, month, 0);
                        
                        var startingDay = firstDay.getDay();
                        var totalDays = lastDay.getDate();

                        var tbody = document.getElementById('mini-cal-body');
                        tbody.innerHTML = '';

                        var row = document.createElement('tr');
                        var realToday = new Date();
                        realToday.setHours(0,0,0,0);

                        // Obtener los límites exactos de la vista activa del calendario grande
                        var viewStart = calendar.view.currentStart;
                        var viewEnd = calendar.view.currentEnd;

                        // Días del mes anterior para rellenar
                        var prevMonthLastDay = new Date(year, month - 1, 0).getDate();
                        for (var i = startingDay - 1; i >= 0; i--) {
                            var td = document.createElement('td');
                            td.innerText = prevMonthLastDay - i;
                            td.style.color = '#d1d5db';
                            row.appendChild(td);
                        }

                        // Llenar días del mes actual
                        for (var day = 1; day <= totalDays; day++) {
                            if (row.children.length === 7) {
                                tbody.appendChild(row);
                                row = document.createElement('tr');
                            }

                            var td = document.createElement('td');
                            td.innerText = day;
                            td.className = 'mc-hover';

                            var monthFormatted = String(month).padStart(2, '0');
                            var dayFormatted = String(day).padStart(2, '0');
                            var fullDateStr = `${year}-${monthFormatted}-${dayFormatted}`;
                            
                            td.setAttribute('data-date', fullDateStr);

                            var cellDate = new Date(year, month - 1, day);
                            cellDate.setHours(0,0,0,0);

                            // Mantiene el marco en el día que está seleccionado en el calendario grande.
                            var calendarDate = new Date(calendar.getDate());
                            calendarDate.setHours(0,0,0,0);
                            if (cellDate.getTime() === calendarDate.getTime()) {
                                td.classList.add('mc-selected-date');
                            }

                            // Marcar el día de hoy real con borde azul (siempre visible)
                            var cellTime = cellDate.getTime();
                            var realTodayTime = realToday.getTime();
                            if (cellTime === realTodayTime) {
                                td.classList.add('mc-today-real');
                            }

                            // Si hay eventos en la BD, pintar de naranja
                            if (Array.isArray(eventDates) && eventDates.includes(fullDateStr)) {
                                td.classList.add('mc-has-event');
                                // En "Todos" usa naranja; con un salón seleccionado, su color de leyenda.
                                var markerColor = selectedSalonFilter ? salonColors[selectedSalonFilter] : '#f59e0b';
                                td.style.setProperty('background-color', markerColor || '#f59e0b', 'important');
                            }

                            // Comprobar si este día pertenece a la semana activa del calendario grande para marcar la fila en plomito de forma precisa
                            if (viewStart && viewEnd) {
                                var startTime = viewStart.getTime();
                                var endTime = viewEnd.getTime();
                                if (cellTime >= startTime && cellTime < endTime) {
                                    row.classList.add('mc-row-selected');
                                }
                            }

                            // Clic para cambiar de fecha en el calendario grande de inmediato
                            td.addEventListener('click', function() {
                                var selectedDate = this.getAttribute('data-date');
                                calendar.gotoDate(selectedDate);
                            });

                            row.appendChild(td);
                        }

                        // Rellenar días del siguiente mes
                        var nextMonthDay = 1;
                        while (row.children.length < 7) {
                            var td = document.createElement('td');
                            td.innerText = nextMonthDay++;
                            td.style.color = '#d1d5db';
                            row.appendChild(td);
                        }
                        tbody.appendChild(row);
                    }).catch(err => {
                        delete miniEventDatesCache[cacheKey];
                        console.error("Error cargando fechas de eventos:", err);
                    });
            }

            document.getElementById('mini-prev').addEventListener('click', function() {
                currentMiniMonth--;
                if (currentMiniMonth < 1) { currentMiniMonth = 12; currentMiniYear--; }
                updateMiniCalendar(currentMiniYear, currentMiniMonth);
            });

            document.getElementById('mini-next').addEventListener('click', function() {
                currentMiniMonth++;
                if (currentMiniMonth > 12) { currentMiniMonth = 1; currentMiniYear++; }
                updateMiniCalendar(currentMiniYear, currentMiniMonth);
            });

            updateMiniCalendar(currentMiniYear, currentMiniMonth);

            // Al elegir un salón actualiza el botón activo, los eventos y las fechas marcadas.
            document.querySelectorAll('.salon-filter').forEach(function(button) {
                button.addEventListener('click', function() {
                    selectedSalonFilter = this.dataset.salon === 'all' ? null : this.dataset.salon;

                    // Solo "Todos" separa en columnas los eventos de distintos salones.
                    calendar.setOption('slotEventOverlap', selectedSalonFilter !== null);

                    document.querySelectorAll('.salon-filter').forEach(function(filterButton) {
                        var isActive = filterButton === button;
                        filterButton.classList.toggle('active', isActive);
                        filterButton.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                    });

                    calendar.refetchEvents();
                    updateMiniCalendar(currentMiniYear, currentMiniMonth);
                });
            });

            // Menú de Vistas
            var btnDropdown = document.getElementById('btn-view-dropdown');
            var viewMenu = document.getElementById('view-menu');
            var currentViewText = document.getElementById('current-view-text');
            var viewOptions = document.querySelectorAll('.view-option');

            btnDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
                viewMenu.style.display = viewMenu.style.display === 'none' ? 'block' : 'none';
            });

            document.addEventListener('click', function() {
                viewMenu.style.display = 'none';
            });

            viewOptions.forEach(function(option) {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    var viewName = this.getAttribute('data-view');

                    // Cambia FullCalendar entre mes, semana y día y actualiza la etiqueta del selector.
                    calendar.changeView(viewName);
                    currentViewText.innerText = this.innerText;

                    viewOptions.forEach(function(opt) {
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

            document.getElementById('btn-prev').addEventListener('click', function() { calendar.prev(); });
            document.getElementById('btn-next').addEventListener('click', function() { calendar.next(); });
            document.getElementById('btn-hoy').addEventListener('click', function() { calendar.today(); });
        });
    </script>
</x-app-layout>
