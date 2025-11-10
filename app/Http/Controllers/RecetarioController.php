<?php

namespace App\Http\Controllers;

use App\Models\Recetario;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\Episodio;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecetarioController extends Controller
{
    public function index()
    {
        $recetarios = Recetario::with(['paciente.usuario', 'medico.usuario', 'episodio'])->get();
        return view('recetarios.index', compact('recetarios'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $pacientes = Paciente::all();
        $medicos   = Medico::all();
        $episodios = Episodio::all();
        return view('recetarios.create', compact('pacientes', 'medicos', 'episodios'));
    }

    // Guardar nueva receta
    public function store(Request $request)
    {
        $request->validate([
            'COD_PAC'  => 'required|exists:pacientes,cod_pac',
            'COD_MED'  => 'required|exists:medico,cod_med',
            'COD_EPI'  => 'required|exists:episodio,COD_EPI',
            'TIT_REC'  => 'required|string|max:255',
            'DES_REC'  => 'nullable|string',
            'DIA_REC'  => 'nullable|integer|min:1',
            'FEC_EMI_REC' => 'required|date',
        ]);

        $rec = Recetario::create($request->all());

        Bitacora::create([
            'cod_usu'    => Auth::user()->cod_usu,
            'acc_bit'    => "El médico {$request->COD_MED} emitió la receta {$rec->COD_REC} para el paciente {$request->COD_PAC} en el episodio {$request->COD_EPI}",
            'fec_hor_bit' => now(),
        ]);

        return redirect()->route('recetarios.index')->with('success', 'Receta creada y registrada en Bitácora ✅');
    }

    public function edit($id)
    {
        $recetario = Recetario::findOrFail($id);
        $pacientes = Paciente::all();
        $medicos   = Medico::all();
        $episodios = Episodio::all();
        return view('recetarios.edit', compact('recetario', 'pacientes', 'medicos', 'episodios'));
    }

    // Actualizar receta
    public function update(Request $request, $id)
    {
        $request->validate([
            'COD_PAC'  => 'required|exists:pacientes,cod_pac',
            'COD_MED'  => 'required|exists:medico,cod_med',
            'COD_EPI'  => 'required|exists:episodio,COD_EPI',
            'TIT_REC'  => 'required|string|max:255',
            'DES_REC'  => 'nullable|string',
            'DIA_REC'  => 'nullable|integer|min:1',
            'FEC_EMI_REC' => 'required|date',
        ]);

        $recetario = Recetario::findOrFail($id);
        $recetario->update($request->all());

        Bitacora::create([
            'cod_usu'    => Auth::user()->cod_usu,
            'acc_bit'    => "El médico {$request->COD_MED} actualizó la receta {$recetario->COD_REC} del paciente {$request->COD_PAC}",
            'fec_hor_bit' => now(),
        ]);

        return redirect()->route('recetarios.index')->with('success', 'Receta actualizada y registrada en Bitácora ✅');
    }
}
