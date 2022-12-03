@extends('layout/main')

@section('title')
    - Stand Alone Revisits
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
    <script type="text/javascript">
        $(function () {
            $('#example3').DataTable({
                "paging": false,
                "lengthChange": true,
                "searching": true,
                "ordering": false,
                "info": true,
                "autoWidth": true,
//                    "order": [[4, "desc"], [5, 'asc']],
                "sScrollY": "400",
                "sScrollX": "100%",
                "sScrollXInner": "250%",
                "bScrollCollapse": true
            });

            $('.datepicker2').datepicker({
                todayHighlight: true,
                autoclose: true
            });
            $('.datepicker').datepicker({
                startDate: "1d",
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

            ///////////////////////////////////////////

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
                        $("#id3").autocomplete("destroy");
                        id3autocomplete('?hospital_id=' + $("#selectHospital4").val());
                        id3Blur();

                        phone3autocomplete('?hospital_id=' + $("#selectHospital4").val());
                        phone3KeyUp();

                        nationalid3autocomplete('?hospital_id=' + $("#selectHospital4").val());
                        nationalid3KeyUp();
                    }
                });
            });

            $("#selectClinic4").change(function (e) {
                $("#selectPhysician4").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getPhysicianByClinicId')}}',
                    method: 'POST',
                    data: {
                        clinic_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectPhysician4").removeAttr('disabled').html(data).select2();
                    }
                });
            });
            $("#selectPhysician4").change(function (e) {
                $("#revisit_date2").val('');
                $("#revisit_time2").val('');
            });

            $("#preferred_contact3").change(function (e) {
                if ($(this).val() == 2) {
                    $("#email3").attr('required', 'required');
                } else {
                    $("#email3").removeAttr('required');
                }
            });

            function id3autocomplete(params) {
                $("#id3").autocomplete({
                    url: '{{route('autoCompletePatient2')}}' + params,
                    minChars: 1,
                    useCache: false,
                    filterResults: false,
                    mustMatch: true,
                    maxItemsToShow: 10,
                    remoteDataType: 'json',
                    onItemSelect: function (item) {
                        $("#id3").val(item.data[0]);
                    }
                });
            }

            id3autocomplete('?hospital_id=' + $("#selectHospital4").val());

            function id3Blur() {
                $("#id3").blur(function (e) {
                    if (!$("#id3").val()) {
                        return;
                    }
                    setTimeout(function () {
                        getPatientData3($("#id3"));
                    }, 500);
                });
            }

            id3Blur();

            function phone3autocomplete(param) {
                $("#phone4").autocomplete("destroy");
                $("#phone4").autocomplete({
                    url: '{{route('autoCompletePatientByPhone')}}' + param,
                    minChars: 2,
                    useCache: false,
                    filterResults: false,
                    mustMatch: false,
                    maxItemsToShow: 10,
                    remoteDataType: 'json',
                    onItemSelect: function (item) {
                        if (item.data[0]) {
                            $("#phone4").val(item.data[0]);
                            $("#patient_id3").val(item.data[1]);
                            setTimeout(function () {
                                getPatientData3($("#patient_id3"));
                            }, 500);
                        }
                    }
                });
            }

            phone3autocomplete('?hospital_id=' + $("#selectHospital4").val());

            function phone3KeyUp() {
                $("#phone4").keyup(function (e) {
                    clearPatientDate3();
                });
            }

            phone3KeyUp();

            function nationalid3autocomplete(param) {
                $("#national_id3").autocomplete("destroy");
                $("#national_id3").autocomplete({
                    url: '{{route('autoCompletePatientByNationalId')}}' + param,
                    minChars: 2,
                    useCache: false,
                    filterResults: false,
                    mustMatch: false,
                    maxItemsToShow: 10,
                    remoteDataType: 'json',
                    onItemSelect: function (item) {
                        if (item.data[0]) {
                            $("#national_id3").val(item.data[0]);
                            $("#patient_id3").val(item.data[1]);
                            setTimeout(function () {
                                getPatientData3($("#patient_id3"));
                            }, 500);
                        }
                    }
                });
            }

            nationalid3autocomplete('?hospital_id=' + $("#selectHospital4").val());

            function nationalid3KeyUp() {
                $("#national_id3").keyup(function (e) {
                    clearPatientDate3();
                });
            }

            nationalid3KeyUp();

            function getPatientData3(opj) {
                var id = $(opj).attr('id');
                if (!$(opj).val()) {
                    return;
                }
                var input = $(opj);
                this_id = id == 'patient_id3';
                this_national_id = id == 'national_id3';
                $('#modalRevisitReservation2 input').attr('disabled', 'disabled');
                $('#modalRevisitReservation2 textarea').attr('disabled', 'disabled');
                $.ajax({
                    url: "{{route('checkPatientExist')}}",
                    method: 'POST',
                    data: {
                        search: $(opj).val(),
                        this_id: this_id,
                        this_national_id: this_national_id,
                        hospital_id: $("#selectHospital4").val()
                    },
                    success: function (data) {
                        if (!data.national_id) {
                            $('#modalRevisitReservation2 input').removeAttr('disabled');
                            $('#modalRevisitReservation2 textarea').removeAttr('disabled');
                            $("#patient_id3").val('');
                            clearPatientDate3();
                            return;
                        }
                        $("#phone4").val(data.phone);
                        $("#national_id3").val(data.national_id);
                        $("#id3").val(data.registration_no);
                        $("#patient_id3").val(data.id);
                        $("#first_name3").val(data.first_name);
                        $("#middle_name3").val(data.middle_name);
                        $("#last_name3").val(data.last_name);
                        $("#family_name3").val(data.family_name);
                        $("#birthday3").val(data.birthday);
                        $("#preferred_contact3").val(data.preferred_contact).select2();
                        $("#email3").val(data.email);
                        if (data.gender == 2) {
                            $("#female3").prop("checked", false);
                            $("#male3").prop("checked", true);
                        } else if (data.gender == 1) {
                            $("#female3").prop("checked", true);
                            $("#male3").prop("checked", false);
                        }
                        $("#address3").val(data.address);
                        $('#modalRevisitReservation2 input').removeAttr('disabled');
                        $('#modalRevisitReservation2 textarea').removeAttr('disabled');
                        $('#id3').attr('disabled', 'disabled');
                    }
                });
            }

            $("#clearData3").click(function () {
                clearAllPatientDate3();
            });

            function clearAllPatientDate3() {
                $("#phone4, #address3, #patient_id3, #national_id3, #id3, #name3, #first_name3, #last_name3, #middle_name3, #family_name3, #birthday3, #email3, #phone23, #caller_id3, #caller_name3").val('');
                $("#current_patient3").html('<option value="">Choose</option>').select2();
                $("#relevant_id3").val('').select2();
                $("#female3, #male3").removeAttr('checked');
                $('#modalRevisitReservation2 input').removeAttr('disabled');
                $('#modalRevisitReservation2 textarea').removeAttr('disabled');
            }

            function clearPatientDate3() {
                $("#address3, #patient_id3, #id3, #name3, #first_name3, #last_name3, #middle_name3, #family_name3, #birthday3, #email3, #phone23").val('');
                $("#current_patient3").html('<option value="">Choose</option>').select2();
                $("#relevant_id3").val('').select2();
                $("#female3, #male3").removeAttr('checked');
                $("#id3").removeAttr('disabled');
            }

            $(document).on('blur', '#revisit_date2', function (e) {
                $("#revisit_time2").attr('disabled', 'disabled');
                if ($("#selectPhysician4").val().length == 0) {
                    alert('Please, select one physician!');
                    return;
                }
                setTimeout(function () {
                    $.ajax({
                        url: '{{route('getAvailableRevisitTime')}}',
                        method: 'POST',
                        data: {
                            date: $("#revisit_date2").val(),
                            physician_id: $("#selectPhysician4").val(),
                            clinic_id: $("#selectClinic4").val(),
                            stand_alon: true
                        },
                        success: function (data) {
                            $("#revisit_time2").html(data).removeAttr('disabled');
                        }
                    });
                }, 500);
            });

            @if(Input::get('hospital_id'))
              $.ajax({
                url: '{{route('getClinicsByHospitalId')}}',
                method: 'POST',
                data: {
                    hospital_id: $("#selectHospital2").val()
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#selectClinic2").removeAttr('disabled').html(data).select2();
                    @if(Input::get('clinic_id'))
$("#selectClinic2").val('{{Input::get('clinic_id')}}').select2();
                    $.ajax({
                        url: '{{route('getPhysicianByClinicId')}}',
                        method: 'POST',
                        data: {
                            clinic_id: $('#selectClinic2').val()
                        },
                        headers: {token: '{{csrf_token()}}'},
                        success: function (data) {
                            $("#selectPhysician2").removeAttr('disabled').html(data).select2();
                            @if(Input::get('physician_id'))
$("#selectPhysician2").val('{{Input::get('physician_id')}}').select2();
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
            Stand Alone Revisits
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
                    {{Form::open(array('method' => 'GET'))}}
                    <div class="box-body">
                        <div class="form-group col-md-3">
                            <label>Hospital</label>
                            <br>
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
                            <br>
                            <select autocomplete="off" id="selectClinic2" name="clinic_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Physician Name</label>
                            <br>
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
                        <div class="form-group col-md-3" style="margin-bottom: 5px">
                            <label>Date From</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{Input::get('date_from')}}"
                                   name="date_from" class="form-control datepic_search">
                        </div>
                        <div class="form-group col-md-3" style="margin-bottom: 5px">
                            <label>Date To</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{Input::get('date_to')}}"
                                   name="date_to" class="form-control datepic_search">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Reservation Status</label>
                            <br>
                            <select autocomplete="off" name="status"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                <option @if(Input::get('status') == 1) selected @endif value="1">Reserved
                                </option>
                                <option @if(Input::get('status') == 2) selected @endif value="2">On Progress
                                </option>
                                <option @if(Input::get('status') == 3) selected @endif value="3">Cancel</option>
                                <option @if(Input::get('status') == 4) selected @endif value="4">Accomplished
                                </option>
                                <option @if(Input::get('status') == 5) selected @endif value="5">No Show
                                </option>
                                <option @if(Input::get('status') == 8) selected @endif value="8">Archive
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Patient Status</label>
                            <br>
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
                                <option @if(Input::get('patient_status') == 2) selected @endif value="2">Patient
                                    Out
                                </option>
                                <option @if(Input::get('patient_status') == 4) selected @endif value="4">Cancel
                                </option>
                                <option @if(Input::get('patient_status') == 5) selected @endif value="5">Pending
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Created By</label>
                            <select autocomplete="off" name="created_by"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($groups as $val)
                                    <option value="{{$val['name']}}" @if(Input::get('created_by') == $val['name'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{route('standAloneRevisit')}}" class="btn btn-info">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        Stand Alone Revisits
                        <div class="pull-right">
                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('standAlonReservation.add'))
                                <a data-target="#modalRevisitReservation2" data-toggle="modal"
                                   class="btn btn-default">Add Stand Alone Revisit</a>
                            @endif
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="">
                            <table class="table table-bordered" id="example3">
                                <thead>
                                <tr>
                                    <th>Options</th>
                                    <th>Clinic Name</th>
                                    <th>Physician Name</th>
                                    <th>Patient Name</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>P National ID</th>
                                    <th>Patient Phone</th>
                                    <th>Patient ID</th>
                                    <th>Reservation Code</th>
                                    <th>Parent Code</th>
                                    <th>Reservation Status</th>
                                    <th>Patient Status</th>
                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('supervisor.access'))
                                        <th>Create By</th>
                                        <th>Create At</th>
                                        <th>Update By</th>
                                        <th>Update At</th>
                                    @endif
                                    <th>Notes</th>
                                    <th>Exception Reason</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($reservations as $key => $val)
                                    @if($val['type'] != 3)
                                        <?php continue; ?>
                                    @endif
                                    <?php
                                    $patient = Patient::getById($val['patient_id']);
                                    ?>
                                    <tr style="
                                    @if($val['patient_attend'] == 1)
                                            background:#84e184;
                                    @endif
                                    @if($val['patient_status'] == \core\enums\PatientStatus::patient_in)
                                            background:#32cd32;
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
                                                @if($val['date'] < date('Y-m-d'))
                                                    @if($val['patient_status'] == \core\enums\PatientStatus::no_show
                                                    && ($c_user->hasAccess('standAlonReservation.edit') || $c_user->user_type_id == 1))
                                                        <a reservation_id="{{$val['id']}}"
                                                           title="Edit"
                                                           class="btn btn-default editRevisitReserveBtn"><i
                                                                    class="fa fa-pencil"></i></a>
                                                    @endif
                                                @else
                                                    @if($val['patient_status'] == \core\enums\PatientStatus::waiting
                                                    || $val['patient_status'] == \core\enums\PatientStatus::pending
                                                    || $val['patient_status'] == \core\enums\PatientStatus::archive
                                                    && ($c_user->hasAccess('standAlonReservation.edit') || $c_user->user_type_id == 1))
                                                        <a reservation_id="{{$val['id']}}"
                                                           title="Edit"
                                                           class="btn btn-default editRevisitReserveBtn"><i
                                                                    class="fa fa-pencil"></i></a>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{Clinic::getNameById($val['clinic_id'])}}</td>
                                        <td>
                                            <div>
                                                {{ucwords(strtolower(User::getName($val['physician_id'])))}}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                {{ucwords(strtolower($patient['name']))}}
                                            </div>
                                        </td>
                                        <td>{{$val['date']}}</td>
                                        <td>
                                            <?php
                                            $seconds = Functions::hoursToSeconds($val['revisit_time_from']);
                                            $newSeconds = $seconds + (10 * 60);
                                            $futureTime = Functions::timeFromSeconds($newSeconds);
                                            ?>
                                            {{$futureTime}}
                                        </td>
                                        <td>{{$patient['national_id']}}</td>
                                        <td>{{$patient['phone']}}</td>
                                        <td>{{$patient['registration_no']}}</td>
                                        <td>{{$val['code']}}</td>
                                        @if($val['parent_id_of_revisit'])
                                            <td class="getParentReservation" reservation_id="{{$val['id']}}"
                                                style="text-decoration:underline;color:blue;cursor: pointer;">
                                                {{Reservation::getById($val['parent_id_of_revisit'])['code']}}
                                            </td>
                                        @else
                                            <td>
                                                <span style="color:green;">Stand Alone</span>
                                            </td>
                                        @endif
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
                                                Patient In
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
                                        @if($c_user->user_type_id == 1 || $c_user->hasAccess('supervisor.access'))
                                            <td>
                                                <div>
                                                    <?php
                                                    if ($val['create_by']) {
                                                        $oneUser = Sentry::findUserById($val['create_by']);
                                                        $groups = $oneUser->getGroups();
                                                        $count = count($groups);
                                                        $create_by = User::getName($val['create_by']) . ' (';
                                                        foreach ($groups as $index2 => $val2) {
                                                            if ($count == $index2 + 1) {
                                                                $create_by .= $val2['name'] . ')';
                                                            } else {
                                                                $create_by .= $val2['name'] . ', ';
                                                            }
                                                        }
                                                    } else {
                                                        $create_by = User::getName($val['create_by']);
                                                    }
                                                    ?>
                                                    {{$create_by}}
                                                </div>
                                            </td>
                                            <td>{{$val['created_at']}}</td>
                                            <td>
                                                <div>
                                                    <?php
                                                    if ($val['update_by']) {
                                                        $oneUser = Sentry::findUserById($val['update_by']);
                                                        $groups = $oneUser->getGroups();
                                                        $count = count($groups);
                                                        $update_by = User::getName($val['update_by']) . ' (';
                                                        foreach ($groups as $index2 => $val2) {
                                                            if ($count == $index2 + 1) {
                                                                $update_by .= $val2['name'] . ')';
                                                            } else {
                                                                $update_by .= $val2['name'] . ', ';
                                                            }
                                                        }
                                                    } else {
                                                        $update_by = User::getName($val['update_by']);
                                                    }
                                                    ?>
                                                    {{$update_by}}
                                                </div>
                                            </td>
                                            <td>{{$val['updated_at']}}</td>
                                        @endif
                                        <td>{{$val['notes']}}</td>
                                        <td>
                                            @if($val['patient_status'] == \core\enums\PatientStatus::pending
                                        || $val['patient_status'] == \core\enums\PatientStatus::no_show
                                        || $val['patient_status'] == \core\enums\PatientStatus::cancel
                                        || $val['patient_status'] == \core\enums\PatientStatus::archive)
                                                {{$val['exception_reason']}}
                                            @endif
                                        </td>
                                    </tr>
                                    <?php unset($reservations[$key]); ?>
                                @endforeach
                                </tbody>
                            </table>
                            {{$reservations->appends(Input::except('_token'))->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

    <div class="modal fade" id="modalRevisitReservation2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        Add Stand Alone Revisit
                        <a style="margin-left: 20%" id="clearData3" class="btn btn-default">Clear data</a>
                    </h4>
                </div>
                {{Form::open(array('role'=>"form", 'route' => 'standAlonRevisitReservation'))}}
                <div class="modal-body col-md-12">
                    <div class="form-group col-md-6">
                        <label>Hospital *</label>
                        <br>
                        <select required autocomplete="off" id="selectHospital4"
                                class="form-control select2" name="hospital_id">
                            <option value="">Choose</option>
                            @foreach($hospitals as $val)
                                <option value="{{$val['id']}}">{{$val['name']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>National Id *</label>
                        <input required autocomplete="off" id="national_id3" type="text"
                               name="national_id" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Phone *</label>
                        <input required autocomplete="off" id="phone4" type="text" maxlength="15"
                               name="phone" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Patient Id</label>
                        <input autocomplete="off" id="id3" type="text"
                               name="id" class="form-control">
                    </div>

                    <input autocomplete="off" type="hidden" value="0" name="patient_id"
                           id="patient_id3">

                    <div class="form-group col-md-6">
                        <label>First Name *</label>
                        <input autocomplete="off" id="first_name3" type="text"
                               name="first_name" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Middle Name</label>
                        <input autocomplete="off" id="middle_name3" type="text"
                               name="middle_name" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Last Name *</label>
                        <input autocomplete="off" id="last_name3" type="text"
                               name="last_name" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Family Name</label>
                        <input autocomplete="off" id="family_name3" type="text"
                               name="family_name" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Updated Phone</label>
                        <input autocomplete="off" id="phone23" type="text" maxlength="15"
                               name="phone2" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Birthday *</label>
                        <input required autocomplete="off" id="birthday3" type="text"
                               data-date-format="yyyy-mm-dd" name="birthday"
                               class="form-control datepicker2">
                    </div>

                    <div class="form-group col-md-6" style="margin-bottom: 30px;">
                        <label>Gender *</label>

                        <div class="checkbox-list">
                            <label class="checkbox-inline">
                                <input required autocomplete="off" id="male3" type="radio"
                                       value="2" name="gender" checked class="checkbox-inline checkbox1"> Male
                            </label>
                            <label class="checkbox-inline">
                                <input required autocomplete="off" id="female3" type="radio"
                                       value="1" name="gender" class="checkbox-inline checkbox1"> Female
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Preferred Contact *</label>
                        <br>
                        <select autocomplete="off" id="preferred_contact3"
                                name="preferred_contact" class="form-control select2">
                            <option value="1">Phone</option>
                            <option value="2">Email</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input autocomplete="off" id="email3" name="email" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>SMS Language: </label>
                        <br>
                        <select id="sms_lang3" name="sms_lang" class="select2 form-control" style="width: 100%;">
                            <option selected value="1">Arabic</option>
                            <option value="2">English</option>
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Address</label>
                        <input autocomplete="off" id="address3" type="text" name="address"
                               class="form-control">
                    </div>
                    <hr>
                    <div class="form-group col-md-6">
                        <label>Clinics *</label>
                        <br>
                        <select required autocomplete="off" id="selectClinic4" name="clinic_id"
                                class="form-control select2">
                            <option value="">Choose</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Physicians *</label>
                        <br>
                        <select required autocomplete="off" id="selectPhysician4" name="physician_id"
                                class="form-control select2">
                            <option value="">Choose</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Date Of Revisit *</label>

                        <div id="revisit_date_div2">
                            <input required autocomplete="off" id="revisit_date2" type="text"
                                   data-date-format="yyyy-mm-dd"
                                   name="date" class="form-control datepicker">
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Time *</label>
                        <select required id="revisit_time2" name="time" class="form-control">
                            <option value="">Choose</option>
                        </select>
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
