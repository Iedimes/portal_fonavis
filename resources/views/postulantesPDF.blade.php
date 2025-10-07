<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Postulantes</title>
    <style>
        body {
            font-family: "Helvetica", "Arial", sans-serif;
            color: #000;
            font-size: 13px;
            margin: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 90%;
            max-width: 950px;
            height: auto;
        }

        h4 {
            text-align: left;
            margin-top: 10px;
            margin-bottom: 10px;
            font-size: 16px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 5px;
        }

        td, th {
            padding: 6px 8px;
            border: 1px solid #999;
            vertical-align: middle;
        }

        th {
            background-color: #f5f5f5;
            text-align: left;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .no-border td {
            border: none;
            padding: 4px 8px;
        }

        .section {
            margin-bottom: 25px;
        }

        .small {
            font-size: 12px;
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="{{ storage_path('images/logofull.png') }}" alt="Encabezado MUVH">
    </div>

    <div class="section">
        <h4>Proyecto: {{ $project->name }}</h4>
        <table class="no-border">
            <tr>
                <td><strong>Código:</strong> {{ $project->id }}</td>
            </tr>
            <tr>
                <td><strong>Líder:</strong> {{ $project->leader_name }}</td>
                <td><strong>Teléfono:</strong> {{ $project->phone }}</td>
                <td><strong>SAT:</strong> {{ $project->sat_id ? $project->getSat->NucNomSat : "" }}</td>
            </tr>
            <tr>
                <td><strong>Departamento:</strong> {{ $project->state_id ? $project->getState->DptoNom : "" }}</td>
                <td><strong>Distrito:</strong> {{ $project->city_id }}</td>
                <td><strong>Localidad:</strong> {{ $project->localidad }}</td>
            </tr>
            <tr>
                <td><strong>Modalidad:</strong> {{ $project->modalidad_id ? $project->getModality->name : "" }}</td>
                <td><strong>Tipo de Terreno:</strong> {{ $project->land_id ? $project->getLand->name : "" }}</td>
                <td><strong>Tipología:</strong> {{ $project->typology_id ? $project->getTypology->name : "" }}</td>
            </tr>
            <tr>
                <td><strong>Estado:</strong> {{ $project->getEstado ? $project->getEstado->getStage->name : "Pendiente" }}</td>
                <td><strong>Cantidad de Viviendas:</strong> {{ $contar }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h4>Listado de Postulantes</h4>
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">#</th>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>Edad</th>
                    <th>Ingreso</th>
                    <th>Nivel</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($postulantesData as $key => $postulante)
                <tr>
                    <td style="text-align:center">{{ $key + 1 }}</td>
                    <td>{{ $postulante['first_name'] }} {{ $postulante['last_name'] }}</td>
                    <td style="text-align:center">
                        @if (is_numeric($postulante['cedula']))
                            {{ number_format($postulante['cedula'], 0, ".", ".") }}
                        @else
                            {{ $postulante['cedula'] }}
                        @endif
                    </td>
                    <td style="text-align:center">{{ $postulante['edad'] }}</td>
                    <td style="text-align:center">{{ number_format($postulante['ingreso'], 0, ".", ".") }}</td>
                    <td style="text-align:center">{{ $postulante['nivel'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>
