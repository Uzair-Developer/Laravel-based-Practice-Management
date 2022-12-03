@extends('layout/main')

@section('title')
    - Today Reservations
@stop


@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/autocomplete/jquery.autocomplete.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('plugins/autocomplete/jquery.autocomplete.js')}}"></script>

    <script>
        $(document).ready(function () {
            $('#example1').DataTable({
                "paging": false,
                "lengthChange": true,
                "searching": true,
                "ordering": false,
                "info": true,
                "autoWidth": true,
                //"order": [[4, "desc"]],
                "sScrollY": "400px",
                "sScrollX": "100%",
                "sScrollXInner": "250%",
                "bScrollCollapse": true
            });

            $(document).on('click', '.cancelInfoBtn', function (e) {
                var reservation_id = $(this).attr('reservation_id');
                $.ajax({
                    url: '{{route('getReservationData')}}',
                    method: 'POST',
                    data: {
                        reservation_id: reservation_id
                    },
                    success: function (data) {
                        if (data) {
                            $("#modal_cancel_reason_id").html(data.reservation['cancel_reason_name']);
                            $("#modal_cancel_notes").html(data.reservation['cancel_notes']);
                            if (data.reservation['send_cancel_sms'] == 2) {
                                $("#modal_send_cancel_sms").html("No");
                            } else if (data.reservation['send_cancel_sms'] == 1) {
                                $("#modal_send_cancel_sms").html("Yes");
                            }
                            $("#modalCancelInfoReservation").modal('show');
                        }
                    }
                });
            });

            $(".getParentReservation").click(function (e) {
                var reservation_id = $(this).attr('reservation_id');
                $.ajax({
                    url: '{{route('getParentReservationData')}}',
                    method: 'POST',
                    data: {
                        reservation_id: reservation_id
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#parent_clinic_name").html(data.reservation['clinic_name']);
                        $("#parent_physician_name").html(data.reservation['physician_name']);
                        $("#parent_code").html(data.reservation['code']);
                        $("#parent_reservation_date").html(data.reservation['date']);
                        $("#parent_reservation_time").html(data.reservation['time_from']);
                        $("#parent_patient_name").html(data.patient['name']);
                        $("#parent_patient_id").html(data.patient['registration_no']);
                        $("#parent_patient_phone").html(data.patient['phone']);
                        $("#parentReservationInfo").modal('show');
                    }
                });
            });

            $(".patientOutBtn").click(function (e) {
                e.preventDefault();
                var reservation_id = $(this).attr('reservation_id');
                $.ajax({
                    url: '{{route('getReservationData')}}',
                    method: 'POST',
                    data: {
                        reservation_id: reservation_id
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#patient_out_reservation_id").val('').val(data.reservation['id']);
                        $("#patient_out_clinic_name").html('').html(data.reservation['clinic_name']);
                        $("#patient_out_physician_name").html('').html(data.reservation['physician_name']);
                        $("#patient_out_reservation_code").html('').html(data.reservation['code']);
                        $("#patient_out_reservation_date").html('').html(data.reservation['date']);
                        $("#patient_out_patient_name").html('').html(data.patient['name']);
                        $("#patient_out_patient_id").html('').html(data.patient['registration_no']);
                        $("#patient_out_patient_phone").html('').html(data.patient['phone']);
                        $("#patientOutModal").modal('show');
                    }
                });
            });

            $("#patientInService").click(function (e) {
                var url = '{{route('patientOutOrPatientInService') . '?type=in_service'}}';
                $("#patientOutForm").attr('action', url).submit();
            });

            $("#patientOutDone").click(function (e) {
                var url = '{{route('patientOutOrPatientInService') . '?type=patient_out'}}';
                $("#patientOutForm").attr('action', url).submit();
            });

            var checkedArray = new Array();
            $(document).on('change', '.patientAttendCheckbox', function () {
                if ($(".patientAttendCheckbox:checked").length > 0) {
                    $("#patientAttendAction").show();
                }
                else {
                    $("#patientAttendAction").hide();
                }
                var checkedValues = $('.patientAttendCheckbox:checked').map(function () {
                    return $(this).attr('reservation_id');
                }).get();
                $('#patientAttendInput').val(checkedValues);
            });


                    @if(Session::get('next_patient_flag') == 1)
            var buttonUrl = $("#next_patient").attr('href');
            $("#next_patient").removeAttr('href');
            $("#next_patient").attr('disabled', 'disabled');
            setTimeout(function () {
                $("#next_patient").removeAttr('disabled');
                $("#next_patient").attr('href', buttonUrl);
            }, 120000);
            @endif

            $("#next_patient").click(function (e) {
                var buttonUrl = $(this).attr('href');
                if (typeof buttonUrl !== typeof undefined && buttonUrl !== false) {
                    e.preventDefault();
                    $.ajax({
                        url: '{{route('getInClinicReservationOfPhysician')}}',
                        method: 'POST',
                        headers: {token: '{{csrf_token()}}'},
                        success: function (data) {
                            if (data.success == 'yes') {
                                window.location.replace(buttonUrl);
                            } else {
                                $("#patient_out_reservation_id2").val('').val(data.reservation['id']);
                                $("#patient_out_clinic_name2").html('').html(data.reservation['clinic_name']);
                                $("#patient_out_physician_name2").html('').html(data.reservation['physician_name']);
                                $("#patient_out_reservation_code2").html('').html(data.reservation['code']);
                                $("#patient_out_reservation_date2").html('').html(data.reservation['date']);
                                $("#patient_out_patient_name2").html('').html(data.patient['name']);
                                $("#patient_out_patient_id2").html('').html(data.patient['registration_no']);
                                $("#patient_out_patient_phone2").html('').html(data.patient['phone']);
                                $("#patientOutModal2").modal('show');
                            }
                        }
                    });
                }
            });

            $("#patientInService2").click(function (e) {
                var url = '{{route('patientOutOrPatientInService') . '?type=in_service'}}';
                $("#patientOutForm2").attr('action', url).submit();
            });

            $("#patientOutDone2").click(function (e) {
                var url = '{{route('patientOutOrPatientInService') . '?type=patient_out'}}';
                $("#patientOutForm2").attr('action', url).submit();
            });

            setInterval(function () {
                var urlParams;
                (window.onpopstate = function () {
                    var match,
                        pl = /\+/g,  // Regex for replacing addition symbol with a space
                        search = /([^&=]+)=?([^&]*)/g,
                        decode = function (s) {
                            return decodeURIComponent(s.replace(pl, " "));
                        },
                        query = window.location.search.substring(1);

                    urlParams = {};
                    while (match = search.exec(query))
                        urlParams[decode(match[1])] = decode(match[2]);
                })();
                var params = jQuery.param(urlParams);
                $.ajax({
                    url: '{{route('getReservationTotalCountRefresh')}}?' + params,
                    method: 'POST',
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#total_count_refresh").html(data);
                    }
                });
            }, 3000);
        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Today Reservations
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <div class="col-md-7" style="margin: 10px 0;">
                            <button class="btn" style="background: #84e184">Patient Attend</button>
                            <button class="btn" style="background: #32cd32">Patient In</button>
                            <button class="btn" style="background: deepskyblue">Patient Out</button>
                            <button class="btn" style="background: #ff8566">Cancel, NoShow, Archive</button>
                            <button class="btn" style="background: #ffb84d">Pending</button>
                            <button class="btn" style="background: #68ffec">In Service</button>
                        </div>
                        <div class="clearfix"></div>
                        @if($c_user->user_type_id == \core\enums\UserRules::physician && $c_user->hasAccess('manageReservation.nextPatientBtn'))

                            <div class="col-md-2" style="margin: 10px 0;">
                                <a id="next_patient" href="{{route('nextPatientInReservation')}}"
                                   class="btn btn-primary">Next Patient</a>
                            </div>
                            <div class="col-md-5" style="margin: 10px 0;" id="total_count_refresh">
                                {{$total_count_refresh}}
                            </div>
                        @endif
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered" id="example1">
                            <thead>
                            <tr>
                                <th>Options</th>
                                <th>Type</th>
                                <th>Queue Code</th>
                                <th>Clinic Name</th>
                                <th>Physician Name</th>
                                <th>Patient Name</th>
                                <th>Date</th>
                                <th>Time From</th>
                                <th>Time To</th>
                                <th>Patient Phone</th>
                                <th>Patient ID</th>
                                <th>Reservation Code</th>
                                <th>Reservation Status</th>
                                <th>Patient Status</th>
                                <th>Revisits Count</th>
                                <th>Notes</th>
                                <th>Exception Reason</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(Input::get('hospital_id'))
                                @foreach($reservations as $key => $val)
                                    <?php
                                    $physician = User::getById($val['physician_id']);
                                    $patient = Patient::getById($val['patient_id']);
                                    $count_revisit = Reservation::countRevisitOfReservation($val['id'])
                                    ?>
                                    <tr style="
                                    @if($val['patient_attend'] == 1)
                                            background:#84e184;
                                    @endif
                                    @if($val['patient_status'] == \core\enums\PatientStatus::patient_in)
                                    @if($val['patient_in_service'] != 2)
                                            background:#68ffec;
                                    @else
                                            background:#32cd32;
                                    @endif
                                    @endif
                                    @if($val['patient_status'] == \core\enums\PatientStatus::patient_out)
                                            background:deepskyblue;
                                    @endif
                                    @if($val['patient_status'] == \core\enums\PatientStatus::cancel)
                                            background:#ff8566;
                                    @endif
                                    @if($val['patient_status'] == \core\enums\PatientStatus::no_show)
                                            background:#ff8566;
                                    @endif
                                    @if($val['patient_status'] == \core\enums\PatientStatus::pending)
                                            background:#ffb84d;
                                    @endif
                                    @if($val['patient_status'] == \core\enums\PatientStatus::archive)
                                            background:#ff8566;
                                    @endif">
                                        <td>
                                            <div class="btn-group" style="width: 130px;">
                                                @if($val['patient_status'] == \core\enums\PatientStatus::waiting)
                                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('manageReservation.patient_in'))
                                                        @if($val['patient_attend'] == 1 && User::getById($val['physician_id'])['is_ready'] == 1)
                                                            <a class="btn btn-default ask-me"
                                                               title="Patient In"
                                                               href="{{route('managePatientReservation', array($val['id'], \core\enums\PatientStatus::patient_in))}}">
                                                                <i class="fa fa-arrow-down"></i>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                                @if($val['patient_status'] == \core\enums\PatientStatus::patient_in
                                                    && ($val['patient_in_service'] != 1))
                                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('manageReservation.patient_out'))
                                                        <a class="btn btn-danger patientOutBtn"
                                                           title="Patient Out" reservation_id="{{$val['id']}}">
                                                            <i class="fa fa-arrow-up"></i>
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($val['type'] == 1)
                                                Call
                                            @elseif($val['type'] == 2)
                                                Waiting
                                            @elseif($val['type'] == 3)
                                                Revisit
                                            @endif
                                        </td>
                                        <td>
                                            @if($val['queue_code'])
                                                <?php
                                                $code = explode('-', $val['queue_code']);
                                                ?>
                                                {{$code[0] . $code[1] . $code[2] . '-' . $code[3]}}
                                            @endif
                                        </td>
                                        <td>{{Clinic::getNameById($val['clinic_id'])}}</td>
                                        <td>
                                            <div style="width: 150px;">
                                                {{ucwords(strtolower($physician['full_name']))}}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="width: 150px;">
                                                {{ucwords(strtolower($patient['name']))}}
                                            </div>
                                        </td>
                                        <td>{{$val['date']}}</td>
                                        <td>
                                            @if($val['type'] == 3)
                                                <?php
                                                $seconds = Functions::hoursToSeconds($val['revisit_time_from']);
                                                $newSeconds = $seconds + (10 * 60);
                                                $futureTime = Functions::timeFromSeconds($newSeconds);
                                                ?>
                                                {{$futureTime}}
                                            @else
                                                {{$val['time_from']}}
                                            @endif
                                        </td>
                                        <td>{{$val['time_to']}}</td>
                                        <td>{{$patient['phone']}}</td>
                                        <td>{{$patient['registration_no']}}</td>
                                        <td>{{$val['code']}}</td>
                                        <td>
                                            @if($val['status'] == \core\enums\ReservationStatus::reserved)
                                                Reserved
                                            @elseif($val['status'] == \core\enums\ReservationStatus::accomplished)
                                                Accomplished
                                            @elseif($val['status'] == \core\enums\ReservationStatus::cancel)
                                                <span>Cancel</span>
                                            @elseif($val['status'] == \core\enums\ReservationStatus::on_progress)
                                                On Progress
                                            @elseif($val['status'] == \core\enums\ReservationStatus::no_show)
                                                <span>No Show</span>
                                            @elseif($val['status'] == \core\enums\ReservationStatus::pending)
                                                <span>Pending</span>
                                            @elseif($val['status'] == \core\enums\ReservationStatus::archive)
                                                <span>Archive</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($val['patient_status'] == \core\enums\PatientStatus::waiting)
                                                @if($val['patient_attend'] == 1)
                                                    <span>Patient Attend</span>
                                                @else
                                                    Waiting
                                                @endif
                                            @elseif($val['patient_status'] == \core\enums\PatientStatus::patient_in)
                                                @if($val['patient_in_service'] == 1)
                                                    <span>In Service</span>
                                                @elseif($val['patient_in_service'] == 3)
                                                    Service Done
                                                @else
                                                    Patient In
                                                @endif
                                            @elseif($val['patient_status'] == \core\enums\PatientStatus::patient_out)
                                                Patient Out
                                            @elseif($val['patient_status'] == \core\enums\PatientStatus::cancel)
                                                <span>Cancel</span>
                                            @elseif($val['patient_status'] == \core\enums\PatientStatus::no_show)
                                                <span>No Show</span>
                                            @elseif($val['patient_status'] == \core\enums\PatientStatus::pending)
                                                <span>Pending</span>
                                            @elseif($val['patient_status'] == \core\enums\PatientStatus::archive)
                                                <span>Archive</span>
                                            @endif
                                        </td>
                                        @if($count_revisit == 0)
                                            <td>
                                                {{$count_revisit}}
                                            </td>
                                        @else
                                            <td class="getRevisitReservation"
                                                reservation_id="{{$val['id']}}"
                                                style="text-decoration:underline;color:blue;cursor: pointer;">{{$count_revisit}}</td>
                                        @endif
                                        <td>{{$val['notes']}}</td>
                                        <td>
                                            @if($val['patient_status'] == \core\enums\PatientStatus::pending)
                                                {{$val['exception_reason']}}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        {{$reservations->appends(Input::except('_token'))->links()}}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalCancelInfoReservation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Cancellation Info</h4>
                </div>
                <div class="modal-body col-md-12">
                    <div class="form-group col-md-12">
                        <label>Reason Of Cancellation</label>

                        <div id="modal_cancel_reason_id"></div>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Cancellation Note</label>

                        <div id="modal_cancel_notes"></div>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Send SMS</label>

                        <div id="modal_send_cancel_sms"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="patientOutModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Patient Out Or Patient In Service!</h4>
                </div>
                {{Form::open(array('id' => 'patientOutForm'))}}
                <div class="modal-body col-md-12">
                    <div class="form-group col-md-6">
                        <label>Clinic Name</label>

                        <div id="patient_out_clinic_name"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Physician Name</label>

                        <div id="patient_out_physician_name"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Reservation Code</label>

                        <div id="patient_out_reservation_code"></div>
                        <input type="hidden" name="reservation_id" id="patient_out_reservation_id">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Reservation Date</label>

                        <div id="patient_out_reservation_date"></div>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Patient Name</label>

                        <div id="patient_out_patient_name"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Patient ID</label>

                        <div id="patient_out_patient_id"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Patient Phone</label>

                        <div id="patient_out_patient_phone"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="patientInService" type="button" class="btn btn-info">In Service</button>
                    <button id="patientOutDone" type="button" class="btn btn-danger">Patient Out</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>

    <div class="modal fade" id="patientOutModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">There is patient in your clinic, please take action with him</h4>
                </div>
                {{Form::open(array('id' => 'patientOutForm2'))}}
                <div class="modal-body col-md-12">
                    <div class="form-group col-md-6">
                        <label>Clinic Name</label>

                        <div id="patient_out_clinic_name2"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Physician Name</label>

                        <div id="patient_out_physician_name2"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Reservation Code</label>

                        <div id="patient_out_reservation_code2"></div>
                        <input type="hidden" name="reservation_id" id="patient_out_reservation_id2">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Reservation Date</label>

                        <div id="patient_out_reservation_date2"></div>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Patient Name</label>

                        <div id="patient_out_patient_name2"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Patient ID</label>

                        <div id="patient_out_patient_id2"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Patient Phone</label>

                        <div id="patient_out_patient_phone2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="patientInService2" type="button" class="btn btn-info">In Service</button>
                    <button id="patientOutDone2" type="button" class="btn btn-danger">Patient Out</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
@stop
