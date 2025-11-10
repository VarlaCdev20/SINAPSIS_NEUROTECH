<?php

namespace App\Http\Controllers;

use App\Models\Episodio;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EpisodioController extends Controller
{
    public function index()
    {
        $episodios = Episodio::with(['paciente.usuario', 'medico.usuario'])->get();
        return view('episodios.index', compact('episodios'));
    }

    public function create()
    {
        $pacientes = Paciente::all();
        $medicos   = Medico::all();
        return view('episodios.create', compact('pacientes', 'medicos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'COD_PAC'     => 'required|exists:pacientes,cod_pac',
            'COD_MED'     => 'required|exists:medico,cod_med',
            'FEC_INI_EPI' => 'required|date',
        ]);

        $episodio = Episodio::create($request->all());

        Bitacora::create([
            'cod_usu'    => Auth::user()->cod_usu,
            'acc_bit'    => "El médico {$request->COD_MED} registró el episodio {$episodio->COD_EPI} para el paciente {$request->COD_PAC}",
            'fec_hor_bit'=> now(),
        ]);

        return redirect()->route('episodios.index')->with('success', 'Episodio registrado y guardado en bitácora ✅');
    }

    public function edit($id)
    {
        $episodio = Episodio::findOrFail($id);
        $pacientes = Paciente::all();
        $medicos   = Medico::all();

        return view('episodios.edit', compact('episodio', 'pacientes', 'medicos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'COD_PAC'     => 'required|exists:pacientes,cod_pac',
            'COD_MED'     => 'required|exists:medico,cod_med',
            'FEC_INI_EPI' => 'required|date',
        ]);

        $episodio = Episodio::findOrFail($id);
        $episodio->update($request->all());

        Bitacora::create([
            'cod_usu'    => Auth::user()->cod_usu,
            'acc_bit'    => "El médico {$request->COD_MED} actualizó el episodio {$episodio->COD_EPI} del paciente {$request->COD_PAC}",
            'fec_hor_bit'=> now(),
        ]);

        return redirect()->route('episodios.index')->with('success', 'Episodio actualizado y registrado en bitácora ✅');
    }
}
