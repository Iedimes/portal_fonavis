@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.reporte.actions.create'))

@section('body')

    <div class="container-xl">

                <div class="card">
        
        <reporte-form
            :action="'{{ url('admin/reportes') }}'"
            v-cloak
            inline-template
            >

            <!-- <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action" novalidate> -->
            <form class="form-horizontal form-create" action="resultados">
                
                <div class="card-header">
                    <!-- <i class="fa fa-plus"></i> {{ trans('admin.reporte.actions.create') }} -->
                    <center><h4>REPORTE DE PROYECTOS</h4></center>
                </div>

                <div class="card-body">
                    <!-- @include('admin.reporte.components.form-elements') -->
                    <div class="form-group row align-items-center">
                                <label for="inicio" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.reporte.columns.inicio') }}</label>
                                <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
                                    <div class="input-group input-group--custom">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <datetime v-model="form.inicio" :config="datetimePickerConfig"  class="flatpickr" id="inicio" name="inicio" class="@error('inicio') is-invalid @enderror"></datetime>
                                    </div>
                                    @error('inicio')
                                    <div class="input-group input-group--custom" style="color: red">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="fin" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.reporte.columns.fin') }}</label>
                                <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
                                    <div class="input-group input-group--custom">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <datetime v-model="form.fin" :config="datetimePickerConfig" class="flatpickr" id="fin" name="fin" class="@error('fin') is-invalid @enderror"></datetime>
                                    </div>
                                    @error('fin')
                                    <div class="input-group input-group--custom" style="color: red">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- <div v-if="!form.inicio && !form.fin"  class="form-group row align-items-center" :class="{'has-danger': errors.has('proyecto_id'), 'has-success': fields.proyecto_id && fields.proyecto_id.valid }"> -->
                            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('proyecto_id'), 'has-success': fields.proyecto_id && fields.proyecto_id.valid }">
                                <label for="proyecto_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.reporte.columns.proyecto_id') }}</label>
                                    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
                                    <!-- <input type="text" v-model="form.sat_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('sat_id'), 'form-control-success': fields.sat_id && fields.sat_id.valid}" id="sat_id" name="sat_id" placeholder="{{ trans('admin.reporte.columns.sat_id') }}"> -->
                                    <select name="proyecto_id" id="proyecto_id" v-model="form.proyecto_id" class="form-control" class="@error('proyecto_id') is-invalid @enderror">
                                        <option value="0">TODOS</option>
                                        @foreach($proyecto as $proyecto)
                                            <option value="{{ $proyecto['id']}}">{{ $proyecto->id }} - {{ $proyecto->name }}</option>
                                        @endforeach
                                    </select>
                                    <!-- <div v-if="errors.has('proyecto_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('proyecto_id') }}</div> -->
                                    @error('proyecto_id')
                                    <div class="input-group input-group--custom" style="color: red">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>



                            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('sat_id'), 'has-success': fields.sat_id && fields.sat_id.valid }">
                                <label for="sat_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.reporte.columns.sat_id') }}</label>
                                    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
                                    <!-- <input type="text" v-model="form.sat_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('sat_id'), 'form-control-success': fields.sat_id && fields.sat_id.valid}" id="sat_id" name="sat_id" placeholder="{{ trans('admin.reporte.columns.sat_id') }}"> -->
                                    <select name="sat_id" id="sat_id" v-model="form.sat_id" class="form-control" class="@error('sat_id') is-invalid @enderror">
                                        <option value="0">TODOS</option>
                                        @foreach($sat as $sat)
                                            <option value="{{ $sat['NucCod']}}">{{ $sat->NucCod }} - {{ $sat->NucNomSat }}</option>
                                        @endforeach
                                    </select>
                                    <!-- <div v-if="errors.has('sat_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('sat_id') }}</div> -->
                                    @error('sat_id')
                                    <div class="input-group input-group--custom" style="color: red">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('state_id'), 'has-success': fields.state_id && fields.state_id.valid }">
                                <label for="state_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.reporte.columns.state_id') }}</label>
                                <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
                                    <select name="state_id" id="state_id" v-model="form.state_id" class="form-control" :class="{'is-invalid': errors.has('state_id') }">
                                        <option value="0">TODOS</option>
                                        @foreach($departamento as $departamento)
                                            <option value="{{ $departamento['DptoId'] }}">{{ $departamento->DptoNom }}</option>
                                        @endforeach
                                    </select>
                                    @error('state_id')
                                    <div class="input-group input-group--custom" style="color: red">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('city_id'), 'has-success': fields.city_id && fields.city_id.valid }">
                                <label for="city_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.reporte.columns.city_id') }}</label>
                                <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
                                    <select name="city_id" id="city_id" v-model="form.city_id" class="form-control" :class="{'is-invalid': errors.has('city_id') }">
                                        <option value="0">TODOS</option>
                                        <option v-for="city in cities" :key="city.CiuId" :value="city.CiuId">@{{ city.CiuNom }}</option>
                                    </select>
                                    @error('city_id')
                                    <div class="input-group input-group--custom" style="color: red">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('modalidad_id'), 'has-success': fields.modalidad_id && fields.modalidad_id.valid }">
                                <label for="modalidad_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.reporte.columns.modalidad_id') }}</label>
                                    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
                                    <!-- <input type="text" v-model="form.modalidad_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('modalidad_id'), 'form-control-success': fields.modalidad_id && fields.modalidad_id.valid}" id="modalidad_id" name="modalidad_id" placeholder="{{ trans('admin.reporte.columns.modalidad_id') }}"> -->
                                    <select name="modalidad_id" id="modalidad_id" v-model="form.modalidad_id" class="form-control" class="@error('modalidad_id') is-invalid @enderror">
                                            <option value="0">TODOS</option>
                                            @foreach($modalidad as $modalidad)
                                                <option value="{{ $modalidad['id']}}">{{ $modalidad->name}}</option>
                                            @endforeach
                                        </select>
                                    <!-- <div v-if="errors.has('modalidad_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('modalidad_id') }}</div> -->
                                    @error('modalidad_id')
                                    <div class="input-group input-group--custom" style="color: red">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('stage_id'), 'has-success': fields.stage_id && fields.stage_id.valid }">
                                <label for="stage_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.reporte.columns.stage_id') }}</label>
                                    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
                                    <!-- <input type="text" v-model="form.stage_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('stage_id'), 'form-control-success': fields.stage_id && fields.stage_id.valid}" id="stage_id" name="stage_id" placeholder="{{ trans('admin.reporte.columns.stage_id') }}"> -->
                                    <select name="stage_id" id="stage_id" v-model="form.stage_id" class="form-control" class="@error('stage_id') is-invalid @enderror">
                                            <option value="0">TODOS</option>
                                            @foreach($estado as $estado)
                                                <option value="{{ $estado['id']}}">{{ $estado->name}}</option>
                                            @endforeach
                                        </select>
                                    <!-- <div v-if="errors.has('stage_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('stage_id') }}</div> -->
                                    @error('stage_id')
                                    <div class="input-group input-group--custom" style="color: red">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                </div>
                                
                <div class="card-footer">
                <button type="submit" class="btn btn-primary" :disabled="!isValidDateRange()">
                    <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-eye'"></i>
                    {{ trans('VER RESULTADOS') }}
                </button>
                </div>
                
            </form>

        </reporte-form>

        </div>

        </div>

    
@endsection