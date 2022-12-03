@extends('layout/main')

@section('title')
    - Add Reservation
@stop

@section('header')
    <meta charset='utf-8'/>
    {{--    <link href='{{asset('plugins/jQueryUI/jquery-ui.css')}}' rel='stylesheet'/>--}}
    <link href='{{asset('plugins/fullcalendar-scheduler/lib/fullcalendar.min.css')}}' rel='stylesheet'/>
    <link href='{{asset('plugins/fullcalendar-scheduler/lib/fullcalendar.print.css')}}' rel='stylesheet' media='print'/>
    <link href='{{asset('plugins/fullcalendar-scheduler/scheduler.min.css')}}' rel='stylesheet'/>
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/loading_mask/waitMe.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/autocomplete/jquery.autocomplete.css')}}">
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
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('plugins/loading_mask/waitMe.js')}}"></script>
    <script src="{{asset('plugins/autocomplete/jquery.autocomplete.js')}}"></script>
    {{--    <script src='{{asset('plugins/jQueryUI/jquery-ui.js')}}'></script>--}}
    <script>
        $(function () {
            $('#selectHospital option[value="{{$hospitalId}}"]').prop('selected', true);
            $("#selectHospital").select2();
            @if($hospitalId)
            $.ajax({
                url: '{{route('getClinicsByHospitalId')}}',
                method: 'POST',
                data: {
                    hospital_id: $("#selectHospital").val()
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#selectClinic").removeAttr('disabled').html(data);
                    $('#selectClinic option[value="{{$clinicId}}"]').prop('selected', true);
                    $("#selectClinic").select2();
                    @if($clinicId)
                    $.ajax({
                        url: '{{route('getPhysicianByClinicId')}}',
                        method: 'POST',
                        data: {
                            clinic_id: $('#selectClinic').val(),
                            bookable: true
                        },
                        headers: {token: '{{csrf_token()}}'},
                        success: function (data) {
                            $("#selectPhysician").html(data);
                            $('#selectPhysician option[value="{{$physician_id}}"]').prop('selected', true);
                            $("#selectPhysician").select2();
                        }
                    });
                    @endif
                }
            });
            @endif
            $('.datepicker').datepicker({
                @if(date('H:i:s') > '23:59:59')
                startDate: "-1d",
                @else
                startDate: "1d",
                @endif
                todayHighlight: true,
                autoclose: true
            });
            $('.datepicker2').datepicker({
                todayHighlight: true,
                autoclose: true
            });
            $('#calendar').fullCalendar({
                aspectRatio: 1.8,
                scrollTime: '00:00', // undo default 6am scrollTime
//                selectable: true,
                defaultDate: '{{$selectDate}}',
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
                        title: '{{addslashes($val['first_name'].' '. $val['middle_name'])}}',
                        eventColor: 'green',
                        name: '{{addslashes($val['user_name'])}}'
                    },
                    @endforeach
                ],

                events: { // you can also specify a plain string like 'json/events.json'
                    url: '{{route('reservationGetEvents')}}?clinic_id={{$clinicId}}',
                    error: function () {
                        $('#script-warning').show();
                    }
                }
            });

            $("#selectHospital").change(function (e) {
                $("#id").autocomplete("destroy");
                idautocomplete('?hospital_id=' + $(this).val());
                idBlur();

                $("#phone").autocomplete("destroy");
                phoneautocomplete('?hospital_id=' + $(this).val());
                phoneKeyUp();

                nationalidautocomplete('?hospital_id=' + $(this).val());
                nationalidKeyUp();
            });

            function idautocomplete(param) {
                $("#id").autocomplete({
                    url: '{{route('autoCompletePatient2')}}' + param,
                    minChars: 1,
                    useCache: false,
                    filterResults: false,
                    mustMatch: true,
                    maxItemsToShow: 10,
                    remoteDataType: 'json',
                    onItemSelect: function (item) {
                        $("#id").val(item.data[0]);
                    }
                });
            }

            idautocomplete('?hospital_id=' + $("#selectHospital").val());

            function idBlur() {
                $("#id").blur(function (e) {
                    if (!$("#id").val()) {
                        return;
                    }
                    setTimeout(function () {
                        getPatientData($("#id"));
                    }, 500);
                });
            }

            idBlur();

            function phoneautocomplete(param) {
                $("#phone").autocomplete({
                    url: '{{route('autoCompletePatientByPhone')}}' + param,
                    minChars: 2,
                    useCache: false,
                    filterResults: false,
                    mustMatch: false,
                    maxItemsToShow: 10,
                    remoteDataType: 'json',
                    onItemSelect: function (item) {
                        if (item.data[0]) {
                            $("#phone").val(item.data[0]);
                            $("#patient_id").val(item.data[1]);
                            setTimeout(function () {
                                getPatientData($("#patient_id"));
                            }, 500);
                        }
                    }
                });
            }

            phoneautocomplete('?hospital_id=' + $("#selectHospital").val());

            function phoneKeyUp() {
                $("#phone").keyup(function (e) {
                    clearPatientDate();
                });
            }

            phoneKeyUp();

            function nationalidautocomplete(param) {
                $("#national_id").autocomplete("destroy");
                $("#national_id").autocomplete({
                    url: '{{route('autoCompletePatientByNationalId')}}' + param,
                    minChars: 2,
                    useCache: false,
                    filterResults: false,
                    mustMatch: false,
                    maxItemsToShow: 10,
                    remoteDataType: 'json',
                    onItemSelect: function (item) {
                        if (item.data[0]) {
                            $("#national_id").val(item.data[0]);
                            $("#patient_id").val(item.data[1]);
                            setTimeout(function () {
                                getPatientData($("#patient_id"));
                            }, 500);
                        }
                    }
                });
            }

            nationalidautocomplete('?hospital_id=' + $("#selectHospital").val());

            function nationalidKeyUp() {
                $("#national_id").keyup(function (e) {
                    clearPatientDate();
                });
            }

            nationalidKeyUp();

            function getPatientData(opj) {
                var id = $(opj).attr('id');
                if (!$(opj).val()) {
                    return;
                }
                var input = $(opj);
                this_id = id == 'patient_id';
                this_national_id = id == 'national_id';
                $('#tab_1 input').attr('disabled', 'disabled');
                $('#tab_1 textarea').attr('disabled', 'disabled');
                $.ajax({
                    url: "{{route('checkPatientExist')}}",
                    method: 'POST',
                    data: {
                        search: $(opj).val(),
                        this_id: this_id,
                        this_national_id: this_national_id,
                        hospital_id: '{{$hospitalId}}'
                    },
                    success: function (data) {
                        if (!data.national_id) {
                            $('#tab_1 input').removeAttr('disabled');
                            $('#tab_1 textarea').removeAttr('disabled');
                            $("#patient_id").val('');
                            clearPatientDate();
                            return;
                        }
                        $("#phone").val(data.phone);
                        $("#national_id").val(data.national_id);
                        $("#id").val(data.registration_no);
                        $("#patient_id").val(data.id);
                        $("#relevant_id").val(data.relevant_type_id).select2();
//                        $("#name").val(data.name);
                        $("#first_name").val(data.first_name);
                        $("#middle_name").val(data.middle_name);
                        $("#last_name").val(data.last_name);
                        $("#family_name").val(data.family_name);
                        $("#birthday").val(data.birthday);
                        $("#preferred_contact").val(data.preferred_contact).select2();
                        $("#email").val(data.email);
                        if (data.gender == 2) {
                            $("#female").prop("checked", false);
                            $("#male").prop("checked", true);
                        } else if (data.gender == 1) {
                            $("#female").prop("checked", true);
                            $("#male").prop("checked", false);
                        }
                        $("#address").val(data.address);
                        $('#tab_1 input').removeAttr('disabled');
                        $('#tab_1 textarea').removeAttr('disabled');
                        $('#id').attr('disabled', 'disabled');
                    }
                });
            }

            $("#clearData").click(function () {
                clearAllPatientDate();
            });

            function clearAllPatientDate() {
                $("#phone, #address, #patient_id, #national_id, #id, #name, #first_name, #last_name, #middle_name, #family_name, #birthday, #email, #phone2, #caller_id, #caller_name").val('');
                $("#current_patient").html('<option value="">Choose</option>').select2();
                $("#relevant_id").val('').select2();
                $("#female, #male").removeAttr('checked');
                $('#tab_1 input').removeAttr('disabled');
                $('#tab_1 textarea').removeAttr('disabled');
            }

            function clearPatientDate() {
                $("#address, #patient_id, #id, #name, #first_name, #last_name, #middle_name, #family_name, #birthday, #email, #phone2").val('');
                $("#current_patient").html('<option value="">Choose</option>').select2();
                $("#relevant_id").val('').select2();
                $("#female, #male").removeAttr('checked');
                $("#id").removeAttr('disabled');
            }

            $(document).on('click', '.deleteReserveBtn', function (e) {
                if (confirm('Are You Sure?')) {
                    $("#modalCancelReservation").modal('show');
                    $("#modal_cancel_reservation_id").val($(this).attr('ref_id'));
                }
            });

            $(document).on('click', '.showReserveBtn', function (e) {
                var ref_id = $(this).attr('ref_id');
                $.ajax({
                    url: "{{route('showReserveBtn')}}",
                    method: 'POST',
                    data: {
                        reservation_id: ref_id
                    },
                    success: function (data) {
                        $("#info_physician_name").html($("#selectPhysician option:selected").html());
                        $("#info_date").html('{{$selectDate}}');
                        $("#info_reservation_id").html(data.reservation_code);
                        $("#info_patient_id").html(data.registration_no);
                        $("#patient_name").html(data.name);
                        $("#patient_phone").html(data.phone);
                        $("#patient_email").html(data.email);
                        $("#patient_birthday").html(data.birthday);
                        $("#patient_address").html(data.address);
                        if (data.gender == 2) {
                            $("#patient_gender").html('Male');
                        } else if (data.gender == 1) {
                            $("#patient_gender").html('Female');
                        } else {
                            $("#patient_gender").html('');
                        }
                        $("#modalShowPatientInfo").modal('show');
                    }
                });
            });

            function patientReservationForm() {
                var o = getFormData($("#patientReservationForm").serializeArray());
                $.ajax({
                    url: $("#patientReservationForm").attr('action'),
                    method: 'POST',
                    data: {
                        data: o,
                        date: '{{$selectDate}}'
                    },
                    success: function (data) {
                        $("#patientReservationBody").html(data);
                        $('#tab_2_search').waitMe('hide');
                    }
                });
            }

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
                        clinic_id: '{{$clinicId}}',
                        date: '{{$selectDate}}',
                        schedule_id: '{{$clinicSchedule['id']}}',
                        physician_id: '{{$physician_id}}'
                    },
                    success: function (data) {
                        $("#physicianTimeHtml").html(data.physicianTimeHtml);
                        patientReservationForm();
                        $("#modalCancelReservation").modal('hide');
                    }
                });
            });

            $(document).on('click', '.unArchiveReserveBtn', function (e) {
                $.ajax({
                    url: "{{route('unArchiveReservation')}}",
                    method: 'POST',
                    data: {
                        reservation_id: $(this).attr('reservation_id'),
                        date: '{{$selectDate}}',
                        schedule_id: '{{$clinicSchedule['id']}}',
                        physician_id: '{{$physician_id}}'
                    },
                    success: function (data) {
                        refreshPhysicianTime();
//                        patientReservationForm();
                    }
                });
            });

            $(document).on('click', '.noteReserveBtn', function (e) {
                var ref_id = $(this).attr('ref_id');
                $.ajax({
                    url: "{{route('editReservation')}}",
                    method: 'POST',
                    data: {
                        reservation_id: ref_id
                    },
                    success: function (data) {
                        $("#reservation_note").val(data.reservation['notes']);
                        $("#modal_note_reservation_id").val(ref_id);
                        $("#modalNoteReservation").modal('show');
                    }
                });
            });

            $("#modalNoteReservationBtn").click(function (e) {
                $.ajax({
                    url: "{{route('addNoteReservation')}}",
                    method: 'POST',
                    data: {
                        reservation_id: $("#modal_note_reservation_id").val(),
                        notes: $("#reservation_note").val()
                    },
                    success: function (data) {
                        $("#modalNoteReservation").modal('hide');
                        alert('Added Successfully');
                    }
                });
            });

            $("#updatePatient").click(function (e) {
                var o = getFormData($("#patientForm").serializeArray());
                if (confirm('Are You Sure?')) {
                    $.ajax({
                        url: "{{route('updatePatientData')}}",
                        method: 'POST',
                        data: {
                            patientData: o,
                            hospital_id: '{{$hospitalId}}'
                        },
                        success: function (data) {
                            if (data.response == 'no') {
                                alert('Updated failed! update process only on exist patient.');
                            } else {
                                alert('Updated successfully.');
                            }
                        }
                    });
                }
            });

            $(document).on('click', '.reserveBtn', function (e) {
                var o = getFormData($("#patientForm").serializeArray());
                if (o['national_id'] == "" || o['national_id'] == " ") {
                    alert('Patient National ID is required');
                    $("#national_id").focus();
                    return;
                }
                if (o['phone'] == "" || o['phone'] == " ") {
                    alert('Patient phone is required');
                    $("#phone").focus();
                    return;
                }
                if (Math.floor(o['phone']) != o['phone']) {
                    alert('Patient phone must be numeric number');
                    return;
                }
                if (o['caller_name'] == "" || o['caller_name'] == " ") {
                    alert('Caller name is required');
                    $("#caller_name").focus();
                    return;
                }
                if (o['first_name'] == "" || o['first_name'] == " ") {
                    alert('Patient first name is required');
                    $("#first_name").focus();
                    return;
                }
                if (o['last_name'] == "" || o['last_name'] == " ") {
                    alert('Patient last name is required');
                    $("#last_name").focus();
                    return;
                }
                if (o['birthday'] == "" || o['birthday'] == " " || o['birthday'] == "0000-00-00") {
                    alert('Patient birthday is required');
                    $("#last_name").focus();
                    return;
                }
                if (typeof o['gender'] == 'undefined') {
                    alert('Patient gender is required');
                    return;
                }
                if ($("#preferred_contact").val() == 2) {
                    if (o['email'] == "" || o['email'] == " ") {
                        alert('Patient email is required');
                        $("#email").focus();
                        return;
                    }
                }
                var now = new Date();
                var h = now.getHours() > 9 ? now.getHours() : '0' + now.getHours();
                var m = now.getMinutes() > 9 ? now.getMinutes() : '0' + now.getMinutes();
                var s = now.getSeconds() > 9 ? now.getSeconds() : '0' + now.getSeconds();
                var timeNow = h + ':' + m + ':' + s;
                console.log(timeNow);
                {{--if('{{$selectDate}}' == '{{date('Y-m-d')}}') {--}}
                {{--if(timeNow > $(this).attr('time')){--}}
                {{--alert('This slot has been expired');--}}
                {{--return;--}}
                {{--}--}}
                {{--}--}}
                $("#physician_name").html($("#selectPhysician option:selected").html());
                var time = $(this).attr('time').split(':');
                var timeObj = new Date(null, null, null, time[0], time[1], time[2]);
                $("#from_time").html(timeObj.getHours() + ':' + timeObj.getMinutes() + ':' + timeObj.getSeconds());
                $("#from_time_origin").val($(this).attr('time'));

                var to_time = $(this).attr('to_time').split(':');
                var to_timeObj = new Date(null, null, null, to_time[0], to_time[1], to_time[2]);
                $("#to_time").html(to_timeObj.getHours() + ':' + to_timeObj.getMinutes() + ':' + to_timeObj.getSeconds());
                $("#to_time_origin").val($(this).attr('to_time'));
                $("#modalAddReservation").modal('show');
            });

            $("#modalAddReservationBtn").click(function (e) {
                var o = getFormData($("#patientForm").serializeArray());
                $('#WithMe').waitMe({
                    effect: 'ios',
                    text: 'Please wait...',
                    bg: 'rgba(255,255,255,0.7)',
                    color: '#000',
                    maxSize: '',
                    source: 'img.svg'
                });
                $("#modalAddReservation").modal('hide');
                $.ajax({
                    url: "{{route('createReservation')}}",
                    method: 'POST',
                    data: {
                        patientData: o,
                        time_from: $("#from_time_origin").val(),
                        physician_id: '{{$physician_id}}',
                        clinic_id: '{{$clinicId}}',
                        hospital_id: '{{$hospitalId}}',
                        date: '{{$selectDate}}',
                        schedule_id: '{{$clinicSchedule['id']}}',
                        sms_lang: $("#sms_lang").val()
                    },
                    success: function (data) {
                        $('#WithMe').waitMe('hide');
                        if (data.physicianTimeHtml == 'No') {
                            alert('This Slot Already Taken By Another Patient, Plz Refresh The Slots.')
                        } else if (data.physicianTimeHtml == 'Error') {
                            alert(data.message);
                        } else {
                            $("#physicianTimeHtml").html(data.physicianTimeHtml);
                            clearAllPatientDate();
                        }
                    }
                });
            });

            $("#tab-li_0").click(function (e) {
                $('#calendar').fullCalendar('refetchEvents');
            });

            $("#patientReservationForm").submit(function (e) {
                e.preventDefault();
                $('#tab_2_search').waitMe({
                    effect: 'ios',
                    text: 'Please wait...',
                    bg: 'rgba(255,255,255,0.7)',
                    color: '#000',
                    maxSize: '',
                    source: 'img.svg'
                });
                patientReservationForm();
            });

            function refreshPhysicianTime() {
                $('#WithMe').waitMe({
                    effect: 'ios',
                    text: 'Please wait...',
                    bg: 'rgba(255,255,255,0.7)',
                    color: '#000',
                    maxSize: '',
                    source: 'img.svg'
                });
                $.ajax({
                    url: '{{route('loadPhysicianTime')}}',
                    method: 'POST',
                    data: {
                        clinic_id: '{{$clinicId}}',
                        date: '{{$selectDate}}',
                        schedule_id: '{{$clinicSchedule['id']}}',
                        physician_id: '{{$physician_id}}'
                    },
                    success: function (data) {
                        $("#physicianTimeHtml").html(data.physicianTimeHtml);
                        $('#WithMe').waitMe('hide');
                    }
                });
            }

            $(document).on('click', '#refreshPhysicianTime', function (e) {
                refreshPhysicianTime();
            });

            function getPatientReservation(page) {
                var o = getFormData($("#patientReservationForm").serializeArray());
                $.ajax({
                    url: $("#patientReservationForm").attr('action') + '?page=' + page,
                    method: 'POST',
                    data: {
                        data: o,
                        clinic_id: '{{$clinicId}}',
                        date: '{{$selectDate}}',
                        schedule_id: '{{$clinicSchedule['id']}}',
                        physician_id: '{{$physician_id}}'
                    }
                }).done(function (data) {
                    $('#patientReservationBody').html(data);
                }).fail(function () {
                    alert('Comments could not be loaded.');
                });
            }

            $(document).on('click', '.pagination a', function (e) {
                getPatientReservation($(this).attr('href').split('page=')[1]);
                e.preventDefault();
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
                            $("#modal_date").val('');
                            $("#modal_reservation_id").val(data.reservation['id']);
                            $("#modal_time").html('');

                            $("#selectHospital4").val(data.hospital_id).select2();
                            var clinic_id = data.reservation['clinic_id'];
                            var physician_id = data.reservation['physician_id'];
                            $.ajax({
                                url: '{{route('getClinicsByHospitalId')}}',
                                method: 'POST',
                                data: {
                                    hospital_id: $("#selectHospital").val()
                                },
                                headers: {token: '{{csrf_token()}}'},
                                success: function (data) {
                                    $("#selectClinic4").html(data).val(clinic_id).attr('disabled', 'disabled').select2();
                                }
                            });
                            $.ajax({
                                url: '{{route('getPhysicianByClinicId')}}',
                                method: 'POST',
                                data: {
                                    clinic_id: $("#selectClinic").val()
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
                    console.log(modal_date);
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
                            $("#modal_time").html(data.modal_time_html).removeAttr('disabled');
                        }
                    });
                }, 500);
            }

            $("#modal_date").blur(function (e) {
                getAvailablePhysicianTime();
            });

            $("#selectPhysician4").change(function (e) {
                getAvailablePhysicianTime();
            });

            $("#updateReservation").submit(function (e) {
                e.preventDefault();
                var formData = getFormData($(this).serializeArray());
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: {
                        data: formData
                    },
                    success: function (data) {
                        patientReservationForm();
                        refreshPhysicianTime();
                        $("#modalEditReservation").modal('hide');
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

            $("#selectClinic4").change(function (e) {
                $("#selectPhysician4").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getPhysicianByClinicId')}}',
                    method: 'POST',
                    data: {
                        clinic_id: $(this).val(),
                        bookable: true
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectPhysician4").removeAttr('disabled').html(data).select2();
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

            $("#selectHospital2").change(function (e) {
                $("#selectClinic2").attr('disabled', 'disabled');
                $("#physicianScheduleDiv").html('');
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
                $("#clinic_schedule_id").attr('disabled', true);
                $("#selectPhysician2").attr('disabled', true);
                $("#physicianScheduleDiv").html('');
                $.ajax({
                    url: '{{route('getPhysicianByClinic')}}',
                    method: 'POST',
                    data: {
                        clinic_id: $(this).val()
                    },
                    success: function (data) {
                        $("#clinic_schedule_id").html(data.schedulesHtml).attr('disabled', false).select2();
                        $("#selectPhysician2").html(data.physiciansHtml).attr('disabled', false).select2();
                    }
                });
            });

            $("#selectPhysician2").change(function () {
                var clinic_schedule_id = $("#clinic_schedule_id").val();
                if (clinic_schedule_id.length == 0) {
                    alert('Please Choose One Clinic Schedule');
                    $(this).val('').select2();
                    return;
                }
                $("#physicianScheduleDiv").html('');
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

            $("#physician_schedule_id").change(function () {
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

            $("#physicianProfileBtn").click(function (e) {
                e.preventDefault();
                var physician_id = $("#selectPhysician").val();
                if (physician_id.length > 0) {
                    $.ajax({
                        url: '{{route('getPhysicianProfile')}}',
                        method: 'POST',
                        data: {
                            physician_id: physician_id
                        },
                        success: function (data) {
                            $("#profile_name").html(data.physician['full_name']);
                            var assets = '{{asset('')}}';
                            $("#profile_image").html('<img width="100" height="100" src="' + assets + '/' + data.physician['profile']['image_url'] + '">');
                            $("#profile_extension_num").html(data.physician['extension_num']);
                            $("#profile_age").html(data.physician['profile']['age']);
                            if (data.physician['profile']['gender'] == 1) {
                                $("#profile_gender").html('Female');
                            } else {
                                $("#profile_gender").html('Male');
                            }
                            $("#profile_certificates").html(data.physician['profile']['certificates']);
                            $("#profile_awards").html(data.physician['profile']['awards']);
                            $("#profile_equipments").html(data.physician['profile']['equipments']);
                            $("#profile_clinic_services").html(data.physician['profile']['clinic_services']);
                            $("#profile_performed_operations").html(data.physician['profile']['performed_operations']);
                            $("#profile_notes").html(data.physician['profile']['notes']);
                            $('#ModalPhysicianProfile').modal('show');
                        }
                    });
                } else {
                    alert('Select One Physician!');
                }
            });

            @if($hospitalId)
            $.ajax({
                url: '{{route('getClinicsByHospitalId')}}',
                method: 'POST',
                data: {
                    hospital_id: '{{$hospitalId}}'
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#selectHospital2").val('{{$hospitalId}}').select2();
                    $("#selectClinic2").html(data).val('{{$clinicId}}').select2();
                    $.ajax({
                        url: '{{route('getPhysicianByClinic')}}',
                        method: 'POST',
                        data: {
                            clinic_id: '{{$clinicId}}'
                        },
                        success: function (data) {
                            $("#clinic_schedule_id").html(data.schedulesHtml).val('{{$clinicSchedule['id']}}').select2();
                            $("#selectPhysician2").html(data.physiciansHtml).val('{{$physician_id}}').select2();
                            $.ajax({
                                url: '{{route('getPhysicianScheduleByPhysicianId')}}',
                                method: 'POST',
                                data: {
                                    physician_id: '{{$physician_id}}'
                                },
                                success: function (data) {
                                    $("#physician_schedule_id").html(data).attr('disabled', false).select2();
                                }
                            });
                        }
                    });
                }
            });
            @endif

            $(".tab-li").removeClass('active');
            $(".tab-pane").removeClass('active');
            $("#tab-li_1, #tab_1").addClass('active');
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
            <div class="col-md-6">
                <div class="box box-primary">
                    <!-- form start -->
                    <div class="box-header">
                        <b>Main Search</b>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    {{Form::open(array('role'=>"form", 'route' => 'reservationAdd'))}}
                    <div class="box-body">

                        <div class="form-group col-md-6">
                            <label>Hospital</label>
                            <br>
                            <select autocomplete="off" id="selectHospital" required name="hospital_id"
                                    class="form-control select2" style="width: 100%;">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    <option value="{{$val['id']}}" @if(Input::old('hospital_id') == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Clinic/Specialty</label>
                            <br>
                            <select autocomplete="off" id="selectClinic" required name="clinic_id"
                                    class="form-control select2" style="width: 100%;">
                                <option value="">Choose</option>
                            </select>
                        </div>

                        {{--<div class="form-group col-md-6">--}}
                        {{--<label>Physician Experience</label>--}}
                        {{--<br>--}}
                        {{--<select autocomplete="off" id="user_experience_id" name="user_experience_id"--}}
                        {{--class="form-control select2">--}}
                        {{--<option value="">Choose</option>--}}
                        {{--@foreach($experience as $val)--}}
                        {{--<option value="{{$val['id']}}"--}}
                        {{--@if($user_experience_id == $val['id'])--}}
                        {{--selected @endif>{{$val['name']}}</option>--}}
                        {{--@endforeach--}}
                        {{--</select>--}}
                        {{--</div>--}}

                        {{--<div class="form-group col-md-6">--}}
                        {{--<label>Physician Specialty</label>--}}
                        {{--<br>--}}
                        {{--<select autocomplete="off" id="user_specialty_id" name="user_specialty_id"--}}
                        {{--class="form-control select2">--}}
                        {{--<option value="">Choose</option>--}}
                        {{--@foreach($specialty as $val)--}}
                        {{--<option @if($user_specialty_id == $val['id'])--}}
                        {{--selected @endif value="{{$val['id']}}">{{$val['name']}}</option>--}}
                        {{--@endforeach--}}
                        {{--</select>--}}
                        {{--</div>--}}

                        <div class="form-group col-md-6">
                            <label>Physician</label>
                            <br>
                            <select autocomplete="off" id="selectPhysician" required name="physician_id"
                                    class="form-control select2" style="width: 100%;">
                                <option value="">Choose</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Date</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{$selectDate}}"
                                   name="date" class="form-control datepicker">
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Get</button>
                        {{--<a id="physicianProfileBtn" class="btn btn-primary" style="margin-left: 20px">Physician--}}
                        {{--Profile--}}
                        {{--</a>--}}
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <b>Clinic Info</b>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <!-- form start -->
                    <div class="box-body">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Clinic Name:</label>
                                {{$clinic['name']}}
                            </div>
                            <div class="form-group">
                                <label>Current schedule:</label>
                                From: {{$clinicSchedule['start_date']}} --
                                To: {{$clinicSchedule['end_date']}}
                            </div>
                            <div class="form-group">
                                <label>Clinic Day off:</label>
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
                            <div class="form-group">
                                <a data-target="#ModalDiscoverPhysicianSchedule" data-toggle="modal"
                                   class="btn btn-primary">Discover Schedules</a>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
            <div class="col-md-12">
                <div class="nav-tabs-custom" id="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li id="tab-li_0" class="tab-li active"><a href="#tab_0" data-toggle="tab">Calendar</a></li>
                        <li id="tab-li_1" class="tab-li "><a href="#tab_1" data-toggle="tab">Physician Time</a></li>
                        @if($c_user->user_type_id == 1 || $c_user->hasAccess('reservation.patient_reservation_tab'))
                            {{--<li id="tab-li_2" class="tab-li "><a href="#tab_2" data-toggle="tab">Patients--}}
                            {{--Reservation</a>--}}
                            {{--</li>--}}
                        @endif
                    </ul>
                    <div class="tab-content col-md-12">
                        <div class="tab-pane active" id="tab_0">
                            <div id='calendar'></div>
                        </div>
                        <div class="tab-pane" id="tab_1">
                            @if($physician_selected['haveSchedule'])
                                <div class="col-md-12">
                                    <div class="form-group col-md-6">
                                        <label>Physician Name:</label>
                                        {{$physician_selected['full_name']}}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Extension Number:</label>
                                        {{$physician_selected['extension_num']}}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Visit Duration:</label>
                                        {{$physician_selected['slots']}} Minutes
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="form-group col-md-4">
                                        <label>Work Time:</label>
                                        <span>Shift 1:</span>
                                        @if($physician_selected['start_time_1'])
                                            {{$physician_selected['start_time_1']}}
                                            -
                                            {{$physician_selected['end_time_1']}}
                                        @else
                                            <span style="color: red">Day Off</span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-4">
                                        @if($clinicSchedule['num_of_shifts'] == 2 || $clinicSchedule['num_of_shifts'] == 3)
                                            <span>Shift 2:</span>
                                            @if($physician_selected['start_time_2'])
                                                {{$physician_selected['start_time_2']}}
                                                -
                                                {{$physician_selected['end_time_2']}}
                                            @else
                                                <span style="color: red">Day Off</span>
                                            @endif
                                    </div>
                                    <div class="form-group col-md-4">
                                        @if($clinicSchedule['num_of_shifts'] == 3)
                                            <span>Shift 3:</span>
                                            @if($physician_selected['start_time_3'])
                                                {{$physician_selected['start_time_3']}}
                                                -
                                                {{$physician_selected['end_time_3']}}
                                            @else
                                                <span style="color: red">Day Off</span>
                                            @endif
                                        @endif
                                        @endif
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="form-group col-md-4">
                                        <label>Shift 1 off:</label>
                                        {{$physician_selected['dayOff_1']}}
                                    </div>
                                    @if($clinicSchedule['num_of_shifts'] == 2 || $clinicSchedule['num_of_shifts'] == 3)
                                        <div class="form-group col-md-4">
                                            <label>Shift 2 off:</label>
                                            {{$physician_selected['dayOff_2']}}
                                        </div>
                                        @if($clinicSchedule['num_of_shifts'] == 3)
                                            <div class="form-group col-md-4">
                                                <label>Shift 3 off:</label>
                                                {{$physician_selected['dayOff_3']}}
                                            </div>
                                        @endif
                                    @endif
                                    <div class="form-group col-md-12">
                                        {{Form::open(array('route' => 'getFirstFreeSlot'))}}
                                        @if($inputs)
                                            @foreach($inputs as $key => $val)
                                                <input type="hidden" name="{{$key}}" value="{{$val}}">
                                            @endforeach
                                        @endif
                                        <button type="submit" class="btn btn-primary">Check Availability</button>
                                        {{Form::close()}}
                                    </div>
                                </div>
                                <div class="col-md-9" style="margin-bottom: 10px;margin-left: 15px">
                                    <button class="btn" style="background: #84e184">Patient Attend</button>
                                    <button class="btn" style="background: #32cd32">Patient In</button>
                                    <button class="btn" style="background: deepskyblue">Patient Out</button>
                                    <button class="btn" style="background: #ffb84d">Pending</button>
                                    <button class="btn" style="background: #ff8566">Archive</button>
                                </div>
                                <div class="col-md-12" id="WithMe">
                                    {{--//////////////View Physician Time//////////////--}}
                                    <div id="physicianTimeHtml">
                                        {{$physicianTimeHtml}}
                                    </div>
                                    <div class="col-md-4">
                                        <div class="box box-primary">
                                            <div class="box-header">
                                                Patient Information
                                                <a id="clearData" class="btn btn-default pull-right">Clear data</a>
                                            </div>
                                            <!-- /.box-header -->
                                            <div class="box-body">
                                                {{Form::open(array('role'=>"form", 'id' => 'patientForm'))}}

                                                <div class="form-group col-md-12">
                                                    <label>National Id *</label>
                                                    <input autocomplete="off" id="national_id" type="text"
                                                           name="national_id" class="form-control">
                                                </div>

                                                <input autocomplete="off" type="hidden" value="0" name="patient_id"
                                                       id="patient_id">

                                                <div class="form-group col-md-6">
                                                    <label>Patient Id</label>
                                                    <input autocomplete="off" id="id" type="text"
                                                           name="id" class="form-control">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label>Phone *</label>
                                                    <input autocomplete="off" id="phone" type="text" maxlength="15"
                                                           name="phone" class="form-control">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label>First Name *</label>
                                                    <input autocomplete="off" id="first_name" type="text"
                                                           name="first_name" class="form-control">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label>Middle Name</label>
                                                    <input autocomplete="off" id="middle_name" type="text"
                                                           name="middle_name" class="form-control">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label>Last Name *</label>
                                                    <input autocomplete="off" id="last_name" type="text"
                                                           name="last_name" class="form-control">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label>Family Name</label>
                                                    <input autocomplete="off" id="family_name" type="text"
                                                           name="family_name" class="form-control">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label>Updated Phone</label>
                                                    <input autocomplete="off" type="text" maxlength="15"
                                                           name="phone2" id="phone2" class="form-control">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label>Birthday *</label>
                                                    <input required autocomplete="off" id="birthday" type="text"
                                                           data-date-format="yyyy-mm-dd" name="birthday"
                                                           class="form-control datepicker2">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label>Gender *</label>

                                                    <div class="radio">
                                                        <label>
                                                            <input autocomplete="off" id="male" type="radio"
                                                                   value="2"
                                                                   name="gender" checked>
                                                            Male
                                                        </label>
                                                    </div>
                                                    <div class="radio">
                                                        <label>
                                                            <input autocomplete="off" id="female" type="radio"
                                                                   value="1" name="gender">
                                                            Female
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-12">
                                                    <label>Preferred Contact *</label>
                                                    <br>
                                                    <select autocomplete="off" id="preferred_contact" style="width:100%"
                                                            name="preferred_contact" class="form-control select2">
                                                        <option value="1">Phone</option>
                                                        <option value="2">Email</option>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-12">
                                                    <label>Email</label>
                                                    <input autocomplete="off" id="email" type="email" name="email"
                                                           class="form-control">
                                                </div>

                                                <div class="form-group col-md-12">
                                                    <label>Address</label>
                                                    <input autocomplete="off" id="address" type="text" name="address"
                                                           class="form-control">
                                                </div>
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('reservation.patientUpdate'))
                                                    <a id="updatePatient" class="btn btn-default pull-right">Update
                                                        Data</a>
                                                @endif
                                                {{Form::close()}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-md-12">
                                    <label>This Physician Didn't Have Schedule For Current Clinic Schedule!</label>
                                </div>
                            @endif
                        </div>
                        <div class="tab-pane" id="tab_2">
                            <div class="col-md-12" id="tab_2_search">
                                <div class="box box-primary">
                                    <div class="box-header">
                                        Search
                                        <button type="button" class="btn btn-box-tool pull-right"
                                                data-widget="collapse">
                                            <i class="fa fa-minus"></i></button>
                                    </div>
                                    <!-- /.box-header -->
                                    {{Form::open(array('role'=>"form", 'id' => 'patientReservationForm',
                                     'route' => 'searchPatientReservation'))}}
                                    <div class="box-body">
                                        <div class="form-group col-md-3">
                                            <label>Hospital</label>
                                            <br>
                                            <select autocomplete="off" id="selectHospital3" name="hospital_id"
                                                    class="form-control select2" style="width: 100%">
                                                <option value="">Choose</option>
                                                @foreach($hospitals as $val)
                                                    <option value="{{$val['id']}}">{{$val['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Clinics</label>
                                            <br>
                                            <select autocomplete="off" id="selectClinic3" name="clinic_id"
                                                    class="form-control select2" style="width: 100%">
                                                <option value="">Choose</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Physicians</label>
                                            <br>
                                            <select autocomplete="off" id="selectPhysician3" name="physician_id"
                                                    class="form-control select2" style="width: 100%">
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Reservation Code</label>
                                            <input type="text" name="code" class="form-control">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Phone</label>
                                            <input type="text" maxlength="15" name="phone" class="form-control">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Name</label>
                                            <input type="text" name="name" class="form-control">
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label>Patient Id</label>
                                            <input type="text" name="id" class="form-control">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>National Id</label>
                                            <input type="text" name="national_id" class="form-control">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Date From</label>
                                            <input type="text" data-date-format="yyyy-mm-dd"
                                                   name="date_from" class="form-control datepicker2">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Date To</label>
                                            <input type="text" data-date-format="yyyy-mm-dd"
                                                   name="date_to" class="form-control datepicker2">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Reservation Type</label>
                                            <select autocomplete="off" name="type"
                                                    class="form-control select2">
                                                <option value="">Choose</option>
                                                <option value="1">Call</option>
                                                <option value="2">Wait</option>
                                                <option value="3">Revisit</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button class="btn btn-primary" type="submit">Search</button>
                                    </div>
                                    {{Form::close()}}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header">
                                        Patients
                                        <button type="button" class="btn btn-box-tool pull-right"
                                                data-widget="collapse">
                                            <i class="fa fa-minus"></i></button>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body table-responsive" id="patientReservationBody">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

    <div class="modal fade" id="modalShowPatientInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Patient Info</h4>
                </div>
                <div class="modal-body col-md-12">
                    <div class="form-group col-md-6">
                        <label>Reservation Id</label>

                        <div id="info_reservation_id"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Physician Name</label>

                        <div id="info_physician_name"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Date</label>

                        <div id="info_date"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Patient Id</label>

                        <div id="info_patient_id"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Patient Name</label>

                        <div id="patient_name"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Patient Phone</label>

                        <div id="patient_phone"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Patient Birthday</label>

                        <div id="patient_birthday"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Patient Gender</label>

                        <div id="patient_gender"></div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNoteReservation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Add Note To Reservation</h4>
                </div>
                <div class="modal-body col-md-12">
                    <div class="form-group col-md-12">
                        <label>Notes</label>
                        <textarea id="reservation_note" name="notes" class="form-control"></textarea>
                        <input type="hidden" id="modal_note_reservation_id" name="reservation_id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="modalNoteReservationBtn" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAddReservation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Reservation Summary</h4>
                </div>
                <div class="modal-body col-md-12">
                    <div class="form-group col-md-12">
                        <label>Date: </label>

                        <div><h3>{{date('l', strtotime($selectDate))}} {{$selectDate}}</h3></div>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Physician Name: </label>

                        <div id="physician_name"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>From Time: </label>

                        <div id="from_time"></div>
                        <input type="hidden" id="from_time_origin">
                    </div>
                    <div class="form-group col-md-6">
                        <label>To Time: </label>

                        <div id="to_time"></div>
                        <input type="hidden" id="to_time_origin">
                    </div>
                    <div class="form-group col-md-6">
                        <label>SMS Language: </label>
                        <br>
                        <select id="sms_lang">
                            <option selected value="1">Arabic</option>
                            <option value="2">English</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="modalAddReservationBtn" class="btn btn-primary">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ModalDiscoverPhysicianSchedule" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="width: 60%">
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
                        <select autocomplete="off" id="selectHospital2" name="hospital_id"
                                class="form-control select2" style="width: 100%">
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
                        <select autocomplete="off" id="selectClinic2" name="clinic_id"
                                class="form-control select2" style="width: 100%">
                            <option value="">Choose</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Clinic Schedules</label>
                        <br>
                        <select autocomplete="off" id="clinic_schedule_id" name="clinic_schedule_id"
                                class="form-control select2" style="width: 100%">

                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Physicians</label>
                        <br>
                        <select autocomplete="off" id="selectPhysician2" name="user_id" class="form-control select2"
                                style="width: 100%">

                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Physician Schedules</label>
                        <select id="physician_schedule_id" name="physician_schedule_id"
                                class="form-control select2">
                            <option selected value="">Choose</option>
                        </select>
                    </div>
                    <div class="form-group col-md-12" id="physicianScheduleDiv">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ModalPhysicianProfile" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="width: 1000px">
            <div class="modal-content col-md-12">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Physician Profile</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group col-md-4">
                        <label>Image</label>

                        <div id="profile_image"></div>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Name</label>

                        <div id="profile_name"></div>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Extension Number</label>

                        <div id="profile_extension_num"></div>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Age</label>

                        <div id="profile_age"></div>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Gender</label>

                        <div id="profile_gender"></div>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Certificates</label>

                        <div id="profile_certificates"></div>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Awards</label>

                        <div id="profile_awards"></div>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Equipments</label>

                        <div id="profile_equipments"></div>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Clinic Services</label>

                        <div id="profile_clinic_services"></div>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Performed Operations</label>

                        <div id="profile_performed_operations"></div>
                    </div>
                    <div class="form-group col-md-12">
                        <label>General Notes</label>

                        <div id="profile_notes"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop