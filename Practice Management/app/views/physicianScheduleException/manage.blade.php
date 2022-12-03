@extends('layout/main')

@section('title')
    - Manage Physician Schedule Exceptions
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datetimepicker/jquery.datetimepicker.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/loading_mask/waitMe.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('plugins/datetimepicker/jquery.datetimepicker.full.js')}}"></script>
    <script src="{{asset('plugins/loading_mask/waitMe.js')}}"></script>
    <script>
        $(function () {
            $('.timepicker').datetimepicker({
                datepicker: false,
                format: 'H:i',
                step: 5,
                {{--minDate: '{{date('Y-m-d')}}'--}}
            });

            $("#physicianDateTime").html('');
            $("#physicianDateTimeDiv").hide();

            $("#selectHospital2").change(function (e) {
                $("#physicianDateTime").html('');
                $("#physicianDateTimeDiv").hide();
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
                $("#physicianDateTime").html('');
                $("#physicianDateTimeDiv").hide();
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
                $("#physicianDateTime").html('');
                $("#physicianDateTimeDiv").hide();
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
            function loadPhysicianDateTimeTable() {
                $("#physicianDateTime").html('');
                $("#physicianDateTimeDiv").hide();
                $.ajax({
                    url: '{{route('getPhysicianScheduleExceptionsDateTime')}}',
                    method: 'POST',
                    data: {
                        physician_id: $('#selectPhysician2').val(),
                        clinic_id: $('#selectClinic2').val(),
                        physician_schedule_id: $("#physician_schedule_id").val()
                    },
                    success: function (data) {
                        $('#loading').waitMe('hide');
                        if (data.success == 'yes') {
                            $("#physicianDateTime").html(data.html);
                            $(".physiciansName").html($('#selectPhysician2 option:selected').html());
                            $(".clinicsName").html($('#selectClinic2 option:selected').html());
                            $("#physicianDateTimeDiv").show();
                        } else {
                            alert(data.message);
                        }
                    }
                });
            }

            $("#physician_schedule_id").change(function (e) {
                withMe('#loading');
                loadPhysicianDateTimeTable();
            });

            $(document).on('click', '.manageException', function (e) {
                var date = $(this).attr('date');
                withMe('#loading');
                $.ajax({
                    url: '{{route('getScheduleWithDate')}}',
                    method: 'POST',
                    data: {
                        physician_id: $('#selectPhysician2').val(),
                        clinic_id: $('#selectClinic2').val(),
                        physician_schedule_id: $("#physician_schedule_id").val(),
                        date: date
                    },
                    success: function (data) {
                        $('#loading').waitMe('hide');
                        if (data.success == 'yes') {
                            $("#updateExceptionBody").html('').html(data.html);
                            $("#updateExceptionModal").modal('show');
                        } else {
                            alert(data.message);
                        }
                    }
                });
            });

            $(document).on('change', 'select[name="shift1_dayoff"]', function (e) {
                var value = $(this).val();
                if(value == 1) {
                    $("input[name='shift1_time_from'], input[name='shift1_time_to']")
                            .attr('disabled', 'disabled');
                } else {
                    $("input[name='shift1_time_from'], input[name='shift1_time_to']")
                            .removeAttr('disabled');
                }
            });

            $(document).on('change', 'select[name="shift2_dayoff"]', function (e) {
                var value = $(this).val();
                if(value == 1) {
                    $("input[name='shift2_time_from'], input[name='shift2_time_to']")
                            .attr('disabled', 'disabled');
                } else {
                    $("input[name='shift2_time_from'], input[name='shift2_time_to']")
                            .removeAttr('disabled');
                }
            });

            $(document).on('change', 'select[name="shift3_dayoff"]', function (e) {
                var value = $(this).val();
                if(value == 1) {
                    $("input[name='shift3_time_from'], input[name='shift3_time_to']")
                            .attr('disabled', 'disabled');
                } else {
                    $("input[name='shift3_time_from'], input[name='shift3_time_to']")
                            .removeAttr('disabled');
                }
            });

            $("#updateExceptionForm").submit(function (e) {
                e.preventDefault();
                $("#updateExceptionModal").modal('hide');
                withMe('#loading');
                var form = $(this).serialize();
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: {
                        physician_id: $('#selectPhysician2').val(),
                        clinic_id: $('#selectClinic2').val(),
                        physician_schedule_id: $("#physician_schedule_id").val(),
                        form: form
                    },
                    success: function (data) {
                        $('#loading').waitMe('hide');
                        if (data.success == 'yes') {
                            alert("Updated Successfully");
                        } else {
                            alert(data.message);
                        }
                    }
                });
            });

            $(document).on('click', '.updateException', function (e) {
                if (confirm('All reservations in this day will be archived!')) {
                    var id = $(this).attr('table_id');
                    var date = $(this).attr('date');
                    var timeFrom = $("#" + date + '--time_from').val();
                    var timeTo = $("#" + date + '--time_to').val();
                    var slot = $("#" + date + '--slot').val();
                    $.ajax({
                        url: '{{route('updatePhysicianScheduleException')}}',
                        method: 'POST',
                        data: {
                            id: id,
                            physician_id: $('#selectPhysician2').val(),
                            clinic_id: $('#selectClinic2').val(),
                            physician_schedule_id: $("#physician_schedule_id").val(),
                            date: date,
                            timeFrom: timeFrom,
                            timeTo: timeTo,
                            slot: slot
                        },
                        success: function (data) {
                            if (data.success == 'yes') {
                                alert("Updated Successfully");
                            } else {
                                alert(data.message);
                            }
                        }
                    });
                }
            });
        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Manage Physician Schedule Exceptions
        </h1>
    </section>

    <section class="content">
        <div class="row">

            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-body" id="loading">

                        <div class="form-group col-md-6">
                            <label>Hospital</label>
                            <br>
                            <select autocomplete="off" id="selectHospital2" name="hospital_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    <option value="{{$val['id']}}">{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Clinic</label>
                            <br>
                            <select autocomplete="off" id="selectClinic2" name="clinic_id" class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Physicians</label>
                            <br>
                            <select autocomplete="off" id="selectPhysician2" class="form-control select2"
                                    name="user_id">
                                <option value="">Choose</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Physician Schedules</label>
                            <select autocomplete="off" id="physician_schedule_id" required name="physician_schedule_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>
                        <div class="col-md-12" id="physicianDateTimeDiv">
                            <div class="box box-primary">
                                <div class="box-header">
                                    Date Time Table
                                    <button type="button" class="btn btn-box-tool pull-right" data-widget="collapse">
                                        <i class="fa fa-minus"></i></button>
                                </div>
                                <div class="box-body table-responsive" id="physicianDateTime">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <a href="{{route('listPhysicianScheduleException')}}" class="btn btn-info">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="updateExceptionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="width: 65%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Physician Schedule Exception ( All reservations in this day will be archived! )</h4>
                </div>
                {{Form::open(array('route' => 'updatePhysicianScheduleException', 'id' => 'updateExceptionForm'))}}
                <div class="modal-body col-md-12" id="updateExceptionBody">

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