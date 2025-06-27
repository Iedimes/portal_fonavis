@extends('adminlte::page')

@section('title', 'FONAVIS')

@section('content_header')
<h1>{{ $title }}</h1>
@stop

@section('content')
<div class="invoice p-3 mb-3">
<div class="box box-primary">
    <form role="form" action="{{url('savepostulanteEdit') }}" method="post">
            @csrf
            @if(isset($postulante['id']))
                {!! method_field('post') !!}
            @endif
            <input type="text"  name="gender" value="{{ $sexo }}"  hidden>
            <input type="text"  name="project_id" value="{{ $project_id->id }}"  hidden>
            <input type="text"  name="grupo" value="{{ $project_id->name }}"  hidden>
            <input type="text"  name="disc_id" value="{{ isset($disc['id'])?$disc['id']:'' }}"  hidden>
            <input type="text"  name="postulante_id" value="{{ $postulante->id }}"  hidden>
      <div class="box-body">
          <div class="row">
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('first_name') ? 'has-error' : '' }}">
                        <label>Nombres</label>
                        <input type="text" class="form-control" name="first_name" value="{{ utf8_encode($nombre) }}"  readonly>
                        {!! $errors->first('first_name','<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->has('cedula') ? 'has-error' : '' }}">
                        <label>Cedula</label>
                        <input type="text" class="form-control" name="cedula" value="{{ $cedula }}"  readonly>
                        {!! $errors->first('cedula','<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->has('nacionalidad') ? 'has-error' : '' }}">
                        <label>Nacionalidad</label>
                        <input type="text" class="form-control" name="nacionalidad" value="{{ utf8_encode($nac) }}"  readonly>
                        {!! $errors->first('nacionalidad','<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->has('localidad') ? 'has-error' : '' }}">
                        <label for="exampleInputPassword1">Localidad</label>
                        <input type="text" class="form-control" required name="localidad" value="{{ old('localidad',isset($postulante['localidad'])?$postulante['localidad']:'') }}" placeholder="Ingrese Localidad">
                        {!! $errors->first('localidad','<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                        <label for="exampleInputPassword1">Dirección</label>
                        <input type="text" required class="form-control" name="address" value="{{ old('localidad',isset($postulante['address'])?$postulante['address']:'') }}" placeholder="Ingrese Dirección">
                        {!! $errors->first('address','<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                        <label for="exampleInputPassword1">Telefono (Linea Baja)</label>
                        <input type="text" required class="form-control" name="phone" value="{{ old('phone',isset($postulante['phone'])?$postulante['phone']:'') }}" placeholder="Ingrese telefono">
                        {!! $errors->first('phone','<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Parentesco</label>
                        <select class="form-control required" name="" readonly>
                            <option value="">Postulante</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('last_name') ? 'has-error' : '' }}">
                        <label>Apellidos</label>
                        <input type="text" class="form-control" name="last_name" value="{{ $apellido }}"  readonly>
                        {!! $errors->first('last_name','<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->has('marital_status') ? 'has-error' : '' }}">
                        <label>Estado Civil</label>
                        <input type="text" class="form-control" name="marital_status" value="{{ utf8_encode($est) }}"  readonly>
                        {!! $errors->first('marital_status','<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->has('birthdate') ? 'has-error' : '' }}">
                        <label>Fecha de Nacimiento</label>
                        <input type="text" class="form-control" name="birthdate" value="{{ substr($fecha, 0, 10) }}" readonly>
                        {!! $errors->first('birthdate','<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->has('asentamiento') ? 'has-error' : '' }}">
                        <label for="exampleInputPassword1">Asentamiento</label>
                        <input type="text" class="form-control" name="asentamiento" value="{{ old('asentamiento',isset($postulante['asentamiento'])?$postulante['asentamiento']:'') }}" placeholder="Ingrese Asentamiento">
                        {!! $errors->first('asentamiento','<span class="help-block">:message</span>') !!}
                    </div>

                    <div class="form-group {{ $errors->has('asentamiento') ? 'has-error' : '' }}">
                        <label for="exampleInputPassword1">Ingreso Mensual</label>
                        <input type="number" required class="form-control" name="ingreso" value="{{ old('ingreso',isset($postulante['ingreso'])?$postulante['ingreso']:'') }}" placeholder="Ingrese el Ingreso Mensual">
                        {!! $errors->first('ingreso','<span class="help-block">:message</span>') !!}
                    </div>

                    <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                        <label for="exampleInputPassword1">Telefono Movil (Celular)</label>
                        <input type="text" required class="form-control" name="mobile" value="{{ old('mobile',isset($postulante['mobile'])?$postulante['mobile']:'') }}" placeholder="Ingrese Telefono Movil (Celular)">
                        {!! $errors->first('mobile','<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->has('land_id') ? 'has-error' : '' }}">
                        <label for="exampleInputPassword1">Discapacidad</label>
                        <select class="form-control required" required name="discapacidad_id">
                            <option value="">Selecciona la Discapacidad</option>
                                @foreach($discapacdad as $key=>$dis)
                                    <option value="{{$dis->id}}"
                                        {{ old('typology_id',isset($disc['discapacidad_id'])?$disc['discapacidad_id']:'') == $dis->id ? 'selected' : '' }}
                                        >{{ $dis->name }}</option>
                                @endforeach
                        </select>
                        {!! $errors->first('discapacidad_id','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
          </div>
          <button type="submit" class="btn btn-primary pull-right">Guardar</button>
      </div>
    </form>
  </div>
</div>
  @stop
  @section('js')
  <script type="text/javascript">
      $('form').on('submit', function() {
          var submitButton = $(this).find('button[type="submit"]');
          submitButton.text('Guardando...');
          setTimeout(function() {
              submitButton.hide();
          }, 300); // espera un poco para que se vea el texto
      });
  </script>
  @stop
