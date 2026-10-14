<x-app-layout>
    <style>
        /* Contenedor principal de cada asiento */
        .asiento-container {
            width: 38px;
            height: 38px;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            position: relative;
            display: flex;
            flex-direction: column;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .dark .asiento-container {
            border-color: #3f3f46;
        }

        .asiento-container:hover {
            transform: scale(1.15);
            z-index: 10;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-color: #94a3b8;
        }

        /* Las dos mitades del asiento (Arriba y Abajo) */
        .mitad {
            flex: 1;
            width: 100%;
            background-color: #f8fafc;
            transition: background-color 0.3s ease;
        }

        .dark .mitad {
            background-color: #27272a;
        }

        /* Línea separadora al medio */
        .mitad-arriba {
            border-bottom: 1px solid #e2e8f0;
        }
        
        .dark .mitad-arriba {
            border-bottom-color: #3f3f46;
        }

        /* Clase mágica: Colorea de naranja solo la mitad ocupada */
        .mitad.ocupada {
            background-color: #f97316 !important; /* Naranja */
        }
        
        .dark .mitad.ocupada {
            background-color: #ea580c !important; /* Naranja oscuro */
        }

        /* El numerito en el centro */
        .numero-asiento {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            color: #1e293b;
            pointer-events: none; /* Para que no estorbe al hacer clic */
            /* Sombra blanca para que el número se lea bien aunque el fondo sea naranja */
            text-shadow: 0px 0px 4px rgba(255, 255, 255, 0.9), 0px 0px 2px rgba(255, 255, 255, 0.9);
        }

        .dark .numero-asiento {
            color: #f8fafc;
            text-shadow: 0px 0px 4px rgba(0, 0, 0, 0.9), 0px 0px 2px rgba(0, 0, 0, 0.9);
        }
    </style>

    <div class="py-8 px-4 sm:px-8 min-h-screen bg-[#FDFDFC] dark:bg-[#0a0a0a]">
        <div class="max-w-7xl mx-auto">
            
            <!-- Encabezado -->
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-[#475b75] dark:text-[#EDEDEC]">Asignación de Curules</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Hemiciclo - Cámara de Diputados</p>
                </div>
                <div class="flex gap-4">
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mr-4">
                        <span class="w-4 h-4 rounded-sm bg-gray-100 border border-gray-300 block"></span> Libre
                        <span class="w-4 h-4 rounded-sm bg-orange-500 block ml-2"></span> Ocupado
                    </div>
                    <button class="px-4 py-2 bg-blue-600 text-white font-bold rounded shadow hover:bg-blue-700 transition">
                        Guardar Configuración
                    </button>
                    <a href="{{ route('salones.supervisar') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-md text-sm hover:bg-gray-300 transition">
                        Volver
                    </a>
                </div>
            </div>

            <!-- Contenedor del Mapa Hemiciclo -->
            <div class="bg-white dark:bg-[#161615] rounded-xl shadow-lg border border-gray-200 dark:border-[#3E3E3A] p-8 overflow-x-auto min-h-[600px] flex flex-col items-center">
                
                <!-- Área Principal (Lados Izquierdo y Derecho) -->
                <div class="flex justify-between w-full max-w-5xl gap-12 mb-12">
                    
                    <!-- BLOQUE IZQUIERDO (76 asientos) -->
                    <div class="flex-1 bg-gray-50 dark:bg-[#1e1e1e] p-6 rounded-xl border border-gray-200 dark:border-[#3E3E3A] shadow-inner">
                        <h3 class="text-center font-bold text-gray-700 dark:text-gray-300 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Bloque Izquierdo (76 Curules)</h3>
                        
                        <div class="flex flex-wrap gap-2.5 justify-center">
                            @for ($i = 1; $i <= 76; $i++)
                                <div id="asiento-izq-{{ $i }}" class="asiento-container" onclick="abrirModalAsiento('Izq-{{ $i }}')" title="Curul Izquierdo {{ $i }}">
                                    <!-- Mitad de la Mujer (Arriba) -->
                                    <div id="asiento-izq-{{ $i }}-top" class="mitad mitad-arriba"></div>
                                    <!-- Mitad del Hombre (Abajo) -->
                                    <div id="asiento-izq-{{ $i }}-bottom" class="mitad"></div>
                                    <!-- Número central -->
                                    <div class="numero-asiento">{{ $i }}</div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- BLOQUE DERECHO (76 asientos) -->
                    <div class="flex-1 bg-gray-50 dark:bg-[#1e1e1e] p-6 rounded-xl border border-gray-200 dark:border-[#3E3E3A] shadow-inner">
                        <h3 class="text-center font-bold text-gray-700 dark:text-gray-300 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Bloque Derecho (76 Curules)</h3>
                        
                        <div class="flex flex-wrap gap-2.5 justify-center">
                            @for ($i = 1; $i <= 76; $i++)
                                <div id="asiento-der-{{ $i }}" class="asiento-container" onclick="abrirModalAsiento('Der-{{ $i }}')" title="Curul Derecho {{ $i }}">
                                    <!-- Mitad de la Mujer (Arriba) -->
                                    <div id="asiento-der-{{ $i }}-top" class="mitad mitad-arriba"></div>
                                    <!-- Mitad del Hombre (Abajo) -->
                                    <div id="asiento-der-{{ $i }}-bottom" class="mitad"></div>
                                    <!-- Número central -->
                                    <div class="numero-asiento">{{ $i }}</div>
                                </div>
                            @endfor
                        </div>
                    </div>

                </div>

                <!-- BLOQUE CENTRAL INFERIOR (Mesa Directiva / Frente) -->
                <div class="bg-gray-100 dark:bg-[#252525] p-6 rounded-xl border border-gray-300 dark:border-[#4E4E4A] w-full max-w-md shadow-md">
                    <h3 class="text-center font-bold text-gray-800 dark:text-gray-200 mb-6 uppercase tracking-wider text-sm">Mesa Central</h3>
                    
                    <div class="flex flex-col items-center gap-4">
                        <!-- Fila 1: 3 asientos -->
                        <div class="flex gap-4">
                            @for ($i = 1; $i <= 3; $i++)
                                <div id="asiento-cen1-{{ $i }}" class="asiento-container !w-12 !h-12" onclick="abrirModalAsiento('Cen1-{{ $i }}')" title="Centro Fila 1 - {{ $i }}">
                                    <div id="asiento-cen1-{{ $i }}-top" class="mitad mitad-arriba"></div>
                                    <div id="asiento-cen1-{{ $i }}-bottom" class="mitad"></div>
                                    <div class="numero-asiento">C1-{{ $i }}</div>
                                </div>
                            @endfor
                        </div>

                        <!-- Fila 2: 4 asientos -->
                        <div class="flex gap-4">
                            @for ($i = 1; $i <= 4; $i++)
                                <div id="asiento-cen2-{{ $i }}" class="asiento-container !w-12 !h-12" onclick="abrirModalAsiento('Cen2-{{ $i }}')" title="Centro Fila 2 - {{ $i }}">
                                    <div id="asiento-cen2-{{ $i }}-top" class="mitad mitad-arriba"></div>
                                    <div id="asiento-cen2-{{ $i }}-bottom" class="mitad"></div>
                                    <div class="numero-asiento">C2-{{ $i }}</div>
                                </div>
                            @endfor
                        </div>

                        <!-- Fila 3: 3 asientos -->
                        <div class="flex gap-4">
                            @for ($i = 1; $i <= 3; $i++)
                                <div id="asiento-cen3-{{ $i }}" class="asiento-container !w-12 !h-12" onclick="abrirModalAsiento('Cen3-{{ $i }}')" title="Centro Fila 3 - {{ $i }}">
                                    <div id="asiento-cen3-{{ $i }}-top" class="mitad mitad-arriba"></div>
                                    <div id="asiento-cen3-{{ $i }}-bottom" class="mitad"></div>
                                    <div class="numero-asiento">C3-{{ $i }}</div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL PARA ASIGNAR NOMBRES -->
    <div id="modal-asignar" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity">
        <div class="bg-white dark:bg-[#161615] w-96 rounded-xl shadow-2xl overflow-hidden border border-gray-200 dark:border-[#3E3E3A]">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-[#3E3E3A] flex justify-between items-center bg-gray-50 dark:bg-[#1e1e1e]">
                <h3 class="font-bold text-lg text-gray-800 dark:text-white" id="modal-titulo">Asignar Asiento</h3>
                <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-700 dark:hover:text-white text-2xl leading-none font-bold">&times;</button>
            </div>
            
            <div class="p-6">
                <!-- ID oculto del asiento que estamos editando -->
                <input type="hidden" id="asiento-activo-id">
                
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 flex justify-between">
                        <span>Ocupante Mujer <small class="font-normal text-gray-500">(Mitad Arriba)</small></span>
                    </label>
                    <input type="text" id="nombre-mujer" placeholder="Nombre completo..." class="w-full px-3 py-2 border border-gray-300 dark:border-[#3E3E3A] rounded-md dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 flex justify-between">
                        <span>Ocupante Hombre <small class="font-normal text-gray-500">(Mitad Abajo)</small></span>
                    </label>
                    <input type="text" id="nombre-hombre" placeholder="Nombre completo..." class="w-full px-3 py-2 border border-gray-300 dark:border-[#3E3E3A] rounded-md dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex gap-3 justify-end pt-2 border-t border-gray-100 dark:border-[#2a2a2a]">
                    <button onclick="liberarAsiento()" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/30 dark:hover:bg-red-900/50 rounded-md font-medium text-sm transition">
                        Liberar Todo
                    </button>
                    <button onclick="guardarAsignacion()" class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-md font-bold transition text-sm shadow-sm">
                        Guardar y Ocupar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPT PARA MANEJAR LA LÓGICA DE CLICS Y MITADES -->
    <script>
        let asignaciones = {};

        function abrirModalAsiento(idOriginal) {
            let idDiv = 'asiento-' + idOriginal.toLowerCase();
            
            document.getElementById('asiento-activo-id').value = idDiv;
            document.getElementById('modal-titulo').innerText = "Asiento " + idOriginal;
            
            // Cargar datos previos si existen
            if(asignaciones[idDiv]) {
                document.getElementById('nombre-mujer').value = asignaciones[idDiv].mujer || '';
                document.getElementById('nombre-hombre').value = asignaciones[idDiv].hombre || '';
            } else {
                document.getElementById('nombre-mujer').value = '';
                document.getElementById('nombre-hombre').value = '';
            }

            document.getElementById('modal-asignar').classList.remove('hidden');
            document.getElementById('modal-asignar').classList.add('flex');
        }

        function cerrarModal() {
            document.getElementById('modal-asignar').classList.add('hidden');
            document.getElementById('modal-asignar').classList.remove('flex');
        }

        function guardarAsignacion() {
            let idDiv = document.getElementById('asiento-activo-id').value;
            let mujer = document.getElementById('nombre-mujer').value.trim();
            let hombre = document.getElementById('nombre-hombre').value.trim();

            if(mujer === "" && hombre === "") {
                alert("Debes ingresar el nombre de la Mujer o del Hombre.");
                return;
            }

            // Guardamos en memoria
            asignaciones[idDiv] = { mujer: mujer, hombre: hombre };

            // PINTAR LAS MITADES INDEPENDIENTEMENTE
            let mitadTop = document.getElementById(idDiv + '-top');
            let mitadBottom = document.getElementById(idDiv + '-bottom');

            // Si hay nombre de mujer, pintamos la mitad de arriba
            if(mujer !== "") {
                mitadTop.classList.add('ocupada');
            } else {
                mitadTop.classList.remove('ocupada');
            }

            // Si hay nombre de hombre, pintamos la mitad de abajo
            if(hombre !== "") {
                mitadBottom.classList.add('ocupada');
            } else {
                mitadBottom.classList.remove('ocupada');
            }

            cerrarModal();
        }

        function liberarAsiento() {
            let idDiv = document.getElementById('asiento-activo-id').value;
            
            delete asignaciones[idDiv];

            // Limpiamos las dos mitades
            let mitadTop = document.getElementById(idDiv + '-top');
            let mitadBottom = document.getElementById(idDiv + '-bottom');
            
            if(mitadTop) mitadTop.classList.remove('ocupada');
            if(mitadBottom) mitadBottom.classList.remove('ocupada');

            cerrarModal();
        }
    </script>
</x-app-layout>