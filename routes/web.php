<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BienController;
use App\Http\Controllers\DependenciaController;
use App\Http\Controllers\HistorialMovimientoController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\OrganismoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ResponsableController;
use App\Http\Controllers\UnidadAdministradoraController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

/*
|--------------------------------------------------------------------------
| Rutas públicas y redirección de inicio
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (Auth::check()) {
        // Si ya está autenticado, redirigir según rol
        $user = Auth::user();
        if ($user instanceof Usuario && $user->isAdmin()) {
            return redirect()->route('usuarios.index');
        }

        return redirect()->route('bienes.index');
    }

    // Si no está autenticado, mostrar login
    return redirect()->route('login');
})->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Rutas de salida de sesión (requieren autenticación)
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Rutas protegidas (requieren autenticación y rol válido)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'redirigir.rol'])->group(function () {
    Route::resource('bienes', BienController::class)->parameters(['bienes' => 'bien']);
    Route::get('bienes/{bien}/pdf', [BienController::class, 'exportPdf'])->name('bienes.pdf');
    Route::resource('dependencias', DependenciaController::class)->parameters(['dependencias' => 'dependencia']);
    Route::get('dependencias/{dependencia}/pdf', [DependenciaController::class, 'exportPdf'])->name('dependencias.pdf');
    Route::resource('historial-movimientos', HistorialMovimientoController::class);
    Route::resource('movimientos', MovimientoController::class);
    // routes/web.php
    Route::get('movimientos/{movimiento}/pdf', [MovimientoController::class, 'pdf'])->name('movimientos.pdf');

    // Restauración/visualización de eliminados se maneja desde MovimientoController (vista combinada)
    Route::post('movimientos/eliminados/{eliminado}/restore', [MovimientoController::class, 'restoreEliminado'])->name('movimientos.eliminados.restore');
    Route::resource('organismos', OrganismoController::class);
    Route::get('organismos/{organismo}/pdf', [OrganismoController::class, 'exportPdf'])->name('organismos.pdf');
    Route::resource('reportes', ReporteController::class);
    Route::resource('responsables', ResponsableController::class);
    Route::resource('unidades', UnidadAdministradoraController::class)->parameters(['unidades' => 'unidadAdministradora']);
    Route::get('unidades/{unidadAdministradora}/pdf', [UnidadAdministradoraController::class, 'exportPdf'])->name('unidades.pdf');
    Route::resource('usuarios', UsuarioController::class)->parameters(['usuarios' => 'usuario']);
    Route::get('usuarios/{usuario}/pdf', [UsuarioController::class, 'exportPdf'])->name('usuarios.pdf');
});
