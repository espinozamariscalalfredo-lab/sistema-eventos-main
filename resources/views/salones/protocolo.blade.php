{{-- Cambio al español: 12/10/2026. La vista usa nombres de reservas y campos en español. --}}
<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="min-h-screen bg-white py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-white border border-gray-200 rounded-lg shadow-sm p-8">
            
            <h2 class="text-2xl font-bold text-'#475b75] text-center mb-8">Reservas de salón</h2>

            <form action="{{ route('salones.protocolo.update', $reserva->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Aquí incluimos nuestro formulario maestro -->
                @include('salones.partials.form_protocolo', ['reserva' => $reserva, 'readonly' => false])

                <!-- Botones de Acción Finales -->
                <div class="flex items-center justify-center space-x-4 pt-6 border-t border-gray-200">
                    <button type="submit" class="bg-'#2563eb] text-white px-6 py-2.5 rounded-md text-sm font-bold hover:bg-blue-700 transition">
                        Completado
                    </button>
                    <a href="{{ route('salones.usar') }}" class="text-blue-600 text-sm hover:underline">
                        Cancelar
                    </a>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>