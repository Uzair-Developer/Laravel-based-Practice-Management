@extends('layout/main')

@section('title')
    - Edit {{$user['user_name']}}
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@stop

@section('content')
    <section class="content-header">
        <h1>
            Edit {{$user['user_name']}}
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    {{Form::open(array('role'=>"form",'files' => true))}}
                    <div class="box-body">
                        <div class="form-group col-md-6">
                            <label>Full name</label>
                            <input required type="text" value="{{$user['full_name']}}" name="full_name"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Extension Number</label>
                            <input required type="text" value="{{$user['extension_num']}}" name="extension_num"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="email" value="{{$user['email']}}" name="email" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Phone number</label>
                            <input type="number" value="{{$user['phone_number']}}"
                                   name="phone_number" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Mobile 1</label>
                            <input type="number" value="{{$user['mobile1']}}"
                                   name="mobile1" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Mobile 2</label>
                            <input type="number" value="{{$user['mobile2']}}"
                                   name="mobile2" class="form-control">
                        </div>

                        <div class="form-group col-md-6" style="margin-bottom: 5px;">
                            <label>Address</label>
                            <input type="text" value="{{$user['address']}}"
                                   name="address" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Country</label>
                            <select autocomplete="off" id="country_id" required name="country_id"
                                    class="form-control select2">
                                <option selected value="">Choose</option>
                                @foreach($countries as $val)
                                    <option @if($physician['country_id'] == $val['id']) selected
                                            @endif value="{{$val['id']}}">{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6" style="margin-bottom: 5px;">
                            <label>City</label>
                            <select autocomplete="off" id="city_id" required name="city_id"
                                    class="form-control select2">
                                <option selected value="">Choose</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6" style="margin-bottom: 5px;">
                            <label>Age</label>
                            <input type="number" value="{{$physician['age']}}"
                                   name="age" class="form-control">
                        </div>

                        <div class="form-group col-md-6" style="margin-bottom: 5px;">
                            <label>Gender</label>
                            <select required name="gender" class="form-control select2">
                                <option value="">Choose</option>
                                <option value="1" @if($physician['gender'] == 1) selected @endif>Male</option>
                                <option value="2" @if($physician['gender'] == 2) selected @endif>Female</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Specialty</label>
                            <select required name="specialty_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($specialty as $val)
                                    <option value="{{$val['id']}}"
                                            @if($user['user_specialty_id'] == $val['id'])
                                            selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="box box-default col-md-12">
                            <div class="box-header with-border">
                                <h3 class="box-title">Career Info</h3>

                                <div class="box-tools pull-right">
                                    <button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body" style="display: block;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group col-md-6">
                                            <label>Photo image</label>
                                            <input type="file" value=""
                                                   name="image_url" class="form-control">
                                            @if(!empty($physician['image_url']))
                                                <img src="{{asset($physician['image_url'])}}" width="100" height="100">
                                            @endif
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
                                            <select name="degree" class="form-control select2">
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
                                            <select name="license_activation" class="form-control select2">
                                                <option value="">Choose</option>
                                                <option value="1" @if($physician['job_position'] == 1) selected @endif>
                                                    Active
                                                </option>
                                                <option value="2" @if($physician['job_position'] == 2) selected @endif>
                                                    Deactivated
                                                </option>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.box-body -->
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('physicians')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer')
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    <script>
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();

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

        });
    </script>
@stop