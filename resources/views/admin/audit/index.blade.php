@extends('brackets/admin-ui::admin.layout.default')

@section('title', 'Auditoría del Sistema')

@section('body')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-primary font-weight-bold">
                <i class="fa fa-history mr-2"></i> Auditoría General del Sistema
            </h5>
            <span class="badge badge-light border text-muted px-3 py-2 font-weight-normal">
                Total Registros: <strong>{{ number_format($audits->total()) }}</strong>
            </span>
        </div>

        <div class="card-body">
            <!-- Formulario de Filtros -->
            <form action="{{ url('admin/audits') }}" method="GET" class="mb-4 bg-light p-3 rounded border">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold text-muted">Búsqueda rápida</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="ID registro, ID auditoría o IP..."
                               value="{{ $filters['search'] ?? '' }}">
                    </div>

                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold text-muted">Módulo / Entidad</label>
                        <select name="auditable_type" class="form-control form-control-sm">
                            <option value="">-- Todos los módulos --</option>
                            @foreach ($modelLabels as $class => $name)
                                <option value="{{ $class }}" {{ ($filters['auditable_type'] ?? '') == $class ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold text-muted">Acción / Evento</label>
                        <select name="event" class="form-control form-control-sm">
                            <option value="">-- Todas --</option>
                            @foreach ($eventLabels as $evKey => $evData)
                                <option value="{{ $evKey }}" {{ ($filters['event'] ?? '') == $evKey ? 'selected' : '' }}>
                                    {{ $evData['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold text-muted">Tipo de Usuario</label>
                        <select name="user_type" class="form-control form-control-sm">
                            <option value="">-- Todos --</option>
                            <option value="App\Models\AdminUser" {{ ($filters['user_type'] ?? '') == 'App\Models\AdminUser' ? 'selected' : '' }}>
                                Funcionario MUVH
                            </option>
                            <option value="App\Models\User" {{ ($filters['user_type'] ?? '') == 'App\Models\User' ? 'selected' : '' }}>
                                SAT / Externo
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold text-muted">Por página</label>
                        <select name="per_page" class="form-control form-control-sm">
                            <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold text-muted">Fecha Desde</label>
                        <input type="date" name="from_date" class="form-control form-control-sm"
                               value="{{ $filters['from_date'] ?? '' }}">
                    </div>

                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold text-muted">Fecha Hasta</label>
                        <input type="date" name="to_date" class="form-control form-control-sm"
                               value="{{ $filters['to_date'] ?? '' }}">
                    </div>

                    <div class="col-md-6 mb-2 d-flex align-items-end justify-content-end">
                        <button type="submit" class="btn btn-primary btn-sm px-3 mr-2">
                            <i class="fa fa-filter mr-1"></i> Filtrar
                        </button>
                        <a href="{{ url('admin/audits') }}" class="btn btn-outline-secondary btn-sm px-3">
                            <i class="fa fa-refresh mr-1"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>

            <!-- Tabla de Auditoría -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 70px;"># ID</th>
                            <th style="width: 150px;">Fecha y Hora</th>
                            <th>Usuario Responsable</th>
                            <th style="width: 110px;" class="text-center">Acción</th>
                            <th>Módulo / Entidad</th>
                            <th>Campos Modificados</th>
                            <th style="width: 130px;">IP</th>
                            <th style="width: 90px;" class="text-center">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($audits as $audit)
                            @php
                                $userData = \App\Http\Controllers\Admin\AuditsController::getUserData($audit);
                                $modelName = \App\Http\Controllers\Admin\AuditsController::getModelName($audit->auditable_type);
                                $eventInfo = $eventLabels[$audit->event] ?? ['label' => ucfirst($audit->event), 'badge' => 'badge-secondary'];

                                $modifiedKeys = [];
                                if ($audit->event === 'updated') {
                                    $modifiedKeys = array_keys($audit->new_values ?? []);
                                } elseif ($audit->event === 'created') {
                                    $modifiedKeys = array_keys($audit->new_values ?? []);
                                } else {
                                    $modifiedKeys = array_keys($audit->old_values ?? []);
                                }
                            @endphp
                            <tr>
                                <td class="font-weight-bold text-muted text-center">{{ $audit->id }}</td>
                                <td>
                                    <span class="small font-weight-bold">
                                        {{ $audit->created_at ? $audit->created_at->format('d/m/Y H:i:s') : '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge {{ $userData['badge'] }} mr-1">{{ $userData['role'] }}</span>
                                        <strong>{{ $userData['name'] }}</strong>
                                    </div>
                                    <div class="small text-muted">{{ $userData['email'] }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $eventInfo['badge'] }} px-2 py-1">
                                        {{ $eventInfo['label'] }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $modelName }}</strong>
                                    <span class="badge badge-light border text-dark ml-1">#{{ $audit->auditable_id }}</span>
                                </td>
                                <td>
                                    @if (count($modifiedKeys) > 0)
                                        <div class="small text-secondary">
                                            @foreach (array_slice($modifiedKeys, 0, 5) as $mk)
                                                <span class="badge badge-light border mr-1">{{ $mk }}</span>
                                            @endforeach
                                            @if (count($modifiedKeys) > 5)
                                                <span class="badge badge-secondary">+{{ count($modifiedKeys) - 5 }} más</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">Sin campos registrados</span>
                                    @endif
                                </td>
                                <td>
                                    <code class="small text-dark">{{ $audit->ip_address ?? '-' }}</code>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-info"
                                            title="Ver Detalle"
                                            onclick="openAuditDetail({{ $audit->id }})">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fa fa-info-circle fa-2x mb-2 text-secondary d-block"></i>
                                    No se encontraron registros de auditoría con los filtros aplicados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Mostrando del {{ $audits->firstItem() ?? 0 }} al {{ $audits->lastItem() ?? 0 }} de {{ number_format($audits->total()) }} registros
                </div>
                <div>
                    {{ $audits->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalle completo de cambios -->
<div class="modal fade" id="auditDetailModal" tabindex="-1" role="dialog" aria-labelledby="auditDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="auditDetailModalLabel">
                    <i class="fa fa-search mr-2"></i> Detalle de Auditoría #<span id="modalAuditId"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="modalAuditBody">
                <div class="text-center py-4" id="modalAuditLoader">
                    <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2 text-muted">Cargando detalles...</p>
                </div>
                <div id="modalAuditContent" style="display: none;">
                    <!-- Cabecera de Datos -->
                    <div class="row mb-3 pb-3 border-bottom">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Fecha:</strong> <span id="modalAuditDate"></span></p>
                            <p class="mb-1"><strong>Acción:</strong> <span id="modalAuditEvent"></span></p>
                            <p class="mb-1"><strong>Módulo:</strong> <span id="modalAuditModel"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Usuario:</strong> <span id="modalAuditUser"></span></p>
                            <p class="mb-1"><strong>Dirección IP:</strong> <span id="modalAuditIp"></span></p>
                            <p class="mb-1"><strong>URL:</strong> <span id="modalAuditUrl" class="text-break small"></span></p>
                        </div>
                    </div>

                    <!-- Tabla Comparativa -->
                    <h6 class="font-weight-bold text-secondary mb-2">Comparativa de Valores</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 25%;">Campo</th>
                                    <th style="width: 37.5%;" class="bg-danger text-white">Valor Anterior</th>
                                    <th style="width: 37.5%;" class="bg-success text-white">Valor Nuevo</th>
                                </tr>
                            </thead>
                            <tbody id="modalAuditChangesTable">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <a id="modalFullLink" href="#" class="btn btn-sm btn-outline-primary mr-auto">
                    <i class="fa fa-external-link"></i> Abrir en página completa
                </a>
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function openAuditDetail(id) {
    $('#modalAuditId').text(id);
    $('#modalAuditLoader').show();
    $('#modalAuditContent').hide();
    $('#modalFullLink').attr('href', '{{ url("admin/audits") }}/' + id);
    $('#auditDetailModal').modal('show');

    fetch('{{ url("admin/audits") }}/' + id, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        $('#modalAuditDate').text(data.created_at);
        $('#modalAuditEvent').html('<span class="badge ' + data.event_badge + '">' + data.event + '</span>');
        $('#modalAuditModel').html('<strong>' + data.model + '</strong> <span class="badge badge-light border">#' + data.auditable_id + '</span>');
        $('#modalAuditUser').html('<span class="badge ' + data.user.badge + ' mr-1">' + data.user.role + '</span> ' + data.user.name + ' (' + data.user.email + ')');
        $('#modalAuditIp').text(data.ip_address || '-');
        $('#modalAuditUrl').text(data.url || '-');

        let rowsHtml = '';
        if (data.changes && data.changes.length > 0) {
            data.changes.forEach(ch => {
                let rowBg = ch.modified ? 'style="background-color: #fff9e6;"' : '';
                rowsHtml += '<tr ' + rowBg + '>';
                rowsHtml += '<td><strong>' + escapeHtml(ch.field) + '</strong></td>';
                rowsHtml += '<td class="text-danger"><pre class="mb-0 small" style="white-space: pre-wrap;">' + (ch.old !== null && ch.old !== '' ? escapeHtml(ch.old) : '<em class="text-muted">(vacío)</em>') + '</pre></td>';
                rowsHtml += '<td class="text-success"><pre class="mb-0 small" style="white-space: pre-wrap;">' + (ch.new !== null && ch.new !== '' ? escapeHtml(ch.new) : '<em class="text-muted">(vacío)</em>') + '</pre></td>';
                rowsHtml += '</tr>';
            });
        } else {
            rowsHtml = '<tr><td colspan="3" class="text-center text-muted">No hay campos para mostrar</td></tr>';
        }

        $('#modalAuditChangesTable').html(rowsHtml);
        $('#modalAuditLoader').hide();
        $('#modalAuditContent').fadeIn(150);
    })
    .catch(err => {
        $('#modalAuditLoader').html('<div class="text-danger"><i class="fa fa-exclamation-triangle"></i> Error al cargar datos del registro.</div>');
    });
}

function escapeHtml(str) {
    if (typeof str !== 'string') return str;
    return str.replace(/&/g, "&amp;")
              .replace(/</g, "&lt;")
              .replace(/>/g, "&gt;")
              .replace(/"/g, "&quot;")
              .replace(/'/g, "&#039;");
}
</script>

<style>
.table td, .table th {
    vertical-align: middle;
}
.badge {
    font-size: 85%;
}
</style>
@endsection
