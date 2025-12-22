{{-- DEPENDENCY 2 --}}
<div class="p-1" v-if="item.get_estado && [2,4,6].includes(item.get_estado.stage_id)">
    <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showDGJN'"
        title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
        <i class="fa fa-search"></i>
    </a>
</div>
<div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 5">
    <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showDGJNFALTANTE'"
        title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
        <i class="fa fa-search"></i>
    </a>
</div>
