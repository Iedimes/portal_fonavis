@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.projects.actions.show'))

<style>
    .table-responsive {
        position: relative;
        overflow: auto; /* Permitir scroll */
        max-height: 400px; /* Altura máxima para el scroll vertical */
    }

    .table {
        width: auto; /* Cambiar a auto para ajustar a los títulos */
        border-collapse: collapse;
    }

    .table th, .table td {
        white-space: nowrap; /* Evitar que el texto se divida en varias líneas */
        padding: 12px; /* Aumentar padding para mejor presentación */
    }

    .thead-light th {
        background-color: #f8f9fa; /* Color de fondo para la cabecera */
        position: sticky; /* Mantener la cabecera fija */
        top: 0; /* Fijar en la parte superior */
        z-index: 10; /* Asegúrate de que la cabecera esté por encima del contenido */
    }

    textarea.form-control {
        height: 150px; /* Aumentar la altura de los textarea */
        resize: vertical; /* Permitir redimensionar verticalmente */
    }
</style>

@section('body')
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header text-center" data-toggle="collapse" data-target="#projectInfo" aria-expanded="true" aria-controls="projectInfo">
                DATOS {{ utf8_encode($project->name) }}
            </div>
            <div id="projectInfo" class="collapse show">
                <div class="card-body">
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            <address>
                                <strong>Lider:</strong> {{ $project->leader_name ?? 'N' }}<br>
                                <strong>Departamento:</strong> {{ utf8_encode($project->state_id ? $project->getState->DptoNom : 'N') }}<br>
                                <strong>Modalidad:</strong> {{ utf8_encode($project->modalidad_id ? $project->getModality->name : 'N') }}<br>
                                <strong>Estado:</strong> <span class="badge bg-success" style="font-size:1.1em; color:white">{{ $project->getEstado ? $project->getEstado->getStage->name : 'Pendiente' }}</span><br>
                            </address>
                        </div>

                        <div class="col-sm-4 invoice-col">
                            <address>
                                <strong>Telefono:</strong> {{ utf8_encode($project->phone ?? 'N') }}<br>
                                <strong>Distrito:</strong> {{ $project->city_id ? strtoupper($project->getCity->CiuNom) : 'N' }}<br>
                                <strong>Tipo de Terreno:</strong> {{ utf8_encode($project->land_id ? $project->getLand->name : 'N') }}<br>
                                <strong>Cantidad de Viviendas:</strong> {{ $postulantes->count() }}<br>
                            </address>
                        </div>

                        <div class="col-sm-4 invoice-col">
                            <address>
                                <strong>SAT:</strong> {{ utf8_encode($project->sat_id ? $project->getSat->NucNomSat : 'N') }}<br>
                                <strong>Localidad:</strong> {{ utf8_encode($project->localidad ?? 'N') }}<br>
                                <strong>Tipologia:</strong> {{ utf8_encode($project->typology_id ? $project->getTypology->name : 'N') }}<br>
                            </address>
                        </div>
                    </div>
                    @if (!empty($project->getEstado) && $project->getEstado->stage_id == 8 && Auth::user()->rol_app->dependency_id == 3)
                        <a href="{{ url('admin/projects/' . $project->id . '/transition') }}" type="button" class="btn btn-primary">CAMBIAR ESTADO</a>
                    @endif
                </div>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    <script>
                        setTimeout(function() {
                            $('.alert').fadeOut('slow');
                        }, 10000);
                    </script>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" id="error-message" style="display: block;">
                        <i class="fa fa-times-circle"></i> {{ session('error') }}
                    </div>
                    <script>
                        setTimeout(function() {
                            $('.alert').fadeOut('slow');
                        }, 10000);
                    </script>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="m-0 text-center flex-grow-1">PLANILLA DE CALIFICACION</h2>
                <a href="{{ url('/admin/postulantes/exportar/' . $project->id) }}" class="btn btn-success mt-2">
                    <i class="fas fa-file-excel"></i> Exportar a Excel
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Orden</th>
                                <th>Biblio</th>
                                <th>Exp.</th>
                                <th>{{ trans('Apellido y Nombre') }}</th>
                                <th class="text-center">{{ trans('N° de cedula de identidad') }}</th>
                                <th class="text-center">{{ trans('Ingreso') }}</th>
                                <th class="text-center">{{ trans('Apellido y Nombre del Conyuge o concubino') }}</th>
                                <th class="text-center">{{ trans('N° de cedula de identidad') }}</th>
                                <th class="text-center">{{ trans('Ingreso') }}</th>
                                <th class="text-center">{{ trans('Ingreso Total') }}</th>
                                <th class="text-center">{{ trans('Nivel') }}</th>
                                <th class="text-center">{{ trans('Cantidad de Hijos') }}</th>
                                <th class="text-center">{{ trans('Discap') }}</th>
                                <th class="text-center">{{ trans('3°Edad') }}</th>
                                <th class="text-center">{{ trans('Hijo Sosten') }}</th>
                                <th class="text-center">{{ trans('Otra Persona a Cargo') }}</th>
                                <th class="text-center">{{ trans('Terreno') }}</th>
                                <th class="text-center">{{ trans('Residencia') }}</th>
                                <th class="text-center">{{ trans('Composición del Grupo Familiar') }}</th>
                                <th class="text-center">{{ trans('Documentos Presentados') }}</th>
                                <th class="text-center">{{ trans('Documentos Faltantes') }}</th>
                                <th class="text-center">{{ trans('Observaciones de Consideracion') }}</th>
                                <th class="text-center">{{ trans('Califica') }}</th>
                                <th class="text-center">{{ trans('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($postulantes) > 0)
                                @foreach ($postulantes as $key => $post)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>1</td>
                                        <td>{{ $post->getPostulante->nexp ?? 'N' }}</td>
                                        <td>{{ $post->getPostulante->last_name . ' ' . $post->getPostulante->first_name ?? 'N' }}</td>
                                        <td class="text-center">
                                            @if (is_numeric($post->getPostulante->cedula ?? ''))
                                                {{ number_format($post->getPostulante->cedula, 0, ',', '.') }}
                                            @else
                                                N
                                            @endif
                                        </td>
                                       <td class="text-center">
                                            <input type="text" class="form-control"
                                                style="background-color: #f0f8ff; text-align: right; width: 120px;"
                                                value="{{ number_format($post->getPostulante->ingreso ?? 0, 0, ',', '.') }}"
                                                onchange="saveField('{{ $post->getPostulante->id }}', 'ingreso', this.value.replace(/\./g, '').replace(',', '.'))">
                                       </td>

                                        @php
                                            $conyuge = $post->getMembers->firstWhere('parentesco_id', 1) ?? $post->getMembers->firstWhere('parentesco_id', 8);
                                        @endphp
                                        <td class="text-center">
                                            @if ($conyuge)
                                                 {{ $conyuge->getPostulante->last_name . ' ' . $conyuge->getPostulante->first_name }}
                                            @else

                                                <a class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#modal" data-postulante-id="{{ $post->getPostulante->id }}" href="#" onclick="setPostulanteId({{ $post->getPostulante->id }})">Agregar Conyuge</a>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($conyuge && is_numeric($conyuge->getPostulante->cedula ?? ''))
                                                {{ number_format($conyuge->getPostulante->cedula, 0, ',', '.') }}
                                            @else
                                                --------------
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($conyuge)
                                                <input type="text" class="form-control"
                                                    style="background-color: #f0f8ff; text-align: right; width: 120px;"
                                                    value="{{ number_format($conyuge->getPostulante->ingreso ?? 0, 0, ',', '.') }}"
                                                    onchange="saveField('{{ $conyuge->getPostulante->id }}', 'ingreso', this.value.replace(/\./g, '').replace(',', '.'))">
                                            @else
                                                --------------
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <input type="text" class="form-control"
                                                style="background-color: #f0f8ff; text-align: right; width: 120px;"
                                                value="{{ number_format(App\Models\ProjectHasPostulantes::getIngreso($post->postulante_id), 0, ',', '.') }}"
                                                onchange="saveField('{{ $post->postulante_id }}', 'ingreso_familiar', this.value.replace(/\./g, '').replace(',', '.'))">
                                        </td>

                                        <td class="text-center">
                                            <input type="text" class="form-control"
                                                style="background-color: #f0f8ff; text-align: right; width: 120px;"
                                                value="{{ App\Models\ProjectHasPostulantes::getNivel($post->postulante_id) }}"
                                                onchange="saveField('{{ $post->postulante_id }}', 'nivel', this.value)">
                                        </td>
                                        <td class="text-center">
                                            <input type="text" class="form-control"
                                                value="{{ $post->getPostulante->cantidad_hijos ?? 0 }}"
                                                onchange="saveField('{{ $post->getPostulante->id }}', 'cantidad_hijos', this.value)"
                                                style="background-color: #f0f8ff; text-align: right; width: 80px;">
                                        </td>
                                        <td class="text-center">
                                            <select class="form-control"
                                                    onchange="saveField('{{ $post->getPostulante->id }}', 'discapacidad', this.value)"
                                                    style="background-color: #f0f8ff;">
                                                <option value="N" {{ $post->getPostulante->discapacidad == 'N' || $post->getPostulante->discapacidad === null ? 'selected' : '' }}>N</option>
                                                <option value="S" {{ $post->getPostulante->discapacidad == 'S' ? 'selected' : '' }}>S</option>
                                            </select>
                                        </td>

                                        <td class="text-center">
                                            <select class="form-control"
                                                    onchange="saveField('{{ $post->getPostulante->id }}', 'tercera_edad', this.value)"
                                                    style="background-color: #f0f8ff;">
                                                <option value="N" {{ $post->getPostulante->tercera_edad == 'N' || $post->getPostulante->tercera_edad === null ? 'selected' : '' }}>N</option>
                                                <option value="S" {{ $post->getPostulante->tercera_edad == 'S' ? 'selected' : '' }}>S</option>
                                            </select>
                                        </td>

                                        <td class="text-center">
                                            <select class="form-control"
                                                    onchange="saveField('{{ $post->getPostulante->id }}', 'hijo_sosten', this.value)"
                                                    style="background-color: #f0f8ff;">
                                                <option value="N" {{ $post->getPostulante->hijo_sosten == 'N' || $post->getPostulante->hijo_sosten === null ? 'selected' : '' }}>N</option>
                                                <option value="S" {{ $post->getPostulante->hijo_sosten == 'S' ? 'selected' : '' }}>S</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <select class="form-control"
                                                    onchange="saveField('{{ $post->getPostulante->id }}', 'otra_persona_a_cargo', this.value)"
                                                    style="background-color: #f0f8ff; padding: 0.375rem 0.75rem;">
                                                <option value="N" {{ $post->getPostulante->otra_persona_a_cargo == 'N' || $post->getPostulante->otra_persona_a_cargo === null ? 'selected' : '' }}>N</option>
                                                <option value="S" {{ $post->getPostulante->otra_persona_a_cargo == 'S' ? 'selected' : '' }}>S</option>
                                            </select>
                                        </td>
                                        <td class="text-center">{{ utf8_encode($project->land_id ? $project->getLand->name : 'N') }}</td>
                                        <td class="text-center">{{ $post->getPostulante->address ?? 'N' }}</td>
                                        <td class="text-center">
                                            <textarea class="form-control"
                                                      onchange="saveField('{{ $post->getPostulante->id }}', 'composicion_del_grupo', this.value)"
                                                      style="background-color: #f0f8ff;">{{ $post->getPostulante->composicion_del_grupo ?? '' }}</textarea>
                                        </td>
                                        <td class="text-center">
                                            <textarea class="form-control"
                                                      onchange="saveField('{{ $post->getPostulante->id }}', 'documentos_presentados', this.value)"
                                                      style="background-color: #f0f8ff;">{{ $post->getPostulante->documentos_presentados ?? '' }}</textarea>
                                        </td>
                                        <td class="text-center">
                                            <textarea class="form-control"
                                                      onchange="saveField('{{ $post->getPostulante->id }}', 'documentos_faltantes', this.value)"
                                                      style="background-color: #f0f8ff;">{{ $post->getPostulante->documentos_faltantes ?? '' }}</textarea>
                                        </td>
                                        <td class="text-center">
                                            <textarea class="form-control"
                                                      onchange="saveField('{{ $post->getPostulante->id }}', 'observacion_de_consideracion', this.value)"
                                                      style="background-color: #f0f8ff;">{{ $post->getPostulante->observacion_de_consideracion ?? '' }}</textarea>
                                        </td>
                                        <td class="text-center">
                                            <select class="form-control"
                                                    onchange="saveField('{{ $post->getPostulante->id }}', 'califica', this.value)"
                                                    style="background-color: #f0f8ff; padding: 0.375rem 0.75rem;">
                                                <option value="S" {{ $post->getPostulante->califica === 'S' ? 'selected' : '' }}>S</option>
                                                <option value="N" {{ $post->getPostulante->califica === 'N' ? 'selected' : '' }}>N</option>
                                            </select>
                                        </td>

                                        <td class="text-center">
                                            <a class="btn btn-sm btn-outline-primary" data-postulante-id="{{ $post->postulante_id }}" href="{{ route('adminprojectsshowpostulantes', ['id' => $project->id, 'idpostulante' => $post->postulante_id]) }}">Ver Miembros</a>
                                            {{-- <a class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#modal" data-postulante-id="{{ $post->getPostulante->id }}" href="#" onclick="setPostulanteId({{ $post->getPostulante->id }})">Agregar Conyuge</a> --}}
                                        </td>


                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="20" class="text-center">{{ trans('admin.project.messages.no_applicants') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="miembro-form" action="#" method="POST">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h4 class="modal-title">Ingrese Número de Cédula del Conyuge</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="postulante_id" id="postulante_id" value="">
                    <div class="form-group {{ $errors->has('cedula') ? 'has-error' : '' }}">
                        <input type="text" class="form-control" name="cedula" value="" required placeholder="Ingrese número de cédula">
                        {!! $errors->first('cedula', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

<script type="text/javascript">
    function setPostulanteId(postulanteId) {
        document.getElementById('postulante_id').value = postulanteId;
    }

    function saveField(postId, fieldName, value) {
        $.ajax({
            url: '/admin/postulantes/' + postId + '/actualizar',
            method: 'POST',
            data: {
                field: fieldName,
                value: value,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Campo guardado exitosamente.');
            },
            error: function(error) {
                console.error('Error al guardar el campo:', error);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('miembro-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Previene el envío del formulario
            const postulanteId = document.getElementById('postulante_id').value;
            form.action = '{{ url('admin/projects/'.$project->id.'/postulante/') }}' + '/' + postulanteId + '/crearmiembro';
            form.submit(); // Ahora envía el formulario
        });
    });
</script>
