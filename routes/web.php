<?php

// Rutas web del sistema. Cambio documentado: 13/10/2026.
// Mantienen la sesión activa y conectan cada URL con su controlador.
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\SalonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Cambio documentado: 13/10/2026. Endpoints del calendario protegidos por sesión.
    Route::get('/api/salones/reservas/{id}', [ReservaController::class, 'obtenerReservaApi']);
    Route::get('/reservas/crear', [ReservaController::class, 'mostrarFormularioCrear'])->name('reservas.create');
    Route::post('/reservas/crear', [ReservaController::class, 'guardarReservaCompleta'])->name('reservas.store_form');

    // Administración de cuentas.
    Route::get('/salones/usuarios', [UsuarioController::class, 'index'])->name('salones.usuarios');
    Route::post('/salones/nombres', [SalonController::class, 'guardarNombre'])->name('salones.nombres.store');
    Route::put('/salones/nombres/{salon}', [SalonController::class, 'actualizarNombre'])->name('salones.nombres.update');
    Route::patch('/salones/nombres/{salon}/estado', [SalonController::class, 'cambiarEstado'])->name('salones.nombres.estado');

    // Supervisión, historial y papelera de reservas.
    Route::get('/reservas/supervisar', [ReservaController::class, 'supervisarReservas'])->name('reservas.supervisar');
    Route::get('/salones/papelera', [ReservaController::class, 'papelera'])->name('salones.papelera');
    Route::delete('/salones/papelera/vaciar', [ReservaController::class, 'vaciarPapelera'])->name('salones.vaciar_papelera');
    Route::post('/salones/papelera/{id}/restaurar', [ReservaController::class, 'restaurarPapelera'])->name('salones.restaurar');

    Route::post('/salones/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::put('/salones/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/salones/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Pantalla del calendario y herramientas de supervisión.
    Route::get('/salones/usar', [SalonController::class, 'calendario'])->name('salones.usar');
    Route::get('/salones/supervisar', [ReservaController::class, 'supervisar'])->name('salones.supervisar');

    Route::get('/salones/reservas/{id}/protocolo', [ReservaController::class, 'mostrarProtocolo'])->name('salones.protocolo');
    Route::put('/salones/reservas/{id}/protocolo', [ReservaController::class, 'actualizarProtocolo'])->name('salones.protocolo.update');

    Route::get('/api/salones/reservas', [ReservaController::class, 'obtenerReservas'])->name('api.salones.reservas');
    Route::post('/api/salones/reservas', [ReservaController::class, 'guardar'])->name('api.salones.reservas.guardar');
    Route::put('/api/salones/reservas/{id}', [ReservaController::class, 'actualizar'])->name('api.salones.reservas.actualizar');
    Route::delete('/api/salones/reservas/{id}', [ReservaController::class, 'eliminar'])->name('api.salones.reservas.eliminar');

    Route::get('/api/reservas/fechas', [SalonController::class, 'fechasConReservas'])
        ->name('api.reservas.fechas');

    Route::view('/salones/asientos', 'salones.asientos')->name('salones.asientos');
});

require __DIR__.'/auth.php';
