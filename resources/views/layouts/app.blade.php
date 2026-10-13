<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <script>
            // Se conserva el tema elegido entre páginas; claro es el tema inicial.
            document.documentElement.dataset.tema = localStorage.getItem('tema-sistema') === 'oscuro' ? 'oscuro' : 'claro';
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>
            .theme-toggle { min-height: 36px; border: 1px solid #cbd5e1 !important; border-radius: 999px !important; background: #fff !important; color: #334155 !important; transition: background-color .18s ease, border-color .18s ease, color .18s ease; }
            .theme-toggle:hover { background: #eff6ff !important; border-color: #93c5fd !important; }
            .theme-toggle svg, .theme-toggle .tema-luna { flex-shrink: 0; font-size: 16px; line-height: 1; }
            .tema-luna { color: #475569; }
            html[data-tema="oscuro"] .theme-toggle { color: #e2e8f0 !important; border-color: #475569 !important; background: #1e293b !important; }
            html[data-tema="oscuro"] .theme-toggle:hover { background: #334155 !important; }
            @media (max-width: 768px) {
                .min-h-screen > div[style*="max-width"] { width: 100% !important; max-width: 100% !important; box-sizing: border-box; }
                main input, main select, main textarea { max-width: 100%; box-sizing: border-box; }
                main div:has(> table) { max-width: 100%; overflow-x: auto !important; }
                main div[style*="justify-content: space-between"] { flex-wrap: wrap; gap: 12px; }
                main table { min-width: 620px; }
                [id*="modal"] > div { width: calc(100vw - 24px) !important; max-width: 600px !important; max-height: calc(100vh - 24px); overflow-y: auto; box-sizing: border-box; }
            }
            @media (max-width: 480px) {
                .theme-toggle { padding-left: 9px !important; padding-right: 9px !important; }
                .theme-toggle .tema-texto { display: none; }
                main [id*="modal"] form > div[style*="display: flex"] { flex-wrap: wrap; gap: 8px !important; }
                main [id*="modal"] form label { width: 100% !important; }
                main [id*="modal"] form input,
                main [id*="modal"] form textarea,
                main [id*="modal"] form select { width: 100% !important; flex: 1 1 100%; }
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <script>
            document.querySelectorAll('.theme-toggle').forEach(function(button) {
                button.addEventListener('click', function() {
                    var nuevoTema = document.documentElement.dataset.tema === 'oscuro' ? 'claro' : 'oscuro';
                    document.documentElement.dataset.tema = nuevoTema;
                    localStorage.setItem('tema-sistema', nuevoTema);
                    document.querySelectorAll('.theme-toggle').forEach(function(toggle) {
                        toggle.setAttribute('aria-pressed', nuevoTema === 'oscuro' ? 'true' : 'false');
                        toggle.setAttribute('aria-label', nuevoTema === 'oscuro' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
                        toggle.title = nuevoTema === 'oscuro' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro';
                        toggle.querySelector('.tema-texto').textContent = nuevoTema === 'oscuro' ? 'Claro' : 'Oscuro';
                    });
                });
            });
            document.querySelectorAll('.theme-toggle').forEach(function(toggle) {
                var oscuro = document.documentElement.dataset.tema === 'oscuro';
                toggle.setAttribute('aria-pressed', oscuro ? 'true' : 'false');
                toggle.setAttribute('aria-label', oscuro ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
                toggle.title = oscuro ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro';
                toggle.querySelector('.tema-texto').textContent = oscuro ? 'Claro' : 'Oscuro';
            });
        </script>
    </body>
</html>
