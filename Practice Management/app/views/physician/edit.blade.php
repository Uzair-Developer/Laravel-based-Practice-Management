@extends('layout/main')

@section('title')
    - Edit {{$user['user_name']}}
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('plugins/dmuploader/dmuploader.js')}}"></script>
    <script>
        $('#drag-and-drop-zone').dmUploader({
            url: '{{route('uploadPhysicianImage')}}?physician_id={{$user['id']}}',
//            dataType: 'json',
            allowedTypes: 'image/*',
            onUploadSuccess: function (id, data) {
                $("#img_image_url").attr('src', '{{asset('')}}/' + data);
            }
        });

        $(function () {

            $('.datepicker2').datepicker({
                todayHighlight: true,
                autoclose: true
            });

            $("#save_tab_0").click(function (e) {
                var o = getFormData($("#tab_0 :input, #tab_0 textarea, #tab_0 select").serializeArray());
                $.ajax({
                    url: "{{route('saveTap0')}}?user_id={{$user['id']}}",
                    method: 'POST',
                    data: {
                        tab_0: o
                    },
                    success: function (data) {
                        if (data.success == 'no') {
                            alert(data.message);
                        } else {
                            location.reload();
                        }
                    }
                });
            });

            $("#save_tab_1").click(function (e) {
                var o = ($("#tab_1 :input, #tab_1 textarea, #tab_1 select").serialize());
                $.ajax({
                    url: "{{route('saveTap1')}}?user_id={{$user['id']}}",
                    method: 'POST',
                    data: {
                        tab_1: o
                    },
                    success: function (data) {
                        if (data.success == 'no') {
                            alert(data.message);
                        } else {
                            location.reload();
                        }
                    }
                });
            });

            $("#save_tab_2").click(function (e) {
                var o = ($("#tab_2 :input, #tab_2 textarea, #tab_2 select").serialize());
                $.ajax({
                    url: "{{route('saveTap2')}}?user_id={{$user['id']}}",
                    method: 'POST',
                    data: {
                        tab_2: o
                    },
                    success: function (data) {
                        if (data.success == 'no') {
                            alert(data.message);
                        } else {
                            location.reload();
                        }
                    }
                });
            });

            $("#country_id").change(function (e) {
                $("#city_id").attr('disabled', 'disabled');
                $.ajax({
                    url: "{{route('getCitiesOfCountry')}}",
                    method: 'POST',
                    data: {country_id: $(this).val()},
                    success: function (data) {
                        $("#city_id").html(data).select2().removeAttr('disabled');
                    }
                });
            });

            $("#physician_save_submit").click(function (e) {
                $("#save_status").val('physician_save_submit');
            });

            $("#physician_save").click(function (e) {
                $("#save_status").val('physician_save');
            });

            $(".ask-me").click(function (e) {
                e.preventDefault();
                msg = $(this).attr('show-msg');
                if (msg) {
                    if (confirm('Are You Sure? (' + msg + ')')) {
                        window.location.replace($(this).attr('href'));
                    }
                } else {
                    if (confirm('Are You Sure?')) {
                        window.location.replace($(this).attr('href'));
                    }
                }
            });

            $(".backToDrBtn, .upPublishBtn").click(function (e) {
                e.preventDefault();
                var href = $(this).attr('href');
                $("#headNotesForm").attr('action', href);
                $("#headNotesModal").modal('show');
            });

            @if(!empty($physician['country_id']))
            $.ajax({
                        url: "{{route('getCitiesOfCountry')}}",
                        method: 'POST',
                        data: {country_id: $("#country_id").val()},
                        success: function (data) {
                            $("#city_id").html(data).select2();
                            @if(!empty($physician['country_id']))
                            $("#city_id").val('{{$physician['city_id']}}').select2();
                            @endif
                        }
                    });
            @endif
    @if($c_user->user_type_id == 7 && !$c_user->hasAccess('head_dept.access'))
        <?php $physicianData = Physician::getByPhysicianId($c_user->id); ?>
        @if(empty($physicianData) || ($physicianData && $physicianData['current_status'] == 0))
        @else
            $("input, textarea, select").attr('disabled', 'disabled');
            $(".submitBtn").hide();
            @endif
        @endif

        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Edit {{$user['user_name']}}
        </h1>
    </section>
    <section class="content">
        <div class="row">
            @if($physician['current_status'] == 0 && $physician['head_notes'])
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header">
                            Notes
                            <button type="button" class="btn btn-box-tool pull-right"
                                    data-widget="collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            {{$physician['head_notes']}}
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-md-12">
                {{Form::open(array('role'=>"form",'files' => true, 'url' => $form_action, 'id' => 'editPhysicianProfile'))}}
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li id="tab-li_0" class="tab-li active"><a href="#tab_0" data-toggle="tab">Personal Info</a>
                        </li>
                        <li id="tab-li_1" class="tab-li"><a href="#tab_1" data-toggle="tab">Career Info</a></li>
                        <li id="tab-li_2" class="tab-li"><a href="#tab_2" data-toggle="tab">Clinic Facilities</a></li>
                    </ul>
                    <div class="tab-content col-md-12">
                        <div class="tab-pane active" id="tab_0">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group col-md-3">
                                        <label>First name En <span class="text-red">*</span></label>
                                        <input required type="text" value="{{$user['first_name']}}" name="first_name"
                                               class="form-control">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>Middle name En <span class="text-red">*</span></label>
                                        <input required type="text" value="{{$user['middle_name']}}" name="middle_name"
                                               class="form-control">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>Last name En <span class="text-red">*</span></label>
                                        <input required type="text" value="{{$user['last_name']}}" name="last_name"
                                               class="form-control">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>Family name En</label>
                                        <input type="text" value="{{$user['family_name']}}" name="family_name"
                                               class="form-control">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>First name Ar</label>
                                        <input type="text" value="{{$user['first_name_ar']}}" name="first_name_ar"
                                               class="form-control">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>Last name Ar</label>
                                        <input type="text" value="{{$user['last_name_ar']}}" name="last_name_ar"
                                               class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Doctor Image</label>

                                        <div id="drag-and-drop-zone" class="uploader">
                                            <input class="form-control" type="file"
                                                   name="image_url">
                                        </div>
                                        @if(!empty($user['image_url']))
                                            <img id="img_image_url" src="{{asset($user['image_url'])}}" width="100"
                                                 height="100">
                                        @endif
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Extension Number <span class="text-red">*</span></label>
                                        <input required type="text" value="{{$user['extension_num']}}"
                                               name="extension_num"
                                               class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Email</label>
                                        <input type="email" value="{{$user['email']}}" name="email"
                                               class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Phone number</label>
                                        <input type="text" value="{{$user['phone_number']}}"
                                               name="phone_number" class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Password</label>
                                        <input autocomplete="off" type="password" name="password" class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Confirmation Password</label>
                                        <input autocomplete="off" type="password" name="password_confirmation"
                                               class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Mobile 1</label>
                                        <input type="text" value="{{$user['mobile1']}}"
                                               name="mobile1" class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Mobile 2</label>
                                        <input type="text" value="{{$user['mobile2']}}"
                                               name="mobile2" class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Birthday</label>
                                        <input type="text" data-date-format="yyyy-mm-dd"
                                               value="{{$physician['birthdate']}}"
                                               name="birthdate" class="form-control datepicker2">
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Address</label>
                                        <input type="text" value="{{$user['address']}}"
                                               name="address" class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Country <span class="text-red">*</span></label>
                                        <select autocomplete="off" id="country_id" required name="country_id"
                                                class="form-control select2">
                                            <option selected value="">Choose</option>
                                            @foreach($countries as $val)
                                                <option @if($physician['country_id'] == $val['id']) selected
                                                        @endif value="{{$val['id']}}">{{$val['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>City <span class="text-red">*</span></label>
                                        <select autocomplete="off" id="city_id" required name="city_id"
                                                class="form-control select2">
                                            <option selected value="">Choose</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Gender <span class="text-red">*</span></label>
                                        <select required name="gender" class="form-control select2">
                                            <option value="">Choose</option>
                                            <option value="2" @if($physician['gender'] == 2) selected @endif>Male
                                            </option>
                                            <option value="1" @if($physician['gender'] == 1) selected @endif>Female
                                            </option>
                                        </select>
                                    </div>

                                    @if($c_user->user_type_id == 1 || $c_user->user_type_id != 7)
                                        <div class="form-group col-md-6">
                                            <label>Bookable <span class="text-red">*</span></label>
                                            <select required name="bookable" class="form-control select2">
                                                <option value="">Choose</option>
                                                <option value="1" @if($user['bookable'] == 1) selected @endif>Bookable
                                                </option>
                                                <option value="2" @if($user['bookable'] == 2) selected @endif>Not
                                                    Bookable
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Show In Reports</label>

                                            <div class="checkbox-list">
                                                <label class="checkbox-inline">
                                                    <input class="checkbox-inline checkbox1" name="in_report"
                                                           @if($user['in_report'] == 1) checked
                                                           @endif
                                                           type="checkbox" autocomplete="off" value="1"> Yes
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Revisit Limit (Days)</label>
                                            <input autocomplete="off" type="number" name="revisit_limit"
                                                   value="{{$user['revisit_limit']}}" class="form-control">
                                        </div>
                                    @endif
                                    <div class="form-group col-md-12">
                                        <button style="margin-bottom: 30px;" class="btn btn-warning" id="save_tab_0"
                                                type="button">Save Tap 1
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_1">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group col-md-6" style="margin-bottom: 5px;">
                                        <label>Specialty</label>
                                        <br>
                                        <select autocomplete="off" multiple name="specialty_id[]" class="form-control select2">
                                            <?php $user_specialty_id = explode(',', $user['user_specialty_id']); ?>
                                            @foreach($specialty as $val)
                                                <option value="{{$val['id']}}"
                                                        @if(in_array($val['id'], $user_specialty_id))
                                                        selected @endif>{{$val['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Experience</label>
                                        <br>
                                        <select name="user_experience_id" class="form-control select2">
                                            <option value="">Choose</option>
                                            @foreach($experience as $val)
                                                <option value="{{$val['id']}}"
                                                        @if($user['user_experience_id'] == $val['id'])
                                                        selected @endif>{{$val['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Graduation</label>
                                        <input type="text" value="{{$physician['graduation']}}"
                                               name="graduation" class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Graduated from</label>
                                        <input type="text" value="{{$physician['graduated_from']}}"
                                               name="graduated_from" class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Degree</label>
                                        <br>
                                        <select name="degree" class="form-control select2" style="width: 500px;">
                                            <option value="">Choose</option>
                                            <option value="1" @if($physician['degree'] == 1) selected @endif>1
                                            </option>
                                            <option value="2" @if($physician['degree'] == 2) selected @endif>2
                                            </option>
                                            <option value="3" @if($physician['degree'] == 3) selected @endif>3
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>About physician</label>
                                            <textarea name="about"
                                                      class="form-control">{{$physician['about']}}</textarea>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Attaches</label>
                                        <input type="file" value="" name="attaches" class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>License number</label>
                                        <input type="text" value="{{$physician['license_number']}}"
                                               name="license_number" class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>License Activation</label>
                                        <br>
                                        <select name="license_activation" class="form-control select2"
                                                style="width: 500px;">
                                            <option value="">Choose</option>
                                            <option value="1" @if($physician['job_position'] == 1) selected @endif>
                                                Active
                                            </option>
                                            <option value="2" @if($physician['job_position'] == 2) selected @endif>
                                                Deactivated
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Certificates</label>
                                        <textarea name="certificates"
                                                  class="form-control">{{$physician['certificates']}}</textarea>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Awards</label>
                                        <textarea name="awards"
                                                  class="form-control">{{$physician['awards']}}</textarea>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Credentials</label>
                                        <textarea name="credentials"
                                                  class="form-control">{{$physician['credentials']}}</textarea>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <button style="margin-bottom: 30px;" class="btn btn-warning" id="save_tab_1"
                                                type="button">Save Tap 2
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_2">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group col-md-6">
                                        <label>Clinic Services <span class="text-red">*</span></label>
                                        <br>
                                        <?php $physician_c_s = explode(',', $physician['clinic_services']); ?>
                                        <select required autocomplete="off" multiple name="clinic_services[]"
                                                class="select2"
                                                style="width: 100%;">
                                            <option value="">Choose</option>
                                            @foreach($clinic_services as $key => $val)
                                                <option value="{{$val['id']}}"
                                                        @if(in_array($val['id'], $physician_c_s)) selected
                                                        @endif>{{$val['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Performed Operations</label>
                                        <br>
                                        <?php $physician_p_o = explode(',', $physician['performed_operations']); ?>
                                        <select autocomplete="off" multiple name="performed_operations[]"
                                                class="select2" style="width: 100%;">
                                            <option value="">Choose</option>
                                            @foreach($performed_operations as $key => $val)
                                                <option value="{{$val['id']}}"
                                                        @if(in_array($val['id'], $physician_p_o)) selected
                                                        @endif>{{$val['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Equipments</label>
                                        <?php $physician_e = explode(',', $physician['equipments']); ?>
                                        <select autocomplete="off" multiple name="equipments[]" class="select2"
                                                style="width: 100%;">
                                            <option value="">Choose</option>
                                            @foreach($equipments as $key => $val)
                                                <option value="{{$val['id']}}"
                                                        @if(in_array($val['id'], $physician_e)) selected
                                                        @endif>{{$val['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>General Notes</label>
                                        <textarea name="notes"
                                                  class="form-control">{{$physician['notes']}}</textarea>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <button style="margin-bottom: 30px;" class="btn btn-warning" id="save_tab_2"
                                                type="button">Save Tap 3
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div>
                            <input autocomplete="off" type="hidden" id="save_status" name="save_status">
                            @if($c_user->user_type_id == 7 && !$c_user->hasAccess('head_dept.access'))
                                <button class="btn btn-primary submitBtn" id="physician_save_submit" type="submit">Save
                                    And Submit
                                </button>
                                <button class="btn btn-primary submitBtn" id="physician_save" type="submit">Save
                                </button>
                            @else
                                <button class="btn btn-primary" type="submit">Save</button>
                            @endif

                            @if($c_user->user_type_id != 7 || $c_user->hasAccess('head_dept.access'))
                                <a href="{{route('physicians')}}" class="btn btn-info">Back</a>
                            @endif
                            <?php $userData = Sentry::findUserByID($user['id']); ?>
                            @if($c_user->user_type_id == 1
/*if physician and head*/       || ($c_user->user_type_id == 7 && $c_user->hasAccess('head_dept.access') && !$userData->hasAccess('head_dept.access'))
/*if clinic manger and head*/   || ($c_user->user_type_id == 3 && $c_user->hasAccess('head_dept.access') && $userData->hasAccess('head_dept.access')))
                                @if($c_user->id != $physician['id'])
                                    @if(isset($physician['current_status']) &&
                                    $physician['current_status'] == 1)
                                        <a class="btn btn-default ask-me"
                                           href="{{route('changeProfileStatus', array($user['id'], 'publish'))}}">
                                            Publish</a>
                                    @endif
                                    @if(isset($physician['current_status']) &&
                                    $physician['current_status'] == 1)
                                        <a class="btn btn-default backToDrBtn"
                                           href="{{route('changeProfileStatus', array($user['id'], 'back-to-dr'))}}">
                                            Back To Dr</a>
                                    @endif
                                    @if(isset($physician['current_status']) &&
                                    $physician['current_status'] == 2)
                                        <a class="btn btn-warning upPublishBtn"
                                           href="{{route('changeProfileStatus', array($user['id'], 'un-publish'))}}">
                                            Un Publish</a>
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </section>

    <div class="modal fade" id="headNotesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Notes</h4>
                </div>
                {{Form::open(array('role'=>"form", 'id' => 'headNotesForm', 'method' => 'GET'))}}
                <div class="modal-body col-md-12">
                    <div class="form-group col-md-12">
                        <label>Notes</label>
                        <textarea required name="head_notes" class="form-control"></textarea>
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
@stop