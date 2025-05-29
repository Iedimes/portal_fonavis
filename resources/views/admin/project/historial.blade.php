@extends('brackets/admin-ui::admin.layout.default')

@section('title', $title)

@section('body')

<div class="card">
    <div class="card-header">
        <h4 style="color: #001c54;">{{ $title }}</h4>
        <p style="color: #001c54;"><strong>{{ $project->name }}</strong></p>
    </div>

    <div class="card-body">
       <table class="table table-bordered table-striped">
    <thead style="background-color: #001c54; color: white;">
        <tr>
            <th>ESTADO</th>
            <th>FECHA</th>
            <th>USUARIO</th>
            <th>OBSERVACION</th>
            <th>ARCHIVO</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($history as $status)
            <tr>
                <td>{{ $status->getStage->name ?? 'N/A' }}</td>
                <td>{{ $status->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $status->nombre_usuario }}</td>
                <td>{{ $status->record }}</td>
                <td>
                    @php
                        $media = $status->imagen->first();
                    @endphp
                    @if ($media)
                        <a href="/media/{{ $media->id }}/{{ $media->file_name }}" target="_blank">VER DICTAMEN</a>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">No hay historial disponible.</td>
            </tr>
        @endforelse
    </tbody>
</table>


        <a href="{{ url()->previous() }}" class="btn btn-primary">
            <i class="fa fa-arrow-left"></i> VOLVER
        </a>
    </div>
</div>

@endsection
