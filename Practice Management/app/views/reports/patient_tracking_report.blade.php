@extends('layout/main')

@section('title')
    - Patient Tracking Report
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/loading_mask/waitMe.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('plugins/loading_mask/waitMe.js')}}"></script>
    <script>
        $(function () {

            //Datepicker
            $('.datepicker2').datepicker({
//                startDate: "1d",
                todayHighlight: true,
                autoclose: true
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
                        in_report : 1,
                        withDeactivate : 1
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectPhysician2").removeAttr('disabled').html(data).select2();
                    }
                });
            });

            function getReport() {
                $("#download_excel_div").hide();
                withMe('#withMe');
                $.ajax({
                    url: '{{route('postPatientTrackingReports')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $('#selectHospital2').val(),
                        clinic_id: $('#selectClinic2').val(),
                        physician_id: $('#selectPhysician2').val(),
                        name: $('#name').val(),
                        id: $('#id').val(),
                        phone: $('#phone').val(),
                        date_from: $('#date_from').val(),
                        date_to: $('#date_to').val(),
                        filter_res_type: $('#filter_res_type').val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $('#withMe').waitMe('hide');
                        if (data.response == 'false') {
                            alert(data.message);
                            $("#report_html").html('');
                            $("#download_excel_div").hide();
                        } else {
                            $("#download_excel_div").show();
                            window.history.pushState('', '', '?' + $("#postPatientTrackingReports").serialize());
                            $("#report_html").html(data.html);
                            $('.showPopover').popover({
                                html: true
                            });
                        }
                    }
                });
            }

            $("#getReport").click(function (e) {
                e.preventDefault();
                getReport();
            });

            $("#download_excel").click(function (e) {
                window.location.href = '{{route('excelPatientTrackingReport')}}?' + $("#postPatientTrackingReports").serialize();
            });

            @if(Input::get('hospital_id'))
            $.ajax({
                        url: '{{route('getClinicsByHospitalId')}}',
                        method: 'POST',
                        async: false,
                        data: {
                            hospital_id: '{{Input::get('hospital_id')}}'
                        },
                        headers: {token: '{{csrf_token()}}'},
                        success: function (data) {
                            $("#selectClinic2").html(data).select2();
                            @if(Input::get('clinic_id'))
                            $("#selectClinic2").val('{{Input::get('clinic_id')}}').select2();
                            $.ajax({
                                url: '{{route('getPhysicianByClinicId')}}',
                                method: 'POST',
                                async: false,
                                data: {
                                    clinic_id: '{{Input::get('clinic_id')}}',
                                    in_report : 1
                                },
                                headers: {token: '{{csrf_token()}}'},
                                success: function (data) {
                                    $("#selectPhysician2").html(data).select2();
                                    @if(Input::get('physician_id'))
                                        $("#selectPhysician2").val('{{Input::get('physician_id')}}').select2();
                                    @endif

                                }
                            });
                            @endif

                        }
                    });
            getReport();
            @endif

        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Patient Tracking Report
        </h1>
    </section>
    <section class="content">
        <div class="row" id="withMe">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    <div class="box-header">
                        Criteria
                        <button type="button" class="btn btn-box-tool pull-right" data-widget="collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                    {{Form::open(array('method' => 'GET', 'id' => 'postPatientTrackingReports'))}}
                    <div class="box-body">
                        <div class="form-group col-md-4">
                            <label>Hospital *</label>
                            <select required autocomplete="off" id="selectHospital2" name="hospital_id"
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
                            <label>Physician</label>
                            <select autocomplete="off" id="selectPhysician2" name="physician_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Patient Id</label>
                            <input type="text" id="id" name="id" value="{{Input::get('id')}}" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Patient Phone</label>
                            <input type="text" maxlength="15" id="phone" name="phone" value="{{Input::get('phone')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Patient Name</label>
                            <input type="text" name="name" id="name" value="{{Input::get('name')}}" class="form-control">
                        </div>

                        <div class="form-group col-md-4">
                            <label>From Date</label>
                            <input required id="date_from" type="text" data-date-format="yyyy-mm-dd"
                                   value="{{Input::get('date_from') ? Input::get('date_from') : date('Y-m-d')}}"
                                   name="date_from" class="form-control datepicker2">
                        </div>

                        <div class="form-group col-md-4">
                            <label>To Date</label>
                            <input required id="date_to" type="text" data-date-format="yyyy-mm-dd"
                                   value="{{Input::get('date_to') ? Input::get('date_to') : date('Y-m-d')}}"
                                   name="date_to" class="form-control datepicker2">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Res. Type</label>
                            <br>
                            <select autocomplete="off" name="filter_res_type"
                                    class="form-control select2" id="filter_res_type">
                                <option value="">All</option>
                                <option @if(Input::get('filter_res_type') == 1) selected @endif value="1">By Call</option>
                                <option @if(Input::get('filter_res_type') == 2) selected @endif value="2">Walk In</option>
                                <option @if(Input::get('filter_res_type') == 3) selected @endif value="3">In patient revisit</option>
                                <option @if(Input::get('filter_res_type') == 5) selected @endif value="5">Out patient by call</option>
                                <option @if(Input::get('filter_res_type') == 6) selected @endif value="6">Out patient by reception</option>
                                <option @if(Input::get('filter_res_type') == 4) selected @endif value="4">Online</option>
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" id="getReport">Get</button>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('reports.patient_tracking_report_excel'))
                <div class="col-md-12" id="download_excel_div" style="display: none;margin-bottom: 20px;">
                    <button id="download_excel" class="btn btn-primary">Download Excel</button>
                </div>
            @endif
            <div class="col-md-12">
                <div id="report_html">

                </div>
            </div>
        </div>
    </section>
@stop