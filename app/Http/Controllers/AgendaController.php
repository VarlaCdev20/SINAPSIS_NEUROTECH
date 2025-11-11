<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Cita, Paciente, Medico, Administrador};
use Carbon\Carbon;
use Illuminate\Support\Str;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $hoy = Carbon::now();
        $inicioMes = $hoy->copy()->startOfMonth();
        $finMes = $hoy->copy()->endOfMonth();

        // ============================
        // 🔹 PACIENTE
        // ============================
        if ($user->hasRole('Paciente')) {
            $paciente = Paciente::where('cod_usu', $user->cod_usu)->orWhere('COD_USU', $user->cod_usu)->first();
            $baseCitas = $this->queryByCodigo('cod_pac', $paciente->cod_pac ?? $paciente->COD_PAC);
        }

        // ============================
        // 🔹 MÉDICO
        // ============================
        elseif ($user->hasRole('Medico')) {
            $medico = Medico::where('cod_usu', $user->cod_usu)->orWhere('COD_USU', $user->cod_usu)->first();
            $baseCitas = $this->queryByCodigo('cod_med', $medico->cod_med ?? $medico->COD_MED);
        }

        // ============================
        // 🔹 ADMINISTRADOR
        // ============================
        elseif ($user->hasRole('Administrador')) {
            $administrador = Administrador::where('cod_usu', $user->cod_usu)->orWhere('COD_USU', $user->cod_usu)->first();
            $baseCitas = Cita::query();
        }

        else {
            abort(403, 'Rol no autorizado.');
        }

        // ============================
        // 📊 Datos comunes
        // ============================
        $citas = (clone $baseCitas)->orderBy('fec_cit', 'asc')->take(300)->get();
        [$proxCita, $tiempoRestante] = $this->proxCitaYTiempo($baseCitas);
        [$total, $programadas, $asistidas, $canceladas] = $this->metricas($baseCitas);
        [$meses, $conteos] = $this->tendencia6Meses($baseCitas);
        $eventos = $this->eventosMes($baseCitas, $inicioMes, $finMes);

        // 📈 Solo médicos
        $pacientesAtendidosMes = null;
        if (isset($medico)) {
            $pacientesAtendidosMes = (clone $baseCitas)
                ->whereBetween('fec_cit', [$inicioMes, $finMes])
                ->whereIn('est_cit', ['atendida', 'ATENDIDA', 'cancelada', 'CANCELADA'])
                ->distinct()
                ->count('cod_pac');
        }

        // ============================
        // 🧩 Retornar vista correspondiente
        // ============================
        if (isset($paciente)) {
            return view('paciente.MiAgenda', compact(
                'paciente', 'citas', 'eventos',
                'total', 'programadas', 'asistidas', 'canceladas',
                'meses', 'conteos', 'hoy', 'inicioMes', 'finMes',
                'proxCita', 'tiempoRestante'
            ));
        }

        if (isset($medico)) {
            return view('medico.MiAgenda', compact(
                'medico', 'citas', 'eventos',
                'total', 'programadas', 'asistidas', 'canceladas',
                'meses', 'conteos', 'hoy', 'inicioMes', 'finMes',
                'proxCita', 'tiempoRestante', 'pacientesAtendidosMes'
            ));
        }

        if (isset($administrador)) {
            return view('admin.MiAgenda', compact(
                'administrador', 'citas', 'eventos',
                'total', 'programadas', 'asistidas', 'canceladas',
                'meses', 'conteos', 'hoy', 'inicioMes', 'finMes',
                'proxCita', 'tiempoRestante'
            ));
        }
    }

    // ============================
    // 🔹 Funciones auxiliares
    // ============================

    protected function queryByCodigo($campo, $codigo)
    {
        return $codigo
            ? Cita::where(fn($q) => $q->where($campo, $codigo)->orWhere(Str::upper($campo), $codigo))
            : Cita::whereRaw('1=0');
    }

    protected function metricas($query)
    {
        $total = (clone $query)->count();
        $programadas = (clone $query)->whereIn('est_cit', ['programada', 'PROGRAMADA'])->count();
        $asistidas = (clone $query)->whereIn('est_cit', ['atendida', 'ATENDIDA'])->count();
        $canceladas = (clone $query)->whereIn('est_cit', ['cancelada', 'CANCELADA'])->count();
        return [$total, $programadas, $asistidas, $canceladas];
    }

    protected function tendencia6Meses($query)
    {
        $meses = $conteos = [];
        for ($i = 5; $i >= 0; $i--) {
            $inicio = Carbon::now()->subMonths($i)->startOfMonth();
            $fin = $inicio->copy()->endOfMonth();
            $meses[] = Str::ucfirst($inicio->locale('es')->isoFormat('MMM'));
            $conteos[] = (clone $query)
                ->whereBetween('fec_cit', [$inicio, $fin])
                ->whereIn('est_cit', ['atendida', 'ATENDIDA', 'cancelada', 'CANCELADA'])
                ->count();
        }
        return [$meses, $conteos];
    }

    protected function proxCitaYTiempo($query)
    {
        $proxCita = (clone $query)->where('fec_cit', '>=', now())->orderBy('fec_cit')->first();
        $tiempoRestante = null;

        if ($proxCita) {
            $prox = Carbon::parse($proxCita->fec_cit);
            $horas = now()->diffInHours($prox, false);
            if ($horas <= 0) $tiempoRestante = 'menos de una hora ⏳';
            else {
                $dias = intdiv($horas, 24);
                $resto = $horas % 24;
                $tiempoRestante = 'faltan ' .
                    ($dias > 0 ? "$dias día" . ($dias > 1 ? 's' : '') . ' y ' : '') .
                    ($resto > 0 ? "$resto hora" . ($resto > 1 ? 's' : '') : '') . ' ⏳';
            }
        }

        return [$proxCita, $tiempoRestante];
    }

    protected function eventosMes($query, Carbon $inicioMes, Carbon $finMes)
    {
        return (clone $query)
            ->whereBetween('fec_cit', [$inicioMes, $finMes])
            ->orderBy('fec_cit')
            ->get()
            ->map(fn($c) => [
                'id' => $c->cod_cit ?? $c->COD_CIT,
                'title' => $c->mot_cit ?? 'Consulta',
                'start' => Carbon::parse($c->fec_cit)->toIso8601String(),
                'extendedProps' => [
                    'estado' => Str::lower($c->est_cit ?? 'programada'),
                    'tipo'   => Str::lower($c->tip_cit ?? 'presencial'),
                    'motivo' => $c->mot_cit ?? 'Consulta',
                ],
            ]);
    }
}
