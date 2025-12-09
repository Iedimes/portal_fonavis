@extends('brackets/admin-ui::admin.layout.default')

@section('title', 'Descarga Masiva de Legajos')

@section('body')
    <div class="container-xl">
        <div class="card">

            {{-- Componente Vue --}}
            <legajo-masivo-form :action="'{{ url('admin/projects/legajo-masivo/generar') }}'" v-cloak inline-template>

                <form class="form-horizontal form-create" @submit.prevent="onSubmit">

                    <div class="card-header text-center">
                        <h4>DESCARGA MASIVA DE LEGAJOS</h4>
                    </div>

                    <div class="card-body">

                        {{-- Fecha inicio --}}
                        <div class="form-group row align-items-center">
                            <label for="inicio" class="col-form-label text-md-right col-md-2">Fecha inicio</label>
                            <div class="col-md-9 col-xl-8">
                                <div class="input-group input-group--custom">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <datetime v-model="form.inicio" :config="datetimePickerConfig" class="flatpickr"
                                        id="inicio" name="inicio"></datetime>
                                </div>
                            </div>
                        </div>

                        {{-- Fecha fin --}}
                        <div class="form-group row align-items-center">
                            <label for="fin" class="col-form-label text-md-right col-md-2">Fecha fin</label>
                            <div class="col-md-9 col-xl-8">
                                <div class="input-group input-group--custom">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <datetime v-model="form.fin" :config="datetimePickerConfig" class="flatpickr"
                                        id="fin" name="fin"></datetime>
                                </div>
                            </div>
                        </div>

                        {{-- Proyecto --}}
                        <div class="form-group row align-items-center">
                            <label for="proyecto_id" class="col-form-label text-md-right col-md-2">Proyecto</label>
                            <div class="col-md-9 col-xl-8">
                                <select name="proyecto_id" v-model="form.proyecto_id" class="form-control">
                                    <option value="0">TODOS</option>
                                    @foreach ($proyecto as $p)
                                        <option value="{{ $p->id }}">{{ $p->id }} - {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- SAT --}}
                        <div class="form-group row align-items-center">
                            <label for="sat_id" class="col-form-label text-md-right col-md-2">SAT</label>
                            <div class="col-md-9 col-xl-8">
                                <select name="sat_id" v-model="form.sat_id" class="form-control">
                                    <option value="0">TODOS</option>
                                    @foreach ($sats as $s)
                                        <option value="{{ $s->NucCod }}">{{ $s->NucCod }} - {{ $s->NucNomSat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Departamento --}}
                        <div class="form-group row align-items-center">
                            <label for="state_id" class="col-form-label text-md-right col-md-2">Departamento</label>
                            <div class="col-md-9 col-xl-8">
                                <select name="state_id" v-model="form.state_id" class="form-control">
                                    <option value="0">TODOS</option>
                                    @foreach ($states as $d)
                                        <option value="{{ $d->DptoId }}">{{ $d->DptoNom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Distrito dependiente --}}
                        <div class="form-group row align-items-center">
                            <label for="city_id" class="col-form-label text-md-right col-md-2">Distrito</label>
                            <div class="col-md-9 col-xl-8">
                                <select name="city_id" v-model="form.city_id" class="form-control">
                                    <option value="0">TODOS</option>
                                    <option v-for="city in cities" :key="city.CiuId" :value="city.CiuId">
                                        @{{ city.CiuNom }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- Modalidad --}}
                        <div class="form-group row align-items-center">
                            <label for="modalidad_id" class="col-form-label text-md-right col-md-2">Modalidad</label>
                            <div class="col-md-9 col-xl-8">
                                <select name="modalidad_id" v-model="form.modalidad_id" class="form-control">
                                    <option value="0">TODOS</option>
                                    @foreach ($modalities as $m)
                                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Estado --}}
                        <div class="form-group row align-items-center">
                            <label for="stage_id" class="col-form-label text-md-right col-md-2">Estado</label>
                            <div class="col-md-9 col-xl-8">
                                <select name="stage_id" v-model="form.stage_id" class="form-control">
                                    <option value="0">TODOS</option>
                                    @foreach ($estado as $e)
                                        <option value="{{ $e->id }}">{{ $e->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner fa-spin' : 'fa-download'"></i>
                            GENERAR ZIP
                        </button>
                    </div>

                </form>

            </legajo-masivo-form>

        </div>
    </div>
@endsection
