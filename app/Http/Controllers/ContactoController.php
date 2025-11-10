<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitante;
use Carbon\Carbon;

class ContactoController extends Controller
{
    public function store(Request $request)
    {
        $hoy = Carbon::today();
        $fechaMin = $hoy->copy()->subYears(90);
        $fechaMax = $hoy->copy()->subYears(18);

        $request->validate([
            'nombre' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'ap_paterno' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'ap_materno' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'correo' => 'required|email|max:150|unique:solicitantes,email_sol',
            'celular' => 'required|digits_between:7,15',
            'fecha_nacimiento' => [
                'required',
                'date',
                'before_or_equal:' . $fechaMax->format('Y-m-d'),
                'after_or_equal:' . $fechaMin->format('Y-m-d'),
            ],
            'ci' => 'required|string|max:20|unique:solicitantes,ci_sol',
            'direccion' => 'required|string|max:150',
            'descripcion' => 'required|string',
        ]);

        $last = Solicitante::orderBy('cod_sol', 'desc')->first();
        $codigo = $last ? 'SOL' . str_pad((int) substr($last->cod_sol, 3) + 1, 2, '0', STR_PAD_LEFT) : 'SOL01';

        Solicitante::create([
            'cod_sol' => $codigo,
            'nom_sol' => $request->nombre,
            'ap_pat_sol' => $request->ap_paterno,
            'ap_mat_sol' => $request->ap_materno,
            'email_sol' => $request->correo,
            'cel_sol' => $request->celular,
            'fec_nac_sol' => $request->fecha_nacimiento,
            'ci_sol' => $request->ci,
            'dir_sol' => $request->direccion,
            'des_sol' => $request->descripcion,
            'est_sol' => 'pendiente',
        ]);

        return redirect()->back()->with('success', 'Tu solicitud de contacto ha sido registrada correctamente.');
    }
}
