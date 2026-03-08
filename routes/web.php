<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\BienController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DependenciaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HistorialMovimientoController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\OrganismoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ResponsableController;
use App\Http\Controllers\UnidadAdministradoraController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\ResponsableController as ApiResponsableController;
use App\Http\Controllers\Api\UsuarioImportController as ApiUsuarioImportController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (sin autenticación)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('welcome')->middleware('prevent-back');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware(['auth', 'prevent-back']);

/*
|--------------------------------------------------------------------------
| Rutas de autenticación (solo invitados)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/configurar-password', [AuthController::class, 'showSetPasswordForm'])
        ->name('auth.set_password.form');

    Route::post('/configurar-password', [AuthController::class, 'setPassword'])
        ->name('auth.set_password.store');
});

/*
|--------------------------------------------------------------------------
| Rutas protegidas (requieren autenticación)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'redirigir.rol', 'prevent-back'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ────────────────────────────────────────────────
    // PERFIL
    // ────────────────────────────────────────────────
    Route::get('/perfil', [ProfileController::class, 'show'])->name('perfil.show');
    Route::patch('/perfil', [ProfileController::class, 'updateProfile'])->name('perfil.update');
    Route::patch('/perfil/password', [ProfileController::class, 'updatePassword'])->name('perfil.password');

    // ────────────────────────────────────────────────
    // BÚSQUEDA GLOBAL
    // ────────────────────────────────────────────────
    Route::get('/buscar', [SearchController::class, 'global'])->name('buscar.global');

    // ────────────────────────────────────────────────
    // AUDITORÍA (solo admin)
    // ────────────────────────────────────────────────
    Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');

    // ────────────────────────────────────────────────
    // BIENES
    // ────────────────────────────────────────────────
    Route::prefix('bienes')->name('bienes.')->group(function () {
        // Rutas específicas (deben ir antes del resource para que tengan prioridad)
        Route::get('reporte',        [BienController::class, 'generarReporte'])     ->name('reporte');
        Route::get('galeria-completa', [BienController::class, 'galeriaCompleta'])->name('galeria');
        Route::get('{bien}/pdf',     [BienController::class, 'exportPdf'])         ->name('pdf');

        // Desincorporación (GET → formulario, POST → procesar y descargar acta)
        Route::get('{bien}/desincorporar',    [BienController::class, 'showDesincorporarForm'])->name('desincorporar.form');
        Route::post('{bien}/desincorporar',   [BienController::class, 'desincorporar'])       ->name('desincorporar');

        // Transferencia entre dependencias
        Route::get('{bien}/transferir',  [BienController::class, 'showTransferirForm'])->name('transferir.form');
        Route::patch('{bien}/transferir',[BienController::class, 'transferir'])        ->name('transferir');
    });

    // Resource completo para bienes (índex, create, store, show, edit, update, destroy)
    Route::resource('bienes', BienController::class)->parameters(['bienes' => 'bien']);

    // ────────────────────────────────────────────────
    // Otras entidades con sus rutas PDF
    // ────────────────────────────────────────────────
    Route::resource('dependencias', DependenciaController::class)
        ->parameters(['dependencias' => 'dependencia']);
    Route::get('dependencias/{dependencia}/pdf', [DependenciaController::class, 'exportPdf'])
        ->name('dependencias.pdf');

    Route::resource('organismos', OrganismoController::class);
    Route::get('organismos/{organismo}/pdf', [OrganismoController::class, 'exportPdf'])
        ->name('organismos.pdf');

    Route::resource('unidades', UnidadAdministradoraController::class)
        ->parameters(['unidades' => 'unidadAdministradora']);
    Route::get('unidades/{unidadAdministradora}/pdf', [UnidadAdministradoraController::class, 'exportPdf'])
        ->name('unidades.pdf');

    Route::resource('responsables', ResponsableController::class);
    Route::post('responsables/buscar', [ApiResponsableController::class, 'buscar'])
        ->name('responsables.buscar');

    Route::resource('usuarios', UsuarioController::class)->parameters(['usuarios' => 'usuario']);
    Route::get('usuarios/{usuario}/pdf', [UsuarioController::class, 'exportPdf'])
        ->name('usuarios.pdf');
    Route::post('usuarios/importar', [ApiUsuarioImportController::class, 'importarPorCedula'])
        ->name('usuarios.importar');

    // ────────────────────────────────────────────────
    // Movimientos e historial
    // ────────────────────────────────────────────────
    Route::resource('movimientos', MovimientoController::class);
    Route::get('movimientos/{movimiento}/pdf', [MovimientoController::class, 'pdf'])
        ->name('movimientos.pdf');

    Route::get('movimientos/eliminados', [MovimientoController::class, 'eliminados'])
        ->name('movimientos.eliminados');
    Route::post('movimientos/eliminados/{eliminado}/restore', [MovimientoController::class, 'restoreEliminado'])
        ->name('movimientos.eliminados.restore');

    Route::patch('movimientos/reintegrar/{bien}', [MovimientoController::class, 'reintegrar'])
        ->name('movimientos.reintegrar');

    Route::resource('historial-movimientos', HistorialMovimientoController::class);

    // ────────────────────────────────────────────────
    // Reportes y gráficas
    // ────────────────────────────────────────────────
    Route::resource('reportes', ReporteController::class);
    Route::get('reportes/pdf/{tipo}', [ReporteController::class, 'generarPdf'])->name('reportes.pdf');
    Route::get('graficas', [ReporteController::class, 'graficas'])->name('graficas');
    Route::get('graficas/pdf', [ReporteController::class, 'graficasPdf'])->name('graficas.pdf');
});
