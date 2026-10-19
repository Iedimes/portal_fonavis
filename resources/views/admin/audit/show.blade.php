@extends('brackets/admin-ui::admin.layout.default')

@section('title', 'Detalle de Auditoría #' . $audit->id)

@section('body')
<div class="container-fluid">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-primary font-weight-bold">
                <i class="fa fa-history mr-2"></i> Auditoría #{{ $audit->id }} - {{ $modelName }} (#{{ $audit->auditable_id }})
            </h5>
            <a href="{{ url('admin/audits') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-arrow-left mr-1"></i> Volver al listado
            </a>
        </div>

        <div class="card-body">
            <!-- Resumen Informativo -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-secondary border-bottom pb-2 mb-3">
                                <i class="fa fa-info-circle mr-1"></i> Información de la Operación
                            </h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <th style="width: 140px;" class="text-muted">Fecha y Hora:</th>
                                    <td><strong>{{ $audit->created_at ? $audit->created_at->format('d/m/Y H:i:s') : '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Acción:</th>
                                    <td>
                                        <span class="badge {{ $eventInfo['badge'] }} px-2 py-1">
                                            {{ $eventInfo['label'] }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Módulo / Modelo:</th>
                                    <td>
                                        <strong>{{ $modelName }}</strong>
                                        <span class="badge badge-light border ml-1">#{{ $audit->auditable_id }}</span>
                                        <div class="small text-muted">{{ $audit->auditable_type }}</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-secondary border-bottom pb-2 mb-3">
                                <i class="fa fa-user-circle mr-1"></i> Usuario y Conexión
                            </h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <th style="width: 140px;" class="text-muted">Responsable:</th>
                                    <td>
                                        <span class="badge {{ $userData['badge'] }} mr-1">{{ $userData['role'] }}</span>
                                        <strong>{{ $userData['name'] }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Correo / ID:</th>
                                    <td>{{ $userData['email'] }} <span class="text-muted">(ID: {{ $userData['id'] ?? '-' }})</span></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Dirección IP:</th>
                                    <td><code>{{ $audit->ip_address ?? 'No disponible' }}</code></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Ruta / URL:</th>
                                    <td><span class="small text-break">{{ $audit->url ?? '-' }}</span></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Navegador:</th>
                                    <td><span class="small text-muted text-break">{{ $audit->user_agent ?? '-' }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Valores Modificados -->
            <h5 class="font-weight-bold text-secondary mb-3">
                <i class="fa fa-exchange mr-1"></i> Detalle de Valores (Comparativa)
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 25%;">Campo / Atributo</th>
                            <th style="width: 37.5%;" class="bg-danger text-white">
                                <i class="fa fa-minus-circle mr-1"></i> Valor Anterior
                            </th>
                            <th style="width: 37.5%;" class="bg-success text-white">
                                <i class="fa fa-plus-circle mr-1"></i> Valor Nuevo
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($changes as $ch)
                            <tr {{ $ch['modified'] ? 'style=background-color:#fff9e6;' : '' }}>
                                <td>
                                    <strong>{{ $ch['field'] }}</strong>
                                    @if ($ch['modified'])
                                        <span class="badge badge-warning text-dark ml-1">Modificado</span>
                                    @endif
                                </td>
                                <td>
                                    <pre class="mb-0 small text-danger" style="white-space: pre-wrap; font-family: Consolas, monospace;">{{ $ch['old'] !== '' && $ch['old'] !== null ? $ch['old'] : '(vacío)' }}</pre>
                                </td>
                                <td>
                                    <pre class="mb-0 small text-success" style="white-space: pre-wrap; font-family: Consolas, monospace;">{{ $ch['new'] !== '' && $ch['new'] !== null ? $ch['new'] : '(vacío)' }}</pre>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    No se registraron cambios específicos en este evento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
