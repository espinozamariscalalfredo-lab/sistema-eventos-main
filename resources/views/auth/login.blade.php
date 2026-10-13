<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="max-w-md mx-auto p-6 bg-white">
        
        <!-- Encabezado -->
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-extrabold text-[#0a1142] mb-2 font-sans tracking-tight">
                SUPERCADI
            </h2>
            <p class="text-lg text-[#0a1142] font-medium">
                Iniciar sesión como administrador
            </p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email Address (Nombre de cuenta) -->
            <div>
                <label for="email" class="block text-sm font-medium text-[#8492a6] mb-1">
                    Nombre de cuenta:
                </label>
                <input id="email" 
                       class="block w-full px-4 py-2 text-gray-700 bg-white border border-[#c0ccda] rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                       type="email" 
                       name="correo" 
                       value="{{ old('correo') }}" 
                       required 
                       autofocus 
                       autocomplete="username">
            <x-input-error :messages="$errors->get('correo')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-[#8492a6] mb-1">
                    Contraseña:
                </label>
                <input id="password" 
                       class="block w-full px-4 py-2 text-gray-700 bg-white border border-[#0a1142] rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                       type="password"
                       name="password"
                       required 
                       autocomplete="current-password">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Opciones de Sesión (Radio Buttons estéticos) -->
            <div class="space-y-3 pt-2">
                <!-- Opción 1 (Seleccionada) -->
                <div class="flex items-center">
                    <input id="remember_me" type="radio" name="session_preference" value="keep" class="w-4 h-4 text-indigo-600 bg-white border-gray-300 focus:ring-indigo-500" checked>
                    <label for="remember_me" class="ml-3 block text-sm font-medium text-[#8492a6]">
                        Mantener mi sesión iniciada
                    </label>
                </div>
                
                <!-- Opción 2 -->
                <div class="flex items-center">
                    <input id="remember_account" type="radio" name="session_preference" value="remember" class="w-4 h-4 text-indigo-600 bg-white border-gray-300 focus:ring-indigo-500">
                    <label for="remember_account" class="ml-3 block text-sm font-medium text-[#8492a6]">
                        Recordar el nombre de mi cuenta
                    </label>
                </div>

                <!-- Opción 3 -->
                <div class="flex items-center">
                    <input id="ask_always" type="radio" name="session_preference" value="ask" class="w-4 h-4 text-indigo-600 bg-white border-gray-300 focus:ring-indigo-500">
                    <label for="ask_always" class="ml-3 block text-sm font-medium text-[#8492a6]">
                        Preguntarme cada vez
                    </label>
                </div>
            </div>

            <!-- Botón de Envío -->
            <div class="pt-4">
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-sm text-base font-bold text-white bg-[#000639] hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Iniciar sesión
                </button>
            </div>

            <!-- Enlace de Registro -->
            <div class="text-center mt-6">
                <span class="text-sm text-gray-500">¿Todavía no tiene cuenta?</span>
                <a href="{{ route('register') }}" class="text-sm font-medium text-[#0a1142] hover:underline underline-offset-2 decoration-1">
                    Crear cuenta
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
