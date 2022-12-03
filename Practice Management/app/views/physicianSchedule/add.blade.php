@extends('layout/main')

@section('title')
    - Add Physician Schedule
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
            $(".select2").select2();

            $('.timepicker').datetimepicker({
                datepicker: false,
                format: 'H:i',
                step: 5
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

            $('.time-mask').mask('00:00:00');

            $("#clinic_id").change(function () {
                $("#clinic_schedule_id").attr('disabled', true);
                $("#user_id").attr('disabled', true);
                $.ajax({
                    url: '{{route('getPhysicianByClinic')}}',
                    method: 'POST',
                    data: {
                        clinic_id: $(this).val()
                    },
                    success: function (data) {
                        $("#clinic_schedule_id").html(data.schedulesHtml).attr('disabled', false);
                        $("#user_id").html(data.physiciansHtml).attr('disabled', false);
                    }
                });
            });

            function change_clinic_schedule() {
                $.ajax({
                    url: '{{route('getScheduleId')}}',
                    method: 'POST',
                    data: {
                        clinic_schedule_id: $("#clinic_schedule_id").val()
                    },
                    success: function (data) {
                        $("#start_date").val(data.start_date);
                        $("#end_date").val(data.end_date);
                        var num_of_shifts = data.num_of_shifts;
                        $("#num_of_shifts").val(num_of_shifts);
                        var shift1_day_of = data.shift1_day_of;
                        var shift2_day_of = data.shift2_day_of;
                        var shift3_day_of = data.shift3_day_of;
                        $(".shift1_start_time").val(data.shift1_start_time);
                        $(".shift2_start_time").val(data.shift2_start_time);
                        $(".shift3_start_time").val(data.shift3_start_time);
                        $(".shift1_end_time").val(data.shift1_end_time);
                        $(".shift2_end_time").val(data.shift2_end_time);
                        $(".shift3_end_time").val(data.shift3_end_time);

                        var shift1_day_off = [];
                        var shift2_day_off = [];
                        var shift3_day_off = [];
                        if (shift1_day_of != null && shift1_day_of != undefined && shift1_day_of != '') {
                            var shift1_day_off = shift1_day_of.split(",");
                        }
                        if (shift2_day_of != null && shift2_day_of != undefined && shift2_day_of != '') {
                            var shift2_day_off = shift2_day_of.split(",");
                        }
                        if (shift3_day_of != null && shift3_day_of != undefined && shift3_day_of != '') {
                            var shift3_day_off = shift3_day_of.split(",");
                        }

                        $("#select_dayoff_1").val(shift1_day_off);
                        var value = shift1_day_off;
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
                        ///////////////////////////////////////
                        $("#select_dayoff_2").val(shift2_day_off);
                        if(num_of_shifts == 2 || num_of_shifts == 3) {
                            var value = shift2_day_off;
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
                        }
                        ///////////////////////////////////////
                        $("#select_dayoff_3").val(shift3_day_off);
                        if(num_of_shifts == 3) {
                            var value = shift3_day_off;
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
                        }
                        $(".select2").select2();
                        if (num_of_shifts == 1) {
                            $(".display").hide();
                            $("#dayoff_1").show();
                            $("#shift_1").show();
                        } else if (num_of_shifts == 2) {
                            $(".display").hide();
                            $("#dayoff_1").show();
                            $("#shift_1").show();
                            $("#dayoff_2").show();
                            $("#shift_2").show();
                        } else if (num_of_shifts == 3) {
                            $(".display").hide();
                            $("#dayoff_1").show();
                            $("#shift_1").show();
                            $("#dayoff_2").show();
                            $("#shift_2").show();
                            $("#dayoff_3").show();
                            $("#shift_3").show();
                        }
                    }
                });
            }

            $("#clinic_schedule_id").change(function () {
                change_clinic_schedule();
            });
            $("#selectHospital2").change(function (e) {
                $("#clinic_id").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getClinicsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#clinic_id").removeAttr('disabled').html(data).select2();
                    }
                });
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

            @if(Input::old('hospital_id'))
             $.ajax({
                        url: '{{route('getClinicsByHospitalId')}}',
                        method: 'POST',
                        data: {
                            hospital_id: $("#selectHospital2").val()
                        },
                        headers: {token: '{{csrf_token()}}'},
                        success: function (data) {
                            $("#clinic_id").removeAttr('disabled').html(data).select2();
                            @if(Input::old('clinic_id'))
                            $("#clinic_id").val('{{Input::old('clinic_id')}}').select2();
                            $.ajax({
                                url: '{{route('getPhysicianByClinic')}}',
                                method: 'POST',
                                data: {
                                    clinic_id: $("#clinic_id").val()
                                },
                                success: function (data) {
                                    $("#clinic_schedule_id").html(data.schedulesHtml).attr('disabled', false);
                                    $("#user_id").html(data.physiciansHtml).attr('disabled', false);
                                    @if(Input::old('user_id'))
                                    $("#user_id").val('{{Input::old('user_id')}}').select2();
                                    @endif
                                    @if(Input::old('clinic_schedule_id'))
                                    $("#clinic_schedule_id").val('{{Input::old('clinic_schedule_id')}}').select2();
                                    change_clinic_schedule();
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
            Add Physician Schedule
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    {{Form::open(array('role'=>"form",'files' => true))}}
                    <div class="box-body">
                        <div class="form-group col-md-4">
                            <label>Hospital</label>
                            <br>
                            <select autocomplete="off" id="selectHospital2" required name="hospital_id"
                                    class="form-control select2" style="width: 100%;">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    <option value="{{$val['id']}}" @if(Input::old('hospital_id') == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Clinics</label>
                            <br>
                            <select autocomplete="off" id="clinic_id" required name="clinic_id"
                                    class="form-control select2" style="width: 100%;">
                                <option value="">Choose</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Clinic Schedules</label>
                            <select id="clinic_schedule_id" required name="clinic_schedule_id"
                                    class="form-control select2" style="width: 100%;">

                            </select>
                            <input type="hidden" name="num_of_shifts" id="num_of_shifts">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Start Date</label>
                            <input autocomplete="off" required id="start_date" type="text" data-date-format="yyyy-mm-dd"
                                   value="{{Input::old('start_date')}}" name="start_date"
                                   class="form-control datepicker2">
                        </div>

                        <div class="form-group col-md-6">
                            <label>End Date</label>
                            <input autocomplete="off" required id="end_date" type="text" data-date-format="yyyy-mm-dd"
                                   value="{{Input::old('end_date')}}"
                                   name="end_date" class="form-control datepicker">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Physicians</label>
                            <br>
                            <select id="user_id" required name="user_id" class="form-control select2"
                                    style="width: 100%;">

                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Visit duration in minuets</label>
                            <input required autocomplete="off" type="number" value="{{Input::old('slots')}}" name="slots"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-12">
                            <label>Split With Months?</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="split"
                                           type="radio" autocomplete="off" value="1"> Yes
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="split" checked
                                           type="radio" autocomplete="off" value="2"> No
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-6 display" style="display: none" id="dayoff_1">
                            <label>Shift 1 Days Off</label>
                            <br>
                            <select id="select_dayoff_1" style="width: 100%" multiple="multiple" name="dayoff_1[]"
                                    class="form-control select2">
                                <option @if(!empty(Input::old('dayoff_1')) && in_array('', Input::old('dayoff_1'))) selected
                                        @endif value="">Choose
                                </option>
                                <option @if(!empty(Input::old('dayoff_1')) && in_array('saturday', Input::old('dayoff_1'))) selected
                                        @endif value="saturday">Saturday
                                </option>
                                <option @if(!empty(Input::old('dayoff_1')) && in_array('sunday', Input::old('dayoff_1'))) selected
                                        @endif value="sunday">Sunday
                                </option>
                                <option @if(!empty(Input::old('dayoff_1')) && in_array('monday', Input::old('dayoff_1'))) selected
                                        @endif value="monday">Monday
                                </option>
                                <option @if(!empty(Input::old('dayoff_1')) && in_array('tuesday', Input::old('dayoff_1'))) selected
                                        @endif value="tuesday">Tuesday
                                </option>
                                <option @if(!empty(Input::old('dayoff_1')) && in_array('wednesday', Input::old('dayoff_1'))) selected
                                        @endif value="wednesday">Wednesday
                                </option>
                                <option @if(!empty(Input::old('dayoff_1')) && in_array('thursday', Input::old('dayoff_1'))) selected
                                        @endif value="thursday">Thursday
                                </option>
                                <option @if(!empty(Input::old('dayoff_1')) && in_array('friday', Input::old('dayoff_1'))) selected
                                        @endif value="friday">Friday
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-md-6 display" style="display: none" id="dayoff_2">
                            <label>Shift 2 Days Off</label>
                            <br>
                            <select id="select_dayoff_2" style="width: 100%" multiple="multiple" name="dayoff_2[]"
                                    class="form-control select2">
                                <option @if(!empty(Input::old('dayoff_2')) && in_array('', Input::old('dayoff_2'))) selected
                                        @endif value="">Choose
                                </option>
                                <option @if(!empty(Input::old('dayoff_2')) && in_array('saturday', Input::old('dayoff_2'))) selected
                                        @endif value="saturday">Saturday
                                </option>
                                <option @if(!empty(Input::old('dayoff_2')) && in_array('sunday', Input::old('dayoff_2'))) selected
                                        @endif value="sunday">Sunday
                                </option>
                                <option @if(!empty(Input::old('dayoff_2')) && in_array('monday', Input::old('dayoff_2'))) selected
                                        @endif value="monday">Monday
                                </option>
                                <option @if(!empty(Input::old('dayoff_2')) && in_array('tuesday', Input::old('dayoff_2'))) selected
                                        @endif value="tuesday">Tuesday
                                </option>
                                <option @if(!empty(Input::old('dayoff_2')) && in_array('wednesday', Input::old('dayoff_2'))) selected
                                        @endif value="wednesday">Wednesday
                                </option>
                                <option @if(!empty(Input::old('dayoff_2')) && in_array('thursday', Input::old('dayoff_2'))) selected
                                        @endif value="thursday">Thursday
                                </option>
                                <option @if(!empty(Input::old('dayoff_2')) && in_array('friday', Input::old('dayoff_2'))) selected
                                        @endif value="friday">Friday
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-md-6 display" style="display: none" id="dayoff_3">
                            <label>Shift 3 Days Off</label>
                            <br>
                            <select id="select_dayoff_3" style="width: 100%" multiple="multiple" name="dayoff_3[]"
                                    class="form-control select2">
                                <option @if(!empty(Input::old('dayoff_3')) && in_array('', Input::old('dayoff_3'))) selected
                                        @endif value="">Choose
                                </option>
                                <option @if(!empty(Input::old('dayoff_3')) && in_array('saturday', Input::old('dayoff_3'))) selected
                                        @endif value="saturday">Saturday
                                </option>
                                <option @if(!empty(Input::old('dayoff_3')) && in_array('sunday', Input::old('dayoff_3'))) selected
                                        @endif value="sunday">Sunday
                                </option>
                                <option @if(!empty(Input::old('dayoff_3')) && in_array('monday', Input::old('dayoff_3'))) selected
                                        @endif value="monday">Monday
                                </option>
                                <option @if(!empty(Input::old('dayoff_3')) && in_array('tuesday', Input::old('dayoff_3'))) selected
                                        @endif value="tuesday">Tuesday
                                </option>
                                <option @if(!empty(Input::old('dayoff_3')) && in_array('wednesday', Input::old('dayoff_3'))) selected
                                        @endif value="wednesday">Wednesday
                                </option>
                                <option @if(!empty(Input::old('dayoff_3')) && in_array('thursday', Input::old('dayoff_3'))) selected
                                        @endif value="thursday">Thursday
                                </option>
                                <option @if(!empty(Input::old('dayoff_3')) && in_array('friday', Input::old('dayoff_3'))) selected
                                        @endif value="friday">Friday
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-md-12 display" style="display: none" id="shift_1">
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
                                            <input required autocomplete="off" type="text" value="{{Input::old('sat_start_time_1')}}"
                                                   name="sat_start_time_1"
                                                   class="form-control timepicker shift1_start_time saturday_1">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker">
                                            <input required autocomplete="off" type="text"
                                                   value="{{Input::old('sat_end_time_1')}}"
                                                   name="sat_end_time_1"
                                                   class="form-control shift1_end_time time-mask saturday_1">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Sunday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input required autocomplete="off" type="text"
                                                                                 value="{{Input::old('sun_start_time_1')}}"
                                                                                 name="sun_start_time_1"
                                                                                 class="form-control timepicker shift1_start_time sunday_1">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input required autocomplete="off" type="text"
                                                                                 value="{{Input::old('sun_end_time_1')}}"
                                                                                 name="sun_end_time_1"
                                                                                 class="form-control time-mask shift1_end_time sunday_1">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Monday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input required autocomplete="off" type="text"
                                                                                 value="{{Input::old('mon_start_time_1')}}"
                                                                                 name="mon_start_time_1"
                                                                                 class="form-control timepicker shift1_start_time monday_1">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input required autocomplete="off" type="text"
                                                                                 value="{{Input::old('mon_end_time_1')}}"
                                                                                 name="mon_end_time_1"
                                                                                 class="form-control time-mask shift1_end_time monday_1">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tuesday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input required autocomplete="off" type="text"
                                                                                 value="{{Input::old('tues_start_time_1')}}"
                                                                                 name="tues_start_time_1"
                                                                                 class="form-control timepicker shift1_start_time tuesday_1">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input required autocomplete="off" type="text"
                                                                                 value="{{Input::old('tues_end_time_1')}}"
                                                                                 name="tues_end_time_1"
                                                                                 class="form-control time-mask shift1_end_time tuesday_1">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Wednesday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input required autocomplete="off" type="text"
                                                                                 value="{{Input::old('wed_start_time_1')}}"
                                                                                 name="wed_start_time_1"
                                                                                 class="form-control timepicker shift1_start_time wednesday_1">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input required autocomplete="off" type="text"
                                                                                 value="{{Input::old('wed_end_time_1')}}"
                                                                                 name="wed_end_time_1"
                                                                                 class="form-control time-mask shift1_end_time wednesday_1">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Thursday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input required autocomplete="off" type="text"
                                                                                 value="{{Input::old('thurs_start_time_1')}}"
                                                                                 name="thurs_start_time_1"
                                                                                 class="form-control timepicker shift1_start_time thursday_1">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input required autocomplete="off" type="text"
                                                                                 value="{{Input::old('thurs_end_time_1')}}"
                                                                                 name="thurs_end_time_1"
                                                                                 class="form-control time-mask shift1_end_time thursday_1">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Friday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input required autocomplete="off" type="text"
                                                                                 value="{{Input::old('fri_start_time_1')}}"
                                                                                 name="fri_start_time_1"
                                                                                 class="form-control timepicker shift1_start_time friday_1">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input required autocomplete="off" type="text"
                                                                                 value="{{Input::old('fri_end_time_1')}}"
                                                                                 name="fri_end_time_1"
                                                                                 class="form-control time-mask shift1_end_time friday_1">
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group col-md-12 display" style="display: none" id="shift_2">
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
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('sat_start_time_2')}}"
                                                                                 name="sat_start_time_2"
                                                                                 class="form-control timepicker shift2_start_time saturday_2">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('sat_end_time_2')}}"
                                                                                 name="sat_end_time_2"
                                                                                 class="form-control time-mask shift2_end_time saturday_2">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Sunday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('sun_start_time_2')}}"
                                                                                 name="sun_start_time_2"
                                                                                 class="form-control timepicker shift2_start_time sunday_2">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('sun_end_time_2')}}"
                                                                                 name="sun_end_time_2"
                                                                                 class="form-control time-mask shift2_end_time sunday_2">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Monday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('mon_start_time_2')}}"
                                                                                 name="mon_start_time_2"
                                                                                 class="form-control timepicker shift2_start_time monday_2">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('mon_end_time_2')}}"
                                                                                 name="mon_end_time_2"
                                                                                 class="form-control time-mask shift2_end_time monday_2">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tuesday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('tues_start_time_2')}}"
                                                                                 name="tues_start_time_2"
                                                                                 class="form-control timepicker shift2_start_time tuesday_2">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('tues_end_time_2')}}"
                                                                                 name="tues_end_time_2"
                                                                                 class="form-control time-mask shift2_end_time tuesday_2">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Wednesday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('wed_start_time_2')}}"
                                                                                 name="wed_start_time_2"
                                                                                 class="form-control timepicker shift2_start_time wednesday_2">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('wed_end_time_2')}}"
                                                                                 name="wed_end_time_2"
                                                                                 class="form-control time-mask shift2_end_time wednesday_2">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Thursday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('thurs_start_time_2')}}"
                                                                                 name="thurs_start_time_2"
                                                                                 class="form-control timepicker shift2_start_time thursday_2">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('thurs_end_time_2')}}"
                                                                                 name="thurs_end_time_2"
                                                                                 class="form-control time-mask shift2_end_time thursday_2">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Friday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('fri_start_time_2')}}"
                                                                                 name="fri_start_time_2"
                                                                                 class="form-control timepicker shift2_start_time friday_2">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('fri_end_time_2')}}"
                                                                                 name="fri_end_time_2"
                                                                                 class="form-control time-mask shift2_end_time friday_2">
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group col-md-12 display" style="display: none" id="shift_3">
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
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('sat_start_time_3')}}"
                                                                                 name="sat_start_time_3"
                                                                                 class="form-control timepicker shift3_start_time saturday_3">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('sat_end_time_3')}}"
                                                                                 name="sat_end_time_3"
                                                                                 class="form-control time-mask shift3_end_time saturday_3">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Sunday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('sun_start_time_3')}}"
                                                                                 name="sun_start_time_3"
                                                                                 class="form-control timepicker shift3_start_time sunday_3">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('sun_end_time_3')}}"
                                                                                 name="sun_end_time_3"
                                                                                 class="form-control time-mask shift3_end_time sunday_3">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Monday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('mon_start_time_3')}}"
                                                                                 name="mon_start_time_3"
                                                                                 class="form-control timepicker shift3_start_time monday_3">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('mon_end_time_3')}}"
                                                                                 name="mon_end_time_3"
                                                                                 class="form-control time-mask shift3_end_time monday_3">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tuesday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('tues_start_time_3')}}"
                                                                                 name="tues_start_time_3"
                                                                                 class="form-control timepicker shift3_start_time tuesday_3">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('tues_end_time_3')}}"
                                                                                 name="tues_end_time_3"
                                                                                 class="form-control time-mask shift3_end_time tuesday_3">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Wednesday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('wed_start_time_3')}}"
                                                                                 name="wed_start_time_3"
                                                                                 class="form-control timepicker shift3_start_time wednesday_3">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('wed_end_time_3')}}"
                                                                                 name="wed_end_time_3"
                                                                                 class="form-control time-mask shift3_end_time wednesday_3">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Thursday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('thurs_start_time_3')}}"
                                                                                 name="thurs_start_time_3"
                                                                                 class="form-control timepicker shift3_start_time thursday_3">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('thurs_end_time_3')}}"
                                                                                 name="thurs_end_time_3"
                                                                                 class="form-control time-mask shift3_end_time thursday_3">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Friday</td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('fri_start_time_3')}}"
                                                                                 name="fri_start_time_3"
                                                                                 class="form-control timepicker shift3_start_time friday_3">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bootstrap-timepicker"><input autocomplete="off" type="text"
                                                                                 value="{{Input::old('fri_end_time_3')}}"
                                                                                 name="fri_end_time_3"
                                                                                 class="form-control time-mask shift3_end_time friday_3">
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('physicianSchedules')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop