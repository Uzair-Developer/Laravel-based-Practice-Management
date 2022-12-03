@extends('layout/main')

@section('title')
    - {{$ipToRoom['type'] ? 'Edit' : 'Add'}} IP To Room
@stop

@section('header')

@stop

@section('footer')
    <script type="text/javascript">
        $(function () {
            $("#selectHospital2").change(function (e) {
                $("#selectRoom").attr('disabled', 'disabled');
                $("#ip_to_screen_id").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getRoomsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val(),
                        @if($ipToRoom['hospital_id'])
                        exceptIds: '{{$ipToRoom['room_id']}}'
                        @endif
                        },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectRoom").removeAttr('disabled').html(data.rooms).select2();
                        $("#ip_to_screen_id").removeAttr('disabled').html(data.screens).select2();
                    }
                });
            });

            $(".for_sms_system").show();
            $(".for_sms_system input").attr('required', 'required');
            $(".for_queue_system").hide();
            $(".for_queue_system input, .for_queue_system select").removeAttr('required');
            $("#type").change(function (e) {
                var value = $(this).val();
                if (value == 2) {
                    $(".for_sms_system").hide();
                    $(".for_sms_system input").removeAttr('required');
                    $(".for_queue_system").show();
                    $(".for_queue_system input, .for_queue_system select").attr('required', 'required');
                } else {
                    $(".for_sms_system").show();
                    $(".for_sms_system input").attr('required', 'required');
                    $(".for_queue_system").hide();
                    $(".for_queue_system input, .for_queue_system select").removeAttr('required');
                }
            });

                    @if(Input::old('type')|| $ipToRoom['type'])
                        var type = '{{Input::old('type') ? Input::old('type') : $ipToRoom['type']}}';
            if (type == 2) {
                $(".for_sms_system").hide();
                $(".for_sms_system input").removeAttr('required');
                $(".for_queue_system").show();
                $(".for_queue_system input, .for_queue_system select").attr('required', 'required');
            } else {
                $(".for_sms_system").show();
                $(".for_sms_system input").attr('required', 'required');
                $(".for_queue_system").hide();
                $(".for_queue_system input, .for_queue_system select").removeAttr('required');
            }
                    @endif

                            @if(Input::old('hospital_id') || $ipToRoom['hospital_id'])
                            var hospital_id = '{{Input::old('hospital_id') ? Input::old('hospital_id') : $ipToRoom['hospital_id']}}';
            $("#selectRoom, #ip_to_screen_id").attr('disabled', 'disabled');
            $.ajax({
                url: '{{route('getRoomsByHospitalId')}}',
                method: 'POST',
                data: {
                    hospital_id: hospital_id,
                    @if($ipToRoom['hospital_id'])
                    exceptIds: '{{$ipToRoom['room_id']}}'
                    @endif
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#selectRoom").removeAttr('disabled').html(data.rooms).select2();
                    $("#ip_to_screen_id").removeAttr('disabled').html(data.screens).select2();
                    @if(Input::old('room_id') || $ipToRoom['room_id'])
                    var room_ids = '{{Input::old('room_id') ? implode(',', Input::old('room_id')) : $ipToRoom['room_id']}}';
                    $("#selectRoom").val(room_ids.split(",")).select2();
                    @endif
                    @if(Input::old('ip_to_screen_id') || $ipToRoom['ip_to_screen_id'])
                    var screen_id = '{{Input::old('ip_to_screen_id') ? Input::old('ip_to_screen_id') : $ipToRoom['ip_to_screen_id']}}';
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
            {{$ipToRoom['type'] ? 'Edit' : 'Add'}} IP To Room
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    {{Form::open()}}
                    <div class="box-body" id="tab_1">
                        <div class="form-group col-md-4">
                            <label>Type *</label>
                            <select autocomplete="off" required name="type" id="type" class="form-control select2">
                                @if(Input::old('type'))
                                    <option value="1" @if(Input::old('type') == 1)selected @endif>IP To Room</option>
                                    <option value="2" @if(Input::old('type') == 2)selected @endif>Screen To Rooms</option>
                                @else
                                    <option value="1" @if($ipToRoom['type'] == 1)selected @endif>IP To Room</option>
                                    <option value="2" @if($ipToRoom['type']== 2)selected @endif>Screen To Rooms</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Hospital *</label>
                            <select autocomplete="off" required id="selectHospital2" name="hospital_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    @if(Input::old('hospital_id'))
                                        <option value="{{$val['id']}}" @if(Input::old('hospital_id') == $val['id'])
                                        selected @endif>{{$val['name']}}</option>
                                    @else
                                        <option value="{{$val['id']}}" @if($ipToRoom['hospital_id'] == $val['id'])
                                        selected @endif>{{$val['name']}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4 for_sms_system">
                            <label>Room IP *</label>
                            <input required autocomplete="off" id="ip" type="text"
                                   name="ip" class="form-control"
                                   value="{{Input::old('ip') ? Input::old('ip') : $ipToRoom['ip']}}">
                        </div>

                        <div class="form-group col-md-4 for_sms_system">
                            <label>Room Number *</label>
                            <input autocomplete="off" type="text"
                                   name="room_num" class="form-control"
                                   value="{{Input::old('room_num') ? Input::old('room_num') : $ipToRoom['room_num']}}">
                        </div>
                        <div class="form-group col-md-4 for_sms_system">
                            <label>Room Name *</label>
                            <input autocomplete="off" type="text"
                                   name="room_name" class="form-control"
                                   value="{{Input::old('room_name') ? Input::old('room_name') : $ipToRoom['room_name']}}">
                        </div>
                        <div class="form-group col-md-4 for_sms_system">
                            <label>Corridor Num *</label>
                            <input required autocomplete="off" id="corridor_num" type="text"
                                   name="corridor_num" class="form-control"
                                   value="{{Input::old('corridor_num') ? Input::old('corridor_num') : $ipToRoom['corridor_num']}}">
                        </div>
                        <div class="form-group col-md-4 for_queue_system">
                            <label>Screen IP *</label>
                            <select autocomplete="off" required name="ip_to_screen_id" id="ip_to_screen_id" class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4 for_queue_system">
                            <label>Rooms *</label>
                            <select required id="selectRoom" name="room_id[]" class="form-control select2" multiple>
                                <option value="">Choose</option>
                            </select>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('ipToRoom')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop
