<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Solicitante;
use App\Models\Recetario;
use App\Models\User;
use App\Models\Bitacora;
use App\Models\Episodio;
use App\Models\Historial;
use Carbon\Carbon;

class PanelController extends Controller
{
    public function index()
    {
        // ==================================
        //   MÉTRICAS GENERALES DEL SISTEMA
        // ==================================

        $totalCitas      = Cita::count();
        $pacientes       = Paciente::with('usuario', 'citas')->get();

        $citasOnline     = Cita::where('tip_cit', 'virtual')->count();
        $citasCanceladas = Cita::where('est_cit', 'cancelada')->count();

        $porcOnline      = $totalCitas > 0 ? round(($citasOnline / $totalCitas) * 100, 1) : 0;
        $porcCanceladas  = $totalCitas > 0 ? round(($citasCanceladas / $totalCitas) * 100, 1) : 0;
        $porcTotales     = 100;

        $hoy = Carbon::today();
        $inicioSemana          = $hoy->copy()->startOfWeek();
        $finSemana             = $hoy->copy()->endOfWeek();
        $inicioSemanaAnterior  = $inicioSemana->copy()->subWeek();
        $finSemanaAnterior     = $finSemana->copy()->subWeek();

        $citasEstaSemana           = Cita::whereBetween('fec_cit', [$inicioSemana, $finSemana])->count();
        $citasSemanaAnterior       = Cita::whereBetween('fec_cit', [$inicioSemanaAnterior, $finSemanaAnterior])->count();
        $citasOnlineEstaSemana     = Cita::where('tip_cit', 'virtual')->whereBetween('fec_cit', [$inicioSemana, $finSemana])->count();
        $citasOnlineSemanaAnterior = Cita::where('tip_cit', 'virtual')->whereBetween('fec_cit', [$inicioSemanaAnterior, $finSemanaAnterior])->count();
        $citasCanceladasEstaSemana = Cita::where('est_cit', 'cancelada')->whereBetween('fec_cit', [$inicioSemana, $finSemana])->count();
        $citasCanceladasSemanaAnterior = Cita::where('est_cit', 'cancelada')->whereBetween('fec_cit', [$inicioSemanaAnterior, $finSemanaAnterior])->count();

        // ==================================
        // ✴️ ESTADÍSTICAS PARA EL NUEVO DISEÑO
        // ==================================
        $stats = [
            'virtual'    => Cita::where('tip_cit', 'virtual')->count(),
            'presencial' => Cita::where('tip_cit', 'presencial')->count(),
            'canceladas' => $citasCanceladas,
            'total'      => $totalCitas,
        ];

        // ✅ Próximas citas por fecha de CITA
        $proximasCitas = Cita::where('fec_cit', '>=', Carbon::now())
            ->with(['paciente.usuario', 'solicitante'])
            ->orderBy('fec_cit', 'asc')
            ->take(20)
            ->get();

        // ✅ Pendientes reales: est_sol = pendiente
        $solicitantesPendientes = Solicitante::where('est_sol', 'pendiente')
            ->orderBy('cod_sol', 'desc')
            ->take(10)
            ->get();

        $solPendientes = Solicitante::where('est_sol', 'pendiente')->count();
        $solAprobados  = Solicitante::where('est_sol', 'aprobado')->count();
        $solRechazados = Solicitante::where('est_sol', 'rechazado')->count();

        // ==================================
        //      TOTALIZADORES INFERIORES
        // ==================================
        $totalPacientes     = Paciente::count();
        $totalUsuario       = User::count();
        $totalSolicitantes  = Solicitante::count();
        $totalSeguimientos  = Recetario::count();
        $totalReprogramados = Cita::where('est_cit', 'reprogramada')->count();
        $citasHoy           = Cita::whereDate('fec_cit', Carbon::today())->count();
        $citasManana        = Cita::whereDate('fec_cit', Carbon::tomorrow())->count();
        $citasAtrasadas     = Cita::where('fec_cit', '<', Carbon::now())
            ->where('est_cit', '!=', 'cancelada')
            ->count();

        $solicitantes = Solicitante::latest()->take(5)->get();

        // ==================================
        //        ESTADÍSTICAS DE USUARIOS
        // ==================================
        $usuariosActivos      = User::where('estado', true)->count();
        $usuariosDesactivados = User::where('estado', false)->count();

        $topMedicos = \App\Models\Medico::withCount('citas')
            ->orderByDesc('citas_count')
            ->take(5)
            ->get();

        $labelsMedicos = $topMedicos->pluck('usuario.name')->toArray();
        $valoresCitas  = $topMedicos->pluck('citas_count')->toArray();

        $usuario  = auth()->user();
        $esMedico = $usuario->hasRole('Medico');
        $esAdmin  = $usuario->hasRole('Admin');

        // ==================================
        //             BITÁCORA
        // ==================================
        $bitacora = Bitacora::with('usuario')
            ->orderBy('fec_hor_bit', 'desc')
            ->take(10)
            ->get()
            ->map(function ($item) {
                $item->usuario_nombre = $item->usuario
                    ? $item->usuario->name
                    : 'Sistema';
                return $item;
            });

        // ==================================
        // 👤 NUEVO BLOQUE: PANEL DEL PACIENTE
        // ==================================
        $hoy = Carbon::now();
        $inicioMes = $hoy->copy()->startOfMonth();
        $finMes    = $hoy->copy()->endOfMonth();

        // 📅 Datos personales del paciente
        $paciente = Paciente::first(); // se reemplazará por el asociado al usuario logueado en la vista
        $codPac   = $paciente->COD_PAC ?? null;

        // 📅 Citas del paciente (pasadas y futuras)
        $proximasCitasPaciente = Cita::when($codPac, fn($q) => $q->where('cod_pac', $codPac))
            ->where('fec_cit', '>=', $hoy)
            ->orderBy('fec_cit', 'asc')
            ->take(10)
            ->get();

        $citasPasadasPaciente = Cita::when($codPac, fn($q) => $q->where('cod_pac', $codPac))
            ->where('fec_cit', '<', $hoy)
            ->orderBy('fec_cit', 'desc')
            ->take(10)
            ->get();

        // 📊 Estadísticas de citas del paciente
        $todasCitas = Cita::when($codPac, fn($q) => $q->where('cod_pac', $codPac))->get();
        $statsPaciente = [
            'total'       => $todasCitas->count(),
            'programadas' => $todasCitas->where('est_cit', 'programada')->count(),
            'asistidas'   => $todasCitas->where('est_cit', 'atendida')->count(),
            'canceladas'  => $todasCitas->where('est_cit', 'cancelada')->count(),
        ];

        // 🧾 Último episodio y último historial
        $ultimoHistorial = Episodio::when($codPac, fn($q) => $q->where('COD_PAC', $codPac))
            ->orderBy('FEC_INI_EPI', 'desc')
            ->first();

        $historialGeneral = Historial::when($codPac, fn($q) => $q->where('COD_PAC', $codPac))
            ->orderBy('FEC_CRE_HIS', 'desc')
            ->first();

        // 📆 Citas del mes agrupadas por fecha
        $citasDelMes = Cita::when($codPac, fn($q) => $q->where('cod_pac', $codPac))
            ->whereBetween('fec_cit', [$inicioMes, $finMes])
            ->orderBy('fec_cit', 'asc')
            ->get()
            ->groupBy(fn($c) => Carbon::parse($c->fec_cit)->toDateString());

        // ==================================
        //              RETORNO
        // ==================================
        return view('dashboard', compact(
            /* EXISTENTE */
            'totalCitas',
            'citasOnline',
            'citasCanceladas',
            'porcOnline',
            'porcCanceladas',
            'porcTotales',
            'pacientes',
            'proximasCitas',
            'solicitantes',
            'citasEstaSemana',
            'citasSemanaAnterior',
            'citasOnlineEstaSemana',
            'citasOnlineSemanaAnterior',
            'citasCanceladasEstaSemana',
            'citasCanceladasSemanaAnterior',
            'totalPacientes',
            'totalUsuario',
            'totalSeguimientos',
            'totalReprogramados',
            'totalSolicitantes',
            'solicitantesPendientes',
            'usuariosActivos',
            'usuariosDesactivados',
            'labelsMedicos',
            'valoresCitas',
            'bitacora',
            'stats',
            'citasHoy',
            'citasManana',
            'citasAtrasadas',
            'solPendientes',
            'solAprobados',
            'esMedico',
            'esAdmin',
            'solPendientes',
            'solAprobados',
            'solRechazados',
            'paciente',
            'proximasCitasPaciente',
            'citasPasadasPaciente',
            'statsPaciente',
            'ultimoHistorial',
            'historialGeneral',
            'citasDelMes',
            'inicioMes',
            'finMes',
            'hoy'
        ));
    }
}
