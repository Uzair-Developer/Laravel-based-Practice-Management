@extends('layout/main')

@section('title')
    - Edit Physician Schedule of {{$physicianName}}
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

            $('.timepicker').datetimepicker({
                datepicker: false,
                format: 'H:i',
                step: 5
            });

            $('.time-mask').mask('00:00:00');

            $(".change_edit").change(function (e) {
                $("#is_edit").val('1');
            });
            $("#editPhysicianSchedule").submit(function (e) {
                if ($("#is_edit").val() == 1) {
                    if (confirm('After Save This Schedule Don\'t Forget To Edit All Reservations In This Period In The Schedule, If Found! And Exceptions, If Found!')) {
                    } else {
                        return false;
                    }
                }
            });

            $("#select_dayoff_1").change(function (e) {
                var value = $(this).val();
                if ($.inArray("saturday", value) >= 0) {
                    $(".saturday_1").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".saturday_1").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("sunday", value) >= 0) {
                    $(".sunday_1").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".sunday_1").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("monday", value) >= 0) {
                    $(".monday_1").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".monday_1").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("tuesday", value) >= 0) {
                    $(".tuesday_1").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".tuesday_1").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("wednesday", value) >= 0) {
                    $(".wednesday_1").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".wednesday_1").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("thursday", value) >= 0) {
                    $(".thursday_1").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".thursday_1").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("friday", value) >= 0) {
                    $(".friday_1").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".friday_1").attr('required', 'required').removeAttr('disabled');
                }
            });

            $("#select_dayoff_2").change(function (e) {
                var value = $(this).val();
                if ($.inArray("saturday", value) >= 0) {
                    $(".saturday_2").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".saturday_2").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("sunday", value) >= 0) {
                    $(".sunday_2").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".sunday_2").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("monday", value) >= 0) {
                    $(".monday_2").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".monday_2").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("tuesday", value) >= 0) {
                    $(".tuesday_2").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".tuesday_2").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("wednesday", value) >= 0) {
                    $(".wednesday_2").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".wednesday_2").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("thursday", value) >= 0) {
                    $(".thursday_2").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".thursday_2").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("friday", value) >= 0) {
                    $(".friday_2").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".friday_2").attr('required', 'required').removeAttr('disabled');
                }
            });

            $("#select_dayoff_3").change(function (e) {
                var value = $(this).val();
                if ($.inArray("saturday", value) >= 0) {
                    $(".saturday_3").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".saturday_3").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("sunday", value) >= 0) {
                    $(".sunday_3").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".sunday_3").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("monday", value) >= 0) {
                    $(".monday_3").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".monday_3").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("tuesday", value) >= 0) {
                    $(".tuesday_3").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".tuesday_3").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("wednesday", value) >= 0) {
                    $(".wednesday_3").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".wednesday_3").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("thursday", value) >= 0) {
                    $(".thursday_3").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".thursday_3").attr('required', 'required').removeAttr('disabled');
                }
                if ($.inArray("friday", value) >= 0) {
                    $(".friday_3").removeAttr('required').val('').attr('disabled', 'disabled');
                } else {
                    $(".friday_3").attr('required', 'required').removeAttr('disabled');
                }
            });

        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Edit Physician Schedule {{$physicianName}}
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-9">
                <div class="box box-primary">
                    <!-- form start -->
                    {{Form::open(array('role'=>"form",'files' => true, 'id' => 'editPhysicianSchedule'))}}
                    <div class="box-body">
                        <div class="form-group col-md-6">
                            <label>Clinic</label>

                            <div>{{$clinicName}}</div>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Clinic Schedules</label>

                            <div>{{$clinicSchedule['name']}}</div>
                            <input autocomplete="off" type="hidden" name="num_of_shifts" id="num_of_shifts"
                                   value="{{$physicianSchedule['num_of_shifts']}}">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Physician</label>

                            <div>{{$physicianName}}</div>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Start Date</label>

                            <div id="start_date_div">{{$physicianSchedule['start_date']}}</div>
                        </div>

                        <div class="form-group col-md-6">
                            <label>End Date</label>

                            <div id="end_date_div">{{$physicianSchedule['end_date']}}</div>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Visit duration in minuets</label>
                            <input required type="number" value="{{$physicianSchedule['slots']}}" name="slots"
                                   class="form-control change_edit">
                            <input type="hidden" name="user_id" value="{{$physicianSchedule['user_id']}}">
                            <input autocomplete="off" type="hidden" id="is_edit" name="is_edit" value="0">
                        </div>

                        <div class="form-group col-md-6 display" id="dayoff_1">
                            <label>Shift 1 Days Off</label>
                            <br>
                            <select id="select_dayoff_1" multiple="multiple"
                                    name="dayoff_1[]"
                                    class="form-control select2 change_edit">
                                <?php $dayoff_1 = explode(',', $physicianSchedule['dayoff_1']); ?>
                                <option @if(!empty($physicianSchedule['dayoff_1']) && in_array('', $dayoff_1)) selected
                                        @endif value="">Choose
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_1']) && in_array('saturday', $dayoff_1)) selected
                                        @endif value="saturday">Saturday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_1']) && in_array('sunday', $dayoff_1)) selected
                                        @endif value="sunday">Sunday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_1']) && in_array('monday', $dayoff_1)) selected
                                        @endif value="monday">Monday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_1']) && in_array('tuesday', $dayoff_1)) selected
                                        @endif value="tuesday">Tuesday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_1']) && in_array('wednesday', $dayoff_1)) selected
                                        @endif value="wednesday">Wednesday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_1']) && in_array('thursday', $dayoff_1)) selected
                                        @endif value="thursday">Thursday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_1']) && in_array('friday', $dayoff_1)) selected
                                        @endif value="friday">Friday
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-md-6 display"
                             @if($physicianSchedule['num_of_shifts'] == 1)
                             style="display: none"
                             @endif
                             id="dayoff_2">
                            <label>Shift 2 Days Off</label>
                            <br>
                            <select id="select_dayoff_2" multiple="multiple"
                                    name="dayoff_2[]"
                                    class="form-control select2 change_edit">
                                <?php $dayoff_2 = explode(',', $physicianSchedule['dayoff_2']); ?>
                                <option @if(!empty($physicianSchedule['dayoff_2']) && in_array('', $dayoff_2)) selected
                                        @endif value="">Choose
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_2']) && in_array('saturday', $dayoff_2)) selected
                                        @endif value="saturday">Saturday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_2']) && in_array('sunday', $dayoff_2)) selected
                                        @endif value="sunday">Sunday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_2']) && in_array('monday', $dayoff_2)) selected
                                        @endif value="monday">Monday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_2']) && in_array('tuesday', $dayoff_2)) selected
                                        @endif value="tuesday">Tuesday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_2']) && in_array('wednesday', $dayoff_2)) selected
                                        @endif value="wednesday">Wednesday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_2']) && in_array('thursday', $dayoff_2)) selected
                                        @endif value="thursday">Thursday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_2']) && in_array('friday', $dayoff_2)) selected
                                        @endif value="friday">Friday
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-md-6 display"
                             @if($physicianSchedule['num_of_shifts'] == 2 || $physicianSchedule['num_of_shifts'] == 1)
                             style="display: none"
                             @endif
                             id="dayoff_3">
                            <label>Shift 3 Days Off</label>
                            <br>
                            <select id="select_dayoff_3" multiple="multiple"
                                    name="dayoff_3[]"
                                    class="form-control select2 change_edit">
                                <?php $dayoff_3 = explode(',', $physicianSchedule['dayoff_3']); ?>
                                <option @if(!empty($physicianSchedule['dayoff_3']) && in_array('', $dayoff_3)) selected
                                        @endif value="">Choose
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_3']) && in_array('saturday', $dayoff_3)) selected
                                        @endif value="saturday">Saturday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_3']) && in_array('sunday', $dayoff_3)) selected
                                        @endif value="sunday">Sunday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_3']) && in_array('monday', $dayoff_3)) selected
                                        @endif value="monday">Monday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_3']) && in_array('tuesday', $dayoff_3)) selected
                                        @endif value="tuesday">Tuesday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_3']) && in_array('wednesday', $dayoff_3)) selected
                                        @endif value="wednesday">Wednesday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_3']) && in_array('thursday', $dayoff_3)) selected
                                        @endif value="thursday">Thursday
                                </option>
                                <option @if(!empty($physicianSchedule['dayoff_3']) && in_array('friday', $dayoff_3)) selected
                                        @endif value="friday">Friday
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-md-12 display" id="shift_1">
                            <label>Shift 1</label>
                            <table class="table">
                                <tbody>
                                <tr>
                                    <th></th>
                                    <th>Time from</th>
                                    <th>Time to</th>
                                </tr>
                                <tr>
                                    <td>Saturday</td>
                                    <td>
                                        <div class="bootstrap-timepicker">
                                            <input type="text"
                                                   @if(strpos($physicianSchedule['dayoff_1'], 'saturday') === false) required
                                                   @endif autocomplete="off"
                                                   value="{{$physicianSchedule['sat_start_time_1']}}"
                                                   name="sat_start_time_1"
                                                   class="form-control timepicker change_edit shift1_start_time saturday_1"
                                                   @if(strpos($physicianSchedule['dayoff_1'], 'saturday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'saturday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['sat_end_time_1']}}"
                                                                                 name="sat_end_time_1"
                                                                                 class="form-control time-mask change_edit shift1_end_time saturday_1"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'saturday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Sunday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'sunday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['sun_start_time_1']}}"
                                                                                 name="sun_start_time_1"
                                                                                 class="form-control timepicker change_edit shift1_start_time sunday_1"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'sunday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'sunday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['sun_end_time_1']}}"
                                                                                 name="sun_end_time_1"
                                                                                 class="form-control time-mask change_edit shift1_end_time sunday_1"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'sunday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Monday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'monday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['mon_start_time_1']}}"
                                                                                 name="mon_start_time_1"
                                                                                 class="form-control timepicker change_edit shift1_start_time monday_1"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'monday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'monday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['mon_end_time_1']}}"
                                                                                 name="mon_end_time_1"
                                                                                 class="form-control time-mask change_edit shift1_end_time monday_1"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'monday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tuesday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'tuesday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['tues_start_time_1']}}"
                                                                                 name="tues_start_time_1"
                                                                                 class="form-control timepicker change_edit shift1_start_time tuesday_1"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'tuesday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'tuesday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['tues_end_time_1']}}"
                                                                                 name="tues_end_time_1"
                                                                                 class="form-control time-mask change_edit shift1_end_time tuesday_1"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'tuesday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Wednesday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'wednesday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['wed_start_time_1']}}"
                                                                                 name="wed_start_time_1"
                                                                                 class="form-control timepicker change_edit shift1_start_time wednesday_1"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'wednesday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'wednesday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['wed_end_time_1']}}"
                                                                                 name="wed_end_time_1"
                                                                                 class="form-control time-mask change_edit shift1_end_time wednesday_1"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'wednesday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Thursday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'thursday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['thurs_start_time_1']}}"
                                                                                 name="thurs_start_time_1"
                                                                                 class="form-control timepicker change_edit shift1_start_time thursday_1"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'thursday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'thursday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['thurs_end_time_1']}}"
                                                                                 name="thurs_end_time_1"
                                                                                 class="form-control time-mask change_edit shift1_end_time thursday_1"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'thursday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Friday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'friday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['fri_start_time_1']}}"
                                                                                 name="fri_start_time_1"
                                                                                 class="form-control timepicker change_edit shift1_start_time friday_1"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'friday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'friday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['fri_end_time_1']}}"
                                                                                 name="fri_end_time_1"
                                                                                 class="form-control time-mask change_edit shift1_end_time friday_1"
                                                                                 @if(strpos($physicianSchedule['dayoff_1'], 'friday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group col-md-12 display"
                             @if($physicianSchedule['num_of_shifts'] == 1)
                             style="display: none"
                             @endif
                             id="shift_2">
                            <label>Shift 2</label>
                            <table class="table">
                                <tbody>
                                <tr>
                                    <th></th>
                                    <th>Time from</th>
                                    <th>Time to</th>
                                </tr>
                                <tr>
                                    <td>Saturday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'saturday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['sat_start_time_2']}}"
                                                                                 name="sat_start_time_2"
                                                                                 class="form-control timepicker change_edit shift2_start_time saturday_2"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'saturday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'saturday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['sat_end_time_2']}}"
                                                                                 name="sat_end_time_2"
                                                                                 class="form-control time-mask change_edit shift2_end_time saturday_2"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'saturday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Sunday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'sunday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['sun_start_time_2']}}"
                                                                                 name="sun_start_time_2"
                                                                                 class="form-control timepicker change_edit shift2_start_time sunday_2"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'sunday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'sunday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['sun_end_time_2']}}"
                                                                                 name="sun_end_time_2"
                                                                                 class="form-control time-mask change_edit shift2_end_time sunday_2"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'sunday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Monday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'monday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['mon_start_time_2']}}"
                                                                                 name="mon_start_time_2"
                                                                                 class="form-control timepicker change_edit shift2_start_time monday_2"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'monday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'monday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['mon_end_time_2']}}"
                                                                                 name="mon_end_time_2"
                                                                                 class="form-control time-mask change_edit shift2_end_time monday_2"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'monday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tuesday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'tuesday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['tues_start_time_2']}}"
                                                                                 name="tues_start_time_2"
                                                                                 class="form-control timepicker change_edit shift2_start_time tuesday_2"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'tuesday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'tuesday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['tues_end_time_2']}}"
                                                                                 name="tues_end_time_2"
                                                                                 class="form-control time-mask change_edit shift2_end_time tuesday_2"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'tuesday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Wednesday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'wednesday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['wed_start_time_2']}}"
                                                                                 name="wed_start_time_2"
                                                                                 class="form-control timepicker change_edit shift2_start_time wednesday_2"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'wednesday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'wednesday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['wed_end_time_2']}}"
                                                                                 name="wed_end_time_2"
                                                                                 class="form-control time-mask change_edit shift2_end_time wednesday_2"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'wednesday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Thursday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'thursday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['thurs_start_time_2']}}"
                                                                                 name="thurs_start_time_2"
                                                                                 class="form-control timepicker change_edit shift2_start_time thursday_2"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'thursday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'thursday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['thurs_end_time_2']}}"
                                                                                 name="thurs_end_time_2"
                                                                                 class="form-control time-mask change_edit shift2_end_time thursday_2"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'thursday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Friday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'friday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['fri_start_time_2']}}"
                                                                                 name="fri_start_time_2"
                                                                                 class="form-control timepicker change_edit shift2_start_time friday_2"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'friday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'friday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['fri_end_time_2']}}"
                                                                                 name="fri_end_time_2"
                                                                                 class="form-control time-mask change_edit shift2_end_time friday_2"
                                                                                 @if(strpos($physicianSchedule['dayoff_2'], 'friday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group col-md-12 display"
                             @if($physicianSchedule['num_of_shifts'] == 2 || $physicianSchedule['num_of_shifts'] == 1)
                             style="display: none"
                             @endif
                             id="shift_3">
                            <label>Shift 3</label>
                            <table class="table">
                                <tbody>
                                <tr>
                                    <th></th>
                                    <th>Time from</th>
                                    <th>Time to</th>
                                </tr>
                                <tr>
                                    <td>Saturday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'saturday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['sat_start_time_3']}}"
                                                                                 name="sat_start_time_3"
                                                                                 class="form-control timepicker change_edit shift3_start_time saturday_3"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'saturday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'saturday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['sat_end_time_3']}}"
                                                                                 name="sat_end_time_3"
                                                                                 class="form-control time-mask change_edit shift3_end_time saturday_3"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'saturday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Sunday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'sunday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['sun_start_time_3']}}"
                                                                                 name="sun_start_time_3"
                                                                                 class="form-control timepicker change_edit shift3_start_time sunday_3"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'sunday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'sunday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['sun_end_time_3']}}"
                                                                                 name="sun_end_time_3"
                                                                                 class="form-control time-mask change_edit shift3_end_time sunday_3"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'sunday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Monday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'monday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['mon_start_time_3']}}"
                                                                                 name="mon_start_time_3"
                                                                                 class="form-control timepicker change_edit shift3_start_time monday_3"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'monday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'monday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['mon_end_time_3']}}"
                                                                                 name="mon_end_time_3"
                                                                                 class="form-control time-mask change_edit shift3_end_time monday_3"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'monday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tuesday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'tuesday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['tues_start_time_3']}}"
                                                                                 name="tues_start_time_3"
                                                                                 class="form-control timepicker change_edit shift3_start_time tuesday_3"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'tuesday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'tuesday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['tues_end_time_3']}}"
                                                                                 name="tues_end_time_3"
                                                                                 class="form-control time-mask change_edit shift3_end_time tuesday_3"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'tuesday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Wednesday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'wednesday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['wed_start_time_3']}}"
                                                                                 name="wed_start_time_3"
                                                                                 class="form-control timepicker change_edit shift3_start_time wednesday_3"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'wednesday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'wednesday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['wed_end_time_3']}}"
                                                                                 name="wed_end_time_3"
                                                                                 class="form-control time-mask change_edit shift3_end_time wednesday_3"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'wednesday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Thursday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'thursday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['thurs_start_time_3']}}"
                                                                                 name="thurs_start_time_3"
                                                                                 class="form-control timepicker change_edit shift3_start_time thursday_3"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'thursday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'thursday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['thurs_end_time_3']}}"
                                                                                 name="thurs_end_time_3"
                                                                                 class="form-control time-mask change_edit shift3_end_time thursday_3"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'thursday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Friday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'friday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['fri_start_time_3']}}"
                                                                                 name="fri_start_time_3"
                                                                                 class="form-control timepicker change_edit shift3_start_time friday_3"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'friday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input type="text"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'friday') === false) required
                                                                                 @endif autocomplete="off"
                                                                                 value="{{$physicianSchedule['fri_end_time_3']}}"
                                                                                 name="fri_end_time_3"
                                                                                 class="form-control time-mask change_edit shift3_end_time friday_3"
                                                                                 @if(strpos($physicianSchedule['dayoff_3'], 'friday') !== false) disabled @endif>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button id="editPhysicianScheduleBtn" class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('physicianSchedules')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop