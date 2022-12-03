@extends('layout/main')

@section('title')
    - Edit {{$patient['name']}}
@stop

@section('header')
    <link href="{{asset('plugins/datepicker/datepicker3.css')}}" rel="stylesheet"/>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Edit {{$patient['name']}}
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-9">
                <div class="box box-primary">
                    <!-- form start -->
                    {{Form::open(array('role'=>"form"))}}
                    <div class="box-body">
                        @if($patient['hospital_id'])
                            <input type="hidden" name="hospital_id" value="{{$patient['hospital_id']}}">
                        @endif

                        <div class="form-group col-md-6">
                            <label>Patient Phone *</label>
                            <input id="phone" required type="text" maxlength="15"
                                   value="{{Input::old('phone') ? Input::old('phone') : $patient['phone']}}"
                                   name="phone"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>First Name *</label>
                            <input id="first_name" required type="text"
                                   value="{{Input::old('first_name') ? Input::old('first_name') : $patient['first_name']}}"
                                   name="first_name"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Middle Name</label>
                            <input id="middle_name" type="text"
                                   value="{{Input::old('middle_name') ? Input::old('middle_name') : $patient['middle_name']}}"
                                   name="middle_name"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Last Name</label>
                            <input id="last_name" required type="text"
                                   value="{{Input::old('last_name') ? Input::old('last_name') : $patient['last_name']}}"
                                   name="last_name"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Family Name</label>
                            <input id="family_name" required type="text"
                                   value="{{Input::old('family_name') ? Input::old('family_name') : $patient['family_name']}}"
                                   name="family_name"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>National Id</label>
                            <input id="national_id" type="text"
                                   value="{{Input::old('national_id')? Input::old('national_id') : $patient['national_id']}}"
                                   name="national_id"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Birthday</label>
                            <input id="birthday" required type="text"
                                   value="{{Input::old('birthday')? Input::old('birthday') : $patient['birthday']}}"
                                   name="birthday"
                                   class="form-control datepicker">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input id="email" type="text"
                                   value="{{Input::old('email') ?Input::old('email') : $patient['email']}}"
                                   name="email"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Gender</label>

                            <div class="radio">
                                <label>
                                    <input autocomplete="off" required id="male" type="radio" value="1"
                                           name="gender" @if($patient['gender'] == 2) checked @endif>
                                    Male
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input autocomplete="off" required id="female" type="radio"
                                           value="2" name="gender" @if($patient['gender'] == 1) checked @endif>
                                    Female
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label>All Reservations SMS Language</label>

                            <div class="radio">
                                <label>
                                    <input autocomplete="off" type="radio" value="1"
                                           name="sms_lang">
                                    Make All Arabic
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input autocomplete="off" type="radio"
                                           value="2" name="sms_lang">
                                    Make All English
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Address</label>
                            <input id="address" type="text"
                                   value="{{Input::old('address') ? Input::old('address') : $patient['address']}}"
                                   name="address"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-12">
                            <label>Past history</label>
                            <textarea id="past_history"
                                      name="past_history"
                                      class="form-control">{{Input::old('past_history')? Input::old('past_history') :$patient['past_history']}}</textarea>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Family history</label>
                            <textarea id="family_history"
                                      name="family_history"
                                      class="form-control">{{Input::old('family_history')? Input::old('family_history'): $patient['family_history']}}</textarea>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Social history</label>
                            <textarea id="social_history"
                                      name="social_history"
                                      class="form-control">{{Input::old('social_history')?Input::old('social_history'):$patient['social_history']}}</textarea>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Health insurance</label>
                            <input id="health_insurance" type="text"
                                   value="{{Input::old('health_insurance')?Input::old('health_insurance'):$patient['health_insurance']}}"
                                   name="health_insurance" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Allergy drug</label>
                            <input id="allergy_drug" type="text"
                                   value="{{Input::old('allergy_drug')?Input::old('allergy_drug'):$patient['allergy_drug']}}"
                                   name="allergy_drug" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Allergy environments</label>
                            <input id="allergy_environments" type="text"
                                   value="{{Input::old('allergy_environments')?Input::old('allergy_environments'):$patient['allergy_environments']}}"
                                   name="allergy_environments" class="form-control">
                        </div>

                        <div class="form-group col-md-12">
                            <label>Notes</label>
                            <textarea id="notes" name="notes"
                                      class="form-control">{{Input::old('notes')?Input::old('notes'):$patient['notes']}}</textarea>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('listPatient')}}" class="btn btn-danger" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop

@section('footer')
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            autoclose: true
        });
        $(function () {
            @if($patient['city_id'])
            $.ajax({
                        url: "{{route('getCitiesOfCountryForEdit')}}",
                        method: 'POST',
                        data: {country_id: $("#country_id").val(), city_id: '{{$patient['city_id']}}'},
                        success: function (data) {
                            $("#city_id").html(data).select2();
                        }
                    });
            @endif
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
        })
    </script>
@stop
