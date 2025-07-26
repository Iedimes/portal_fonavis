@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.project.actions.index'))

@section('body')

    <project-listing
        :data="{{ $data->toJson() }}"
        :url="'{{ url('admin/projects') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.project.actions.index') }}
                        <p style="text-align: right">{{ trans('DEPENDENCIA') }} - {{$dependencia->name}}</p>
                        {{--<a class="btn btn-primary  btn-sm pull-right m-b-0" href="{{ url('admin/projects/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.project.actions.create') }}</a>--}}
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
                                        {{--<th class="bulk-checkbox">
                                            <input class="form-check-input" id="enabled" type="checkbox" v-model="isClickedAll" v-validate="''" data-vv-name="enabled"  name="enabled_fake_element" @click="onBulkItemsClickedAllWithPagination()">
                                            <label class="form-check-label" for="enabled">
                                                #
                                            </label>
                                        </th>--}}

                                        <th is='sortable' :column="'id'">{{ trans('admin.project.columns.id') }}</th>
                                        <th is='sortable' :column="'name'">{{ trans('admin.project.columns.name') }}</th>
                                        <th is='sortable' :column="'phone'">{{ trans('admin.project.columns.phone') }}</th>
                                        <th is='sortable' :column="'sat_id'">{{ trans('admin.project.columns.sat_id') }}</th>
                                        <th is='sortable' :column="'sat_nombre'">{{ trans('admin.project.columns.sat_nombre') }}</th>
                                        <th is='sortable' :column="'state_id'">{{ trans('admin.project.columns.state_id') }}</th>
                                        <th is='sortable' :column="'city_id'">{{ trans('admin.project.columns.city_id') }}</th>
                                        <th is='sortable' :column="'modalidad_id'">{{ trans('admin.project.columns.modalidad_id') }}</th>
                                        <th is='sortable' :column="'leader_name'">{{ trans('admin.project.columns.leader_name') }}</th>
                                        <th is='sortable' :column="'localidad'">{{ trans('admin.project.columns.localidad') }}</th>
                                        <th is='sortable' :column="'estado'">{{ trans('Estado') }}</th>
                                        {{-- <th is='sortable' :column="'rol'">{{ trans('Rol') }}</th> --}}


                                        <th></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="16">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/projects')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/projects/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
                                            </span>

                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id" :class="bulkItems[item.id] ? 'bg-bulk' : ''">
                                        {{--<td class="bulk-checkbox">
                                            <input class="form-check-input" :id="'enabled' + item.id" type="checkbox" v-model="bulkItems[item.id]" v-validate="''" :data-vv-name="'enabled' + item.id"  :name="'enabled' + item.id + '_fake_element'" @click="onBulkItemClicked(item.id)" :disabled="bulkCheckingAllLoader">
                                            <label class="form-check-label" :for="'enabled' + item.id">
                                            </label>
                                        </td>--}}

                                    <td>@{{ item.id }}</td>
                                        <td>@{{ item.name }}</td>
                                        <td>@{{ item.phone }}</td>
                                        <td>@{{ item.sat_id }}</td>
                                        <td>@{{ item.get_sat.NucNomSat }}</td>
                                        <td>@{{ item.get_state.DptoNom }}</td>
                                        <td>@{{ item.get_city.CiuNom }}</td>
                                        <td>@{{ item.get_modality.name }}</td>
                                        <td>@{{ item.leader_name }}</td>
                                        <td>@{{ item.localidad }}</td>
                                        {{-- <td>@{{ item.get_estado ? item.get_estado.stage_id : '' }}</td> --}}
                                        {{-- <td>
                                            <span v-bind:class="{
                                                'btn btn-success': item.get_estado && item.get_estado.stage_id === 1,
                                                'btn btn-warning': item.get_estado && item.get_estado.stage_id === 2,
                                                'btn btn-danger': item.get_estado && item.get_estado.stage_id === 3,
                                                'btn btn-success': item.get_estado && item.get_estado.stage_id === 4,
                                                'btn btn-warning': item.get_estado && item.get_estado.stage_id === 5,
                                                'btn btn-danger': item.get_estado && item.get_estado.stage_id === 6,
                                                'text-light': item.get_estado && (item.get_estado.stage_id === 1 || item.get_estado.stage_id === 6)
                                            }">
                                                @{{ item.get_estado && item.get_estado.stage_id === 1 ? 'ENVIADO' : '' }}
                                                @{{ item.get_estado && item.get_estado.stage_id === 2 ? 'REVISION PRELIMINAR' : '' }}
                                                @{{ item.get_estado && item.get_estado.stage_id === 3 ? 'APROBADO DGJN' : '' }}
                                                @{{ item.get_estado && item.get_estado.stage_id === 4 ? 'ARCHIVADO DGJN' : '' }}
                                                @{{ item.get_estado && item.get_estado.stage_id === 5 ? 'CON DOCUMENTACION DGJN' : '' }}
                                                @{{ item.get_estado && item.get_estado.stage_id === 6 ? 'RECHAZADO DGJN' : '' }}
                                                @{{ item.get_estado && item.get_estado.stage_id === 7 ? 'EVALUACION SOCIAL' : '' }}
                                                @{{ item.get_estado && item.get_estado.stage_id === 8 ? 'ENVIAR GRUPO FAMILIAR' : '' }}
                                            </span>
                                        </td> --}}

                                        <td>
                                            <span>
                                                <button v-if="item.get_estado && item.get_estado.stage_id === 1"
                                                        class="btn"
                                                        style="background-color: green; color: white;">
                                                    ENVIADO
                                                </button>
                                                <button v-else-if="item.get_estado && item.get_estado.stage_id === 2"
                                                        class="btn"
                                                        style="background-color: orange; color: white;">
                                                    REVISION PRELIMINAR
                                                </button>
                                                <button v-else-if="item.get_estado && item.get_estado.stage_id === 3"
                                                        class="btn"
                                                        style="background-color: rgb(3, 78, 20); color: white;">
                                                    APROBADO DGJN
                                                </button>
                                                <button v-else-if="item.get_estado && item.get_estado.stage_id === 4"
                                                        class="btn"
                                                        style="background-color: rgb(169, 197, 7); color: white;">
                                                    OBSERVADO DGJN
                                                </button>
                                                <button v-else-if="item.get_estado && item.get_estado.stage_id === 5"
                                                        class="btn"
                                                        style="background-color: rgb(68, 128, 123); color: white;">
                                                    CON DOCUMENTACION DGJN
                                                </button>
                                                <button v-else-if="item.get_estado && item.get_estado.stage_id === 6"
                                                        class="btn"
                                                        style="background-color: red; color: white;">
                                                    RECHAZADO DGJN
                                                </button>
                                                <button v-else-if="item.get_estado && item.get_estado.stage_id === 7"
                                                        class="btn"
                                                        style="background-color: blue; color: white;">
                                                    EVALUACION SOCIAL
                                                </button>
                                                <button v-else-if="item.get_estado && item.get_estado.stage_id === 8"
                                                        class="btn"
                                                        style="background-color: purple; color: white;">
                                                    GRUPO FAMILIAR ENVIADO
                                                </button>
                                                <button v-else-if="item.get_estado && item.get_estado.stage_id === 9"
                                                        class="btn"
                                                        style="background-color: rgb(9, 170, 170); color: white;">
                                                    CON DICTAMEN SOCIAL
                                                </button>
                                                <button v-else-if="item.get_estado && item.get_estado.stage_id === 10"
                                                        class="btn"
                                                        style="background-color: rgb(214, 101, 8); color: white;">
                                                    EVALUACION TECNICA
                                                </button>
                                                <button v-else-if="item.get_estado && item.get_estado.stage_id === 11"
                                                        class="btn"
                                                        style="background-color: rgb(83, 5, 5); color: white;">
                                                    DOCUMENTACION TECNICA ENVIADA
                                                </button>
                                                <button v-else-if="item.get_estado && item.get_estado.stage_id === 12"
                                                        class="btn"
                                                        style="background-color: rgb(5, 65, 83); color: white;">
                                                    VERIFICACION TECNICO AMBIENTAL
                                                </button>
                                                <button v-else-if="item.get_estado && item.get_estado.stage_id === 13"
                                                        class="btn"
                                                        style="background-color: rgb(1, 5, 2); color: white;">
                                                    CON INFORME VTA
                                                </button>

                                                <button v-else-if="item.get_estado && item.get_estado.stage_id === 14"
                                                        class="btn"
                                                        style="background-color: rgb(240, 216, 5); color: white;">
                                                    OBSERVACION DIGH
                                                </button>

                                                <button v-else-if="item.get_estado && item.get_estado.stage_id === 15"
                                                        class="btn"
                                                        style="background-color: rgb(250, 15, 15); color: white;">
                                                    RECHAZADO DIGH
                                                </button>

                                                <button v-else-if="item.get_estado && item.get_estado.stage_id === 16"
                                                        class="btn"
                                                        style="background-color: rgb(160, 51, 8); color: white;">
                                                    EVALUACION TECNICO HABITACIONAL
                                                </button>

                                                <button v-else-if="item.get_estado && item.get_estado.stage_id === 17"
                                                        class="btn"
                                                        style="background-color: rgb(8, 160, 21); color: white;">
                                                    CON CALIFICACION TECNICA HABITACIONAL
                                                </button>

                                                <button v-else-if="item.get_estado && item.get_estado.stage_id === 18"
                                                        class="btn"
                                                        style="background-color: rgb(27, 92, 145); color: white;">
                                                    ADJUDICADO
                                                </button>
                                            </span>
                                        </td>


                                        <td>
    <div class="d-flex">

        {{-- DEPENDENCY 1 --}}
        @if (Auth::user()->rol_app->dependency_id == 1)


             {{-- No estado --}}
            <div class="p-1" v-if="!item.get_estado">
                <a class="btn btn-sm btn-danger" :href="'/admin/motivos/' + item.id + '/create/'" title="{{ trans('ELIMINAR PROYECTO') }}" role="button">
                    <i class="fa fa-trash-o"></i>
                </a>
            </div>

            {{-- Imprimir postulantes --}}
            <div class="p-1">
                <a class="btn btn-sm btn-primary" :href="item.resource_url + '/project'" title="{{ trans('IMPRIMIR POSTULANTES') }}" role="button">
                    <i class="fa fa-print"></i>
                </a>
            </div>

            {{-- Stages FONAVIS --}}
            <div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 1">
                <a class="btn btn-sm btn-warning" :href="item.resource_url + '/show'" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                    <i class="fa fa-search"></i>
                </a>
            </div>

           <div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 2">
                <a class="btn btn-sm btn-success" :href="item.resource_url + '/showVERDOCFONAVIS'" title="{{ trans('brackets/admin-ui::admin.btn.showverdocfonavis') }}" role="button">
                    <i class="fa fa-search"></i>
                </a>
            </div>


            <div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 3">
                <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVIS'" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                    <i class="fa fa-search"></i>
                </a>
            </div>
            <div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 4">
                <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVIS'" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                    <i class="fa fa-search"></i>
                </a>
            </div>
            <div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 6">
                <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVIS'" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                    <i class="fa fa-search"></i>
                </a>
            </div>
            <div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 8">
                <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVIS'" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                    <i class="fa fa-search"></i>
                </a>
            </div>

            {{-- Stages especializados FONAVIS SIEMPRE--}}
            <div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 9">
                <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVISSOCIAL'" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                    <i class="fa fa-search"></i>
                </a>
            </div>
            <div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 11">
                <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVISTECNICO'" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                    <i class="fa fa-search"></i>
                </a>
            </div>
            <div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 13">
                <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVIS'" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                    <i class="fa fa-search"></i>
                </a>
            </div>
            <div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 17">
                <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showFONAVISADJ'" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                    <i class="fa fa-search"></i>
                </a>
            </div>
        @endif

        {{-- DEPENDENCY 2 --}}
        @if (Auth::user()->rol_app->dependency_id == 2)
            {{-- <div class="p-1" v-if="item.get_estado && [2,4,6].includes(item.get_estado.stage_id)"> SACO EL 4 DUDO SI OBS DGJN PUEDEN MODIFICAR, CREO QUE DEPENDE QUE SAT ENVIE DOCUMENTOS SOLICITADOS--}}
                <div class="p-1" v-if="item.get_estado && [2,6].includes(item.get_estado.stage_id)">
                <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showDGJN'" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                    <i class="fa fa-search"></i>
                </a>
            </div>
            <div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 5">
                <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showDGJNFALTANTE'" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                    <i class="fa fa-search"></i>
                </a>
            </div>
        @endif

        {{-- DEPENDENCY 3 --}}
        @if (Auth::user()->rol_app->dependency_id == 3)
            <div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 8">
                <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showDGSO'" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                    <i class="fa fa-search"></i>
                </a>
            </div>

             {{-- Imprimir postulantes --}}
            <div class="p-1">
                <a class="btn btn-sm btn-primary" :href="item.resource_url + '/project'" title="{{ trans('IMPRIMIR POSTULANTES') }}" role="button">
                    <i class="fa fa-print"></i>
                </a>
            </div>
        @endif

        {{-- DEPENDENCY 4 --}}
        @if (Auth::user()->rol_app->dependency_id == 4)
            <div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 11">
                <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showDIGH'" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                    <i class="fa fa-search"></i>
                </a>
            </div>
        @endif

        {{-- DEPENDENCY 5 --}}
        @if (Auth::user()->rol_app->dependency_id == 5)
            <div class="p-1" v-if="item.get_estado && item.get_estado.stage_id === 16">
                <a class="btn btn-sm btn-warning" :href="item.resource_url + '/showDSGO'" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                    <i class="fa fa-search"></i>
                </a>
            </div>
        @endif

        {{-- Historial (Común a todos) --}}
        <div class="p-1">
            <a class="btn btn-sm" style="background-color: #ec600e; color: #b5bbbb;" :href="item.resource_url + '/historial'" title="VER HISTORIAL" role="button">
                <i class="fa fa-history"></i>
            </a>
        </div>

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
                                <a class="btn btn-primary " href="{{ url('admin/projects/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.project.actions.create') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </project-listing>

@endsection
