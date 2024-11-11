@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.postulante.actions.index'))

@section('body')

    <postulante-listing
        :data="{{ $data->toJson() }}"
        :url="'{{ url('admin/postulantes') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.postulante.actions.index') }}
                        <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/postulantes/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.postulante.actions.create') }}</a>
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

                                        {{-- <th is='sortable' :column="'id'">{{ trans('admin.postulante.columns.id') }}</th> --}}
                                        <th is='sortable' :column="'cedula'">{{ trans('admin.postulante.columns.cedula') }}</th>
                                        <th is='sortable' :column="'first_name'">{{ trans('admin.postulante.columns.first_name') }}</th>
                                        <th is='sortable' :column="'last_name'">{{ trans('admin.postulante.columns.last_name') }}</th>
                                        <th is='sortable' :column="'project'">{{ trans('Proyecto_id') }}</th>
                                        <th is='sortable' :column="'projectd'">{{ trans('Proyecto') }}</th>
                                        <th is='sortable' :column="'sat_cod'">{{ trans('Codigo SAT') }}</th>
                                        <th is='sortable' :column="'sat'">{{ trans('SAT') }}</th>
                                        {{-- <th is='sortable' :column="'marital_status'">{{ trans('admin.postulante.columns.marital_status') }}</th> --}}
                                        {{-- <th is='sortable' :column="'nacionalidad'">{{ trans('admin.postulante.columns.nacionalidad') }}</th> --}}
                                        {{-- <th is='sortable' :column="'gender'">{{ trans('admin.postulante.columns.gender') }}</th> --}}
                                        {{-- <th is='sortable' :column="'birthdate'">{{ trans('admin.postulante.columns.birthdate') }}</th> --}}
                                        {{-- <th is='sortable' :column="'localidad'">{{ trans('admin.postulante.columns.localidad') }}</th>
                                        <th is='sortable' :column="'asentamiento'">{{ trans('admin.postulante.columns.asentamiento') }}</th> --}}
                                        {{-- <th is='sortable' :column="'ingreso'">{{ trans('admin.postulante.columns.ingreso') }}</th> --}}
                                        {{-- <th is='sortable' :column="'address'">{{ trans('admin.postulante.columns.address') }}</th>
                                        <th is='sortable' :column="'grupo'">{{ trans('admin.postulante.columns.grupo') }}</th>
                                        <th is='sortable' :column="'phone'">{{ trans('admin.postulante.columns.phone') }}</th>
                                        <th is='sortable' :column="'mobile'">{{ trans('admin.postulante.columns.mobile') }}</th>
                                        <th is='sortable' :column="'nexp'">{{ trans('admin.postulante.columns.nexp') }}</th> --}}

                                        <th></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="18">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/postulantes')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/postulantes/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
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
                                        <td>@{{ item.cedula }}</td>
                                        <td>@{{ item.first_name }}</td>
                                        <td>@{{ item.last_name }}</td>
                                        <td>@{{ item.get_project_has_postulante?.project_id ?? 'N/A' }}</td>
                                        <td>@{{ item.get_project_has_postulante?.project?.name ?? 'N/A' }}</td>
                                        <td>@{{ item.get_project_has_postulante?.project?.get_sat?.NucCod ?? 'N/A' }}</td>
                                        <td>@{{ item.get_project_has_postulante?.project?.get_sat?.NucNomSat ?? 'N/A' }}</td>

                                        {{-- <td>@{{ item.marital_status }}</td>
                                        <td>@{{ item.nacionalidad }}</td>
                                        <td>@{{ item.gender }}</td>
                                        <td>@{{ item.birthdate }}</td> --}}
                                        {{-- <td>@{{ item.localidad }}</td>
                                        <td>@{{ item.asentamiento }}</td> --}}
                                        {{-- <td>@{{ item.ingreso }}</td> --}}
                                        {{-- <td>@{{ item.address }}</td>
                                        <td>@{{ item.grupo }}</td>
                                        <td>@{{ item.phone }}</td>
                                        <td>@{{ item.mobile }}</td>
                                        <td>@{{ item.nexp }}</td> --}}

                                        <td>
                                            <div class="row no-gutters">
                                                <div class="col-auto">
                                                    {{-- <a class="btn btn-sm btn-spinner btn-info" :href="item.resource_url + '/edit'" title="{{ trans('brackets/admin-ui::admin.btn.edit') }}" role="button"><i class="fa fa-edit"></i></a> --}}
                                                </div>

                                                    <div class="col-auto">
                                                         {{-- <a class="btn btn-sm btn-spinner btn-warning" :href="item.resource_url + '/comentario'" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button"><i class="fa fa-search"></i></a> --}}
                                                         <a class="btn btn-sm btn-danger"
                                                         :href="'/admin/comentarios/' + item.id + '/create/' + encodeURIComponent(item.cedula)"
                                                         title="{{ trans('ELIMINAR POSTULANTE') }}"
                                                         role="button">
                                                          <i class="fa fa-trash-o"></i>
                                                      </a>
                                                    </div>

                                                <form class="col" @submit.prevent="deleteItem(item.resource_url)">
                                                    {{-- <button type="submit" class="btn btn-sm btn-danger" title="{{ trans('brackets/admin-ui::admin.btn.delete') }}"><i class="fa fa-trash-o"></i></button> --}}
                                                </form>
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
                                <a class="btn btn-primary btn-spinner" href="{{ url('admin/postulantes/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.postulante.actions.create') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </postulante-listing>

@endsection
