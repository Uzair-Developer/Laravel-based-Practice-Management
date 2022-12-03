@extends('layout/main')

@section('title')
    - Reservations
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

            $(document).on('click', '.revisitReserveBtn', function (e) {
                var reservation_id = $(this).attr('reservation_id');
                $("#revisit_reservation_id").val(reservation_id);
                $.ajax({
                    url: '{{route('getReservationData')}}',
                    method: 'POST',
                    data: {
                        reservation_id: reservation_id,
                        getPhysicians: true
                    },
                    success: function (data) {
                        if (data) {
                            $("#revisit_date_div").html(data.inputDate);
                            $("#revisit_phone").html(data.patient['phone']);
                            $("#revisit_first_name").html(data.patient['first_name']);
                            $("#revisit_middle_name").html(data.patient['middle_name']);
                            $("#revisit_last_name").html(data.patient['last_name']);
                            $("#revisit_clinic_name").html(data.reservation['clinic_name']);
                            $("#revisit_clinic_id").val(data.reservation['clinic_id']);
                            $("#revisit_physician_id").html(data.physicianHtml).select2();
                            $("#formRevisitReservation").attr('action', '{{route('createRevisitReservation')}}');
                            $("#modalRevisitReservation").modal('show');
                        }
                    }
                });
            });

            $("#revisit_physician_id").change(function (e) {
                $("#revisit_date").val('');
                $("#revisit_time").html('<option value="">Choose</option>');
            });

            $(document).on('blur', '#revisit_date', function (e) {
                $("#revisit_time").attr('disabled', 'disabled');
                if ($("#revisit_physician_id").val().length == 0) {
                    alert('Please, select one physician!');
                    return;
                }
                setTimeout(function () {
                    $.ajax({
                        url: '{{route('getAvailableRevisitTime')}}',
                        method: 'POST',
                        data: {
                            reservation_id: $("#revisit_reservation_id").val(),
                            date: $("#revisit_date").val(),
                            physician_id: $("#revisit_physician_id").val(),
                            clinic_id: $("#revisit_clinic_id").val()
                        },
                        success: function (data) {
                            $("#revisit_time").html(data).removeAttr('disabled');
                        }
                    });
                }, 500);
            });

            $(document).on('click', '.editRevisitReserveBtn', function (e) {
                var reservation_id = $(this).attr('reservation_id');
                $("#revisit_reservation_id").val(reservation_id);
                $.ajax({
                    url: '{{route('getReservationData')}}',
                    method: 'POST',
                    data: {
                        reservation_id: reservation_id,
                        getPhysicians: true,
                        editRevisit: true
                    },
                    success: function (data) {
                        if (data) {
                            $("#revisit_date_div").html(data.inputDate);
                            $("#revisit_phone").html(data.patient['phone']);
                            $("#revisit_first_name").html(data.patient['first_name']);
                            $("#revisit_middle_name").html(data.patient['middle_name']);
                            $("#revisit_last_name").html(data.patient['last_name']);
                            $("#revisit_clinic_name").html(data.reservation['clinic_name']);
                            $("#revisit_clinic_id").val(data.reservation['clinic_id']);
                            $("#revisit_physician_id").html(data.physicianHtml).select2();
                            $("#formRevisitReservation").attr('action', '{{route('updateRevisitReservation')}}');
                            $("#modalRevisitReservation").modal('show');
                        }
                    }
                });
            });

            $("#selectPhysician4").change(function (e) {
                $("#modal_date").val('');
                $("#modal_time").html('<option value="">Choose</option>');
            });

            $(document).on('click', '.editReserveBtn', function (e) {
                var reservation_id = $(this).attr('reservation_id');
                $.ajax({
                    url: '{{route('editReservation')}}',
                    method: 'POST',
                    data: {
                        reservation_id: reservation_id
                    },
                    success: function (data) {
                        if (data) {
                            $("#modal_reservation_code").html(data.reservation['code']);
                            $("#modal_patient_id").html(data.patient['registration_no']);
                            $("#modal_phone").html(data.patient['phone']);
                            $("#modal_first_name").html(data.patient['first_name']);
                            $("#modal_middle_name").html(data.patient['middle_name']);
                            $("#modal_last_name").html(data.patient['last_name']);
                            $("#modal_family_name").html(data.patient['family_name']);
                            $("#modal_birthday").html(data.patient['birthday']);
                            if (data.patient['gender'] == 2) {
                                $("#modal_gender").html("Male");
                            } else if (data.patient['gender'] == 1) {
                                $("#modal_gender").html("Female");
                            }

                            $("#modal_reservation_id").val(data.reservation['id']);
                            $("#modal_date").val('');
                            $("#modal_time").html('');

                            $("#selectHospital4").val(data.hospital_id).select2();
                            var clinic_id = data.reservation['clinic_id'];
                            var physician_id = data.reservation['physician_id'];
                            $.ajax({
                                url: '{{route('getClinicsByHospitalId')}}',
                                method: 'POST',
                                data: {
                                    hospital_id: data.hospital_id
                                },
                                headers: {token: '{{csrf_token()}}'},
                                success: function (data) {
                                    $("#selectClinic4").removeAttr('disabled').html(data).val(clinic_id).attr('disabled', 'disabled').select2();
                                }
                            });
                            $.ajax({
                                url: '{{route('getPhysicianByClinicId')}}',
                                method: 'POST',
                                data: {
                                    clinic_id: clinic_id
                                },
                                headers: {token: '{{csrf_token()}}'},
                                success: function (data) {
                                    $("#selectPhysician4").removeAttr('disabled').html(data).val(physician_id).select2();
                                }
                            });

                            $("#modalEditReservation").modal('show');
                        }
                    }
                });
            });

            function getAvailablePhysicianTime() {
                setTimeout(function () {
                    var modal_date = $("#modal_date").val();
                    var selectPhysician4 = $("#selectPhysician4").val();
                    if (selectPhysician4.length == 0) {
                        alert('The physician field is required!');
                        return;
                    }
                    $('#modal_time').attr('disabled', 'disabled');
                    $.ajax({
                        url: '{{route('getAvailablePhysicianTime')}}',
                        method: 'POST',
                        data: {
                            reservation_id: $("#modal_reservation_id").val(),
                            physician_id: selectPhysician4,
                            clinic_id: $("#selectClinic4").val(),
                            date: modal_date
                        },
                        success: function (data) {
                            $("#modal_time").html('<option value="">Not Available</option>').html(data.modal_time_html).removeAttr('disabled');
                        }
                    });
                }, 500);
            }

            $("#modal_date").blur(function (e) {
                getAvailablePhysicianTime();
            });

            $("#updateReservation").submit(function (e) {
                e.preventDefault();
                var formData = getFormData($(this).serializeArray());
                $("#modalEditReservation").modal('hide');
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: {
                        data: formData
                    },
                    success: function (data) {
                        if(data.success == 'no') {
                            alert(data.msg);
                        } else {
                            window.location.reload();
                        }
                    }
                });
            });

            $(document).on('click', '.deleteReserveBtn', function (e) {
                if (confirm('Are You Sure?')) {
                    $("#modalCancelReservation").modal('show');
                    $("#modal_cancel_reservation_id").val($(this).attr('ref_id'));
                }
            });

            $("#modalCancelReservationBtn").click(function (e) {
                if ($("#cancel_reason_id").val() == "") {
                    alert('Cancel Reason is required');
                    return;
                }
                $.ajax({
                    url: "{{route('deleteReservation')}}",
                    method: 'POST',
                    data: {
                        reservation_id: $("#modal_cancel_reservation_id").val(),
                        cancel_notes: $("#reservation_cancel").val(),
                        cancel_reason_id: $("#cancel_reason_id").val(),
                        send_sms: $('input[name=send_sms]:checked', '#radio_sms').val(),
                        return_str: 'yes'
                    },
                    success: function (data) {
                        window.location.reload();
                    }
                });
            });
        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Reservations
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        Search
                        <button type="button" class="btn btn-box-tool pull-right"
                                data-widget="collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                    <!-- /.box-header -->
                    {{Form::open(array('role'=>"form",'method' => 'GET'))}}
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
                            <label>Physician Name</label>
                            <select id="selectPhysician2" name="physician_id" class="form-control select2">
                                <option value="">Choose</option>

                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Reservation Code</label>
                            <input type="text" name="code" value="{{Input::get('code')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Patient Id</label>
                            <input type="text" name="id" value="{{Input::get('id')}}" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Patient Phone</label>
                            <input type="text" maxlength="15" name="phone" value="{{Input::get('phone')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Patient Name</label>
                            <input type="text" name="name" value="{{Input::get('name')}}" class="form-control">
                        </div>

                        <div class="form-group col-md-3">
                            <label>National Id</label>
                            <input type="text" name="national_id" value="{{Input::get('national_id')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Reservation Status</label>
                            <select autocomplete="off" name="status"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                <option @if(Input::get('status') == 1) selected @endif value="1">Reserved
                                </option>
                                <option @if(Input::get('status') == 2) selected @endif value="2">On Progress
                                </option>
                                <option @if(Input::get('status') == 3) selected @endif value="3">Cancel</option>
                                <option @if(Input::get('status') == 5) selected @endif value="5">No Show
                                </option>
                                <option @if(Input::get('status') == 8) selected @endif value="8">Archive
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Patient Status</label>
                            <select autocomplete="off" name="patient_status"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                <option @if(Input::get('patient_status') === "0") selected @endif value="0">
                                    Waiting
                                </option>
                                <option @if(Input::get('patient_status') == 10) selected @endif value="10">
                                    Patient
                                    Attend
                                </option>
                                <option @if(Input::get('patient_status') == 1) selected @endif value="1">Patient
                                    In
                                </option>
                                <option @if(Input::get('patient_status') == 4) selected @endif value="4">Cancel
                                </option>
                                <option @if(Input::get('patient_status') == 5) selected @endif value="5">Pending
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Reservation Type</label>
                            <br>
                            <select autocomplete="off" name="type"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                <option @if(Input::get('type') == 1) selected @endif value="1">
                                    By Call
                                </option>
                                <option @if(Input::get('type') == 2) selected @endif value="2">
                                    Wait
                                </option>
                                <option @if(Input::get('type') == 3) selected @endif value="3">
                                    Revisit
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{route('receptionReservations')}}?hospital_id={{Input::get('hospital_id')}}&clinic_id={{Input::get('clinic_id')}}&date_from={{date('Y-m-d')}}&date_to={{date('Y-m-d')}}"
                           class="btn btn-info">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <div class="col-md-10" style="margin: 10px 0;">
                            <button class="btn" style="background: #84e184">Patient Attend</button>
                            <button class="btn" style="background: #32cd32">Patient In</button>
                            <button class="btn" style="background: deepskyblue">Patient Out</button>
                            <button class="btn" style="background: #ff8566">Cancel, NoShow, Archive</button>
                            <button class="btn" style="background: #ffb84d">Pending</button>
                            <button class="btn" style="background: #68ffec">In Service</button>
                        </div>
                        <button type="button" class="btn btn-box-tool pull-right"
                                data-widget="collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered" id="example1">
                            <thead>
                            <tr>
                                <th>Options</th>
                                <th>Attend Time</th>
                                <th>Patient ID</th>
                                <th>Physician Name</th>
                                <th>Patient Name</th>
                                <th>Date</th>
                                <th>Time From</th>
                                <th>Time To</th>
                                <th>Patient Phone</th>
                                <th>Clinic Name</th>
                                <th>Reservation Code</th>
                                <th>Reservation Status</th>
                                <th>Patient Status</th>
                                <th>Type</th>
                                <th>Queue Code</th>
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
                                            <div class="btn-group" style="width: 220px;">
                                                @if($val['patient_status'] == \core\enums\PatientStatus::cancel)
                                                    <a reservation_id="{{$val['id']}}"
                                                       title="Cancellation Info"
                                                       class="btn btn-info cancelInfoBtn"><i
                                                                class="fa fa-search"></i></a>
                                                @endif
                                                @if($val['type'] == 2)
                                                    @if($val['patient_status'] == \core\enums\PatientStatus::waiting && $val['walk_in_approval'] == '0')
                                                        @if($c_user->user_type_id == 1 || $c_user->hasAccess('manageReservation.waitListApproval'))
                                                            <a class="btn btn-flickr ask-me"
                                                               title="Approved"
                                                               href="{{route('approvedWalkInReservation', $val['id'])}}">
                                                                <i class="fa fa-calendar-check-o"></i>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                                @if($val['date'] == date('Y-m-d') && $c_user->user_type_id == \core\enums\UserRules::receptionPersonnel
                                                && $c_user->hasAccess('manageReservation.call_callDone'))
                                                    <a class="btn btn-flickr ask-me"
                                                       title="Call"
                                                       href="{{route('receptionCallPatientReservation', array($val['id'], 'call'))}}">
                                                        <i class="fa fa-microphone"></i>
                                                    </a>
                                                    @if($val['reception_call_flag'] != 2)
                                                        <a class="btn btn-linkedin ask-me"
                                                           title="Call Done"
                                                           href="{{route('receptionCallPatientReservation', array($val['id'], 'call-done'))}}">
                                                            <i class="fa fa-check"></i>
                                                        </a>
                                                    @endif
                                                @endif
                                                @if($val['patient_status'] == \core\enums\PatientStatus::waiting)
                                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('manageReservation.patient_attend'))
                                                        <a @if($val['patient_attend'] == 0)
                                                           title="Patient Attend"
                                                           @else
                                                           title="Patient Not Attend"
                                                           @endif
                                                           class="btn btn-default ask-me"
                                                           href="{{route('managePatientAttendReservation', $val['id'])}}">
                                                            @if($val['patient_attend'] == 0)
                                                                <i class="fa fa-check-circle"></i>
                                                            @else
                                                                <i class="fa fa-circle-thin"></i>
                                                            @endif
                                                        </a>
                                                    @endif
                                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('manageReservation.patient_pending_resume'))
                                                        <a class="btn btn-warning ask-me" title="Pending"
                                                           href="{{route('changeStatusPatientReservation', array($val['id'], \core\enums\PatientStatus::pending))}}">
                                                            <i class="fa fa-exclamation"></i>
                                                        </a>
                                                    @endif
                                                @endif
                                                @if($val['patient_status'] == \core\enums\PatientStatus::pending)
                                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('manageReservation.patient_pending_resume'))
                                                        <a class="btn btn-default ask-me" title="Resume"
                                                           href="{{route('changeStatusPatientReservation', array($val['id'], \core\enums\PatientStatus::waiting))}}">
                                                            <i class="fa fa-check"></i>
                                                        </a>
                                                    @endif
                                                @endif
                                                @if($val['type'] != 3)
                                                    @if(($val['patient_status'] == \core\enums\PatientStatus::waiting
                                                    || $val['patient_status'] == \core\enums\PatientStatus::pending
                                                    || $val['patient_status'] == \core\enums\PatientStatus::archive)
                                                    && ($c_user->hasAccess('reservation.edit') || $c_user->user_type_id == 1))
                                                        <a reservation_id="{{$val['id']}}"
                                                           title="Edit"
                                                           class="btn btn-default editReserveBtn"><i
                                                                    class="fa fa-pencil"></i></a>
                                                    @endif
                                                @else
                                                    @if($val['patient_status'] == \core\enums\PatientStatus::waiting
                                                        || $val['patient_status'] == \core\enums\PatientStatus::pending
                                                        || $val['patient_status'] == \core\enums\PatientStatus::archive
                                                        && ($c_user->hasAccess('reservation.edit') || $c_user->user_type_id == 1)
                                                        && ($val['patient_in_service'] != 1))
                                                        <a reservation_id="{{$val['id']}}"
                                                           title="Edit"
                                                           class="btn btn-default editRevisitReserveBtn"><i
                                                                    class="fa fa-pencil"></i></a>
                                                    @endif
                                                @endif
                                                @if($val['patient_status'] == \core\enums\PatientStatus::waiting
                                                            || $val['patient_status'] == \core\enums\PatientStatus::pending)
                                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('manageReservation.patient_cancel'))
                                                        <a ref_id="{{$val['id']}}" title="Cancel"
                                                           class="btn btn-danger deleteReserveBtn"><i
                                                                    class="fa fa-times"></i></a>
                                                    @endif
                                                @endif
                                                @if($val['type'] != 3)
                                                    @if($physician['revisit_limit'] && ($val['patient_status'] == \core\enums\PatientStatus::patient_in
                                                    || $val['patient_status'] == \core\enums\PatientStatus::patient_out
                                                    || $val['patient_status'] == \core\enums\PatientStatus::no_show)
                                                    && $count_revisit == 0
                                                    && ($c_user->hasAccess('manageReservation.revisit') || $c_user->user_type_id == 1)
                                                    && ($val['patient_in_service'] != 1))
                                                        <a reservation_id="{{$val['id']}}"
                                                           title="Revisit"
                                                           class="btn btn-default revisitReserveBtn"><i
                                                                    class="fa fa-repeat"></i></a>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                {{date('h:i A', strtotime($val['patient_attend_datetime']))}}
                                            </div>
                                        </td>
                                        <td>{{$patient['registration_no']}}</td>

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
                                        <td>{{Clinic::getNameById($val['clinic_id'])}}</td>
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

    <div class="modal fade" id="modalRevisitReservation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Revisit</h4>
                </div>
                {{Form::open(array('role'=>"form", 'id' => 'formRevisitReservation'))}}
                <div class="modal-body col-md-12">
                    <div class="form-group col-md-4">
                        <label>Phone</label>

                        <div id="revisit_phone"></div>
                    </div>

                    <div class="form-group col-md-4">
                        <label>First Name</label>

                        <div id="revisit_first_name"></div>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Middle Name</label>

                        <div id="revisit_middle_name"></div>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Last Name</label>

                        <div id="revisit_last_name"></div>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Clinic</label>

                        <div id="revisit_clinic_name"></div>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Physician *</label>
                        <select id="revisit_physician_id" required name="physician_id" class="form-control"
                                style="width:250px;">
                            <option value="">Choose</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Date Of Revisit *</label>

                        <div id="revisit_date_div">
                            <input autocomplete="off" id="revisit_date" required type="text"
                                   data-date-format="yyyy-mm-dd"
                                   name="date" class="form-control limit_datepicker">
                        </div>
                        <input type="hidden" id="revisit_reservation_id" name="reservation_id">
                        <input type="hidden" id="revisit_clinic_id" name="clinic_id">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Time *</label>
                        <select id="revisit_time" required name="time" class="form-control">
                            <option value="">Choose</option>
                        </select>
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

    <div class="modal fade" id="modalEditReservation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Edit Reservation</h4>
                </div>
                {{Form::open(array('role'=>"form", 'route' => 'updateReservation', 'id' => 'updateReservation'))}}
                <div class="modal-body col-md-12">
                    <div class="form-group col-md-4">
                        <label>Reservation Code</label>

                        <div id="modal_reservation_code"></div>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Patient Id</label>

                        <div id="modal_patient_id"></div>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Phone</label>

                        <div id="modal_phone"></div>
                    </div>

                    <div class="form-group col-md-4">
                        <label>First Name</label>

                        <div id="modal_first_name"></div>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Middle Name</label>

                        <div id="modal_middle_name"></div>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Last Name</label>

                        <div id="modal_last_name"></div>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Family Name</label>

                        <div id="modal_family_name"></div>
                    </div>
                    <input type="hidden" value="" name="reservation_id" id="modal_reservation_id">

                    <div class="form-group col-md-4">
                        <label>Birthday</label>

                        <div id="modal_birthday"></div>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Gender</label>

                        <div id="modal_gender"></div>
                    </div>

                    <div class="form-group col-md-12">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Hospital</label>
                        <br>
                        <select autocomplete="off" id="selectHospital4" name="hospital_id" disabled
                                class="form-control select2" style="width: 100%">
                            <option value="">Choose</option>
                            @foreach($hospitals as $val)
                                <option value="{{$val['id']}}">{{$val['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Clinics</label>
                        <br>
                        <select required autocomplete="off" id="selectClinic4" name="clinic_id" disabled
                                class="form-control select2" style="width: 100%">
                            <option value="">Choose</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Physicians</label>
                        <br>
                        <select required autocomplete="off" id="selectPhysician4" name="physician_id"
                                class="form-control select2"
                                style="width: 100%">
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Reservation Date</label>
                        <input required type="text" data-date-format="yyyy-mm-dd" id="modal_date"
                               name="date" class="form-control datepicker">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Available Time</label>
                        <select autocomplete="off" required class="form-control" name="time" id="modal_time">
                            <option value="">Choose</option>
                        </select>
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

    <div class="modal fade" id="modalCancelReservation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Cancel Reservation</h4>
                </div>
                <div class="modal-body col-md-12">
                    <div class="form-group col-md-12">
                        <label>Cancel Reason</label>
                        <br>
                        <select autocomplete="off" name="cancel_reason_id" id="cancel_reason_id"
                                class="form-control select2" style="width: 100%">
                            <option value="">Choose</option>
                            @foreach($cancelResReason as $val)
                                <option value="{{$val['id']}}">{{$val['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Reason Of Cancellation</label>
                        <textarea id="reservation_cancel" name="cancel_notes" class="form-control"></textarea>
                        <input type="hidden" id="modal_cancel_reservation_id" name="reservation_id">
                    </div>
                    <div class="form-group col-md-6" id="radio_sms">
                        <label>Send SMS? </label>

                        <div class="radio">
                            <label>
                                <input autocomplete="off" id="male" type="radio"
                                       value="1"
                                       name="send_sms" checked>
                                Yes
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input autocomplete="off" id="female" type="radio"
                                       value="2" name="send_sms">
                                No
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="modalCancelReservationBtn" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

@stop
