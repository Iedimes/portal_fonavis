@extends('brackets/admin-ui::admin.layout.default')

@section('title', $title)

@section('body')
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $title }}</h3>
            </div>
            <div class="card-body">
                <form role="form" action="{{url('admin/postulantes/miembro/guardar') }}" method="post">
                    @csrf
                    @if(isset($project['id']))
                        {!! method_field('post') !!}
                    @endif
                    <input type="text" name="gender" value="{{ $sexo }}" hidden>
                    <input type="text" name="project_id" value="{{ $project_id->id }}" hidden>
                    <input type="text" name="grupo" value="{{ utf8_encode($project_id->name) }}" hidden>
                    <input type="text" name="postulante_id" value="{{ $idpostulante}}" hidden>
                    <input type="text" name="disc_id" value="{{ isset($disc['id'])?$disc['id']:'' }}" hidden>
                    <input type="text" name="parent_id" value="{{ isset($parent->id)?$parent->id:''}}" hidden>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('first_name') ? 'has-error' : '' }}">
                                <label class="form-label">Nombres</label>
                                <input type="text" class="form-control" name="first_name" value="{{ $nombre }}" readonly>
                                {!! $errors->first('first_name','<span class="invalid-feedback">:message</span>') !!}
                            </div>

                            <div class="form-group {{ $errors->has('cedula') ? 'has-error' : '' }}">
                                <label class="form-label">Cedula</label>
                                <input type="text" class="form-control" name="cedula" value="{{ $cedula }}" readonly>
                                {!! $errors->first('cedula','<span class="invalid-feedback">:message</span>') !!}
                            </div>

                            <div class="form-group {{ $errors->has('nacionalidad') ? 'has-error' : '' }}">
                                <label class="form-label">Nacionalidad</label>
                                <input type="text" class="form-control" name="nacionalidad" value="{{ $nac }}" readonly>
                                {!! $errors->first('nacionalidad','<span class="invalid-feedback">:message</span>') !!}
                            </div>

                            <div class="form-group {{ $errors->has('localidad') ? 'has-error' : '' }}">
                                <label class="form-label">Localidad</label>
                                <input type="text" class="form-control" required name="localidad" value="{{ old('localidad',isset($postulante['localidad'])?$postulante['localidad']:'') }}" placeholder="Ingrese Localidad">
                                {!! $errors->first('localidad','<span class="invalid-feedback">:message</span>') !!}
                            </div>

                            <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                                <label class="form-label">Dirección</label>
                                <input type="text" required class="form-control" name="address" value="{{ old('localidad',isset($postulante['address'])?$postulante['address']:'') }}" placeholder="Ingrese Dirección">
                                {!! $errors->first('address','<span class="invalid-feedback">:message</span>') !!}
                            </div>

                            <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                                <label class="form-label">Telefono (Linea Baja)</label>
                                <input type="text" required class="form-control" name="phone" value="{{ old('phone',isset($postulante['phone'])?$postulante['phone']:'') }}" placeholder="Ingrese telefono">
                                {!! $errors->first('phone','<span class="invalid-feedback">:message</span>') !!}
                            </div>

                            <div class="form-group">
                                <label class="form-label">Parentesco</label>
                                <select class="form-control required" name="parentesco_id" required>
                                    <option value="">Seleccione Parentesco</option>
                                    @foreach($parentesco as $key=>$par)
                                        <option value="{{$par->id}}"
                                            {{ old('typology_id',isset($parent['parentesco_id'])?$parent['parentesco_id']:'') == $par->id ? 'selected' : '' }}
                                            >{{ $par->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('last_name') ? 'has-error' : '' }}">
                                <label class="form-label">Apellidos</label>
                                <input type="text" class="form-control" name="last_name" value="{{ $apellido }}" readonly>
                                {!! $errors->first('last_name','<span class="invalid-feedback">:message</span>') !!}
                            </div>

                            <div class="form-group {{ $errors->has('marital_status') ? 'has-error' : '' }}">
                                <label class="form-label">Estado Civil</label>
                                <input type="text" class="form-control" name="marital_status" value="{{ $est }}" readonly>
                                {!! $errors->first('marital_status','<span class="invalid-feedback">:message</span>') !!}
                            </div>

                            <div class="form-group {{ $errors->has('birthdate') ? 'has-error' : '' }}">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="text" class="form-control" name="birthdate" value="{{ substr($fecha, 0, 10) }}" readonly>
                                {!! $errors->first('birthdate','<span class="invalid-feedback">:message</span>') !!}
                            </div>

                            <div class="form-group {{ $errors->has('asentamiento') ? 'has-error' : '' }}">
                                <label class="form-label">Asentamiento</label>
                                <input type="text" class="form-control" name="asentamiento" value="{{ old('asentamiento',isset($postulante['asentamiento'])?$postulante['asentamiento']:'') }}" placeholder="Ingrese Asentamiento">
                                {!! $errors->first('asentamiento','<span class="invalid-feedback">:message</span>') !!}
                            </div>

                            <div class="form-group {{ $errors->has('asentamiento') ? 'has-error' : '' }}">
                                <label class="form-label">Ingreso Mensual</label>
                                <input type="text" class="form-control" required name="ingreso" value="{{ old('ingreso',isset($postulante['ingreso'])?$postulante['ingreso']:'') }}" placeholder="Ingrese el Ingreso Mensual">
                                {!! $errors->first('ingreso','<span class="invalid-feedback">:message</span>') !!}
                            </div>

                            <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                                <label class="form-label">Telefono Movil (Celular)</label>
                                <input type="text" class="form-control" required name="mobile" value="{{ old('mobile',isset($postulante['mobile'])?$postulante['mobile']:'') }}" placeholder="Ingrese Telefono Movil (Celular)">
                                {!! $errors->first('mobile','<span class="invalid-feedback">:message</span>') !!}
                            </div>

                            <div class="form-group {{ $errors->has('land_id') ? 'has-error' : '' }}">
                                <label class="form-label">Discapacidad</label>
                                <select class="form-control required" required name="discapacidad_id">
                                    <option value="">Selecciona la Discapacidad</option>
                                        @foreach($discapacdad as $key=>$dis)
                                            <option value="{{$dis->id}}"
                                                {{ old('typology_id',isset($disc['discapacidad_id'])?$disc['discapacidad_id']:'') == $dis->id ? 'selected' : '' }}
                                                >{{ $dis->name }}</option>
                                        @endforeach
                                </select>
                                {!! $errors->first('typology_id','<span class="invalid-feedback">:message</span>') !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

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
@endsection
