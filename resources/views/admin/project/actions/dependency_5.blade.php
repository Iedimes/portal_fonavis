{{-- DEPENDENCY 5 --}}
<div class="p-1"
    v-if="item.get_estado && item.get_estado.stage_id === 16 || item.get_estado && item.get_estado.stage_id === 11">
    <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showDSGO'"
        title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
        <i class="fa fa-search"></i>
    </a>
</div>
