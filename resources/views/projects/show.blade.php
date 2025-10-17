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
                    {{-- @else
    <button type="button" class="btn btn-success float-right" onclick="allchecked()">
        <i class="fa fa-plus-circle"></i> Enviar al MUVH
        </button>
    @endif --}}
                    {{-- <a type="button" href="{{ url('generate-pdf/'.$project->id) }}" class="btn btn-danger float-right"  style="margin-right: 5px;">
        <i class="fas fa-download"></i> IMPRIMIR PDF
        </a> --}}

                    {{-- <button type="button" class="btn btn-success float-right" onclick="allchecked()">
            <i class="fa fa-plus-circle"></i> Enviar al MUVH
            </button> --}}

                    <!-- Botón -->
                    {{-- <button type="button" class="btn btn-success float-right" onclick="allchecked()" {{ $todosCargados ? '' : 'disabled' }}>
            <i class="fa fa-plus-circle"></i> Enviar al MUVH
        </button> --}}

       @if (!$project->getEstado || $project->getEstado->stage_id == 22)
            <button id="enviarBtn" type="button" class="btn btn-success float-right" onclick="allchecked()"
                {{ $todosCargados ? '' : 'disabled' }}>
                <i class="fa fa-plus-circle"></i> Enviar al MUVH
            </button>
        @endif

        @if ($project->getEstado && $project->getEstado->stage_id == 4)
            <a href="{{ url('projectsDoc/'.$project->id) }}" class="btn btn-success float-right">
                <i class="fa fa-plus-circle"></i> Enviar Documento solicitado
            </a>
        @endif

        {{-- @if ($project->getEstado && $project->getEstado->stage_id == 7)
            <a href="{{ url('projectsMiembros/'.$project->id) }}" class="btn btn-success float-right">
                <i class="fa fa-plus-circle"></i> Enviar Grupo Familiar
            </a>
        @endif --}}

                    {{-- <button id="enviarBtn" type="button" class="btn btn-success float-right" onclick="allchecked()" {{ $todosCargados ? '' : 'disabled' }}>
            <i class="fa fa-plus-circle"></i> Enviar al MUVH
        </button> --}}
                    {{-- @if (isset($project->getEstado) && $project->getEstado->stage_id == 1)
            <button type="button" class="btn btn-success float-right" onclick="allchecked()">
                <i class="fa fa-plus-circle"></i> Enviar al MUVH
            </button>
        @else
            <!-- Código o lógica adicional cuando la condición no se cumple -->
        @endif



        {{-- @if (session('bandera'))
    @php
        $bandera = session('bandera');
    @endphp
    <button type="button" class="btn btn-success float-right" {{ $bandera ? '' : 'disabled' }}>
        <i class="fa fa-plus-circle"></i> Enviar al MUVH
    </button>
@endif --}}

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
                    <strong>SAT:</strong> {{ ($project->getSat && $project->getSat->NucNomSat) ? $project->getSat->NucNomSat : 'N/A' }}<br>
                    <strong>Localidad:</strong> {{ $project->localidad }}
                    {{-- <strong>Localidad:</strong> @if ($project->localidad == 123)
    Villa Florida
    @elseif($project->localidad==230)
    Santa Rosa del Mbutuy
    @elseif($project->localidad==145)
    Itakyry
    @elseif($project->localidad==49)
    Colonia Independencia
    @elseif($project->localidad==179)
    Asuncion
    @endif --}}
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
                            aria-selected="true">Documentos</a>
                    </li>
                    @if ($existenDocumentos)
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-two-home-tab" data-toggle="pill"
                            href="#custom-tabs-two-home" role="tab" aria-controls="custom-tabs-two-home"
                            aria-selected="false">Documentos VTA y ETH</a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-one-applicant-tab" data-toggle="pill"
                            href="#custom-tabs-one-applicant" role="tab" aria-controls="custom-tabs-one-applicant"
                            aria-selected="false">Postulantes</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill"
                            href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile"
                            aria-selected="false">Historial</a>
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
                                            <th>{{-- N° FOLIO  --}}</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th>Adjuntar Documento</th>
                                            <th>Accion</th>
                                            {{-- <th>Check</th> --}}
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
                                                <td></td>
                                                <td>
                                                    @if ($uploadedFiles[$item->document_id])
                                                        Documento adjuntado
                                                        <a
                                                        href="{{ route('downloadFile', ['project' => $project->id, 'document_id' => $item->document_id, 'file_name' => $uploadedFiles[$item->document_id]]) }}">
                                                            {{-- href="{{ url('get/' . $project->id . '/' . $item->document_id . '/' . $uploadedFiles[$item->document_id]) }}"> --}}
                                                            <button class="btn btn-info">
                                                                <i class="fa fa-search"></i>
                                                            </button>
                                                        </a>
                                                    @else
                                                        <form action="/levantar" method="POST" enctype="multipart/form-data">
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
                                                    @if (!$project->getEstado || $project->getEstado->stage_id == 22)
                                                        @if ($uploadedFiles[$item->document_id])
                                                            {{-- <form
                                                                action="{{ route('eliminar', ['project_id' => $project->id, 'document_id' => $item->document->id]) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="submit" class="btn btn-danger">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form> --}}
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
                                                {{-- <td>
                    <a class="btn btn-sm btn-success" href="/ver/{{$project->id}}/{{$item->document->id}}" target="_blank" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                        <i class="fa fa-eye"></i>
                    </a>

                    </td> --}}
                                                </tr>

                                        @endforeach

                                        @if ($project->getLand->id == 1)
                                        <tr>
                                            <td>

                                                    <a href="{{ url('projectsDocNoExcluyentes/'.$project->id) }}">
                                                        DOCUMENTOS NO EXCLUYENTES
                                                    </a>

                                            </td>
                                            <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>



                                        </tr>
                                        @endif

                                        <tr>
                                            <td colspan="2">
                                                <a href="{{ url('projectsDocCondominio/'.$project->id) }}">
                                                    INFORME DE CONDICION DE DOMINIO (POR CADA FINCA Y O MATRICULA)
                                                </a>
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        @if ($project->modalidad_id == 1)
                                        <tr>
                                            <td colspan="2">
                                                <a href="{{ url('projectsDocIndi/'.$project->id) }}">
                                                    NO OBJECIÓN DEL INDI
                                                </a>
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        @endif


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

                    <div class="tab-pane fade" id="custom-tabs-two-home" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Documento</th>
                                            <th>{{-- N° FOLIO  --}}</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th>Adjuntar Documento</th>
                                            <th>Accion</th>
                                            {{-- <th>Check</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $todosCargados = true;
                                        @endphp

                                        @foreach ($documentos as $key => $item)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $item->title }}</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>
                                                    @if ($uploadedFiles2[$item->document_id])
                                                        Documento adjuntado
                                                        <a href="{{ route('downloadFile', ['project' => $project->id, 'document_id' => $item->document_id, 'file_name' => $uploadedFiles2[$item->document_id]]) }}">
                                                            <button class="btn btn-info">
                                                                <i class="fa fa-search"></i>
                                                            </button>
                                                        </a>
                                                    @else
                                                        <form action="/levantar" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name="project_id" value="{{ $project->id }}">
                                                            <input type="file" name="archivo">
                                                            <input type="hidden" name="title" value="{{ $item->document->name }}">
                                                            <input type="hidden" name="document_id" value="{{ $item->document->id }}">
                                                            <button type="submit">Subir</button>
                                                        </form>
                                                        @php
                                                            $todosCargados = false;
                                                        @endphp
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($project->getEstado)
                                                    @else
                                                        @if (isset($uploadedFiles[$item->document_id]) && $uploadedFiles[$item->document_id])
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

                    <div class="tab-pane fade" id="custom-tabs-one-applicant" role="tabpanel"
                        aria-labelledby="custom-tabs-one-profile-tab">
                        <a href="{{ url('projects/' . $project->id . '/postulantes') }}">

                           @if ($project->getEstado && $project->getEstado->getStage && $project->getEstado->getStage->id != 22)
    <a href="{{ url('imprimir/' . $project->id) }}">
        <button type="button" class="btn btn-info btn-block btn-lg btn-lg">
            <i class="fa fa-file-excel-o"></i> Imprimir Listado
        </button>
    </a>
@endif


                            {{-- @if ($project->getEstado || $postulantes->count() >= 50) --}}
                            @if (!$project->getEstado || $project->getEstado->stage_id == 22)
                                <button type="button" class="btn btn-info float-right">
                                    <i class="fa fa-user"></i> Ir a la Seccion de Postulantes
                                </button>
                            @endif

                        </a>
                        <br>
                        <br>
                        <table class="table table-striped">
                            <thead>
                                <th>#</th>
                                <th>Nombre</th>
                                <th class="text-center">Cedula</th>
                                <th class="text-center">Edad</th>
                                <th class="text-center">Ingreso</th>
                                <th class="text-center">Nivel</th>
                            </thead>
                            <tbody>
                                @if (count($postulantesData) > 0)
                                    @foreach ($postulantesData as $key => $data)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $data['first_name'] }} {{ $data['last_name'] }}</td>

                                            @if (is_numeric($data['cedula']))
                                                <td class="text-center">
                                                    {{ number_format($data['cedula'], 0, '.', '.') }}
                                                </td>
                                            @else
                                                <td class="text-center">{{ $data['cedula'] }}</td>
                                            @endif

                                            <td class="text-center">{{ $data['edad'] }}</td>
                                            <td class="text-center">{{ number_format($data['ingreso'], 0, '.', '.') }}</td>
                                            <td class="text-center">{{ $data['nivel'] }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>

                    </div>
                    <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel"
                        aria-labelledby="custom-tabs-one-profile-tab">

                        <table class="table table-striped">
                            <thead>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <!-- <th>Usuario</th> -->
                                <th>Observacion</th>
                            </thead>
                            <tbody>
                                @foreach ($history as $item)
                                    <tr>
                                        <td>{{ $item->getStage->name }}</td>
                                        <td>{{ $item->created_at }} </td>
                                        <!-- <td>{{ $item->getUser ? $item->getUser['first_name'] : 'N/A' }}</td> -->
                                        <td> {{ $item->record }} </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                    </div>

                </div>

            </div>

        </div>


    </div>




    <div class="modal modal-info fade" id="modal-enviar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title"><i class="fa  fa-send"></i> Enviar Proyecto al MUVH</h4>
                </div>
                <div class="modal-body">
                    <form action="{{ url('projects/send') }}" method="post">
                        {{ csrf_field() }}
                        <p id="demoproy"></p>
                        <input id="send_id" name="send_id" type="hidden" value="" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-outline">Enviar</button>
                </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

@stop

@section('js')

<script>
    function delay(time) {
        return new Promise(resolve => setTimeout(resolve, time));
    }

    async function allchecked() {
        const enviarBtn = document.getElementById('enviarBtn'); // 🔹 agrega id="enviarBtn" al botón en tu vista
        enviarBtn.disabled = true;
        enviarBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Enviando...';

        const sites = {!! json_encode($project['id']) !!};
        const keys = {!! json_encode($claves) !!};
        const applicants = {!! $postulantes->count() !!};
        const edades = {!! json_encode($edadesPostulantes) !!};
        const edadesConyuges = {!! json_encode($edadesConyuges) !!};

        // 🚫 Verificar si algún postulante o cónyuge es menor de edad
        const menorEdad = edades.some(edad => edad < 18);
        const menorConyuge = edadesConyuges.some(edad => edad < 18);
        if (menorEdad || menorConyuge) {
            alert('Existe un postulante o cónyuge menor de 18 años. No se puede enviar el proyecto al MUVH.');
            enviarBtn.disabled = false;
            enviarBtn.innerHTML = '<i class="fa fa-plus-circle"></i> Enviar al MUVH';
            return;
        }

        // 🚫 Verificar cantidad mínima de postulantes
        if (applicants < 4) {
            alert('Debe tener al menos 4 (cuatro) postulantes para enviar el proyecto al MUVH.');
            enviarBtn.disabled = false;
            enviarBtn.innerHTML = '<i class="fa fa-plus-circle"></i> Enviar al MUVH';
            return;
        }

        // 🚫 Verificar documentos cargados
        let todosCargados = true;
        const rows = document.querySelectorAll('tr');
        rows.forEach(row => {
            const uploadForm = row.querySelector('form[action="/levantar"]');
            if (uploadForm) {
                todosCargados = false;
            }
        });

        if (!todosCargados) {
            alert('Debe adjuntar y cargar todos los documentos para enviar al MUVH.');
            enviarBtn.disabled = false;
            enviarBtn.innerHTML = '<i class="fa fa-plus-circle"></i> Enviar al MUVH';
            return;
        }

        // ✅ Enviar la solicitud AJAX
        $.ajax({
            url: '{{ URL::to('/projects/send') }}/' + sites,
            type: "GET",
            dataType: "json",
            success: async function(data) {
                if (data.message === 'success') {
                    $(document).Toasts('create', {
                        icon: 'fas fa-check-circle',
                        class: 'bg-success m-1',
                        autohide: true,
                        delay: 4000,
                        title: 'Importante!',
                        body: 'El proyecto ha sido enviado correctamente al MUVH.'
                    });
                    await delay(3000);
                    location.reload();
                } else if (data.message === 'duplicate') {
                    $(document).Toasts('create', {
                        icon: 'fas fa-exclamation-triangle',
                        class: 'bg-warning m-1',
                        autohide: true,
                        delay: 5000,
                        title: 'Atención!',
                        body: 'El proyecto ya fue enviado anteriormente.'
                    });
                    enviarBtn.disabled = false;
                    enviarBtn.innerHTML = '<i class="fa fa-plus-circle"></i> Enviar al MUVH';
                } else {
                    alert('No se pudo procesar el envío.');
                    enviarBtn.disabled = false;
                    enviarBtn.innerHTML = '<i class="fa fa-plus-circle"></i> Enviar al MUVH';
                }
            },
            error: function() {
                alert('Error al enviar el proyecto. Verifique su conexión e intente nuevamente.');
                enviarBtn.disabled = false;
                enviarBtn.innerHTML = '<i class="fa fa-plus-circle"></i> Enviar al MUVH';
            }
        });
    }

    // 💬 Manejo visual de mensajes de éxito y error
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');
    const tiempoDesaparicion = 5000;

    if (successMessage) {
        successMessage.style.backgroundColor = 'lightgreen';
        successMessage.style.opacity = 1;
        successMessage.style.border = 'none';
    }

    if (errorMessage) {
        errorMessage.style.backgroundColor = 'lightcoral';
        errorMessage.style.opacity = 1;
        errorMessage.style.border = 'none';
    }

    setTimeout(() => {
        if (successMessage) successMessage.style.opacity = 0;
        if (errorMessage) errorMessage.style.opacity = 0;
    }, tiempoDesaparicion);

    // 🔄 Habilita el botón solo cuando todos los archivos estén cargados
    function todos() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        let allFilesUploaded = true;

        fileInputs.forEach(input => {
            if (!input.files.length) allFilesUploaded = false;
        });

        const enviarBtn = document.querySelector('.btn-success');
        if (enviarBtn) enviarBtn.disabled = !allFilesUploaded;
    }
</script>


@endsection
