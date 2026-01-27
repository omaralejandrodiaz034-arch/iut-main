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
use App\Http\Controllers\Api\ResponsableController as ApiResponsableController;
use App\Http\Controllers\Api\UsuarioImportController as ApiUsuarioImportController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (Visibles sin Login)
|--------------------------------------------------------------------------
*/

// La movemos aquí para que cargue siempre, pero con 'prevent-back' para el historial
Route::get('/', function () {
    return view('welcome');
})->name('welcome')->middleware('prevent-back');

Route::get('/dashboard', function () {
    if (Auth::check()) {
        return redirect()->route('welcome');
    }
    return redirect()->route('login');
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| Rutas para Invitados (Solo si NO estás logueado)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Solo con Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'redirigir.rol', 'prevent-back'])->group(function () {

    // Salida de sesión
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --- BIENES ---
    // 1. Rutas específicas (DEBEN ir primero para evitar conflictos)
    Route::get('/bienes/reporte', [BienController::class, 'generarReporte'])->name('bienes.reporte');
    Route::get('/bienes/galeria-completa', [BienController::class, 'galeriaCompleta'])->name('bienes.galeria');
    Route::get('bienes/{bien}/pdf', [BienController::class, 'exportPdf'])->name('bienes.pdf');

    // 2. Ruta de recurso (Genera las rutas automáticas como bienes.index, bienes.show, etc.)
    Route::resource('bienes', BienController::class)->parameters(['bienes' => 'bien']);
    // --- DEPENDENCIAS ---
    Route::resource('dependencias', DependenciaController::class)->parameters(['dependencias' => 'dependencia']);
    Route::get('dependencias/{dependencia}/pdf', [DependenciaController::class, 'exportPdf'])->name('dependencias.pdf');

    // --- MOVIMIENTOS ---
    Route::resource('historial-movimientos', HistorialMovimientoController::class);
    Route::resource('movimientos', MovimientoController::class);
    Route::get('movimientos/{movimiento}/pdf', [MovimientoController::class, 'pdf'])->name('movimientos.pdf');
    Route::get('movimientos/eliminados', [MovimientoController::class, 'eliminados'])->name('movimientos.eliminados');
    Route::post('movimientos/eliminados/{eliminado}/restore', [MovimientoController::class, 'restoreEliminado'])->name('movimientos.eliminados.restore');
    Route::patch('movimientos/reintegrar/{bien}', [MovimientoController::class, 'reintegrar'])->name('movimientos.reintegrar');

    // --- RESPONSABLES ---
    Route::resource('responsables', ResponsableController::class);
    Route::post('responsables/buscar', [ApiResponsableController::class, 'buscar'])->name('responsables.buscar');

    // --- ORGANISMOS ---
    Route::resource('organismos', OrganismoController::class);
    Route::get('organismos/{organismo}/pdf', [OrganismoController::class, 'exportPdf'])->name('organismos.pdf');

    // --- REPORTES Y GRÁFICAS ---
    Route::resource('reportes', ReporteController::class);
    Route::get('reportes/pdf/{tipo}', [ReporteController::class, 'generarPdf'])->name('reportes.pdf');
    Route::get('graficas', [ReporteController::class, 'graficas'])->name('graficas');

    // --- UNIDADES ADMINISTRADORAS ---
    Route::resource('unidades', UnidadAdministradoraController::class)->parameters(['unidades' => 'unidadAdministradora']);
    Route::get('unidades/{unidadAdministradora}/pdf', [UnidadAdministradoraController::class, 'exportPdf'])->name('unidades.pdf');

    // --- USUARIOS ---
    Route::resource('usuarios', UsuarioController::class)->parameters(['usuarios' => 'usuario']);
    Route::get('usuarios/{usuario}/pdf', [UsuarioController::class, 'exportPdf'])->name('usuarios.pdf');
    // Importar usuario desde API (por cédula)
    Route::post('usuarios/importar', [ApiUsuarioImportController::class, 'importarPorCedula'])->name('usuarios.importar');
});
