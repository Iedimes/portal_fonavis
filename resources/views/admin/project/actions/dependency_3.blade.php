{{-- DEPENDENCY 3 --}}
<div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 8">
    <template v-if="item.calificacion_finalizada && item.shd_migrated">
        <span class="badge badge-success p-2" style="font-size: 0.85em; font-weight: bold; color: white;">
            <i class="fa fa-check-circle"></i> CALIFICADO
        </span>
    </template>
    <template v-else>
        <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showDGSO'"
            title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
            <i class="fa fa-search"></i>
        </a>
    </template>
</div>

{{-- Imprimir postulantes --}}
<div class="p-1">
    <a class="btn btn-sm btn-primary" :href="item.resource_url + '/project'" title="{{ trans('IMPRIMIR POSTULANTES') }}"
        role="button">
        <i class="fa fa-print"></i>
    </a>
</div>
