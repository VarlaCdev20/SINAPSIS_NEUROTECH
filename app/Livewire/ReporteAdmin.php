<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Bitacora;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BitacoraExport; // lo crearemos luego

class ReporteAdmin extends Component
{
    public $fechaInicio;
    public $fechaFin;
    public $rolUsuario = 'todos';

    public $bitacoras;
    public $labels = [];
    public $data = [];

    public function mount()
    {
        // Por defecto: últimos 7 días
        $this->fechaInicio = now()->subDays(7)->format('Y-m-d');
        $this->fechaFin = now()->format('Y-m-d');
        $this->filtrar();
    }

    /** 🔍 Filtro principal */
    public function filtrar()
    {
        $query = Bitacora::with('usuario.roles')
            ->whereBetween('fec_hor_bit', [
                Carbon::parse($this->fechaInicio)->startOfDay(),
                Carbon::parse($this->fechaFin)->endOfDay()
            ])
            ->orderBy('fec_hor_bit', 'desc');

        if ($this->rolUsuario !== 'todos') {
            $query->whereHas('usuario.roles', function ($q) {
                $q->where('name', $this->rolUsuario);
            });
        }

        $this->bitacoras = $query->get();

        $this->actualizarGrafico();
    }

    /** 📊 Actualiza los datos del gráfico */
    protected function actualizarGrafico()
    {
        $roles = ['Administrador', 'Medico', 'Paciente'];
        $conteos = [];

        foreach ($roles as $rol) {
            $conteos[] = Bitacora::whereBetween('fec_hor_bit', [
                Carbon::parse($this->fechaInicio)->startOfDay(),
                Carbon::parse($this->fechaFin)->endOfDay()
            ])
                ->whereHas('usuario.roles', fn($q) => $q->where('name', $rol))
                ->count();
        }

        $this->labels = $roles;
        $this->data = $conteos;
    }

    /** 🧾 Exportar a PDF */
    public function exportarPDF()
    {
        $bitacoras = $this->bitacoras ?? collect();
        $pdf = Pdf::loadView('exports.reporte-bitacora', [
            'bitacoras' => $bitacoras,
            'fechaInicio' => $this->fechaInicio,
            'fechaFin' => $this->fechaFin,
            'rolUsuario' => $this->rolUsuario
        ])->setPaper('A4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Reporte_Bitacora_' . now()->format('d-m-Y') . '.pdf');
    }

    /** 📗 Exportar a Excel */
    public function exportarExcel()
    {
        return Excel::download(new BitacoraExport(
            $this->fechaInicio,
            $this->fechaFin,
            $this->rolUsuario
        ), 'Reporte_Bitacora_' . now()->format('d-m-Y') . '.xlsx');
    }

    public function render()
    {
        return view('livewire.reporte-admin', [
            'bitacoras' => $this->bitacoras ?? collect(),
            'labels' => $this->labels,
            'data' => $this->data
        ]);
    }
}
