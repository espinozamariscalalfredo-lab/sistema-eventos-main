# Guía futura para separar la aplicación

Esta guía prepara una separación posterior. **Actualmente la aplicación sigue funcionando como un proyecto Laravel único.** No hay que cambiar la forma de iniciarla ni mover archivos para seguir trabajando.

## Qué hace cada parte hoy

- `resources/views/`: presenta las páginas Blade, incluido el calendario.
- `routes/web.php`: conecta las direcciones del navegador con sus controladores.
- `app/Http/Controllers/`: valida solicitudes y aplica las reglas de la aplicación.
- `app/Models/` y `app/Database/`: leen y guardan información en PostgreSQL.
- `database/migrations/`: describe la estructura de las tablas.

El flujo actual es:

```text
Navegador → ruta Laravel → controlador → modelo → PostgreSQL
                 ↓
             vista Blade
```

## Organización objetivo, cuando se decida migrar

Mantener dos carpetas de proyecto separadas, sin duplicar las reglas ni las credenciales:

```text
proyecto/
├── backend/   Laravel: API, autenticación, permisos y PostgreSQL
└── frontend/  Interfaz: calendario y pantallas que consumen la API
```

En el backend quedarán las reglas de negocio y el acceso a datos. El frontend solo mostrará información y enviará solicitudes; no debe conectarse directamente a PostgreSQL ni guardar claves de servidor.

## Orden recomendado para la separación

1. **Anotar el contrato actual.** Registrar qué datos recibe y devuelve cada solicitud del calendario y qué permisos requiere.
2. **Preparar autenticación para una interfaz independiente.** Las rutas actuales `/api/...` viven en `routes/web.php` y usan la sesión de Laravel. Primero hay que decidir el mecanismo nuevo de sesión o token; no basta con copiar esas rutas a `routes/api.php`.
3. **Preparar acceso entre sitios.** Configurar HTTPS, CORS y cookies según los dominios finales. No abrir CORS a cualquier origen.
4. **Separar una pantalla a la vez.** Empezar por el calendario, conservar las mismas validaciones en Laravel y comparar las funciones antes de continuar.
5. **Mantener ambas interfaces durante la transición.** Retirar Blade solo después de comprobar que crear, consultar y editar reservas y permisos siguen funcionando.
6. **Publicar cuando se autorice.** Guardar secretos solo en el servidor backend y confirmar cómo alcanzarán ese servidor los navegadores.

## Decisión de red pendiente para el despliegue

Si el frontend queda en la nube y Laravel solo en una computadora o red local, los navegadores externos no podrán llegar al backend por sí solos. Para usarlo desde fuera se necesitará una conexión accesible y protegida, por ejemplo VPN o un servidor puente aprobado. Elegir esta opción antes de publicar; esta guía no crea esa conexión ni despliega nada.

## Regla para el equipo

Hasta iniciar esa migración, desarrollar y ejecutar el proyecto como está: Laravel sirve las vistas, atiende las rutas y se conecta a PostgreSQL. Hacer la separación en cambios pequeños y revisables, sin borrar tablas ni rutas que todavía usa la aplicación.
