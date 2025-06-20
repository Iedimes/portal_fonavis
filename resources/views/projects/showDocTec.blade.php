@extends('adminlte::page')

@section('title', 'FONAVIS')


@section('content')
    <br>
    <div class="invoice p-3 mb-3">

        <div class="row">
            <div class="col-12">
                <h4>
                    <i class="fas fa-university"></i> Proyecto: {{ $project->name }}
                    @if ($project->getEstado && $project->getEstado->stage_id == 8)
                        <a type="button" href="{{ url('generate-pdf/'.$project->id) }}" class="btn btn-danger float-right"  style="margin-right: 5px;">
                            <i class="fas fa-download"></i> IMPRIMIR PDF
                        </a>
                    @endif


        @if (($project->getEstado))

        @else
            <button id="enviarBtn" type="button" class="btn btn-success float-right" onclick="allchecked()"
                {{ $todosCargados ? '' : 'disabled' }}>
                <i class="fa fa-plus-circle"></i> Enviar al MUVH
            </button>
        @endif

        @if ($project->getEstado && $project->getEstado->stage_id == 4)
            <a href="{{ url('projectsDocTec/'.$project->id) }}" class="btn btn-success float-right">
                <i class="fa fa-plus-circle"></i> Enviar Documento solicitado
            </a>
        @endif

        @if ($project->getEstado && $project->getEstado->stage_id == 11)

        @else

        <button id="enviarDocumentosBtn" class="btn btn-success float-right" onclick="allchecked()">
            <i class="fa fa-plus-circle"></i> Enviar Documentos
        </button>

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
                    <strong>Estado:</strong>
                    {{ $project->getEstado ? $project->getEstado->getStage->name : 'Pendiente' }}<br>
                </address>
            </div>

            <div class="col-sm-4 invoice-col">
                <address>
                    <strong>Telefono:</strong> {{ $project->phone }}<br>
                    <strong>Distrito:</strong> {{ $project->city_id ? strtoupper($project->getCity->CiuNom) : '' }}<br>
                    <strong>Tipo de Terreno:</strong> {{ $project->land_id ? $project->getLand->name : '' }}<br>
                    <strong>Cantidad de Viviendas:</strong> {{ $postulantes->count() }}<br>
                </address>
            </div>

            <div class="col-sm-4 invoice-col">
                <address>
                    <strong>SAT:</strong> {{ $project->sat_id ? $project->getSat->NucNomSat : '' }}<br>
                    <strong>Localidad:</strong> {{ $project->localidad }}

                    <br>
                    <strong>Tipologia:</strong> {{ $project->typology_id ? $project->getTypology->name : '' }}<br>
                </address>
            </div>

        </div>

        <div class="card card-primary card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill"
                            href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home"
                            aria-selected="true">Documentos VTA y ETH</a>
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
                                            <th>#</th>
                                            <th>Documento</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th>Adjuntar Documento</th>
                                            <th>Accion</th>
                                            </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $todosCargados = true;
                                        @endphp

                                        @foreach ($docproyecto as $key => $item)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $item->document->name }}</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>

                                                <td>
                                                    @if ($uploadedFiles[$item->document_id])
                                                        Documento adjuntado

                                                            {{-- href="{{ url('get/' . $project->id . '/' . $item->document_id . '/' . $uploadedFiles[$item->document_id]) }}"> --}}
                                                            <a href="{{ route('downloadFile', ['project' => $project->id, 'document_id' => $item->document_id, 'file_name' => $uploadedFiles[$item->document_id]]) }}">
                                                            <button class="btn btn-info">
                                                                <i class="fa fa-search"></i>
                                                            </button>
                                                        </a>
                                                    @else
                                                        <form action="/levantarTecnico" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name="project_id"
                                                                value="{{ $project->id }}">
                                                            <input type="file" name="archivo">
                                                            <input type="hidden" name="title"
                                                                value="{{ $item->document->name }}">
                                                            <input type="hidden" name="document_id"
                                                                value="{{ $item->document->id }}">
                                                            <button type="submit">Subir</button>
                                                        </form>
                                                        @php
                                                            $todosCargados = false;
                                                        @endphp
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($project->getEstado && $project->getEstado->stage_id == 10)
                                                        @if ($uploadedFiles[$item->document_id])
                                                            <form action="{{ route('eliminar', ['project_id' => $project->id, 'document_id' => $item->document->id]) }}" method="GET">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="submit" class="btn btn-danger">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endif

                                                </td>
                                            </tr>
                                        @endforeach

                                        @if (session('message'))
                                            <div class="alert alert-success" id="success-message">
                                                {{ session('message') }}
                                            </div>
                                        @endif


                                        @if ($errors->any())
                                            <div class="alert alert-danger" id="error-message">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                        </div>


                    </div>


                </div>
            </div>

        </div>


    </div>

@stop

@section('js')

    <script>
        function delay(time) {
            return new Promise(resolve => setTimeout(resolve, time));
        }

        function allchecked() {
            var sites = {!! json_encode($project['id']) !!};
            var abc = sites;
            var keys = {!! json_encode($claves) !!};
            var applicants = {!! $postulantes->count() !!}
            var def = keys;
            var si = 0;
            var no = 0;


                let todosCargados = true; // Variable en JavaScript

                // Verificar si todos los documentos están cargados
                const rows = document.querySelectorAll('tr');
                rows.forEach(row => {
                    const uploadForm = row.querySelector('form[action="/levantarTecnico"]');
                    if (uploadForm) {
                        todosCargados = false;
                    }
                });

                if (todosCargados) {
                    console.log('Puede Enviar al MUVH');
                    // Mostrar mensaje de éxito o realizar cualquier acción adicional
                    //alert('Todos los documentos están adjuntos y cargados. Puede enviar al MUVH.');

                    // Realizar la llamada AJAX solo si todos los documentos están adjuntos
                    var projectId = {{ $project->id }};
                    $.ajax({
                        url: '/projectsTecnico/' + projectId,
                        type: "GET",
                        dataType: "json",
                        success: async function(data) {
                            if (data.message == 'success') {
                                $(document).Toasts('create', {
                                    icon: 'fas fa-exclamation',
                                    class: 'bg-success m-1',
                                    autohide: true,
                                    delay: 5000,
                                    title: 'Importante!',
                                    body: 'Los documentos han sido enviados'
                                });
                                await delay(3000);
                                location.reload();
                                console.log('refrescar');
                            } else {
                                console.log('no hace nada');
                            }
                        }
                    });
                } else {
                    // Mostrar mensaje de error o realizar cualquier acción adicional
                    alert('Debe adjuntar y cargar todos los documentos para enviar al MUVH.');
                }

        }

        // Obtén las referencias a los elementos de mensaje
        var successMessage = document.getElementById('success-message');
        var errorMessage = document.getElementById('error-message');

        // Establece el tiempo de desaparición en milisegundos (5 segundos en este caso)
        var tiempoDesaparicion = 5000;

        // Cambia el color de fondo y establece la opacidad para los mensajes de éxito y error
        if (successMessage) {
            successMessage.style.backgroundColor = 'lightgreen'; // Cambia el color de fondo a verde claro
            successMessage.style.opacity = 1; // Establece la opacidad al máximo
            successMessage.style.border = 'none'; // Elimina el borde del mensaje de éxito
        }

        if (errorMessage) {
            errorMessage.style.backgroundColor = 'lightcoral'; // Cambia el color de fondo a coral claro
            errorMessage.style.opacity = 1; // Establece la opacidad al máximo
            errorMessage.style.border = 'none'; // Elimina el borde del mensaje de error
        }

        // Función para desvanecer y ocultar los mensajes después del tiempo de desaparición
        function ocultarMensajes() {
            if (successMessage) {
                successMessage.style.opacity = 0; // Establece la opacidad a 0 para desvanecer el mensaje de éxito
            }

            if (errorMessage) {
                errorMessage.style.opacity = 0; // Establece la opacidad a 0 para desvanecer el mensaje de error
            }
        }

        // Inicia el temporizador para ocultar los mensajes después del tiempo de desaparición
        setTimeout(ocultarMensajes, tiempoDesaparicion);

        function todos() {
            var fileInputs = document.querySelectorAll('input[type="file"]'); // Obtener todos los inputs de tipo file
            var allFilesUploaded = true;

            fileInputs.forEach(function(input) {
                if (!input.files.length) {
                    allFilesUploaded = false;
                    return;
                }
            });

            // Habilitar o deshabilitar el botón según la condición de carga de los archivos
            var enviarBtn = document.querySelector('.btn-success');
            enviarBtn.disabled = !allFilesUploaded;
        }
    </script>

@endsection
