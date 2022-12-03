@extends('layout/main')

@section('title')
    - Ip To Room
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $(function () {
            $(".ask-me").click(function (e) {
                e.preventDefault();
                if (confirm('Are You Sure?')) {
                    window.location.replace($(this).attr('href'));
                }
            });

            $('#example1').DataTable({
                "paging": false,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                'order': [[0, 'asc']],
                "sScrollY": "400px",
                "sScrollX": "100%",
                "sScrollXInner": "120%",
                "bScrollCollapse": true
            });

            $("#selectHospital2").change(function (e) {
                $("#selectRoom").attr('disabled', 'disabled');
                $("#ip_to_screen_id").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getRoomsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectRoom").removeAttr('disabled').html(data.rooms).select2();
                        $("#ip_to_screen_id").removeAttr('disabled').html(data.screens).select2();
                    }
                });
            });
            @if(Input::get('hospital_id'))
            $.ajax({
                        url: '{{route('getRoomsByHospitalId')}}',
                        method: 'POST',
                        data: {
                            hospital_id: $('#selectHospital2').val()
                        },
                        headers: {token: '{{csrf_token()}}'},
                        success: function (data) {
                            $("#selectRoom").removeAttr('disabled').html(data.rooms).select2();
                            $("#ip_to_screen_id").removeAttr('disabled').html(data.screens).select2();
                            @if(Input::get('room_id'))
                            $("#selectRoom").val('{{Input::get('room_id')}}').select2();
                            @endif
                            @if(Input::get('ip_to_screen_id'))
                            $("#ip_to_screen_id").val('{{Input::get('ip_to_screen_id')}}').select2();
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
            Ip To Room
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        Search
                        <button type="button" class="btn btn-box-tool pull-right"
                                data-widget="collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                    <!-- /.box-header -->
                    {{Form::open(array('role'=>"form",'method' => 'GET'))}}
                    <div class="box-body">
                        <div class="form-group col-md-3">
                            <label>Type</label>
                            <select autocomplete="off" name="type" id="type" class="form-control select2">
                                <option value="">Choose</option>
                                <option value="1" @if(Input::get('type') == 1)selected @endif>IP To Room</option>
                                <option value="2" @if(Input::get('type') == 2)selected @endif>Screen To Rooms</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Hospital</label>
                            <select autocomplete="off" id="selectHospital2" name="hospital_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    <option value="{{$val['id']}}" @if(Input::get('hospital_id') == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Room IP</label>
                            <input type="text" name="ip" value="{{Input::get('ip')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Room Num</label>
                            <input type="number" name="room_num" value="{{Input::get('room_num')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Room Name</label>
                            <input type="text" name="room_name" value="{{Input::get('room_name')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Corridor Num</label>
                            <input type="text" name="corridor_num" value="{{Input::get('corridor_num')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Screen IP</label>
                            <select name="ip_to_screen_id" id="ip_to_screen_id" class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Room</label>
                            <select id="selectRoom" name="room_id" class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{route('ipToRoom')}}" class="btn btn-info">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('ipToRoom.add'))
                <div class="col-md-2">
                    <a href="{{route('addIpToRoom')}}">
                        <button class="btn btn-block btn-default">Add Ip To Room</button>
                    </a>
                    <br>
                </div>
            @endif
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered" id="example1">
                            <thead>
                            <tr>
                                <th style="width: 15px">#</th>
                                <th>Type</th>
                                <th>Hospital</th>
                                <th>Room IP</th>
                                <th>Room Num</th>
                                <th>Room Name</th>
                                <th>Corridor Num</th>
                                <th>Screen IP</th>
                                <th>Rooms</th>
                                <th>Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($ipToRoom as $val)
                                <tr>
                                    <td>{{$val['id']}}</td>
                                    <td>{{$val['type'] == 1 ? 'IP To Room' : 'Screen To Rooms'}}</td>
                                    <td>{{$val['hospital_name']}}</td>
                                    <td>{{$val['ip']}}</td>
                                    <td>{{$val['room_num']}}</td>
                                    <td>{{$val['room_name']}}</td>
                                    <td>{{$val['corridor_num']}}</td>
                                    <td>{{$val['ip_to_screen_name']}}</td>
                                    <td>{{$val['rooms_name']}}</td>
                                    <td>
                                        <div class="btn-group" style="width: 150px;">
                                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('ipToRoom.edit'))
                                                <a class="btn btn-default"
                                                   href="{{route('editIpToRoom', $val['id'])}}">Edit</a>

                                            @endif
                                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('ipToRoom.delete'))
                                                <a class="btn btn-danger ask-me"
                                                   href="{{route('deleteIpToRoom', $val['id'])}}">Delete</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
    </section>
@stop