@extends('layout/main')

@section('title')
    - Clinic Schedules
@stop


@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script>
        $(function () {

            $(".ask-me").click(function (e) {
                e.preventDefault();
                if (confirm('Confirm Delete?')) {
                    window.location.replace($(this).attr('href'));
                }
            });

            $('#example1').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": false,
                "info": true,
                "autoWidth": true
            });
            $('.datepicker').datepicker({
                startDate: "1d",
                todayHighlight: true,
                autoclose: true
            });
            $('.datepicker2').datepicker({
                todayHighlight: true,
                autoclose: true
            });
            $(".duplicateButton").click(function (e) {
                var clinic_id = $(this).attr('clinic_id');
                var schedule_id = $(this).attr('schedule_id');
                $.ajax({
                    url: '{{route('getLastScheduleOfClinic')}}',
                    method: 'POST',
                    data: {
                        clinic_id: clinic_id,
                        schedule_id: schedule_id
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#modalTitle").html('Duplicate Schedule Of ' + data.name);
                        $("#clinic_schedule_id").val(data.schedule_id);
                        var end_date = data.end_date;
                        var dmy = end_date.split("-");
                        var joindate = new Date(
                                parseInt(dmy[0], 10),
                                parseInt(dmy[1], 10) - 1,
                                parseInt(dmy[2], 10)
                        );
                        joindate.setDate(joindate.getDate() + 1);
                        var date = joindate.getFullYear() + "-" +
                                ("0" + (joindate.getMonth() + 1)).slice(-2) + "-" +
                                ("0" + joindate.getDate()).slice(-2);
                        $("#start_date").val(date);
                        $("#start_date_div").html(date);
                        $("#myModal").modal('show');
                    }
                });
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
            @if(Input::get('hospital_id'))
            $.ajax({
                        url: '{{route('getClinicsByHospitalId')}}',
                        method: 'POST',
                        data: {
                            hospital_id: $('#selectHospital2').val()
                        },
                        headers: {token: '{{csrf_token()}}'},
                        success: function (data) {
                            $("#selectClinic2").html(data).select2();
                            @if(Input::get('clinic_id'))
                            $("#selectClinic2").val('{{Input::get('clinic_id')}}').select2();
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
            List Clinic Schedules
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
                            <label>Schedule Name</label>
                            <input type="text" name="name" value="{{Input::get('name')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Start Date</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{Input::get('start_date')}}"
                                   name="start_date" class="form-control datepicker2">
                        </div>
                        <div class="form-group col-md-3">
                            <label>End Date</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{Input::get('end_date')}}"
                                   name="end_date" class="form-control datepicker2">
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{route('clinicSchedules')}}" class="btn btn-info">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>

            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-header">
                        @if($c_user->user_type_id == 1 || $c_user->hasAccess('clinicSchedule.add'))
                            <div class="col-md-3">
                                <a href="{{route('addClinicSchedule')}}">
                                    <button class="btn btn-block btn-default">Add ClinicSchedule</button>
                                </a>
                            </div>
                        @endif
                        @if($c_user->user_type_id == 1 || $c_user->hasAccess('clinicSchedule.importExcel'))
                            <div class="col-md-3">
                                <a href="{{route('importExcelClinicSchedule')}}">
                                    <button class="btn btn-block btn-default">Import Excel</button>
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="example1">
                                <thead>
                                <tr>
                                    <th style="width: 15px">#</th>
                                    <th>Schedule Name</th>
                                    <th>Hospital Name</th>
                                    <th>Clinic Name</th>
                                    <th>Shift Count</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($clinicSchedules as $val)
                                    <tr>
                                        <td>{{$val['id']}}</td>
                                        <td>{{$val['name']}}</td>
                                        <td>{{$val['hospital_name']}}</td>
                                        <td>{{$val['clinic_name']}}</td>
                                        <td>{{$val['num_of_shifts']}}</td>
                                        <td>{{$val['start_date']}}</td>
                                        <td>{{$val['end_date']}}</td>
                                        <td>
                                            <div class="btn-group" style="width: 200px;">
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('clinicSchedule.edit'))
                                                    <a class="btn btn-default"
                                                       href="{{route('editClinicSchedule', $val['id'])}}">Edit</a>

                                                @endif
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('clinicSchedule.duplicate'))
                                                    <a class="btn btn-default duplicateButton" style="cursor: pointer"
                                                       schedule_id="{{$val['id']}}"
                                                       clinic_id="{{$val['clinic_id']}}">Duplicate</a>
                                                @endif
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('clinicSchedule.delete'))
                                                    <?php
                                                    $lastSchedule = ClinicSchedule::getAllByClinicId($val['clinic_id'])->last();
                                                    ?>
                                                    @if($lastSchedule['id'] == $val['id'])
                                                        <a class="btn btn-danger ask-me"
                                                           href="{{route('deleteClinicSchedule', $val['id'])}}">Delete</a>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="modalTitle"></h4>
                </div>
                {{Form::open(array('role'=>"form", 'route' => 'duplicateClinicSchedule'))}}
                <div class="modal-body">
                    <input id="clinic_schedule_id" name="clinic_schedule_id" type="hidden">

                    <div class="form-group">
                        <label>Start Date</label>

                        <div id="start_date_div"></div>
                        <input id="start_date" name="start_date" type="hidden">
                    </div>

                    <div class="form-group">
                        <label>End Date *</label>
                        <input required id="end_date" name="end_date" type="text"
                               class="form-control datepicker" data-date-format="yyyy-mm-dd">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Duplicate</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>

@stop