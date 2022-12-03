@extends('layout/main')

@section('title')
    - {{$pmsDiagnosis['patient_id'] ? 'Edit' : 'Add'}} PMS Diagnosis
@stop

@section('header')
    <link href='{{asset('plugins/jQueryUI/jquery-ui.css')}}' rel='stylesheet'/>
@stop

@section('footer')
    <script src='{{asset('plugins/jQueryUI/jquery-ui.js')}}'></script>
    <script type="text/javascript">
        $(function () {
            function split(val) {
                return val.split(/\s\s*/);
            }

            function extractLast(term) {
                return split(term).pop();
            }

            $("#primary_diagnosis")
            // don't navigate away from the field on tab when selecting an item
                .on("keydown", function (event) {
                    if (event.keyCode === $.ui.keyCode.TAB &&
                        $(this).autocomplete("instance").menu.active) {
                        event.preventDefault();
                    }
                })
                .autocomplete({
                    minLength: 0,
                    source: function (request, response) {
                        $.getJSON("{{route('getDiseaseByName')}}", {
                            term: extractLast(request.term)
                        }, response);
                    },
                    focus: function () {
                        // prevent value inserted on focus
                        return false;
                    },
                    select: function (event, ui) {
                        var terms = split(this.value);
                        // remove the current input
                        terms.pop();
                        // add the selected item
                        terms.push(ui.item.value);
                        // add placeholder to get the comma-and-space at the end
                        terms.push("");
                        this.value = terms.join(" ");
                        return false;
                    }
                });

            $("#hospital_id").change(function (e) {
                $("#id").autocomplete("destroy");
                idautocomplete('?hospital_id=' + $(this).val());
                idBlur();

                $("#phone").autocomplete("destroy");
                phoneautocomplete('?hospital_id=' + $(this).val());
                phoneKeyUp();

                $("#national_id").autocomplete("destroy");
                nationalidautocomplete('?hospital_id=' + $(this).val());
                nationalidKeyUp();
            });

            function idautocomplete(param) {
                $("#id").autocomplete({
                    source: "{{route('autoCompletePatient')}}" + param,
                    minLength: 1,
                    maxResults: 10,
                    select: function (a, b) {
                        $("#caller_name").focus();
                    }
                });
            }

            idautocomplete("?hospital_id=" + $("#hospital_id").val());

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
                    source: "{{route('autoCompletePatientByPhone2')}}" + param,
                    minLength: 1,
                    maxResults: 10,
                    select: function (a, b) {
                        if (b.item.row_id) {
                            $("#patient_id").val(b.item.row_id);
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
                $("#national_id").autocomplete({
                    source: "{{route('autoCompletePatientByNationalId2')}}" + param,
                    minLength: 1,
                    maxResults: 10,
                    select: function (a, b) {
                        if (b.item.row_id) {
                            $("#patient_id").val(b.item.row_id);
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
                        hospital_id: $("#hospital_id").val()
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
                        $("#age").val(data.age);
                        $("#preferred_contact").val(data.preferred_contact).select2();
                        $("#email").val(data.email);
                        if (data.gender == 2) {
                            $("#female").prop("checked", false);
                            $("#male").prop("checked", true);
                        } else if (data.gender == 1) {
                            $("#female").prop("checked", true);
                            $("#male").prop("checked", false);
                        }
                        if (data.marital_status_id == 1) {
                            $("#single").prop("checked", true);
                            $("#married").prop("checked", false);
                        } else if (data.marital_status_id == 2) {
                            $("#single").prop("checked", false);
                            $("#married").prop("checked", true);
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
                $("#phone, #address, #patient_id, #national_id, #id, #name, #first_name, #last_name, #middle_name, #family_name, #age, #email, #phone2, #caller_id, #caller_name").val('');
                $("#current_patient").html('<option value="">Choose</option>').select2();
                $("#relevant_id").val('').select2();
                $("#female, #male, #single, #married").removeAttr('checked');
                $('#tab_1 input').prop('disabled', false);
                $('#tab_1 textarea').prop('disabled', false);
            }

            function clearPatientDate() {
                $("#address, #patient_id, #id, #name, #first_name, #last_name, #middle_name, #family_name, #age, #email, #phone2").val('');
                $("#current_patient").html('<option value="">Choose</option>').select2();
                $("#relevant_id").val('').select2();
                $("#female, #male, #single, #married").removeAttr('checked');
                $("#id").removeAttr('disabled');
            }

            $("#referred_to_parent_id").change(function (e) {
                $("#referred_to_child_id").attr('disabled', 'disabled');
                $.ajax({
                    url: "{{route('getChildReferredTo')}}",
                    method: 'POST',
                    data: {
                        id: $(this).val()
                    },
                    success: function (data) {
                        $("#referred_to_child_id").html(data).removeAttr('disabled').select2();
                    }
                });
            });

            @if($pmsDiagnosis['referred_to_parent_id'])
            $("#referred_to_child_id").attr('disabled', 'disabled');
            $.ajax({
                url: "{{route('getChildReferredTo')}}",
                method: 'POST',
                data: {
                    id: '{{$pmsDiagnosis['referred_to_parent_id']}}'
                },
                success: function (data) {
                    $("#referred_to_child_id").html(data).val('{{$pmsDiagnosis['referred_to_child_id']}}')
                        .removeAttr('disabled').select2();
                }
            });
            @endif






        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            {{$pmsDiagnosis['patient_id'] ? 'Edit' : 'Add'}} PMS Diagnosis
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    @if(empty($pmsDiagnosis['patient_id']))
                        <div class="box-header">
                            <a id="clearData" class="btn btn-default pull-right">Clear Patient data</a>
                        </div>
                    @endif
                    {{Form::open()}}
                    <div class="box-body" id="tab_1">
                        @if(empty($pmsDiagnosis['patient_id']))
                            <div class="form-group col-md-4">
                                <label>Hospital *</label>
                                <select required name="hospital_id" id="hospital_id" class="form-control select2">
                                    <option value="">Choose</option>
                                    @foreach($hospitals as $val)
                                        <option value="{{$val['id']}}" @if(Input::old('hospital_id') == $val['id'])
                                        selected @endif>{{$val['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="form-group col-md-4">
                            <label>National Id *</label>
                            @if(empty($pmsDiagnosis['patient_id']))
                                <input required autocomplete="off" id="national_id" type="text"
                                       name="national_id" class="form-control">
                            @else
                                <div>{{$patient['national_id']}}</div>
                            @endif
                        </div>

                        <div class="form-group col-md-4">
                            <label>Phone *</label>
                            @if(empty($pmsDiagnosis['patient_id']))
                                <input required autocomplete="off" id="phone" type="text" maxlength="15"
                                       name="phone" class="form-control">
                            @else
                                <div>{{$patient['phone']}}</div>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Patient Id</label>
                            @if(empty($pmsDiagnosis['patient_id']))
                                <input autocomplete="off" id="id" type="text"
                                       name="id" class="form-control">
                            @else
                                <div>{{$patient['registration_no']}}</div>
                            @endif
                        </div>
                        @if(empty($pmsDiagnosis['patient_id']))
                            <input autocomplete="off" type="hidden" value="0" name="patient_id"
                                   id="patient_id">

                            <div class="form-group col-md-4">
                                <label>First Name *</label>
                                <input required autocomplete="off" id="first_name" type="text"
                                       name="first_name" class="form-control">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Middle Name *</label>
                                <input required autocomplete="off" id="middle_name" type="text"
                                       name="middle_name" class="form-control">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Last Name *</label>
                                <input required autocomplete="off" id="last_name" type="text"
                                       name="last_name" class="form-control">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Family Name</label>
                                <input autocomplete="off" id="family_name" type="text"
                                       name="family_name" class="form-control">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Updated Phone</label>
                                <input autocomplete="off" id="phone2" type="text" maxlength="15"
                                       name="phone2" class="form-control">
                            </div>
                        @else
                            <div class="form-group col-md-4">
                                <label>Patient Name</label>

                                <div>
                                    <div>{{$patient['first_name']}} {{$patient['middle_name']}} {{$patient['last_name']}}
                                        {{$patient['family_name']}}</div>
                                </div>
                            </div>
                        @endif

                        <div class="form-group col-md-4">
                            <label>Age *</label>
                            @if(empty($pmsDiagnosis['patient_id']))
                                <input required autocomplete="off" id="age" type="number"
                                       name="age" class="form-control">
                            @else
                                <div>{{$patient['age']}}</div>
                            @endif
                        </div>

                        <div class="form-group col-md-4" style="height: 60px;">
                            <label>Gender *</label>
                            @if(empty($pmsDiagnosis['patient_id']))
                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox1" name="gender"
                                               id="male" type="radio" autocomplete="off" value="2"> Male
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox1" name="gender"
                                               id="female" type="radio" autocomplete="off" value="1"> Female
                                    </label>
                                </div>
                            @else
                                <div>{{$patient['gender'] == 2 ? 'Female' : 'Male'}}</div>
                            @endif
                        </div>

                        <div class="form-group col-md-4" style="height: 60px;">
                            <label>Marital Status *</label>

                            @if(empty($pmsDiagnosis['patient_id']))
                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input required class="checkbox-inline checkbox1" name="marital_status_id"
                                               id="single" type="radio" autocomplete="off" value="1"> Single
                                    </label>
                                    <label class="checkbox-inline">
                                        <input required class="checkbox-inline checkbox1" name="marital_status_id"
                                               id="married" type="radio" autocomplete="off" value="2"> Married
                                    </label>
                                </div>
                            @else
                                <div>{{$patient['marital_status_id'] == 1 ? 'Single' : 'Married'}}</div>
                            @endif
                        </div>

                        <div class="form-group col-md-4">
                            <label>Email</label>
                            @if(empty($pmsDiagnosis['patient_id']))
                                <input autocomplete="off" id="email" type="email" name="email"
                                       class="form-control">
                            @else
                                <div>{{$patient['email']}}</div>
                            @endif
                        </div>

                        <div class="form-group col-md-4">
                            <label>Address</label>
                            @if(empty($pmsDiagnosis['patient_id']))
                                <input autocomplete="off" id="address" type="text" name="address"
                                       class="form-control">
                            @else
                                <div>{{$patient['address']}}</div>
                            @endif
                        </div>

                        <div class="form-group col-md-12">
                            <label>Complain</label>
                            <textarea autocomplete="off" name="complain"
                                      class="form-control">{{$pmsDiagnosis['complain']}}</textarea>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Organs *</label>
                            <select required name="organ_id[]" class="form-control select2" multiple>
                                <option value="">Choose</option>
                                <?php
                                $organsArray = array();
                                if ($pmsDiagnosis['organ_id']) {
                                    $organsArray = explode(',', $pmsDiagnosis['organ_id']);
                                }
                                ?>
                                @foreach($organs as $val)
                                    <option value="{{$val['id']}}" @if(in_array($val['id'], $organsArray))
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Main System Affected *</label>
                            <select required name="main_system_affected_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($main_system_affected as $val)
                                    <option value="{{$val['id']}}"
                                            @if($pmsDiagnosis['main_system_affected_id'] == $val['id'])
                                            selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Primary Diagnosis</label>
                            <textarea autocomplete="off" name="primary_diagnosis" id="primary_diagnosis"
                                      class="form-control">{{$pmsDiagnosis['primary_diagnosis']}}</textarea>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Speciality Referred To *</label>
                            <select required name="referred_to_parent_id" id="referred_to_parent_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($referred_to_parent as $val)
                                    <option value="{{$val['id']}}"
                                            @if($pmsDiagnosis['referred_to_parent_id'] == $val['id'])
                                            selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Sub Speciality Referred To</label>
                            <select name="referred_to_child_id" class="form-control select2" id="referred_to_child_id">
                                <option value="">Choose</option>
                            </select>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('pmsDiagnosis')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop
