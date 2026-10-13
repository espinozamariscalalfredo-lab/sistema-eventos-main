{{-- Cambio al español: 12/10/2026. La vista usa nombres de reservas y campos en español. --}}
@php
    // Si la variable $readonly viene en true, los campos estarán bloqueados
    $isReadonly = isset($readonly) && $readonly === true;
    $reqs = old('requerimientos', isset($reserva) ? ($reserva->requerimientos ?? []) : []);
    if(is_string($reqs)) { $reqs = json_decode($reqs, true) ?? []; }
@endphp

<div class="space-y-6">
    <!-- Nombre del evento -->
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del evento *</label>
        <input type="text" name="titulo" value="{{ old('titulo', $reserva->titulo ?? '') }}" class="w-full border border-gray-300 rounded-md p-2.5 text-sm {{ $isReadonly ? 'bg-gray-100' : 'focus:ring-blue-500 focus:border-blue-500' }}" {{ $isReadonly ? 'readonly' : 'required' }}>
    </div>

    <!-- Este selector comparte el catálogo del calendario; el modo lectura solo muestra el valor. -->
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">Salón *</label>
        @if($isReadonly)
            <input type="text" value="{{ $reserva->salon ?? '' }}" class="w-full border border-gray-300 rounded-md p-2.5 text-sm bg-gray-100" readonly>
        @else
            <select name="salon" required class="w-full border border-gray-300 rounded-md p-2.5 text-sm bg-white">
                @foreach(($salones ?? collect()) as $salon)
                    @if($salon->activo || old('salon', $reserva->salon ?? '') === $salon->nombre)
                    <option value="{{ $salon->nombre }}" {{ old('salon', $reserva->salon ?? '') === $salon->nombre ? 'selected' : '' }}>{{ $salon->nombre }}</option>
                    @endif
                @endforeach
            </select>
        @endif
    </div>

    <!-- Autoridad solicitante -->
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">Autoridad solicitante *</label>
        <input type="text" name="nombre_autoridad" value="{{ old('nombre_autoridad', $reserva->nombre_autoridad ?? '') }}" class="w-full border border-gray-300 rounded-md p-2.5 text-sm {{ $isReadonly ? 'bg-gray-100' : '' }}" {{ $isReadonly ? 'readonly' : '' }}>
        <p class="text-xs text-gray-400 mt-1">Agregar si es diputado (Dip) o senador (Sen)</p>
    </div>

    <!-- Tipo de evento -->
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">Tipo de evento *</label>
        @if($isReadonly)
            <input type="text" value="{{ $reserva->tipo_evento ?? '' }}" class="w-full border border-gray-300 rounded-md p-2.5 text-sm bg-gray-100" readonly>
        @else
            <select name="tipo_evento" class="w-full border border-gray-300 rounded-md p-2.5 text-sm bg-white">
                <option value="Reconocimientos" {{ (old('tipo_evento', $reserva->tipo_evento ?? '') == 'Reconocimientos') ? 'selected' : '' }}>Reconocimientos</option>
                <option value="Reunión" {{ (old('tipo_evento', $reserva->tipo_evento ?? '') == 'Reunión') ? 'selected' : '' }}>Reunión</option>
                <option value="Conferencia" {{ (old('tipo_evento', $reserva->tipo_evento ?? '') == 'Conferencia') ? 'selected' : '' }}>Conferencia</option>
                <option value="Taller / Seminario" {{ (old('tipo_evento', $reserva->tipo_evento ?? '') == 'Taller / Seminario') ? 'selected' : '' }}>Taller / Seminario</option>
            </select>
        @endif
    </div>

    <!-- Requerimientos (Checkboxes) -->
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">Requerimientos *</label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-700">
            <label class="flex items-center space-x-2"><input type="checkbox" name="requerimientos[]" value="Sonido" {{ in_array('Sonido', $reqs) ? 'checked' : '' }} {{ $isReadonly ? 'disabled' : '' }} class="rounded border-gray-300 text-blue-600"> <span>Sonido</span></label>
            <label class="flex items-center space-x-2"><input type="checkbox" name="requerimientos[]" value="Maestro de ceremonias" {{ in_array('Maestro de ceremonias', $reqs) ? 'checked' : '' }} {{ $isReadonly ? 'disabled' : '' }} class="rounded border-gray-300 text-blue-600"> <span>Maestro de ceremonias</span></label>
            <label class="flex items-center space-x-2"><input type="checkbox" name="requerimientos[]" value="Tiene refrigerio" {{ in_array('Tiene refrigerio', $reqs) ? 'checked' : '' }} {{ $isReadonly ? 'disabled' : '' }} class="rounded border-gray-300 text-blue-600"> <span>Tiene refrigerio</span></label>
            <label class="flex items-center space-x-2"><input type="checkbox" name="requerimientos[]" value="Prensa" {{ in_array('Prensa', $reqs) ? 'checked' : '' }} {{ $isReadonly ? 'disabled' : '' }} class="rounded border-gray-300 text-blue-600"> <span>Prensa</span></label>
            <label class="flex items-center space-x-2"><input type="checkbox" name="requerimientos[]" value="Testera" {{ in_array('Testera', $reqs) ? 'checked' : '' }} {{ $isReadonly ? 'disabled' : '' }} class="rounded border-gray-300 text-blue-600"> <span>Testera</span></label>
            <label class="flex items-center space-x-2"><input type="checkbox" name="requerimientos[]" value="Atril" {{ in_array('Atril', $reqs) ? 'checked' : '' }} {{ $isReadonly ? 'disabled' : '' }} class="rounded border-gray-300 text-blue-600"> <span>Atril</span></label>
            <label class="flex items-center space-x-2"><input type="checkbox" name="requerimientos[]" value="Protocolo sala" {{ in_array('Protocolo sala', $reqs) ? 'checked' : '' }} {{ $isReadonly ? 'disabled' : '' }} class="rounded border-gray-300 text-blue-600"> <span>Protocolo sala</span></label>
            <label class="flex items-center space-x-2"><input type="checkbox" name="requerimientos[]" value="Protocolo antesala" {{ in_array('Protocolo antesala', $reqs) ? 'checked' : '' }} {{ $isReadonly ? 'disabled' : '' }} class="rounded border-gray-300 text-blue-600"> <span>Protocolo antesala</span></label>
            <label class="flex items-center space-x-2"><input type="checkbox" name="requerimientos[]" value="Guía" {{ in_array('Guía', $reqs) ? 'checked' : '' }} {{ $isReadonly ? 'disabled' : '' }} class="rounded border-gray-300 text-blue-600"> <span>Guía</span></label>
        </div>
    </div>

    <!-- Aforo -->
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">Aforo *</label>
        <input type="number" name="aforo" value="{{ old('aforo', $reserva->aforo ?? '') }}" placeholder="Cantidad de personas" class="w-full border border-gray-300 rounded-md p-2.5 text-sm {{ $isReadonly ? 'bg-gray-100' : '' }}" {{ $isReadonly ? 'readonly' : 'required' }}>
    </div>

    <!-- Tipo de ingreso -->
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">Tipo de ingreso *</label>
        @if($isReadonly)
            <input type="text" value="{{ $reserva->tipo_ingreso ?? '' }}" class="w-full border border-gray-300 rounded-md p-2.5 text-sm bg-gray-100" readonly>
        @else
            <select name="tipo_ingreso" class="w-full border border-gray-300 rounded-md p-2.5 text-sm bg-white">
                <option value="Libre" {{ (old('tipo_ingreso', $reserva->tipo_ingreso ?? '') == 'Libre') ? 'selected' : '' }}>Libre</option>
                <option value="Con invitación" {{ (old('tipo_ingreso', $reserva->tipo_ingreso ?? '') == 'Con invitación') ? 'selected' : '' }}>Con invitación</option>
                <option value="Lista cerrada" {{ (old('tipo_ingreso', $reserva->tipo_ingreso ?? '') == 'Lista cerrada') ? 'selected' : '' }}>Lista cerrada</option>
            </select>
        @endif
        <p class="text-xs text-gray-400 mt-1">Como ingresarán al edificio</p>
    </div>

    <!-- Nombre del coordinador externo -->
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del coordinador externo *</label>
        <input type="text" name="nombre_coordinador_externo" value="{{ old('nombre_coordinador_externo', $reserva->nombre_coordinador_externo ?? '') }}" class="w-full border border-gray-300 rounded-md p-2.5 text-sm {{ $isReadonly ? 'bg-gray-100' : '' }}" {{ $isReadonly ? 'readonly' : '' }}>
    </div>

    <!-- Celular del coordinador externo -->
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">Celular del coordinador externo *</label>
        <input type="text" name="telefono_coordinador_externo" value="{{ old('telefono_coordinador_externo', $reserva->telefono_coordinador_externo ?? '') }}" class="w-full border border-gray-300 rounded-md p-2.5 text-sm {{ $isReadonly ? 'bg-gray-100' : '' }}" {{ $isReadonly ? 'readonly' : '' }}>
    </div>

    <!-- Requerimiento especial -->
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">Requerimiento especial</label>
        <input type="text" name="requerimientos_especiales" value="{{ old('requerimientos_especiales', $reserva->requerimientos_especiales ?? '') }}" class="w-full border border-gray-300 rounded-md p-2.5 text-sm {{ $isReadonly ? 'bg-gray-100' : '' }}" {{ $isReadonly ? 'readonly' : '' }}>
    </div>

    <!-- Nombre de quien registra -->
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre de quien registra *</label>
        <input type="text" name="registrado_por" value="{{ old('registrado_por', $reserva->registrado_por ?? (Auth::user()->nombre ?? '')) }}" class="w-full border border-gray-300 rounded-md p-2.5 text-sm bg-gray-50" {{ $isReadonly ? 'readonly' : '' }}>
    </div>

    <!-- Nombre coordinador interno -->
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre coordinador interno</label>
        <input type="text" name="coordinador_interno" value="{{ old('coordinador_interno', $reserva->coordinador_interno ?? '') }}" class="w-full border border-gray-300 rounded-md p-2.5 text-sm {{ $isReadonly ? 'bg-gray-100' : '' }}" {{ $isReadonly ? 'readonly' : '' }}>
    </div>
</div>
