@extends('adminlte::page')

@section('title', 'FONAVIS')

@section('content_header')
<h1>{{ $title }}</h1>
@stop

@section('content')
<div class="invoice p-3 mb-3">
<div class="box box-primary">
    <form role="form" action="{{url('projects/'.$project['id']) }}" method="post">
            @csrf
            {{ method_field('PUT') }}
            <div class="box-body">
                <div class="row">
                      <div class="col-md-6">
                          <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                              <label>Nombre del Proyecto</label>
                              <input type="text" required class="form-control" name="name" value="{{ old('name',isset($project['name'])?utf8_decode($project['name']):'') }}"  placeholder="Ingrese Nombre del Proyecto">
                              {!! $errors->first('name','<span class="help-block">:message</span>') !!}
                          </div>
                      </div>

                      <div class="col-md-6">
                          <div class="form-group {{ $errors->has('sat_id') ? 'has-error' : '' }}">
                              <label>SAT</label>
                              <input type="hidden" name="sat_id" value="{{ $user->sat_ruc }}">
                              <input type="text" class="form-control" value="{{ utf8_encode($user->sat_ruc?$user->getSat->NucNomSat:"") }}" readonly>
                              {!! $errors->first('sat_id','<span class="help-block">:message</span>') !!}
                          </div>

                      </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                      <div class="form-group {{ $errors->has('leader_name') ? 'has-error' : '' }}">
                          <label for="exampleInputPassword1">Nombre del Representante del Grupo</label>
                          <input required type="text" class="form-control" name="leader_name" value="{{ old('leader_name',isset($project['leader_name'])?$project['leader_name']:'') }}" placeholder="Ingrese Nombre del Lider del Grupo">
                          {!! $errors->first('leader_name','<span class="help-block">:message</span>') !!}
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                          <label for="exampleInputPassword1">Telefono</label>
                          <input required type="number" class="form-control" name="phone" value="{{ old('phone',isset($project['phone'])?$project['phone']:'') }}" placeholder="Ingrese Telefono de Contacto">
                          {!! $errors->first('phone','<span class="help-block">:message</span>') !!}
                      </div>
                  </div>

                    <div class="col-md-4">
                      <div class="form-group {{ $errors->has('res_nro') ? 'has-error' : '' }}">
                          <label for="exampleInputPassword1">Resolución</label>
                          <input required type="number" class="form-control" name="res_nro" value="{{ old('res_nro',isset($project['res_nro'])?$project['res_nro']:'') }}" placeholder="Ingrese Nro de Resolucion">
                          {!! $errors->first('res_nro','<span class="help-block">:message</span>') !!}
                      </div>
                  </div>


                        <div class="col-md-4">
                      <!-- Nueva columna para fecha de resolucion -->
                      <div class="form-group {{ $errors->has('fechares') ? 'has-error' : '' }}">
                          <label for="exampleInputPassword1">Fecha de Resolución</label>
                          <input required type="text" class="form-control" name="fechares" value="{{ old('fechares',isset($project['fechares'])?$project['fechares']:'') }}" placeholder="Ingrese fecha de resolución">
                          {!! $errors->first('fechares','<span class="help-block">:message</span>') !!}
                      </div>
                  </div>

                   <div class="col-md-4">
                      <!-- Nueva columna para coordenadax -->
                      <div class="form-group {{ $errors->has('coordenadax') ? 'has-error' : '' }}">
                          <label for="exampleInputPassword1">Coordenada X</label>
                          <input required type="text" class="form-control" name="coordenadax" value="{{ old('coordenadax',isset($project['coordenadax'])?$project['coordenadax']:'') }}" placeholder="Ingrese la coordenada X">
                          {!! $errors->first('coordenadax','<span class="help-block">:message</span>') !!}
                      </div>
                  </div>

                  <div class="col-md-4">
                      <!-- Nueva columna para coordenada Y -->
                      <div class="form-group {{ $errors->has('coordenaday') ? 'has-error' : '' }}">
                          <label for="exampleInputPassword1">Coordenada Y</label>
                          <input required type="text" class="form-control" name="coordenaday" value="{{ old('coordenaday',isset($project['coordenaday'])?$project['coordenaday']:'') }}" placeholder="Ingrese la Coordenada Y">
                          {!! $errors->first('coordenaday','<span class="help-block">:message</span>') !!}
                      </div>
                  </div>
                  {{--<div class="col-md-4">
                      <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                          <label for="exampleInputPassword1">Cantidad de Viviendas</label>
                          <input required type="number" class="form-control" name="households" value="{{ old('households',isset($project['households'])?$project['households']:'') }}" placeholder="Ingrese Cantidad de Viviendas del Proyecto">
                          {!! $errors->first('households','<span class="help-block">:message</span>') !!}
                      </div>
                  </div>--}}

                </div>
                <div class="row">
                    <div class="col-md-4">
                      <div class="form-group {{ $errors->has('modalidad_id') ? 'has-error' : '' }}">
                          <label for="exampleInputPassword1">Modalidad</label>
                          <select class="form-control required" name="modalidad_id">
                              <option value="">Selecciona la Modalidad</option>
                                  @foreach($modalidad as $key=>$mod)
                                      <option value="{{$mod->id}}"
                                          {{ old('modalidad_id',isset($project['modalidad_id'])?$project['modalidad_id']:'') == $mod->id ? 'selected' : '' }}
                                          >{{ $mod->name }}</option>
                                  @endforeach
                          </select>
                          {!! $errors->first('modalidad_id','<span class="help-block">:message</span>') !!}
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group {{ $errors->has('land_id') ? 'has-error' : '' }}">
                          <label for="exampleInputPassword1">Tipo Terreno</label>
                          <select class="form-control required" name="land_id">
                              <option value="">Selecciona el Tipo de Terreno</option>
                              @if(isset($lands))
                                  @foreach($tierra as $key=>$name)
                                      <option value="{{$name->id}}"
                                          {{ old('land_id',isset($project['land_id'])?$project['land_id']:'') == $name->id ? 'selected' : '' }}
                                          >{{ $name->name }}</option>
                                  @endforeach
                              @endif
                          </select>
                          {!! $errors->first('land_id','<span class="help-block">:message</span>') !!}
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group {{ $errors->has('land_id') ? 'has-error' : '' }}">
                          <label for="exampleInputPassword1">Tipologia</label>
                          <select class="form-control required" name="typology_id">
                              <option value="">Selecciona la Tipologia</option>
                              @if(isset($typology))
                                  @foreach($tipologias as $key=>$tipo)
                                      <option value="{{$tipo->id}}"
                                          {{ old('typology_id',isset($project['typology_id'])?$project['typology_id']:'') == $tipo->id ? 'selected' : '' }}
                                          >{{ $tipo->name }}</option>
                                  @endforeach
                              @endif
                          </select>
                          {!! $errors->first('typology_id','<span class="help-block">:message</span>') !!}
                      </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group {{ $errors->has('state_id') ? 'has-error' : '' }}">
                      <label for="exampleInputPassword1">Departamento</label>
                      <select class="form-control required" name="state_id" id="state_id" required>
                        <option value="">Selecciona el Departamento</option>
                        @foreach($departamentos as $key=>$dpto)
                          <option value="{{$dpto->DptoId}}"
                            {{ old('state_id',isset($project['state_id'])?$project['state_id']:'') == $dpto->DptoId ? 'selected' : '' }}
                            >{{ utf8_encode($dpto->DptoNom) }}</option>
                        @endforeach
                      </select>
                      {!! $errors->first('state_id','<span class="help-block">:message</span>') !!}
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group {{ $errors->has('city_id') ? 'has-error' : '' }}">
                      <label for="exampleInputPassword1">Distrito</label>
                      <select class="form-control required" name="city_id" id="city_id" required>
                        <option value="">Selecciona el Distrito</option>
                        @foreach($localidad as $key=>$loc)
                          <option value="{{$loc->CiuId}}"
                            {{ old('state_id',isset($project['city_id'])?$project['city_id']:'') == $loc->CiuId ? 'selected' : '' }}
                            >{{ utf8_encode($loc->CiuNom) }}</option>
                        @endforeach
                      </select>
                      {!! $errors->first('city_id','<span class="help-block">:message</span>') !!}
                    </div>
                  </div>

                    <div class="col-md-4">
                      <div class="form-group {{ $errors->has('localidad') ? 'has-error' : '' }}">
                          <label>Localidad/Barrio</label>
                          <input type="text" required class="form-control" name="localidad" value="{{ old('localidad',isset($project['localidad'])?utf8_encode($project['localidad']):'') }}"  placeholder="Ingrese Localidad">
                          {!! $errors->first('localidad','<span class="help-block">:message</span>') !!}
                      </div>
                    </div>

                    <div class="col-md-4">
                      <!-- Nueva columna para coordenada Y -->
                      <div class="form-group {{ $errors->has('finca_nro') ? 'has-error' : '' }}">
                          <label for="exampleInputPassword1">Finca Nro</label>
                          <input required type="text" class="form-control" name="finca_nro" value="{{ old('finca_nro',isset($project['finca_nro'])?$project['finca_nro']:'') }}" placeholder="Ingrese Nro de Finca">
                          {!! $errors->first('finca_nro','<span class="help-block">:message</span>') !!}
                      </div>  </div>
                </div>
                <button type="submit" class="btn btn-primary pull-right">Guardar</button>
            </div>
          </form>
        </div>
      </div>
      @stop
      @section('js')

      <script type="text/javascript">
          $('select[name="modalidad_id"]').on('change', function() {
            var stateID = $(this).val();
            if(stateID) {
              $.ajax({
                url: '{{URL::to('/projects')}}/ajax/'+stateID+"/lands",
                type: "GET",
                dataType: "json",
                success:function(data) {
                  $('select[name="land_id"]').empty();
                  $('select[name="land_id"]').append('<option value="">Selecciona el Tipo de Terreno</option>');
                  $.each(data, function(key, value) {
                    $('select[name="land_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                  });
                }
              });
            } else {
              $('select[name="land_id"]').empty();
            }
          });

          $('select[name="land_id"]').on('change', function() {
            var stateID = $(this).val();
            if(stateID) {
              $.ajax({
                url: '{{URL::to('/projects')}}/ajax/'+stateID+"/typology",
                type: "GET",
                dataType: "json",
                success:function(data) {
                  $('select[name="typology_id"]').empty();
                  $('select[name="typology_id"]').append('<option value="">Selecciona la Tipologia</option>');
                  $.each(data, function(key, value) {
                    $('select[name="typology_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                  });
                }
              });
            } else {
              $('select[name="typology_id"]').empty();
            }
          });

          $('#state_id').on('change', function() {
            var state_id = $(this).val();
            if (state_id) {
              $.ajax({
                url: '/projects/ajax/' + state_id + '/local',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                  $('select[name="city_id"]').empty();
                  $('select[name="city_id"]').append('<option value="">Selecciona el Distrito</option>');

                  $.each(data, function(key, value) {
                  $('select[name="city_id"]').append('<option value="' + key + '">' + value + '</option>');
                  });
                }
              });
            } else {
              $('select[name="city_id"]').empty();
            }
          });

          function encode_utf8( s )
          {
            return unescape( encodeURIComponent( s ) );
          }

          function decode_utf8( s )
          {
            return decodeURIComponent( escape( s ) );
          }
        </script>

      @stop
