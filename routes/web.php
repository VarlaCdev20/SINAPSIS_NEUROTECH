<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    PanelController,
    ContactoController,
    SolicitanteExportController,
    RecetarioController,
    EpisodioController
};
use App\Livewire\{MisPacientes, ListaPacientes, ListaRoles};

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/contacto/store', [ContactoController::class, 'store'])->name('contacto.store');

/*
|--------------------------------------------------------------------------
| RUTAS CON AUTENTICACIÓN
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD PRINCIPAL
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [PanelController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | GESTIÓN DE USUARIOS (ADMIN)
    |--------------------------------------------------------------------------
    */
    Route::resource('/users', UserController::class)->names('users');
    Route::get('/ciudades/{pais_id}', [UserController::class, 'getCiudades'])->name('ciudades.get');
    Route::post('/users/refresh-roles', [UserController::class, 'refreshRoles'])->name('users.refreshRoles');

    /*
    |--------------------------------------------------------------------------
    | ADMINISTRADOR: MÓDULOS
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->middleware(['auth'])->group(function () {

        // 📋 Pacientes (versión admin)
        Route::get('/pacientes', function () {
            return view('admin.Pacientes.listar');
        })->name('pacientes.index');

        // 🧩 Roles (Livewire)
        Route::get('/roles', function () {
            return view('admin.roles.listar');
        })->name('roles.index');
    });

    /*
    |--------------------------------------------------------------------------
    | MÉDICO: MÓDULOS
    |--------------------------------------------------------------------------
    */
    Route::prefix('medico')->middleware(['auth'])->group(function () {

        // 🧠 Solicitudes médicas
        Route::get('/solicitudes', function () {
            return view('medico.solicitudes.listar');
        })->name('medico.solicitudes.listar')->middleware('can:Solicitudes');

        // 📤 Exportación de solicitantes
        Route::get('/solicitantes/exportar/csv', [SolicitanteExportController::class, 'exportCsv'])
            ->name('solicitantes.exportar.csv');
        Route::get('/solicitantes/exportar/pdf', [SolicitanteExportController::class, 'exportPdf'])
            ->name('solicitantes.exportar.pdf');

        // 👩‍⚕️ Pacientes del médico
        Route::get('/mis-pacientes', function () {
            return view('medico.mis_pacientes.listar');
        })->name('mis_pacientes.listar');

        // ➕ Registrar nuevo paciente
        Route::get('/mis-pacientes/registrar', function () {
            $pais = \App\Models\Pais::all();
            $ciudad = \App\Models\Ciudad::all();
            return view('medico.mis_pacientes.registrar_pacientes', compact('pais', 'ciudad'));
        })->name('mis_pacientes.registrar');

        // ✏️ Editar paciente (encriptado)
        Route::get('/mis-pacientes/{cod_usu}/editar', [UserController::class, 'editPaciente'])
            ->name('pacientes.editar');

        // 🔄 Actualizar paciente
        Route::put('/mis-pacientes/{cod_usu}/actualizar', [UserController::class, 'updatePaciente'])
            ->name('pacientes.actualizar');
    });
});
