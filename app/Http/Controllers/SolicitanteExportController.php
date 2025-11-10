<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Solicitante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;


class SolicitanteExportController extends Controller
{
    // Exportar PDF
    public function exportPdf()
    {
        $solicitantes = Solicitante::all();
        $pdf = Pdf::loadView('medico.pdf', compact('solicitantes'));
        return $pdf->download('solicitantes.pdf');
    }


public function exportCsv()
{
    $solicitantes = Solicitante::all();
    $filename = "solicitantes.csv";

    $headers = [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $callback = function() use ($solicitantes) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['Código', 'Nombre', 'Apellido Paterno', 'Apellido Materno']);

        foreach ($solicitantes as $sol) {
            fputcsv($file, [$sol->cod_sol, $sol->nom_sol, $sol->ap_pat_sol, $sol->ap_mat_sol]);
        }

        fclose($file);
    };

    return Response::stream($callback, 200, $headers);
}

}
