@extends('layout/main')

@section('title')
    - Physician Schedules
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
            $('#checkbox1_select_all').click(function (event) {  //on click
                if (this.checked) { // check select status
                    $('.checkbox1').each(function () { //loop through each checkbox
                        this.checked = true;  //select all checkboxes with class "checkbox1"
                    });
                } else {
                    $('.checkbox1').each(function () { //loop through each checkbox
                        this.checked = false; //deselect all checkboxes with class "checkbox1"
                    });
                }
                $(".publishCheckbox").trigger("change");
            });

            var checkedArray = new Array();
            $(document).on('change', '.publishCheckbox', function () {
                if ($(".publishCheckbox:checked").length > 0) {
                    $("#publishAction").show();
                    $("#deleteAction").show();
                }
                else {
                    $("#publishAction").hide();
                    $("#deleteAction").hide();
                }
                var checkedValues = $('.publishCheckbox:checked').map(function () {
                    return $(this).attr('clinic_schedule_id');
                }).get();
                $('#publishInput').val(checkedValues);
                $('#deleteInput').val(checkedValues);
            });

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
                'order': [[5, 'asc']]
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
            $('.datepic_search').datepicker({
                todayHighlight: true,
                autoclose: true
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

            $(".changeDatePhysicianScheduleBtn").click(function (e) {
                var schedule_id = $(this).attr('schedule_id');
                $.ajax({
                    url: '{{route('getPhysicianSchedule')}}',
                    method: 'POST',
                    data: {
                        physician_schedule_id: schedule_id
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#modal_start_date").html('').html(data.start_date);
                        $("#modal_end_date").val('').val(data.end_date);
                        $("#modal_schedule_id").val('').val(data.id);
                        $("#changeDatePhysicianScheduleModal").modal('show');
                    }
                });
            });

            $("#modal_end_date").change(function (e) {
                var start_date = $("#modal_start_date").html();
                if (start_date > $(this).val()) {
                    $(this).val('');
                }
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
            List Physician Schedules
        </h1>
    </section>

    <section class="content">
        <div class="row">
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
                                    class="form-control select2" style="width: 100%;">
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

                        <div class="form-group col-md-3">
                            <label>Status</label>
                            <select autocomplete="off" name="publish" class="form-control select2">
                                <option value="">Choose</option>
                                <option @if(Input::get('publish') == 1) selected @endif value="1">Published</option>
                                <option @if(Input::get('publish') == 2) selected @endif value="2">UnPublished</option>
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{route('physicianSchedules')}}" class="btn btn-info">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-header">
                        <div class="col-md-1">
                            <input id="checkbox1_select_all" type="checkbox"
                                   autocomplete="off">
                        </div>
                        @if($c_user->user_type_id == 1 || $c_user->hasAccess('physicianSchedule.delete'))
                            <div class="col-md-2" id="deleteAction" style="display: none;">
                                {{Form::open(array('route' => 'deletePhysicianScheduleArray'))}}
                                <input type="hidden" id="deleteInput" name="ids">
                                <button class="btn btn-block btn-danger" type="submit">Delete Selected</button>
                                {{Form::close()}}
                            </div>
                        @endif
                        @if($c_user->user_type_id == 1 || $c_user->hasAccess('physicianSchedule.on-off'))
                            <div class="col-md-2" id="publishAction" style="display: none;">
                                {{Form::open(array('route' => 'changeStatusPhysicianScheduleArray'))}}
                                <input type="hidden" id="publishInput" name="ids">
                                <button class="btn btn-block btn-info" type="submit">Publish Selected</button>
                                {{Form::close()}}
                            </div>
                        @endif

                        @if($c_user->user_type_id == 1 || $c_user->hasAccess('physicianSchedule.add'))
                            <div class="col-md-3">
                                <a href="{{route('addPhysicianSchedule')}}">
                                    <button class="btn btn-block btn-default">Add Physician Schedule</button>
                                </a>
                            </div>
                        @endif
                        @if($c_user->user_type_id == 1 || $c_user->hasAccess('physicianSchedule.importExcel'))
                            <div class="col-md-2">
                                <a href="{{route('importExcelPhysicianSchedule')}}">
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
                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('physicianSchedule.on-off'))
                                        <th></th>
                                    @endif
                                    <th>#</th>
                                    <th>Physician name</th>
                                    <th>Clinic name</th>
                                    <th>Clinic Schedule name</th>
                                    <th>Start date</th>
                                    <th>End date</th>
                                    <th>Status</th>
                                    <th>Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($physicianSchedules as $val)
                                    <tr>
                                        @if($c_user->user_type_id == 1 || $c_user->hasAccess('physicianSchedule.on-off'))
                                            <td>
                                                @if($val['publish'] == 2)
                                                    <input clinic_schedule_id="{{$val['id']}}"
                                                           class="checkbox-inline publishCheckbox checkbox1"
                                                           type="checkbox"
                                                           autocomplete="off">
                                                @endif
                                            </td>
                                        @endif
                                        <td>{{$val['id']}}</td>
                                        <td>
                                            <div style="width: 200px;">{{$val['full_name']}}</div>
                                        </td>
                                        <td>{{$val['name']}}</td>
                                        <td>{{$val['clinic_schedule_name']}}</td>
                                        <td>{{$val['start_date']}}</td>
                                        <td>{{$val['end_date']}}</td>
                                        <td>
                                            @if($val['publish'] == 1)
                                                <span style="color:green">Published</span>
                                            @else
                                                <span style="color:red">Unpublished</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" style="width: 350px;">
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('physicianSchedule.edit'))

                                                    <a class="btn btn-default"
                                                       href="{{route('editPhysicianSchedule', $val['id'])}}">Edit</a>

                                                @endif
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('physicianSchedule.delete'))
                                                    <a class="ask-me btn btn-danger"
                                                       href="{{route('deletePhysicianSchedule', $val['id'])}}">Delete</a>

                                                @endif
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('physicianSchedule.on-off'))
                                                    <a class="ask-me btn btn-info"
                                                       href="{{route('changeStatusPhysicianSchedule', $val['id'])}}">
                                                        @if($val['publish'] == 1)
                                                            Unpublish
                                                        @else
                                                            Publish
                                                        @endif
                                                    </a>

                                                @endif
                                                @if($val['end_date'] >= date('Y-m-d') && ($c_user->user_type_id == 1 || $c_user->hasAccess('physicianSchedule.edit')))
                                                    <a class="btn btn-warning changeDatePhysicianScheduleBtn"
                                                       schedule_id="{{$val['id']}}">Change Dates</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{$physicianSchedules->appends(Input::except('_token'))->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="changeDatePhysicianScheduleModal" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Change Dates</h4>
                </div>
                {{Form::open(array('role'=>"form", 'route' => 'changeDatePhysicianSchedule'))}}
                <div class="modal-body col-md-12">
                    <div class="form-group col-md-6">
                        <label>Start Date</label>

                        <div id="modal_start_date"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>End Date</label>
                        <input required id="modal_end_date" type="text" data-date-format="yyyy-mm-dd" name="end_date"
                               class="form-control datepicker">
                        <input id="modal_schedule_id" type="hidden" name="id">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
@stop