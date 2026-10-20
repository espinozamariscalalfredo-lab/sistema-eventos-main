# Sistema de reservas de salones

Aplicación Laravel para consultar el calendario, registrar reservas y administrar salones y cuentas. PostgreSQL es la única base de datos de la aplicación.

> Guía revisada: 20/10/2026. La aplicación continúa ejecutándose como Laravel con vistas Blade.

Para estudiar el proyecto desde cero, empieza por [la guía de código para junior](docs/GUIA_DE_CODIGO_PARA_JUNIOR.md). Explica backend, frontend, estilos, librerías, Composer, npm y PostgreSQL con ejemplos de dónde seguir cada función.

## Cómo funciona una pantalla

1. Una ruta de `routes/web.php` recibe la dirección del navegador.
2. El controlador de `app/Http/Controllers/` valida la solicitud y aplica las reglas.
3. El modelo de `app/Models/` lee o guarda datos en PostgreSQL.
4. Laravel devuelve una vista Blade de `resources/views/` o una respuesta JSON.

El calendario usa FullCalendar desde `resources/views/salones/usar.blade.php`. Esa vista solicita eventos a `ReservaController`; los colores y nombres salen de `salones`.

### Ejemplo: abrir el calendario

1. `routes/web.php` conecta `/salones/usar` con `SalonController::calendario`.
2. `SalonController` consulta los salones y prepara sus colores.
3. `resources/views/salones/usar.blade.php` dibuja el calendario.
4. La vista pide los eventos a `/api/salones/reservas`.
5. `ReservaController::obtenerReservas` arma los datos JSON desde `reservas` y `salones`.

Si una pantalla falla, sigue ese orden y revisa primero el mensaje de error del navegador o de Laravel. Mantén las reglas de permisos y validación en el servidor.

## Módulos y conexiones

| Función | Ruta principal | Controlador | Vista o destino |
| --- | --- | --- | --- |
| Calendario | `/salones/usar` | `SalonController` | `salones/usar.blade.php` |
| Eventos del calendario | `/api/salones/reservas` | `ReservaController` | JSON para FullCalendar |
| Crear reserva | `/reservas/crear` | `ReservaController` | `salones/crear_reserva.blade.php` |
| Supervisar reservas | `/salones/supervisar` | `ReservaController` | `salones/supervisar.blade.php` |
| Historial paginado | `/reservas/supervisar` | `ReservaController` | `salones/supervisar_reservas.blade.php` |
| Papelera | `/salones/papelera` | `ReservaController` | `salones/papelera.blade.php` |
| Usuarios y catálogo | `/salones/usuarios` | `UsuarioController` | `salones/usuarios.blade.php` |
| Protocolo | `/salones/reservas/{id}/protocolo` | `ReservaController` | `salones/protocolo.blade.php` y `partials/form_protocolo.blade.php` |
| Perfil | `/profile` | `ProfileController` | `profile/` |
| Ingreso y recuperación | `/login`, `/forgot-password` | Controladores Laravel Auth | `auth/` |

Las rutas que necesitan sesión están dentro de `auth` en `routes/web.php`. Las rutas de ingreso están en `routes/auth.php`. Los permisos sensibles también se validan en el controlador, no solo ocultando botones.

## Carpetas

| Carpeta | Qué contiene |
| --- | --- |
| `app/Http/Controllers/` | Reglas de cada solicitud y conexión entre pantalla y datos. |
| `app/Models/` | Mapeo de Laravel a las tablas `reservas`, `salones` y `usuarios`. |
| `app/Database/` | Adaptadores que permiten a Laravel usar nombres de columnas en español. |
| `app/Providers/` | Configuración de servicios de Laravel al iniciar. |
| `routes/` | Direcciones y métodos HTTP del sitio. |
| `resources/views/` | Plantillas Blade, JavaScript y estilos de las pantallas. |
| `resources/js/`, `resources/css/` | Entrada de JavaScript y estilos procesados por Vite. |
| `database/migrations/` | Cambios del esquema y comentarios para PostgreSQL. |
| `database/factories/`, `database/seeders/` | Datos de prueba o carga inicial. |
| `config/` | Configuración que Laravel lee desde `.env`. |
| `public/` | Punto de entrada y archivos públicos compilados. |
| `tests/` | Pruebas automatizadas del proyecto. |

No borres `vendor/` ni `node_modules/` para limpiar el código: son dependencias instaladas. Sus listas de paquetes están en `composer.json` y `package.json`.

## Preparación para separar frontend y backend

Por ahora el proyecto sigue unido en Laravel: Blade muestra las pantallas y Laravel atiende las rutas, valida permisos y consulta PostgreSQL. Esta forma sigue siendo la que se ejecuta.

La separación es un paso futuro. La guía [docs/GUIA_SEPARACION_FRONT_BACK.md](docs/GUIA_SEPARACION_FRONT_BACK.md) describe el orden para hacerlo sin perder funciones. No muevas archivos ni publiques una copia del frontend hasta completar esos pasos.

Hoy las direcciones `/api/...` también están en `routes/web.php` y usan la sesión de Laravel. No las traslades directamente a `routes/api.php`: la autenticación, las cookies y la protección CSRF deben prepararse para el nuevo frontend primero.

## Cambios documentados

- **13/10/2026:** se añadió una guía para una futura separación frontend/backend. No se movió código ejecutable; las rutas, vistas y conexión actual siguen iguales.
- **13/10/2026:** se aclaró el recorrido del calendario para facilitar el mantenimiento por módulos.
- **20/10/2026:** se agregó una guía de estudio con el mapa actual de archivos, capas, dependencias, base de datos y reglas para fechar comentarios.

Al hacer un cambio futuro, agrega una línea aquí solo si afecta la estructura, la base de datos o el flujo de un módulo. En el código, coloca la fecha junto a la regla nueva o modificada, no en cada línea.

## Herramientas del proyecto

- PHP 8.2 o superior y Laravel 12 para el servidor y las reglas de negocio.
- PostgreSQL para los datos. La conexión `pgsql` es la única configurada.
- Vite 7 procesa los archivos de `resources/`.
- Tailwind CSS 3 y Alpine.js 3 apoyan los estilos y la interacción.
- FullCalendar 6.1.10 y Font Awesome 6.4 se cargan desde CDN en las vistas que los usan.
- PHPUnit 11 está declarado para pruebas; Laravel Pint 1.24 da formato a PHP.

Las versiones declaradas no garantizan que cada herramienta esté disponible en todos los equipos; Composer y npm instalan las dependencias indicadas.

## PostgreSQL

Configura la conexión en `.env`; no copies contraseñas al código ni a este README. Los valores de ejemplo están en `.env.example`.

- Base: `DB_DATABASE` (en la instalación actual, `sistema_certificados`).
- Esquema: `DB_SCHEMA=protocolos`.
- Motor: `DB_CONNECTION=pgsql`.

Tablas principales:

- `reservas`: datos de eventos. `eliminado_en` marca la baja lógica.
- `salones`: nombre, color, estado y posición del catálogo.
- `usuarios`: cuentas y rol de acceso.
- `eventos`: tabla histórica; la agenda actual consulta `reservas`.
- `historial_migraciones` y tablas de autenticación: soporte para Laravel.

La reserva guarda el nombre en `reservas.salon`, que debe coincidir con `salones.nombre`. Las reservas eliminadas se conservan en PostgreSQL y la papelera las consulta.

En el catálogo, admin y superadmin pueden ver la lista; solo superadmin puede cambiarla. Los colores son únicos y quedan fijos al crear el nombre. No se permite dar de baja un nombre que tenga reservas, incluso si están en la papelera.

Las clases `RepositorioMigracionesEspanol`, `RepositorioTokensEspanol` y `GestorRestablecimientoEspanol` adaptan partes internas de Laravel a los nombres españoles. `AppServiceProvider` registra esos adaptadores; no renombres sus métodos de contrato sin revisar Laravel.

## Preparar el proyecto

1. Copia `.env.example` como `.env` y coloca los datos de tu PostgreSQL.
2. Instala dependencias con `composer install` y `npm install`.
3. Crea la clave de Laravel con `php artisan key:generate`.
4. Aplica solo migraciones pendientes con `php artisan migrate`.
5. En terminales separadas ejecuta `php artisan serve` y `npm run dev`.

No uses `migrate:fresh` en una base con datos: ese comando elimina tablas. Las migraciones que documentan tablas usan comentarios breves en español y no borran los datos al revertirse.

## Reglas para mantener el código

- Al agregar una pantalla, define su ruta, controlador, modelo y vista; añade una fila a la tabla de módulos de este README.
- Mantén cada módulo en sus archivos actuales y separa responsabilidades: rutas conectan, controladores coordinan, modelos guardan datos y vistas presentan.
- Al cambiar PostgreSQL, crea una migración nueva con nombre y comentarios en español. No edites una migración ya aplicada para intentar actualizar la base existente.
- Valida permisos y datos en el servidor, aunque la vista oculte una acción.
- Escribe comentarios cortos solo donde expliquen una regla o una conexión que no sea obvia; agrega fecha a cambios de reglas o estructura.
- Conserva las tablas históricas y de soporte; primero confirma quién las usa antes de retirar código.
