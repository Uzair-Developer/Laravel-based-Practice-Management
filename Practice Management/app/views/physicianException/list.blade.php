@extends('layout/main')

@section('title')
    - Physician Exceptions
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
                'order': [[5, 'asc'], [0, 'asc']],
                "sScrollY": "400px",
                "sScrollX": "100%",
                "sScrollXInner": "200%",
                "bScrollCollapse": true
            });

            $(".ask-me").click(function (e) {
                e.preventDefault();
                if (confirm('Are You Sure?')) {
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
            List Physician Exceptions
        </h1>
    </section>

    <section class="content">
        <div class="row">
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('physicianException.add'))
                <div class="col-md-3">
                    <a href="{{route('addPhysicianException')}}">
                        <button class="btn btn-block btn-default">Add Physician Exception</button>
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
                            <br>
                            <select autocomplete="off" id="selectClinic2" name="clinic_id"
                                    class="form-control select2" style="width: 100%;">
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
                            <label>Reason</label>
                            <select autocomplete="off" id="selectPhysician2" name="reason_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($reasons as $val)
                                    <option @if(Input::get('reason_id') == $val['id']) selected
                                            @endif value="{{$val['id']}}">{{$val['name']}}</option>
                                @endforeach
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
                        <div class="form-group col-md-3">
                            <label>Status</label>
                            <select autocomplete="off" name="status"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                <option @if(Input::get('status') === "0") selected @endif value="0">Pending</option>
                                <option @if(Input::get('status') == 1) selected @endif value="1">Approved</option>
                                <option @if(Input::get('status') == 2) selected @endif value="2">Reject
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{route('physicianExceptions')}}" class="btn btn-info">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
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
                                <th>Reason</th>
                                <th>Effect?</th>
                                <th>F Date</th>
                                <th>T Date</th>
                                <th>F Time</th>
                                <th>T Time</th>
                                <th>Schedule Times</th>
                                <th>Notes</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Changed By</th>
                                <th>Changed At</th>
                                <th>Status</th>
                                <th>Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($physicianExceptions as $physicianException)
                                <tr>
                                    <td>{{$physicianException['id']}}</td>
                                    <td>{{$physicianException['clinic_name']}}</td>
                                    <td>{{$physicianException['physician_name']}}</td>
                                    <td>{{$physicianException['reason_name']}}</td>
                                    <td>{{$physicianException['effect'] == 1 ? 'Yes' : 'No'}}</td>
                                    <td>{{$physicianException['from_date']}}</td>
                                    <td>{{$physicianException['to_date']}}</td>
                                    <td>{{$physicianException['from_time']}}</td>
                                    <td>{{$physicianException['to_time']}}</td>
                                    <td>{{str_replace(' ', ' to ', $physicianException['schedule_times'])}}</td>
                                    <td>{{$physicianException['notes']}}</td>
                                    <td>{{$physicianException['create_name']}}</td>
                                    <td>{{$physicianException['created_at']}}</td>
                                    <td>{{$physicianException['change_by_name']}}</td>
                                    <td>{{$physicianException['change_status_at']}}</td>
                                    <td>
                                        @if($physicianException['status'] == 0)
                                            Pending
                                        @elseif($physicianException['status'] == 1)
                                            Approved
                                        @elseif($physicianException['status'] == 2)
                                            Rejected
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" style="width: 200px;">
                                            @if($physicianException['to_date'] >= date('Y-m-d'))
                                                @if($c_user->user_type_id == 1 || ($c_user->hasAccess('physicianException.edit') && $physicianException['status'] == 0))
                                                    <a class="btn btn-default btn-sm"
                                                       href="{{route('editPhysicianException', $physicianException['id'])}}">Edit</a>
                                                @endif
                                            @endif
                                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('physicianException.delete'))
                                                <a class="btn btn-danger btn-sm ask-me"
                                                   href="{{route('deletePhysicianException', $physicianException['id'])}}">Delete</a>
                                            @endif
                                            @if(($c_user->user_type_id == 1 || $c_user->hasAccess('physicianException.changeStatus')) && $physicianException['status'] == 0)
                                                <a class="btn btn-default btn-sm"
                                                   href="{{route('approvedPhysicianException', $physicianException['id'])}}">Approve</a>
                                            @endif
                                            @if(($c_user->user_type_id == 1 || $c_user->hasAccess('physicianException.changeStatus')) && $physicianException['status'] == 0)
                                                <a class="btn btn-default btn-sm"
                                                   href="{{route('notApprovedPhysicianException', $physicianException['id'])}}">Reject</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        {{$physicianExceptions->appends(Input::except('_token'))->links()}}
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop