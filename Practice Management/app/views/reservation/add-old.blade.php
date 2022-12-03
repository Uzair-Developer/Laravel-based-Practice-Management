@extends('layout/main')

@section('title')
    - Add Reservation
@stop

@section('header')
    <meta charset='utf-8'/>
    <link href='{{asset('plugins/fullcalendar-scheduler/lib/fullcalendar.min.css')}}' rel='stylesheet'/>
    <link href='{{asset('plugins/fullcalendar-scheduler/lib/fullcalendar.print.css')}}' rel='stylesheet' media='print'/>
    <link href='{{asset('plugins/fullcalendar-scheduler/scheduler.min.css')}}' rel='stylesheet'/>
    <style>
        #calendar {
            max-width: 1100px;
        }
    </style>
@stop

@section('footer')
    <script src='{{asset('plugins/fullcalendar-scheduler/lib/moment.min.js')}}'></script>
    <script src='{{asset('plugins/fullcalendar-scheduler/lib/fullcalendar.min.js')}}'></script>
    <script src='{{asset('plugins/fullcalendar-scheduler/scheduler.min.js')}}'></script>
    <script>
        $(function () {
            $("#reservationForm").submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: '{{route('createReservation')}}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (data) {
                        $("#myModal").modal('hide');
                        $("#user_id").val('');
                        $("#date").val('');
                        $("#your_turn").html('');

                        $("#time_from").html('');
                        $("#time_from_input").val('');

                        $("#time_to").html('');
                        $("#time_to_input").val('');

                        $('#calendar').fullCalendar( 'refetchEvents' );
                        alert('Added Successfully');
                    }
                });
            });

            $('#calendar').fullCalendar({
                aspectRatio: 1.8,
                scrollTime: '00:00', // undo default 6am scrollTime
                selectable: true,
                titleFormat: 'dddd D-MMMM-YYYY',
                header: {
                    left: 'today prev,next',
                    center: 'title',
                    right: 'timelineDay,timelineWeek'
                },
                defaultView: 'timelineDay',
                resourceLabelText: 'Physicians',
                resources: [
                        @foreach($physicians as $key => $val)
                        {
                        id: '{{$val['id']}}',
                        title: '{{$val['user_name'] . ' \n Shift 1 DayOff: ' . $val['dayOff_1']}}' +
                        '{{$val['dayOff_2'] ? '\n Shift 2 DayOff: ' . $val['dayOff_2'] : ''}}' +
                        '{{$val['dayOff_3'] ? '\n Shift 3 DayOff: ' . $val['dayOff_3'] : ''}}',
                        eventColor: 'green',
                        name: '{{$val['user_name']}}'
                    },
                    @endforeach
        ],

                events: { // you can also specify a plain string like 'json/events.json'
                    url: '{{route('reservationGetEvents')}}?clinic_id={{$clinicId}}',
                    error: function() {
                        $('#script-warning').show();
                    }
                },
                dayClick: function (date, jsEvent, view, resource) {
                    dayString = date.toString();
                    dayStringParts = dayString.split(' ');
                    dayStringParts[0] = dayStringParts[0].toLowerCase();
                    dayClick = date.format();
                    dateParts = dayClick.split('T');
                    timeClick = dateParts[1];
                    if (dateParts[0] >= '{{$clinicSchedule['start_date']}}' && dateParts[0] <= '{{$clinicSchedule['end_date']}}') {
                        popUp = false;
                        if (timeClick >= '{{$clinicSchedule['shift1_start_time']}}'
                                && timeClick <= '{{$clinicSchedule['shift1_end_time']}}') {
                            shift1_day_of = '{{$clinicSchedule['shift1_day_of']}}';
                            if (shift1_day_of.indexOf(dayStringParts[0]) > -1) {
                                alert('This day is day off for shift 1');
                            } else {
                                popUp = true;
                            }
                        }
                        if (timeClick >= '{{$clinicSchedule['shift2_start_time']}}'
                                && timeClick <= '{{$clinicSchedule['shift2_end_time']}}') {
                            shift2_day_of = '{{$clinicSchedule['shift2_day_of']}}';
                            if (shift2_day_of.indexOf(dayStringParts[0]) > -1) {
                                alert('This day is day off for shift 2');
                            } else {
                                popUp = true;
                            }
                        }
                        if (timeClick >= '{{$clinicSchedule['shift3_start_time']}}'
                                && timeClick <= '{{$clinicSchedule['shift3_end_time']}}') {
                            shift3_day_of = '{{$clinicSchedule['shift3_day_of']}}';
                            if (shift3_day_of.indexOf(dayStringParts[0]) > -1) {
                                alert('This day is day off for shift 3');
                            } else {
                                popUp = true;
                            }
                        }
                        if (popUp) {
                            $("#modalTitle").html('(' + resource.name + ') ' + dateParts[0]);
                            $.ajax({
                                url: '{{route('getPhysicianTime')}}',
                                method: 'POST',
                                data: {
                                    date: dateParts[0],
                                    clinic_id: '{{$clinicId}}',
                                    physician_id: resource.id,
                                    clinicScheduleId: '{{$clinicSchedule['id']}}'
                                },
                                success: function (data) {
                                    if (data.true == 0) {
                                        alert(data.message);
                                    } else {
                                        $("#user_id").val(resource.id);
                                        $("#date").val(dateParts[0]);
                                        $("#your_turn").html(data.your_turn);

                                        $("#time_from").html(data.time_from);
                                        $("#time_from_input").val(data.time_from);

                                        $("#time_to").html(data.time_to);
                                        $("#time_to_input").val(data.time_to);
                                        $("#myModal").modal('show');
                                    }
                                }
                            });
                        }
                    } else {
                        alert("You cannot book on this day the schedule from {{$clinicSchedule['start_date']}} to {{$clinicSchedule['end_date']}}");
                    }
                }
            });

        });
    </script>
@stop


@section('content')
    <section class="content-header">
        <h1>
            Add Reservation
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    <div class="box-body">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Clinic Name:</label>
                                {{$clinic['name']}}
                            </div>
                            <div class="form-group">
                                <label>Current schedule:</label>
                                From: {{$clinicSchedule['start_date']}} -- To: {{$clinicSchedule['end_date']}}
                            </div>
                            <div class="form-group">
                                <label>Shift 1 Day off:</label>
                                {{$clinicSchedule['shift1_day_of']}}
                            </div>
                            @if($clinicSchedule['num_of_shifts'] == 2)
                                <div class="form-group">
                                    <label>Shift 2 Day off:</label>
                                    {{$clinicSchedule['shift2_day_of']}}
                                </div>
                            @endif
                            @if($clinicSchedule['num_of_shifts'] == 3)
                                <div class="form-group">
                                    <label>Shift 2 Day off:</label>
                                    {{$clinicSchedule['shift2_day_of']}}
                                </div>
                                <div class="form-group">
                                    <label>Shift 3 Day off:</label>
                                    {{$clinicSchedule['shift3_day_of']}}
                                </div>
                            @endif
                        </div>
                        <div class="col-md-12">
                            <div id='calendar'></div>
                            <input type="hidden" value="{{$hospitalId}}" id="hospitalId">
                            <input type="hidden" value="{{$clinicId}}" id="clinicId">
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <a href="{{route('home')}}" class="btn btn-info">Back</a>
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
                {{Form::open(array('role'=>"form", 'id' => 'reservationForm'))}}
                <div class="modal-body">
                    <input id="user_id" name="physician_id" type="hidden">
                    <input id="clinic_id" name="clinic_id" type="hidden" value="{{$clinicId}}">
                    <input id="date" name="date" type="hidden">

                    <div class="form-group">
                        <label>Your Turn: &nbsp;</label> <span id="your_turn"></span>
                    </div>
                    <div class="form-group">
                        <label>Time From: &nbsp;</label> <span id="time_from"></span>
                        <input id="time_from_input" name="time_from" type="hidden">
                    </div>
                    <div class="form-group">
                        <label>Time To: &nbsp;</label> <span id="time_to"></span>
                        <input id="time_to_input" name="time_to" type="hidden">
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
@stop