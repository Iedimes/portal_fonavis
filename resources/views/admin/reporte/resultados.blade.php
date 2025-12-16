@extends('brackets/admin-ui::admin.layout.default')

@section('title', 'Resultados del Reporte')

@section('body')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fa fa-align-justify"></i> {{ trans('Reporte de Proyectos') }}
                    </div>
                    <div>
                        <!-- Botón para generar el PDF -->
                        <!-- <a href="{{ url('admin/reportes/imprimir?inicio=' . request()->query('inicio') . '&fin=' . request()->query('fin') . '&user_id=' . request()->query('user_id') . '&state_id=' . request()->query('state_id')) }}"
                           target="_blank"
                           class="btn btn-danger">
                            <i class="fa fa-file-pdf-o"></i> GENERAR INFORME
                        </a> -->
                        <a href="{{ url('admin/reportes/exportar-resultados') }}?{{ http_build_query(request()->only(['inicio', 'fin', 'proyecto_id', 'sat_id', 'state_id', 'city_id', 'modalidad_id', 'stage_id'])) }}"
                        class="btn btn-success">
                            Exportar a Excel
                        </a>



                    </div>
                </div>
                <div class="card-body" v-cloak>
                    <div class="card-block">
                        <!-- Mostrar resultados -->
                        @if($results->isEmpty())
                            <p>No se encontraron registros con los criterios especificados.</p>
                        @else
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Nombre del Proyecto</th>
                                        <th>Descripción</th>
                                        <th>SAT</th>
                                        <th>DEPARTAMENTO</th>
                                        <th>DISTRITO</th>
                                        <th>MODALIDAD</th>
                                        <th>Estado</th>
                                        <th>Fecha de Creación</th>
                                        <th>Última Actualización</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($results as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->getEstado->record ?? 'Sin Descripcion' }}</td>
                                            <td>{{ $item->getsat->NucNomSat }}</td>
                                            <td>{{ $item->getstate->DptoNom }}</td>
                                            <td>{{ $item->getcity->CiuNom }}</td>
                                            <td>{{ $item->getmodality->name }}</td>
                                            <td>{{ $item->getEstado->getstage->name ?? 'Sin Estado'}}</td>
                                            <td>{{ $item->created_at }}</td>
                                            <td><small>Última actualización: {{ $item->updated_at }}</small></td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <p style="font-weight: bold;">Total de Proyectos: {{ $results->count() }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
