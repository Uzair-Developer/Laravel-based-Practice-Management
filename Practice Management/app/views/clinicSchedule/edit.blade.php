@extends('layout/main')

@section('title')
    - Edit {{$clinicSchedule['name']}}
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datetimepicker/jquery.datetimepicker.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/datetimepicker/jquery.datetimepicker.full.js')}}"></script>
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('plugins/jquery-mask/jquery.mask.min.js')}}"></script>
    <script>
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();
            //Timepicker
            $('.timepicker').datetimepicker({
                datepicker: false,
                format: 'H:i',
                step: 5,
                minDate: '{{date('Y-m-d')}}'
            });

            //Datepicker
            $('.datepicker').datepicker({
                startDate: "1d",
                todayHighlight: true,
                autoclose: true
            });

            $('.time-mask').mask('00:00:00');

            {{--$("#selectHospital").change(function (e) {--}}
            {{--$("#selectClinic").attr('disabled', 'disabled');--}}
            {{--$.ajax({--}}
            {{--url: '{{route('getClinicsByHospitalId')}}',--}}
            {{--method: 'POST',--}}
            {{--data: {--}}
            {{--hospital_id: $(this).val()--}}
            {{--},--}}
            {{--headers: {token: '{{csrf_token()}}'},--}}
            {{--success: function (data) {--}}
            {{--$("#selectClinic").removeAttr('disabled').html(data).select2();--}}
            {{--}--}}
            {{--});--}}
            {{--});--}}

            @if($clinicSchedule['hospital_id'] != '')
            $.ajax({
                url: '{{route('getClinicsByHospitalId')}}',
                method: 'POST',
                data: {
                    hospital_id: $("#selectHospital").val()
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#selectClinic").html(data).val('{{$clinicSchedule['clinic_id']}}').attr('disabled', 'disabled').select2();
                }
            });
            @endif

            {{--$("#selectClinic").change(function (e) {--}}
            {{--$.ajax({--}}
            {{--url: '{{route('getLastScheduleOfClinic')}}',--}}
            {{--method: 'POST',--}}
            {{--data: {--}}
            {{--clinic_id: $(this).val()--}}
            {{--},--}}
            {{--headers: {token: '{{csrf_token()}}'},--}}
            {{--success: function (data) {--}}
            {{--if (data.true == 1) {--}}
            {{--var end_date = data.end_date;--}}
            {{--var dmy = end_date.split("-");--}}
            {{--var joindate = new Date(--}}
            {{--parseInt(dmy[0], 10),--}}
            {{--parseInt(dmy[1], 10) - 1,--}}
            {{--parseInt(dmy[2], 10)--}}
            {{--);--}}
            {{--joindate.setDate(joindate.getDate() + 1);--}}
            {{--var date = joindate.getFullYear() + "-" +--}}
            {{--("0" + (joindate.getMonth() + 1)).slice(-2) + "-" +--}}
            {{--("0" + joindate.getDate()).slice(-2);--}}
            {{--$("#start_date").val(date);--}}
            {{--$("#start_date_div").html(date);--}}
            {{--} else {--}}
            {{--$("#start_date").val('{{date('Y-m-d')}}');--}}
            {{--$("#start_date_div").html('{{date('Y-m-d')}}');--}}
            {{--}--}}
            {{--}--}}
            {{--});--}}
            {{--});--}}

            $(".shifts").click(function (e) {
                var shift = $(this).val();
                if (shift == 1) {
                    $("#divShift2").hide();
                    $("#divShift3").hide();
                } else if (shift == 2) {
                    $("#divShift2").show();
                    $("#divShift3").hide();
                } else {
                    $("#divShift2").show();
                    $("#divShift3").show();
                }
            });
                    @if($clinicSchedule['num_of_shifts'] != 1)
            var shift = $(".shiftRadios input:checked").val();
            if (shift == 1) {
                $("#divShift2").hide();
                $("#divShift3").hide();
            } else if (shift == 2) {
                $("#divShift2").show();
                $("#divShift3").hide();
            } else {
                $("#divShift2").show();
                $("#divShift3").show();
            }
            @endif

            $("#end_date").change(function (e) {
                var end_date = $(this).val();
                if (end_date < $("#start_date").val()) {
                    alert('Make sure the end date is greater than start date');
                    $(this).val('');
                }
            });

        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Edit {{$clinicSchedule['name']}}
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    {{Form::open(array('role'=>"form"))}}
                    <div class="box-body">
                        <div class="form-group col-md-6">
                            <label>Hospital</label>
                            <select disabled id="selectHospital" required name="hospital_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    <option value="{{$val['id']}}" @if($clinicSchedule['hospital_id'] == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Clinic</label>
                            <select disabled id="selectClinic" required name="clinic_id" class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Start Date</label>
                            <input disabled id="start_date" required type="text" data-date-format="yyyy-mm-dd"
                                   value="{{$clinicSchedule['start_date']}}"
                                   name="start_date" class="form-control datepicker">
                        </div>

                        <div class="form-group col-md-6">
                            <label>End Date</label>
                            <input disabled id="end_date" type="text" data-date-format="yyyy-mm-dd"
                                   value="{{$clinicSchedule['end_date']}}"
                                   name="end_date" class="form-control datepicker">
                        </div>

                        <div class="form-group shiftRadios col-md-12">
                            <label>Shifts</label>

                            <div class="clearfix"></div>
                            <div class="col-md-1">
                                <label>1</label>
                                <input autocomplete="off" @if(!$clinicSchedule['num_of_shifts']) checked @endif
                                @if($clinicSchedule['num_of_shifts'] == 1) checked @endif type="radio" value="1"
                                       name="num_of_shifts"
                                       class="form-control shifts">
                            </div>
                            <div class="col-md-1">
                                <label>2</label>
                                <input autocomplete="off" @if($clinicSchedule['num_of_shifts'] == 2) checked
                                       @endif type="radio" value="2" name="num_of_shifts"
                                       class="form-control shifts">
                            </div>
                            <div class="col-md-1">
                                <label>3</label>
                                <input autocomplete="off" @if($clinicSchedule['num_of_shifts'] == 3) checked
                                       @endif type="radio" value="3" name="num_of_shifts"
                                       class="form-control shifts">
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <br>

                        <div id="divShift1" style="background: lightgrey !important;"
                             class="form-group box box-solid col-md-12">
                            <div class="box-header with-border">
                                <h3 class="box-title">Shift 1</h3>
                            </div>
                            <div class="box-body">

                                <div class="bootstrap-timepicker col-md-6">
                                    <label>Start Time</label>

                                    <div class="input-group">
                                        <input type="text" required value="{{$clinicSchedule['shift1_start_time']}}"
                                               name="shift1_start_time" class="form-control timepicker">

                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <div class="bootstrap-timepicker col-md-6">
                                    <label>End Time</label>

                                    <div class="input-group">
                                        <input type="text" required value="{{$clinicSchedule['shift1_end_time']}}"
                                               name="shift1_end_time" class="form-control time-mask">

                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <div class="col-md-6">
                                    <label>Day Off</label>
                                    <select required multiple="multiple" name="shift1_day_of[]"
                                            class="form-control select2">
                                        <?php $shift1_day_of = explode(',', $clinicSchedule['shift1_day_of']);?>
                                        <option value="">Choose</option>
                                        <option @if(!empty($shift1_day_of) && in_array('saturday', $shift1_day_of)) selected
                                                @endif value="saturday">Saturday
                                        </option>
                                        <option @if(!empty($shift1_day_of) && in_array('sunday', $shift1_day_of)) selected
                                                @endif value="sunday">Sunday
                                        </option>
                                        <option @if(!empty($shift1_day_of) && in_array('monday', $shift1_day_of)) selected
                                                @endif value="monday">Monday
                                        </option>
                                        <option @if(!empty($shift1_day_of) && in_array('tuesday', $shift1_day_of)) selected
                                                @endif value="tuesday">Tuesday
                                        </option>
                                        <option @if(!empty($shift1_day_of) && in_array('wednesday', $shift1_day_of)) selected
                                                @endif value="wednesday">Wednesday
                                        </option>
                                        <option @if(!empty($shift1_day_of) && in_array('thursday', $shift1_day_of)) selected
                                                @endif value="thursday">Thursday
                                        </option>
                                        <option @if(!empty($shift1_day_of) && in_array('friday', $shift1_day_of)) selected
                                                @endif value="friday">Friday
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="divShift2" style="background: lightgrey !important;display: none;"
                             class="form-group box box-solid col-md-12">
                            <div class="box-header with-border">
                                <h3 class="box-title">Shift 2</h3>
                            </div>
                            <div class="box-body">
                                <div class="bootstrap-timepicker col-md-6">
                                    <label>Start Time</label>

                                    <div class="input-group">
                                        <input type="text" value="{{$clinicSchedule['shift2_start_time']}}"
                                               name="shift2_start_time" class="form-control timepicker">

                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <div class="bootstrap-timepicker col-md-6">
                                    <label>End Time</label>

                                    <div class="input-group">
                                        <input type="text" value="{{$clinicSchedule['shift2_end_time']}}"
                                               name="shift2_end_time" class="form-control time-mask">

                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <div class="col-md-6">
                                    <label>Day Off</label>
                                    <select multiple="multiple" style="width: 100%" name="shift2_day_of[]"
                                            class="form-control select2">
                                        <?php $shift2_day_of = explode(',', $clinicSchedule['shift2_day_of']);?>
                                        <option value="">Choose</option>
                                        <option @if(!empty($shift2_day_of) && in_array('saturday', $shift2_day_of)) selected
                                                @endif value="saturday">Saturday
                                        </option>
                                        <option @if(!empty($shift2_day_of) && in_array('sunday', $shift2_day_of)) selected
                                                @endif value="sunday">Sunday
                                        </option>
                                        <option @if(!empty($shift2_day_of) && in_array('monday', $shift2_day_of)) selected
                                                @endif value="monday">Monday
                                        </option>
                                        <option @if(!empty($shift2_day_of) && in_array('tuesday', $shift2_day_of)) selected
                                                @endif value="tuesday">Tuesday
                                        </option>
                                        <option @if(!empty($shift2_day_of) && in_array('wednesday', $shift2_day_of)) selected
                                                @endif value="wednesday">Wednesday
                                        </option>
                                        <option @if(!empty($shift2_day_of) && in_array('thursday', $shift2_day_of)) selected
                                                @endif value="thursday">Thursday
                                        </option>
                                        <option @if(!empty($shift2_day_of) && in_array('friday', $shift2_day_of)) selected
                                                @endif value="friday">Friday
                                        </option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div id="divShift3" style="background: lightgrey !important;display: none;"
                             class="form-group box box-solid col-md-12">
                            <div class="box-header with-border">
                                <h3 class="box-title">Shift 3</h3>
                            </div>
                            <div class="box-body">
                                <div class="bootstrap-timepicker col-md-6">
                                    <label>Start Time</label>

                                    <div class="input-group">
                                        <input type="text" value="{{$clinicSchedule['shift3_start_time']}}"
                                               name="shift3_start_time" class="form-control timepicker">

                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <div class="bootstrap-timepicker col-md-6">
                                    <label>End Time</label>

                                    <div class="input-group">
                                        <input type="text" value="{{$clinicSchedule['shift3_end_time']}}"
                                               name="shift3_end_time" class="form-control time-mask">

                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <div class="col-md-6">
                                    <label>Day Off</label>
                                    <select multiple="multiple" style="width: 100%" name="shift3_day_of[]"
                                            class="form-control select2">
                                        <?php $shift3_day_of = explode(',', $clinicSchedule['shift3_day_of']);?>
                                        <option value="">Choose</option>
                                        <option @if(!empty($shift3_day_of) && in_array('saturday', $shift3_day_of)) selected
                                                @endif value="saturday">Saturday
                                        </option>
                                        <option @if(!empty($shift3_day_of) && in_array('sunday', $shift3_day_of)) selected
                                                @endif value="sunday">Sunday
                                        </option>
                                        <option @if(!empty($shift3_day_of) && in_array('monday', $shift3_day_of)) selected
                                                @endif value="monday">Monday
                                        </option>
                                        <option @if(!empty($shift3_day_of) && in_array('tuesday', $shift3_day_of)) selected
                                                @endif value="tuesday">Tuesday
                                        </option>
                                        <option @if(!empty($shift3_day_of) && in_array('wednesday', $shift3_day_of)) selected
                                                @endif value="wednesday">Wednesday
                                        </option>
                                        <option @if(!empty($shift3_day_of) && in_array('thursday', $shift3_day_of)) selected
                                                @endif value="thursday">Thursday
                                        </option>
                                        <option @if(!empty($shift3_day_of) && in_array('friday', $shift3_day_of)) selected
                                                @endif value="friday">Friday
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control">{{$clinicSchedule['notes']}}</textarea>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('clinicSchedules')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop