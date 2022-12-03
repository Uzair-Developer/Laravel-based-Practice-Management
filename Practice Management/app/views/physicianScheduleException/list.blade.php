@extends('layout/main')

@section('title')
    - Physician Schedule Exceptions
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $(function () {

            $('.datepicker2').datepicker({
                todayHighlight: true,
                autoclose: true
            });

            $('.datepic_search').datepicker({
                todayHighlight: true,
                autoclose: true
            });
            $('#example1').DataTable({
                "paging": false,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "sScrollY": "400px",
                "sScrollX": "100%",
                "sScrollXInner": "100%",
                "bScrollCollapse": true
            });

            $(".ask-me").click(function (e) {
                e.preventDefault();
                if (confirm('All Reservations In This Day Will Be Converted To Archived!!!?')) {
                    window.location.replace($(this).attr('href'));
                }
            });

            $("#selectHospital2").change(function (e) {
                $("#selectClinic2").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getClinicsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectClinic2").removeAttr('disabled').html(data).select2();
                    }
                });
            });

            $("#selectClinic2").change(function (e) {
                $("#selectPhysician2").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getPhysicianByClinicId')}}',
                    method: 'POST',
                    data: {
                        clinic_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectPhysician2").removeAttr('disabled').html(data).select2();
                    }
                });
            });

            @if(Input::get('hospital_id'))
            $.ajax({
                        url: '{{route('getClinicsByHospitalId')}}',
                        method: 'POST',
                        async: false,
                        data: {
                            hospital_id: '{{Input::get('hospital_id')}}'
                        },
                        headers: {token: '{{csrf_token()}}'},
                        success: function (data) {
                            $("#selectClinic2").html(data).select2();
                            @if(Input::get('clinic_id'))
                            $("#selectClinic2").val('{{Input::get('clinic_id')}}').select2();
                            $.ajax({
                                url: '{{route('getPhysicianByClinicId')}}',
                                method: 'POST',
                                async: false,
                                data: {
                                    clinic_id: '{{Input::get('clinic_id')}}'
                                },
                                headers: {token: '{{csrf_token()}}'},
                                success: function (data) {
                                    $("#selectPhysician2").html(data).select2();
                                    @if(Input::get('user_id'))
                                        $("#selectPhysician2").val('{{Input::get('user_id')}}').select2();
                                    @endif
                                }
                            });
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
            Physician Schedule Exceptions
        </h1>
    </section>

    <section class="content">
        <div class="row">
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('physicianScheduleException.manage'))
                <div class="col-md-3">
                    <a href="{{route('managePhysicianScheduleException')}}">
                        <button class="btn btn-block btn-default">Manage Dr Schedule Exception</button>
                    </a>
                    <br>
                </div>
            @endif
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        Search
                        <button type="button" class="btn btn-box-tool pull-right" data-widget="collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                    <!-- /.box-header -->
                    {{Form::open(array('role'=>"form",'method' => 'GET'))}}
                    <div class="box-body">

                        <div class="form-group col-md-3">
                            <label>Hospital</label>
                            <select id="selectHospital2" name="hospital_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    <option value="{{$val['id']}}" @if(Input::get('hospital_id') == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Clinic</label>
                            <select id="selectClinic2" name="clinic_id" class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Physician</label>
                            <select autocomplete="off" id="selectPhysician2" name="user_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Start Date</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{Input::get('start_date')}}"
                                   name="start_date" class="form-control datepic_search">
                        </div>
                        <div class="form-group col-md-3">
                            <label>End Date</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{Input::get('end_date')}}"
                                   name="end_date" class="form-control datepic_search">
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{route('listPhysicianScheduleException')}}" class="btn btn-info">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            @if($inputs)
                <div class="col-md-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-bordered" id="example1">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Clinic Name</th>
                                    <th>Physician Name</th>
                                    <th>Date</th>
                                    <th>Visit Duration</th>
                                    <th>Sh1 From</th>
                                    <th>Sh1 To</th>
                                    <th>Sh1 DayOff</th>
                                    <th>Sh2 From</th>
                                    <th>Sh2 To</th>
                                    <th>Sh2 DayOff</th>
                                    <th>Sh3 From</th>
                                    <th>Sh3 To</th>
                                    <th>Sh3 DayOff</th>
                                    <th>Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($physicianScheduleExceptions as $val)
                                    <tr>
                                        <td>{{$val['id']}}</td>
                                        <td>{{$val['clinic_name']}}</td>
                                        <td>{{$val['physician_name']}}</td>
                                        <td>{{$val['date']}}</td>
                                        <td>{{$val['slots']}}</td>
                                        <td>{{$val['shift1_dayoff'] != 1 ? $val['shift1_time_from'] : ''}}</td>
                                        <td>{{$val['shift1_dayoff'] != 1 ? $val['shift1_time_to'] : ''}}</td>
                                        <td>{{$val['shift1_dayoff'] == 1 ? 'Yes' : 'No'}}</td>
                                        <td>{{$val['shift2_dayoff'] != 1 ? $val['shift2_time_from'] : ''}}</td>
                                        <td>{{$val['shift2_dayoff'] != 1 ? $val['shift2_time_to'] : ''}}</td>
                                        <td>{{$val['shift2_dayoff'] == 1 ? 'Yes' : 'No'}}</td>
                                        <td>{{$val['shift3_dayoff'] != 1 ? $val['shift3_time_from'] : ''}}</td>
                                        <td>{{$val['shift3_dayoff'] != 1 ? $val['shift3_time_to'] : ''}}</td>
                                        <td>{{$val['shift3_dayoff'] == 1 ? 'Yes' : 'No'}}</td>
                                        <td>
                                            <div class="btn-group">
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('physicianScheduleException.delete'))
                                                    <a class="btn btn-danger btn-sm ask-me"
                                                       href="{{route('deletePhysicianScheduleException', $val['id'])}}">Delete</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{$physicianScheduleExceptions->appends(Input::except('_token'))->links()}}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@stop