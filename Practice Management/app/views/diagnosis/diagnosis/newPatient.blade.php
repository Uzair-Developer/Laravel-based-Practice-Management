@extends('layout/main')

@section('title')
    - Diagnoses System
@stop

@section('header')
    <link href="{{asset('plugins/datepicker/datepicker3.css')}}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@stop

@section('content')
    <section class="content-header">
        <h1>
            Diagnoses System
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li id="li_0" class="tab-li active"><a href="#tab_0" data-toggle="tab">Patient</a></li>
                        <li id="li_1" class="tab-li "><a href="#tab_1" data-toggle="tab">Symptoms</a></li>
                        <li id="li_2" class="tab-li "><a href="#tab_2" data-toggle="tab">Additional Symptom</a></li>
                        <li id="li_3" class="tab-li "><a href="#tab_3" data-toggle="tab">Disease Questions</a></li>
                        <li id="li_4" class="tab-li "><a href="#tab_4" data-toggle="tab">Result</a></li>
                        <li id="li_5" class="tab-li "><a href="#tab_5" data-toggle="tab">Manage Symptoms</a></li>
                        <li id="li_6" class="tab-li "><a href="#tab_6" data-toggle="tab">Symptoms Comment</a></li>
                    </ul>
                    <div class="tab-content col-md-12">
                        <div class="tab-pane active" id="tab_0">
                            <div class="col-md-10">
                                <div class="box box-primary">
                                    <div class="box-header">
                                        <a id="clearData" class="btn btn-default">Clear data</a>
                                    </div>
                                    <!-- form start -->
                                    {{Form::open(array('role'=>"form", 'route' => 'postStartDiagnosis1', 'id' => 'newPatientForm'))}}
                                    <div class="box-body">
                                        <div class="form-group col-md-6">
                                            <label>Phone</label>
                                            <input id="phone" required type="text" maxlength="15"
                                                   value="{{Input::old('phone')}}"
                                                   name="phone"
                                                   class="form-control">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Name</label>
                                            <input id="name" required type="text" value="{{Input::old('name')}}"
                                                   name="name"
                                                   class="form-control">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Patient Id</label>
                                            <input id="id" type="text" value="{{Input::old('id')}}"
                                                   name="id" class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>National Id</label>
                                            <input id="national_id" type="text" value="{{Input::old('national_id')}}"
                                                   name="national_id"
                                                   class="form-control">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Another phone</label>
                                            <input id="phone2" type="text" maxlength="15"
                                                   value="{{Input::old('phone2')}}" name="phone2"
                                                   class="form-control">
                                        </div>

                                        <input type="hidden" value="0" name="patient_id" id="patient_id">

                                        <div class="form-group col-md-6">
                                            <label>Birthday</label>
                                            <input id="birthday" required type="text" value="{{Input::old('birthday')}}"
                                                   name="birthday"
                                                   class="form-control datepicker">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Email</label>
                                            <input id="email" type="email" value="{{Input::old('email')}}" name="email"
                                                   class="form-control">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Gender</label>

                                            <div class="radio">
                                                <label>
                                                    <input autocomplete="off" required id="male" type="radio" value="1"
                                                           name="gender" checked>
                                                    Male
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input autocomplete="off" required id="female" type="radio"
                                                           value="2" name="gender">
                                                    Female
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label>Address</label>
                                            <input id="address" type="text" value="{{Input::old('address')}}"
                                                   name="address"
                                                   class="form-control">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Country</label>
                                            <select autocomplete="off" id="country_id" required name="country_id"
                                                    class="form-control select2">
                                                <option selected value="">Choose</option>
                                                @foreach($countries as $val)
                                                    <option value="{{$val['id']}}">{{$val['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>City</label>
                                            <select autocomplete="off" id="city_id" required name="city_id"
                                                    class="form-control select2">
                                                <option selected value="">Choose</option>
                                                {{--@foreach($cities as $val)--}}
                                                {{--<option value="{{$val['id']}}">{{$val['name']}}</option>--}}
                                                {{--@endforeach--}}
                                            </select>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label>Past history</label>
                            <textarea id="past_history"
                                      name="past_history" class="form-control">{{Input::old('past_history')}}</textarea>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label>Family history</label>
                            <textarea id="family_history"
                                      name="family_history"
                                      class="form-control">{{Input::old('family_history')}}</textarea>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label>Social history</label>
                            <textarea id="social_history"
                                      name="social_history"
                                      class="form-control">{{Input::old('social_history')}}</textarea>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Health insurance</label>
                                            <input id="health_insurance" type="text"
                                                   value="{{Input::old('health_insurance')}}"
                                                   name="health_insurance" class="form-control">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Allergy drug</label>
                                            <input id="allergy_drug" type="text" value="{{Input::old('allergy_drug')}}"
                                                   name="allergy_drug" class="form-control">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Allergy environments</label>
                                            <input id="allergy_environments" type="text"
                                                   value="{{Input::old('allergy_environments')}}"
                                                   name="allergy_environments" class="form-control">
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label>Notes</label>
                                            <textarea id="notes" name="notes" class="form-control"></textarea>
                                        </div>

                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <button class="btn btn-primary" type="submit">Continue to: step2</button>
                                    </div>
                                    {{Form::close()}}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <a data-toggle="modal" data-target="#myModal" class="btn btn-app" id="patientEvents"
                                   style="display: none;">
                                    <span class="badge bg-yellow" id="eventsCount">0</span>
                                    <i class="fa fa-bullhorn"></i> Events
                                </a>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_1">

                            <div class="col-md-6">
                                <div class="box box-primary">
                                    <!-- form start -->
                                    {{Form::open(array('role'=>"form", 'id' => 'chooseSymptomForm'))}}
                                    <div class="box-body">
                                        <div class="form-group col-md-12">
                                            <label>Symptoms</label>
                                            <br>
                                            <select style="width: 400px;" required multiple name="symptom_id[]"
                                                    id="symptoms"
                                                    class="form-control select2">
                                                @foreach($symptoms as $val)
                                                    <option value="{{$val['id']}}"
                                                            @if(Input::old('symptom_id') == $val['id'])
                                                            selected @endif>{{$val['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <button class="btn btn-primary" type="submit">Continue to: step3</button>
                                    </div>
                                    {{Form::close()}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="box box-primary">
                                    <!-- form start -->
                                    <div class="box-body">
                                        <div class="form-group col-md-12">
                                            <label>Possible diseases</label>

                                            <div id="diseasesDiv">

                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_2"></div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_3"></div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_4"></div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_5">
                            <div class="col-md-6">
                                <div class="box box-primary">
                                    <div class="box-header">
                                        New Symptom
                                    </div>
                                    <!-- form start -->
                                    {{Form::open(array('role'=>"form", 'route' => 'createSymptomInDiagnosis', 'id' => 'form1'))}}
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label>Organs</label>
                                            <br>
                                            <select style="width: 400px;" required name="organ_id"
                                                    class="form-control select2">
                                                <option value="">Choose</option>
                                                @foreach($organs as $val)
                                                    <option value="{{$val['id']}}"
                                                            @if(Input::old('organ_id') == $val['id'])
                                                            selected @endif>{{$val['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input style="width: 400px;" required type="text"
                                                   value="{{Input::old('name')}}"
                                                   name="name" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Type</label>
                                            <br>
                                            <select required name="type" class="form-control select2" style="width: 400px;">
                                                <option selected value="">Choose</option>
                                                @foreach(\core\diagnosis\enums\SymptomAttr::$type as $key => $val)
                                                    <option value="{{$key}}">{{$val}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>S/S</label>
                                            <br>
                                            <select required name="s_s" class="form-control select2" style="width: 400px;">
                                                <option selected value="">Choose</option>
                                                @foreach(\core\diagnosis\enums\SymptomAttr::$SS as $key => $val)
                                                    <option value="{{$key}}">{{$val}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Description</label>
                                                <textarea style="width: 400px;" name="text"
                                                          class="form-control">{{Input::old('text')}}</textarea>
                                        </div>

                                        <div class="box">
                                            <div class="box-header">
                                                Diseases
                                                <div class="box-tools pull-right">
                                                    <button type="button" class="btn btn-box-tool"
                                                            data-widget="collapse"><i
                                                                class="fa fa-minus"></i></button>
                                                </div>
                                            </div>
                                            <div class="box-body">
                                                <table id="tableDiseases" class="table table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 200px;">Disease</th>
                                                        <th style="width: 100px;">Rate (%)</th>
                                                        <th style="width: 100px">Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr id="tr_disease_0">
                                                        <td>
                                                            <select required name="disease_id[]"
                                                                    style="width: 200px;"
                                                                    class="form-control select2 diseases">
                                                                <option selected value="">Choose</option>
                                                                @foreach($diseases as $val)
                                                                    <option value="{{$val['id']}}">{{$val['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input required type="text" name="diseaseRate[]"
                                                                   class="form-control">
                                                        </td>
                                                        <td><a class="btn btn-primary deleteDiseaseRow"
                                                               trDelete="tr_disease_0">Delete</a></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="box-footer">
                                                <a id="addDiseaseRow" class="btn btn-primary">New row</a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <button id="addNewSymptom" type="submit" class="btn btn-primary">Save
                                        </button>
                                    </div>
                                    {{Form::close()}}

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="box box-primary">
                                    <div class="box-header">
                                        Exist Symptom
                                    </div>
                                    <!-- form start -->
                                    {{Form::open(array('role'=>"form", 'route' => 'addDiseaseToSymptom', 'id' => 'form2'))}}
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label>Symptoms</label>
                                            <br>
                                            <select required name="symptom_id" style="width: 200px;"
                                                    class="form-control select2">
                                                <option value="">Choose</option>
                                                @foreach($symptoms as $val)
                                                    <option value="{{$val['id']}}"
                                                            @if(Input::old('symptom_id') == $val['id'])
                                                            selected @endif>{{$val['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="box">
                                            <div class="box-header">
                                                Diseases
                                                <div class="box-tools pull-right">
                                                    <button type="button" class="btn btn-box-tool"
                                                            data-widget="collapse"><i
                                                                class="fa fa-minus"></i></button>
                                                </div>
                                            </div>
                                            <div class="box-body">
                                                <table id="tableDiseases2" class="table table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 200px;">Disease</th>
                                                        <th style="width: 100px;">Rate (%)</th>
                                                        <th style="width: 100px">Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr id="tr2_disease_0">
                                                        <td>
                                                            <select required name="disease_id[]"
                                                                    style="width: 200px;"
                                                                    class="form-control select2 diseases">
                                                                <option selected value="">Choose</option>
                                                                @foreach($diseases as $val)
                                                                    <option value="{{$val['id']}}">{{$val['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input required type="text" name="diseaseRate[]"
                                                                   class="form-control">
                                                        </td>
                                                        <td><a class="btn btn-primary deleteDiseaseRow2"
                                                               trDelete="tr2_disease_0">Delete</a></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="box-footer">
                                                <a id="addDiseaseRow2" class="btn btn-primary">New row</a>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                    {{Form::close()}}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <!-- form start -->
                                    <div class="box-body" id="diseaseSymptomsDiv">

                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_6">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <!-- form start -->
                                    {{Form::open(array('role'=>"form", 'route' => 'addCommentToSymptom', 'id' => 'form3'))}}
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label>Symptoms</label>
                                            <br>
                                            <select required name="symptom_id" style="width: 200px;"
                                                    class="form-control select2">
                                                <option value="">Choose</option>
                                                @foreach($symptoms as $val)
                                                    <option value="{{$val['id']}}"
                                                            @if(Input::old('symptom_id') == $val['id'])
                                                            selected @endif>{{$val['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Comment</label>
                                            <br>
                                            <textarea name="comment" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <button class="btn btn-primary" type="submit">Save</button>
                                    </div>
                                    {{Form::close()}}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <!-- form start -->
                                    <div class="box-body" id="commentsDiv">

                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->
            </div>
        </div>
    </section>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Patient Events</h4>
                </div>
                <div class="modal-body" id="myModalBody">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer')
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    <script src="{{asset('plugins/underscore/underscore.js')}}"></script>
    <style>
        progress[value] {
            appearance: none;
            border: none;
            /* Add dimensions */
            width: 100%;
            height: 20px;
            /* Although firefox doesn't provide any additional pseudo class to style the progress element container, any style applied here works on the container. */
            background-color: whiteSmoke;
            border-radius: 3px;
            box-shadow: 0 2px 3px rgba(0, 0, 0, .5) inset;
            /* Of all IE, only IE10 supports progress element that too partially. It only allows to change the background-color of the progress value using the 'color' attribute. */
            color: royalblue;
            position: relative;
        }

        progress[value]::-webkit-progress-bar {
            background-color: whiteSmoke;
            border-radius: 3px;
            box-shadow: 0 2px 3px rgba(0, 0, 0, .5) inset;
        }

        progress[value]::-webkit-progress-value {
            position: relative;

            background-size: 35px 20px, 100% 100%, 100% 100%;
            border-radius: 3px;

            /* Let's animate this */
            animation: animate-stripes 5s linear infinite;
        }

        @keyframes animate-stripes {
            100% {
                background-position: -100px 0;
            }
        }

        /* Let's spice up things little bit by using pseudo elements. */
        progress[value]::-webkit-progress-value:after {
            /* Only webkit/blink browsers understand pseudo elements on pseudo classes. A rare phenomenon! */
            content: '';
            position: absolute;
            width: 5px;
            height: 5px;
            top: 7px;
            right: 7px;
            background-color: white;
            border-radius: 100%;
        }

        /* Firefox provides a single pseudo class to style the progress element value and not for container. -moz-progress-bar */
        progress[value]::-moz-progress-bar {
            /* Gradient background with Stripes */
            background-image: -moz-linear-gradient(135deg,
            transparent,
            transparent 33%,
            rgba(0, 0, 0, .1) 33%,
            rgba(0, 0, 0, .1) 66%,
            transparent 66%),
            -moz-linear-gradient(top,
                    rgba(255, 255, 255, .25),
                    rgba(0, 0, 0, .2)),
            -moz-linear-gradient(left, #09c, #f44);
            background-size: 35px 20px, 100% 100%, 100% 100%;
            border-radius: 3px;
            /* Firefox doesn't support CSS3 keyframe animations on progress element. Hence, we did not include animate-stripes in this code block */
        }

        /* Fallback technique styles */
        .progress-bar {
            background-color: whiteSmoke;
            border-radius: 3px;
            box-shadow: 0 2px 3px rgba(0, 0, 0, .5) inset;
            /* Dimensions should be similar to the parent progress element. */
            width: 100%;
            height: 20px;
        }

        .html5::-webkit-progress-value {
            /* Gradient background with Stripes */
            background-image: -webkit-linear-gradient(135deg,
            transparent,
            transparent 33%,
            rgba(0, 0, 0, .1) 33%,
            rgba(0, 0, 0, .1) 66%,
            transparent 66%),
            -webkit-linear-gradient(top,
                    rgba(255, 255, 255, .25),
                    rgba(0, 0, 0, .2)),
            -webkit-linear-gradient(left, #09c, #f44);
        }

        /* Similarly, for Mozillaa. Unfortunately combining the styles for different browsers will break every other browser. Hence, we need a separate block. */
        .html5::-moz-progress-bar {
            /* Gradient background with Stripes */
            background-image: -moz-linear-gradient(135deg,
            transparent,
            transparent 33%,
            rgba(0, 0, 0, .1) 33%,
            rgba(0, 0, 0, .1) 66%,
            transparent 66%),
            -moz-linear-gradient(top,
                    rgba(255, 255, 255, .25),
                    rgba(0, 0, 0, .2)),
            -moz-linear-gradient(left, #09c, #f44);
        }
    </style>
    <script>
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            autoclose: true
        });

        ////////// prevent button back ////////////
        history.pushState({page: 1}, "Title 1", "#no-back");
        window.onhashchange = function (event) {
            window.location.hash = "no-back";
        };
        //////////////////////////////////////////

        $(function () {
            $(".select2").select2();
            $("#phone, #id, #national_id").blur(function (e) {
                if (!$(this).val()) {
                    return;
                }
                var input = $(this);
                $('#tab_0 input').attr('disabled', 'disabled');
                $('#tab_0 textarea').attr('disabled', 'disabled');
                $.ajax({
                    url: "{{route('checkPatientExist')}}",
                    method: 'POST',
                    data: {search: $(this).val()},
                    success: function (data) {
                        if (data) {

                        } else {

                        }
                        if (!data.phone) {
                            $("#patient_id").val('');
                            $('#patientEvents').hide();
                            $('#eventsCount').html(0);
                            $('#myModalBody').html('');
                            $('#myModalLabel').html('');
                            $('#tab_0 input').removeAttr('disabled');
                            $('#tab_0 textarea').removeAttr('disabled');
                            return;
                        }
                        $('#patientEvents').show();
                        $('#eventsCount').html(data.eventsCount);
                        $('#myModalBody').html(data.patientEvents);
                        $('#myModalLabel').html('Events Of ' + data.name);
                        if (input.attr('id') != 'phone') {
                            $("#phone").val(data.phone);
                        }
                        if (input.attr('id') != 'national_id') {
                            $("#national_id").val(data.national_id);
                        }
                        if (input.attr('id') != 'id') {
                            $("#id").val(data.id);
                        }
                        $("#patient_id").val(data.id);
                        $("#name").val(data.name);
                        $("#birthday").val(data.birthday);
                        if (data.gender == 1) {
                            $("#female").prop("checked", false);
                            $("#male").prop("checked", true);
                        } else if (data.gender == 2) {
                            $("#female").prop("checked", true);
                            $("#male").prop("checked", false);
                        }
                        $("#email").val(data.email);
                        $("#phone2").val(data.phone2);
                        $("#notes").html('').html(data.notes);
                        $("#family_history").html('').html(data.family_history);
                        $("#past_history").html('').html(data.past_history);
                        $("#health_insurance").val(data.health_insurance);
                        $("#allergy_drug").val(data.allergy_drug);
                        $("#allergy_environments").val(data.allergy_environments);
                        $("#social_history").html('').html(data.social_history);
                        $("#address").val(data.address);
                        if (data.countries) {
                            $("#country_id").html('').html(data.countries).select2();
                        }
                        if (data.cities) {
                            $("#city_id").html('').html(data.cities).select2();
                        }
                        $('#tab_0 input').removeAttr('disabled');
                        $('#tab_0 textarea').removeAttr('disabled');
                    }
                });
            });
            $("#clearData").click(function () {
                $('#patientEvents').hide();
                $('#eventsCount').html(0);
                $('#myModalBody').html('');
                $('#myModalLabel').html('');
                $("#phone, #address, #allergy_environments, #patient_id, #national_id, #id, #name, #birthday, #email, #phone2, #health_insurance, #allergy_drug").val('');
                $("#female, #male").removeAttr('checked');
                $("#diseasesDiv, #notes, #family_history, #past_history, #social_history").html('');
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

            $("#newPatientForm").submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{route('postStartDiagnosis1')}}",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (data) {
                        if (data == 1) {
                            alert('Create Event for Patient Successfully');
                            $(".tab-pane , .tab-li").removeClass('active');
                            $("#tab_1 , #li_1").addClass('active');
                        } else {
                            alert('Ops, error occurred try again!');
                        }
                    }
                });
            });

            $("#chooseSymptomForm").submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{route('postStartDiagnosis2')}}",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (data) {
                        if (data == 0) {
                            alert('Your Session Has Expired');
                            $(".tab-pane , .tab-li").removeClass('active');
                            $("#tab_0 , #li_0").addClass('active');
                            $("#tab_2, #tab_3, #tab_4").html('');
                            $("#phone, #address, #allergy_environments, #patient_id, #national_id, #id, #name, #birthday, #email, #phone2, #health_insurance, #allergy_drug").val('');
                            $("#female, #male").removeAttr('checked');
                            $("#diseasesDiv, #notes, #family_history, #past_history, #social_history").html('');
                            $(".select2").val('').select2();
                        } else if (data == 2) {
                            alert('Choose at least 1 symptom');
                        } else if (data == 1) {
                            $.ajax({
                                url: "{{route('startDiagnosis3')}}",
                                method: 'GET',
                                success: function (data) {
                                    $("#tab_2").html('').html(data);
                                    alert('Symptoms saved successfully');
                                    $(".tab-pane , .tab-li").removeClass('active');
                                    $("#tab_2 , #li_2").addClass('active');
                                }
                            });
                        }
                    }
                });
            });

            /////////////////////////////////////////
            var trNum = 1;
            $("#addDiseaseRow").click(function (e) {
                e.preventDefault();
                var template = _.template($('#diseasesScript').html(), {
                    trNum: trNum
                });
                $('#tableDiseases tbody').append(template);
                $('.select22').select2().addClass('select2').removeClass('select22');
                ++trNum;
            });

            $(document).on('click', '.deleteDiseaseRow', function (e) {
                var tr = $(this).attr('trDelete');
                $("#" + tr).remove();
            });
            /////////////////////////////////////////
            var trNum2 = 1;
            $("#addDiseaseRow2").click(function (e) {
                e.preventDefault();
                var template = _.template($('#diseasesScript2').html(), {
                    trNum: trNum2
                });
                $('#tableDiseases2 tbody').append(template);
                $('.select22').select2().addClass('select2').removeClass('select22');
                ++trNum2;
            });

            $(document).on('click', '.deleteDiseaseRow2', function (e) {
                var tr = $(this).attr('trDelete');
                $("#" + tr).remove();
            });
            /////////////////////////////////////////

            $("#symptoms").change(function (e) {
                $(this).attr('disabled', 'disabled');
                var symptoms = $(this).val();
                if (symptoms) {
                    $.ajax({
                        url: "{{route('getDiseaseOfSymptoms')}}",
                        method: 'POST',
                        data: {symptoms: $(this).val()},
                        success: function (data) {
                            $("#diseasesDiv").html('').html(data);
                            $("#symptoms").removeAttr('disabled');
                        }
                    });
                } else {
                    $("#diseasesDiv").html('');
                    $(this).removeAttr('disabled');
                }
            });


            $("#form1").submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: '{{route('createSymptomInDiagnosis')}}',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function (data) {
                        if (data != 0) {
                            getDiseaseSymptoms(1);
                            alert(data);
                        } else {
                            alert('Ops, Error Occurred Try Again Later!')
                        }
                    }
                });
            });

            $("#form2").submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: '{{route('addDiseaseToSymptom')}}',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function (data) {
                        if (data != 0) {
                            getDiseaseSymptoms(1);
                            alert(data);
                        } else {
                            alert('Ops, Error Occurred Try Again Later!')
                        }
                    }
                });
            });

            $("#form3").submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: '{{route('addCommentToSymptom')}}',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function (data) {
                        if (data != 0) {
                            getComments(1);
                            alert(data);
                        } else {
                            alert('Ops, Error Occurred Try Again!')
                        }
                    }
                });
            });
            $.fn.modal.Constructor.prototype.enforceFocus = function () {
            }; // very important for select2 in popup
            /////////////////////////////////////////

            $(document).ready(function () {

                $("#li_5").click(function (e) {
                    getDiseaseSymptoms(1);
                });
                $("#li_6").click(function (e) {
                    getComments(1);
                });
                $(document).on('click', '.pagination a', function (e) {
                    getComments($(this).attr('href').split('page=')[1]);
                    getDiseaseSymptoms($(this).attr('href').split('page=')[1]);
                    e.preventDefault();
                });

                $(document).on('click', '.deleteComment', function (e) {
                    if (confirm('Are you sure?')) {
                        var id = $(this).attr('ref_id');
                        $.ajax({
                            url: '{{route('diagnosisDeleteComment')}}',
                            method: 'POST',
                            data: {id: id}
                        }).done(function (data) {
                            $("#tr_comment_" + id).remove();
                        });
                    }
                });

                $(document).on('click', '.deleteDiseaseSymptom', function (e) {
                    if (confirm('Are you sure?')) {
                        var id = $(this).attr('ref_id');
                        console.log(id);
                        $.ajax({
                            url: '{{route('diagnosisDeleteDiseaseSymptom')}}',
                            method: 'POST',
                            data: {id: id}
                        }).done(function (data) {
                            $("#tr_diseaseSymptom_" + id).remove();
                        });
                    }
                });
            });
            function getComments(page) {
                $.ajax({
                    url: '?page=' + page
                }).done(function (data) {
                    $('#commentsDiv').html(data.comments);
                }).fail(function () {
                    alert('Comments could not be loaded.');
                });
            }

            function getDiseaseSymptoms(page) {
                $.ajax({
                    url: '?page=' + page
                }).done(function (data) {
                    $('#diseaseSymptomsDiv').html(data.diseaseSymptom);
                }).fail(function () {
                    alert('Disease Symptoms could not be loaded.');
                });
            }
        })
    </script>
    <script id="diseasesScript" type="text/html">
        <tr id="tr_disease_<%= trNum %>">
            <td>
                <select required name="disease_id[]" style="width: 200px;" class="form-control diseases select22">
                    <option selected value="">Choose</option>
                    @foreach($diseases as $val)
                        <option value="{{$val['id']}}">{{$val['name']}}</option>
                    @endforeach
                </select>
            </td>
            <td><input required type="text" name="diseaseRate[]" class="form-control"></td>
            <td><a class="btn btn-primary deleteDiseaseRow" trDelete="tr_disease_<%= trNum %>">Delete</a></td>
        </tr>
    </script>

    <script id="diseasesScript2" type="text/html">
        <tr id="tr2_disease_<%= trNum %>">
            <td>
                <select required name="disease_id[]" style="width: 200px;" class="form-control diseases select22">
                    <option selected value="">Choose</option>
                    @foreach($diseases as $val)
                        <option value="{{$val['id']}}">{{$val['name']}}</option>
                    @endforeach
                </select>
            </td>
            <td><input required type="text" name="diseaseRate[]" class="form-control"></td>
            <td><a class="btn btn-primary deleteDiseaseRow2" trDelete="tr2_disease_<%= trNum %>">Delete</a></td>
        </tr>
    </script>
@stop