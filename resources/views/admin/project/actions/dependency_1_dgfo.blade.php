{{-- DEPENDENCY 1 --}}
{{-- No estado --}}
<div class="p-1" v-if="!item.get_estado">
    <a class="btn btn-sm btn-danger" :href="'/admin/motivos/' + item.id + '/create/'"
        title="{{ trans('ELIMINAR PROYECTO') }}" role="button">
        <i class="fa fa-trash-o"></i>
    </a>
</div>

{{-- Imprimir postulantes --}}
<div class="p-1">
    <a class="btn btn-sm btn-primary" :href="item.resource_url + '/project'" title="{{ trans('IMPRIMIR POSTULANTES') }}"
        role="button">
        <i class="fa fa-print"></i>
    </a>
</div>

{{-- Stages FONAVIS --}}
<div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 1">
    <a class="btn btn-sm btn-warning" :href="item.resource_url + '/show'"
        title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
        <i class="fa fa-search"></i>
    </a>
</div>

<div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 2">
    <a class="btn btn-sm btn-success" :href="item.resource_url + '/showVERDOCFONAVIS'"
        title="{{ trans('brackets/admin-ui::admin.btn.showverdocfonavis') }}" role="button">
        <i class="fa fa-search"></i>
    </a>
</div>


<div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 3">
    <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVIS'"
        title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
        <i class="fa fa-search"></i>
    </a>
</div>
<div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 4">
    <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVIS'"
        title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
        <i class="fa fa-search"></i>
    </a>
</div>
<div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 6">
    <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVIS'"
        title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
        <i class="fa fa-search"></i>
    </a>
</div>
<div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 8">
    <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVIS'"
        title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
        <i class="fa fa-search"></i>
    </a>
</div>

<div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 21">
    <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVIS'"
        title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
        <i class="fa fa-search"></i>
    </a>
</div>

<div class="p-1" v-if="item.get_estado">
    <a class="btn btn-sm btn-spinner btn-info" :href="item.resource_url + '/edit'"
        title="{{ trans('brackets/admin-ui::admin.btn.edit') }}" role="button"><i class="fa fa-edit"></i></a>
</div>

{{-- Stages especializados FONAVIS SIEMPRE --}}
<div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 9">
    <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVISSOCIAL'"
        title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
        <i class="fa fa-search"></i>
    </a>
</div>
<div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 11">
    <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVISTECNICO'"
        title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
        <i class="fa fa-search"></i>
    </a>
</div>
<div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 13">
    <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVIS'"
        title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
        <i class="fa fa-search"></i>
    </a>
</div>
<div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 17">
    <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVISADJ'"
        title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
        <i class="fa fa-search"></i>
    </a>
</div>
