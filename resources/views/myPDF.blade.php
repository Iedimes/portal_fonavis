<!DOCTYPE html>
<html>
<head>
    <title>Contraseña Mesa de Entrada</title>
    <style>
        /** Define los estilos generales **/
        body {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }

        h4 {
            text-align: center;
            position: relative;
        }

        h4::after {
            content: "";
            display: block;
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 2px;
            background-color: black;
        }

        #logo {
            text-align: center;
            padding: 20px 0;
            position: relative;
        }

        #logo img {
            width: 690px;
            border: 1px solid #DDD;
        }

        #logo::after {
            content: "";
            display: block;
            position: absolute;
            bottom: -5px;
            left: 0;
            right: 0;
            margin: 0 auto;
            width: 80px;
            height: 2px;
            background-color: black;
        }

        hr {
            border: none;
            border-top: 1px solid #DDD;
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #DDDDDD;
            padding: 8px;
            font-size: 12px;
        }

        th {
            background-color: #0e3d80;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #fff;
        }

        tr:hover {
            background-color: #DDD;
        }

        td:nth-child(2) {
            border: none;
        }
    </style>
</head>
<body>
    <header>
        <div id="logo">
            <img src="{{ storage_path('images/logofull.png') }}" alt="Logo">
        </div>
    </header>

    <h4>CONTRASEÑA <span style="font-weight: normal;">PARA MESA DE ENTRADA</span></h4>

    <hr>

    <table>
        <tr>
            <td style="width: 40%">
                <strong>Proyecto:</strong> {{ $project->name }}<br>
                <strong>Código:</strong> {{ $project->id }}<br>
                <strong>Programa:</strong> FONAVIS<br>
                <strong>Cantidad de Viviendas:</strong> {{ $project->households }}<br>
                <strong>SAT:</strong> {{ utf8_encode($project->sat_id ? $project->getSat->NucNomSat : '') }}<br>
                <strong>Departamento:</strong> {{ $project->state_id ? $project->getState->DptoNom : '' }}<br>
                <strong>Distrito:</strong> {{ $project->city_id }}<br>
                <strong>Modalidad:</strong> {{ $project->modalidad_id ? $project->getModality->name : '' }}<br>
                <strong>Tipo de Terreno:</strong> {{ $project->land_id ? $project->getLand->name : '' }}<br>
            </td>
            <td style="text-align: right">
                <img src="data:image/png;base64, {{ base64_encode($valor) }}" alt="">
            </td>
        </tr>
    </table>
</body>
</html>
