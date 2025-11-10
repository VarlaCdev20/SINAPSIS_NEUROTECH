<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use App\Models\Pais;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use App\Models\Administrador;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Bitacora; // ✅ Importamos la Bitácora
use Carbon\Carbon; // ✅ Para la fecha y hora

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users.mostrar_usuarios');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::select('*')
            ->whereNot('name', 'Paciente')
            ->get();
        $pais = Pais::select('*')->get();
        $ciudad = Ciudad::select('*')->get();
        return view('users.registrar_usuarios', compact('pais', 'ciudad', 'roles'));
    }

    public function getCiudades($pais_id)
    {
        $ciudades = Ciudad::where('pais_id', $pais_id)->get();
        return response()->json($ciudades);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|max:50',
                'paterno' => 'required|max:50',
                'materno' => 'required|max:50',
                'celular' => 'required',
                'direccion' => 'required',
                'fecha_nacimiento' => 'required|date',
                'email' => 'required|email|unique:users,email',
                'carnet' => 'required|unique:users,carnet',
                'password' => 'required|min:6',
                'ocupacion' => 'required|max:100',
                'ciudad_id' => 'required',
                'rol' => 'required',
            ]);

            // Generar código de usuario
            $ultimoUsuario = User::orderBy('id', 'desc')->first();
            if ($ultimoUsuario && preg_match('/US-(\d+)/', $ultimoUsuario->cod_usu, $matches)) {
                $numero = intval($matches[1]) + 1;
            } else {
                $numero = 1;
            }
            $codigoNuevo = 'US-' . str_pad($numero, 3, '0', STR_PAD_LEFT);

            // Crear usuario
            $user = new User();
            $user->cod_usu = $codigoNuevo;
            $user->name = $request->name;
            $user->paterno = $request->paterno;
            $user->materno = $request->materno;
            $user->celular = $request->celular;
            $user->direccion = $request->direccion;
            $user->fecha_nacimiento = $request->fecha_nacimiento;
            $user->email = $request->email;
            $user->carnet = $request->carnet;
            $user->password = Hash::make($request->password);
            $user->ocupacion = $request->ocupacion;
            $user->ciudad_id = $request->ciudad_id;
            $user->save();

            // Asignar rol con Spatie
            $user->assignRole($request->rol);

            // Crear registro en tabla correspondiente
            switch ($request->rol) {
                case 'Administrador':
                    Administrador::create(['cod_usu' => $user->cod_usu]);
                    break;
                case 'Medico':
                    Medico::create(['cod_usu' => $user->cod_usu]);
                    break;
                case 'Paciente':
                    Paciente::create([
                        'cod_usu' => $user->cod_usu,
                        'cod_med' => auth()->user()->medico->cod_med ?? null
                    ]);
                    break;
            }

            // ✅ Registrar en Bitácora
            Bitacora::create([
                'cod_usu' => auth()->user()->cod_usu,
                'acc_bit' => "Registró un nuevo usuario: {$user->name} {$user->paterno} ({$request->rol})",
                'fec_hor_bit' => Carbon::now(),
            ]);

            if ($request->rol === 'Paciente') {
                return redirect()->route('mis_pacientes.listar')->with('success', 'Paciente registrado correctamente');
            } else {
                return redirect()->route('users.index')->with('success', 'Usuario registrado correctamente');
            }
        } catch (ValidationException $e) {
            return back()->with('error', 'Error de validación: ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al registrar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $pais = Pais::select('*')->get();
            $ciudad = Ciudad::select('*')->get();
            $id = decrypt($id);
            $user = User::find($id);
            return view('users.editar_usuarios', compact('user', 'pais', 'ciudad'));
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Acceso no autorizado');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'name' => 'required|max:50',
                'paterno' => 'required|max:50',
                'materno' => 'required|max:50',
                'celular' => 'required',
                'direccion' => 'required',
                'fecha_nacimiento' => 'required|date',
                'email' => 'required|email|unique:users,email,' . $id,
                'carnet' => 'required|unique:users,carnet,' . $id,
                'password' => 'required|min:6',
                'ocupacion' => 'required|max:100',
                'ciudad_id' => 'required',
            ]);

            $user = User::find($id);
            if (!$user) abort(404, 'Acceso no autorizado');

            $user->update([
                'name' => $request->name,
                'paterno' => $request->paterno,
                'materno' => $request->materno,
                'celular' => $request->celular,
                'direccion' => $request->direccion,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'email' => $request->email,
                'carnet' => $request->carnet,
                'password' => Hash::make($request->password),
                'ocupacion' => $request->ocupacion,
                'ciudad_id' => $request->ciudad_id,
            ]);

            // ✅ Registrar en Bitácora
            Bitacora::create([
                'cod_usu' => auth()->user()->cod_usu,
                'acc_bit' => "Actualizó los datos del usuario: {$user->name} {$user->paterno}",
                'fec_hor_bit' => Carbon::now(),
            ]);

            return redirect()->route('users.index')->with('update', 'Usuario actualizado correctamente');
        } catch (ValidationException $e) {
            return back()->with('error', 'Ocurrió un error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function refreshRoles()
    {
        try {
            $creados = ['Administrador' => 0, 'Medico' => 0, 'Paciente' => 0];

            foreach (User::all() as $user) {
                if (empty($user->cod_usu)) continue;

                $rol = $user->getRoleNames()->first();
                if (!$rol) continue;

                switch ($rol) {
                    case 'Administrador':
                        if (!Administrador::where('cod_usu', $user->cod_usu)->exists()) {
                            Administrador::create(['cod_usu' => $user->cod_usu]);
                            $creados['Administrador']++;
                        }
                        break;

                    case 'Medico':
                        if (!Medico::where('cod_usu', $user->cod_usu)->exists()) {
                            Medico::create(['cod_usu' => $user->cod_usu]);
                            $creados['Medico']++;
                        }
                        break;

                    case 'Paciente':
                        if (!Paciente::where('cod_usu', $user->cod_usu)->exists()) {
                            Paciente::create(['cod_usu' => $user->cod_usu]);
                            $creados['Paciente']++;
                        }
                        break;
                }
            }

            $total = array_sum($creados);

            if ($total === 0) {
                // ✅ Bitácora sin cambios
                Bitacora::create([
                    'cod_usu' => auth()->user()->cod_usu,
                    'acc_bit' => 'Verificó roles: todos los usuarios ya cuentan con su código.',
                    'fec_hor_bit' => Carbon::now(),
                ]);

                return back()->with('info', 'Todos los usuarios ya cuentan con su respectivo código.');
            }

            // ✅ Bitácora con cambios
            $detalles = [];
            foreach ($creados as $rol => $cantidad) {
                if ($cantidad > 0) $detalles[] = "$rol ($cantidad)";
            }

            Bitacora::create([
                'cod_usu' => auth()->user()->cod_usu,
                'acc_bit' => 'Sincronizó roles y códigos: ' . implode(', ', $detalles),
                'fec_hor_bit' => Carbon::now(),
            ]);

            $mensaje = "Se crearon {$total} registros nuevos correctamente: " . implode(', ', $detalles);
            return back()->with('success', $mensaje);
        } catch (\Throwable $e) {
            // ✅ Bitácora de error
            Bitacora::create([
                'cod_usu' => auth()->user()->cod_usu,
                'acc_bit' => 'Error al sincronizar roles: ' . $e->getMessage(),
                'fec_hor_bit' => Carbon::now(),
            ]);

            return back()->with('error', 'Error al sincronizar roles: ' . $e->getMessage());
        }
    }

}
