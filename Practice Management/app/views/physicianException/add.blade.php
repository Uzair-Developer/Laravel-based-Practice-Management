@extends('layout/main')

@section('title')
    - {{$physicianException['user_id'] ? 'Edit' : 'Add'}} Physician Exception
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datetimepicker/jquery.datetimepicker.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('plugins/datetimepicker/jquery.datetimepicker.full.js')}}"></script>
    <script src="{{asset('plugins/jquery-mask/jquery.mask.min.js')}}"></script>
    <script>
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();

            //Datepicker
            $('.datepicker').datepicker({
                startDate: "1d",
                todayHighlight: true,
                autoclose: true
            });

            $('.datepicker2').datepicker({
                todayHighlight: true,
                autoclose: true
            });

            $('.time-mask').mask('00:00:00');

            $("#to_date").change(function (e) {
                var to_date = $(this).val();
                if ($("#from_date").val().length) {
                    if (to_date < $("#from_date").val()) {
                        $(this).val($("#from_date").val());
                    }
                }
            });

            $("#from_date").change(function (e) {
                var from_date = $(this).val();
                if ($("#to_date").val().length) {
                    if (from_date > $("#to_date").val()) {
                        $(this).val($("#to_date").val());
                    }
                }
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
                    $("#from_time, #to_time").removeAttr('required');
                } else {
                    $(".bootstrap-timepicker").show();
                    $("#from_time, #to_time").attr('required', 'required');
                }
            });
            @if($physicianException['all_day'])
                $(".bootstrap-timepicker").hide();
            $("#from_time, #to_time").removeAttr('required');
            @endif

            $("#selectHospital2").change(function (e) {
                        $("#physicianTimeHtml").html('');
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

            $("#selectClinic2").change(function () {
                $("#physicianTimeHtml").html('');
                $("#selectPhysician2").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getPhysicianByClinic')}}',
                    method: 'POST',
                    data: {
                        clinic_id: $(this).val()
                    },
                    success: function (data) {
                        $("#selectPhysician2").html(data.physiciansHtml).attr('disabled', false);
                    }
                });
            });
            $("#selectPhysician2").change(function () {
                $("#physicianTimeHtml").html('');
                $("#physician_schedule_id, #physician_schedule_id2, #physician_schedule_id3").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getPhysicianScheduleByPhysicianId')}}',
                    method: 'POST',
                    data: {
                        physician_id: $(this).val(),
                        clinic_id: $("#selectClinic2").val()
                    },
                    success: function (data) {
                        $("#physician_schedule_id, #physician_schedule_id2, #physician_schedule_id3").html(data).attr('disabled', false).select2();
                        $("#physicianScheduleDiv").html('');
                        $("#physicianExceptionDiv").html('');
                    }
                });
            });

            $("#physician_schedule_id2").change(function () {
                $.ajax({
                    url: '{{route('getPhysicianScheduleView')}}',
                    method: 'POST',
                    data: {
                        physician_schedule_id: $(this).val()
                    },
                    success: function (data) {
                        $("#physicianScheduleDiv").html(data.schedulesHtml);
                    }
                });
            });

            $("#physician_schedule_id3").change(function () {
                $.ajax({
                    url: '{{route('getByPhysicianSchedule')}}',
                    method: 'POST',
                    data: {
                        physician_schedule_id: $(this).val()
                    },
                    success: function (data) {
                        $("#physicianExceptionDiv").html(data.exceptionHtml);
                    }
                });
            });

            $("#physician_schedule_id").change(function () {
                $("#physicianTimeHtml").html('');
                $.ajax({
                    url: '{{route('getPhysicianSchedule')}}',
                    method: 'POST',
                    data: {
                        physician_schedule_id: $(this).val(),
                        withScheduleTimesSelect: true
                    },
                    success: function (data) {
                        if (data.end_date < '{{date('Y-m-d')}}') {
                            alert('Sorry You Selected An Old Date Range');
                            $("#from_date").val('');
                            $("#to_date").val('');
                        } else {
                            $("#from_date").val('{{date('Y-m-d')}}');
                            $("#to_date").val(data.end_date);
                        }

                        if (data.start_date > '{{date('Y-m-d')}}') {
                            $("#from_date").val(data.start_date);
                        }
                        $("#schedule_times").html('').html('<option value="">Choose</option>');
                        var timesLength = data.scheduleTimesSelect.length;
                        for (var i = 0; i < timesLength; i++) {
                            $("#schedule_times").append('<option value="' + data.scheduleTimesSelect[i] + '">' + data.scheduleTimesSelect[i] + '</option>')
                        }
                        $("#schedule_times").select2();
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

            function refreshAvailableTimes(date, physician_schedule_id) {
                $.ajax({
                    url: '{{route('getPhysicianAvailableTime')}}',
                    method: 'POST',
                    data: {
                        date: date,
                        physician_id: $("#selectPhysician2").val(),
                        physician_schedule_id: physician_schedule_id,
                        with_exception: true
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#physicianTimeHtml").html(data.physicianTimeHtml);
                    }
                });
            }

            window.setInterval(function () {
                var physician_schedule_id = $("#physician_schedule_id").val();
                if (physician_schedule_id.length > 0) {
                    var date = $("#date").val();
                    if (date.length > 0) {
                        refreshAvailableTimes(date, physician_schedule_id);
                    }
                }
            }, 30000);

            $("#get_date").click(function (e) {
                var physician_schedule_id = $("#physician_schedule_id").val();
                if (physician_schedule_id.length > 0) {
                    var date = $("#date").val();
                    refreshAvailableTimes(date, physician_schedule_id);
                } else {
                    alert('Physician schedule is required!');
                }
            });

            $("#date").change(function (e) {
                $("#physicianTimeHtml").html('');
            });

            var checkedArray = new Array();
            $(document).on('change', '.unlockSlotCheckbox', function () {
                if ($(".unlockSlotCheckbox:checked").length > 0) {
                    $("#unlockSlotSave").show();
                }
                else {
                    $("#unlockSlotSave").hide();
                }
                var checkedValues = $('.unlockSlotCheckbox:checked').map(function () {
                    return $(this).attr('times');
                }).get();
                $('#unlockSlotInput').val(checkedValues);
            });

            $(document).on('click', '#unlockSlotSave', function (e) {
                if (confirm('Are You Sure?')) {
                    var physician_schedule_id = $("#physician_schedule_id").val();
                    if (physician_schedule_id.length > 0) {
                        var date = $("#date").val();
                        $.ajax({
                            url: "{{route('unLockSlotReservation')}}",
                            method: 'POST',
                            data: {
                                times: $("#unlockSlotInput").val(),
                                date: date,
                                physician_id: $("#selectPhysician2").val(),
                                physician_schedule_id: physician_schedule_id,
                                with_exception: true
                            },
                            success: function (data) {
                                $("#physicianTimeHtml").html(data.physicianTimeHtml);
                            }
                        });
                    } else {
                        alert('Physician schedule is required!');
                    }
                }
            });
            $(document).on('click', '.unlockSlotBtn', function (e) {
                var physician_schedule_id = $("#physician_schedule_id").val();
                if (physician_schedule_id.length > 0) {
                    if (confirm('Are You Sure?')) {
                        var date = $("#date").val();
                        $.ajax({
                            url: "{{route('unLockSlotReservation')}}",
                            method: 'POST',
                            data: {
                                times: $(this).attr('times'),
                                date: date,
                                physician_id: $("#selectPhysician2").val(),
                                physician_schedule_id: physician_schedule_id,
                                with_exception: true
                            },
                            success: function (data) {
                                $("#physicianTimeHtml").html(data.physicianTimeHtml);
                            }
                        });
                    }
                } else {
                    alert('Physician schedule is required!');
                }
            });

            $(document).on('click', '.lockSlotBtn', function (e) {
                $("#modal_from_time").val($(this).attr('time').trim());
                $("#modal_to_time").val($(this).attr('to_time').trim());
                $("#modalLockSlot").modal('show');
            });

            $("#saveLockSlot").click(function (e) {
                var physician_schedule_id = $("#physician_schedule_id").val();
                if (physician_schedule_id.length > 0) {
                    var date = $("#date").val();
                    var reason_id = $("#modal_reason_id").val();
                    if (reason_id.length > 0) {
                        $.ajax({
                            url: "{{route('lockSlotReservation')}}",
                            method: 'POST',
                            data: {
                                time: $("#modal_from_time").val(),
                                to_time: $("#modal_to_time").val(),
                                reason_id: reason_id,
                                date: date,
                                physician_id: $("#selectPhysician2").val(),
                                physician_schedule_id: physician_schedule_id,
                                with_exception: true
                            },
                            success: function (data) {
                                $("#physicianTimeHtml").html(data.physicianTimeHtml);
                                $("#modalLockSlot").modal('hide');
                            }
                        });
                    } else {
                        alert('Exception Reason is required!');
                    }
                } else {
                    alert('Physician schedule is required!');
                }
            });

            @if(Input::old('hospital_id') || Session::has('physicianException'))
            $("#selectHospital2").val('{{Input::old('hospital_id') ? Input::old('hospital_id') : Session::get('physicianException')['hospital_id']}}').select2();
            $.ajax({
                url: '{{route('getClinicsByHospitalId')}}',
                method: 'POST',
                data: {
                    hospital_id: $("#selectHospital2").val()
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#selectClinic2").removeAttr('disabled').html(data).select2();
                    @if(Input::old('clinic_id') || Session::has('physicianException'))
                    $("#selectClinic2").val('{{Input::old('clinic_id') ? Input::old('clinic_id') : Session::get('physicianException')['clinic_id']}}').select2();
                    $.ajax({
                        url: '{{route('getPhysicianByClinic')}}',
                        method: 'POST',
                        data: {
                            clinic_id: $("#selectClinic2").val()
                        },
                        success: function (data) {
                            $("#selectPhysician2").html(data.physiciansHtml).attr('disabled', false);
                            @if(Input::old('user_id') || Session::has('physicianException'))
                            $("#selectPhysician2").val('{{Input::old('user_id') ? Input::old('user_id') : Session::get('physicianException')['user_id']}}').select2();
                            $.ajax({
                                url: '{{route('getPhysicianScheduleByPhysicianId')}}',
                                method: 'POST',
                                data: {
                                    physician_id: $("#selectPhysician2").val()
                                },
                                success: function (data) {
                                    $("#physician_schedule_id").html(data).val('{{Input::old('physician_schedule_id') ? Input::old('physician_schedule_id') : Session::get('physicianException')['physician_schedule_id']}}').select2();
                                    $("#physician_schedule_id2, #physician_schedule_id3").html(data).select2();
                                    $.ajax({
                                        url: '{{route('getPhysicianSchedule')}}',
                                        method: 'POST',
                                        data: {
                                            physician_schedule_id: $("#physician_schedule_id").val(),
                                            withScheduleTimesSelect: true
                                        },
                                        success: function (data) {
                                            if (data.end_date < '{{date('Y-m-d')}}') {
                                                alert('Sorry You Selected An Old Date Range');
                                                $("#from_date").val('');
                                                $("#to_date").val('');
                                            } else {
                                                $("#from_date").val('{{date('Y-m-d')}}');
                                                $("#to_date").val(data.end_date);
                                            }
                                            if (data.start_date > '{{date('Y-m-d')}}') {
                                                $("#from_date").val(data.start_date);
                                            }
                                            $("#schedule_times").html('').html('<option value="">Choose</option>');
                                            var timesLength = data.scheduleTimesSelect.length;
                                            for (var i = 0; i < timesLength; i++) {
                                                $("#schedule_times").append('<option value="' + data.scheduleTimesSelect[i] + '">' + data.scheduleTimesSelect[i] + '</option>')
                                            }
                                            $("#schedule_times").select2();
                                        }
                                    });
                                }
                            });
                            @endif
                        }
                    });
                    @endif
                }
            });
            @endif

            @if($physicianException['effect'] == 1 || Input::old('effect') == 1)
                $("#div_reason1").show();
            $("#div_reason2").hide();
            $("#reason_id").attr('required', 'required');
            $("#reason_id2").removeAttr('required');
            @elseif($physicianException['effect'] == 2 ||  Input::old('effect') == 2)
                $("#div_reason2").show();
            $("#div_reason1").hide();
            $("#reason_id").removeAttr('required');
            $("#reason_id2").attr('required', 'required');
            @endif

            @if($physician_schedule)
            $.ajax({
                        url: '{{route('getPhysicianScheduleView')}}',
                        method: 'POST',
                        data: {
                            physician_schedule_id: $('#physician_schedule_id2').val()
                        },
                        success: function (data) {
                            $("#physicianScheduleDiv").html(data.schedulesHtml);
                        }
                    });
            $.ajax({
                url: '{{route('getByPhysicianSchedule')}}',
                method: 'POST',
                data: {
                    physician_schedule_id: $("#physician_schedule_id3").val()
                },
                success: function (data) {
                    $("#physicianExceptionDiv").html(data.exceptionHtml);
                }
            });
            @endif

            @if($physicianException['from_date'] && $physicianException['from_date'] < date('Y-m-d'))
                $("#from_date").val('{{date('Y-m-d')}}');
            @endif






        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            <a data-target="#ModalDiscoverPhysicianSchedule" data-toggle="modal"
               class="btn btn-primary">Discover Schedules</a>

            <a style="margin-left: 20px" data-target="#ModalReviewException" data-toggle="modal"
               class="btn btn-primary">Review Exceptions</a>
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <!-- form start -->
                    <div class="box-header">
                        {{$physicianException['user_id'] ? 'Edit' : 'Add'}} Physician Exception
                    </div>
                    {{Form::open()}}
                    <div class="box-body">
                        <div class="form-group col-md-6">
                            <label>Hospital</label>
                            <br>
                            <select @if($physicianException['user_id']) disabled @endif autocomplete="off"
                                    id="selectHospital2" name="hospital_id"
                                    class="form-control select2">
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
                            <select @if($physicianException['user_id']) disabled @endif autocomplete="off"
                                    id="selectClinic2" name="clinic_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Physicians</label>
                            <br>
                            <select @if($physicianException['user_id']) disabled @endif autocomplete="off"
                                    id="selectPhysician2" class="form-control select2"
                                    name="user_id">
                                <option value="">Choose</option>
                                @if($physician)
                                    {{$physician}}
                                @endif
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Physician Schedules</label>
                            <select @if($physicianException['user_id']) disabled @endif id="physician_schedule_id"
                                    required
                                    name="physician_schedule_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                @if($physician_schedule)
                                    {{$physician_schedule}}
                                @endif
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>From Date</label>
                            <input autocomplete="off" id="from_date" type="text" data-date-format="yyyy-mm-dd"
                                   value="{{Input::old('from_date') ? Input::old('from_date') : $physicianException['from_date']}}"
                                   name="from_date" class="form-control datepicker">
                        </div>

                        <div class="form-group col-md-6">
                            <label>To Date</label>
                            <input autocomplete="off" id="to_date" type="text" data-date-format="yyyy-mm-dd"
                                   value="{{Input::old('to_date') ? Input::old('to_date') : $physicianException['to_date']}}"
                                   name="to_date" class="form-control datepicker">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Effect Reason?</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input autocomplete="off" id="reason1" required class="checkbox-inline"
                                           name="effect"
                                           @if(Input::old('effect') == 1) checked @endif
                                           @if(!Input::old('effect') && $physicianException['effect'] == 1) checked
                                           @endif
                                           type="radio" value="1"> Effect
                                </label>
                                <label class="checkbox-inline">
                                    <input autocomplete="off" id="reason2" required class="checkbox-inline"
                                           name="effect"
                                           @if(Input::old('effect') == 2) checked @endif
                                           @if(!Input::old('effect') && $physicianException['effect'] == 2) checked
                                           @endif
                                           type="radio" value="2"> Non Effect
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-6" id="div_reason1">
                            <label>Exception Reason</label>
                            <select autocomplete="off" class="form-control select2" name="reason_id" id="reason_id">
                                <option value="">Choose</option>
                                @foreach($reasons as $val)
                                    @if($val['effect'] == 1)
                                        @if(Input::old('reason_id'))
                                            <option @if(Input::old('reason_id') == $val['id']) selected
                                                    @endif value="{{$val['id']}}">{{$val['name']}}</option>
                                        @else
                                            <option @if($physicianException['reason_id'] == $val['id']) selected
                                                    @endif value="{{$val['id']}}">{{$val['name']}}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6" id="div_reason2" style="display: none;">
                            <label>Exception Reason</label>
                            <br>
                            <select autocomplete="off" class="form-control select2" name="reason_id2" id="reason_id2">
                                <option value="">Choose</option>
                                @foreach($reasons as $val)
                                    @if($val['effect'] == 2)
                                        @if(Input::old('reason_id'))
                                            <option @if(Input::old('reason_id') == $val['id']) selected
                                                    @endif value="{{$val['id']}}">{{$val['name']}}</option>
                                        @else
                                            <option @if($physicianException['reason_id'] == $val['id']) selected
                                                    @endif value="{{$val['id']}}">{{$val['name']}}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label>All Day</label>
                            <input id="all_day"
                                   {{$physicianException['all_day'] ? 'checked' : ''}} autocomplete="off"
                                   class="icheckbox_flat-blue" name="all_day" type="checkbox" value="1">
                        </div>

                        <div class="bootstrap-timepicker col-md-6">
                            <label>From Time</label>

                            <div class="input-group">
                                <input type="text" id="from_time" required
                                       value="{{Input::old('from_time') ? Input::old('from_time') : $physicianException['from_time']}}"
                                       name="from_time" class="form-control time-mask">

                                <div class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                </div>
                            </div>
                            <!-- /.input group -->
                        </div>

                        <div class="bootstrap-timepicker col-md-6">
                            <label>To Time</label>

                            <div class="input-group">
                                <input type="text" id="to_time" required
                                       value="{{Input::old('to_time') ? Input::old('to_time') : $physicianException['to_time']}}"
                                       name="to_time" class="form-control time-mask">

                                <div class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                </div>
                            </div>
                            <!-- /.input group -->
                        </div>

                        <div class="form-group col-md-12" style="margin-top: 20px">
                            <label>Apply On Schedule Times</label>
                            <br>
                            <select autocomplete="off" class="form-control select2" name="schedule_times[]"
                                    id="schedule_times" multiple>
                                <option value="">Choose</option>
                                @if($physicianException['user_id'])
                                    <?php $schedule_timesArray = explode(',', $physicianException['schedule_times']); ?>
                                    @foreach($schedule_times as $key => $val)
                                        <option @if(in_array($val, $schedule_timesArray)) selected
                                                @endif value="{{$val}}">{{$val}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Notes</label>
                            <textarea name="notes"
                                      class="form-control">{{Input::old('notes') ? Input::old('notes') : $physicianException['notes']}}</textarea>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        @if (($c_user->user_type_id == 1 ) || ($c_user->user_type_id != 7 && $c_user->hasAccess('physicianException.changeStatus')))
                            <button class="btn btn-primary" type="submit">Save And Approve</button>
                        @else
                            <button class="btn btn-primary" type="submit">Save</button>
                        @endif

                        <a href="{{route('physicianExceptions')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>

            </div>

            <div class="col-md-6">
                <div class="box box-primary">
                    <!-- form start -->
                    <div class="box-header">
                        Lock & Unlock Slots
                    </div>
                    <div class="box-body">

                        <div class="form-group col-md-6">
                            <label>Date</label>
                            <input id="date" type="text" data-date-format="yyyy-mm-dd"
                                   value="{{date('Y-m-d')}}"
                                   name="date" class="form-control datepicker2">
                        </div>
                        <div class="form-group col-md-2">
                            <label>&nbsp;</label>
                            <button id="get_date" class="form-control btn btn-info">Get</button>
                        </div>

                        <div class="form-group col-md-12" id="physicianTimeHtml">

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

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
                        <label>Physician Schedules</label>
                        <br>
                        <select id="physician_schedule_id2" name="physician_schedule_id" style=""
                                class="form-control select2">
                            <option selected value="">Choose</option>
                            @if($physician_schedule)
                                {{$physician_schedule}}
                            @endif
                        </select>
                    </div>
                    <div class="form-group col-md-12" id="physicianScheduleDiv">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ModalReviewException" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="width: 1000px">
            <div class="modal-content col-md-12">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Review Physician Exception</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group col-md-6">
                        <label>Physician Schedules</label>
                        <br>
                        <select id="physician_schedule_id3" name="physician_schedule_id" style=""
                                class="form-control select2">
                            <option value="">Choose</option>
                            @if($physician_schedule)
                                {{$physician_schedule}}
                            @endif
                        </select>
                    </div>
                    <div class="form-group col-md-12" id="physicianExceptionDiv">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalLockSlot" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content col-md-12">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Effect Exception Reason</h4>
                </div>
                <div class="modal-body col-md-12">
                    <div class="form-group col-md-6">
                        <label>Exception Reason</label>
                        <br>
                        <select autocomplete="off" class="form-control select2" id="modal_reason_id"
                                style="">
                            <option value="">Choose</option>
                            @foreach($reasons as $val)
                                @if($val['effect'] == 1)
                                    <option value="{{$val['id']}}">{{$val['name']}}</option>
                                @endif
                            @endforeach
                        </select>
                        <input type="hidden" id="modal_from_time">
                        <input type="hidden" id="modal_to_time">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="saveLockSlot">Save</button>
                </div>
            </div>
        </div>
    </div>
@stop