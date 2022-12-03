@extends('layout/main')

@section('title')
    - Patients
@stop

@section('header')

@stop

@section('content')
    <section class="content-header">
        <h1>
            List Patients
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        Filters
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        {{Form::open(array('role'=>"form", 'method' => 'GET'))}}
                        <div class="form-group col-md-4">
                            <label>Hospital</label>
                            <select name="hospital_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    <option value="{{$val['id']}}" @if(Input::get('hospital_id') == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group col-md-4">
                            <input placeholder="Name" type="text" value="{{Input::get('name')}}" name="name"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <input placeholder="Phone" type="text" value="{{Input::get('phone')}}" name="phone"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <input placeholder="Patient Id" type="text" value="{{Input::get('id')}}"
                                   name="id" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <input placeholder="National Id" type="text" value="{{Input::get('national_id')}}"
                                   name="national_id" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <select id="country_id" class="form-control select2" name="country_id">
                                <option value="">Country</option>
                                @foreach($countries as $val)
                                    <option @if(Input::get('country_id') == $val['id']) selected
                                            @endif value="{{$val['id']}}">{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <select id="city_id" class="form-control select2" name="city_id">
                                <option value="">Cities</option>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <button class="btn btn-primary" type="submit">Search</button>
                            <a href="{{route('listPatient')}}" class="btn btn-default" type="submit">Clear</a>
                        </div>
                        {{Form::close()}}
                    </div>

                </div>
            </div>
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="example1">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Patient Id</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Gender</th>
                                    <th>Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($patients as $val)
                                    <tr>
                                        <td>{{$val['id']}}</td>
                                        <td>{{$val['registration_no']}}</td>
                                        <td>{{$val['name']}}</td>
                                        <td>{{$val['phone']}}</td>
                                        <td>{{$val['email']}}</td>
                                        <td>{{$val['gender'] == 2 ? 'Male' : 'Female'}}</td>
                                        <td>
                                            <div class="btn-group" style="width: 200px;">
                                                <a class="btn btn-default" id="getEvents" patient_id="{{$val['id']}}"
                                                   style="cursor: pointer"
                                                   data-toggle="modal" data-target="#myModal">Events</a>
                                                <a class="btn btn-default" href="{{route('editPatient', $val['id'])}}">Edit</a>
                                                {{--<a class="btn btn-default"--}}
                                                   {{--href="{{route('deletePatient', $val['id'])}}">Delete</a>--}}

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div>
                            {{$links}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="modalLabel">Patient Events</h4>
                </div>
                <div class="modal-body" id="modalBody">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer')
    <script>
        $(function () {
            $("#getEvents").click(function (e) {
                $("#modalBody").html('');
                $("#modalLabel").html('');
                $.ajax({
                    url: "{{route('listEvents')}}",
                    method: 'POST',
                    data: {patient_id: $(this).attr('patient_id')},
                    success: function (data) {
                        $("#modalBody").html(data.patientEvents);
                        $("#modalLabel").html('Events of (' + data.patient['name'] + ')');
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
        })
    </script>
@stop