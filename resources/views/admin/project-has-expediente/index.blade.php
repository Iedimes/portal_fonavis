@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.project-has-expediente.actions.index'))

@section('body')

    <project-has-expediente-listing
        :data="{{ $data->toJson() }}"
        :url="'{{ url('admin/project-has-expedientes') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.project-has-expediente.actions.index') }}
                        {{-- <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/project-has-expedientes/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.project-has-expediente.actions.create') }}</a> --}}
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <form @submit.prevent="">
                                <div class="row justify-content-md-between">
                                    <div class="col col-lg-7 col-xl-5 form-group">
                                        <div class="input-group">
                                            <input class="form-control" placeholder="{{ trans('brackets/admin-ui::admin.placeholder.search') }}" v-model="search" @keyup.enter="filter('search', $event.target.value)" />
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-primary" @click="filter('search', search)"><i class="fa fa-search"></i>&nbsp; {{ trans('brackets/admin-ui::admin.btn.search') }}</button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-auto form-group ">
                                        <select class="form-control" v-model="pagination.state.per_page">

                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <table class="table table-hover table-listing">
                                <thead>
                                    <tr>
                                        {{-- <th class="bulk-checkbox">
                                            <input class="form-check-input" id="enabled" type="checkbox" v-model="isClickedAll" v-validate="''" data-vv-name="enabled"  name="enabled_fake_element" @click="onBulkItemsClickedAllWithPagination()">
                                            <label class="form-check-label" for="enabled">
                                                #
                                            </label>
                                        </th> --}}

                                        {{-- <th is='sortable' :column="'id'">{{ trans('admin.project-has-expediente.columns.id') }}</th> --}}
                                        <th is='sortable' :column="'project_id'">{{ trans('admin.project-has-expediente.columns.project_id') }}</th>
                                        <th is='sortable' :column="'project_name'">{{ trans('admin.project-has-expediente.columns.project_name') }}</th>
                                        <th is='sortable' :column="'exp'">{{ trans('admin.project-has-expediente.columns.exp') }}</th>
                                        <th is='sortable' :column="'sol'">{{ trans('admin.project-has-expediente.columns.solicitante') }}</th>
                                        <th is='sortable' :column="'con'">{{ trans('admin.project-has-expediente.columns.concepto') }}</th>
                                        <th is='sortable' :column="'fecha'">{{ trans('admin.project-has-expediente.columns.fecha_exp') }}</th>
                                        <th is='sortable' :column="'postulantes'">{{ trans('ACCIONES') }}</th>

                                        <th></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="5">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/project-has-expedientes')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/project-has-expedientes/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
                                            </span>

                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id" :class="bulkItems[item.id] ? 'bg-bulk' : ''">
                                        {{-- <td class="bulk-checkbox">
                                            <input class="form-check-input" :id="'enabled' + item.id" type="checkbox" v-model="bulkItems[item.id]" v-validate="''" :data-vv-name="'enabled' + item.id"  :name="'enabled' + item.id + '_fake_element'" @click="onBulkItemClicked(item.id)" :disabled="bulkCheckingAllLoader">
                                            <label class="form-check-label" :for="'enabled' + item.id">
                                            </label>
                                        </td> --}}

                                  {{-- <td>@{{ item.id }}</td> --}}
                                        <td>@{{ item.project_id }}</td>
                                        <td>@{{ item.proyecto ? item.proyecto.name : '' }}</td>
                                        <td>@{{ item.exp }}</td>
                                        <td>@{{ item.expediente ? item.expediente.NroExpsol : '' }}</td>
                                        <td>@{{ item.expediente ? item.expediente.NroExpCon : '' }}</td>
                                        <td>@{{ item.expediente.NroExpFch | date("DD/MM/Y") }}</td>





                                        <td>
                                            <div class="row no-gutters">
                                                <div class="col-auto">
                                                    {{-- <a class="btn btn-sm btn-spinner btn-info" :href="item.resource_url + '/edit'" title="{{ trans('brackets/admin-ui::admin.btn.edit') }}" role="button"><i class="fa fa-edit"></i></a> --}}
                                                </div>
                                                @if (Auth::user()->rol_app->dependency_id!==8)
                                                    <form class="col" @submit.prevent="deleteItem(item.resource_url)">
                                                        <button type="submit" class="btn btn-sm btn-danger" title="{{ trans('brackets/admin-ui::admin.btn.delete') }}"><i class="fa fa-trash-o"></i></button>
                                                    </form>
                                                @endif
                                                <a class="btn btn-sm btn-primary" :href="'projects/' + item.project_id + '/project'" title="{{ trans('VER POSTULANTES') }}" role="button">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="row" v-if="pagination.state.total > 0">
                                <div class="col-sm">
                                    <span class="pagination-caption">{{ trans('brackets/admin-ui::admin.pagination.overview') }}</span>
                                </div>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div>

                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                                {{-- <a class="btn btn-primary btn-spinner" href="{{ url('admin/project-has-expedientes/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.project-has-expediente.actions.create') }}</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </project-has-expediente-listing>

@endsection
