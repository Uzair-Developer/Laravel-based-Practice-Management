@extends('layout/main')

@section('title')
    - Clinic Availability Times
@stop

@section('header')
    {{--    <link href='{{asset('plugins/jQueryUI/jquery-ui.css')}}' rel='stylesheet'/>--}}
    <link rel="stylesheet" href="{{asset('plugins/autocomplete/jquery.autocomplete.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">

    <style>
        .acResults {
            z-index: 100000000;
        }

        .ui-widget.ui-widget-content {
            z-index: 2147483647;
        }
    </style>
@stop

@section('footer')

    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    {{--<script src='{{asset('plugins/jQueryUI/jquery-ui.js')}}'></script>--}}
    <script src="{{asset('plugins/autocomplete/jquery.autocomplete.js')}}"></script>
    <script>
        $(function () {

            $('.datepicker2').datepicker({
                todayHighlight: true,
                autoclose: true
            });

            $('.datepicker').datepicker({
                @if(date('H:i:s') > '23:59:59')
                startDate: "-1d",
                @else
                startDate: "1d",
                @endif
                todayHighlight: true,
                autoclose: true
            });

            $("#selectClinic2").attr('disabled', 'disabled');
            $("#selectHospital2").val(2).select2();
            $.ajax({
                url: '{{route('getClinicsByHospitalId')}}',
                method: 'POST',
                data: {
                    hospital_id: 2
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#selectClinic2").removeAttr('disabled').html(data).select2();
                }
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
                        $("#id").autocomplete("destroy");
                        idautocomplete('?hospital_id=' + $("#selectHospital2").val());
                        idBlur();

                        phoneautocomplete('?hospital_id=' + $("#selectHospital2").val());
                        phoneKeyUp();

                        nationalidautocomplete('?hospital_id=' + $("#selectHospital2").val());
                        nationalidKeyUp();
                    }
                });
            });

            $("#selectClinic2").change(function (e) {
                $("#selectPhysician2").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getPhysicianByClinicId')}}',
                    method: 'POST',
                    data: {
                        clinic_id: $(this).val(),
                        bookable: true
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectPhysician2").removeAttr('disabled').html(data).select2();
                        $("#availability_div").html('');
                        $("#getDate").val('');
                    }
                });
            });

            $("#selectPhysician2").change(function (e) {
                $("#availability_div").html('');
                $("#getDate").val('');
            });

            $('#getDate').datepicker().on('changeDate', function (ev) {
                if ($('#selectClinic2').val().length) {
                    $("#availability_div").html('');
                    $.ajax({
                        url: '{{route('getAvailabilityByClinicId')}}',
                        method: 'POST',
                        data: {
                            clinic_id: $('#selectClinic2').val(),
                            physician_id: $('#selectPhysician2').val(),
                            date: $('#getDate').val()
                        },
                        headers: {token: '{{csrf_token()}}'},
                        success: function (data) {
                            $("#availability_div").html(data);
                        }
                    });
                } else {
                    alert('Please select clinic first');
                }
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
                        $("#id2").autocomplete("destroy");
                        id2autocomplete('?hospital_id=' + $("#selectHospital3").val());
                        id2Blur();

                        phone2autocomplete('?hospital_id=' + $("#selectHospital3").val());
                        phone2KeyUp();

                        nationalid2autocomplete('?hospital_id=' + $("#selectHospital3").val());
                        nationalid2KeyUp();
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
                $("#revisit_date").val('');
                $("#revisit_time").val('');
            });

            $("#selectHospital5").change(function (e) {
                $("#selectClinic5").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getClinicsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectClinic5").removeAttr('disabled').html(data).select2();
                        $("#physicianTimeHtml2").html('');
                        $("#getDate2").val('');
                    }
                });
            });

            $("#selectClinic5").change(function (e) {
                $("#selectPhysician5").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getPhysicianByClinicId')}}',
                    method: 'POST',
                    data: {
                        clinic_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectPhysician5").removeAttr('disabled').html(data).select2();
                        $("#physicianTimeHtml2").html('');
                        $("#getDate2").val('');
                    }
                });
            });

            $("#selectPhysician5").change(function (e) {
                $("#physicianTimeHtml2").html('');
                $("#getDate2").val('');
            });

            $('#getDate2').datepicker().on('changeDate', function (ev) {
                if ($("#selectClinic5").val().length && $("#selectPhysician5").val().length) {
                    $.ajax({
                        url: '{{route('getPhysicianAvailableTime')}}',
                        method: 'POST',
                        data: {
                            date: $('#getDate2').val(),
                            clinic_id: $("#selectClinic5").val(),
                            physician_id: $("#selectPhysician5").val(),
                            without_physician_schedule: true
                        },
                        headers: {token: '{{csrf_token()}}'},
                        success: function (data) {
                            $("#physicianTimeHtml2").html(data.physicianTimeHtml);
                        }
                    });
                }
            });

            $(document).on('click', '#addReservationBtn', function (e) {
                e.preventDefault();
                var o = getFormData($("#patientForm").serializeArray());
                if (o['national_id'] == "" || o['national_id'] == " ") {
                    alert('Patient National ID is required');
                    return;
                }
                if (o['phone'] == "" || o['phone'] == " ") {
                    alert('Patient phone is required');
                    return;
                }
                if (Math.floor(o['phone']) != o['phone']) {
                    alert('Patient phone must be numeric number');
                    return;
                }
                if (o['first_name'] == "" || o['first_name'] == " ") {
                    alert('Patient first name is required');
                    return;
                }
                if (o['last_name'] == "" || o['last_name'] == " ") {
                    alert('Patient last name is required');
                    return;
                }
                if (typeof o['gender'] == 'undefined') {
                    alert('Patient gender is required');
                    return;
                }
                if ($("#preferred_contact").val() == 2) {
                    if (o['email'] == "" || o['email'] == " ") {
                        alert('Patient email is required');
                        return;
                    }
                }
                if (confirm('Are You Sure?')) {
                    var time = $("#reservationData").attr('time');
                    var timeForId = $("#reservationData").attr('time_for_id');
                    var physician_id = $("#reservationData").attr('physician_id');
                    var clinic_schedule_id = $("#reservationData").attr('clinic_schedule_id');
                    var o = getFormData($("#patientForm").serializeArray());
                    $.ajax({
                        url: "{{route('createReservation')}}",
                        method: 'POST',
                        data: {
                            patientData: o,
                            time_from: time,
                            physician_id: physician_id,
                            hospital_id: $("#selectHospital2").val(),
                            clinic_id: $("#selectClinic2").val(),
                            date: $("#getDate").val(),
                            schedule_id: clinic_schedule_id,
                            sms_lang: $("#sms_lang").val()
                        },
                        success: function (data) {
                            if (data.physicianTimeHtml == 'No') {
                                alert('This Slot Already Taken By Another Patient, Plz Refresh The Slots.')
                            } else {
                                $("#addReservationModal").modal('hide');
                                alert('Added Successfully');
                                $("#" + physician_id + "_" + timeForId).hide();
                                clearAllPatientDate();
                            }
                        }
                    });
                }
            });

            $(document).on('click', '.reserveBtn', function (e) {
                $("#reservationData").attr('time', $(this).attr('time'));
                $("#reservationData").attr('time_for_id', $(this).attr('time_for_id'));
                $("#reservationData").attr('physician_id', $(this).attr('physician_id'));
                $("#reservationData").attr('clinic_schedule_id', $(this).attr('clinic_schedule_id'));
                $("#addReservationModal").modal('show');
            });

            $("#preferred_contact").change(function (e) {
                if ($(this).val() == 2) {
                    $("#email").attr('required', 'required');
                } else {
                    $("#email").removeAttr('required');
                }
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

            idautocomplete('?hospital_id=' + $("#selectHospital2").val());

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
                $("#phone").autocomplete("destroy");
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

            phoneautocomplete('?hospital_id=' + $("#selectHospital2").val());

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

            nationalidautocomplete('?hospital_id=' + $("#selectHospital2").val());

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
                $('#patientForm input').attr('disabled', 'disabled');
                $('#patientForm textarea').attr('disabled', 'disabled');
                $.ajax({
                    url: "{{route('checkPatientExist')}}",
                    method: 'POST',
                    data: {
                        search: $(opj).val(),
                        this_id: this_id,
                        this_national_id: this_national_id,
                        hospital_id: $("#selectHospital2").val()
                    },
                    success: function (data) {
                        if (!data.national_id) {
                            $('#patientForm input').removeAttr('disabled');
                            $('#patientForm textarea').removeAttr('disabled');
                            $("#patient_id").val('');
                            clearPatientDate();
                            return;
                        }
                        $("#phone").val(data.phone);
                        $("#national_id").val(data.national_id);
                        $("#id").val(data.registration_no);
                        $("#patient_id").val(data.id);
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
                        $('#patientForm input').removeAttr('disabled');
                        $('#patientForm textarea').removeAttr('disabled');
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
                $('#patientForm input').removeAttr('disabled');
                $('#patientForm textarea').removeAttr('disabled');
            }

            function clearPatientDate() {
                $("#address, #patient_id, #id, #name, #first_name, #last_name, #middle_name, #family_name, #birthday, #email, #phone2").val('');
                $("#current_patient").html('<option value="">Choose</option>').select2();
                $("#relevant_id").val('').select2();
                $("#female, #male").removeAttr('checked');
                $("#id").removeAttr('disabled');
            }

            ///////////////////////////////////////////////////

            $("#preferred_contact2").change(function (e) {
                if ($(this).val() == 2) {
                    $("#email2").attr('required', 'required');
                } else {
                    $("#email2").removeAttr('required');
                }
            });

            function id2autocomplete(param) {
                $("#id2").autocomplete({
                    url: '{{route('autoCompletePatient2')}}' + param,
                    minChars: 1,
                    useCache: false,
                    filterResults: false,
                    mustMatch: true,
                    maxItemsToShow: 10,
                    remoteDataType: 'json',
                    onItemSelect: function (item) {
                        $("#id2").val(item.data[0]);
                    }
                });
            }

            id2autocomplete('?hospital_id=' + $("#selectHospital3").val());

            function id2Blur() {
                $("#id2").blur(function (e) {
                    if (!$("#id2").val()) {
                        return;
                    }
                    setTimeout(function () {
                        getPatientData2($("#id2"));
                    }, 500);
                });
            }

            id2Blur();

            function phone2autocomplete(param) {
                $("#phone3").autocomplete("destroy");
                $("#phone3").autocomplete({
                    url: '{{route('autoCompletePatientByPhone')}}' + param,
                    minChars: 2,
                    useCache: false,
                    filterResults: false,
                    mustMatch: false,
                    maxItemsToShow: 10,
                    remoteDataType: 'json',
                    onItemSelect: function (item) {
                        if (item.data[0]) {
                            $("#phone3").val(item.data[0]);
                            $("#patient_id2").val(item.data[1]);
                            setTimeout(function () {
                                getPatientData2($("#patient_id2"));
                            }, 500);
                        }
                    }
                });
            }

            phone2autocomplete('?hospital_id=' + $("#selectHospital3").val());

            function phone2KeyUp() {
                $("#phone3").keyup(function (e) {
                    clearPatientDate2();
                });
            }

            phone2KeyUp();

            function nationalid2autocomplete(param) {
                $("#national_id2").autocomplete("destroy");
                $("#national_id2").autocomplete({
                    url: '{{route('autoCompletePatientByNationalId')}}' + param,
                    minChars: 2,
                    useCache: false,
                    filterResults: false,
                    mustMatch: false,
                    maxItemsToShow: 10,
                    remoteDataType: 'json',
                    onItemSelect: function (item) {
                        if (item.data[0]) {
                            $("#national_id2").val(item.data[0]);
                            $("#patient_id2").val(item.data[1]);
                            setTimeout(function () {
                                getPatientData2($("#patient_id2"));
                            }, 500);
                        }
                    }
                });
            }

            nationalid2autocomplete('?hospital_id=' + $("#selectHospital3").val());

            function nationalid2KeyUp() {
                $("#national_id2").keyup(function (e) {
                    clearPatientDate2();
                });
            }

            nationalid2KeyUp();

            function getPatientData2(opj) {
                var id = $(opj).attr('id');
                if (!$(opj).val()) {
                    return;
                }
                var input = $(opj);
                this_id = id == 'patient_id2';
                this_national_id = id == 'national_id2';
                $('#addWalkInReservation input').attr('disabled', 'disabled');
                $('#addWalkInReservation textarea').attr('disabled', 'disabled');
                $.ajax({
                    url: "{{route('checkPatientExist')}}",
                    method: 'POST',
                    data: {
                        search: $(opj).val(),
                        this_id: this_id,
                        this_national_id: this_national_id,
                        hospital_id: $("#selectHospital3").val()
                    },
                    success: function (data) {
                        if (!data.national_id) {
                            alert('hhhhhh');
                            $('#addWalkInReservation input').removeAttr('disabled');
                            $('#addWalkInReservation textarea').removeAttr('disabled');
                            $("#patient_id2").val('');
                            clearPatientDate2();
                            return;
                        }
                        $("#phone3").val(data.phone);
                        $("#national_id2").val(data.national_id);
                        $("#id2").val(data.registration_no);
                        $("#patient_id2").val(data.id);
                        $("#first_name2").val(data.first_name);
                        $("#middle_name2").val(data.middle_name);
                        $("#last_name2").val(data.last_name);
                        $("#family_name2").val(data.family_name);
                        $("#birthday2").val(data.birthday);
                        $("#preferred_contact2").val(data.preferred_contact).select2();
                        $("#email2").val(data.email);
                        if (data.gender == 2) {
                            $("#female2").prop("checked", false);
                            $("#male2").prop("checked", true);
                        } else if (data.gender == 1) {
                            $("#female2").prop("checked", true);
                            $("#male2").prop("checked", false);
                        }
                        $("#address2").val(data.address);
                        $('#addWalkInReservation input').removeAttr('disabled');
                        $('#addWalkInReservation textarea').removeAttr('disabled');
                        $('#id2').attr('disabled', 'disabled');
                    }
                });
            }

            $("#clearData2").click(function () {
                clearAllPatientDate2();
            });

            function clearAllPatientDate2() {
                $("#phone3, #address2, #patient_id2, #national_id2, #id2, #name2, #first_name2, #last_name2, #middle_name2, #family_name2, #birthday2, #email2, #phone22, #caller_id2, #caller_name2").val('');
                $("#current_patient2").html('<option value="">Choose</option>').select2();
                $("#relevant_id2").val('').select2();
                $("#female2, #male2").removeAttr('checked');
                $('#addWalkInReservation input').removeAttr('disabled');
                $('#addWalkInReservation textarea').removeAttr('disabled');
            }

            function clearPatientDate2() {
                $("#address2, #patient_id2, #id2, #name2, #first_name2, #last_name2, #middle_name2, #family_name2, #birthday2, #email2, #phone22").val('');
                $("#current_patient2").html('<option value="">Choose</option>').select2();
                $("#relevant_id2").val('').select2();
                $("#female2, #male2").removeAttr('checked');
                $("#id2").removeAttr('disabled');
            }

            ///////////////////////////////////////////

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
                $('#modalRevisitReservation input').attr('disabled', 'disabled');
                $('#modalRevisitReservation textarea').attr('disabled', 'disabled');
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
                            $('#modalRevisitReservation input').removeAttr('disabled');
                            $('#modalRevisitReservation textarea').removeAttr('disabled');
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
                        $('#modalRevisitReservation input').removeAttr('disabled');
                        $('#modalRevisitReservation textarea').removeAttr('disabled');
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
                $('#modalRevisitReservation input').removeAttr('disabled');
                $('#modalRevisitReservation textarea').removeAttr('disabled');
            }

            function clearPatientDate3() {
                $("#address3, #patient_id3, #id3, #name3, #first_name3, #last_name3, #middle_name3, #family_name3, #birthday3, #email3, #phone23").val('');
                $("#current_patient3").html('<option value="">Choose</option>').select2();
                $("#relevant_id3").val('').select2();
                $("#female3, #male3").removeAttr('checked');
                $("#id3").removeAttr('disabled');
            }
        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Clinic Availability Times
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    <div class="box-header">
                        Search
                        <div class="pull-right" style="margin-left: 20px;">
                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('manageReservation.walkIn_add'))
                                <a data-target="#addWalkInReservation" data-toggle="modal"
                                   class="btn btn-default">Add Waiting List Reservation</a>
                            @endif
                        </div>
                        <div class="pull-right" style="margin-right: 20px;">
                            <a data-target="#modalDiscoverDay" data-toggle="modal"
                               class="btn btn-default">Discover Day</a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group col-md-3">
                            <label>Hospital</label>
                            <select autocomplete="off" id="selectHospital2" name="hospital_id"
                                    class="form-control select2" style="width: 100%;" name="hospital_id">
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
                                    class="form-control select2" style="width: 100%;">
                                <option value="">Choose</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Physicians</label>
                            <br>
                            <select autocomplete="off" id="selectPhysician2" name="physician_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Date</label>
                            <input autocomplete="off" type="text" data-date-format="yyyy-mm-dd" id="getDate"
                                   name="date" class="form-control datepicker">
                        </div>

                        <input type="hidden" id="reservationData">

                        <div class="col-md-12" id="availability_div">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="addReservationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Add Reservation
                        <a style="margin-left: 20%" id="clearData" class="btn btn-default">Clear data</a>
                    </h4>
                </div>
                {{Form::open(array('role'=>"form", 'id' => 'patientForm'))}}
                <div class="box-body">

                    <div class="form-group col-md-6">
                        <label>National Id *</label>
                        <input required autocomplete="off" id="national_id" type="text"
                               name="national_id" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Phone *</label>
                        <input autocomplete="off" id="phone" type="text" maxlength="15"
                               name="phone" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Patient Id</label>
                        <input autocomplete="off" id="id" type="text"
                               name="id" class="form-control">
                    </div>

                    <input autocomplete="off" type="hidden" value="0" name="patient_id"
                           id="patient_id">

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
                        <input autocomplete="off" id="phone2" type="text" maxlength="15"
                               name="phone2" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Birthday *</label>
                        <input required autocomplete="off" id="birthday" type="text"
                               data-date-format="yyyy-mm-dd" name="birthday"
                               class="form-control datepicker2">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Gender *</label>

                        <div class="checkbox-list">
                            <label class="checkbox-inline">
                                <input required autocomplete="off" id="male" type="radio"
                                       value="2" name="gender" checked class="checkbox-inline checkbox1"> Male
                            </label>
                            <label class="checkbox-inline">
                                <input required autocomplete="off" id="female" type="radio"
                                       value="1" name="gender" class="checkbox-inline checkbox1"> Female
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Preferred Contact *</label>
                        <br>
                        <select autocomplete="off" id="preferred_contact"
                                name="preferred_contact" class="form-control select2">
                            <option value="1">Phone</option>
                            <option value="2">Email</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input autocomplete="off" id="email" name="email" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>SMS Language: </label>
                        <br>
                        <select id="sms_lang" class="select2 form-control" style="width: 100%;">
                            <option selected value="1">Arabic</option>
                            <option value="2">English</option>
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Address</label>
                        <input autocomplete="off" id="address" type="text" name="address"
                               class="form-control">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="addReservationBtn" type="submit" class="btn btn-primary">Save</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDiscoverDay" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Discover Day Of Physician</h4>
                </div>
                <div class="box-body">
                    <div class="form-group col-md-6">
                        <label>Hospital *</label>
                        <select required autocomplete="off" id="selectHospital5"
                                class="form-control select2" name="hospital_id">
                            <option value="">Choose</option>
                            @foreach($hospitals as $val)
                                <option value="{{$val['id']}}">{{$val['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Clinics *</label>
                        <select required autocomplete="off" id="selectClinic5" name="clinic_id"
                                class="form-control select2">
                            <option value="">Choose</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Physicians *</label>
                        <select required autocomplete="off" id="selectPhysician5" name="physician_id"
                                class="form-control select2">
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Date</label>
                        <input autocomplete="off" type="text" data-date-format="yyyy-mm-dd" id="getDate2"
                               name="date" class="form-control datepicker">
                    </div>

                    <div class="form-group col-md-12">
                        <div id="physicianTimeHtml2">

                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>

    <div class="modal fade" id="addWalkInReservation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Add Walk In Reservation
                        <a style="margin-left: 20%" id="clearData2" class="btn btn-default">Clear data</a>
                    </h4>
                </div>
                {{Form::open(array('role'=>"form", 'route' => 'addWalkInReservation'))}}
                <div class="modal-body col-md-12">

                    <div class="form-group col-md-6">
                        <label>Hospital *</label>
                        <br>
                        <select required autocomplete="off" id="selectHospital3"
                                class="form-control select2" name="hospital_id">
                            <option value="">Choose</option>
                            @foreach($hospitals as $val)
                                <option value="{{$val['id']}}">{{$val['name']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>National Id *</label>
                        <input required autocomplete="off" id="national_id2" type="text"
                               name="national_id" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Phone *</label>
                        <input required autocomplete="off" id="phone3" type="text" maxlength="15"
                               name="phone" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Patient Id</label>
                        <input autocomplete="off" id="id2" type="text"
                               name="id" class="form-control">
                    </div>
                    <input autocomplete="off" type="hidden" value="0" name="patient_id"
                           id="patient_id2">

                    <div class="form-group col-md-6">
                        <label>First Name *</label>
                        <input autocomplete="off" id="first_name2" type="text"
                               name="first_name" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Middle Name</label>
                        <input autocomplete="off" id="middle_name2" type="text"
                               name="middle_name" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Last Name *</label>
                        <input autocomplete="off" id="last_name2" type="text"
                               name="last_name" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Family Name</label>
                        <input autocomplete="off" id="family_name2" type="text"
                               name="family_name" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Updated Phone</label>
                        <input autocomplete="off" id="phone22" type="text" maxlength="15"
                               name="phone2" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Birthday *</label>
                        <input required autocomplete="off" id="birthday2" type="text"
                               data-date-format="yyyy-mm-dd" name="birthday"
                               class="form-control datepicker2">
                    </div>

                    <div class="form-group col-md-6" style="margin-bottom: 30px;">
                        <label>Gender *</label>

                        <div class="checkbox-list">
                            <label class="checkbox-inline">
                                <input required autocomplete="off" id="male2" type="radio"
                                       value="2" name="gender" checked class="checkbox-inline checkbox1"> Male
                            </label>
                            <label class="checkbox-inline">
                                <input required autocomplete="off" id="female2" type="radio"
                                       value="1" name="gender" class="checkbox-inline checkbox1"> Female
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Preferred Contact *</label>
                        <br>
                        <select autocomplete="off" id="preferred_contact2"
                                name="preferred_contact" class="form-control select2">
                            <option value="1">Phone</option>
                            <option value="2">Email</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input autocomplete="off" id="email2" name="email" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>SMS Language: </label>
                        <br>
                        <select id="sms_lang2" name="sms_lang" class="select2 form-control" style="width: 100%;">
                            <option selected value="1">Arabic</option>
                            <option value="2">English</option>
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Address</label>
                        <input autocomplete="off" id="address2" type="text" name="address"
                               class="form-control">
                    </div>
                    <hr>
                    <div class="form-group col-md-6">
                        <label>Clinics *</label>
                        <br>
                        <select required autocomplete="off" id="selectClinic3" name="clinic_id"
                                class="form-control select2">
                            <option value="">Choose</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Physicians *</label>
                        <br>
                        <select required autocomplete="off" id="selectPhysician3" name="physician_id"
                                class="form-control select2">
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