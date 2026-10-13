<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <script>document.documentElement.dataset.tema = localStorage.getItem('tema-sistema') === 'oscuro' ? 'oscuro' : 'claro';</script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>
            .theme-toggle { position: fixed; top: 16px; right: 16px; z-index: 20; display: inline-flex; align-items: center; gap: 8px; min-height: 36px; padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 999px; background: #fff; color: #334155; cursor: pointer; }
            .theme-toggle .tema-luna { width: 17px; height: 17px; font-size: 16px; line-height: 1; }
            html[data-tema="oscuro"] .theme-toggle { background: #1e293b; border-color: #475569; color: #e2e8f0; }
            @media (max-width: 480px) { .theme-toggle { padding: 8px; } .theme-toggle .tema-texto { display: none; } }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <button type="button" class="theme-toggle" aria-label="Cambiar a modo oscuro" title="Cambiar a modo oscuro">
            <span class="tema-texto">Oscuro</span>
        </button>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-white">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
        <script>
            var temaOscuro = document.documentElement.dataset.tema === 'oscuro';
            var controlTema = document.querySelector('.theme-toggle');
            controlTema.querySelector('.tema-texto').textContent = temaOscuro ? 'Claro' : 'Oscuro';
            controlTema.setAttribute('aria-label', temaOscuro ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
            controlTema.addEventListener('click', function() {
                temaOscuro = !temaOscuro;
                document.documentElement.dataset.tema = temaOscuro ? 'oscuro' : 'claro';
                localStorage.setItem('tema-sistema', temaOscuro ? 'oscuro' : 'claro');
                controlTema.querySelector('.tema-texto').textContent = temaOscuro ? 'Claro' : 'Oscuro';
                controlTema.setAttribute('aria-label', temaOscuro ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
            });
        </script>
    </body>
</html>
