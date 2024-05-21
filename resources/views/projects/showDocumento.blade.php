@extends('adminlte::page')

@section('title', 'FONAVIS')

@section('content')
    <br>
    <div class="invoice p-3 mb-3">
        <div class="row">
            <div class="col-12">
                <h4>
                    <i class="fas fa-university"></i> Proyecto: {{ $project->name }}

                    {{-- <button id="enviarBtn" type="button" class="btn btn-success float-right enviar-muvh-btn" onclick="allchecked()">
                        <i class="fa fa-plus-circle"></i> Enviar Documentos Faltantes
                    </button> --}}
                    {{-- <form action="{{ route('enviarDocumentosFaltantes') }}" method="POST">
                        @csrf
                        <button id="enviarBtn" type="button" class="btn btn-success float-right enviar-muvh-btn" onclick="allchecked()">
                            <i class="fa fa-plus-circle"></i> Enviar Documentos Faltantes
                        </button>
                    </form> --}}


                    @if ($project->getEstado->stage_id == 5)



                    @else
                    <form method="POST">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                        <button id="enviarBtn" type="submit" formaction="/enviar-documentos-faltantes" class="btn btn-success float-right enviar-muvh-btn">
                            <i class="fa fa-plus-circle"></i> Enviar Documento
                        </button>
                    </form>
                    @endif
                </h4>
            </div>
        </div>

        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                <address>
                    <strong>Lider:</strong> {{ $project->leader_name }}<br>
                    <strong>Departamento: </strong>{{ $project->state_id ? $project->getState->DptoNom : '' }}<br>
                    <strong>Modalidad:</strong> {{ $project->modalidad_id ? $project->getModality->name : '' }}<br>
                    <strong>Estado:</strong> {{ $project->getEstado ? $project->getEstado->getStage->name : 'Pendiente' }}<br>
                </address>
            </div>

            <div class="col-sm-4 invoice-col">
                <address>
                    <strong>Teléfono:</strong> {{ $project->phone }}<br>
                    <strong>Distrito:</strong> {{ $project->city_id ? strtoupper($project->getCity->CiuNom) : '' }}<br>
                    <strong>Tipo de Terreno:</strong> {{ $project->land_id ? $project->getLand->name : '' }}<br>
                    <strong>Cantidad de Viviendas:</strong> {{ $postulantes->count() }}<br>
                </address>
            </div>

            <div class="col-sm-4 invoice-col">
                <address>
                    <strong>SAT:</strong> {{ $project->sat_id ? $project->getSat->NucNomSat : '' }}<br>
                    <strong>Localidad:</strong> {{ $project->localidad }}<br>
                    <strong>Tipología:</strong> {{ $project->typology_id ? $project->getTypology->name : '' }}<br>
                </address>
            </div>
        </div>

        <div class="card card-primary card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill"
                            href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home"
                            aria-selected="true">Documentos a Entregar</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                    <div class="tab-pane fade active show" id="custom-tabs-one-home" role="tabpanel"
                        aria-labelledby="custom-tabs-one-home-tab">
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            @if ($project->getEstado->stage_id == 4)
                                            <th>Accion</th>
                                            {{-- <th>Archivo</th>
                                            <th>Eliminar</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>

                                                <form action="/levantarDocumento" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="project_id" value="{{ $project->id }}">

                                                    <div id="archivos-container">
                                                        <div class="archivo-input">
                                                            <input type="file" name="archivos[]" multiple>
                                                        </div>
                                                    </div>



                                                    <button type="button" id="agregar-archivo" onclick="agregarArchivo()">Agregar archivo</button>
                                                    <button type="submit">Subir</button>
                                                </form>
                                                @endif
                                            </td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>

                                            <th>Archivo</th>
                                            <th>Descargar Archivo</th>
                                            <th>Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($documents as $document)
                                            <tr>

                                                <td>

                                                    {{-- {{ $document->title }} --}}
                                                    {{ $document->file_path }}


                                                </td>

                                                <td>
                                                    @if ($project->getEstado->stage_id == 4)
                                                    <a href="{{ route('bajarDocumento', ['project' => $project->id, 'document_id' => $document->document_id, 'file_name' => $document->file_path]) }}" class="btn btn-primary">Descargar</a>
                                                    @elseif($project->getEstado->stage_id == 5)


                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($project->getEstado->stage_id == 4)
                                                    <form action="{{ route('eliminarDocumento', ['project_id' => $project->id, 'document_id' => $document->document_id]) }}" method="GET">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="document_id" value="{{ $document->id }}">
                                                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                                    </form>
                                                    @elseif($project->getEstado->stage_id == 5)


                                                    @endif
                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

                                        @if (session('message'))
                                            <div class="alert alert-success" id="success-message">
                                                {{ session('message') }}
                                            </div>
                                        @endif
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function agregarArchivo() {
            var archivosContainer = document.getElementById('archivos-container');
            var archivoInput = document.createElement('div');
            archivoInput.classList.add('archivo-input');
            archivoInput.innerHTML = '<input type="file" name="archivos[]" multiple>';
            archivosContainer.appendChild(archivoInput);
        }

        $(document).ready(function() {
        // Esperar 5 segundos y luego ocultar el mensaje
        setTimeout(function() {
            $('#success-message').fadeOut('slow');
        }, 5000);
         // Obtener la cantidad de archivos cargados
         var cantidadArchivos = {{ count($documents) }};

        // Obtener el botón "Enviar al MUVH"
        var enviarBtn = document.getElementById('enviarBtn');

        // Habilitar o deshabilitar el botón según la cantidad de archivos
        if (cantidadArchivos > 0) {
            enviarBtn.disabled = false;
            enviarBtn.style.display = 'block';
        } else {
            enviarBtn.disabled = true;
            enviarBtn.style.display = 'none';
        }
            });
    </script>
@endsection
