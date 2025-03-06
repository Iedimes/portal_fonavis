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
                        <a href="{{ url('admin/reportes/imprimir?inicio=' . $filtros['inicio'] . '&fin=' . $filtros['fin'] . '&user_id=' . $filtros['user_id'] . '&state_id=' . $filtros['state_id']) }}"
   target="_blank"
   class="btn btn-danger">
    <i class="fa fa-file-pdf-o"></i> GENERAR INFORME
</a>


                    </div>
                </div>
                <div class="card-body" v-cloak>
                    <div class="card-block">
                        <!-- Mostrar filtros aplicados -->
                        {{-- <h5>Filtros Aplicados</h5>
                        <ul>
                            <li><strong>Fecha de Inicio:</strong> {{ $filtros['inicio'] }}</li>
                            <li><strong>Fecha de Fin:</strong> {{ $filtros['fin'] }}</li>
                            <li><strong>Usuario:</strong> {{ $filtros['user_id'] == 0 ? 'Todos' : $filtros['user_id'] }}</li>
                            <li><strong>Estado:</strong> {{ $filtros['state_id'] == 0 ? 'Todos' : $filtros['state_id'] }}</li>
                        </ul>
                        <hr> --}}

                        <!-- Mostrar resultados -->
                        @if($dhelps->isEmpty())
                            <p>No se encontraron registros con los criterios especificados.</p>
                        @else
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Nombre y Apellido</th>
                                        <th>Solución</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dhelps as $item)
                                        <tr>
                                            <td>{{ $item->help_id }}</td>
                                            <td>{{ $item->user->first_name }} {{ $item->user->last_name }}</td>
                                            <td>{{ $item->solution }}</td>
                                            <td>{{ $item->updated_at }}</td>
                                            <td>{{ $item->state->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <p style="font-weight: bold;">Total de Asistencias: {{ $contar }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
