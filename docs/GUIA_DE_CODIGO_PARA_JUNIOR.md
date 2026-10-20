# Guía del código para empezar

**Actualizada: 20/10/2026.** Esta guía explica dónde buscar cada parte del sistema sin cambiar cómo se ejecuta.

## 1. Cómo está construido hoy

El proyecto todavía funciona como una sola aplicación Laravel. Las palabras “frontend” y “backend” describen responsabilidades; todavía no son dos proyectos separados.

```text
Navegador
  → ruta en routes/
  → controlador en app/Http/Controllers/
  → modelo en app/Models/
  → PostgreSQL
  → vista Blade en resources/views/
  → HTML/CSS/JavaScript en el navegador
```

- **Backend:** recibe solicitudes, verifica permisos y datos, aplica reglas y lee o guarda información.
- **Frontend:** presenta pantallas y controles; envía solicitudes al backend y muestra respuestas.
- **Blade:** plantilla de Laravel que produce HTML. Puede incluir estilos y JavaScript de una pantalla; por eso algunas vistas son largas.

Las reglas importantes y los permisos deben validarse en el backend. Ocultar un botón en la pantalla no reemplaza esa validación.

## 2. Mapa del proyecto

### Backend: solicitudes, reglas y datos

| Carpeta o archivo | Para qué sirve | Por dónde empezar |
| --- | --- | --- |
| `routes/web.php` | Direcciones del sitio y del calendario; muchas requieren sesión. | Busca la URL y el nombre del controlador. |
| `routes/auth.php` | Ingreso, salida, registro y recuperación de contraseña. | Sigue la ruta hasta el controlador dentro de `Auth/`. |
| `app/Http/Controllers/ReservaController.php` | Reservas, calendario, supervisión, papelera, protocolo y exportación. | Busca el nombre del método conectado en la ruta. |
| `app/Http/Controllers/SalonController.php` | Catálogo de salones, colores y datos para el calendario. | Revisa permisos y validaciones antes de guardar. |
| `app/Http/Controllers/UsuarioController.php` | Lista y operaciones de cuentas de usuario. | Revisa el rol permitido en cada método. |
| `app/Http/Controllers/ProfileController.php` | Perfil de la cuenta iniciada. | Atiende las rutas `/profile`. |
| `app/Http/Requests/` | Reglas de validación reutilizables para formularios. | Sigue el tipo de Request que recibe el controlador. |
| `app/Models/` | Relación entre clases PHP y tablas PostgreSQL. | `Reserva`, `Salon` y `Usuario` son los modelos principales. |
| `app/Database/` | Adaptadores para que Laravel use nombres de columnas en español. | Son piezas internas; cambia con cuidado y revisa `AppServiceProvider`. |
| `app/Providers/AppServiceProvider.php` | Registra servicios y adaptadores cuando Laravel inicia. | Aquí se conectan los adaptadores anteriores. |

### Frontend: pantallas y recursos

| Carpeta o archivo | Para qué sirve |
| --- | --- |
| `resources/views/salones/usar.blade.php` | Calendario día/semana/mes, vista Disponible, filtros y formularios. |
| `resources/views/salones/supervisar.blade.php` | Tabla de reservas, filtros, columnas y papelera. |
| `resources/views/salones/usuarios.blade.php` | Cuentas y catálogo de salones. |
| `resources/views/salones/papelera.blade.php` | Reservas eliminadas, restauración y vaciado. |
| `resources/views/salones/protocolo.blade.php` y `partials/` | Documento y formulario del protocolo. |
| `resources/views/auth/` | Pantallas de ingreso y recuperación de acceso. |
| `resources/views/layouts/` | Estructura compartida: navegación, sesión y diseño general. |
| `resources/views/components/` | Controles Blade reutilizables, como botones y campos. |
| `resources/js/app.js` | Entrada JavaScript procesada por Vite; inicia Alpine.js. |
| `resources/js/bootstrap.js` | Prepara Axios para solicitudes del navegador. |
| `resources/css/app.css` | Estilos globales y modo oscuro. |

Las vistas `usar.blade.php` y `supervisar.blade.php` contienen HTML Blade, estilos y JavaScript de su pantalla. Sus comentarios señalan secciones para encontrarlas sin mover código y arriesgar el funcionamiento.

En Blade, `{{ $valor }}` imprime texto escapado, `@if` decide si aparece un bloque, `@foreach` repite una lista y `@json($valor)` pasa datos del servidor al JavaScript.

## 3. Recorrido de una función: cargar el calendario

1. `routes/web.php` registra `/salones/usar` y `/api/salones/reservas`.
2. `SalonController::calendario()` prepara los salones y abre `salones/usar.blade.php`.
3. JavaScript de esa vista pide las reservas a la ruta `/api/salones/reservas`.
4. `ReservaController::obtenerReservas()` consulta el modelo `Reserva` y devuelve JSON.
5. FullCalendar presenta los eventos en el navegador.

Para modificar una función, sigue la ruta completa antes de editar: **URL → controlador → modelo/base de datos → vista/JavaScript**.

## 4. Base de datos

La aplicación usa PostgreSQL. La conexión se configura en `config/database.php` con valores locales de `.env`; el archivo `.env.example` contiene ejemplos. No copies claves o contraseñas a este documento ni al código.

| Tabla | Uso principal |
| --- | --- |
| `reservas` | Fechas, salón, descripción, responsables y datos del evento. |
| `salones` | Nombres, colores únicos, estado activo y orden de aparición. |
| `usuarios` | Cuentas, correo, contraseña cifrada y rol. |
| `historial_migraciones` | Registro de cambios de estructura aplicados por Laravel. |

`database/migrations/` describe cómo se crea o cambia la estructura. Una migración nueva es el historial de un cambio; no edites una migración que ya se ejecutó en una base compartida. Usa `database/seeders/` para cargas iniciales y `database/factories/` para datos de prueba.

## 5. Composer, npm y herramientas

Los manifiestos de dependencias no admiten comentarios JSON; aquí se explica su contenido.

### Composer: PHP y backend

- `composer.json` enumera Laravel 12, PHP requerido y paquetes PHP.
- `composer.lock` fija las versiones instaladas para que los equipos reproduzcan el entorno.
- `vendor/` contiene los paquetes instalados. No edites sus archivos como parte de la aplicación.
- PHPUnit ejecuta pruebas; Pint aplica formato PHP. Son herramientas declaradas en dependencias de desarrollo.

### npm: recursos del frontend

- `package.json` declara scripts y paquetes JavaScript/CSS.
- `package-lock.json` fija las versiones de npm.
- Vite compila `resources/css/app.css` y `resources/js/app.js`; `vite.config.js` define esas entradas.
- Tailwind CSS genera utilidades; `tailwind.config.js` indica qué vistas revisar.
- Alpine.js añade interacciones pequeñas y Axios facilita solicitudes HTTP.
- `node_modules/` contiene paquetes instalados. No edites sus archivos como código del sistema.

Algunas vistas cargan FullCalendar, Font Awesome y SweetAlert desde CDN. Esas librerías son interfaz; sus enlaces están en las propias vistas.

Comandos de desarrollo habituales, desde la carpeta donde está `artisan`:

```powershell
composer install
npm install
php artisan serve
npm run dev
```

El servidor PHP y Vite pueden ejecutarse en terminales distintas. Las migraciones solo deben ejecutarse contra la base correcta y después de revisar su efecto. No uses `migrate:fresh` en una base con datos.

## 6. Configuración y carpetas generadas

- `config/`: opciones que Laravel toma de `.env`.
- `bootstrap/`: arranque de Laravel y registro de proveedores.
- `public/index.php`: entrada web de la aplicación; `public/build/` guarda recursos compilados.
- `storage/`: registros, cachés y vistas Blade compiladas. No edites los archivos generados de `storage/framework/views/`.
- `vendor/` y `node_modules/`: dependencias instaladas; su origen es Composer y npm.
- `tests/`: pruebas automatizadas. `phpunit.xml` configura PHPUnit.
- `docs/`: guías de mantenimiento. `GUIA_SEPARACION_FRONT_BACK.md` explica una separación futura, no aplicada.

## 7. Cómo comentar cambios

Usa el tipo de comentario adecuado: `//` en PHP/JavaScript, `/* ... */` en CSS y `{{-- ... --}}` en Blade. Escribe una frase para explicar una regla no obvia o la conexión con otra parte.

Pon fecha cuando el comentario registra una modificación concreta, por ejemplo: `// 20/10/2026: evita duplicar filas al cambiar la fecha.` No agregues la misma fecha a código antiguo que no cambió ni describas cada línea.

## Cambios de esta guía

- **20/10/2026:** se añadió el mapa de backend/frontend, las vistas principales, PostgreSQL, dependencias y recorrido del calendario para facilitar el estudio inicial.
- **20/10/2026:** se documentó cómo marcar cambios futuros sin alterar la separación actual de Laravel ni las reglas ejecutables.
