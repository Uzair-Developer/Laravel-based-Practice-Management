@extends('layout/main')

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/autocomplete/jquery.autocomplete.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('plugins/autocomplete/jquery.autocomplete.js')}}"></script>
    <script>
        $('.datepicker2').datepicker({
            todayHighlight: true,
            autoclose: true
        });
        $("#patientName").autocomplete({
            url: '{{route('autoCompletePatientShowName')}}',
            minChars: 1,
            useCache: false,
            filterResults: false,
            mustMatch: true,
            maxItemsToShow: 10,
            remoteDataType: 'json',
            onItemSelect: function (item) {
                $("#patientName").val(item.data[0]);
            }
        });

        setInterval(function () {
            $.ajax({
                url: '{{route('getReservationCounts')}}',
                method: 'POST',
                data: {
                    date_from: $("#stat_date_from").val(),
                    date_to: $("#stat_date_to").val(),
                    hospital_id: $("#selectHospital2").val(),
                    clinic_id: $("#selectClinic2").val(),
                    physician_id: $("#selectPhysician2").val()
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#count_reservations").html(data.count_reservations);
                }
            });
        }, 10000);

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
    </script>
@stop

@section('content')
    @if($c_user->user_type_id == \core\enums\UserRules::patientRelation)
        <section class="content-header">
            <h1>
                Manage Clinics
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
                        {{Form::open(array('role'=>"form",'method' => 'GET', 'route' => 'manageClinicReservations'))}}
                        <div class="box-body">
                            <div class="form-group col-md-3">
                                <label>Patient Name or Id</label>
                                <input autocomplete="off" id="patientName" type="text" name="name" class="form-control">
                                <input type="hidden" name="hospital_id" value="1">
                                <input type="hidden" name="date_from" value="{{date('Y-m-d')}}">
                                <input type="hidden" name="date_to" value="{{date('Y-m-d')}}">
                                <input type="hidden" name="status" value="1">
                                <input type="hidden" name="patient_attend" value="0">
                            </div>

                            <div class="form-group col-md-3">
                                <label>Patient Phone</label>
                                <input autocomplete="off" type="text" name="phone" class="form-control">
                            </div>
                        </div>
                        <div class="box-footer">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            @foreach($clinics as $val)
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <a
                                            href="{{route('manageClinicReservations')}}?hospital_id={{$val['hospital_id']}}&clinic_id={{$val['id']}}&date_from={{date('Y-m-d')}}&date_to={{date('Y-m-d')}}&status=1&patient_attend=0">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-green"><i class="fa fa-flag-o"></i></span>

                                            <div class="info-box-content">
                                                <span class="info-box-text"></span>
                                                <span class="info-box-number">{{$val['name']}}</span>
                                            </div>
                                            <!-- /.info-box-content -->
                                        </div>
                                    </a>
                                    <!-- /.info-box -->
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    @if ($c_user->user_type_id == 1 || $c_user->hasAccess('dashboard.reservationCounts'))
        <section class="content-header">
            <h1>
                Statistics
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
                        {{Form::open(array('role'=>"form",'method' => 'GET'))}}
                        <div class="box-body">
                            <div class="form-group col-md-4">
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

                            <div class="form-group col-md-4">
                                <label>Clinic</label>
                                <br>
                                <select autocomplete="off" id="selectClinic2" name="clinic_id"
                                        class="form-control select2">
                                    <option value="">Choose</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Physician Name</label>
                                <br>
                                <select id="selectPhysician2" name="physician_id" class="form-control select2">
                                    <option value="">Choose</option>

                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>From Date</label>
                                <input type="text" data-date-format="yyyy-mm-dd" id="stat_date_from"
                                       value="{{Input::get('date_from') ? Input::get('date_from') : date('Y-m-d')}}"
                                       name="date_from" class="form-control datepicker2">
                            </div>
                            <div class="form-group col-md-4">
                                <label>To Date</label>
                                <input type="text" data-date-format="yyyy-mm-dd" id="stat_date_to"
                                       value="{{Input::get('date_to') ? Input::get('date_to') : date('Y-m-d')}}"
                                       name="date_to" class="form-control datepicker2">
                            </div>
                        </div>
                        <div class="box-footer">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
                <div id="count_reservations">
                    {{$count_reservations}}
                </div>
            </div>
        </section>
    @endif
@stop