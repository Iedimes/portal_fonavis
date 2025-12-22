{{-- DEPENDENCY 7 DGTI --}}

{{-- Grupo 1: Herramientas Generales (Imprimir, Legajo) --}}
{{-- Imprimir --}}
<div class="p-1">
    <a class="btn btn-sm btn-primary" :href="item.resource_url + '/project'" title="{{ trans('IMPRIMIR POSTULANTES') }}"
        role="button">
        <i class="fa fa-print"></i>
    </a>
</div>

{{-- Legajo --}}
<div class="p-1">
    <a class="btn btn-sm" style="background-color: #0787db; color: white;" :href="item.resource_url + '/legajo'"
        title="VER LEGAJO" role="button">
        <i class="fa fa-folder-open"></i>
    </a>
</div>


{{-- Grupo 2: Flujo de Etapas (Menor a Mayor Stage ID) --}}

<div class="p-1">
    <a class="btn btn-sm" style="background-color: #3c8dbc; color: white;" :href="item.resource_url + '/show'"
        title="VER PROYECTO" role="button">
        <i class="fa fa-search"></i>
    </a>
</div>

{{-- Stage 2: showVERDOCFONAVIS --}}
<div class="p-1">
    <a class="btn btn-sm btn-success" :href="item.resource_url + '/showVERDOCFONAVIS'" title="VER DOC FONAVIS"
        role="button">
        <i class="fa fa-file-text-o"></i>
    </a>
</div>

{{-- Stage 2/4/6: showDGJN (Legal) --}}
<div class="p-1">
    <a class="btn btn-sm" style="background-color: #6610f2; color: white;" :href="item.resource_url + '/showDGJN'"
        title="VER DGJN (LEGAL)" role="button">
        <i class="fa fa-gavel"></i>
    </a>
</div>

{{-- Stage 3-8-13-21: showFONAVIS (Principal) --}}
<div class="p-1">
    <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVIS'" title="VER FONAVIS" role="button">
        <i class="fa fa-institution"></i>
    </a>
</div>

{{-- Stage 5: showDGJNFALTANTE --}}
<div class="p-1">
    <a class="btn btn-sm" style="background-color: #e83e8c; color: white;"
        :href="item.resource_url + '/showDGJNFALTANTE'" title="VER DGJN FALTANTE" role="button">
        <i class="fa fa-exclamation-circle"></i>
    </a>
</div>

{{-- Stage 8: showDGSO --}}
<div class="p-1">
    <a class="btn btn-sm" style="background-color: #20c997; color: white;" :href="item.resource_url + '/showDGSO'"
        title="VER DGSO (SOCIAL)" role="button">
        <i class="fa fa-users"></i>
    </a>
</div>

{{-- Stage 9: showFONAVISSOCIAL --}}
<div class="p-1">
    <a class="btn btn-sm" style="background-color: #6f42c1; color: white;"
        :href="item.resource_url + '/showFONAVISSOCIAL'" title="VER FONAVIS SOCIAL" role="button">
        <i class="fa fa-child"></i>
    </a>
</div>

{{-- Stage 11: showFONAVISTECNICO --}}
<div class="p-1">
    <a class="btn btn-sm" style="background-color: #fd7e14; color: white;"
        :href="item.resource_url + '/showFONAVISTECNICO'" title="VER FONAVIS TECNICO" role="button">
        <i class="fa fa-cogs"></i>
    </a>
</div>

{{-- Stage 11/12: showDIGH --}}
<div class="p-1">
    <a class="btn btn-sm" style="background-color: #17a2b8; color: white;" :href="item.resource_url + '/showDIGH'"
        title="VER DIGH (HABITAT)" role="button">
        <i class="fa fa-home"></i>
    </a>
</div>

{{-- Stage 16: showDSGO --}}
<div class="p-1">
    <a class="btn btn-sm" style="background-color: #343a40; color: white;" :href="item.resource_url + '/showDSGO'"
        title="VER DSGO" role="button">
        <i class="fa fa-building-o"></i>
    </a>
</div>

{{-- Stage 17: showFONAVISADJ --}}
<div class="p-1">
    <a class="btn btn-sm" style="background-color: #28a745; color: white;"
        :href="item.resource_url + '/showFONAVISADJ'" title="VER ADJUDICACION" role="button">
        <i class="fa fa-check-circle"></i>
    </a>
</div>


{{-- Grupo 3: Edición y Eliminación (Al final por seguridad) --}}

{{-- Editar --}}
<div class="p-1">
    <a class="btn btn-sm btn-spinner btn-info" :href="item.resource_url + '/edit'"
        title="{{ trans('brackets/admin-ui::admin.btn.edit') }}" role="button"><i class="fa fa-edit"></i></a>
</div>

{{-- Eliminar --}}
<div class="p-1">
    <a class="btn btn-sm btn-danger" :href="'/admin/motivos/' + item.id + '/create/'"
        title="{{ trans('ELIMINAR PROYECTO') }}" role="button">
        <i class="fa fa-trash-o"></i>
    </a>
</div>
