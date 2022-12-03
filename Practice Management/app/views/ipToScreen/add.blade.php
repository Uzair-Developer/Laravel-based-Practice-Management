@extends('layout/main')

@section('title')
    - {{$ipToScreen['ip'] ? 'Edit' : 'Add'}} IP To Screen
@stop

@section('header')

@stop

@section('footer')
    <script type="text/javascript">
        $(function () {

        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            {{$ipToScreen['ip'] ? 'Edit' : 'Add'}} IP To Screen
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    {{Form::open()}}
                    <div class="box-body" id="tab_1">

                        <div class="form-group col-md-3">
                            <label>Hospital *</label>
                            <select autocomplete="off" required id="selectHospital2" name="hospital_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    @if(Input::old('hospital_id'))
                                        <option value="{{$val['id']}}" @if(Input::old('hospital_id') == $val['id'])
                                        selected @endif>{{$val['name']}}</option>
                                    @else
                                        <option value="{{$val['id']}}" @if($ipToScreen['hospital_id'] == $val['id'])
                                        selected @endif>{{$val['name']}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Screen IP *</label>
                            <input required autocomplete="off" type="text"
                                   name="ip" class="form-control"
                                   value="{{Input::old('ip') ? Input::old('ip') : $ipToScreen['ip']}}">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Screen Name *</label>
                            <input required autocomplete="off" type="text"
                                   name="screen_name" class="form-control"
                                   value="{{Input::old('screen_name') ? Input::old('screen_name') : $ipToScreen['screen_name']}}">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Wait Area Name *</label>
                            <input required autocomplete="off" type="text"
                                   name="wait_area_name" class="form-control"
                                   value="{{Input::old('wait_area_name') ? Input::old('wait_area_name') : $ipToScreen['wait_area_name']}}">
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('ipToScreen')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop
