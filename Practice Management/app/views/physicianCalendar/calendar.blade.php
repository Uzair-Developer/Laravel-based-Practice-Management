@extends('layout/main')

@section('title')
    - Physician Calendar
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
    <link href='{{asset('plugins/fullcalendar-scheduler/lib/fullcalendar.min.css')}}' rel='stylesheet'/>
    <link href='{{asset('plugins/fullcalendar-scheduler/lib/fullcalendar.print.css')}}' rel='stylesheet' media='print'/>
    <link rel="stylesheet" href="{{asset('plugins/datetimepicker/jquery.datetimepicker.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src='{{asset('plugins/fullcalendar-scheduler/lib/moment.min.js')}}'></script>
    <script src='{{asset('plugins/fullcalendar-scheduler/lib/fullcalendar.min.js')}}'></script>
    <script src="{{asset('plugins/datetimepicker/jquery.datetimepicker.full.js')}}"></script>
    <script>
        $(function () {

            //Datepicker
            $('.datepicker').datepicker({
                startDate: "1d",
                todayHighlight: true,
                autoclose: true
            });

            $('.timepicker').datetimepicker({
                datepicker: false,
                format: 'H:i',
                step: 5,
                minDate: '{{date('Y-m-d')}}'
            });

            $('#all_day').change(function () {
                if ($(this).is(':checked')) {
                    $(".bootstrap-timepicker").hide();
                } else {
                    $(".bootstrap-timepicker").show();
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
            $("#selectHospital3").change(function (e) {
                $("#selectClinic3").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getClinicsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectClinic3").removeAttr('disabled').html(data).select2();
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

            $("#selectClinic3").change(function (e) {
                $("#selectPhysician3").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getPhysicianByClinicId')}}',
                    method: 'POST',
                    data: {
                        clinic_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectPhysician3").removeAttr('disabled').html(data).select2();
                    }
                });
            });
            $("#selectPhysician2").change(function (e) {
                var physician_id = $(this).val();
                var clinic_id = $("#selectClinic2").val();
                $("#calendar_div").html('').append("<div id='calendar'></div>");
                $('#calendar').fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'agendaWeek,agendaDay'
                    },
                    defaultView: 'agendaDay',
                    eventLimit: true, // allow "more" link when too many events
                    events: { // you can also specify a plain string like 'json/events.json'
                        url: '{{route('physicianCalendarGetEvents')}}?physician_id=' + physician_id + '&clinic_id=' + clinic_id,
                        error: function () {
                            $('#script-warning').show();
                        }
                    }
                });
            });
            $("#addPhysicianExceptionForm").submit(function (e) {
                e.preventDefault();
                var o = getFormData($(this).serializeArray());
                $.ajax({
                    url: '{{route('addPhysicianExceptionPopUp')}}',
                    method: 'POST',
                    data: {
                        physicianException: o
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        alert(data.message);
                        $("#addPhysicianException").modal('hide');
                    }
                });
            });

            $("#selectHospital4").change(function (e) {
                $("#selectClinic4").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getClinicsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectClinic4").removeAttr('disabled').html(data).select2();
                    }
                });
            });

            $("#selectClinic4").change(function () {
                $("#clinic_schedule_id").attr('disabled', true);
                $("#selectPhysician4").attr('disabled', true);
                $.ajax({
                    url: '{{route('getPhysicianByClinic')}}',
                    method: 'POST',
                    data: {
                        clinic_id: $(this).val()
                    },
                    success: function (data) {
                        $("#clinic_schedule_id").html(data.schedulesHtml).attr('disabled', false).select2();
                        $("#selectPhysician4").html(data.physiciansHtml).attr('disabled', false).select2();
                    }
                });
            });

            $("#selectPhysician4").change(function () {
                var clinic_schedule_id = $("#clinic_schedule_id").val();
                if (clinic_schedule_id.length == 0) {
                    alert('Please Chose One Clinic Schedule');
                    $(this).val('').select2();
                    return;
                }
                $.ajax({
                    url: '{{route('getPhysicianScheduleByClinicSchedule')}}',
                    method: 'POST',
                    data: {
                        clinic_schedule_id: clinic_schedule_id,
                        user_id: $(this).val()
                    },
                    success: function (data) {
                        $("#physicianScheduleDiv").html(data.schedulesHtml);
                    }
                });
            });

            $("#physician_schedule_id").change(function () {
                $("#physicianTimeHtml").html('');
                $.ajax({
                    url: '{{route('getPhysicianSchedule')}}',
                    method: 'POST',
                    data: {
                        physician_schedule_id: $(this).val()
                    },
                    success: function (data) {
                        $("#from_date").val(data.start_date);
                        $("#to_date").val(data.end_date);
                        var times = [];
                        if (data.sat_start_time_1 != null && data.sat_start_time_1 != undefined && data.sat_start_time_1 != '') {
                            times.push(data.sat_start_time_1 + ' ' + data.sat_end_time_1);
                        }
                        if (data.sun_start_time_1 != null && data.sun_start_time_1 != undefined && data.sun_start_time_1 != ''
                                && $.inArray(data.sun_start_time_1 + ' ' + data.sun_end_time_1, times) == -1) {
                            times.push(data.sun_start_time_1 + ' ' + data.sun_end_time_1);
                        }
                        if (data.mon_start_time_1 != null && data.mon_start_time_1 != undefined && data.mon_start_time_1 != ''
                                && $.inArray(data.mon_start_time_1 + ' ' + data.mon_end_time_1, times) == -1) {
                            times.push(data.mon_start_time_1 + ' ' + data.mon_end_time_1);
                        }
                        if (data.tues_start_time_1 != null && data.tues_start_time_1 != undefined && data.tues_start_time_1 != ''
                                && $.inArray(data.tues_start_time_1 + ' ' + data.tues_end_time_1, times) == -1) {
                            times.push(data.tues_start_time_1 + ' ' + data.tues_end_time_1);
                        }
                        if (data.wed_end_time_1 != null && data.wed_end_time_1 != undefined && data.wed_end_time_1 != ''
                                && $.inArray(data.wed_start_time_1 + ' ' + data.wed_end_time_1, times) == -1) {
                            times.push(data.wed_start_time_1 + ' ' + data.wed_end_time_1);
                        }
                        if (data.thurs_end_time_1 != null && data.thurs_end_time_1 != undefined && data.thurs_end_time_1 != ''
                                && $.inArray(data.thurs_start_time_1 + ' ' + data.thurs_end_time_1, times) == -1) {
                            times.push(data.thurs_start_time_1 + ' ' + data.thurs_end_time_1);
                        }
                        if (data.fri_end_time_1 != null && data.fri_end_time_1 != undefined && data.fri_end_time_1 != ''
                                && $.inArray(data.fri_start_time_1 + ' ' + data.fri_end_time_1, times) == -1) {
                            times.push(data.fri_start_time_1 + ' ' + data.fri_end_time_1);
                        }
                        $("#schedule_times").html('').html('<option value="">Choose</option>');
                        var timesLength = times.length;
                        for (var i = 0; i < timesLength; i++) {
                            $("#schedule_times").append('<option value="' + times[i] + '">' + times[i] + '</option>')
                        }
                        $("#schedule_times").select2();
                    }
                });
            });

            $("#selectPhysician3").change(function () {
                $("#physicianTimeHtml").html('');
                $("#physician_schedule_id").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getPhysicianScheduleByPhysicianId')}}',
                    method: 'POST',
                    data: {
                        physician_id: $(this).val()
                    },
                    success: function (data) {
                        $("#physician_schedule_id").html(data).attr('disabled', false).select2();
                    }
                });
            });

            $("#reason1").click(function (e) {
                $("#div_reason1").show();
                $("#div_reason2").hide();
                $("#reason_id").attr('required', 'required');
                $("#reason_id2").removeAttr('required');
            });
            $("#reason2").click(function (e) {
                $("#div_reason2").show();
                $("#div_reason1").hide();
                $("#reason_id").removeAttr('required');
                $("#reason_id2").attr('required', 'required');
            });

            @if($c_user->user_type_id == 7)
            $.ajax({
                        url: '{{route('getAnyClinicAndHospitalByPhysician')}}',
                        method: 'POST',
                        data: {
                            physician_id: '{{$c_user->id}}'
                        },
                        headers: {token: '{{csrf_token()}}'},
                        success: function (data) {
                            if (data) {
                                $("#selectHospital2, #selectHospital3").val(data.hospital_id).select2();
                                $.ajax({
                                    url: '{{route('getClinicsByHospitalId')}}',
                                    method: 'POST',
                                    data: {
                                        hospital_id: data.hospital_id
                                    },
                                    headers: {token: '{{csrf_token()}}'},
                                    success: function (data2) {
                                        $("#selectClinic2, #selectClinic3").html(data2).val(data.clinic_id).select2();
                                        $.ajax({
                                            url: '{{route('getPhysicianByClinicId')}}',
                                            method: 'POST',
                                            data: {
                                                clinic_id: data.clinic_id
                                            },
                                            headers: {token: '{{csrf_token()}}'},
                                            success: function (data3) {
                                                $("#selectPhysician2, #selectPhysician3").html(data3).val('{{$c_user->id}}').select2();
                                                var physician_id = '{{$c_user->id}}';
                                                var clinic_id = data.clinic_id;
                                                $("#calendar_div").html('').append("<div id='calendar'></div>");
                                                $('#calendar').fullCalendar({
                                                    header: {
                                                        left: 'prev,next today',
                                                        center: 'title',
                                                        right: 'agendaWeek,agendaDay'
                                                    },
                                                    defaultView: 'agendaDay',
                                                    eventLimit: true, // allow "more" link when too many events
                                                    events: { // you can also specify a plain string like 'json/events.json'
                                                        url: '{{route('physicianCalendarGetEvents')}}?physician_id=' + physician_id + '&clinic_id=' + clinic_id,
                                                        error: function () {
                                                            $('#script-warning').show();
                                                        }
                                                    }
                                                });
                                            }
                                        });
                                    }
                                });
                            }
                        }
                    });
            @endif



        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Physician Calendar
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    <div class="box-header">
                        Search
                        @if($c_user->user_type_id == 1 || $c_user->hasAccess('physicianException.add'))
                            {{--<div class="pull-right col-md-3">--}}
                                {{--<a data-target="#addPhysicianException" data-toggle="modal">--}}
                                    {{--<button class="btn btn-block btn-default">Add Physician Exception</button>--}}
                                {{--</a>--}}
                                {{--<br>--}}
                            {{--</div>--}}
                        @endif
                        <a style="margin-left: 20px" data-target="#ModalDiscoverPhysicianSchedule" data-toggle="modal"
                           class="btn btn-primary">Discover Schedules</a>
                    </div>
                    <div class="box-body">
                        <div class="form-group col-md-3">
                            <label>Hospital</label>
                            <select autocomplete="off" id="selectHospital2" name="hospital_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    <option value="{{$val['id']}}" @if(Input::get('hospital_id') == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Clinic</label>
                            <select autocomplete="off" id="selectClinic2" name="clinic_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Physician</label>
                            <select autocomplete="off" id="selectPhysician2" name="physician_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>
                        <div class="col-md-12" id="calendar_div">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="addPhysicianException" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Add Physician Exception</h4>
                </div>
                {{Form::open(array('role'=>"form", 'id' => 'addPhysicianExceptionForm'))}}
                <div class="box-body">
                    <div class="form-group col-md-6">
                        <label>Hospital</label>
                        <br>
                        <select required autocomplete="off" id="selectHospital3" name="hospital_id"
                                class="form-control select2" style="width:250px">
                            <option value="">Choose</option>
                            @foreach($hospitals as $val)
                                <option value="{{$val['id']}}" @if(Input::old('hospital_id') == $val['id'])
                                selected @endif>{{$val['name']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Clinic</label>
                        <br>
                        <select required autocomplete="off" id="selectClinic3" name="clinic_id"
                                class="form-control select2" style="width:250px">
                            <option value="">Choose</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Physicians</label>
                        <br>
                        <select required autocomplete="off" id="selectPhysician3" class="form-control select2"
                                name="user_id" style="width:250px">
                            <option value="">Choose</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Physician Schedules</label>
                        <br>
                        <select id="physician_schedule_id" required style="width: 250px;"
                                name="physician_schedule_id" class="form-control select2">
                            <option value="">Choose</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Effect Reason?</label>

                        <div class="checkbox-list">
                            <label class="checkbox-inline">
                                <input autocomplete="off" id="reason1" required class="checkbox-inline"
                                       name="effect"
                                       type="radio" value="1"> Effect
                            </label>
                            <label class="checkbox-inline">
                                <input autocomplete="off" id="reason2" required class="checkbox-inline"
                                       name="effect"
                                       type="radio" value="2"> Non Effect
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-6" id="div_reason1">
                        <label>Exception Reason</label>
                        <br>
                        <select autocomplete="off" class="form-control select2" style="width: 250px;"
                                name="reason_id" id="reason_id">
                            <option value="">Choose</option>
                            @foreach($reasons as $val)
                                @if($val['effect'] == 1)
                                    <option value="{{$val['id']}}">{{$val['name']}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6" id="div_reason2" style="display: none;">
                        <label>Exception Reason</label>
                        <br>
                        <select autocomplete="off" class="form-control select2" name="reason_id2" id="reason_id2"
                                style="width: 250px">
                            <option value="">Choose</option>
                            @foreach($reasons as $val)
                                @if($val['effect'] == 2)
                                    <option value="{{$val['id']}}">{{$val['name']}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>From Date</label>
                        <input required type="text" data-date-format="yyyy-mm-dd"
                               value="{{Input::old('from_date')}}" id="from_date"
                               name="from_date" class="form-control datepicker">
                    </div>

                    <div class="form-group col-md-6">
                        <label>To Date</label>
                        <input required type="text" data-date-format="yyyy-mm-dd"
                               value="{{Input::old('to_date')}}" id="to_date"
                               name="to_date" class="form-control datepicker">
                    </div>

                    <div class="form-group col-md-12">
                        <label>All Day</label>
                        <input id="all_day" autocomplete="off" class="icheckbox_flat-blue" name="all_day"
                               type="checkbox">
                    </div>

                    <div class="bootstrap-timepicker col-md-6">
                        <label>From Time</label>

                        <div class="input-group">
                            <input type="text"
                                   value="{{Input::old('from_time')}}"
                                   name="from_time" class="form-control timepicker">

                            <div class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                        </div>
                        <!-- /.input group -->
                    </div>

                    <div class="bootstrap-timepicker col-md-6">
                        <label>To Time</label>

                        <div class="input-group">
                            <input type="text"
                                   value="{{Input::old('to_time')}}"
                                   name="to_time" class="form-control timepicker">

                            <div class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                        </div>
                        <!-- /.input group -->
                    </div>

                    <div class="form-group col-md-6" style="margin-top: 10px">
                        <label>Apply On Schedule Times</label>
                        <br>
                        <select autocomplete="off" class="form-control select2" name="schedule_times[]"
                                id="schedule_times" style="width: 260px;" multiple>
                            <option value="">Choose</option>

                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Notes</label>
                            <textarea name="notes"
                                      class="form-control">{{Input::old('notes')}}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>

    <div class="modal fade" id="ModalDiscoverPhysicianSchedule" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="width: 1000px">
            <div class="modal-content col-md-12">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Physician Schedules</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group col-md-6">
                        <label>Hospital</label>
                        <br>
                        <select autocomplete="off" id="selectHospital4" name="hospital_id"
                                class="form-control select2" style="width: 400px">
                            <option value="">Choose</option>
                            @foreach($hospitals as $val)
                                <option value="{{$val['id']}}" @if(Input::old('hospital_id') == $val['id'])
                                selected @endif>{{$val['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Clinics</label>
                        <br>
                        <select autocomplete="off" id="selectClinic4" name="clinic_id"
                                class="form-control select2" style="width: 400px">
                            <option value="">Choose</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Clinic Schedules</label>
                        <br>
                        <select autocomplete="off" id="clinic_schedule_id" name="clinic_schedule_id"
                                class="form-control select2" style="width: 400px">

                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Physicians</label>
                        <br>
                        <select autocomplete="off" id="selectPhysician4" name="user_id" class="form-control select2"
                                style="width: 400px">

                        </select>
                    </div>
                    <div class="form-group col-md-12" id="physicianScheduleDiv">
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop