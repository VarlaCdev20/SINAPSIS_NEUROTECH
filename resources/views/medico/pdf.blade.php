<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Solicitantes - Sinapsis</title>
    <style>
        @page {
            margin: 80px 50px;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: #1f2937; /* gray-800 */
        }

        header {
            position: fixed;
            top: -60px;
            left: 0;
            right: 0;
            height: 60px;
            text-align: center;
        }

        footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 40px;
            font-size: 12px;
            color: #6b7280; /* gray-500 */
            text-align: center;
        }

        .page-number:after {
            content: counter(page);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th, td {
            border: 1px solid #e5e7eb; /* gray-200 */
            padding: 8px 10px;
            font-size: 14px;
        }

        th {
            background-color: #6d28d9; /* violet-700 */
            color: white;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: #f9fafb; /* gray-50 */
        }

        .title {
            font-size: 22px;
            font-weight: 700;
            color: #4c1d95; /* indigo-900 */
        }

        .subtitle {
            font-size: 16px;
            color: #6b21a8; /* purple-800 */
            font-weight: 500;
        }
    </style>
</head>
<body>

    <header>
        <div class="flex justify-between items-center">
            <div class="text-center">
                <h1 class="title">Sinapsis</h1>
                <p class="subtitle">Lista de Solicitantes</p>
            </div>
        </div>
    </header>

    <main class="mt-10">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Apellido Paterno</th>
                    <th>Apellido Materno</th>
                </tr>
            </thead>
            <tbody>
                @foreach($solicitantes as $sol)
                <tr>
                    <td>{{ $sol->cod_sol }}</td>
                    <td>{{ $sol->nom_sol }}</td>
                    <td>{{ $sol->ap_pat_sol }}</td>
                    <td>{{ $sol->ap_mat_sol }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>

    <footer>
        Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} |
        Página <span class="page-number"></span>
    </footer>

</body>
</html>
