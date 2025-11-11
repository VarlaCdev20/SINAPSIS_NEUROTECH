<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AgendaController extends Controller
{
    /**
     * Muestra la vista "Mi Agenda" del paciente autenticado.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Validar paciente asociado
        $paciente = $this->resolvePaciente($user);

        $hoy        = Carbon::now();
        $inicioMes  = $hoy->copy()->startOfMonth();
        $finMes     = $hoy->copy()->endOfMonth();

        // Base de citas
        $baseCitas = Cita::query();
        if ($paciente && ($paciente->COD_PAC ?? null)) {
            $baseCitas->where(function ($q) use ($paciente) {
                $q->where('cod_pac', $paciente->COD_PAC)
                    ->orWhere('COD_PAC', $paciente->COD_PAC);
            });
        } else {
            $baseCitas->whereRaw('1=0');
        }

        // Obtener citas
        $citas = (clone $baseCitas)
            ->orderBy('fec_cit', 'asc')
            ->take(300)
            ->get();

        // 📅 Próxima cita
        $proxCita = (clone $baseCitas)
            ->where('fec_cit', '>=', now())
            ->orderBy('fec_cit', 'asc')
            ->first();

        $tiempoRestante = null;

        if ($proxCita) {
            $proxFecha = Carbon::parse($proxCita->fec_cit);
            $diffHoras = now()->diffInHours($proxFecha, false);

            if ($diffHoras <= 0) {
                $tiempoRestante = 'menos de una hora ⏳';
            } else {
                $diffDias = intdiv($diffHoras, 24);
                $restoHoras = $diffHoras % 24;

                $partes = [];
                if ($diffDias > 0) {
                    $partes[] = $diffDias . ' día' . ($diffDias > 1 ? 's' : '');
                }
                if ($restoHoras > 0) {
                    $partes[] = $restoHoras . ' hora' . ($restoHoras > 1 ? 's' : '');
                }

                $tiempoRestante = 'faltan ' . implode(' y ', $partes) . ' ⏳';
            }
        }

        // 📊 Estadísticas
        $total       = (clone $baseCitas)->count();
        $programadas = (clone $baseCitas)->where('est_cit', 'programada')->count();
        $asistidas   = (clone $baseCitas)->where('est_cit', 'atendida')->count();
        $canceladas  = (clone $baseCitas)->where('est_cit', 'cancelada')->count();

        // 📈 Tendencia últimos 6 meses
        $meses   = [];
        $conteos = [];
        for ($i = 5; $i >= 0; $i--) {
            $inicio = Carbon::now()->copy()->subMonths($i)->startOfMonth();
            $fin    = $inicio->copy()->endOfMonth();
            $meses[] = Str::ucfirst($inicio->locale('es')->isoFormat('MMM'));
            $conteos[] = (clone $baseCitas)
                ->whereBetween('fec_cit', [$inicio, $fin])
                ->count();
        }

        // 📅 Eventos para calendario mensual
        $eventos = (clone $baseCitas)
            ->whereBetween('fec_cit', [$inicioMes, $finMes])
            ->orderBy('fec_cit', 'asc')
            ->get()
            ->map(function ($c) {
                $start  = Carbon::parse($c->fec_cit ?? $c->fec_reg_cit);
                $motivo = $c->mot_cit ?? 'Consulta';
                return [
                    'id'    => $c->cod_cit ?? $c->COD_CIT ?? null,
                    'title' => $motivo,
                    'start' => $start->toIso8601String(),
                    'extendedProps' => [
                        'estado' => Str::lower($c->est_cit ?? 'programada'),
                        'tipo'   => Str::lower($c->tip_cit ?? 'presencial'),
                        'motivo' => $motivo,
                    ],
                ];
            });

        return view('paciente.MiAgenda', compact(
            'paciente',
            'citas',
            'eventos',
            'total',
            'programadas',
            'asistidas',
            'canceladas',
            'meses',
            'conteos',
            'hoy',
            'inicioMes',
            'finMes',
            'proxCita',
            'tiempoRestante'
        ));
    }

    /**
     * Intenta encontrar el registro Paciente vinculado al usuario autenticado.
     */
    protected function resolvePaciente($user)
    {
        // 1️⃣ Relación directa usando cod_usu (tu sistema de códigos)
        try {
            $paciente = \App\Models\Paciente::where('cod_usu', $user->cod_usu)->first();
            if ($paciente) return $paciente;
        } catch (\Throwable $e) {
        }

        // 2️⃣ Por si acaso la relación está definida en el modelo
        try {
            if (method_exists($user, 'paciente')) {
                $rel = $user->paciente();
                if ($rel && $rel->exists()) return $rel->first();
            }
        } catch (\Throwable $e) {
        }

        // 3️⃣ Por relación inversa
        try {
            $byHas = \App\Models\Paciente::whereHas('usuario', function ($q) use ($user) {
                $q->where('cod_usu', $user->cod_usu);
            })->first();
            if ($byHas) return $byHas;
        } catch (\Throwable $e) {
        }

        // Fallback: sin paciente asociado
        return null;
    }
}
