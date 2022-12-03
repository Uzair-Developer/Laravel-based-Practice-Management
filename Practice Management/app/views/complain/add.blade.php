@extends('layout/main')

@section('title')
    - {{$complain['patient_id'] ? 'Edit' : 'Add'}} Complaint
@stop

@section('header')
    {{--    <link href='{{asset('plugins/jQueryUI/jquery-ui.css')}}' rel='stylesheet'/>--}}
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/autocomplete/jquery.autocomplete.css')}}">

@stop

@section('footer')
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('plugins/autocomplete/jquery.autocomplete.js')}}"></script>
    {{--    <script src='{{asset('plugins/jQueryUI/jquery-ui.js')}}'></script>--}}
    <script>
        $(function () {
            $('.datepicker2').datepicker({
                todayHighlight: true,
                autoclose: true
            });

            $("#id").autocomplete({
                url: '{{route('autoCompletePatient2')}}',
                minChars: 1,
                useCache: false,
                filterResults: false,
                mustMatch: true,
                maxItemsToShow: 10,
                remoteDataType: 'json',
                onItemSelect: function (item) {
                    console.log(item.data[0]);
                    $("#id").val(item.data[0]);
                }
            });

            $("#id").blur(function (e) {
                if (!$("#id").val()) {
                    return;
                }
                setTimeout(function () {
                    getPatientData($("#id"));
                }, 500);
            });

            $("#national_id").blur(function (e) {
                if (!$("#national_id").val()) {
                    return;
                }
                getPatientData($("#national_id"));
            });

            function getPatientData(opj) {
                var id = $(opj).attr('id');
                if (!$(opj).val()) {
                    return;
                }
                var input = $(opj);
                this_id = id == 'current_patient';
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
                        hospital_id: 2
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
                        $("#first_name").val(data.first_name);
                        $("#middle_name").val(data.middle_name);
                        $("#last_name").val(data.last_name);
                        $("#family_name").val(data.family_name);
                        $("#birthday").val(data.birthday);
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
                $("#national_id, #address, #patient_id, #id, #name, #first_name, #last_name, #middle_name, #family_name, #birthday, #email, #phone2").val('');
                $("#current_patient").html('<option value="">Choose</option>').select2();
                $("#relevant_id").val('').select2();
                $("#female, #male").removeAttr('checked');
                $("#id").removeAttr('disabled');
            }
        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            {{$complain['patient_id'] ? 'Edit' : 'Add'}} Complaint
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    <div class="box-header">
                        <a id="clearData" class="btn btn-default pull-right">Clear data</a>
                    </div>
                    {{Form::open(array('role'=>"form"))}}
                    <div class="box-body" id="tab_1">

                        <div class="form-group col-md-6">
                            <label>Departments</label>
                            <select autocomplete="off" required name="department_id"
                                    class="form-control select2">
                                <option selected value="">Choose</option>
                                @foreach($departments as $val)
                                    <option @if(Input::old('department_id') == $val['id'])
                                            selected
                                            @elseif($complain['department_id'] == $val['id'])
                                            selected
                                            @endif
                                            value="{{$val['id']}}">{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($complain['patient_id'])
                            <div class="form-group col-md-6">
                                <label>Patient Name: {{$patient['name']}}</label>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Patient Phone: {{$patient['phone']}}</label>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Patient Id: {{$patient['registration_no']}}</label>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Birthday: {{$patient['birthday']}}</label>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Gender: {{$patient['gender'] == 1 ? 'Female' : 'Male'}}</label>
                            </div>
                        @else

                            <div class="form-group col-md-6">
                                <label>National Id *</label>
                                <input required autocomplete="off" id="national_id" type="text"
                                       name="national_id" class="form-control">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Phone *</label>
                                <input required autocomplete="off" id="phone" type="text" maxlength="15"
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
                                <input required autocomplete="off" id="first_name" type="text"
                                       name="first_name" class="form-control">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Middle Name *</label>
                                <input required autocomplete="off" id="middle_name" type="text"
                                       name="middle_name" class="form-control">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Last Name *</label>
                                <input required autocomplete="off" id="last_name" type="text"
                                       name="last_name" class="form-control">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Family Name</label>
                                <input autocomplete="off" id="family_name" type="text"
                                       name="family_name" class="form-control">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Birthday *</label>
                                <input required autocomplete="off" id="birthday" type="text"
                                       data-date-format="yyyy-mm-dd" name="birthday"
                                       class="form-control datepicker2">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Email</label>
                                <input autocomplete="off" id="email" type="email" name="email"
                                       class="form-control">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Gender *</label>

                                <div class="radio">
                                    <label>
                                        <input required autocomplete="off" id="male" type="radio"
                                               value="2"
                                               name="gender" checked>
                                        Male
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input required autocomplete="off" id="female" type="radio"
                                               value="1" name="gender">
                                        Female
                                    </label>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Address</label>
                                <input autocomplete="off" id="address" type="text" name="address"
                                       class="form-control">
                            </div>
                        @endif
                        <div class="form-group col-md-12">
                            <label>Complaint Text</label>
                            <textarea name="notes"
                                      class="form-control">{{Input::old('notes') ? Input::old('notes') : $complain['notes']}}</textarea>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('listComplain')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop