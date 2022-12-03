@extends('layout/main')

@section('title')
    - Reception Delegate
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
                "sScrollXInner": "100%",
                "bScrollCollapse": true
            });

            $("#selectHospital2").change(function (e) {
                $("#reception_id").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getRoomsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#reception_id").removeAttr('disabled').html(data.screens).select2();
                    }
                });
            });
            @if(Input::get('hospital_id'))
            $.ajax({
                        url: '{{route('getRoomsByHospitalId')}}',
                        method: 'POST',
                        data: {
                            hospital_id: '{{Input::get('hospital_id')}}'
                        },
                        headers: {token: '{{csrf_token()}}'},
                        success: function (data) {
                            $("#reception_id").removeAttr('disabled').html(data.screens).select2();
                            @if(Input::get('reception_id'))
                            $("#reception_id").val('{{Input::get('reception_id')}}').select2();
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
            Reception Delegate
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
                    {{Form::open(array('method' => 'GET'))}}
                    <div class="box-body">
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
                            <label>Reception</label>
                            <select autocomplete="off" id="reception_id" name="reception_id" class="form-control select2">
                                <option value="">Choose</option>
                                
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{route('receptionDelegate')}}" class="btn btn-info">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('receptionDelegate.add'))
                <div class="col-md-3">
                    <a href="{{route('addReceptionDelegate')}}">
                        <button class="btn btn-block btn-default">Add Reception Delegate</button>
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
                                <th>Hospital</th>
                                <th>Reception</th>
                                <th>Rec. Delegate 1</th>
                                <th>Rec. Delegate 2</th>
                                <th>Rec. Delegate 3</th>
                                <th>Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($receptionDelegate as $val)
                                <tr>
                                    <td>{{$val['id']}}</td>
                                    <td>{{$val['hospital_name']}}</td>
                                    <td>{{$val['reception_name']}}</td>
                                    <td>{{$val['reception_del1']}}</td>
                                    <td>{{$val['reception_del2']}}</td>
                                    <td>{{$val['reception_del3']}}</td>
                                    <td>
                                        <div class="btn-group" style="width: 150px;">
                                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('receptionDelegate.edit'))
                                                <a class="btn btn-default"
                                                   href="{{route('editReceptionDelegate', $val['id'])}}">Edit</a>

                                            @endif
                                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('receptionDelegate.delete'))
                                                <a class="btn btn-danger ask-me"
                                                   href="{{route('deleteReceptionDelegate', $val['id'])}}">Delete</a>
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