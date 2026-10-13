<x-app-layout>
    <!-- Importamos FontAwesome para los íconos de SuperSaaS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="min-h-screen bg-white py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            
            <!-- Alerta de Sesión Iniciada -->
            <div class="w-full bg-[#2ecc71] text-white text-center py-2 rounded mb-8 font-bold text-sm shadow-sm">
                Sesión iniciada con éxito
            </div>

            <!-- Título Principal -->
            <h1 class="text-[28px] font-bold text-[#475b75] mb-10 tracking-tight">
                Resumen de Supercadi de Protocolo Camara de Dip
            </h1>

            <!-- TABLA 1: Recursos y Reservas -->
            <div class="max-w-[600px] mb-12">
                <div class="border border-[#e2e8f0] rounded-lg overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#f8fafc] text-[#475b75] text-sm border-b border-[#e2e8f0]">
                                <th class="py-2 px-4 font-bold w-1/2">Nombre</th>
                                <th class="py-2 px-4 font-bold w-1/2">Función</th>
                            </tr>
                        </thead>
                        <tbody class="text-[13px] text-gray-700">
                            <!-- Fila: Salones -->
                            <tr class="border-b border-[#e2e8f0] bg-white hover:bg-gray-50 transition">
                                <td class="py-3 px-4 flex items-center">
                                    <span class="w-3.5 h-3.5 rounded-full bg-[#2ecc71] mr-3"></span>
                                    salones
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-6">
                                        <a href="{{ route('salones.usar') }}" class="text-[#3b82f6] hover:underline flex items-center">
                                            <i class="fa-regular fa-calendar mr-2"></i> Usar
                                        </a>
                                        <a href="{{ route('salones.supervisar') }}" class="text-[#3b82f6] hover:underline flex items-center">
                                            <i class="fa-solid fa-sliders mr-2"></i> Supervisar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <!-- Fila: Reservas de salón -->
                            <tr class="border-b border-[#e2e8f0] bg-white hover:bg-gray-50 transition">
                                <td class="py-3 px-4 flex items-center">
                                    <span class="w-3.5 h-3.5 rounded-full bg-[#2ecc71] mr-3"></span>
                                    reservas de salon
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-6">
                                        <a href="{{ route('reservas.create') }}" class="text-[#3b82f6] hover:underline flex items-center">
                                            <i class="fa-regular fa-file-lines mr-2"></i> Usar
                                        </a>
                                        <a href="{{ route('reservas.supervisar') }}" class="text-[#3b82f6] hover:underline flex items-center">
                                            <i class="fa-solid fa-sliders mr-2"></i> Supervisar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TABLA 2: Funciones Administrativas -->
            <div class="max-w-[600px]">
                <div class="border border-[#e2e8f0] rounded-lg overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#f8fafc] text-[#475b75] text-sm border-b border-[#e2e8f0]">
                                <th class="py-2 px-4 font-bold w-[35%]">Función</th>
                                <th class="py-2 px-4 font-bold w-[65%]">Descripción</th>
                            </tr>
                        </thead>
                        <tbody class="text-[13px] text-[#475b75]">
                            <!-- Fila: Gestión de usuarios -->
                            <tr class="border-b border-[#e2e8f0] bg-white hover:bg-gray-50 transition">
                                <td class="py-3 px-4">
                                    <a href="{{ route('salones.usuarios') }}" class="text-[#3b82f6] hover:underline flex items-center">
                                        <i class="fa-solid fa-user-group w-5 text-center mr-1"></i> Gestión de usuarios
                                    </a>
                                </td>
                                <td class="py-3 px-4">Añadir o borrar usuarios y superusuarios</td>
                            </tr>
                            <!-- Fila: Importar -->
                            <tr class="border-b border-[#e2e8f0] bg-white hover:bg-gray-50 transition">
                                <td class="py-3 px-4">
                                    <a href="#" class="text-[#3b82f6] hover:underline flex items-center">
                                        <i class="fa-solid fa-download w-5 text-center mr-1"></i> Importar
                                    </a>
                                </td>
                                <td class="py-3 px-4">Importar datos de usuario de un archivo</td>
                            </tr>
                            <!-- Fila: Exportar -->
                            <tr class="border-b border-[#e2e8f0] bg-white hover:bg-gray-50 transition">
                                <td class="py-3 px-4">
                                    <a href="#" class="text-[#3b82f6] hover:underline flex items-center">
                                        <i class="fa-solid fa-upload w-5 text-center mr-1"></i> Exportar
                                    </a>
                                </td>
                                <td class="py-3 px-4">Exportar datos de usuario a un archivo</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
