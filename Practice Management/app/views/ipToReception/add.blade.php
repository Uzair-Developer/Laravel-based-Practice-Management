@extends('layout/main')

@section('title')
    - {{$ipToReception['ip'] ? 'Edit' : 'Add'}} IP To Reception
@stop

@section('header')

@stop

@section('footer')
    <script type="text/javascript">
        $(function () {

            $("#selectHospital2").change(function (e) {
                $("#ip_to_screen_id").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getRoomsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#ip_to_screen_id").removeAttr('disabled').html(data.screens).select2();
                    }
                });
            });

                    @if(Input::old('hospital_id') || $ipToReception['hospital_id'])
                                var hospital_id = '{{Input::old('hospital_id') ? Input::old('hospital_id') : $ipToReception['hospital_id']}}';
            $.ajax({
                url: '{{route('getRoomsByHospitalId')}}',
                method: 'POST',
                data: {
                    hospital_id: hospital_id
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#ip_to_screen_id").removeAttr('disabled').html(data.screens).select2();
                            @if(Input::old('ip_to_screen_id') || $ipToReception['ip_to_screen_id'])
                            var screen_id = '{{Input::old('ip_to_screen_id') ? Input::old('ip_to_screen_id') : $ipToReception['ip_to_screen_id']}}';
                    $("#ip_to_screen_id").val(screen_id).select2();
                    @endif
                }
            });
            @endif

        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            {{$ipToReception['ip'] ? 'Edit' : 'Add'}} IP To Reception
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
                            <select autocomplete="off" required id="selectHospital2" name="hospital_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    @if(Input::old('hospital_id'))
                                        <option value="{{$val['id']}}" @if(Input::old('hospital_id') == $val['id'])
                                        selected @endif>{{$val['name']}}</option>
                                    @else
                                        <option value="{{$val['id']}}" @if($ipToReception['hospital_id'] == $val['id'])
                                        selected @endif>{{$val['name']}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Reception IP *</label>
                            <input required autocomplete="off" type="text"
                                   name="ip" class="form-control"
                                   value="{{Input::old('ip') ? Input::old('ip') : $ipToReception['ip']}}">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Reception Name *</label>
                            <input required autocomplete="off" type="text"
                                   name="name" class="form-control"
                                   value="{{Input::old('name') ? Input::old('name') : $ipToReception['name']}}">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Screen IP *</label>
                            <select autocomplete="off" required name="ip_to_screen_id" id="ip_to_screen_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('ipToReception')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop
