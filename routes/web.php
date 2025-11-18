<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    PanelController,
    ContactoController,
    SolicitanteExportController,
    AgendaController,
    RecetarioController,
    EpisodioController
};
use App\Livewire\{
    MisPacientes,
    ListaPacientes,
    ListaRoles
};

/*
|--------------------------------------------------------------------------
| 🔓 RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/contacto/store', [ContactoController::class, 'store'])->name('contacto.store');


/*
|--------------------------------------------------------------------------
| 🔐 RUTAS CON AUTENTICACIÓN (MIDDLEWARE GENERAL)
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 🏠 DASHBOARD PRINCIPAL
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [PanelController::class, 'index'])->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | 👥 GESTIÓN DE USUARIOS (ADMINISTRADOR)
    |--------------------------------------------------------------------------
    */
    Route::resource('/users', UserController::class)->names('users');

    // Obtener ciudades dinámicamente
    Route::get('/ciudades/{pais_id}', [UserController::class, 'getCiudades'])
        ->name('ciudades.get');

    // Sincronizar roles y códigos
    Route::post('/users/refresh-roles', [UserController::class, 'refreshRoles'])
        ->name('users.refreshRoles');


    /*
    |--------------------------------------------------------------------------
    | 💜 PACIENTE: MÓDULOS
    |--------------------------------------------------------------------------
    */
    Route::prefix('paciente')->group(function () {
        // 🗓️ Agenda del paciente
        Route::get('/agenda', [AgendaController::class, 'index'])
            ->name('paciente.agenda');
    });


    /*
    |--------------------------------------------------------------------------
    | ⚙️ ADMINISTRADOR: MÓDULOS
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->group(function () {

        // 🗓️ Agenda del administrador
        Route::get('/agenda', [AgendaController::class, 'index'])
            ->name('admin.agenda');

        // 📋 Pacientes (vista listar)
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
| 🧠 MÉDICO: MÓDULOS  
|--------------------------------------------------------------------------  
*/
    Route::prefix('medico')->group(function () {

        // 🗓️ Agenda del médico
        Route::get('/agenda', [AgendaController::class, 'index'])
            ->name('medico.agenda');

        // 🧠 Solicitudes médicas
        Route::get('/solicitudes', function () {
            return view('medico.solicitudes.listar');
        })->name('medico.solicitudes.listar');

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

        // 🧠 Historial Médico (Livewire dentro de Blade)
        Route::get('/historial', function () {
            return view('medico.historial.mostrar');
        })->name('medico.historial');
    });
});
